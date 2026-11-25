<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \App\Models\BookingHotel::where('check_in', 'like', '2027-04%')->get();
foreach ($rows as $r) {
    echo "ID: {$r->id}, Booking ID: {$r->booking_id}, Hotel: {$r->hotel_name}, In: {$r->check_in}, Out: {$r->check_out}, Hijri In: {$r->hijri_check_in}, Hijri Out: {$r->hijri_check_out}\n";
}
