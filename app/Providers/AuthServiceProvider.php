<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Models\Classroom;
use App\Models\Content;
use App\Models\User;
use App\Services\ClassroomService;
use App\Services\ContentStatusService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('view-classroom', function (User $user, Classroom $classroom) {
            return $user->id === $classroom->teacher_id || $user->hasRole(RoleEnum::ADMIN) || ClassroomService::isMember($classroom, $user);
        });

        Gate::define('view-content', function (User $user, Content $content) {
            $status = (new ContentStatusService($user, $content->classroom_id))->getStatuses($content->classroom->contents()->orderBy('order', 'asc')->get());

            $contentStatus = $status[$content->id] ?? 'locked';

            return in_array($contentStatus, ['unlocked', 'completed']);
        });

        Gate::define('update-quiz-submission', function (User $user, QuizSubmission $submission) {
            return $user->id === $submission->student_id;
        });
    }
}
