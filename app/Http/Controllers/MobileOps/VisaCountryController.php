<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\VisaCountry;
use App\Models\VisaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VisaCountryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = VisaCountry::with('visaTypes')->latest();

        if (!empty($search)) {
            $query->where('country_name', 'like', '%' . trim($search) . '%')
                ->orWhere('country_code', 'like', '%' . trim($search) . '%');
        }

        $countries = $query->paginate(15);
        $totalCount = VisaCountry::count();

        return view('mobile-ops.visa-countries.index', compact('countries', 'search', 'totalCount'));
    }

    public function create()
    {
        $defaultRequirements = [
            'Valid Passport',
            'Picture with White Background',
            'CNIC Front & Back',
            'Bank Statement',
            'Other Documents',
        ];

        return view('mobile-ops.visa-countries.create', compact('defaultRequirements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_name'      => 'required|string|max:150',
            'country_code'      => 'required|string|max:10',
            'monument_picture'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'country_flag'      => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'types'             => 'required|array|min:1',
            'types.*.visa_name' => 'required|string|max:150',
            'types.*.b2b_rate'  => 'required|numeric|min:0',
            'types.*.visa_fee'  => 'required|numeric|min:0',
            'types.*.process_time' => 'nullable|string|max:100',
            'types.*.notes'     => 'nullable|string',
            'types.*.requirements' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $countryCode = strtoupper(trim($request->country_code));

            $monumentPath = null;
            if ($request->hasFile('monument_picture')) {
                $monumentPath = $request->file('monument_picture')->store('visa/monuments', 'public');
            }

            $flagPath = null;
            if ($request->hasFile('country_flag')) {
                $flagPath = $request->file('country_flag')->store('visa/flags', 'public');
            } elseif (strlen($countryCode) === 2) {
                // Auto-fetch flag from FlagCDN if no file uploaded and valid 2-letter ISO code
                $flagPath = $this->fetchAndSaveCountryFlag($countryCode);
            }

            $country = VisaCountry::create([
                'country_name'     => $request->country_name,
                'country_code'     => $countryCode,
                'monument_picture' => $monumentPath,
                'country_flag'     => $flagPath,
                'is_active'        => $request->has('is_active'),
            ]);

            foreach ($request->types as $typeData) {
                // Filter out empty requirements
                $cleanRequirements = !empty($typeData['requirements'])
                    ? array_values(array_filter(array_map('trim', $typeData['requirements'])))
                    : [];

                VisaType::create([
                    'visa_country_id' => $country->id,
                    'visa_name'       => $typeData['visa_name'],
                    'b2b_rate_pkr'    => $typeData['b2b_rate'] ?? 0,
                    'visa_fee_usd'    => $typeData['visa_fee'] ?? 0,
                    'process_time'    => $typeData['process_time'] ?? null,
                    'notes'           => $typeData['notes'] ?? null,
                    'requirements'    => $cleanRequirements,
                ]);
            }

            DB::commit();
            return redirect()->route('mobile-ops.visa-countries.index')->with('success', 'Visa Country & Visa Types added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error saving visa country: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $country = VisaCountry::with('visaTypes')->findOrFail($id);
        return view('mobile-ops.visa-countries.edit', compact('country'));
    }

    public function update(Request $request, $id)
    {
        $country = VisaCountry::findOrFail($id);

        $request->validate([
            'country_name'      => 'required|string|max:150',
            'country_code'      => 'required|string|max:10',
            'monument_picture'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'country_flag'      => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'types'             => 'required|array|min:1',
            'types.*.visa_name' => 'required|string|max:150',
            'types.*.b2b_rate'  => 'required|numeric|min:0',
            'types.*.visa_fee'  => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $countryCode = strtoupper(trim($request->country_code));

            $data = [
                'country_name' => $request->country_name,
                'country_code' => $countryCode,
                'is_active'    => $request->has('is_active'),
            ];

            if ($request->hasFile('monument_picture')) {
                if ($country->monument_picture && Storage::disk('public')->exists($country->monument_picture)) {
                    Storage::disk('public')->delete($country->monument_picture);
                }
                $data['monument_picture'] = $request->file('monument_picture')->store('visa/monuments', 'public');
            }

            if ($request->hasFile('country_flag')) {
                if ($country->country_flag && Storage::disk('public')->exists($country->country_flag)) {
                    Storage::disk('public')->delete($country->country_flag);
                }
                $data['country_flag'] = $request->file('country_flag')->store('visa/flags', 'public');
            } elseif (strlen($countryCode) === 2 && (empty($country->country_flag) || $country->country_code !== $countryCode)) {
                // When country code changes or no flag exists and no custom upload provided, auto-fetch
                $fetchedFlag = $this->fetchAndSaveCountryFlag($countryCode);
                if ($fetchedFlag) {
                    if ($country->country_flag && $country->country_flag !== $fetchedFlag && Storage::disk('public')->exists($country->country_flag)) {
                        Storage::disk('public')->delete($country->country_flag);
                    }
                    $data['country_flag'] = $fetchedFlag;
                }
            }

            $country->update($data);

            // Re-sync visa types
            $country->visaTypes()->delete();
            foreach ($request->types as $typeData) {
                $cleanRequirements = !empty($typeData['requirements'])
                    ? array_values(array_filter(array_map('trim', $typeData['requirements'])))
                    : [];

                VisaType::create([
                    'visa_country_id' => $country->id,
                    'visa_name'       => $typeData['visa_name'],
                    'b2b_rate_pkr'    => $typeData['b2b_rate'] ?? 0,
                    'visa_fee_usd'    => $typeData['visa_fee'] ?? 0,
                    'process_time'    => $typeData['process_time'] ?? null,
                    'notes'           => $typeData['notes'] ?? null,
                    'requirements'    => $cleanRequirements,
                ]);
            }

            DB::commit();
            return redirect()->route('mobile-ops.visa-countries.index')->with('success', 'Visa Country updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating visa country: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $country = VisaCountry::findOrFail($id);
        $country->delete();

        return redirect()->route('mobile-ops.visa-countries.index')->with('success', 'Visa Country moved to trash successfully.');
    }

    public function trash(Request $request)
    {
        $search = $request->get('search');

        $query = VisaCountry::onlyTrashed()->withCount('visaTypes')->latest('deleted_at');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('country_name', 'like', '%' . trim($search) . '%')
                  ->orWhere('country_code', 'like', '%' . trim($search) . '%');
            });
        }

        $countries = $query->paginate(15);
        $trashCount = VisaCountry::onlyTrashed()->count();

        return view('mobile-ops.visa-countries.trash', compact('countries', 'search', 'trashCount'));
    }

    public function restore($id)
    {
        $country = VisaCountry::onlyTrashed()->findOrFail($id);
        $country->restore();

        return redirect()->route('mobile-ops.visa-countries.trash')->with('success', 'Visa Country restored successfully.');
    }

    /**
     * Helper to auto-fetch country flag image from FlagCDN and store in public storage.
     */
    protected function fetchAndSaveCountryFlag(string $countryCode): ?string
    {
        $code = strtolower(trim($countryCode));
        if (strlen($code) !== 2) {
            return null;
        }

        $flagPath = 'visa/flags/' . $code . '.png';

        // If file already exists locally, reuse it
        if (Storage::disk('public')->exists($flagPath)) {
            return $flagPath;
        }

        try {
            $url = 'https://flagcdn.com/w320/' . $code . '.png';
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                Storage::disk('public')->put($flagPath, $response->body());
                return $flagPath;
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to auto-fetch flag for country code {$countryCode}: " . $e->getMessage());
        }

        return null;
    }
}
