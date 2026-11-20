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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
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

    /**
     * Convert Gregorian date (YYYY-MM-DD) to standard Hajj Hijri format (e.g. 22-ZILQAIDA)
     */
    public static function gregorianToHijri($dateStr): string
    {
        if (empty($dateStr)) {
            return '';
        }

        try {
            $timestamp = strtotime($dateStr);
            if (!$timestamp) {
                return '';
            }
            $y = (int)date('Y', $timestamp);
            $m = (int)date('n', $timestamp);
            $d = (int)date('j', $timestamp);

            if (($y > 1582) || (($y == 1582) && ($m > 10)) || (($y == 1582) && ($m == 10) && ($d > 14))) {
                $jd = (int)((1461 * ($y + 4800 + (int)(($m - 14) / 12))) / 4) +
                      (int)((367 * ($m - 2 - 12 * ((int)(($m - 14) / 12)))) / 12) -
                      (int)((3 * (int)((($y + 4900 + (int)(($m - 14) / 12)) / 100))) / 4) +
                      $d - 32075;
            } else {
                $jd = 367 * $y - (int)((7 * ($y + 5001 + (int)(($m - 9) / 7))) / 4) +
                      (int)((275 * $m) / 9) + $d + 1729777;
            }

            $l = $jd - 1948440 + 10632;
            $n = (int)(($l - 1) / 10631);
            $l = $l - 10631 * $n + 354;
            $j = ((int)((10985 - $l) / 5316)) * ((int)((50 * $l) / 17719)) +
                 ((int)($l / 5670)) * ((int)((43 * $l) / 15238));
            $l = $l - ((int)((30 - $j) / 15)) * ((int)((17719 * $j) / 50)) -
                 ((int)($j / 16)) * ((int)((15238 * $j) / 43)) + 29;
            $hm = (int)((24 * $l) / 709);
            $hd = $l - (int)((709 * $hm) / 24);

            $months = [
                1 => 'MUHARRAM', 2 => 'SAFAR', 3 => 'RABI-I', 4 => 'RABI-II',
                5 => 'JAMADI-I', 6 => 'JAMADI-II', 7 => 'RAJAB', 8 => 'SHABAN',
                9 => 'RAMADAN', 10 => 'SHAWWAL', 11 => 'ZILQAIDA', 12 => 'ZILHAJJ'
            ];

            $monthName = $months[$hm] ?? ('M' . $hm);
            return sprintf('%02d-%s', $hd, $monthName);
        } catch (\Throwable $e) {
            return '';
        }
    }
}
