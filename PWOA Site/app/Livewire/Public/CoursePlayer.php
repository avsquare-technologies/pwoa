<?php

namespace App\Livewire\Public;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CoursePlayer extends Component
{
    public Course $course;

    public $currentLesson;

    public array $completedLessonIds = [];

    public function mount($slug, $lesson = null)
    {
        $this->course = Course::where('slug', $slug)
            ->where('is_published', true)
            ->with(['lessons', 'quiz'])
            ->firstOrFail();

        // Auto-enroll the user silently when they open the course
        if (Auth::check()) {
            Auth::user()->enrolledCourses()->syncWithoutDetaching([
                $this->course->id => ['joined_at' => now()],
            ]);
            
            $this->loadCompletedLessons();
        }

        if ($lesson) {
            $requestedLesson = Lesson::where('slug', $lesson)->firstOrFail();
            if ($this->isLessonLocked($requestedLesson)) {
                $this->currentLesson = $this->course->lessons->first(function ($l) {
                    return !in_array($l->id, $this->completedLessonIds);
                }) ?? $this->course->lessons->first();
            } else {
                $this->currentLesson = $requestedLesson;
            }
        } else {
            $this->currentLesson = $this->course->lessons->first(function ($l) {
                return !in_array($l->id, $this->completedLessonIds);
            }) ?? $this->course->lessons->first();
        }
    }

    public function loadCompletedLessons()
    {
        if (Auth::check()) {
            $this->completedLessonIds = Auth::user()->completedLessons()
                ->whereIn('lesson_id', $this->course->lessons->pluck('id'))
                ->pluck('lesson_id')
                ->toArray();
        }
    }

    public function selectLesson($lessonId)
    {
        $lesson = Lesson::find($lessonId);
        if ($lesson && !$this->isLessonLocked($lesson)) {
            $this->currentLesson = $lesson;
        }
    }

    public function nextLesson()
    {
        if (Auth::check()) {
            Auth::user()->completedLessons()->syncWithoutDetaching([
                $this->currentLesson->id => ['completed_at' => now()]
            ]);
            $this->loadCompletedLessons();
        }

        $next = $this->course->lessons->where('order', '>', $this->currentLesson->order)->first();
        if ($next) {
            $this->currentLesson = $next;
        }
    }

    public function startExam()
    {
        if (Auth::check()) {
            Auth::user()->completedLessons()->syncWithoutDetaching([
                $this->currentLesson->id => ['completed_at' => now()]
            ]);
        }

        return redirect()->route('quiz.engine', $this->course->quiz->id);
    }

    public function previousLesson()
    {
        $prev = $this->course->lessons->where('order', '<', $this->currentLesson->order)->last();
        if ($prev) {
            $this->currentLesson = $prev;
        }
    }

    public function isLessonLocked($lesson)
    {
        $isEnrolled = Auth::check() && Auth::user()->enrolledCourses()->where('course_id', $this->course->id)->exists();

        if (!$isEnrolled && $lesson->is_restricted && (!Auth::check() || !Auth::user()->isActiveMember())) {
            return true;
        }

        // Sequential check
        $lessons = $this->course->lessons;
        $index = $lessons->search(fn($l) => $l->id === $lesson->id);

        if ($index !== false && $index > 0) {
            $prevLesson = $lessons[$index - 1];
            if (!in_array($prevLesson->id, $this->completedLessonIds)) {
                return true;
            }
        }

        return false;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.public.course-player', [
            'isLocked' => $this->isLessonLocked($this->currentLesson),
        ]);
    }
}
