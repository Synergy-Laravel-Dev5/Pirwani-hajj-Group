<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p1 = \App\Models\Package::find(1);
echo "Package 1: {$p1->name}\n";
foreach ($p1->accommodations as $a) {
    echo "  Acc: {$a->place}, In: {$a->check_in}, Out: {$a->check_out}\n";
}
