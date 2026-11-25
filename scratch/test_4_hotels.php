<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HotelSale;
use App\Models\HotelSaleItem;

$sale = HotelSale::with(['items.hotel', 'company', 'client', 'hotelClient', 'supplier'])->find(3);

// Create 4 simulated items
$mockItems = collect([
    new HotelSaleItem([
        'hotel_name' => 'PULLMAN ZAMZAM MAKKAH',
        'room_type' => 'DOUBLE',
        'quantity' => 30,
        'meal_plan' => 'HALF BOARD',
        'check_in' => '2027-04-28',
        'check_out' => '2027-05-06',
        'hijri_check_in' => '21-ZILQAIDA',
        'hijri_check_out' => '29-ZILQAIDA',
        'no_of_nights' => 8,
        'rate_per_unit' => 1250,
        'amount' => 300000,
    ]),
    new HotelSaleItem([
        'hotel_name' => 'AZIZYA BUILDING',
        'room_type' => 'SHARING',
        'quantity' => 120,
        'meal_plan' => 'FULL BOARD',
        'check_in' => '2027-05-06',
        'check_out' => '2027-05-13',
        'hijri_check_in' => '29-ZILQAIDA',
        'hijri_check_out' => '07-ZILHAJJ',
        'no_of_nights' => 7,
        'rate_per_unit' => 450,
        'amount' => 378000,
    ]),
    new HotelSaleItem([
        'hotel_name' => 'MINA & ARAFAT CAMP (MAKTAB 02)',
        'room_type' => 'TENT BED',
        'quantity' => 120,
        'meal_plan' => 'FULL BOARD',
        'check_in' => '2027-05-14',
        'check_out' => '2027-05-18',
        'hijri_check_in' => '08-ZILHAJJ',
        'hijri_check_out' => '12-ZILHAJJ',
        'no_of_nights' => 4,
        'rate_per_unit' => 800,
        'amount' => 384000,
    ]),
    new HotelSaleItem([
        'hotel_name' => 'NUSK AL HIJRA MADINAH',
        'room_type' => 'DOUBLE',
        'quantity' => 30,
        'meal_plan' => 'BED & BREAKFAST',
        'check_in' => '2027-05-23',
        'check_out' => '2027-05-28',
        'hijri_check_in' => '17-ZILHAJJ',
        'hijri_check_out' => '22-ZILHAJJ',
        'no_of_nights' => 5,
        'rate_per_unit' => 600,
        'amount' => 90000,
    ]),
]);

$sale->setRelation('items', $mockItems);
$sale->total_amount = 1152000;
$sale->till_received = 350000;
$sale->balance_remaining = 802000;

$logoPath = public_path('assets/images/PIRWANI PNG FILE.png');
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

$pdf = Pdf::loadView('hotel_sales.pdf', compact('sale', 'logoBase64'));
$pdf->setPaper('A4', 'portrait');
$pdf->setOption('isHtml5ParserEnabled', true);
$pdf->setOption('isRemoteEnabled', true);
$pdf->setOption('defaultFont', 'DejaVu Sans');

$dompdf = $pdf->getDomPDF();
$dompdf->render();
$canvas = $dompdf->getCanvas();
echo "Simulated 4-Hotel Package Total Pages: " . $canvas->get_page_count() . "\n";
file_put_contents('scratch/test_4_hotels.pdf', $pdf->output());
