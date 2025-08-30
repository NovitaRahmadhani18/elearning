<?php

namespace Tests\Unit\Achievements;

use App\Achievements\TopRanker;
use App\Models\Content;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;
use App\Services\LeaderboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Tests\TestCase;

class TopRankerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_unlocks_achievement_when_user_is_in_top_three()
    {
        // Arrange
        $user = User::factory()->create();
        $content = Content::factory()->for(Quiz::factory(), 'contentable')->create();
        $submission = QuizSubmission::factory()->create(['student_id' => $user->id, 'quiz_id' => $content->contentable->id]);

        $leaderboardData = new Collection([
            (object)['user' => $user],
            (object)['user' => User::factory()->create()],
            (object)['user' => User::factory()->create()],
        ]);

        $this->mock(LeaderboardService::class, function (MockInterface $mock) use ($content, $leaderboardData) {
            $mock->shouldReceive('getLeaderboardForContent')
                 ->with(\Mockery::on(function ($argument) use ($content) {
                     return $argument->id === $content->id;
                 }))
                 ->andReturn($leaderboardData);
        });

        $achievement = app(TopRanker::class);

        // Act & Assert
        $this->assertTrue($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_unlock_achievement_when_user_is_not_in_top_three()
    {
        // Arrange
        $user = User::factory()->create();
        $content = Content::factory()->for(Quiz::factory(), 'contentable')->create();
        $submission = QuizSubmission::factory()->create(['student_id' => $user->id, 'quiz_id' => $content->contentable->id]);

        $leaderboardData = new Collection([
            (object)['user' => User::factory()->create()],
            (object)['user' => User::factory()->create()],
            (object)['user' => User::factory()->create()],
        ]);

        $this->mock(LeaderboardService::class, function (MockInterface $mock) use ($content, $leaderboardData) {
            $mock->shouldReceive('getLeaderboardForContent')
                 ->with(\Mockery::on(function ($argument) use ($content) {
                     return $argument->id === $content->id;
                 }))
                 ->andReturn($leaderboardData);
        });

        $achievement = app(TopRanker::class);

        // Act & Assert
        $this->assertFalse($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_unlock_achievement_if_no_submission_in_context()
    {
        // Arrange
        $user = User::factory()->create();
        $achievement = app(TopRanker::class);

        // Act & Assert
        $this->assertFalse($achievement->check($user));
    }
}
