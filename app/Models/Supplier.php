<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suppliers';

    protected $guarded = ['id'];

    protected $casts = [
        'opening_balance' => 'decimal:2',
    ];

    /**
     * Compatibility accessor so that $supplier->company_name falls back to name.
     */
    public function getCompanyNameAttribute()
    {
        return $this->attributes['company_name'] ?? ($this->attributes['name'] ?? '');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function inventories()
    {
        return $this->hasMany(RoomInventory::class, 'supplier_id');
    }

    public function hotelSales()
    {
        return $this->hasMany(HotelSale::class, 'supplier_id');
    }

    /**
     * Generate sequential supplier code like SUP-001
     */
    public static function generateSupplierCode(): string
    {
        $last = self::withTrashed()
            ->whereNotNull('supplier_code')
            ->where('supplier_code', 'like', 'SUP-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($last && preg_match('/SUP-(\d+)$/', $last->supplier_code, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        }

        return 'SUP-' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
    }
}
