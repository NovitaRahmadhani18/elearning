<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'body',
        'attachment_path',
    ];

    public function content(): MorphOne
    {
        return $this->morphOne(Content::class, 'contentable');
    }
}