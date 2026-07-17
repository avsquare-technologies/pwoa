<?php

namespace App\Livewire\Public;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class CourseList extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $courses = Course::query()
            ->where('is_published', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->category_id, function ($query) {
                $query->where('course_category_id', $this->category_id);
            })
            ->withCount(['lessons', 'users'])
            ->latest()
            ->paginate(12);

        $joinedCourseIds = Auth::check() 
            ? Auth::user()->enrolledCourses()->pluck('courses.id')->toArray() 
            : [];

        return view('livewire.public.course-list', [
            'courses' => $courses,
            'categories' => \App\Models\CourseCategory::orderBy('name')->get(),
            'joinedCourseIds' => $joinedCourseIds,
            'isSubscribed' => Auth::check() && Auth::user()->isActiveMember(),
        ]);
    }
}
