<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentResource;
use App\Models\Classroom;
use App\Models\Content;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    protected $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function index()
    {
        return inertia('teacher/quiz/index', [
            'quizzes' => $this->contentService->getQuizzes(),
            'classrooms' => $this->contentService->getClassrooms(),
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
            'questions.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'], // 1MB max

            'questions.*.answers' => ['required', 'array', 'min:2', 'max:5'],
            'questions.*.answers.*.answer_text' => ['required_without:questions.*.answers.*.image', 'nullable', 'string', 'max:255'], // Teks wajib jika tidak ada gambar
            'questions.*.answers.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
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
    public function update(Request $request, Content $content)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:0'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:1'],

            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.image' => ['nullable', 'image', 'max:1024'],
            'questions.*.answers' => ['required', 'array', 'min:2', 'max:5'],
            'questions.*.answers.*.answer_text' => ['required_without:questions.*.answers.*.image', 'nullable', 'string', 'max:255'],
            'questions.*.answers.*.image' => ['nullable', 'image', 'max:1024'],
            'questions.*.answers.*.is_correct' => ['required', 'boolean'],
        ]);

        try {
            $this->contentService->updateQuiz($content, $validated);
            return to_route('teacher.quizzes.index')->with('success', 'Quiz updated successfully.');
        } catch (\Throwable $th) {
            return to_route('teacher.quizzes.edit', $content->id)
                ->withErrors('error', 'Failed to update quiz: ' . $th->getMessage())
                ->withInput();
        }
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
