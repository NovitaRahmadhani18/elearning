<?php

namespace Tests\Unit\Achievements;

use App\Achievements\StreakMaster;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StreakMasterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_awards_if_user_has_5_consecutive_login_days()
    {
        $user = User::factory()->create();
        $achievement = new StreakMaster();

        // Create 5 consecutive login activities
        $baseDate = Carbon::parse('2025-01-05')->startOfDay(); // A fixed date
        for ($i = 0; $i < 5; $i++) {
            ActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'user.login',
                'description' => 'User logged in',
                'created_at' => $baseDate->copy()->subDays($i),
            ]);
        }

        $this->assertTrue($achievement->check($user));
    }

    /** @test */
    public function it_does_not_award_if_user_does_not_have_5_consecutive_login_days()
    {
        $user = User::factory()->create();
        $achievement = new StreakMaster();

        $baseDate = Carbon::parse('2025-01-05')->startOfDay();
        // Less than 5 days
        for ($i = 0; $i < 4; $i++) {
            ActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'user.login',
                'description' => 'User logged in',
                'created_at' => $baseDate->copy()->subDays($i),
            ]);
        }
        $this->assertFalse($achievement->check($user));

        // 5 days, but not consecutive (gap)
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => $baseDate->copy()->subDays(0),
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => $baseDate->copy()->subDays(1),
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => $baseDate->copy()->subDays(2),
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => $baseDate->copy()->subDays(4), // Gap here
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => $baseDate->copy()->subDays(5),
        ]);
        $this->assertFalse($achievement->check($user));
    }
}
