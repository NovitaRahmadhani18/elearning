<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Web Development', 'Data Science', 'Mobile Development', 'UI/UX Design', 'Digital Marketing'];
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(4),
            'category' => $this->faker->randomElement($categories),
            'teacher_id' => \App\Models\User::role('teacher')->inRandomOrder()->first()->id,
        ];
    }
}
