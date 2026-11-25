<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dates = \App\Models\Booking::where('package_year', '2027')
    ->select('departure_date', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
    ->groupBy('departure_date')
    ->get();

foreach ($dates as $d) {
    echo "Departure Date: {$d->departure_date} -> Total: {$d->total}\n";
}
