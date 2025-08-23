<?php

namespace App\Services;

use App\Models\Content;
use App\Models\User;

class LeaderboardService
{

    public function getLeaderboardByContent(
        Content $content,
        int $limit = 10,
    ) {

        $students = $content->students()
            ->withPivot('score', 'completed_at', 'status')
            ->orderBy('pivot_score', 'desc')
            ->orderBy('pivot_completed_at', 'desc')
            ->limit($limit)
            ->get();

        $leaderboard = $students->map(
            function ($student) {
                return [
                    'student_id' => $student->id,
                    'name' => $student->name,
                    'score' => $student->pivot->score,
                    'completed_at' => $student->pivot->completed_at,
                    'status' => $student->pivot->status,
                ];
            }
        );

        return $leaderboard->values()->all();
    }

    public function getLeaderboardByClassroom(
        int $classroomId,
        int $limit = 10,
    ) {
        $contents = Content::where('classroom_id', $classroomId)
            ->with('students')
            ->get();

        $leaderboard = collect();

        foreach ($contents as $content) {
            $contentLeaderboard = $this->getLeaderboardByContent($content, $limit);
            $leaderboard = $leaderboard->merge($contentLeaderboard);
        }

        return $leaderboard->sortByDesc('score')->take($limit)->values()->all();
    }

    public function getLeaderboardByUser(
        User $user,
        int $limit = 10,
    ) {
        $contents = $user->contents()
            ->with('students')
            ->get();

        $leaderboard = collect();

        foreach ($contents as $content) {
            $contentLeaderboard = $this->getLeaderboardByContent($content, $limit);
            $leaderboard = $leaderboard->merge($contentLeaderboard);
        }

        return $leaderboard->sortByDesc('score')->take($limit)->values()->all();
    }
}
