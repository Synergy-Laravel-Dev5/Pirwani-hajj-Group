<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bIds = [172, 174, 175, 176, 178, 180, 182, 103, 203, 204, 207, 224, 226];
foreach ($bIds as $id) {
    $b = \App\Models\Booking::find($id);
    if ($b) {
        echo "Booking $id: Pkg={$b->package_id}, Dep={$b->departure_date}, Arr={$b->arrival_date}, PkgName={$b->package_name}\n";
    }
}
