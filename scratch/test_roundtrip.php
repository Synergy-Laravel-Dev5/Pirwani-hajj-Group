<?php
function gregorianToHijri($dateStr): string
{
    if (empty($dateStr)) return '';
    $timestamp = strtotime($dateStr);
    if (!$timestamp) return '';
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
}

function hijriToGregorian(string $hijriStr, int $defaultHijriYear = 1448): string
{
    if (empty($hijriStr)) return '';
    $clean = strtoupper(trim($hijriStr));
    $clean = str_replace(['HIJRI', '/', '\\'], ' ', $clean);

    $hy = $defaultHijriYear;
    if (preg_match('/\b(14\d{2})\b/', $clean, $yMatches)) {
        $hy = (int)$yMatches[1];
        $clean = str_replace($yMatches[0], ' ', $clean);
    }

    if (!preg_match('/(\d{1,2})/', $clean, $dMatches)) return '';
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
    elseif (strpos($clean, 'ZILQ') !== false || strpos($clean, 'ZIL-Q') !== false || strpos($clean, 'ZIL Q') !== false || strpos($clean, 'QAIDA') !== false) $hm = 11;
    elseif (strpos($clean, 'ZILH') !== false || strpos($clean, 'ZIL-H') !== false || strpos($clean, 'ZIL H') !== false || strpos($clean, 'HAJJ') !== false) $hm = 12;

    if ($hm === 0) return '';

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
}

$testDates = [
    '2027-04-28' => '21-ZILQAIDA',
    '2027-05-06' => '29-ZILQAIDA',
    '2027-05-07' => '01-ZILHAJJ',
    '2027-05-14' => '08-ZILHAJJ',
    '2027-05-18' => '12-ZILHAJJ',
    '2027-05-23' => '17-ZILHAJJ',
    '2027-05-28' => '22-ZILHAJJ',
];

echo "Testing Round-Trip conversions:\n";
foreach ($testDates as $greg => $expectedHijri) {
    $hijri = gregorianToHijri($greg);
    $backGreg = hijriToGregorian($hijri);
    echo "$greg -> $hijri (Expected: $expectedHijri) -> $backGreg [Match: " . ($greg === $backGreg ? "YES" : "NO") . "]\n";
}

$userInputs = ['21-ZILQAIDA', '21-ZILQAD', '21 ZIL QADH', '21-ZILQAD 1448', '01-ZILHAJJ', '1-ZILHAJJ', '08-ZILHAJJ'];
echo "\nTesting various user Hijri inputs:\n";
foreach ($userInputs as $inp) {
    echo "$inp => " . hijriToGregorian($inp) . "\n";
}
