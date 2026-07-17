<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FrontCourseController extends Controller
{
    public function index(): View
    {
        return view('pages.courses.list');
    }

    public function player(string $slug): View
    {
        return view('pages.courses.player', compact('slug'));
    }
}
