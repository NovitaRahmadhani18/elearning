<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Peringkat akan bernilai null jika siswa belum mengerjakan
            'rank' => $this->whenNotNull($this->rank),
            'user' => [
                'id' => $this->user_id,
                'name' => $this->name,
                'avatar' => $this->avatar, // Asumsi ada accessor
            ],
            // Data pengerjaan hanya akan ada jika siswa sudah mengerjakan
            'score' => $this->whenNotNull($this->score),
            'duration_seconds' => $this->whenNotNull($this->duration_seconds),
            'completed_at' => $this->whenNotNull($this->completed_at),

            // Contoh: Menambahkan 'points_awarded' untuk materi
            'points_awarded' => $this->when(isset($this->points_awarded), $this->points_awarded),
        ];
    }
}
