<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HotelSale;

echo "Pdf facade exists: " . (class_exists(Pdf::class) ? 'YES' : 'NO') . "\n";

$sale = HotelSale::with(['items.hotel', 'company', 'client'])->latest()->first();
if ($sale) {
    echo "Found sale ID: {$sale->id}, Invoice: {$sale->invoice_number}\n";
} else {
    echo "No HotelSale found\n";
}
