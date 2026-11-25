<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HotelSale;

$dates = [
    '2027-04-28' => '21-ZILQAIDA',
    '2027-05-06' => '29-ZILQAIDA',
    '2027-05-07' => '01-ZILHAJJ',
    '2027-05-14' => '08-ZILHAJJ',
    '2027-05-18' => '12-ZILHAJJ',
    '2027-05-23' => '17-ZILHAJJ',
    '2027-05-28' => '22-ZILHAJJ',
];

echo "--- Gregorian to Hijri ---\n";
foreach ($dates as $greg => $expectedHijri) {
    $actualHijri = HotelSale::gregorianToHijri($greg);
    $status = ($actualHijri === $expectedHijri) ? "PASS" : "FAIL (got $actualHijri)";
    echo "$greg => $actualHijri [$status]\n";
}

echo "\n--- Hijri to Gregorian ---\n";
foreach ($dates as $expectedGreg => $hijri) {
    $actualGreg = HotelSale::hijriToGregorian($hijri, 1448);
    $status = ($actualGreg === $expectedGreg) ? "PASS" : "FAIL (got $actualGreg)";
    echo "$hijri => $actualGreg [$status]\n";
}
