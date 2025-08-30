<?php

namespace Tests\Unit\Achievements;

use App\Achievements\PerfectScore;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfectScoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_unlocks_achievement_when_user_has_5_perfect_scores()
    {
        // Arrange
        $user = User::factory()->create();
        QuizSubmission::factory()->count(5)->create([
            'student_id' => $user->id,
            'score' => 100,
        ]);

        $achievement = new PerfectScore();

        // Act & Assert
        $this->assertTrue($achievement->check($user));
    }

    /** @test */
    public function it_does_not_unlock_achievement_when_user_has_less_than_5_perfect_scores()
    {
        // Arrange
        $user = User::factory()->create();
        QuizSubmission::factory()->count(4)->create([
            'student_id' => $user->id,
            'score' => 100,
        ]);

        $achievement = new PerfectScore();

        // Act & Assert
        $this->assertFalse($achievement->check($user));
    }
}
