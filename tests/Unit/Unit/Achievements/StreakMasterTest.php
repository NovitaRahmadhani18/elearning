<?php

namespace Tests\Unit\Achievements;

use App\Achievements\StreakMaster;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        for ($i = 0; $i < 5; $i++) {
            ActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'user.login',
                'description' => 'User logged in',
                'created_at' => Carbon::now()->subDays($i)->startOfDay(),
            ]);
        }

        $this->assertTrue($achievement->check($user));
    }

    /** @test */
    public function it_does_not_award_if_user_does_not_have_5_consecutive_login_days()
    {
        $user = User::factory()->create();
        $achievement = new StreakMaster();

        // Less than 5 days
        for ($i = 0; $i < 4; $i++) {
            ActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'user.login',
                'description' => 'User logged in',
                'created_at' => Carbon::now()->subDays($i)->startOfDay(),
            ]);
        }
        $this->assertFalse($achievement->check($user));

        // 5 days, but not consecutive (gap)
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => Carbon::now()->subDays(0)->startOfDay(),
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => Carbon::now()->subDays(1)->startOfDay(),
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => Carbon::now()->subDays(2)->startOfDay(),
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => Carbon::now()->subDays(4)->startOfDay(), // Gap here
        ]);
        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'user.login',
            'description' => 'User logged in',
            'created_at' => Carbon::now()->subDays(5)->startOfDay(),
        ]);
        $this->assertFalse($achievement->check($user));
    }
}
