<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class LeaderboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->user_id,
                'name' => $this->name,
                'avatar' => $this->avatar,
            ],
            'rank' => $this->rank,
            'score' => $this->when(isset($this->score), $this->score),
            'time_spent' => $this->when(isset($this->duration_seconds), gmdate('i:s', $this->duration_seconds)),
            'completed_at' => Carbon::parse($this->completed_at)->toDateTimeString(),
        ];
    }
}
