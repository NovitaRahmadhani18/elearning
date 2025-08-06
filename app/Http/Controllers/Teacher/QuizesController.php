<?php

namespace App\Http\Controllers\Teacher;

use App\CustomClasses\Column;
use App\CustomClasses\TableData;
use App\Http\Controllers\Controller;
use App\Models\ContentUser;
use App\Models\Quiz;

class QuizesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizes = \App\Models\Quiz::query()
            ->when(request('search'), function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->with('classroom')
            ->latest()
            ->paginate(10);
        return view('pages.teacher.quizes.index', compact('quizes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {

        $content = $quiz->contents->first();
        $classroom = $content ? $content->classroom : null;

        if (!$classroom) {
            return to_route('teacher.material.index')
                ->with('error', 'Material not found or classroom not associated.');
        }


        $query = ContentUser::query()
            ->where('content_id', $content->id)
            ->with(['user'])
            ->latest();


        $tableData = TableData::make(
            $query,
            [
                Column::make('user', 'User')->setView('reusable-table.column.user-card'),
                Column::make('completed_at', 'Completed')->setView('reusable-table.column.date-yyyy'),
                Column::make('', 'point')->setView('reusable-table.column.quiz-point'),
            ],
            perPage: request('perPage', 10),
            id: 'log-activity-table',
        );


        return view('pages.teacher.quizes.show', compact('quiz', 'tableData', 'classroom'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return to_route('teacher.quizes.index')
            ->with('success', 'Quiz deleted successfully.');
    }
}
