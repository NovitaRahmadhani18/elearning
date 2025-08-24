<?php

namespace Database\Factories;

use App\Models\ClassroomCategory;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppModelsClassroom>
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
        return [
            'name' => $this->faker->word() . ' Class',
            'description' => $this->faker->sentence(),
            'teacher_id' => User::factory()->create(['role' => 'teacher'])->id, // Ensure a teacher user
            'code' => Str::random(8),
            'category_id' => ClassroomCategory::factory()->create()->id, // Create and get ID
            'status_id' => Status::factory()->create()->id, // Create and get ID
            'thumbnail' => null,
            'invite_code' => Str::random(8),
        ];
    }
}
