<?php

namespace Database\Seeders;

use App\Models\VisaCountry;
use App\Models\VisaType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VisaCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'country_name' => 'United Arab Emirates',
                'country_code' => 'AE',
                'b2b_rate_raw' => 'PKR 15,000/-',
                'visa_fee_raw' => '$90',
                'process_time' => '5-7 working days',
                'notes'        => '30 Days Tourist Visa',
                'requirements' => [
                    'Valid Passport',
                    'Picture with White Background',
                    'CNIC Front & Back',
                    'Bank Statement',
                    'Other Documents',
                ],
            ],
            [
                'country_name' => 'Saudi Arabia',
                'country_code' => 'SA',
                'b2b_rate_raw' => 'PKR 12,000/-',
                'visa_fee_raw' => '$185 (USD)',
                'process_time' => '15-30 Days',
                'notes'        => 'Standard Umrah / Tourist Visa',
                'requirements' => [
                    'Valid Passport (6 months validity)',
                    'Passport Size Photo (White Background)',
                    'CNIC Front & Back',
                    'Polio / Meningitis Vaccine Certificate',
                ],
            ],
            [
                'country_name' => 'Turkey',
                'country_code' => 'TR',
                'b2b_rate_raw' => 'PKR 14,000/-',
                'visa_fee_raw' => '$60',
                'process_time' => '15-30 Days',
                'notes'        => 'E-Visa requires valid US, UK, or Schengen Visa',
                'requirements' => [
                    'Valid Passport',
                    'Valid US, UK, or Schengen Visa',
                    'CNIC Front & Back',
                    'Return Flight Ticket',
                ],
            ],
            [
                'country_name' => 'United Kingdom',
                'country_code' => 'GB',
                'b2b_rate_raw' => 'PKR 25,000/-',
                'visa_fee_raw' => 'MIN: £115',
                'process_time' => '15-30 Days',
                'notes'        => 'Standard Visitor Visa (Fee: MIN: £115)',
                'requirements' => [
                    'Valid Passport',
                    '6 Months Bank Statement',
                    'Account Maintenance Certificate',
                    'CNIC Front & Back',
                    'Employment / Business Letter',
                ],
            ],
            [
                'country_name' => 'United States',
                'country_code' => 'US',
                'b2b_rate_raw' => 'PKR 30,000/-',
                'visa_fee_raw' => '$185 (USD)',
                'process_time' => '15-30 Days',
                'notes'        => 'B1/B2 Tourist & Business Visa ($185 USD)',
                'requirements' => [
                    'Valid Passport',
                    'DS-160 Confirmation Page',
                    'Appointment Confirmation',
                    'Bank Statement & Tax Returns',
                ],
            ],
            [
                'country_name' => 'Malaysia',
                'country_code' => 'MY',
                'b2b_rate_raw' => 'PKR 8,000/-',
                'visa_fee_raw' => '$45',
                'process_time' => '5-7 working days',
                'notes'        => 'Single Entry Tourist E-Visa',
                'requirements' => [
                    'Valid Passport',
                    'White Background Photo',
                    'CNIC Front & Back',
                    'Return Ticket & Hotel Booking',
                ],
            ],
            [
                'country_name' => 'Thailand',
                'country_code' => 'TH',
                'b2b_rate_raw' => 'PKR 10,000/-',
                'visa_fee_raw' => '$40',
                'process_time' => '7-10 working days',
                'notes'        => 'Tourist Visa (Single Entry)',
                'requirements' => [
                    'Valid Passport',
                    'Two Passport-sized Photos (White background)',
                    'Bank Statement (Last 6 Months)',
                    'CNIC Front & Back',
                ],
            ],
            [
                'country_name' => 'Singapore',
                'country_code' => 'SG',
                'b2b_rate_raw' => 'PKR 12,000/-',
                'visa_fee_raw' => '$35',
                'process_time' => '5-7 working days',
                'notes'        => 'Tourist E-Visa (30 Days)',
                'requirements' => [
                    'Valid Passport',
                    'White Background Photo',
                    'CNIC Front & Back',
                    'Bank Statement',
                ],
            ],
            [
                'country_name' => 'Azerbaijan',
                'country_code' => 'AZ',
                'b2b_rate_raw' => 'PKR 6,000/-',
                'visa_fee_raw' => '$26',
                'process_time' => '3-5 working days',
                'notes'        => 'Standard ASAN E-Visa',
                'requirements' => [
                    'Passport First Page Copy (Clear scan)',
                    'CNIC Front & Back',
                ],
            ],
            [
                'country_name' => 'Egypt',
                'country_code' => 'EG',
                'b2b_rate_raw' => 'PKR 15,000/-',
                'visa_fee_raw' => '$25',
                'process_time' => '15-20 working days',
                'notes'        => 'Tourist Visa',
                'requirements' => [
                    'Valid Passport',
                    'CNIC Copy',
                    'Bank Statement',
                    'Passport Photos',
                ],
            ],
            [
                'country_name' => 'Oman',
                'country_code' => 'OM',
                'b2b_rate_raw' => 'PKR 8,000/-',
                'visa_fee_raw' => '$55',
                'process_time' => '3-5 working days',
                'notes'        => 'Royal Oman Police Tourist E-Visa',
                'requirements' => [
                    'Valid Passport (6 months validity)',
                    'Photograph with blue/white background',
                    'CNIC Copy',
                ],
            ],
            [
                'country_name' => 'Qatar',
                'country_code' => 'QA',
                'b2b_rate_raw' => 'PKR 8,500/-',
                'visa_fee_raw' => '$28',
                'process_time' => '3-5 working days',
                'notes'        => 'Hayya / Tourist Visa',
                'requirements' => [
                    'Valid Passport',
                    'Passport Photo',
                    'Confirmed Return Ticket',
                    'Hotel Booking via Discover Qatar (if required)',
                ],
            ],
        ];

        foreach ($countries as $item) {
            $code = strtoupper(trim($item['country_code']));

            // Parse numeric values
            $b2bRate = $this->parseRate($item['b2b_rate_raw'] ?? null);
            $parsedFee = $this->parseFee($item['visa_fee_raw'] ?? null);
            $visaFee = $parsedFee['amount'];

            // Combine fee note with existing notes if note exists
            $notes = $item['notes'] ?? null;
            if (!empty($parsedFee['note'])) {
                $notes = $notes ? ($notes . ' | ' . $parsedFee['note']) : $parsedFee['note'];
            }

            // Ensure flag is downloaded/saved locally
            $flagPath = $this->resolveCountryFlag($code);

            // Upsert country (match by country_code or country_name)
            $country = VisaCountry::where('country_code', $code)
                ->orWhere('country_name', $item['country_name'])
                ->first();

            if ($country) {
                $country->update([
                    'country_name' => $item['country_name'],
                    'country_code' => $code,
                    'country_flag' => $country->country_flag ?: $flagPath,
                    'is_active'    => true,
                ]);
            } else {
                $country = VisaCountry::create([
                    'country_name' => $item['country_name'],
                    'country_code' => $code,
                    'country_flag' => $flagPath,
                    'is_active'    => true,
                ]);
            }

            // Upsert default visa type
            VisaType::updateOrCreate(
                [
                    'visa_country_id' => $country->id,
                    'visa_name'       => 'Standard Visa',
                ],
                [
                    'b2b_rate_pkr' => $b2bRate,
                    'visa_fee_usd' => $visaFee,
                    'process_time' => $item['process_time'] ?? '15-30 Days',
                    'notes'        => $notes,
                    'requirements' => $item['requirements'] ?? [
                        'Valid Passport',
                        'Picture with White Background',
                        'CNIC Front & Back',
                        'Bank Statement',
                        'Other Documents',
                    ],
                ]
            );
        }
    }

    /**
     * Parse numeric B2B rate in PKR.
     * e.g., 'PKR 12,000/-' -> 12000.00
     */
    protected function parseRate(?string $raw): float
    {
        if (empty($raw)) {
            return 0.00;
        }

        // Remove commas and extract numbers (allowing decimals)
        $clean = str_replace(',', '', $raw);
        if (preg_match('/(\d+(?:\.\d+)?)/', $clean, $matches)) {
            return (float) $matches[1];
        }

        return 0.00;
    }

    /**
     * Parse numeric visa fee in USD.
     * Extracts numeric value and returns any currency notes.
     * e.g., '$185 (USD)' -> 185.00
     *       'MIN: £115'   -> 115.00 with note 'Original fee text: MIN: £115'
     */
    protected function parseFee(?string $raw): array
    {
        if (empty($raw)) {
            return ['amount' => 0.00, 'note' => null];
        }

        $note = null;
        if (preg_match('/[£€]|MIN:/i', $raw)) {
            $note = 'Original Fee: ' . trim($raw);
        }

        $clean = str_replace(',', '', $raw);
        if (preg_match('/(\d+(?:\.\d+)?)/', $clean, $matches)) {
            return [
                'amount' => (float) $matches[1],
                'note'   => $note,
            ];
        }

        return ['amount' => 0.00, 'note' => 'Original Fee: ' . trim($raw)];
    }

    /**
     * Auto-fetch and store flag locally in storage/app/public/visa/flags/{code}.png
     */
    protected function resolveCountryFlag(string $countryCode): ?string
    {
        $code = strtolower(trim($countryCode));
        if (strlen($code) !== 2) {
            return null;
        }

        $flagRelativePath = 'visa/flags/' . $code . '.png';

        if (Storage::disk('public')->exists($flagRelativePath)) {
            return $flagRelativePath;
        }

        try {
            $url = 'https://flagcdn.com/w320/' . $code . '.png';
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                Storage::disk('public')->put($flagRelativePath, $response->body());
                return $flagRelativePath;
            }
        } catch (\Throwable $e) {
            Log::warning("VisaCountrySeeder: Failed to fetch flag for {$countryCode}: " . $e->getMessage());
        }

        return null;
    }
}
