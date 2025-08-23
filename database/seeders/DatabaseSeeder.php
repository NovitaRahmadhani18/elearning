<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'address' => '123 Admin Street',
            'gender' => 'male',
            'id_number' => 'ID123456789',
        ]);

        $teacher = User::create([
            'name' => 'Teacher',
            'email' => 'teacher@teacher.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
            'address' => '456 Teacher Avenue',
            'gender' => 'female',
            'id_number' => 'ID987654321',
        ]);

        $student = User::create([
            'name' => 'Student',
            'email' => 'student@student.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'address' => '789 Student Boulevard',
            'gender' => 'male',
            'id_number' => 'ID1122334455',
        ]);

        // create 1 classroom with 1 teacher and 1 student
        $classroom = \App\Models\Classroom::create([
            'name' => 'Matematika',
            'description' => 'This is a sample classroom.',
            'teacher_id' => $teacher->id,
            'code' => 'CLASS123',
            'category_id' => 1,
            'status_id' => 1,
            'thumbnail' => null,
            'invite_code' => 'INVITE123',
        ]);

        \App\Models\ClassroomStudent::create([
            'classroom_id' => $classroom->id,
            'student_id' => $student->id,
        ]);

        User::factory(30)->create()->each(function ($user) use ($classroom) {
            $user->classrooms()->attach($classroom->id);
        });

        for ($i = 1; $i < 5; $i++) {
            \App\Models\Classroom::create([
                'name' => 'Matematika',
                'description' => 'Description for Classroom ' . ($i + 1),
                'teacher_id' => $teacher->id,
                'code' => 'CLASS' . ($i + 1) . '123',
                'category_id' => $i + 1,
                'status_id' => 1,
                'thumbnail' => null,
                'invite_code' => 'INVITE' . ($i + 1) . '123',
            ]);
        }
    }
}
