<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Content;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeaderboardService
{

    public function getLeaderboardForContent(Content $content): Collection
    {
        // Ambil semua siswa yang terdaftar di kelas ini
        $studentsInClass = $content->classroom->students()->get();

        if ($studentsInClass->isEmpty()) {
            return collect();
        }

        // Bungkus konten ke dalam collection agar bisa digunakan oleh metode bulk
        $contentsCollection = collect([$content]);

        // Gunakan kembali helper yang efisien untuk mengambil data pengerjaan
        if ($content->contentable_type === Quiz::class) {
            $submissions = $this->getBulkQuizSubmissions($contentsCollection)
                ->first() ?? collect(); // Ambil grup pertama (dan satu-satunya)
            return $this->buildLeaderboardFromSubmissions($submissions, $studentsInClass);
        } else { // Material::class
            $completions = $this->getBulkMaterialCompletions($contentsCollection)
                ->first() ?? collect(); // Ambil grup pertama (dan satu-satunya)
            return $this->buildLeaderboardFromSubmissions($completions, $studentsInClass, $content->points);
        }
    }


    /**
     * Mendapatkan semua leaderboard untuk semua konten dari kelas-kelas yang diikuti siswa.
     * Versi ini dioptimalkan untuk menghindari masalah N+1 query.
     *
     * @param User $student
     * @return Collection
     */
    public function getLeaderboardsForStudent(User $student): Collection
    {
        $classroomIds = $student->classrooms()->pluck('classrooms.id');
        if ($classroomIds->isEmpty()) {
            return collect();
        }

        $contents = Content::whereIn('classroom_id', $classroomIds)
            ->with(['classroom', 'contentable'])
            ->orderBy('classroom_id')
            ->orderBy('order')
            ->get();

        if ($contents->isEmpty()) {
            return collect();
        }

        $studentsByClassroom = $this->getStudentIdsByClassroom($classroomIds);
        $quizSubmissions = $this->getBulkQuizSubmissions($contents);
        $materialCompletions = $this->getBulkMaterialCompletions($contents);

        return $contents->map(function (Content $content) use ($studentsByClassroom, $quizSubmissions, $materialCompletions) {

            $studentsInClass = User::find($studentsByClassroom->get($content->classroom_id) ?? []);

            if ($content->contentable_type === Quiz::class) {
                $submissions = $quizSubmissions->get($content->contentable_id, collect());
                $content->leaderboard = $this->buildLeaderboardFromSubmissions($submissions, $studentsInClass);
            } else { // Material::class
                $completions = $materialCompletions->get($content->id, collect());
                $content->leaderboard = $this->buildLeaderboardFromSubmissions($completions, $studentsInClass, $content->points);
            }

            return $content;
        });
    }

    /**
     * Mengambil ID siswa untuk setiap kelas dalam satu query.
     *
     * @param Collection $classroomIds
     * @return Collection
     */
    private function getStudentIdsByClassroom(Collection $classroomIds): Collection
    {
        return DB::table('classroom_students')
            ->whereIn('classroom_id', $classroomIds)
            ->get()
            ->groupBy('classroom_id')
            ->map(fn($group) => $group->pluck('student_id'));
    }

    /**
     * Mengambil semua data pengerjaan kuis yang relevan dalam satu query.
     *
     * @param Collection $contents
     * @return Collection
     */
    private function getBulkQuizSubmissions(Collection $contents): Collection
    {
        $quizIds = $contents->where('contentable_type', Quiz::class)->pluck('contentable.id');
        if ($quizIds->isEmpty()) {
            return collect();
        }

        return DB::table('quiz_submissions')
            ->whereIn('quiz_id', $quizIds)
            ->select(
                'quiz_id',
                'student_id',
                'score',
                'duration_seconds',
                'completed_at',
                'student_id as user_id' // Alias untuk konsistensi
            )
            ->orderBy('score', 'desc')
            ->orderBy('duration_seconds', 'asc')
            ->get()
            ->groupBy('quiz_id');
    }

    /**
     * Mengambil semua data penyelesaian materi yang relevan dalam satu query.
     *
     * @param Collection $contents
     * @return Collection
     */
    private function getBulkMaterialCompletions(Collection $contents): Collection
    {
        $materialContentIds = $contents->where('contentable_type', Material::class)->pluck('id');
        if ($materialContentIds->isEmpty()) {
            return collect();
        }

        return DB::table('content_student')
            ->whereIn('content_id', $materialContentIds)
            ->select('content_id', 'user_id', 'completed_at')
            ->orderBy('completed_at', 'asc')
            ->get()
            ->groupBy('content_id');
    }

    /**
     * Merakit data mentah pengerjaan dan daftar siswa menjadi struktur leaderboard yang bersih.
     *
     * @param Collection $submissions Data mentah dari query
     * @param Collection $allStudentsInClass Koleksi model User
     * @param integer $pointsIfMaterial Poin default jika ini adalah materi
     * @return Collection
     */
    private function buildLeaderboardFromSubmissions(Collection $submissions, Collection $allStudentsInClass, int $pointsIfMaterial = 0): Collection
    {
        $rankedSubmissions = $submissions->map(function ($submission, $index) {
            $submission->rank = $index + 1;
            return $submission;
        });

        $submissionMap = $rankedSubmissions->keyBy('user_id');

        return $allStudentsInClass->map(function (User $student) use ($submissionMap, $pointsIfMaterial) {
            $submission = $submissionMap->get($student->id);

            return [
                'rank' => $submission->rank ?? null,
                'user' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'avatar' => $student->avatar, // Asumsi ada accessor avatar_url
                ],
                'score' => $submission->score ?? ($submission ? $pointsIfMaterial : null),
                'duration_seconds' => $submission->duration_seconds ?? null,
                'completed_at' => $submission->completed_at ?? null,
            ];
        })
            ->sortBy(function ($studentData) {
                return $studentData['rank'] ?? INF;
            })
            ->values(); // values() untuk mereset key array
    }
}
