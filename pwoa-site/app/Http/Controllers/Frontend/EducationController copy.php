<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\DemoCatalog;


class EducationController extends Controller
{
    public function index(Request $request)
    {
        $courses = DemoCatalog::courses()
            ->when($request->filled('category'), fn ($items) => $items->where('category', $request->string('category')->toString()))
            ->values();

        return view('frontend.education.index', [
            'certificationTracks' => DemoCatalog::certificationTracks(),
            'courses' => $courses,
        ]);
    }

    public function track(string $slug)
    {
        DemoCatalog::findBySlug(DemoCatalog::certificationTracks(), $slug);

        return $this->index(request())->with('success', 'Track detail pages can be added next. The education hub is loading correctly now.');
    }

    public function course(string $slug)
    {
        DemoCatalog::findBySlug(DemoCatalog::courses(), $slug);

        return $this->index(request())->with('success', 'Course detail pages can be added next. The education catalog is loading correctly now.');
    }

    public function lesson(string $slug, string $lesson)
    {
        return redirect()->route('frontend.education.course', $slug)->with('success', 'Lesson playback flow is ready for backend content wiring.');
    }

    public function enroll(string $slug)
    {
        return redirect()->route('frontend.education.course', $slug)->with('success', 'Enrollment flow placeholder complete. Connect billing or membership checks when ready.');
    }

    public function completeLesson(string $slug, string $lesson)
    {
        return redirect()->route('frontend.education.course', $slug)->with('success', 'Lesson completion will be tracked once course progress storage is added.');
    }

    public function exam(string $slug)
    {
        return redirect()->route('frontend.education.course', $slug)->with('success', 'Exam UI can be added next without blocking the rest of the site.');
    }

    public function submitExam(string $slug)
    {
        return redirect()->route('frontend.education.course', $slug)->with('success', 'Exam submission placeholder complete.');
    }

    public function certificate(string $id)
    {
        return redirect()->route('frontend.education.index')->with('success', 'Certificate download can be connected once real completions exist.');
    }
}


