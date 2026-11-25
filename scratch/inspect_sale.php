<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HotelSale;

$sale = HotelSale::with(['items', 'company', 'client'])->find(3);
echo "Customer: " . ($sale->customer_name ?? 'N/A') . "\n";
echo "Date: " . ($sale->invoice_date ?? 'N/A') . "\n";
echo "Total Amount: " . ($sale->total_amount ?? 0) . "\n";
echo "Items count: " . $sale->items->count() . "\n";
foreach ($sale->items as $i) {
    echo "  - Hotel: {$i->hotel_name}, Room: {$i->room_type}, Qty: {$i->quantity}, In: {$i->check_in} ({$i->hijri_check_in}), Out: {$i->check_out} ({$i->hijri_check_out}), Nights: {$i->no_of_nights}, Rate: {$i->rate_per_unit}, Amount: {$i->amount}\n";
}
