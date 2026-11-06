<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QurbaniBatchHajji extends Model
{
    protected $table = 'qurbani_batch_hajjis';

    protected $fillable = [
        'qurbani_batch_id',
        'booking_id',
        'booking_person_id',
        'status',
        'notified_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(QurbaniBatch::class, 'qurbani_batch_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(BookingPerson::class, 'booking_person_id');
    }
}
