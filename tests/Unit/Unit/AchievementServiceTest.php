<?php

namespace Tests\Unit;

use App\Contracts\AchievementContract;
use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\QuizSubmission;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AchievementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed achievements for testing
        $this->artisan('db:seed', ['--class' => 'AchievementSeeder']);
    }

    /** @test */
    public function it_awards_achievement_on_quiz_completion_if_criteria_met()
    {
        Event::fake(); // Prevent actual events from dispatching

        $user = User::factory()->create();
        $submission = QuizSubmission::factory()->create(['student_id' => $user->id, 'score' => 90]); // Score for QuizChampion

        // Mock a specific achievement to return true
        $mockAchievement = $this->mock(AchievementContract::class);
        $mockAchievement->shouldReceive('slug')->andReturn('quiz-champion');
        $mockAchievement->shouldReceive('check')->with($user, ['submission' => $submission])->andReturn(true);

        // Instantiate service with the mocked achievement object
        $service = new AchievementService([$mockAchievement]);

        $service->processQuizCompletion($user, $submission);

        $achievementModel = Achievement::firstWhere('slug', 'quiz-champion');
        $this->assertNotNull($achievementModel);
        $this->assertTrue($user->achievements->contains($achievementModel));
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($user, $achievementModel) {
            return $event->user->is($user) && $event->achievement->is($achievementModel);
        });
        $this->assertEquals($achievementModel->points_reward, $user->fresh()->total_points);
    }

    /** @test */
    public function it_does_not_award_achievement_if_criteria_not_met()
    {
        Event::fake();

        $user = User::factory()->create();
        $submission = QuizSubmission::factory()->create(['student_id' => $user->id, 'score' => 50]); // Score too low

        $mockAchievement = $this->mock(AchievementContract::class);
        $mockAchievement->shouldReceive('slug')->andReturn('quiz-champion');
        $mockAchievement->shouldReceive('check')->with($user, ['submission' => $submission])->andReturn(false);

        // Instantiate service with the mocked achievement object
        $service = new AchievementService([$mockAchievement]);

        $service->processQuizCompletion($user, $submission);

        $achievementModel = Achievement::firstWhere('slug', 'quiz-champion');
        $this->assertNotNull($achievementModel);
        $this->assertFalse($user->achievements->contains($achievementModel));
        Event::assertNotDispatched(AchievementUnlocked::class);
        $this->assertEquals(0, $user->fresh()->total_points);
    }

    /** @test */
    public function it_does_not_award_achievement_if_already_awarded()
    {
        Event::fake();

        $user = User::factory()->create();
        $achievementModel = Achievement::firstWhere('slug', 'quiz-champion');
        $this->assertNotNull($achievementModel);
        $user->achievements()->attach($achievementModel->id, ['unlocked_at' => now()]); // Manually award

        $submission = QuizSubmission::factory()->create(['student_id' => $user->id, 'score' => 90]);

        $mockAchievement = $this->mock(AchievementContract::class);
        $mockAchievement->shouldReceive('slug')->andReturn('quiz-champion');
        $mockAchievement->shouldReceive('check')->with($user, ['submission' => $submission])->andReturn(true);

        // Instantiate service with the mocked achievement object
        $service = new AchievementService([$mockAchievement]);

        $service->processQuizCompletion($user, $submission);

        // Should not be awarded again, so event should not be dispatched
        Event::assertNotDispatched(AchievementUnlocked::class);
        $this->assertEquals(0, $user->fresh()->total_points);
    }

    /** @test */
    public function it_processes_user_activity_achievements()
    {
        Event::fake();

        $user = User::factory()->create();

        $mockAchievement = $this->mock(AchievementContract::class);
        $mockAchievement->shouldReceive('slug')->andReturn('streak-master');
        $mockAchievement->shouldReceive('check')->with($user)->andReturn(true);

        // Instantiate service with the mocked achievement object
        $service = new AchievementService([$mockAchievement]);

        $service->processUserActivity($user);

        $achievementModel = Achievement::firstWhere('slug', 'streak-master');
        $this->assertNotNull($achievementModel);
        $this->assertTrue($user->achievements->contains($achievementModel));
        Event::assertDispatched(AchievementUnlocked::class);
        $this->assertEquals($achievementModel->points_reward, $user->fresh()->total_points);
    }
}