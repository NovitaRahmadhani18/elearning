<?php

namespace Tests\Unit\Achievements;

use App\Achievements\TopRank;
use App\Models\Content;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;
use App\Services\LeaderboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TopRankTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_awards_if_user_is_in_top_3()
    {
        $user = User::factory()->create();
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();
        $otherUser3 = User::factory()->create();

        $quiz = Quiz::factory()->create();
        $content = $quiz->content()->create([
            'classroom_id' => \App\Models\Classroom::factory()->create()->id,
            'title' => 'Test Content',
            'points' => 100,
            'order' => 1,
        ]);
        $submission = QuizSubmission::factory()->create(['student_id' => $user->id, 'quiz_id' => $quiz->id]);

        // Mock LeaderboardService
        $mockLeaderboardService = $this->mock(LeaderboardService::class);
        $mockLeaderboardService->shouldReceive('getLeaderboardForContent')
            ->withArgs(function ($argContent) use ($content) {
                return $argContent->is($content);
            })
            ->andReturn(new Collection([
                ['user' => ['id' => $otherUser1->id]],
                ['user' => ['id' => $otherUser2->id]],
                ['user' => ['id' => $user->id]], // User is in top 3
                ['user' => ['id' => $otherUser3->id]],
            ]));

        $achievement = new TopRank($mockLeaderboardService);

        $this->assertTrue($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_award_if_user_is_not_in_top_3()
    {
        $user = User::factory()->create();
        $otherUser1 = User::factory()->create();
        $otherUser2 = User::factory()->create();
        $otherUser3 = User::factory()->create();

        $quiz = Quiz::factory()->create();
        $content = $quiz->content()->create([
            'classroom_id' => \App\Models\Classroom::factory()->create()->id,
            'title' => 'Test Content',
            'points' => 100,
            'order' => 1,
        ]);
        $submission = QuizSubmission::factory()->create(['student_id' => $user->id, 'quiz_id' => $quiz->id]);

        // Mock LeaderboardService
        $mockLeaderboardService = $this->mock(LeaderboardService::class);
        $mockLeaderboardService->shouldReceive('getLeaderboardForContent')
            ->andReturn(new Collection([
                ['user' => ['id' => $otherUser1->id]],
                ['user' => ['id' => $otherUser2->id]],
                ['user' => ['id' => $otherUser3->id]],
                ['user' => ['id' => $user->id]], // User is not in top 3
            ]));

        $achievement = new TopRank($mockLeaderboardService);

        $this->assertFalse($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_award_if_submission_context_is_missing()
    {
        $user = User::factory()->create();
        $mockLeaderboardService = $this->mock(LeaderboardService::class);
        $achievement = new TopRank($mockLeaderboardService);

        $this->assertFalse($achievement->check($user, []));
    }
}
