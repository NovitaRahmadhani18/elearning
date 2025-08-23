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

        User::create([
            'name' => 'Teacher',
            'email' => 'teacher@teacher.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
            'address' => '456 Teacher Avenue',
            'gender' => 'female',
            'id_number' => 'ID987654321',
        ]);

        User::create([
            'name' => 'Student',
            'email' => 'student@student.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'address' => '789 Student Boulevard',
            'gender' => 'male',
            'id_number' => 'ID1122334455',
        ]);
    }
}
