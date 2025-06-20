<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $classrooms = \App\Models\Classroom::all();

        return view('pages.admin.classroom.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.classroom.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'number_of_modules' => 'required|integer|min:0',
            'max_students' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $data = $request->only(['title', 'description', 'category', 'number_of_modules', 'max_students']);

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('classroom_thumbnails', 'public');
                $data['thumbnail_path'] = $path;
            }

            \App\Models\Classroom::create($data);
        });

        return to_route('admin.classroom.index')->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Classroom $classroom)
    {
        return view('pages.admin.classroom.edit', compact('classroom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\Classroom $classroom)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'number_of_modules' => 'required|integer|min:0',
            'max_students' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $classroom) {
            $data = $request->only(['title', 'description', 'category', 'number_of_modules', 'max_students']);

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('classroom_thumbnails', 'public');
                $data['thumbnail_path'] = $path;
            }

            $classroom->update($data);
        });

        return to_route('admin.classroom.index')->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        DB::transaction(function () use ($classroom) {
            // Delete the classroom's thumbnail if it exists
            if ($classroom->thumbnail_path) {
                Storage::disk('public')->delete($classroom->thumbnail_path);
            }

            // Delete the classroom
            $classroom->delete();
        });


        return to_route('admin.classroom.index')->with('success', 'Classroom deleted successfully.');
    }
}
