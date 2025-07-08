<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Material;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = auth()->user()->classrooms()->with(['teacher'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pages.user.classroom.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function joinForm(Classroom $classroom)
    {
        if (auth()->user()->classrooms->contains($classroom)) {
            return to_route('user.classroom.show', $classroom)
                ->with('success', 'You are already enrolled in this classroom.');
        }

        // Show the join form with the classroom code pre-filled
        return view('pages.user.classroom.join', compact('classroom'));
    }

    public function join(Request $request, Classroom $classroom)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        if ($request->code !== $classroom->secret_code) {
            return to_route('user.classroom.join.form', $classroom->invite_code)
                ->with('error', 'Invalid classroom code.');
        }

        if (auth()->user()->classrooms->contains($classroom)) {
            return to_route('user.classroom.index')
                ->with('error', 'You are already enrolled in this classroom.');
        }

        // Enroll user in the classroom
        auth()->user()->classrooms()->attach($classroom);

        return to_route('user.classroom.show', $classroom)
            ->with('success', 'You have successfully joined the classroom.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {

        $classroom->load(['teacher', 'contents.contentable']);

        if (!auth()->user()->classrooms->contains($classroom)) {
            return to_route('user.classroom.index')
                ->with('error', 'You are not enrolled in this classroom.');
        }

        // check completed contents
        $completedContents = auth()->user()->completedContents()
            ->where('classroom_id', $classroom->id)
            ->pluck('contentable.id')
            ->toArray();

        return view('pages.user.classroom.show', compact('classroom', 'completedContents'));
    }


    public function showMaterial(Classroom $classroom, Material $material)
    {
        $currentContent = $material->contents()
            ->where('classroom_id', $classroom->id)
            ->firstOrFail();

        auth()->user()->addPoints(10);

        if (
            !$currentContent->isCompletedByUser()
        ) {
            auth()->user()->completedContents()->syncWithoutDetaching($currentContent->id);
        }
        return view('pages.user.classroom.show-material', compact('classroom', 'material'));
    }

    public function showQuiz(Classroom $classroom, Quiz $quiz)
    {
        return view('pages.user.classroom.show-quiz', compact('classroom', 'quiz'));
    }

    public function reviewQuiz(Classroom $classroom, Quiz $quiz)
    {
        // Verify user is enrolled in the classroom
        if (!auth()->user()->classrooms()->where('classroom_id', $classroom->id)->exists()) {
            return to_route('user.classroom.index')
                ->with('error', 'You are not enrolled in this classroom.');
        }

        // Check if user has completed the quiz
        $submission = $quiz->submissions()
            ->where('user_id', auth()->id())
            ->where('is_completed', true)
            ->first();

        if (!$submission) {
            return to_route('user.classroom.show', $classroom)
                ->with('error', 'You have not completed this quiz yet.');
        }

        // Load quiz with questions and options
        $quiz->load(['questions.options']);

        // Get user's answers
        $userAnswers = $submission->quizAnswers()->with('question', 'selectedOption')->get();

        return view('pages.user.classroom.review-quiz', compact('classroom', 'quiz', 'submission', 'userAnswers'));
    }
}
