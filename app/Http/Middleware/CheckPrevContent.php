<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ExpiredQuizService;

class CheckPrevContent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $classroom = $request->route('classroom');

        if (!$classroom) {
            return to_route('user.classroom.index')
                ->with('error', 'Classroom not found.');
        }

        $content = $request->route('material') ?? $request->route('quiz');

        if (!$content) {
            return to_route('user.classroom.show', $classroom)
                ->with('error', 'Content not found.');
        }

        $currentContent = $content->contents()->where('classroom_id', $classroom->id)->first();

        if (!$currentContent) {
            return to_route('user.classroom.show', $classroom)
                ->with('error', 'Content not found in this classroom.');
        }

        // check if current content is the first one from classroom contents
        if ($currentContent->id === $classroom->contents()->first()->id) {
            return $next($request);
        }

        // Get the previous content in the classroom
        $previousContent = $classroom->contents()
            ->where('id', '<', $currentContent->id)
            ->orderBy('id', 'desc')
            ->first();

        $completedContents = auth()->user()->completedContents()
            ->where('classroom_id', $classroom->id)
            ->pluck('id');

        // check if previous content is completed
        $prevContentCompleted = $previousContent && $completedContents->contains($previousContent->id);

        // If previous content is not completed, check if it's an expired quiz
        if (!$prevContentCompleted && $previousContent) {
            $prevContentCompleted = ExpiredQuizService::handleExpiredQuizContent($previousContent, auth()->id());
        }

        if (!$prevContentCompleted) {
            return to_route('user.classroom.show', $classroom)
                ->with('error', 'You must complete the previous content before accessing this one.');
        }

        return $next($request);
    }
}
