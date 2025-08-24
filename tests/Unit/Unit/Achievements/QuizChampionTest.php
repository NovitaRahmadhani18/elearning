<?php

namespace Tests\Unit\Achievements;

use App\Achievements\QuizChampion;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizChampionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_awards_if_score_is_85_or_more()
    {
        $user = User::factory()->create();
        $submission = QuizSubmission::factory()->create(['score' => 85]);
        $achievement = new QuizChampion();

        $this->assertTrue($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_award_if_score_is_below_85()
    {
        $user = User::factory()->create();
        $submission = QuizSubmission::factory()->create(['score' => 84]);
        $achievement = new QuizChampion();

        $this->assertFalse($achievement->check($user, ['submission' => $submission]));
    }

    /** @test */
    public function it_does_not_award_if_submission_context_is_missing()
    {
        $user = User::factory()->create();
        $achievement = new QuizChampion();

        $this->assertFalse($achievement->check($user, []));
    }
}
