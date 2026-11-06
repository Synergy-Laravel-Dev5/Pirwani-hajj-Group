<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QurbaniBatch extends Model
{
    use SoftDeletes;

    protected $table = 'qurbani_batches';

    protected $fillable = [
        'batch_name',
        'qurbani_date',
        'start_time',
        'end_time',
        'title',
        'message',
        'status',
        'fired_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'qurbani_date' => 'date',
        'fired_at'     => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hajjis(): HasMany
    {
        return $this->hasMany(QurbaniBatchHajji::class, 'qurbani_batch_id');
    }

    /**
     * Check if the batch announcement is ready to be fired.
     */
    public function isDueToFire(): bool
    {
        if ($this->status === 'fired' || $this->status === 'completed' || $this->status === 'cancelled') {
            return false;
        }

        $dateStr = $this->qurbani_date->format('Y-m-d');
        $timeStr = is_string($this->start_time) ? substr($this->start_time, 0, 8) : $this->start_time;
        $batchStart = \Carbon\Carbon::parse("{$dateStr} {$timeStr}");

        return now()->greaterThanOrEqualTo($batchStart);
    }
}
