<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Display a listing of all content leaderboards from enrolled classrooms.
     */
    public function index()
    {
        // Get all classrooms user is enrolled in
        $enrolledClassrooms = auth()->user()->classrooms()->with([
            'contents.contentable',
            'contents.completedByUser' => function ($query) {
                $query->select('users.id', 'users.name')
                    ->withPivot('completion_time', 'points_earned', 'score', 'created_at', 'updated_at');
            }
        ])->get();

        // Process each content to get leaderboard data
        $contentLeaderboards = collect();

        foreach ($enrolledClassrooms as $classroom) {
            foreach ($classroom->contents as $content) {
                $leaderboardData = $this->getContentLeaderboardData($content);
                $userRank = $this->getUserRankInContent($content, auth()->id());
                $totalParticipants = $this->getTotalParticipants($content, $classroom->id);

                $contentLeaderboards->push([
                    'content' => $content,
                    'classroom' => $classroom,
                    'leaderboard' => $leaderboardData,
                    'user_rank' => $userRank,
                    'total_participants' => $totalParticipants
                ]);
            }
        }

        return view('pages.user.leaderboard.index', compact('contentLeaderboards'));
    }

    /**
     * Get leaderboard data for specific content (top 5 only for overview)
     */
    private function getContentLeaderboardData(Content $content, $limit = 5)
    {
        $query = User::select([
            'users.id',
            'users.name',
            'content_users.created_at as started_at',
            'content_users.updated_at as completed_at',
            'content_users.completion_time',
            'content_users.points_earned',
            'content_users.score'
        ])
            ->join('classroom_user', 'users.id', '=', 'classroom_user.user_id')
            ->leftJoin('content_users', function ($join) use ($content) {
                $join->on('users.id', '=', 'content_users.user_id')
                    ->where('content_users.content_id', $content->id);
            })
            ->where('classroom_user.classroom_id', $content->classroom_id);

        if ($content->contentable_type === Quiz::class) {
            // For quiz: order by completion status first, then score, then by time
            $query->leftJoin('quiz_submissions', function ($join) use ($content) {
                $join->on('users.id', '=', 'quiz_submissions.user_id')
                    ->where('quiz_submissions.quiz_id', $content->contentable_id)
                    ->where('quiz_submissions.is_completed', true);
            })
                ->addSelect([
                    'quiz_submissions.total_questions',
                    'quiz_submissions.correct_answers',
                    'quiz_submissions.time_spent as quiz_time_spent'
                ])
                // First: Order by completion status (completed users first)
                ->orderByDesc(DB::raw('content_users.score IS NOT NULL'))
                // Second: Order by score (highest first)
                ->orderByDesc('content_users.score')
                // Third: Order by time spent (fastest first, for same scores)
                ->orderBy('quiz_submissions.time_spent')
                // Fourth: Order by submission time (earliest first, for tie-breaking)
                ->orderBy('content_users.updated_at');
        } else {
            // For material: order by completion status, then by speed
            $query->orderByDesc(DB::raw('content_users.updated_at IS NOT NULL'))
                ->orderBy('content_users.completion_time')
                ->orderByDesc('content_users.points_earned');
        }

        return $query->limit($limit)->get()->map(function ($user, $index) use ($content) {
            $user->is_completed = !is_null($user->completed_at);
            $user->content_type = $content->contentable_type === Quiz::class ? 'quiz' : 'material';
            $user->rank = $index + 1;
            return $user;
        });
    }

    /**
     * Get user's rank in specific content
     */
    private function getUserRankInContent(Content $content, $userId)
    {
        $allUsers = $this->getContentLeaderboardData($content, 1000); // Get all users

        foreach ($allUsers as $index => $user) {
            if ($user->id == $userId) {
                return $index + 1;
            }
        }

        return null; // User not found in leaderboard
    }

    /**
     * Get total participants count for a content within a classroom
     */
    private function getTotalParticipants(Content $content, $classroomId)
    {
        return User::join('classroom_user', 'users.id', '=', 'classroom_user.user_id')
            ->where('classroom_user.classroom_id', $classroomId)
            ->count();
    }
}
