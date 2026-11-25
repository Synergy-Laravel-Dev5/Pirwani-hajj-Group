<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::beginTransaction();
try {
    // 1. Update bookings departure_date
    $updatedBookings = DB::table('bookings')
        ->where('departure_date', '2027-04-27')
        ->update([
            'departure_date' => '2027-04-28',
            'updated_at' => now(),
        ]);
    echo "Updated bookings departure_date from 2027-04-27 to 2027-04-28: $updatedBookings\n";

    // Update arrival_date for those on 2027-05-27 that had departure 2027-04-27
    $updatedArrivals = DB::table('bookings')
        ->where('departure_date', '2027-04-28')
        ->where('arrival_date', '2027-05-27')
        ->update([
            'arrival_date' => '2027-05-28',
            'updated_at' => now(),
        ]);
    echo "Updated bookings arrival_date to 2027-05-28: $updatedArrivals\n";

    // 2. Update booking_hotels for the 11 bookings
    $targetBookingIds = [172, 174, 175, 176, 178, 180, 182, 103, 203, 204, 207];

    $hotels = DB::table('booking_hotels')
        ->whereIn('booking_id', $targetBookingIds)
        ->get();

    echo "Found " . count($hotels) . " hotel stays to calibrate for the 11 bookings.\n";

    foreach ($hotels as $h) {
        $in = $h->check_in ? date('Y-m-d', strtotime($h->check_in)) : null;
        $out = $h->check_out ? date('Y-m-d', strtotime($h->check_out)) : null;

        if ($in && $out) {
            $newIn = date('Y-m-d', strtotime($in . ' +1 day'));
            $newOut = date('Y-m-d', strtotime($out . ' +1 day'));

            DB::table('booking_hotels')
                ->where('id', $h->id)
                ->update([
                    'check_in' => $newIn,
                    'check_out' => $newOut,
                    'updated_at' => now(),
                ]);
            echo "  Hotel ID {$h->id} (Booking {$h->booking_id}, {$h->hotel_name}): $in -> $newIn | $out -> $newOut\n";
        }
    }

    DB::commit();
    echo "\nAll database dates successfully committed!\n";
} catch (\Throwable $e) {
    DB::rollBack();
    echo "Error updating database: " . $e->getMessage() . "\n";
}
