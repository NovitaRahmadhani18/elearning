<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizes = \App\Models\Quiz::query()
            ->with('classroom')
            ->latest()
            ->paginate(10);
        return view('pages.teacher.quizes.index', compact('quizes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        return view('pages.teacher.quizes.show', compact('quiz'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return to_route('teacher.quizes.index')
            ->with('success', 'Quiz deleted successfully.');
    }
}
