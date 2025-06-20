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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classrooms = Classroom::pluck('title', 'id');
        return view('pages.teacher.quizes.create', compact('classrooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // Menyimpan kuis baru beserta pertanyaannya
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'classroom_id' => 'required|exists:classrooms,id',
            'time_limit' => 'required|integer|min:0',
            'custom_time_limit' => 'nullable|boolean',
            'start_time' => 'required|date',
            'due_time' => 'required|date|after_or_equal:start_time',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.correct_option_index' => 'required|integer',
        ]);

        DB::transaction(function () use ($request) {
            $quiz = new Quiz();
            $quiz->title = $request->title;
            $quiz->description = $request->description;
            $quiz->start_time = $request->start_time;
            $quiz->due_time = $request->due_time;
            $quiz->classroom_id = $request->classroom_id;

            // Menyesuaikan time_limit berdasarkan input
            if ($request->boolean('custom_time_limit')) {
                // Konversi dari menit (input custom) ke detik
                $quiz->time_limit = $request->time_limit * 60;
            } else {
                // Gunakan nilai dari select (sudah dalam detik)
                $quiz->time_limit = $request->time_limit;
            }

            $quiz->save();

            // Loop untuk menyimpan setiap pertanyaan dan pilihan jawabannya
            foreach ($request->questions as $qData) {
                $question = $quiz->questions()->create(['question_text' => $qData['text']]);

                foreach ($qData['options'] as $oIndex => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct' => ($oIndex == $qData['correct_option_index'])
                    ]);
                }
            }
        });

        return to_route('teacher.quizes.index')
            ->with('success', 'Quiz created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        return view('pages.teacher.quizes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        $quiz->load('questions.options');
        $classrooms = Classroom::pluck('title', 'id');
        $questions = $quiz->questions->map(function ($q) {
            return [
                'id' => $q->id,
                'text' => $q->question_text,
                'correctOptionIndex' => $q->options->search(fn($o) => $o->is_correct),
                'options' => $q->options->map(fn($o) => ['id' => $o->id, 'text' => $o->option_text]),
            ];
        });
        return view('pages.teacher.quizes.edit', compact('quiz', 'classrooms', 'questions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'classroom_id' => 'required|exists:classrooms,id',
            'time_limit' => 'required|integer|min:0',
            'custom_time_limit' => 'nullable|boolean',
            'start_time' => 'required|date',
            'due_time' => 'required|date|after_or_equal:start_time',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:questions,id', // Validasi ID pertanyaan yang ada
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.id' => 'nullable|exists:question_options,id', // Validasi ID opsi yang ada
            'questions.*.options.*.text' => 'required|string',
            'questions.*.correct_option_index' => 'required|integer',
        ]);

        DB::transaction(function () use ($request, $quiz) {
            // 1. Update data utama kuis
            $quizData = $request->only(['title', 'description', 'start_time', 'due_time']);
            // Sesuaikan nama kolom jika berbeda di form dan DB
            $quizData['classroom_id'] = $request->classroom_id;

            // Logika untuk time_limit
            if ($request->boolean('custom_time_limit')) {
                $quizData['time_limit'] = $request->time_limit * 60; // Konversi menit ke detik
            } else {
                $quizData['time_limit'] = $request->time_limit; // Ambil nilai langsung (dalam detik)
            }
            $quiz->update($quizData);

            // 2. Sinkronisasi Pertanyaan
            $submittedQuestionIds = [];
            foreach ($request->questions as $qData) {
                $questionData = ['question_text' => $qData['text']];

                // Update atau buat pertanyaan baru
                $question = $quiz->questions()->updateOrCreate(
                    ['id' => $qData['id'] ?? null], // Cari berdasarkan ID, atau null jika baru
                    $questionData
                );

                $submittedOptionIds = [];
                // 3. Sinkronisasi Opsi Jawaban untuk setiap pertanyaan
                foreach ($qData['options'] as $oIndex => $optionData) {
                    $option = $question->options()->updateOrCreate(
                        ['id' => $optionData['id'] ?? null], // Cari berdasarkan ID, atau null jika baru
                        [
                            'option_text' => $optionData['text'],
                            'is_correct' => ($oIndex == $qData['correct_option_index'])
                        ]
                    );
                    $submittedOptionIds[] = $option->id;
                }

                // Hapus opsi yang tidak lagi disubmit untuk pertanyaan ini
                $question->options()->whereNotIn('id', $submittedOptionIds)->delete();
                $submittedQuestionIds[] = $question->id;
            }

            // Hapus pertanyaan yang tidak lagi disubmit
            $quiz->questions()->whereNotIn('id', $submittedQuestionIds)->delete();
        });

        return to_route('teacher.quizes.index')
            ->with('success', 'Quiz updated successfully.');
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
