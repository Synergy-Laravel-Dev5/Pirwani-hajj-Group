<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HajjBook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'thumbnail',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $appends = ['thumbnail_url', 'file_url'];

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}
