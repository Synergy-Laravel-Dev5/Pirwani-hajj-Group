<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

$user = User::first();
auth()->login($user);

$req = Request::create('/admin/hotel-sales/pdf/3?stream=1', 'GET');
$res = $app->handle($req);

echo "Stream Status Code: " . $res->getStatusCode() . "\n";
echo "Stream Content-Type: " . $res->headers->get('content-type') . "\n";
echo "Stream Content-Disposition: " . $res->headers->get('content-disposition') . "\n";
