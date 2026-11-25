<?php
$lines = array_slice(file('resources/views/hotel_sales/show.blade.php'), 390, 25);
foreach ($lines as $i => $line) {
    echo ($i + 391) . ": " . bin2hex(substr($line, 0, 30)) . " -> " . $line;
}
