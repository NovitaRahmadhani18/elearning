<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AchievementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();
        $isUnlocked = false;
        $unlockedAt = null;

        if ($user) {
            // Check if the achievement is unlocked for the current user
            // The 'users' relation should be loaded on the Achievement model
            // or we query the pivot table directly.
            // Assuming 'users' relation is loaded with pivot data.
            $pivot = $this->whenLoaded('users', function () use ($user) {
                return $this->users->firstWhere('id', $user->id)?->pivot;
            });

            if ($pivot) {
                $isUnlocked = true;
                $unlockedAt = $pivot->unlocked_at;
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->icon_path ? Storage::disk('public')->url($this->icon_path) : null,
            'locked' => !$isUnlocked,
            'achieved_at' => $this->when($isUnlocked, $unlockedAt),
        ];
    }
}
