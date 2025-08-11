<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classroom;
use App\Models\ClassroomStudent;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClassroomActivityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $teacher;
    private Classroom $classroom;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles first
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->user = User::factory()->create();
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');

        $this->classroom = Classroom::factory()->create([
            'teacher_id' => $this->teacher->id,
            'title' => 'Test Classroom',
            'category' => 'Programming',
        ]);
    }

    /** @test */
    public function test_student_joining_classroom_logs_activity()
    {
        // Act
        $classroomStudent = ClassroomStudent::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->user->id,
        ]);

        // Assert
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'classroom_activity',
            'subject_type' => ClassroomStudent::class,
            'subject_id' => $classroomStudent->id,
            'causer_type' => User::class,
            'causer_id' => $this->user->id,
        ]);

        $activity = Activity::where('log_name', 'classroom_activity')->latest()->first();
        $this->assertNotNull($activity);
        $this->assertStringContainsString('Joined classroom', $activity->description);

        $activity = Activity::where('log_name', 'classroom_activity')->latest()->first();
        $this->assertNotNull($activity);

        $properties = $activity->properties;
        $this->assertEquals('Test Classroom', $properties['classroom_name']);
        $this->assertEquals($this->classroom->id, $properties['classroom_id']);
        $this->assertEquals('Programming', $properties['classroom_category']);
        $this->assertEquals($this->teacher->name, $properties['teacher_name']);
        $this->assertEquals($this->teacher->id, $properties['teacher_id']);
        $this->assertEquals('direct_invitation', $properties['join_method']);
    }

    /** @test */
    public function test_join_via_invite_code_vs_direct_invitation()
    {
        // Test with invite code
        request()->merge(['invite_code' => 'TEST123']);

        $classroomStudent = ClassroomStudent::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->user->id,
        ]);

        $activity = Activity::where('log_name', 'classroom_activity')->latest()->first();
        $properties = $activity->properties;

        $this->assertEquals('invite_code', $properties['join_method']);
        $this->assertEquals('TEST123', $properties['invite_code_used']);
        $this->assertStringContainsString('using invite code', $activity->description);
    }

    /** @test */
    public function test_student_leaving_classroom_logs_activity()
    {
        // Arrange - First join the classroom
        $classroomStudent = ClassroomStudent::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->user->id,
        ]);

        // Verify join activity was logged
        $joinActivity = Activity::where('log_name', 'classroom_activity')->latest()->first();
        $this->assertNotNull($joinActivity);
        $this->assertStringContainsString('Joined classroom', $joinActivity->description);

        // Act - Leave the classroom
        $classroomStudent->delete();

        // Assert
        $activities = Activity::where('log_name', 'classroom_activity')->get();
        $this->assertCount(2, $activities); // Join + Leave

        $leaveActivity = $activities->filter(function ($activity) {
            return strpos($activity->description, 'Left classroom') !== false;
        })->first();
        $this->assertNotNull($leaveActivity);

        $properties = $leaveActivity->properties;
        $this->assertEquals('Test Classroom', $properties['classroom_name']);
        $this->assertEquals($this->classroom->id, $properties['classroom_id']);
        $this->assertArrayHasKey('progress_before_leaving', $properties);
        $this->assertArrayHasKey('classroom_stats', $properties);
    }

    /** @test */
    public function test_classroom_join_includes_stats()
    {
        // Act
        $classroomStudent = ClassroomStudent::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->user->id,
        ]);

        // Assert
        $activity = Activity::where('log_name', 'classroom_activity')->latest()->first();
        $properties = $activity->properties;

        $this->assertArrayHasKey('classroom_stats', $properties);
        $this->assertArrayHasKey('total_students', $properties['classroom_stats']);
        $this->assertArrayHasKey('total_contents', $properties['classroom_stats']);
        $this->assertArrayHasKey('total_quizzes', $properties['classroom_stats']);

        // Should show 1 student (the one who just joined)
        $this->assertEquals(1, $properties['classroom_stats']['total_students']);
    }

    /** @test */
    public function test_multiple_students_joining_logs_separate_activities()
    {
        // Arrange
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // Act
        ClassroomStudent::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->user->id,
        ]);

        ClassroomStudent::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => $user2->id,
        ]);

        ClassroomStudent::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => $user3->id,
        ]);

        // Assert
        $activities = Activity::where('log_name', 'classroom_activity')->get();
        $this->assertCount(3, $activities);

        // Check that each activity has the correct causer
        $causers = $activities->pluck('causer_id')->sort()->values();
        $expectedCausers = [$this->user->id, $user2->id, $user3->id];
        sort($expectedCausers);

        $this->assertEquals($expectedCausers, $causers->toArray());
    }

    /** @test */
    public function test_classroom_activity_with_user_role()
    {
        // Arrange - Assign role to user
        $this->user->assignRole('user');

        // Act
        ClassroomStudent::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->user->id,
        ]);

        // Assert
        $activity = Activity::where('log_name', 'classroom_activity')->latest()->first();
        $properties = $activity->properties;

        $this->assertEquals('user', $properties['user_role']);
    }
}
