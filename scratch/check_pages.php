<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HotelSale;

$sale = HotelSale::with(['items.hotel', 'company', 'client', 'hotelClient', 'supplier'])->find(3);

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
echo "Total Pages: " . $canvas->get_page_count() . "\n";
