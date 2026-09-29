<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'hotel_number',
        'code',
        'name',
        'address',
        'contact',
        'email',
        'place',
        'accommodation_type',
        'accommodation_category',
        'logo',
        'status',
    ];
}
