<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\Package::find(2);
echo "Package 2 created: {$p->created_at}, updated: {$p->updated_at}\n";
foreach ($p->accommodations as $a) {
    echo "  Acc {$a->id} created: {$a->created_at}, updated: {$a->updated_at}, in: {$a->check_in}\n";
}
