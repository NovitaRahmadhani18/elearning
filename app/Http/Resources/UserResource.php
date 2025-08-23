<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'avater' => $this->profile_picture, // Assuming you have a profile_picture attribute
            'id_number' => $this->id_number, // Assuming you have an id_number attribute
            'is_active' => $this->is_active, // Assuming you have an is_active attribute
            'address' => $this->address, // Assuming you have an address attribute
            'gender' => $this->gender, // Assuming
            'avatar' => $this->when($this->avatar, function () {
                return Storage::disk('public')->url($this->avatar);
            }, null)
        ];
    }
}
