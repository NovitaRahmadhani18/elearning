<?php

namespace Tests\Unit\Achievements;

use App\Achievements\FastLearner;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FastLearnerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_awards_if_duration_is_less_than_10_minutes()
    {
        $user = User::factory()->create();
        $submission = QuizSubmission::factory()->create(['duration_seconds' => 599]); // Less than 10 minutes (600 seconds)
        $achievement = new FastLearner();

        $this->assertTrue($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_award_if_duration_is_10_minutes_or_more()
    {
        $user = User::factory()->create();
        $submission = QuizSubmission::factory()->create(['duration_seconds' => 600]); // Exactly 10 minutes
        $achievement = new FastLearner();

        $this->assertFalse($achievement->check($user, ['submission' => $submission]));

        $submission = QuizSubmission::factory()->create(['duration_seconds' => 601]); // More than 10 minutes
        $this->assertFalse($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_award_if_submission_context_is_missing()
    {
        $user = User::factory()->create();
        $achievement = new FastLearner();

        $this->assertFalse($achievement->check($user, []));
    }
}
