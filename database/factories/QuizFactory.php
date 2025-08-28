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

        $startTime = Carbon::now()->subDays($this->faker->numberBetween(1, 30))->setTime(
            $this->faker->numberBetween(8, 18),
            $this->faker->numberBetween(0, 59),
            0
        );

        $endTime = (clone $startTime)->addMinutes($this->faker->numberBetween(30, 120));

        return [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $this->faker->numberBetween(30, 120),
        ];
    }
}
