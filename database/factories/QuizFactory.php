<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class QuizFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quiz::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startTime = $this->faker->dateTimeBetween('-1 month', '+1 week');
        $endTime = Carbon::parse(clone $startTime)->addMinutes($this->faker->numberBetween(30, 120));

        return [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $this->faker->numberBetween(30, 120),
        ];
    }
}
