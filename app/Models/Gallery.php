<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category',
        'title',
        'image',
        'description',
    ];

    protected $appends = ['image_url'];

    public function images()
    {
        return $this->hasMany(GalleryImage::class)->orderBy('is_primary', 'desc')->orderBy('id', 'asc');
    }

    public function getImageUrlAttribute()
    {
        $primary = $this->relationLoaded('images')
            ? $this->images->first()
            : $this->images()->where('is_primary', true)->first();

        if ($primary) {
            return $primary->image_url;
        }

        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
