<?php

namespace App\Http\Controllers\Teacher;

use App\CustomClasses\Column;
use App\CustomClasses\TableData;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ContentUser;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = Material::query()
            ->with(['contents.classroom'])
            ->latest()
            ->paginate(10);

        return view('pages.teacher.material.index', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classrooms = auth()->user()->classrooms()
            ->pluck('title', 'id');

        return view('pages.teacher.material.create', compact('classrooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'points' => 'nullable|integer|min:0|max:100',
        ]);

        // Assuming you have a Material model to handle the storage
        $material = \App\Models\Material::create([
            'title' => $request->title,
            'points' => $request->points, // Default to 10 if not provided
            'material-trixFields' => request('material-trixFields'),
            'attachment-material-trixFields' => request('attachment-material-trixFields'),
        ]);

        // Attach the material to the selected classroom
        \App\Models\Content::create([
            'classroom_id' => $request->classroom_id,
            'contentable_type' => \App\Models\Material::class,
            'contentable_id' => $material->id,
        ]);

        return to_route('teacher.material.index')
            ->with('success', 'Material created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        $content = $material->contents->first();
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
            ],
            perPage: request('perPage', 10),
            id: 'log-activity-table',
        );

        return view('pages.teacher.material.show', compact('material', 'tableData', 'classroom'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        return view('pages.teacher.material.edit', compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'points' => 'nullable|integer|min:0|max:100',
        ]);

        $material->update([
            'title' => $request->title,
            'points' => $request->points ?? $material->points ?? 10, // Keep existing points or default to 10
            'material-trixFields' => request('material-trixFields'),
            'attachment-material-trixFields' => request('attachment-material-trixFields'),
        ]);

        return to_route('teacher.material.index')
            ->with('success', 'Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();
        return to_route('teacher.material.index')->with('success', 'Material deleted successfully.');
    }
}
