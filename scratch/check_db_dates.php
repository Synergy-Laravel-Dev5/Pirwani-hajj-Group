<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Packages ===\n";
foreach (\App\Models\Package::all() as $p) {
    echo "ID: {$p->id}, Name: {$p->name}, Year: {$p->year}, Dep: {$p->departure_date_str}, Arr: {$p->arrival_date_str}\n";
    if ($p->accommodations) {
        foreach ($p->accommodations as $a) {
            echo "  Acc: {$a->place}, In: {$a->check_in}, Out: {$a->check_out}, Note: {$a->note}, Sharing: {$a->sharing}\n";
        }
    }
}

echo "=== Room Inventories ===\n";
foreach (\App\Models\RoomInventory::all() as $ri) {
    echo "ID: {$ri->id}, Hotel: {$ri->hotel_name}, In: {$ri->check_in}, Out: {$ri->check_out}, Hijri In: {$ri->hijri_check_in}, Hijri Out: {$ri->hijri_check_out}\n";
}

echo "=== Hotel Sales ===\n";
foreach (\App\Models\HotelSale::all() as $hs) {
    echo "ID: {$hs->id}, Customer: {$hs->customer_name}\n";
    if ($hs->items) {
        foreach ($hs->items as $it) {
            echo "  Item: {$it->hotel_name}, In: {$it->check_in}, Out: {$it->check_out}, Hijri In: {$it->hijri_check_in}, Hijri Out: {$it->hijri_check_out}\n";
        }
    }
}
