<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ClassroomStudents extends Component
{
    public Classroom $classroom;
    public $searchQuery = '';
    public $students = [];
    public $selectedStudents = [];

    public function mount(Classroom $classroom)
    {
        $this->classroom = $classroom;
        $this->loadStudents();
        $this->loadSelectedStudents();
        
        Log::info('ClassroomStudents mounted', [
            'classroom_id' => $this->classroom->id,
            'total_students' => count($this->students),
            'selected_students' => count($this->selectedStudents)
        ]);
    }

    public function loadStudents()
    {
        $this->students = User::role('user')
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                ];
            })
            ->toArray();
    }

    public function loadSelectedStudents()
    {
        $this->selectedStudents = $this->classroom->students()->pluck('users.id')->toArray();
    }

    public function getFilteredStudentsProperty()
    {
        if (empty($this->searchQuery)) {
            return $this->students;
        }

        return array_filter($this->students, function ($student) {
            return stripos($student['name'], $this->searchQuery) !== false ||
                   stripos($student['email'], $this->searchQuery) !== false;
        });
    }

    public function toggleStudent($studentId)
    {
        try {
            $studentId = (int) $studentId;
            
            Log::info('Toggling student', [
                'student_id' => $studentId,
                'classroom_id' => $this->classroom->id,
                'currently_selected' => in_array($studentId, $this->selectedStudents)
            ]);
            
            DB::transaction(function () use ($studentId) {
                if (in_array($studentId, $this->selectedStudents)) {
                    // Remove student
                    $this->classroom->students()->detach($studentId);
                    $this->selectedStudents = array_filter($this->selectedStudents, function ($id) use ($studentId) {
                        return $id !== $studentId;
                    });
                    
                    Log::info('Student removed from classroom', ['student_id' => $studentId]);
                    
                    $this->dispatch('student-removed', [
                        'message' => 'Student removed from classroom successfully!'
                    ]);
                } else {
                    // Add student
                    $this->classroom->students()->attach($studentId);
                    $this->selectedStudents[] = $studentId;
                    
                    Log::info('Student added to classroom', ['student_id' => $studentId]);
                    
                    $this->dispatch('student-added', [
                        'message' => 'Student added to classroom successfully!'
                    ]);
                }
            });

            // Refresh selected students to ensure consistency
            $this->loadSelectedStudents();
            
        } catch (Exception $e) {
            Log::error('Error toggling student', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('student-error', [
                'message' => 'Error updating student: ' . $e->getMessage()
            ]);
        }
    }

    public function isStudentSelected($studentId)
    {
        return in_array((int) $studentId, $this->selectedStudents);
    }

    public function getSelectedStudentsCountProperty()
    {
        return count($this->selectedStudents);
    }

    public function getTotalStudentsProperty()
    {
        return count($this->students);
    }

    public function render()
    {
        return view('livewire.classroom-students', [
            'filteredStudents' => $this->filteredStudents,
            'selectedStudentsCount' => $this->selectedStudentsCount,
            'totalStudents' => $this->totalStudents,
        ]);
    }
}
