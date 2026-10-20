<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Swissôtel & Pullman Zamzam Vendor',
                'supplier_code' => 'SUP-001',
                'contact_person' => 'Sheikh Tariq Al-Ghamdi',
                'phone' => '+966 12 571 5555',
                'mobile' => '+966 50 123 4567',
                'email' => 'reservations@zamzam-supplier.sa',
                'service_type' => 'Hotels & Accommodation',
                'country' => 'Saudi Arabia',
                'city' => 'Makkah',
                'address' => 'Abraj Al Bait Complex, King Abdulaziz Endowment, Makkah',
                'tax_number' => '300123456700003',
                'commercial_reg_no' => '4031098765',
                'currency' => 'SAR',
                'status' => 'active',
            ],
            [
                'name' => 'Elaf Group of Hotels & Services',
                'supplier_code' => 'SUP-002',
                'contact_person' => 'Mr. Ziyad Al-Harbi',
                'phone' => '+966 12 555 1000',
                'mobile' => '+966 55 987 6543',
                'email' => 'sales@elaf-hotels.sa',
                'service_type' => 'Hotels & Accommodation',
                'country' => 'Saudi Arabia',
                'city' => 'Makkah',
                'address' => 'Ajyad Street, Near Haram, Makkah',
                'tax_number' => '300765432100003',
                'commercial_reg_no' => '4031054321',
                'currency' => 'SAR',
                'status' => 'active',
            ],
            [
                'name' => 'Rawabi Al Bait Hotel Allotments',
                'supplier_code' => 'SUP-003',
                'contact_person' => 'Ahmed Mansoor',
                'phone' => '+966 12 534 8888',
                'mobile' => '+966 54 321 0987',
                'email' => 'allotments@rawabialbait.com',
                'service_type' => 'Hotels & Accommodation',
                'country' => 'Saudi Arabia',
                'city' => 'Makkah',
                'address' => 'Ibrahim Al Khalil Road, Makkah Al Mukarramah',
                'tax_number' => '300998877600003',
                'commercial_reg_no' => '4031088776',
                'currency' => 'SAR',
                'status' => 'active',
            ],
            [
                'name' => 'Dallah Taibah & Nusk Suppliers (Madinah)',
                'supplier_code' => 'SUP-004',
                'contact_person' => 'Eng. Fahad Al-Madani',
                'phone' => '+966 14 829 0000',
                'mobile' => '+966 56 456 7890',
                'email' => 'contact@dallah-madinah.sa',
                'service_type' => 'Hotels & Accommodation',
                'country' => 'Saudi Arabia',
                'city' => 'Madinah',
                'address' => 'Northern Central Area, Near Prophet\'s Mosque, Madinah',
                'tax_number' => '300445566700003',
                'commercial_reg_no' => '4032044556',
                'currency' => 'SAR',
                'status' => 'active',
            ],
            [
                'name' => 'Al Shohada Hotels & Pilgrims Services',
                'supplier_code' => 'SUP-005',
                'contact_person' => 'Haji Saeed Al-Otaibi',
                'phone' => '+966 12 574 4401',
                'mobile' => '+966 50 678 1234',
                'email' => 'info@alshohada-hajj.com',
                'service_type' => 'Hotels & Accommodation',
                'country' => 'Saudi Arabia',
                'city' => 'Makkah',
                'address' => 'Ajyad District, Makkah',
                'tax_number' => '300112233400003',
                'commercial_reg_no' => '4031011223',
                'currency' => 'SAR',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(
                ['name' => $sup['name']],
                $sup
            );
        }
    }
}
