<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckClassroomEnrollment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $classroom = $request->route('classroom');

        if ($classroom && $classroom->students->doesntContain(auth()->user())) {
            return to_route('user.classroom.index')
                ->with('error', 'You are not enrolled in this classroom.');
        }

        return $next($request);
    }
}
