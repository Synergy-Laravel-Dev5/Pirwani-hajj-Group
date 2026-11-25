<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$b11 = [172, 174, 175, 176, 178, 180, 182, 103, 203, 204, 207];

echo "Simulating update for 11 bookings hotels:\n";
foreach ($b11 as $id) {
    $hotels = \App\Models\BookingHotel::where('booking_id', $id)->get();
    echo "Booking $id (" . count($hotels) . " hotels):\n";
    foreach ($hotels as $h) {
        $in = $h->check_in ? date('Y-m-d', strtotime($h->check_in)) : null;
        $out = $h->check_out ? date('Y-m-d', strtotime($h->check_out)) : null;
        $newIn = $in ? date('Y-m-d', strtotime($in . ' +1 day')) : null;
        $newOut = $out ? date('Y-m-d', strtotime($out . ' +1 day')) : null;
        echo "   {$h->hotel_name}: {$in} -> {$newIn} | {$out} -> {$newOut}\n";
    }
}
