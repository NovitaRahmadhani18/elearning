<?php

namespace Tests\Unit\Achievements;

use App\Achievements\LegendaryLearner;
use App\Models\Content;
use App\Models\ContentStudent;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegendaryLearnerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_unlocks_achievement_when_user_completes_20_materials()
    {
        // Arrange
        $user = User::factory()->create();
        $materials = Content::factory()->count(20)->for(Material::factory(), 'contentable')->create();

        foreach ($materials as $material) {
            ContentStudent::factory()->create([
                'user_id' => $user->id,
                'content_id' => $material->id,
                'status' => ContentStudent::STATUS_COMPLETED,
            ]);
        }

        $achievement = new LegendaryLearner();

        // Act & Assert
        $this->assertTrue($achievement->check($user));
    }

    /** @test */
    public function it_does_not_unlock_achievement_when_user_completes_less_than_20_materials()
    {
        // Arrange
        $user = User::factory()->create();
        $materials = Content::factory()->count(19)->for(Material::factory(), 'contentable')->create();

        foreach ($materials as $material) {
            ContentStudent::factory()->create([
                'user_id' => $user->id,
                'content_id' => $material->id,
                'status' => ContentStudent::STATUS_COMPLETED,
            ]);
        }

        $achievement = new LegendaryLearner();

        // Act & Assert
        $this->assertFalse($achievement->check($user));
    }
}
