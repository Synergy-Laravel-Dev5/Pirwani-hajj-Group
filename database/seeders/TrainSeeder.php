<?php

namespace Database\Seeders;

use App\Models\Train;
use Illuminate\Database\Seeder;

class TrainSeeder extends Seeder
{
    public function run(): void
    {
        $trains = [
            ['train_name' => 'Haramain High Speed Railway (HHR)', 'train_code' => 'HHR-01', 'status' => 'active'],
            ['train_name' => 'Saudi Arabia Railways (SAR)', 'train_code' => 'SAR-01', 'status' => 'active'],
            ['train_name' => 'Makkah-Madinah Bullet Train', 'train_code' => 'BT-01', 'status' => 'active'],
        ];

        foreach ($trains as $data) {
            Train::firstOrCreate(['train_name' => $data['train_name']], $data);
        }
    }
}
