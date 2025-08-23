<?php

namespace App\Listeners;

use App\Events\ContentCompleted;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\StudentPoint;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class ProcessContentCompletion implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ContentCompleted $event): void
    {
        DB::transaction(function () use ($event) {

            // 2. Award points based on content type
            $pointsToAward = 0;
            $description = '';

            if ($event->content->contentable_type === Material::class) {
                $pointsToAward = $event->content->points;
                $description = 'Completed material: ' . $event->content->title;
            } elseif ($event->content->contentable_type === Quiz::class && isset($event->data['score'])) {
                // For quizzes, points are based on the score passed in the event data
                $pointsToAward = $event->data['score']; // Or some calculation based on score
                $description = 'Completed quiz: ' . $event->content->title;
            }

            if ($pointsToAward > 0) {
                // Check if points have already been awarded for this specific content
                $alreadyAwarded = StudentPoint::where('user_id', $event->user->id)
                    ->where('sourceable_id', $event->content->id)
                    ->where('sourceable_type', get_class($event->content))
                    ->exists();

                if (!$alreadyAwarded) {
                    StudentPoint::create([
                        'user_id' => $event->user->id,
                        'points_earned' => $pointsToAward,
                        'sourceable_id' => $event->content->id,
                        'sourceable_type' => get_class($event->content),
                        'description' => $description,
                    ]);

                    // 3. Update the total points on the user model
                    $event->user->increment('total_points', $pointsToAward);

                    // 1. Mark content as completed for the student (if not already)
                    $event->user->contents()
                        ->syncWithoutDetaching(
                            [
                                $event->content->id => [
                                    'status' => 'completed',
                                    'completed_at' => now(),
                                    'score' => $pointsToAward,
                                ]
                            ]
                        );
                }
            }
        });
    }
}
