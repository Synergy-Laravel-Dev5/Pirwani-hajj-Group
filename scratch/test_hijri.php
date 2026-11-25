<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dates = ['2027-04-26', '2027-04-27', '2027-04-28', '2027-04-29'];

foreach ($dates as $d) {
    echo "Date: $d\n";
    echo "  HotelSale::gregorianToHijri: " . \App\Models\HotelSale::gregorianToHijri($d) . "\n";
    
    if (class_exists('IntlDateFormatter')) {
        $formatter = new IntlDateFormatter(
            'en_US@calendar=islamic-umalqura',
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            'UTC',
            IntlDateFormatter::TRADITIONAL,
            'd-MMMM-y GGGG'
        );
        $time = strtotime($d . ' 12:00:00 UTC');
        echo "  IntlDateFormatter (Umm al Qura): " . $formatter->format($time) . "\n";

        $formatterCivil = new IntlDateFormatter(
            'en_US@calendar=islamic-civil',
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            'UTC',
            IntlDateFormatter::TRADITIONAL,
            'd-MMMM-y GGGG'
        );
        echo "  IntlDateFormatter (Islamic Civil): " . $formatterCivil->format($time) . "\n";
    }
}
