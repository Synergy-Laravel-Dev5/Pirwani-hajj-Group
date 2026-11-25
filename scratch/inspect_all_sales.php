<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HotelSale;

$sales = HotelSale::withCount('items')->get();
foreach ($sales as $s) {
    echo "ID: {$s->id}, Invoice: {$s->invoice_number}, Title: {$s->reservation_title}, Customer: {$s->customer_name}, Items: {$s->items_count}, Total: {$s->total_amount}\n";
}
