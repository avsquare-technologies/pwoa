<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\View\View;

class FrontQuizController extends Controller
{
    public function engine(Quiz $quiz): View
    {
        return view('pages.quiz.engine', compact('quiz'));
    }
}
