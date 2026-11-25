<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$c27 = DB::table('bookings')->where('departure_date', '2027-04-27')->count();
$c28 = DB::table('bookings')->where('departure_date', '2027-04-28')->count();
echo "Bookings with departure_date 2027-04-27: $c27\n";
echo "Bookings with departure_date 2027-04-28: $c28\n";

$h27 = DB::table('booking_hotels')->where('check_in', '2027-04-27')->count();
$h28 = DB::table('booking_hotels')->where('check_in', '2027-04-28')->count();
echo "BookingHotels with check_in 2027-04-27: $h27\n";
echo "BookingHotels with check_in 2027-04-28: $h28\n";
