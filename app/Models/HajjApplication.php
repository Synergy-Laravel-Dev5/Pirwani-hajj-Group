<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HajjApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'dob' => 'date',
        'passport_expiry' => 'date',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
