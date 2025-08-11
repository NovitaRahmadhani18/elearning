<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\ActivityService;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityServiceTest extends TestCase
{
    use RefreshDatabase;

    private ActivityService $activityService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activityService = new ActivityService();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_get_activities_for_user_returns_correct_activities()
    {
        // Arrange
        $otherUser = User::factory()->create();

        // Create activities for our user
        activity('quiz_completion')->causedBy($this->user)->log('User completed quiz');
        activity('material_completion')->causedBy($this->user)->log('User completed material');

        // Create activity for other user (should not be included)
        activity('quiz_completion')->causedBy($otherUser)->log('Other user completed quiz');

        // Act
        $activities = $this->activityService->getActivitiesForUser($this->user, 10);

        // Assert
        $this->assertCount(2, $activities);
        $this->assertTrue($activities->every(function ($activity) {
            return $activity->causer_id === $this->user->id;
        }));
    }

    /** @test */
    public function test_get_activities_by_log_name_filters_correctly()
    {
        // Arrange
        activity('quiz_completion')->causedBy($this->user)->log('Quiz completed');
        activity('material_completion')->causedBy($this->user)->log('Material completed');
        activity('classroom_activity')->causedBy($this->user)->log('Joined classroom');

        // Act
        $quizActivities = $this->activityService->getActivitiesByLogName('quiz_completion', 10);

        // Assert
        $this->assertCount(1, $quizActivities);
        $this->assertEquals('quiz_completion', $quizActivities->first()->log_name);
    }

    /** @test */
    public function test_get_user_activity_stats_calculates_correctly()
    {
        // Arrange
        activity('quiz_completion')
            ->causedBy($this->user)
            ->withProperties(['is_perfect_score' => true, 'is_high_score' => true])
            ->log('Perfect quiz');

        activity('quiz_completion')
            ->causedBy($this->user)
            ->withProperties(['is_perfect_score' => false, 'is_high_score' => true])
            ->log('High score quiz');

        activity('material_completion')->causedBy($this->user)->log('Material completed');
        activity('classroom_activity')->causedBy($this->user)->log('Student joined classroom');
        activity('achievement_earned')->causedBy($this->user)->log('Achievement earned');

        // Act
        $stats = $this->activityService->getUserActivityStats($this->user);

        // Assert
        $this->assertEquals(5, $stats['total_activities']);
        $this->assertEquals(2, $stats['quiz_completions']);
        $this->assertEquals(1, $stats['material_completions']);
        $this->assertEquals(1, $stats['classroom_joins']);
        $this->assertEquals(1, $stats['achievements_earned']);
        $this->assertEquals(1, $stats['perfect_scores']);
        $this->assertEquals(2, $stats['high_scores']);
    }

    /** @test */
    public function test_get_recent_activities_for_user_returns_limited_results()
    {
        // Arrange
        for ($i = 0; $i < 15; $i++) {
            activity('quiz_completion')->causedBy($this->user)->log("Quiz $i completed");
        }

        // Act
        $recentActivities = $this->activityService->getRecentActivitiesForUser($this->user, 5);

        // Assert
        $this->assertCount(5, $recentActivities);
        // The most recent activity should be the last one created
        $this->assertStringContainsString('Quiz', $recentActivities->first()->description);
        $this->assertStringContainsString('completed', $recentActivities->first()->description);
    }

    /** @test */
    public function test_get_formatted_description_for_quiz_activity()
    {
        // Arrange
        $activity = Activity::create([
            'log_name' => 'quiz_completion',
            'description' => 'Quiz completed',
            'subject_type' => 'App\Models\QuizSubmission',
            'subject_id' => 1,
            'causer_type' => 'App\Models\User',
            'causer_id' => $this->user->id,
            'properties' => [
                'quiz_title' => 'Laravel Basics',
                'score_percentage' => 95,
                'is_perfect_score' => false,
                'is_high_score' => true,
            ]
        ]);

        // Act
        $formattedDescription = $this->activityService->getFormattedDescription($activity);

        // Assert
        $this->assertEquals("⭐ High score (95%) in 'Laravel Basics'", $formattedDescription);
    }

    /** @test */
    public function test_get_formatted_description_for_perfect_score()
    {
        // Arrange
        $activity = Activity::create([
            'log_name' => 'quiz_completion',
            'description' => 'Quiz completed',
            'subject_type' => 'App\Models\QuizSubmission',
            'subject_id' => 1,
            'causer_type' => 'App\Models\User',
            'causer_id' => $this->user->id,
            'properties' => [
                'quiz_title' => 'PHP Fundamentals',
                'score_percentage' => 100,
                'is_perfect_score' => true,
                'is_high_score' => true,
            ]
        ]);

        // Act
        $formattedDescription = $this->activityService->getFormattedDescription($activity);

        // Assert
        $this->assertEquals("🎯 Perfect score (100%) in 'PHP Fundamentals'", $formattedDescription);
    }

    /** @test */
    public function test_get_formatted_description_for_material_completion()
    {
        // Arrange
        $activity = Activity::create([
            'log_name' => 'material_completion',
            'description' => 'Material completed',
            'subject_type' => 'App\Models\Content',
            'subject_id' => 1,
            'causer_type' => 'App\Models\User',
            'causer_id' => $this->user->id,
            'properties' => [
                'content_title' => 'Introduction to Laravel',
                'points_earned' => 50,
            ]
        ]);

        // Act
        $formattedDescription = $this->activityService->getFormattedDescription($activity);

        // Assert
        $this->assertEquals("📚 Completed 'Introduction to Laravel' and earned 50 points", $formattedDescription);
    }

    /** @test */
    public function test_get_formatted_description_for_classroom_activity()
    {
        // Arrange
        $activity = Activity::create([
            'log_name' => 'classroom_activity',
            'description' => 'Student joined classroom',
            'subject_type' => 'App\Models\ClassroomStudent',
            'subject_id' => 1,
            'causer_type' => 'App\Models\User',
            'causer_id' => $this->user->id,
            'properties' => [
                'classroom_name' => 'Web Development 101',
                'join_method' => 'invite_code',
            ]
        ]);

        // Act
        $formattedDescription = $this->activityService->getFormattedDescription($activity);

        // Assert
        $this->assertEquals("🚪 Joined 'Web Development 101' using invite code", $formattedDescription);
    }

    /** @test */
    public function test_get_formatted_description_for_achievement_activity()
    {
        // Arrange
        $activity = Activity::create([
            'log_name' => 'achievement_earned',
            'description' => 'Achievement earned',
            'subject_type' => 'App\Models\QuizSubmission',
            'subject_id' => 1,
            'causer_type' => 'App\Models\User',
            'causer_id' => $this->user->id,
            'properties' => [
                'achievement_name' => 'Quiz Champion',
                'trigger_event' => 'quiz_completion',
            ]
        ]);

        // Act
        $formattedDescription = $this->activityService->getFormattedDescription($activity);

        // Assert
        $this->assertEquals("🏆 Earned 'Quiz Champion' achievement from quiz_completion", $formattedDescription);
    }

    /** @test */
    public function test_get_user_learning_streak_calculates_correctly()
    {
        // Arrange - Create activities for consecutive days
        $today = now();
        for ($i = 0; $i < 5; $i++) {
            Activity::create([
                'log_name' => 'quiz_completion',
                'description' => 'Quiz completed',
                'subject_type' => 'App\Models\QuizSubmission',
                'subject_id' => 1,
                'causer_type' => 'App\Models\User',
                'causer_id' => $this->user->id,
                'created_at' => $today->copy()->subDays($i),
                'updated_at' => $today->copy()->subDays($i),
            ]);
        }

        // Act
        $streak = $this->activityService->getUserLearningStreak($this->user);

        // Assert
        $this->assertArrayHasKey('current_streak', $streak);
        $this->assertArrayHasKey('longest_streak', $streak);
        $this->assertArrayHasKey('active_days_this_month', $streak);
        $this->assertArrayHasKey('last_activity_date', $streak);

        $this->assertEquals(5, $streak['active_days_this_month']);
        $this->assertNotNull($streak['last_activity_date']);
    }
}
