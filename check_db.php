<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "package_accommodations COLUMNS:\n";
print_r(Illuminate\Support\Facades\Schema::getColumnListing('package_accommodations'));

echo "\npackages COLUMNS:\n";
print_r(Illuminate\Support\Facades\Schema::getColumnListing('packages'));
