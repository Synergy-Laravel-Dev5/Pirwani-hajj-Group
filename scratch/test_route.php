<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Find an admin user to authenticate
$user = User::first();
auth()->login($user);

$req = Request::create('/admin/hotel-sales/pdf/3', 'GET');
$res = $app->handle($req);

echo "Status Code: " . $res->getStatusCode() . "\n";
echo "Content-Type: " . $res->headers->get('content-type') . "\n";
echo "Content-Disposition: " . $res->headers->get('content-disposition') . "\n";
echo "Content Length: " . strlen($res->getContent()) . "\n";
