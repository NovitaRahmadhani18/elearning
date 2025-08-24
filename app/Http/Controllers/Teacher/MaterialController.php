<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentResource;
use App\Models\Classroom;
use App\Models\Content;
use App\Services\ContentService;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    protected $contentService;
    protected $leaderboardService;

    public function __construct(ContentService $contentService, LeaderboardService $leaderboardService)
    {
        $this->contentService = $contentService;
        $this->leaderboardService = $leaderboardService;
    }

    public function index()
    {
        return inertia('teacher/material/index', [
            'materials' => $this->contentService->getMaterials(),
        ]);
    }

    public function show(Content $content)
    {

        $leaderboardContent = $this->leaderboardService->getLeaderboardForContent($content);
        $content->load(['classroom', 'contentable']);
        $content->leaderboard = $leaderboardContent;

        return inertia('teacher/material/show', [
            'material' => ContentResource::make($content),
        ]);
    }

    public function create()
    {
        return inertia('teacher/material/create', [
            'classrooms' => $this->contentService->getClassrooms(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],

            'classroom_id' => [
                'required',
                'integer',
                Rule::exists('classrooms', 'id')->where('teacher_id', auth()->id())
            ],

            'points' => ['required', 'integer', 'min:0'],
            'body' => ['nullable', 'string'],

            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,jpg,png,zip', 'max:10240'], // 10MB max
        ], [
            'classroom_id.exists' => 'The selected classroom does not belong to you.',
            'attachment.mimes' => 'The attachment must be a file of type: pdf, doc, docx, ppt, pptx, jpg, png, zip.',
            'attachment.max' => 'The attachment may not be greater than 10MB.',
        ]);

        $classroom = Classroom::findOrFail($validated['classroom_id']);

        try {
            $this->contentService->createMaterial($validated, $classroom);
            return to_route('teacher.materials.index')->with('success', 'Material created successfully.');
        } catch (\Throwable $th) {
            return to_route('teacher.materials.create')->withErrors(['error' => 'Failed to create material: ' . $th->getMessage()])->withInput();
        }
    }

    public function edit(Content $content)
    {
        return inertia('teacher/material/edit', [
            'material' => ContentResource::make($content->load('contentable')),
            'classrooms' => $this->contentService->getClassrooms(),
        ]);
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:0'],
            'body' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,jpg,png,zip', 'max:10240'],
            'remove_attachment' => ['nullable', 'boolean'], // Validasi flag baru kita
        ]);

        try {
            $this->contentService->updateMaterial($content, $validated);
            return to_route('teacher.materials.index')->with('success', 'Material updated successfully.');
        } catch (\Throwable $th) {
            return to_route('teacher.materials.edit', $content->id)
                ->withErrors(['error' => 'Failed to update material: ' . $th->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $this->contentService->deleteContent($content);

        return redirect()->back()->with('success', 'Material deleted successfully.');
    }
}
