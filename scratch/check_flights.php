<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Package::all() as $p) {
    echo "Package ID: {$p->id}, Name: {$p->name}\n";
    if ($p->transportFlights) {
        foreach ($p->transportFlights as $f) {
            echo "  Flight: Dep Date: {$f->departure_date}, Arr Date: {$f->arrival_date}, Flight No: {$f->flight_no}\n";
        }
    }
}
