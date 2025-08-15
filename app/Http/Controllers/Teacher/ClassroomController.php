<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    public function index()
    {
        $query = Classroom::query()
            ->where('teacher_id', auth()->id())
            ->orderByDesc('created_at')
            ->with(['teacher'])
            ->withCount(['students', 'contents', 'quizzes', 'materials']);

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $classrooms = $query->get();

        if (request()->header('X-Alpine-Request') || request()->ajax()) {
            return view('pages.teacher.classroom.partials.classroom-grid', compact('classrooms'))->render();
        }

        return view('pages.teacher.classroom.index', compact('classrooms'));
    }

    public function create()
    {
        return view('pages.teacher.classroom.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $data = $request->only(['title', 'description', 'category']);
            $data['teacher_id'] = auth()->id();

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('classroom_thumbnails', 'public');
                $data['thumbnail_path'] = $path;
            }

            Classroom::create($data);
        });

        return to_route('teacher.classroom.index')->with('success', 'Class created successfully.');
    }

    public function show(Classroom $classroom)
    {
        abort_unless($classroom->teacher_id === auth()->id(), 403);

        $contents = $classroom->contents()->with(['contentable'])->get();

        return view('pages.teacher.classroom.show', compact('classroom', 'contents'));
    }

    public function edit(Classroom $classroom)
    {
        abort_unless($classroom->teacher_id === auth()->id(), 403);
        return view('pages.teacher.classroom.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        abort_unless($classroom->teacher_id === auth()->id(), 403);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $classroom) {
            $data = $request->only(['title', 'description', 'category']);

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('classroom_thumbnails', 'public');
                $data['thumbnail_path'] = $path;
            }

            $classroom->update($data);
        });

        return to_route('teacher.classroom.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(Classroom $classroom)
    {
        abort_unless($classroom->teacher_id === auth()->id(), 403);

        try {
            DB::transaction(function () use ($classroom) {
                if ($classroom->thumbnail_path) {
                    Storage::disk('public')->delete($classroom->thumbnail_path);
                }
                $classroom->students()->detach();
                $classroom->contents()->delete();
                $classroom->delete();
            });

            if (request()->header('X-Alpine-Request')) {
                return response()->json(['success' => true, 'message' => 'Classroom deleted successfully.']);
            }

            return redirect()->route('teacher.classroom.index')->with('success', 'Classroom deleted successfully.');
        } catch (\Exception $e) {
            if (request()->header('X-Alpine-Request')) {
                return response()->json(['success' => false, 'message' => 'Failed to delete classroom: ' . $e->getMessage()], 500);
            }
            return redirect()->route('teacher.classroom.index')->with('error', 'Failed to delete classroom: ' . $e->getMessage());
        }
    }
}
