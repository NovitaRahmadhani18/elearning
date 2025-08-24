<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizSubmissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QuizSubmission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'quiz_id' => Quiz::factory(), // Creates a new Quiz
            'student_id' => User::factory(), // Creates a new User
            'score' => $this->faker->numberBetween(0, 100),
            'started_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'completed_at' => $this->faker->dateTimeBetween('-6 days', 'now'),
            'duration_seconds' => $this->faker->numberBetween(60, 3600),
        ];
    }
}
