<?php

namespace Tests\Unit\Achievements;

use App\Achievements\KnowledgeExplorer;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeExplorerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_unlocks_achievement_when_user_has_almost_all_achievements()
    {
        // Arrange
        $user = User::factory()->create();
        $achievements = Achievement::factory()->count(5)->create();
        $user->achievements()->attach($achievements->pluck('id')->take(4), ['unlocked_at' => now()]);

        $achievement = new KnowledgeExplorer();

        // Act & Assert
        $this->assertTrue($achievement->check($user));
    }

    /** @test */
    public function it_does_not_unlock_achievement_when_user_has_few_achievements()
    {
        // Arrange
        $user = User::factory()->create();
        Achievement::factory()->count(5)->create();
        $user->achievements()->attach(Achievement::factory()->create()->id, ['unlocked_at' => now()]);

        $achievement = new KnowledgeExplorer();

        // Act & Assert
        $this->assertFalse($achievement->check($user));
    }
}
