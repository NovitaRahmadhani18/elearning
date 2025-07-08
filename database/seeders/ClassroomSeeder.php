<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classroom::factory(10)->create();

        // make 10 random quiz or material content for first classroom
        $classroom = Classroom::first();

        // make 5 materials without factory
        for ($i = 0; $i < 5; $i++) {
            \App\Models\Material::create([
                'title' => 'Material ' . ($i + 1),
            ]);

            $quiz = \App\Models\Quiz::create([
                'title' => 'Quiz ' . ($i + 1),
                'description' => 'This is the description of quiz ' . ($i + 1),
                'start_time' => now()->addDays($i - 2),
                'due_time' => now()->addDays($i + 3),
                'time_limit' => 60 * 30, // 30 minutes
            ]);

            // make 2 options and 1 question for each quiz
            $question = \App\Models\Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => 'What is the answer for question ' . ($i + 1) . '?',
            ]);

            for ($j = 0; $j  < 2; $j++) {
                \App\Models\QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => 'Option ' . ($j + 1),
                    'is_correct' => $j === 0, // first option is correct
                ]);
            }
        }

        // get all materials and quizzes created above
        $materials = \App\Models\Material::all();
        $quizzes = \App\Models\Quiz::all();

        // attach materials and quizzes to the first classroom
        foreach ($materials as $material) {
            $classroom->contents()->create([
                'contentable_type' => \App\Models\Material::class,
                'contentable_id' => $material->id,
            ]);
        }
        foreach ($quizzes as $quiz) {
            $classroom->contents()->create([
                'contentable_type' => \App\Models\Quiz::class,
                'contentable_id' => $quiz->id,
            ]);
        }



        // get user with email user@user.com then assign to first classroom
        $user = User::where('email', 'user@user.com')->first();
        $classroom->students()->attach($user->id);
    }
}
