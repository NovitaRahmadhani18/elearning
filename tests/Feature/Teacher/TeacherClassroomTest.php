<?php

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeTeacher(): User
{
    $user = User::factory()->create();
    $user->assignRole('teacher');
    return $user;
}

it('teacher sees only own classrooms', function () {
    $teacher = makeTeacher();
    $other = makeTeacher();

    Classroom::factory()->create(['teacher_id' => $teacher->id, 'title' => 'Mine']);
    Classroom::factory()->create(['teacher_id' => $other->id, 'title' => 'Not Mine']);

    $this->actingAs($teacher)
        ->get('/teacher/classroom')
        ->assertOk()
        ->assertSee('Mine')
        ->assertDontSee('Not Mine');
});

it('teacher can create classroom for self', function () {
    $teacher = makeTeacher();

    $this->actingAs($teacher)
        ->post('/teacher/classroom', [
            'title' => 'New Class',
            'category' => 'Math',
            'description' => 'Desc',
        ])
        ->assertRedirect('/teacher/classroom');

    $this->assertDatabaseHas('classrooms', [
        'title' => 'New Class',
        'teacher_id' => $teacher->id,
    ]);
});

it('teacher cannot edit others classroom', function () {
    $teacher = makeTeacher();
    $other = makeTeacher();

    $classroom = Classroom::factory()->create(['teacher_id' => $other->id, 'title' => 'Other Class']);

    $this->actingAs($teacher)
        ->put('/teacher/classroom/' . $classroom->id, [
            'title' => 'Hack',
        ])
        ->assertStatus(403);
});

it('teacher can update own classroom', function () {
    $teacher = makeTeacher();
    $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id, 'title' => 'Old']);

    $this->actingAs($teacher)
        ->put('/teacher/classroom/' . $classroom->id, [
            'title' => 'New',
        ])
        ->assertRedirect('/teacher/classroom');

    $this->assertDatabaseHas('classrooms', [
        'id' => $classroom->id,
        'title' => 'New',
    ]);
});

it('teacher can delete own classroom', function () {
    $teacher = makeTeacher();
    $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id, 'title' => 'To Delete']);

    $this->actingAs($teacher)
        ->delete('/teacher/classroom/' . $classroom->id)
        ->assertRedirect('/teacher/classroom');

    $this->assertDatabaseMissing('classrooms', [
        'id' => $classroom->id,
    ]);
});
