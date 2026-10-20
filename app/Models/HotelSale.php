<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hotel_sales';

    protected $guarded = ['id'];

    protected $casts = [
        'invoice_date'      => 'date',
        'total_amount'      => 'decimal:2',
        'till_received'     => 'decimal:2',
        'balance_remaining' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(HotelSaleItem::class, 'hotel_sale_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Generate next sequential invoice number like INV-HR-2027-0001
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = "HR-{$year}-";
        $last = self::withTrashed()
            ->where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($last && preg_match('/-(\d+)$/', $last->invoice_number, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        }

        return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
