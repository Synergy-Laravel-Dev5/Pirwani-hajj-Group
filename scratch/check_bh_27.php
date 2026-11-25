<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bHotels = \App\Models\BookingHotel::where('check_in', '2027-04-27')->get();
echo "BookingHotels with check_in 2027-04-27: " . count($bHotels) . "\n";
foreach ($bHotels as $bh) {
    echo "ID: {$bh->id}, Booking ID: {$bh->booking_id}, Hotel: {$bh->hotel_name}, In: {$bh->check_in}, Out: {$bh->check_out}\n";
}
