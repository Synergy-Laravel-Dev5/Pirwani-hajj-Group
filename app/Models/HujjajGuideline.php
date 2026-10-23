<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HujjajGuideline extends Model
{
    protected $table = 'hujjaj_guidelines';

    protected $fillable = [
        'title',
        'sub_heading',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
