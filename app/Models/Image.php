<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    /** @use HasFactory<\Database\Factories\ImageFactory> */
    use HasFactory;

    protected $appends = [
        'url',
    ];

    protected $fillable = [
        'name',
        'filename',
        'size',
        'mimeType',
        'alt',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Image $image) {
            Storage::disk('images')->delete($image->filename);
        });
    }


    public function getUrlAttribute(): string
    {
        return route('images.show', ['image' => $this->id]);
    }

    public function getPathAttribute(): string
    {
        return Storage::disk('images')->path($this->filename);
    }
}
