<?php

namespace Tests\Unit\Achievements;

use App\Achievements\PerfectScore;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfectScoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_awards_if_score_is_100()
    {
        $user = User::factory()->create();
        $submission = QuizSubmission::factory()->create(['score' => 100]);
        $achievement = new PerfectScore();

        $this->assertTrue($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_award_if_score_is_not_100()
    {
        $user = User::factory()->create();
        $submission = QuizSubmission::factory()->create(['score' => 99]);
        $achievement = new PerfectScore();

        $this->assertFalse($achievement->check($user, ['submission' => $submission]));

        $submission = QuizSubmission::factory()->create(['score' => 0]);
        $this->assertFalse($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_award_if_submission_context_is_missing()
    {
        $user = User::factory()->create();
        $achievement = new PerfectScore();

        $this->assertFalse($achievement->check($user, []));
    }
}
