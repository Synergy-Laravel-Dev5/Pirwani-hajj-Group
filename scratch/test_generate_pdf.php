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

echo "Logo base64 length: " . strlen($logoBase64) . "\n";

// Test rendering HTML
$html = '<html><head><style>body { font-family: DejaVu Sans; }</style></head><body><h1>Testing PDF for Invoice ' . $sale->invoice_number . '</h1></body></html>';
$pdf = Pdf::loadHTML($html);
$pdf->setPaper('A4', 'portrait');
$output = $pdf->output();

echo "PDF generated successfully, size: " . strlen($output) . " bytes\n";
