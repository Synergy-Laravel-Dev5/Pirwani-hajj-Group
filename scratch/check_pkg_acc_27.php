<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$accs = \Illuminate\Support\Facades\DB::table('package_accommodations')->where('check_in', 'like', '%2027-04-27%')->get();
echo "Package accommodations with 2027-04-27: " . count($accs) . "\n";
foreach ($accs as $a) {
    echo "  Package ID: {$a->package_id}, Place: {$a->place}, Check In: {$a->check_in}\n";
}
