<?php

namespace App\Http\Controllers\Settings;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Http\Resources\ContentStudentResourc;
use App\Models\ContentStudent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show()
    {
        $classroomsCount = match (auth()->user()->role) {
            RoleEnum::ADMIN => \App\Models\Classroom::count(),
            RoleEnum::TEACHER => \App\Models\Classroom::where('teacher_id', auth()->id())->count(),
            RoleEnum::STUDENT => \App\Models\Classroom::whereHas('students', function ($q) {
                $q->where('student_id', auth()->id());
            })->count(),
            default => 0,
        };

        $contentStudent = match (auth()->user()->role) {
            RoleEnum::STUDENT => ContentStudent::where('user_id', auth()->id())
                ->with(['content', 'content.classroom'])
                ->orderBy('completed_at', 'desc')
                ->limit(10)
                ->get(),
            default => collect(),
        };

        return inertia(
            'settings/show',
            [
                'classroomsCount' => $classroomsCount,
                'contentStudent' => ContentStudentResourc::collection($contentStudent)
            ]
        );
    }

    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile settings.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        } else {
            // If no new avatar is uploaded, retain the existing avatar
            $validated['avatar'] = $user->avatar;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return to_route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
