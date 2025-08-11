<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Classroom;
use App\Models\Content;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuizCompletionActivityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Quiz $quiz;
    private Classroom $classroom;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles first
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->user = User::factory()->create();

        // Create teacher user first
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Create classroom with specific teacher_id
        $this->classroom = Classroom::factory()->create([
            'teacher_id' => $teacher->id
        ]);

        // Create quiz manually
        $this->quiz = Quiz::create([
            'title' => 'Test Quiz',
            'description' => 'Test quiz description',
            'start_time' => now(),
            'due_time' => now()->addDays(7),
            'time_limit' => 60,
            'points' => 100,
        ]);

        // Create content relationship
        Content::create([
            'contentable_type' => Quiz::class,
            'contentable_id' => $this->quiz->id,
            'classroom_id' => $this->classroom->id,
            'order' => 1,
        ]);
    }
    /** @test */
    public function test_quiz_completion_logs_activity_with_correct_properties()
    {
        // Arrange
        $quizSubmission = QuizSubmission::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->user->id,
            'started_at' => now()->subMinutes(10),
            'score' => 85,
            'total_questions' => 10,
            'correct_answers' => 8,
            'time_spent' => 420, // 7 minutes
            'is_completed' => false,
        ]);

        // Act - Complete the quiz (this should trigger the observer)
        $quizSubmission->update([
            'completed_at' => now(),
            'is_completed' => true,
        ]);

        // Assert
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'quiz_completion',
            'description' => 'High score achieved in quiz completion',
            'subject_type' => QuizSubmission::class,
            'subject_id' => $quizSubmission->id,
            'causer_type' => User::class,
            'causer_id' => $this->user->id,
        ]);

        $activity = Activity::where('log_name', 'quiz_completion')
            ->where('causer_id', $this->user->id)
            ->where('description', 'High score achieved in quiz completion')
            ->latest()
            ->first();
        $this->assertNotNull($activity);

        $properties = $activity->properties;
        $this->assertEquals($this->quiz->title, $properties['quiz_title']);
        $this->assertEquals(85, $properties['score']);
        $this->assertEquals(80.0, $properties['score_percentage']); // 8/10 * 100 = 80%
        $this->assertEquals(10, $properties['total_questions']);
        $this->assertEquals(8, $properties['correct_answers']);
        $this->assertEquals(420, $properties['time_spent']);
        $this->assertEquals('07:00', $properties['time_spent_formatted']);
        $this->assertFalse($properties['is_perfect_score']);
        $this->assertTrue($properties['is_high_score']);
    }

    /** @test */
    public function test_perfect_score_quiz_includes_achievement_reference()
    {
        // Arrange
        $quizSubmission = QuizSubmission::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->user->id,
            'started_at' => now()->subMinutes(5),
            'score' => 100,
            'total_questions' => 10,
            'correct_answers' => 10,
            'time_spent' => 300,
            'is_completed' => false,
        ]);

        // Act
        $quizSubmission->update([
            'completed_at' => now(),
            'is_completed' => true,
        ]);

        // Assert
        $activity = Activity::where('log_name', 'quiz_completion')->latest()->first();
        $this->assertNotNull($activity);

        $properties = $activity->properties;
        $this->assertTrue($properties['is_perfect_score']);
        $this->assertTrue($properties['is_high_score']);
        $this->assertEquals('Perfect score achieved in quiz completion', $activity->description);
    }

    /** @test */
    public function test_failed_quiz_logs_appropriate_details()
    {
        // Arrange
        $quizSubmission = QuizSubmission::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->user->id,
            'started_at' => now()->subMinutes(15),
            'score' => 45,
            'total_questions' => 10,
            'correct_answers' => 4,
            'time_spent' => 600,
            'is_completed' => false,
        ]);

        // Act
        $quizSubmission->update([
            'completed_at' => now(),
            'is_completed' => true,
        ]);

        // Assert
        $activity = Activity::where('log_name', 'quiz_completion')->latest()->first();
        $this->assertNotNull($activity);

        $properties = $activity->properties;
        $this->assertEquals(45, $properties['score']);
        $this->assertEquals(45.0, $properties['score_percentage']);
        $this->assertFalse($properties['is_perfect_score']);
        $this->assertFalse($properties['is_high_score']);
        $this->assertEquals('Quiz completed', $activity->description);
    }

    /** @test */
    public function test_quiz_update_without_completion_does_not_log_activity()
    {
        // Arrange
        $quizSubmission = QuizSubmission::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->user->id,
            'started_at' => now()->subMinutes(5),
            'score' => 0,
            'total_questions' => 10,
            'correct_answers' => 0,
            'time_spent' => 120,
            'is_completed' => false,
        ]);

        $initialActivityCount = Activity::count();

        // Act - Update without completing
        $quizSubmission->update([
            'score' => 25,
            'correct_answers' => 2,
            'time_spent' => 180,
        ]);

        // Assert - No new activity should be logged
        $this->assertEquals($initialActivityCount, Activity::count());
    }

    /** @test */
    public function test_quiz_creation_logs_submission_started()
    {
        // Arrange & Act
        $quizSubmission = QuizSubmission::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->user->id,
            'started_at' => now(),
            'score' => 0,
            'total_questions' => 10,
            'correct_answers' => 0,
            'time_spent' => 0,
            'is_completed' => false,
        ]);

        // Assert
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'quiz_completion',
            'description' => 'Quiz submission started',
            'subject_type' => QuizSubmission::class,
            'subject_id' => $quizSubmission->id,
            'causer_type' => User::class,
            'causer_id' => $this->user->id,
        ]);
    }
}
