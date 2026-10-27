<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HujjajGuideline extends Model
{
    use SoftDeletes;

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
