<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\CourseCategory;
use App\Models\Certificate;
use App\Models\QuizResult;


class EducationController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::where('is_published', true)
            ->with('category')
            ->withCount('lessons')
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            })
            ->latest()
            ->get();

        return view('frontend.education.index', [
            'courses' => $courses,
           'certificationTracks' => CourseCategory::withCount('courses')->get(),
        ]);
    }


  public function course($slug)
{
    $course = Course::where('slug', $slug)
        ->with([
            'lessons' => fn($q) => $q->orderBy('order'),
            'quiz.questions'
        ])
        ->firstOrFail();

    $user = auth()->user();
    $quizResult = null;
    $certificate = null;
    $progress = 0;

    if ($user) {
        $completedLessonIds = $user->completedLessons()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();
        
        $totalLessons = $course->lessons->count();
        $completedCount = count(array_unique($completedLessonIds));
        
        $progress = $totalLessons > 0 
            ? min(100, round(($completedCount / $totalLessons) * 100)) 
            : 0;

        if ($course->quiz) {
            $quizResult = QuizResult::where('user_id', $user->id)
                ->where('quiz_id', $course->quiz->id)
                ->latest()
                ->first();
        }

        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
    }

    return view('frontend.education.course', compact('course', 'quizResult', 'certificate', 'progress'));
}

    public function track($slug)
{
    $track = CourseCategory::where('slug', $slug)->firstOrFail();

    $courses = Course::where('course_category_id', $track->id)
        ->where('is_published', true)
        ->withCount('lessons')
        ->get();

    return view('frontend.education.track', compact('track', 'courses'));
}

public function submitQuiz(Request $request, $slug)
{
    $course = Course::where('slug', $slug)->with('quiz.questions')->firstOrFail();
    $user = auth()->user();

    if (!$course->quiz) {
        return redirect()->back()->with('error', 'Quiz not found for this course.');
    }

    $answers = $request->input('answers', []);
    $score = 0;
    $questions = $course->quiz->questions;
    $total = $questions->count();

    if ($total === 0) {
        return redirect()->back()->with('error', 'No questions found for this quiz.');
    }

    foreach ($questions as $question) {
        $userAnswer = $answers[$question->id] ?? null;
        $correctAnswer = $question->correct_answer;
        
        if ($userAnswer !== null) {
            // Check if user answer matches the correct answer key
            // OR if the option text matches the correct answer
            if ($userAnswer == $correctAnswer) {
                $score++;
            } elseif (isset($question->options[$userAnswer]) && $question->options[$userAnswer] == $correctAnswer) {
                $score++;
            }
        }
    }

    $percentage = ($score / $total) * 100;
    $passed = $percentage >= $course->quiz->pass_percentage;

    // Save Result
    $result = QuizResult::create([
        'user_id' => $user->id,
        'quiz_id' => $course->quiz->id,
        'score' => $percentage,
        'passed' => $passed,
    ]);

    if ($passed) {
        // Generate Certificate
        app(\App\Services\CertificateService::class)->generateForCourse($user, $course, $percentage, $result->id);
        
        // Mark course as completed in pivot
        $user->enrolledCourses()->updateExistingPivot($course->id, [
            'completed_at' => now()
        ]);
    }

    return redirect()->back()->with('quiz_result', [
        'score' => round($percentage, 2),
        'passed' => $passed,
        'message' => $passed ? 'Congratulations! You passed the assessment.' : 'You did not pass. Please review the material and try again.'
    ]);
}

    public function lesson($courseSlug, $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)
            ->with('lessons')
            ->firstOrFail();

        $lesson = $course->lessons()
            ->where('slug', $lessonSlug)
            ->firstOrFail();

        // For public/fallback access, enforce sequential locked validation
        $completedLessonIds = auth()->check()
            ? auth()->user()->completedLessons()->whereIn('lesson_id', $course->lessons->pluck('id'))->pluck('lesson_id')->toArray()
            : [];

        $lessons = $course->lessons;
        $index = $lessons->search(fn($l) => $l->id === $lesson->id);

        if ($index !== false && $index > 0) {
            $prevLesson = $lessons[$index - 1];
            if (!in_array($prevLesson->id, $completedLessonIds)) {
                return redirect()->route('education.course', $course->slug)
                    ->with('error', 'Please complete the previous lessons first.');
            }
        }

        return view('frontend.education.lesson', compact('course', 'lesson'));
    }


    public function enroll($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        auth()->user()->enrolledCourses()->syncWithoutDetaching([
            $course->id => ['joined_at' => now()]
        ]);

        return back()->with('success', 'Enrolled successfully! You can now start the course.');
    }

    public function completeLesson($courseSlug, $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)
            ->with('lessons')
            ->firstOrFail();

        $currentLesson = $course->lessons()
            ->where('slug', $lessonSlug)
            ->firstOrFail();

        // Mark as completed for the authenticated user
        auth()->user()->completedLessons()->syncWithoutDetaching([
            $currentLesson->id => ['completed_at' => now()]
        ]);

        $nextLesson = $course->lessons()
            ->where('order', '>', $currentLesson->order)
            ->orderBy('order')
            ->first();

        if ($nextLesson) {
            return redirect()->route('education.lesson', [$course->slug, $nextLesson->slug])
                ->with('success', 'Lesson completed! Moving to the next one.');
        }

        // Check if all lessons are completed
        $totalLessons = $course->lessons->count();
        $completedLessonsCount = auth()->user()->completedLessons()
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->count();

        if ($completedLessonsCount >= $totalLessons) {
            // We no longer generate certificate here. 
            // Certificate is generated upon passing the Final Assessment (Quiz).
            // We just notify the user.
            return redirect()->to(route('education.course', $course->slug) . '#assessment-section')
                ->with('success', 'Congratulations! You have completed all lessons. You can now take the final assessment to earn your certificate.');
        }

        return redirect()->route('education.course', $course->slug)
            ->with('success', 'Lesson completed!');
    }

    public function exam($slug)
    {
        return back()->with('info', 'Exam feature is coming soon.');
    }

   public function submitExam(Request $request, $slug)
{
    $course = Course::where('slug', $slug)->firstOrFail();

    $score = $this->calculateScore($request); // your logic

    $quizResult = QuizResult::create([
        'user_id' => auth()->id(),
        'course_id' => $course->id,
        'score' => $score,
    ]);

    // ✅ ONLY generate certificate if passed
    if ($score >= 70) {

        Certificate::firstOrCreate([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
        ], [
            'certificate_number' => Certificate::generateCertificateNumber(),
            'issued_at' => now(),
            'score' => $score,
        ]);
    }

    return redirect()->route('education.course', $course->slug);
}
    public function certificate($id)
    {
        $certificate = Certificate::where('id', $id)
            ->orWhere('certificate_number', $id)
            ->firstOrFail();

        return redirect()->route('certificates.show', $certificate->certificate_number);
    }
}
