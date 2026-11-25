<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\Package::find(2);
echo "Package 2:\n";
echo "  Title: {$p->title}\n";
echo "  Dep Date Str: {$p->departure_date_str}\n";
echo "  Arr Date Str: {$p->arrival_date_str}\n";
foreach ($p->accommodations as $acc) {
    echo "  Acc ID: {$acc->id}, Place: {$acc->place}, Hotel: {$acc->hotel}, In: {$acc->check_in}, Out: {$acc->check_out}, Note: {$acc->note}, Sharing: {$acc->sharing}\n";
}
