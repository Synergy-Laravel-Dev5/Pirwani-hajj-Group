<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bh = \App\Models\BookingHotel::where('booking_id', 103)->get();
foreach ($bh as $h) {
    echo "ID: {$h->id}, In: {$h->check_in}, Out: {$h->check_out}, Hijri In: {$h->hijri_check_in}, Hijri Out: {$h->hijri_check_out}\n";
}
