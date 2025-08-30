<?php

namespace Tests\Unit\Achievements;

use App\Achievements\QuizChallenger;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizChallengerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_unlocks_achievement_when_user_has_10_submissions_with_80_score()
    {
        // Arrange
        $user = User::factory()->create();
        QuizSubmission::factory()->count(10)->create([
            'student_id' => $user->id,
            'score' => 80,
        ]);

        $achievement = new QuizChallenger();

        // Act & Assert
        $this->assertTrue($achievement->check($user));
    }

    /** @test */
    public function it_does_not_unlock_achievement_when_user_has_less_than_10_submissions()
    {
        // Arrange
        $user = User::factory()->create();
        QuizSubmission::factory()->count(9)->create([
            'student_id' => $user->id,
            'score' => 80,
        ]);

        $achievement = new QuizChallenger();

        // Act & Assert
        $this->assertFalse($achievement->check($user));
    }
}
