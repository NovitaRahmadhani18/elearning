<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppModelsClassroomCategory>
 */
class ClassroomCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word() . ' ' . $this->faker->unique()->randomNumber(4);
        return [
            'name' => $name,
            'value' => $name,
        ];
    }
}
