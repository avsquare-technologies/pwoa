<?php

namespace App\Livewire\Public;

use App\Models\Certificate;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class QuizEngine extends Component
{
    public Quiz $quiz;

    public $questions;

    public $answers = [];

    public $currentQuestionIndex = 0;

    public $isCompleted = false;

    public $score = 0;

    public $passed = false;

    public $certificate = null;

    public function mount(Quiz $quiz)
    {
        if (!Auth::check() || !Auth::user()->wallet) {
            session()->flash('error', 'A connected wallet is mandatory to attempt this quiz and receive your NFT Certificate.');
            $this->redirect(route('wallet.index'), navigate: true);
            return;
        }

        $this->quiz = $quiz->load('questions');
        $this->questions = $this->quiz->questions;
    }

    public function submitAnswer($answerText)
    {
        $this->answers[$this->currentQuestionIndex] = $answerText;

        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        } else {
            $this->calculateResults();
        }
    }

    protected function calculateResults()
    {
        $correctCount = 0;
        foreach ($this->questions as $index => $question) {
            if (($this->answers[$index] ?? '') === $question->correct_answer) {
                $correctCount++;
            }
        }

        $this->score = round(($correctCount / count($this->questions)) * 100);
        $this->passed = $this->score >= $this->quiz->pass_percentage;
        $this->isCompleted = true;

        $quizResult = QuizResult::create([
            'user_id' => Auth::id(),
            'quiz_id' => $this->quiz->id,
            'score' => $this->score,
            'passed' => $this->passed,
        ]);

        // Issue or update certificate if the user passed
        if ($this->passed && $this->quiz->course_id) {
            $course = \App\Models\Course::find($this->quiz->course_id);
            $this->certificate = app(\App\Services\CertificateService::class)
                ->generateForCourse(Auth::user(), $course, $this->score, $quizResult->id);

            // Mark the course as completed in the enrollment pivot
            Auth::user()->enrolledCourses()->updateExistingPivot(
                $this->quiz->course_id,
                ['completed_at' => now()]
            );
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.public.quiz-engine');
    }
}
