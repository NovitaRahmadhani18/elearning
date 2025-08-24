<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ContentStatusService
{
    private Collection $completedContentIds;

    public function __construct(User $student)
    {
        $this->completedContentIds = $student->contents()->pluck('contents.id');
    }

    public function getStatuses(Collection $contents): Collection
    {
        $statuses = collect();
        $previousContentCompleted = true; // The first content is always unlocked by default

        foreach ($contents as $content) {
            $isCompleted = $this->completedContentIds->contains($content->id);

            if ($isCompleted) {
                $status = 'completed';
                $previousContentCompleted = true;
            } elseif ($previousContentCompleted) {
                $status = 'unlocked';
                $previousContentCompleted = false;
            } else {
                $status = 'locked';
            }

            $statuses->put($content->id, $status);
        }

        return $statuses;
    }
}
