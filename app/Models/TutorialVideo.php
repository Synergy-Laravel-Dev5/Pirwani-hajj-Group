<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialVideo extends Model
{
    protected $table = 'tutorial_videos';

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'video_type',
        'video_file',
        'video_link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['thumbnail_url', 'play_url'];

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        // If it's a YouTube link, automatically generate YouTube thumbnail
        if ($this->video_type === 'link' && $this->video_link) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->video_link, $match);
            if (!empty($match[1])) {
                return 'https://img.youtube.com/vi/' . $match[1] . '/hqdefault.jpg';
            }
        }

        return null;
    }

    public function getPlayUrlAttribute(): ?string
    {
        if ($this->video_type === 'file' && $this->video_file) {
            return asset('storage/' . $this->video_file);
        }
        return $this->video_link;
    }
}
