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

    public function hotelClient()
    {
        return $this->belongsTo(HotelClient::class, 'hotel_client_id');
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
     * Exact Umm Al-Qura month start dates for Hajj / Umrah seasons (1446-1450 AH).
     * Synchronized with official Saudi calendar and Maghrib date transition.
     */
    public static $ummAlQuraMonthStarts = [
        // 1447 AH (2025-2026)
        '1447-09' => '2026-02-18', // Ramadan
        '1447-10' => '2026-03-20', // Shawwal
        '1447-11' => '2026-04-18', // Zilqaida
        '1447-12' => '2026-05-18', // Zilhajj
        // 1448 AH (2026-2027) - Hajj 2027
        '1448-01' => '2026-06-16', // Muharram
        '1448-02' => '2026-07-16', // Safar
        '1448-03' => '2026-08-14', // Rabi-I
        '1448-04' => '2026-09-13', // Rabi-II
        '1448-05' => '2026-10-12', // Jamadi-I
        '1448-06' => '2026-11-11', // Jamadi-II
        '1448-07' => '2026-12-10', // Rajab
        '1448-08' => '2027-01-09', // Shaban
        '1448-09' => '2027-02-08', // Ramadan
        '1448-10' => '2027-03-10', // Shawwal
        '1448-11' => '2027-04-08', // Zilqaida (29 days) -> 21 Zilqaida = 2027-04-28
        '1448-12' => '2027-05-07', // Zilhajj (30 days)  -> 01 Zilhajj = 2027-05-07, 08 Zilhajj = 2027-05-14
        // 1449 AH (2027-2028)
        '1449-01' => '2027-06-06',
        '1449-11' => '2028-03-28',
        '1449-12' => '2028-04-26',
        // 1450 AH (2028-2029)
        '1450-01' => '2028-05-26',
        '1450-11' => '2029-04-16',
        '1450-12' => '2029-05-15',
    ];

    /**
     * Convert Gregorian date (YYYY-MM-DD) to standard Hajj Hijri format (e.g. 21-ZILQAIDA)
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
            $targetDate = date('Y-m-d', $timestamp);

            $months = [
                1 => 'MUHARRAM', 2 => 'SAFAR', 3 => 'RABI-I', 4 => 'RABI-II',
                5 => 'JAMADI-I', 6 => 'JAMADI-II', 7 => 'RAJAB', 8 => 'SHABAN',
                9 => 'RAMADAN', 10 => 'SHAWWAL', 11 => 'ZILQAIDA', 12 => 'ZILHAJJ'
            ];

            // 1. Check Umm Al-Qura calendar table
            $keys = array_keys(self::$ummAlQuraMonthStarts);
            sort($keys);
            for ($i = 0; $i < count($keys); $i++) {
                $currKey = $keys[$i];
                $currDate = self::$ummAlQuraMonthStarts[$currKey];
                $nextDate = isset($keys[$i + 1]) ? self::$ummAlQuraMonthStarts[$keys[$i + 1]] : null;

                if ($targetDate >= $currDate && ($nextDate === null || $targetDate < $nextDate)) {
                    $dayDiff = (int)round((strtotime($targetDate) - strtotime($currDate)) / 86400);
                    $hDay = $dayDiff + 1;
                    $parts = explode('-', $currKey);
                    $hMonth = (int)$parts[1];
                    $monthName = $months[$hMonth] ?? ('M' . $hMonth);
                    return sprintf('%02d-%s', $hDay, $monthName);
                }
            }

            // 2. Fallback to Julian Day Kuwaiti formula
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

            $monthName = $months[$hm] ?? ('M' . $hm);
            return sprintf('%02d-%s', $hd, $monthName);
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Convert Hijri date string (e.g. 21-ZILQAIDA, 01-ZILHAJJ) to Gregorian date (YYYY-MM-DD)
     */
    public static function hijriToGregorian(string $hijriStr, int $defaultHijriYear = 1448): string
    {
        if (empty($hijriStr)) {
            return '';
        }

        try {
            $clean = strtoupper(trim($hijriStr));
            $clean = str_replace(['HIJRI', '/', '\\'], ' ', $clean);

            $hy = $defaultHijriYear;
            if (preg_match('/\b(14\d{2})\b/', $clean, $yMatches)) {
                $hy = (int)$yMatches[1];
                $clean = str_replace($yMatches[0], ' ', $clean);
            }

            if (!preg_match('/(\d{1,2})/', $clean, $dMatches)) {
                return '';
            }
            $hd = (int)$dMatches[1];

            $hm = 0;
            if (strpos($clean, 'MUHAR') !== false) $hm = 1;
            elseif (strpos($clean, 'SAF') !== false) $hm = 2;
            elseif (strpos($clean, 'RABI-I') !== false || strpos($clean, 'RABI 1') !== false || strpos($clean, 'RABI-1') !== false || strpos($clean, 'RABI-UL-AWWAL') !== false) $hm = 3;
            elseif (strpos($clean, 'RABI-II') !== false || strpos($clean, 'RABI 2') !== false || strpos($clean, 'RABI-2') !== false || strpos($clean, 'RABI-US-SANI') !== false) $hm = 4;
            elseif (strpos($clean, 'JAMADI-I') !== false || strpos($clean, 'JAMADI 1') !== false || strpos($clean, 'JAMADI-1') !== false) $hm = 5;
            elseif (strpos($clean, 'JAMADI-II') !== false || strpos($clean, 'JAMADI 2') !== false || strpos($clean, 'JAMADI-2') !== false) $hm = 6;
            elseif (strpos($clean, 'RAJ') !== false) $hm = 7;
            elseif (strpos($clean, 'SHA') !== false && strpos($clean, 'SHAW') === false) $hm = 8;
            elseif (strpos($clean, 'RAM') !== false) $hm = 9;
            elseif (strpos($clean, 'SHAW') !== false) $hm = 10;
            elseif (strpos($clean, 'ZILQ') !== false || strpos($clean, 'QAIDA') !== false || strpos($clean, 'QADH') !== false) $hm = 11;
            elseif (strpos($clean, 'ZILH') !== false || strpos($clean, 'HAJJ') !== false || strpos($clean, 'HIJJ') !== false) $hm = 12;

            if ($hm === 0) {
                return '';
            }

            // 1. Check Umm Al-Qura calendar table
            $key = sprintf('%04d-%02d', $hy, $hm);
            if (isset(self::$ummAlQuraMonthStarts[$key])) {
                $startDate = self::$ummAlQuraMonthStarts[$key];
                return date('Y-m-d', strtotime($startDate . " +" . ($hd - 1) . " days"));
            }

            // 2. Fallback to Julian Day
            $jd = (int)((11 * $hy + 3) / 30) + 354 * $hy + 30 * $hm - (int)(($hm - 1) / 2) + $hd + 1948440 - 385;

            $l = $jd + 68569;
            $n = (int)((4 * $l) / 146097);
            $l = $l - (int)((146097 * $n + 3) / 4);
            $i = (int)((4000 * ($l + 1)) / 1461001);
            $l = $l - (int)((1461 * $i) / 4) + 31;
            $j = (int)((80 * $l) / 2447);
            $day = $l - (int)((2447 * $j) / 80);
            $l = (int)($j / 11);
            $month = $j + 2 - 12 * $l;
            $year = 100 * ($n - 49) + $i + $l;

            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        } catch (\Throwable $e) {
            return '';
        }
    }
}
