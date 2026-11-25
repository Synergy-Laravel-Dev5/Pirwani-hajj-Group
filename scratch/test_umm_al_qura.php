<?php
// Let's test the Umm Al Qura month start mapping
class UmmAlQuraTest {
    // Exact Umm Al-Qura start dates for months in Hajj years 1446 - 1450
    // Key format: "YYYY-MM" (Hijri year - month) => Gregorian start date "YYYY-MM-DD"
    public static $monthStarts = [
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
        '1448-11' => '2027-04-08', // Zilqaida (29 days) -> 21 Zilqaida = 2027-04-28!
        '1448-12' => '2027-05-07', // Zilhajj (30 days) -> 1 Zilhajj = 2027-05-07!
        // 1449 AH (2027-2028)
        '1449-01' => '2027-06-06',
        '1449-11' => '2028-03-28',
        '1449-12' => '2028-04-26',
        // 1450 AH (2028-2029)
        '1450-01' => '2028-05-26',
        '1450-11' => '2029-04-16',
        '1450-12' => '2029-05-15',
    ];

    public static function gregorianToHijri($dateStr) {
        $ts = strtotime($dateStr);
        if (!$ts) return '';
        $targetDate = date('Y-m-d', $ts);

        // Find match in Umm Al Qura table
        $prevKey = null;
        $prevDate = null;
        $months = [
            1 => 'MUHARRAM', 2 => 'SAFAR', 3 => 'RABI-I', 4 => 'RABI-II',
            5 => 'JAMADI-I', 6 => 'JAMADI-II', 7 => 'RAJAB', 8 => 'SHABAN',
            9 => 'RAMADAN', 10 => 'SHAWWAL', 11 => 'ZILQAIDA', 12 => 'ZILHAJJ'
        ];

        // Sort keys
        $keys = array_keys(self::$monthStarts);
        sort($keys);
        for ($i = 0; $i < count($keys); $i++) {
            $currKey = $keys[$i];
            $currDate = self::$monthStarts[$currKey];
            $nextDate = isset($keys[$i + 1]) ? self::$monthStarts[$keys[$i + 1]] : null;

            if ($targetDate >= $currDate && ($nextDate === null || $targetDate < $nextDate)) {
                $dayDiff = (int)round((strtotime($targetDate) - strtotime($currDate)) / 86400);
                $hDay = $dayDiff + 1;
                $parts = explode('-', $currKey);
                $hMonth = (int)$parts[1];
                $monthName = $months[$hMonth] ?? 'M' . $hMonth;
                return sprintf('%02d-%s', $hDay, $monthName);
            }
        }
        return '';
    }

    public static function hijriToGregorian($hijriStr, $defaultYear = 1448) {
        $clean = strtoupper(trim($hijriStr));
        $hy = $defaultYear;
        if (preg_match('/\b(14\d{2})\b/', $clean, $ym)) $hy = (int)$ym[1];
        if (!preg_match('/(\d{1,2})/', $clean, $dm)) return '';
        $hd = (int)$dm[1];

        $hm = 0;
        if (strpos($clean, 'MUHAR') !== false) $hm = 1;
        elseif (strpos($clean, 'SAF') !== false) $hm = 2;
        elseif (strpos($clean, 'RABI-I') !== false || strpos($clean, 'RABI 1') !== false || strpos($clean, 'RABI-1') !== false) $hm = 3;
        elseif (strpos($clean, 'RABI-II') !== false || strpos($clean, 'RABI 2') !== false || strpos($clean, 'RABI-2') !== false) $hm = 4;
        elseif (strpos($clean, 'JAMADI-I') !== false || strpos($clean, 'JAMADI 1') !== false) $hm = 5;
        elseif (strpos($clean, 'JAMADI-II') !== false || strpos($clean, 'JAMADI 2') !== false) $hm = 6;
        elseif (strpos($clean, 'RAJ') !== false) $hm = 7;
        elseif (strpos($clean, 'SHA') !== false && strpos($clean, 'SHAW') === false) $hm = 8;
        elseif (strpos($clean, 'RAM') !== false) $hm = 9;
        elseif (strpos($clean, 'SHAW') !== false) $hm = 10;
        elseif (strpos($clean, 'ZILQ') !== false || strpos($clean, 'QAIDA') !== false) $hm = 11;
        elseif (strpos($clean, 'ZILH') !== false || strpos($clean, 'HAJJ') !== false) $hm = 12;

        if ($hm === 0) return '';
        $key = sprintf('%04d-%02d', $hy, $hm);
        if (isset(self::$monthStarts[$key])) {
            $startDate = self::$monthStarts[$key];
            return date('Y-m-d', strtotime($startDate . " +" . ($hd - 1) . " days"));
        }
        return '';
    }
}

echo "2027-04-28 => " . UmmAlQuraTest::gregorianToHijri('2027-04-28') . "\n";
echo "2027-05-06 => " . UmmAlQuraTest::gregorianToHijri('2027-05-06') . "\n";
echo "2027-05-07 => " . UmmAlQuraTest::gregorianToHijri('2027-05-07') . "\n";
echo "2027-05-14 => " . UmmAlQuraTest::gregorianToHijri('2027-05-14') . "\n";
echo "2027-05-18 => " . UmmAlQuraTest::gregorianToHijri('2027-05-18') . "\n";
echo "2027-05-23 => " . UmmAlQuraTest::gregorianToHijri('2027-05-23') . "\n";
echo "2027-05-28 => " . UmmAlQuraTest::gregorianToHijri('2027-05-28') . "\n";

echo "21-ZILQAIDA => " . UmmAlQuraTest::hijriToGregorian('21-ZILQAIDA') . "\n";
echo "29-ZILQAIDA => " . UmmAlQuraTest::hijriToGregorian('29-ZILQAIDA') . "\n";
echo "01-ZILHAJJ => " . UmmAlQuraTest::hijriToGregorian('01-ZILHAJJ') . "\n";
echo "08-ZILHAJJ => " . UmmAlQuraTest::hijriToGregorian('08-ZILHAJJ') . "\n";
echo "12-ZILHAJJ => " . UmmAlQuraTest::hijriToGregorian('12-ZILHAJJ') . "\n";
echo "17-ZILHAJJ => " . UmmAlQuraTest::hijriToGregorian('17-ZILHAJJ') . "\n";
echo "22-ZILHAJJ => " . UmmAlQuraTest::hijriToGregorian('22-ZILHAJJ') . "\n";
