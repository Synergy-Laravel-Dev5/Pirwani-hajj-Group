<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$b172 = \App\Models\Booking::with(['client', 'persons'])->find(172);
echo "Booking 172:\n";
echo "  Client: " . ($b172->client ? $b172->client->name : 'No client') . "\n";
echo "  Persons:\n";
foreach ($b172->persons as $p) {
    echo "    {$p->given_name} {$p->sur_name}, Passport: {$p->passport_number}\n";
}
