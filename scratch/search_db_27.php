<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
$dbName = \Illuminate\Support\Facades\DB::getDatabaseName();
$key = "Tables_in_" . $dbName;

foreach ($tables as $t) {
    $tableName = $t->$key;
    try {
        $rows = \Illuminate\Support\Facades\DB::table($tableName)->get();
        foreach ($rows as $row) {
            $json = json_encode($row);
            if (stripos($json, '2027-04-27') !== false || stripos($json, '27 APR') !== false || stripos($json, '27-APR') !== false) {
                echo "Table: $tableName, Row: $json\n";
            }
        }
    } catch (\Throwable $e) {}
}
echo "Done search.\n";
