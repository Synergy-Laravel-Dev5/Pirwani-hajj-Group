<?php

namespace Database\Seeders;

use App\Models\Airline;
use Illuminate\Database\Seeder;

class AirlineSeeder extends Seeder
{
    public function run(): void
    {
        $airlines = [
            ['name' => 'Saudi Arabian Airlines (Saudia)', 'code' => 'SV', 'iata_code' => 'SV', 'icao_code' => 'SVA', 'country' => 'Saudi Arabia', 'status' => 'active'],
            ['name' => 'Pakistan International Airlines (PIA)', 'code' => 'PK', 'iata_code' => 'PK', 'icao_code' => 'PIA', 'country' => 'Pakistan', 'status' => 'active'],
            ['name' => 'Flynas', 'code' => 'XY', 'iata_code' => 'XY', 'icao_code' => 'KNE', 'country' => 'Saudi Arabia', 'status' => 'active'],
            ['name' => 'Emirates Airlines', 'code' => 'EK', 'iata_code' => 'EK', 'icao_code' => 'UAE', 'country' => 'UAE', 'status' => 'active'],
            ['name' => 'Qatar Airways', 'code' => 'QR', 'iata_code' => 'QR', 'icao_code' => 'QTR', 'country' => 'Qatar', 'status' => 'active'],
            ['name' => 'Airblue', 'code' => 'PA', 'iata_code' => 'PA', 'icao_code' => 'ABQ', 'country' => 'Pakistan', 'status' => 'active'],
            ['name' => 'Flydubai', 'code' => 'FZ', 'iata_code' => 'FZ', 'icao_code' => 'FDB', 'country' => 'UAE', 'status' => 'active'],
        ];

        foreach ($airlines as $data) {
            Airline::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
