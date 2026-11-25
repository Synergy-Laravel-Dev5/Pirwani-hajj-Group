<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$b = \App\Models\Booking::find(172);
echo "Booking 172:\n";
echo "  Package ID: {$b->package_id}\n";
echo "  Package Name: {$b->package_name}\n";
echo "  Departure Date: {$b->departure_date}\n";
echo "  Arrival Date: {$b->arrival_date}\n";
echo "  Remarks: {$b->remarks}\n";

$b109 = \App\Models\Booking::find(109);
echo "Booking 109:\n";
echo "  Package ID: {$b109->package_id}\n";
echo "  Package Name: {$b109->package_name}\n";
echo "  Departure Date: {$b109->departure_date}\n";
echo "  Arrival Date: {$b109->arrival_date}\n";
echo "  Remarks: {$b109->remarks}\n";
