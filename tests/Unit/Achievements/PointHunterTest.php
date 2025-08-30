<?php

namespace Tests\Unit\Achievements;

use App\Achievements\PointHunter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointHunterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_unlocks_achievement_when_user_has_1000_points()
    {
        // Arrange
        $user = User::factory()->create(['total_points' => 1000]);
        $achievement = new PointHunter();

        // Act & Assert
        $this->assertTrue($achievement->check($user));
    }

    /** @test */
    public function it_does_not_unlock_achievement_when_user_has_less_than_1000_points()
    {
        // Arrange
        $user = User::factory()->create(['total_points' => 999]);
        $achievement = new PointHunter();

        // Act & Assert
        $this->assertFalse($achievement->check($user));
    }
}
