<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserClassroomEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with 'user' role
        $users = User::role(RoleEnum::USER->value)->get();

        // Get all available classrooms
        $classrooms = Classroom::all();

        if ($users->isEmpty()) {
            $this->command->info('No users with "user" role found. Skipping enrollment seeder.');
            return;
        }

        if ($classrooms->isEmpty()) {
            $this->command->info('No classrooms found. Skipping enrollment seeder.');
            return;
        }

        $this->command->info("Enrolling {$users->count()} users into random classrooms...");

        foreach ($users as $user) {
            // Random number of classrooms to enroll (1-3 classrooms per user)
            $numberOfEnrollments = rand(1, min(3, $classrooms->count()));

            // Get random classrooms for this user
            $randomClassrooms = $classrooms->random($numberOfEnrollments);

            foreach ($randomClassrooms as $classroom) {
                // Check if user is not already enrolled
                if (!$user->classrooms()->where('classroom_id', $classroom->id)->exists()) {
                    $user->classrooms()->attach($classroom->id, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->command->info("✓ Enrolled {$user->name} into {$classroom->title}");
                }
            }
        }

        $this->command->info('User classroom enrollment seeder completed!');
    }
}
