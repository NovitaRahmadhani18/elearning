<?php

namespace App\Observers;

use App\Models\Content;
use App\Models\ContentUser;
use Illuminate\Support\Facades\Log;

class ContentCompletionObserver
{
    /**
     * Handle the pivot table attachment for content completion
     * This will be triggered when a user completes a material
     */
    public function created(ContentUser $contentUser): void
    {
        $content = $contentUser->content;
        $user = $contentUser->user;

        if (!$content || !$user) {
            Log::warning("Content or user not found for ContentUser {$contentUser->id}");
            return;
        }

        // Log the material completion activity
        $this->logMaterialCompletion($content, [$user->id], [
            [
                'points_earned' => $contentUser->points_earned ?? 0,
                'score' => $contentUser->score ?? 0,
                'completion_time' => $contentUser->completed_at?->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Log detailed material completion activity
     */
    protected function logMaterialCompletion(Content $content, array $userIds, array $attributes): void
    {
        try {
            foreach ($userIds as $index => $userId) {
                $user = \App\Models\User::find($userId);
                if (!$user) continue;

                $pivotAttributes = $attributes[$index] ?? [];
                $pointsEarned = $pivotAttributes['points_earned'] ?? 0;
                $score = $pivotAttributes['score'] ?? 0;
                $completionTime = $pivotAttributes['completion_time'] ?? null;

                $contentable = $content->contentable;
                $classroom = $content->classroom;

                activity('material_completion')
                    ->causedBy($user)
                    ->performedOn($content)
                    ->withProperties([
                        'content_title' => $content->title ?? 'Unknown Content',
                        'content_type' => $content->contentable_type ?? 'Unknown Type',
                        'contentable_id' => $content->contentable_id,
                        'classroom_name' => $classroom?->title ?? 'Unknown Classroom',
                        'classroom_id' => $content->classroom_id,
                        'points_earned' => $pointsEarned,
                        'score' => $score,
                        'completion_time' => $completionTime,
                        'completed_at' => now()->toDateTimeString(),
                        'material_details' => [
                            'type' => class_basename($content->contentable_type ?? ''),
                            'title' => $contentable?->title ?? $contentable?->name ?? 'Unknown',
                        ]
                    ])
                    ->log(
                        $pointsEarned > 0
                            ? "Material completed with {$pointsEarned} points earned"
                            : 'Material completed'
                    );

                Log::info("Material completion activity logged for user {$userId}, content {$content->id}, points: {$pointsEarned}");
            }
        } catch (\Exception $e) {
            Log::error("Error logging material completion activity for content {$content->id}: " . $e->getMessage());
        }
    }
}
