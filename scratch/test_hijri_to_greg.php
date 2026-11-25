<?php
// Let's test standard Hijri to Gregorian conversion algorithms
function hijri_to_gregorian_tabular($hy, $hm, $hd) {
    // Julian day from Hijri
    $jd = (int)((11 * $hy + 3) / 30) + 354 * $hy + 30 * $hm - (int)(($hm - 1) / 2) + $hd + 1948440 - 385;
    
    // Julian day to Gregorian
    $l = $jd + 68569;
    $n = (int)((4 * $l) / 146097);
    $l = $l - (int)((146097 * $n + 3) / 4);
    $i = (int)((4000 * ($l + 1)) / 1461001);
    $l = $l - (int)((1461 * $i) / 4) + 31;
    $j = (int)((80 * $l) / 2447);
    $d = $l - (int)((2447 * $j) / 80);
    $l = (int)($j / 11);
    $m = $j + 2 - 12 * $l;
    $y = 100 * ($n - 49) + $i + $l;
    
    return sprintf('%04d-%02d-%02d', $y, $m, $d);
}

echo "1448-11-21 (21 Zilqaida 1448) => " . hijri_to_gregorian_tabular(1448, 11, 21) . "\n";
echo "1448-11-22 (22 Zilqaida 1448) => " . hijri_to_gregorian_tabular(1448, 11, 22) . "\n";
