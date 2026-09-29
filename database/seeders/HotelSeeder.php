<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            ['name' => 'Fairmont Makkah', 'hotel_number' => 'HTL-001', 'code' => 'FRM-MAK', 'place' => 'Makkah', 'status' => 'active'],
            ['name' => 'Dar Al Tawhid Makkah', 'hotel_number' => 'HTL-002', 'code' => 'DAT-MAK', 'place' => 'Makkah', 'status' => 'active'],
            ['name' => 'Anjum Makkah', 'hotel_number' => 'HTL-003', 'code' => 'ANJ-MAK', 'place' => 'Makkah', 'status' => 'active'],
            ['name' => 'Pullman ZamZam Makkah', 'hotel_number' => 'HTL-004', 'code' => 'PUL-MAK', 'place' => 'Makkah', 'status' => 'active'],
            ['name' => 'Swissôtel Makkah', 'hotel_number' => 'HTL-005', 'code' => 'SWS-MAK', 'place' => 'Makkah', 'status' => 'active'],
            ['name' => 'Shaza Madinah', 'hotel_number' => 'HTL-006', 'code' => 'SHZ-MED', 'place' => 'Medinah', 'status' => 'active'],
            ['name' => 'Dar Al Taqwa Madinah', 'hotel_number' => 'HTL-007', 'code' => 'DAT-MED', 'place' => 'Medinah', 'status' => 'active'],
            ['name' => 'Pullman Zamzam Madinah', 'hotel_number' => 'HTL-008', 'code' => 'PUL-MED', 'place' => 'Medinah', 'status' => 'active'],
            ['name' => 'Hilton Madinah', 'hotel_number' => 'HTL-009', 'code' => 'HLT-MED', 'place' => 'Medinah', 'status' => 'active'],
        ];

        foreach ($hotels as $data) {
            Hotel::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
