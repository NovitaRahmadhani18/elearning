<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppModelsContent>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'classroom_id' => \App\Models\Classroom::factory()->create()->id, // Ensure a valid classroom_id
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'points' => $this->faker->numberBetween(10, 100),
            'order' => $this->faker->numberBetween(1, 10),
            'contentable_id' => \App\Models\Material::factory(), // Default to Material
            'contentable_type' => \App\Models\Material::class,
        ];
    }
}
