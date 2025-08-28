<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Resources\ContentResource;
use App\Models\Classroom;
use App\Models\Content;
use App\Services\ContentService;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    protected $contentService;
    protected $leaderboardService;

    public function __construct(ContentService $contentService, LeaderboardService $leaderboardService)
    {
        $this->contentService = $contentService;
        $this->leaderboardService = $leaderboardService;
    }


    public function index()
    {
        return inertia('teacher/quiz/index', [
            'quizzes' => $this->contentService->getQuizzes(),
        ]);
    }

    public function show(Content $content)
    {

        $leaderboardContent = $this->leaderboardService->getLeaderboardForContent($content);

        $content->load(['classroom', 'contentable']);

        $content->leaderboard = $leaderboardContent;


        return inertia('teacher/quiz/show', [
            'quiz' => ContentResource::make($content),
        ]);
    }

    public function create()
    {
        return inertia('teacher/quiz/create', [
            'classrooms' => $this->contentService->getClassrooms(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'classroom_id' => [
                'required',
                'integer',
                Rule::exists('classrooms', 'id')->where('teacher_id', auth()->id())
            ],
            'points' => ['required', 'integer', 'min:0'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after_or_equal:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:1'],

            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 1MB max

            'questions.*.answers' => ['required', 'array', 'min:2', 'max:5'],
            'questions.*.answers.*.answer_text' => ['required_without:questions.*.answers.*.image', 'nullable', 'string', 'max:255'], // Teks wajib jika tidak ada gambar
            'questions.*.answers.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'questions.*.answers.*.is_correct' => ['required', 'boolean'],
        ]);

        try {
            $this->contentService->createQuiz($validated);
            return to_route('teacher.quizzes.index')->with('success', 'Quiz created successfully.');
        } catch (\Throwable $th) {
            return to_route('teacher.quizzes.create')->withErrors('error', 'Failed to create quiz: ' . $th->getMessage())->withInput();
        }
    }

    public function edit(Content $content)
    {
        return inertia('teacher/quiz/edit', [
            'quiz' => ContentResource::make($content->load('contentable')),
            'classrooms' => $this->contentService->getClassrooms(),
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuizRequest $request, Content $content)
    {
        try {
            $this->contentService->updateQuiz($content, $request->validated());
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Failed to update quiz: ' . $e->getMessage());
        }

        return redirect()->route('teacher.quizzes.index')->with('success', 'Quiz updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $this->contentService->deleteContent($content);

        return redirect()->back()->with('success', 'Quiz deleted successfully.');
    }
}
