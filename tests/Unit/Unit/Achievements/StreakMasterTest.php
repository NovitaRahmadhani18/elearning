<?php

namespace Tests\Unit\Achievements;

use App\Achievements\StreakMaster;
use App\Models\User;
use App\Models\Content;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StreakMasterTest extends TestCase
{
    use RefreshDatabase;

    private StreakMaster $achievement;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->achievement = new StreakMaster();

        $this->user = User::factory()->create();

        // Mock Log facade untuk menghindari log output saat testing
        Log::shouldReceive('info')->andReturn(null);
    }

    /** @test */
    public function it_returns_true_for_perfect_5_day_consecutive_streak()
    {
        // Buat 5 content untuk 5 hari berturut-turut
        for ($i = 7; $i >= 0; $i--) {

            $content = Content::factory()->create();
            Log::info('Creating content for date: ' . Carbon::now()->subDays($i)->toDateString());
            $date = Carbon::now()->subDays($i);

            $this->user->contents()->attach($content->id, [
                'status' => 'completed',
                'created_at' => $date,
                'updated_at' => $date,
                'completed_at' => $date,
            ]);
        }

        $result = $this->achievement->check($this->user);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_when_streak_is_broken()
    {
        // Buat 5 content tapi dengan gap di tengah
        $dates = [
            Carbon::now()->subDays(4),
            Carbon::now()->subDays(3),
            Carbon::now()->subDays(1), // Missing day 2
            Carbon::now(),
        ];

        foreach ($dates as $date) {
            $content = Content::factory()->create();
            $this->user->contents()->attach($content->id, [
                'status' => 'completed',
                'created_at' => $date,
                'updated_at' => $date,
                'completed_at' => $date,
            ]);
        }

        $result = $this->achievement->check($this->user);

        $this->assertFalse($result);
    }
}
