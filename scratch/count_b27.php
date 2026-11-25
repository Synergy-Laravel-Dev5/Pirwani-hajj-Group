<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bookings = \App\Models\Booking::where('departure_date', '2027-04-27')->get();
echo "Total bookings with departure_date 2027-04-27: " . count($bookings) . "\n";
foreach ($bookings as $b) {
    echo "ID: {$b->id}, Client: " . ($b->client ? $b->client->name : 'N/A') . ", Care of: {$b->care_of}, Package: {$b->package_name}\n";
}
