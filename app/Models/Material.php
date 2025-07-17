<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Te7aHoudini\LaravelTrix\Traits\HasTrixRichText;

class Material extends Model
{
    use HasTrixRichText;


    protected $guarded = [];

    public function getThumbnailUrlAttribute()
    {

        // get from trix rich text
        $trixAttachment = $this->trixAttachments()->first();
        if ($trixAttachment && $trixAttachment->attachment) {
            return Storage::disk($trixAttachment->disk)->url($trixAttachment->attachment);
        }

        return '';
    }

    public function contents(): MorphMany
    {
        return $this->morphMany(Content::class, 'contentable');
    }

    public function classroom()
    {
        return $this->hasOneThrough(Classroom::class, Content::class, 'contentable_id', 'id', 'id', 'classroom_id')
            ->where('contentable_type', self::class);
    }
}
