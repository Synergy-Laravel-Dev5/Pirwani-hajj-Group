<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageInquiry extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'pax' => 'integer',
    ];

    protected $appends = [
        'passport_document_url',
        'nic_or_visa_document_url',
    ];

    public function getPassportDocumentUrlAttribute(): ?string
    {
        if (empty($this->passport_document)) {
            return null;
        }

        if (str_starts_with($this->passport_document, 'http://') || str_starts_with($this->passport_document, 'https://')) {
            return $this->passport_document;
        }

        return asset('storage/' . ltrim($this->passport_document, '/'));
    }

    public function getNicOrVisaDocumentUrlAttribute(): ?string
    {
        if (empty($this->nic_or_visa_document)) {
            return null;
        }

        if (str_starts_with($this->nic_or_visa_document, 'http://') || str_starts_with($this->nic_or_visa_document, 'https://')) {
            return $this->nic_or_visa_document;
        }

        return asset('storage/' . ltrim($this->nic_or_visa_document, '/'));
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
