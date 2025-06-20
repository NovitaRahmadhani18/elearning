<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
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
            ->with('classroom')
            ->latest()
            ->paginate(10);

        return view('pages.teacher.material.index', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $classrooms = Classroom::query()
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
        ]);
        // Assuming you have a Material model to handle the storage
        \App\Models\Material::create([
            'title' => $request->title,
            'material-trixFields' => request('material-trixFields'),
            'attachment-material-trixFields' => request('attachment-material-trixFields'),
            'classroom_id' => $request->classroom_id,
        ]);

        return to_route('teacher.material.index')
            ->with('success', 'Material created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {

        return view('pages.teacher.material.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        $classrooms = \App\Models\Classroom::pluck('title', 'id');
        return view('pages.teacher.material.edit', compact('material', 'classrooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        $material->update([
            'title' => $request->title,
            'material-trixFields' => request('material-trixFields'),
            'attachment-material-trixFields' => request('attachment-material-trixFields'),
            'classroom_id' => $request->classroom_id,
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
