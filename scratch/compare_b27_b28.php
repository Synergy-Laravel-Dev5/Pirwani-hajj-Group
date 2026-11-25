<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- 18 bookings with 2027-04-27 ---\n";
$b27 = \App\Models\Booking::where('departure_date', '2027-04-27')->get();
foreach ($b27 as $b) {
    echo "ID: {$b->id}, Dep: {$b->departure_date}, Arr: {$b->arrival_date}, Pkg: {$b->package_name}\n";
    foreach ($b->hotels as $h) {
        echo "   Hotel: {$h->hotel_name}, In: {$h->check_in}, Out: {$h->check_out}, Nights: {$h->no_of_nights}\n";
    }
}

echo "--- Sample of 65 bookings with 2027-04-28 ---\n";
$b28 = \App\Models\Booking::where('departure_date', '2027-04-28')->take(3)->get();
foreach ($b28 as $b) {
    echo "ID: {$b->id}, Dep: {$b->departure_date}, Arr: {$b->arrival_date}, Pkg: {$b->package_name}\n";
    foreach ($b->hotels as $h) {
        echo "   Hotel: {$h->hotel_name}, In: {$h->check_in}, Out: {$h->check_out}, Nights: {$h->no_of_nights}\n";
    }
}
