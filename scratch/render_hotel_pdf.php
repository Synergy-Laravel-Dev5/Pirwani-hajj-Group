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

try {
    $pdf = Pdf::loadView('hotel_sales.pdf', compact('sale', 'logoBase64'));
    $pdf->setPaper('A4', 'portrait');
    $pdf->setOption('isHtml5ParserEnabled', true);
    $pdf->setOption('isRemoteEnabled', true);
    $pdf->setOption('defaultFont', 'DejaVu Sans');

    $content = $pdf->output();
    file_put_contents('scratch/test_hotel_voucher.pdf', $content);
    echo "SUCCESS: PDF generated and saved to scratch/test_hotel_voucher.pdf (size: " . strlen($content) . " bytes)\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
