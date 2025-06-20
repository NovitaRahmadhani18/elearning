<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Te7aHoudini\LaravelTrix\Traits\HasTrixRichText;

class Material extends Model
{
    use HasTrixRichText;

    protected $guarded = [];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function getThumbnailUrlAttribute()
    {

        // get from trix rich text
        $trixAttachment = $this->trixAttachments()->first();
        if ($trixAttachment && $trixAttachment->attachment) {
            return Storage::disk($trixAttachment->disk)->url($trixAttachment->attachment);
        }

        return '';
    }
}
