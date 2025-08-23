<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Facades\DataTable;
use App\Http\Resources\ClassroomResource;
use App\Http\Resources\ContentResource;
use App\Models\Classroom;
use App\Models\Content;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\SubmissionAnswer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ContentService
{

    public function getMaterials()
    {
        $query =  Content::with('contentable')
            ->when(auth()->user()->role == RoleEnum::TEACHER, function ($query) {
                $query->whereHas('classroom', function ($q) {
                    $q->where('teacher_id', auth()->id());
                });
            })
            ->where('contentable_type', Material::class);

        $result = DataTable::query($query)
            ->searchable(['title'])
            ->make();

        return ContentResource::collection($result);
    }

    public function getQuizzes()
    {
        $query = Content::with('contentable')
            ->when(auth()->user()->role == RoleEnum::TEACHER, function ($query) {
                $query->whereHas('classroom', function ($q) {
                    $q->where('teacher_id', auth()->id());
                });
            })
            ->where('contentable_type', Quiz::class);

        $result = DataTable::query($query)
            ->searchable(['title'])
            ->make();

        return ContentResource::collection($result);
    }

    public function getClassrooms()
    {
        $query = Classroom::query()
            ->when(auth()->user()->role == RoleEnum::TEACHER, function ($query) {
                $query->where('teacher_id', auth()->id());
            });

        return ClassroomResource::collection(
            $query->get()
        );
    }


    public function createMaterial(array $data, Classroom $classroom): Content
    {
        return DB::transaction(function () use ($data, $classroom) {

            // 1. Buat entitas 'Material' terlebih dahulu
            $material = Material::create([
                'body' => $data['body'],
                'attachment_path' => $this->handleFileUpload($data['attachment'] ?? null),
            ]);

            // 2. Hitung 'order' baru secara otomatis
            //    Cari nilai 'order' tertinggi di kelas ini, default ke 0 jika belum ada konten.
            $lastOrder = $classroom->contents()->max('order') ?? 0;
            $newOrder = $lastOrder + 1;

            // 3. Buat entitas 'Content' utama melalui relasi dari 'Material'
            //    Ini secara otomatis akan mengisi 'contentable_id' dan 'contentable_type'.
            $content = $material->content()->create([
                'classroom_id' => $classroom->id,
                'title' => $data['title'],
                'points' => $data['points'],
                'order' => $newOrder, // Gunakan order baru yang sudah dihitung
            ]);

            return $content;
        });
    }

    protected function handleFileUpload($file)
    {
        if (!$file) {
            return null;
        }

        return $file->store('attachments', 'public');
    }

    public function updateMaterial(Content $content, array $data): Content
    {
        return DB::transaction(function () use ($content, $data) {
            $content->update([
                'title' => $data['title'],
                'points' => $data['points'],
            ]);

            $material = $content->contentable;

            $attachmentPath = $material->attachment_path;

            // 1. Jika ada file attachment BARU yang diupload
            if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
                // Hapus file lama jika ada
                if ($material->attachment_path) {
                    Storage::disk('public')->delete($material->attachment_path);
                }
                // Simpan file baru
                $attachmentPath = $data['attachment']->store('attachments', 'public');
            }
            // 2. Jika flag remove_attachment diset ke true
            else if (isset($data['remove_attachment']) && $data['remove_attachment']) {
                if ($material->attachment_path) {
                    Storage::disk('public')->delete($material->attachment_path);
                }
                $attachmentPath = null;
            }

            $material->body = $data['body'];
            $material->attachment_path = $attachmentPath;
            $material->save();

            return $content;
        });
    }

    public function createQuiz(array $data): Content
    {

        return DB::transaction(function () use ($data) {


            // 1. Buat record di tabel 'quizzes' (detail dari konten)
            $quiz = Quiz::create([
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'duration_minutes' => $data['duration_minutes'],
            ]);

            // 2. Tentukan urutan konten secara otomatis
            $lastOrder = Content::where('classroom_id', $data['classroom_id'])->max('order') ?? 0;

            // 3. Buat record 'Content' utama menggunakan polymorphic relationship
            $content = $quiz->content()->create([
                'classroom_id' => $data['classroom_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'points' => $data['points'],
                'order' => $lastOrder + 1,
            ]);

            // 4. Loop untuk membuat setiap pertanyaan dan jawabannya
            foreach ($data['questions'] as $questionData) {
                $questionImagePath = null;
                if (isset($questionData['image']) && $questionData['image'] instanceof UploadedFile) {
                    $questionImagePath = $questionData['image']->store('question-images', 'public');
                }

                $question = $quiz->questions()->create([
                    'question_text' => $questionData['question_text'],
                    'image_path' => $questionImagePath,
                ]);

                foreach ($questionData['answers'] as $answerData) {
                    $answerImagePath = null;
                    if (isset($answerData['image']) && $answerData['image'] instanceof UploadedFile) {
                        $answerImagePath = $answerData['image']->store('answer-images', 'public');
                    }

                    $question->answers()->create([
                        'answer_text' => $answerData['answer_text'],
                        'image_path' => $answerImagePath,
                        'is_correct' => $answerData['is_correct'],
                    ]);
                }
            }

            return $content;
        });
    }

    public function updateQuiz(Content $content, array $data): Content
    {
        return DB::transaction(function () use ($content, $data) {
            $quiz = $content->contentable;

            // 1. HAPUS SEMUA PERTANYAAN & JAWABAN LAMA BESERTA FILENYA
            foreach ($quiz->questions as $question) {
                // Hapus gambar pertanyaan lama
                if ($question->image_path) {
                    Storage::disk('public')->delete($question->image_path);
                }
                // Hapus gambar jawaban lama
                foreach ($question->answers as $answer) {
                    if ($answer->image_path) {
                        Storage::disk('public')->delete($answer->image_path);
                    }
                }
                // Hapus record jawaban & pertanyaan dari database
                $question->answers()->delete();
                $question->delete();
            }

            // 2. UPDATE RECORD UTAMA (QUIZ & CONTENT)
            $quiz->update([
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'duration_minutes' => $data['duration_minutes'],
            ]);

            $content->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'points' => $data['points'],
            ]);

            // 3. BUAT ULANG PERTANYAAN & JAWABAN DARI DATA BARU
            //    (Logika ini sama persis dengan di metode createQuiz)
            foreach ($data['questions'] as $questionData) {
                $questionImagePath = $questionData['_existingImage'] ?? null;
                if (isset($questionData['image']) && $questionData['image'] instanceof UploadedFile) {
                    $questionImagePath = $questionData['image']->store('question-images', 'public');
                }

                $question = $quiz->questions()->create([
                    'question_text' => $questionData['question_text'],
                    'image_path' => $questionImagePath,
                ]);

                foreach ($questionData['answers'] as $answerData) {
                    $answerImagePath = $answerData['_existingImage'] ?? null;
                    if (isset($answerData['image']) && $answerData['image'] instanceof UploadedFile) {
                        $answerImagePath = $answerData['image']->store('answer-images', 'public');
                    }

                    $question->answers()->create([
                        'answer_text' => $answerData['answer_text'],
                        'image_path' => $answerImagePath,
                        'is_correct' => $answerData['is_correct'],
                    ]);
                }
            }

            return $content->fresh(); // Ambil versi terbaru dari database
        });
    }

    public function deleteContent(Content $content): void
    {
        DB::transaction(function () use ($content) {
            // The contentable model (Material or Quiz) will be deleted
            // automatically due to the cascade on delete constraint
            // defined in the migration. However, to be explicit and to
            // handle potential file deletions, we do it manually.
            $content->contentable->delete();
            $content->delete();
        });
    }


    public function selectAnswer(Content $content, array $data)
    {

        // Find the current submission for the user
        $submission = auth()->user()->quizSubmissions()
            ->where('quiz_id', $content->contentable->id)
            ->whereNull('completed_at')
            ->first();

        if (!$submission) {
            throw new \Exception('You have not started this quiz yet.');
        }

        // Check if the submission is already completed
        if ($submission->completed_at) {
            throw new \Exception('This quiz has already been completed.');
        }

        return DB::transaction(function () use ($submission, $data, $content) {

            // Check if the question exists in the quiz
            $question = $content->contentable->questions()->findOrFail($data['question_id']);

            // Check if the answer exists for the question
            $answer = $question->answers()->findOrFail($data['answer_id']);

            // Save the answer to the pivot table
            SubmissionAnswer::updateOrCreate(
                [
                    'quiz_submission_id' => $submission->id,
                    'question_id' => $question->id,
                ],
                [
                    'answer_id' => $answer->id,
                    'is_correct' => $answer->is_correct,
                ]
            );

            return $submission->load('quiz.questions.answers', 'answers');
        });
    }
}
