<?php

namespace Tests\Unit;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; // Import TestCase

class ActivityLogServiceTest extends TestCase // Extend TestCase
{
    use RefreshDatabase; // Use trait directly

    /** @test */
    public function it_logs_user_activity_correctly()
    {
        $user = User::factory()->create();
        $service = new ActivityLogService();

        $service->log($user, 'test.activity', null, 'This is a test activity.');

        $this->assertDatabaseHas('activity_log', [
            'user_id' => $user->id,
            'activity_type' => 'test.activity',
            'description' => 'This is a test activity.',
            'subject_id' => null,
            'subject_type' => null,
        ]);

        $this->assertEquals(1, ActivityLog::count());
    }

    /** @test */
    public function it_logs_user_activity_with_a_subject_correctly()
    {
        $user = User::factory()->create();
        $subject = User::factory()->create(); // Using User as a subject for testing
        $service = new ActivityLogService();

        $service->log($user, 'test.activity.with.subject', $subject, 'Activity with a subject.');

        $this->assertDatabaseHas('activity_log', [
            'user_id' => $user->id,
            'activity_type' => 'test.activity.with.subject',
            'description' => 'Activity with a subject.',
            'subject_id' => $subject->id,
            'subject_type' => get_class($subject),
        ]);

        $this->assertEquals(1, ActivityLog::count());
    }
}
