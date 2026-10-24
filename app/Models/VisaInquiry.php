<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class VisaInquiry extends Model
{
    protected $fillable = [
        'visa_country_id',
        'visa_type_id',
        'full_name',
        'phone_number',
        'email',
        'cnic',
        'documents',
        'status',
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'visa_country_id');
    }

    public function visaType(): BelongsTo
    {
        return $this->belongsTo(VisaType::class, 'visa_type_id');
    }

    // Accessor that formats document paths into full accessible URLs
    public function getDocumentListAttribute(): array
    {
        if (empty($this->documents) || !is_array($this->documents)) {
            return [];
        }

        return array_map(function ($doc) {
            return [
                'title'    => $doc['title'] ?? 'Document',
                'file_url' => !empty($doc['file_path']) ? asset('storage/' . $doc['file_path']) : null,
            ];
        }, $this->documents);
    }
}
