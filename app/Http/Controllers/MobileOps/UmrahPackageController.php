<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Package;
use App\Models\PackageAccommodation;
use App\Models\PackageTerm;
use App\Models\PackageTransport;
use App\Models\PackageTransportFlight;
use App\Models\VisaCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UmrahPackageController extends Controller
{
    protected array $durationOptions = [
        '7 Days',
        '10 Days',
        '14 Days',
        '15 Days',
        '20 Days',
        '21 Days',
        '28 Days',
        '30 Days',
    ];

    protected array $allCountries = [
        'Afghanistan',
        'Albania',
        'Algeria',
        'Andorra',
        'Angola',
        'Argentina',
        'Armenia',
        'Australia',
        'Austria',
        'Azerbaijan',
        'Bahrain',
        'Bangladesh',
        'Belgium',
        'Bosnia and Herzegovina',
        'Brazil',
        'Canada',
        'China',
        'Cyprus',
        'Czech Republic',
        'Denmark',
        'Egypt',
        'Finland',
        'France',
        'Georgia',
        'Germany',
        'Greece',
        'Hungary',
        'India',
        'Indonesia',
        'Iran',
        'Iraq',
        'Ireland',
        'Italy',
        'Japan',
        'Jordan',
        'Kazakhstan',
        'Kuwait',
        'Lebanon',
        'Malaysia',
        'Maldives',
        'Morocco',
        'Netherlands',
        'New Zealand',
        'Norway',
        'Oman',
        'Pakistan',
        'Palestine',
        'Philippines',
        'Poland',
        'Portugal',
        'Qatar',
        'Romania',
        'Russia',
        'Saudi Arabia',
        'Singapore',
        'South Africa',
        'Spain',
        'Sri Lanka',
        'Sweden',
        'Switzerland',
        'Syria',
        'Thailand',
        'Tunisia',
        'Turkey',
        'Ukraine',
        'United Arab Emirates',
        'United Kingdom',
        'United States',
        'Uzbekistan',
        'Vietnam',
        'Yemen'
    ];

    protected array $mealPlans = [
        'Daily Breakfast',
        'Full Board',
        'Half Board',
        'Room Only (RO)',
        'Bed & Breakfast',
    ];

    protected array $flightTypes = [
        'Direct Flight',
        'Connecting Flight',
        'Ticket Included',
        'Ticket Not Included',
    ];

    protected array $hotelCategories = [
        '5 Star',
        '4 Star',
        '3 Star',
        '2 Star',
        'Economy / Standard',
        'Building / Apartment',
    ];

    /**
     * Display listing (supports active and trashed tabs).
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $tab = $request->get('tab', 'active'); // 'active' or 'trash'

        $query = Package::where('package_type', 'umrah')
            ->with(['accommodations', 'transportFlights']);

        if ($tab === 'trash') {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('package_title', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('subtitle', 'like', "%{$term}%")
                    ->orWhere('package_code', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%");
            });
        }

        $packages = $query->latest('id')->paginate(15)->withQueryString();

        $activeCount = Package::where('package_type', 'umrah')->count();
        $trashCount  = Package::onlyTrashed()->where('package_type', 'umrah')->count();

        return view('mobile-ops.umrah-packages.index', compact('packages', 'search', 'tab', 'activeCount', 'trashCount'));
    }

    public function create()
    {
        $allHotels = Hotel::orderBy('name')->get();
        $makkahHotels = $allHotels->filter(fn($h) => strtolower(trim($h->place ?? '')) === 'makkah');
        $madinahHotels = $allHotels->filter(fn($h) => in_array(strtolower(trim($h->place ?? '')), ['medinah', 'madinah']));

        $dbCountries = VisaCountry::where('is_active', true)->pluck('country_name')->toArray();
        $countriesList = array_unique(array_merge($dbCountries, $this->allCountries));
        sort($countriesList);

        $durationOptions = $this->durationOptions;
        $mealPlans = $this->mealPlans;
        $flightTypes = $this->flightTypes;
        $hotelCategories = $this->hotelCategories;

        return view('mobile-ops.umrah-packages.create', compact(
            'allHotels',
            'makkahHotels',
            'madinahHotels',
            'countriesList',
            'durationOptions',
            'mealPlans',
            'flightTypes',
            'hotelCategories'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_title'          => 'required|string|max:255',
            'subtitle'               => 'nullable|string|max:255',
            'package_code'           => 'nullable|string|max:50',
            'status'                 => 'required|in:active,inactive,draft',
            'is_group_package'       => 'nullable|boolean',
            'visa_status'            => 'required|in:included,excluded',
            'is_dual_country'        => 'nullable|boolean',
            'additional_destinations' => 'nullable|array',
            'valid_from'             => 'nullable|date',
            'valid_until'            => 'nullable|date',
            'price_sharing'          => 'nullable|numeric|min:0',
            'price_quad'             => 'nullable|numeric|min:0',
            'price_triple'           => 'nullable|numeric|min:0',
            'price_double'           => 'nullable|numeric|min:0',
            'duration_pricing'       => 'nullable|array',
            'package_image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $request->hasFile('package_image')
                ? $request->file('package_image')->store('packages/umrah', 'public')
                : null;

            $durationPricingOptions = [];
            if ($request->has('duration_pricing') && is_array($request->duration_pricing)) {
                foreach ($request->duration_pricing as $duration => $matrix) {
                    if (!empty($matrix['enabled'])) {
                        $durationPricingOptions[] = [
                            'duration' => $duration,
                            'sharing'  => isset($matrix['sharing']) ? (float)$matrix['sharing'] : 0,
                            'quad'     => isset($matrix['quad']) ? (float)$matrix['quad'] : 0,
                            'triple'   => isset($matrix['triple']) ? (float)$matrix['triple'] : 0,
                            'double'   => isset($matrix['double']) ? (float)$matrix['double'] : 0,
                        ];
                    }
                }
            }

            $additionalDestinations = [];
            if ($request->has('additional_destinations') && is_array($request->additional_destinations)) {
                foreach ($request->additional_destinations as $dest) {
                    if (!empty($dest['country'])) {
                        $additionalDestinations[] = [
                            'country'        => $dest['country'],
                            'hotel_name'     => $dest['hotel_name'] ?? null,
                            'hotel_distance' => $dest['hotel_distance'] ?? null,
                            'hotel_period'   => $dest['hotel_period'] ?? null,
                            'hotel_meal_plan' => $dest['hotel_meal_plan'] ?? null,
                            'hotel_category' => $dest['hotel_category'] ?? null,
                        ];
                    }
                }
            }

            $package = Package::create([
                'package_type'             => 'umrah',
                'package_title'            => $request->package_title,
                'name'                     => $request->package_title,
                'subtitle'                 => $request->subtitle,
                'package_code'             => $request->package_code,
                'code'                     => $request->package_code,
                'status'                   => $request->status ?? 'active',
                'is_group_package'         => (bool)$request->is_group_package,
                'visa_status'              => $request->visa_status ?? 'included',
                'is_dual_country'          => (bool)$request->is_dual_country,
                'additional_destinations'  => $additionalDestinations,
                'valid_from'               => $request->valid_from,
                'valid_until'              => $request->valid_until,
                'price_sharing'            => (float)($request->price_sharing ?? 0),
                'price_quad'               => (float)($request->price_quad ?? 0),
                'price_triple'             => (float)($request->price_triple ?? 0),
                'price_double'             => (float)($request->price_double ?? 0),
                'duration_pricing_options' => $durationPricingOptions,
                'key_features'             => $request->key_features,
                'inclusions'               => $request->inclusions,
                'exclusions'               => $request->exclusions,
                'requirements_text'        => $request->requirements_text,
                'package_image'            => $imagePath,
            ]);

            if ($request->filled('makkah_hotel') || $request->filled('makkah_distance')) {
                PackageAccommodation::create([
                    'package_id'         => $package->id,
                    'place'              => 'makkah',
                    'hotel'              => $request->makkah_hotel,
                    'distance'           => is_numeric($request->makkah_distance) ? (int)$request->makkah_distance : null,
                    'note'               => $request->makkah_distance,
                    'days'               => is_numeric($request->makkah_period) ? (int)$request->makkah_period : null,
                    'food_package'       => $request->makkah_meal_plan,
                    'accommodation_type' => $request->makkah_room_category,
                ]);
            }

            if ($request->filled('madinah_hotel') || $request->filled('madinah_distance')) {
                PackageAccommodation::create([
                    'package_id'         => $package->id,
                    'place'              => 'madinah',
                    'hotel'              => $request->madinah_hotel,
                    'distance'           => is_numeric($request->madinah_distance) ? (int)$request->madinah_distance : null,
                    'note'               => $request->madinah_distance,
                    'days'               => is_numeric($request->madinah_period) ? (int)$request->madinah_period : null,
                    'food_package'       => $request->madinah_meal_plan,
                    'accommodation_type' => $request->madinah_room_category,
                ]);
            }

            foreach ($additionalDestinations as $dest) {
                if (!empty($dest['hotel_name']) || !empty($dest['country'])) {
                    PackageAccommodation::create([
                        'package_id'         => $package->id,
                        'place'              => $dest['country'],
                        'hotel'              => $dest['hotel_name'] ?? null,
                        'note'               => $dest['hotel_distance'] ?? null,
                        'food_package'       => $dest['hotel_meal_plan'] ?? null,
                        'accommodation_type' => $dest['hotel_category'] ?? null,
                    ]);
                }
            }

            if ($request->filled('outbound_airline') || $request->filled('outbound_origin') || $request->filled('outbound_destination') || $request->filled('outbound_flight_type')) {
                PackageTransportFlight::create([
                    'package_id'   => $package->id,
                    'airline'      => $request->outbound_airline,
                    'flight_class' => $request->outbound_flight_type,
                    'origin'       => $request->outbound_origin,
                    'destination'  => $request->outbound_destination,
                    'flight_no'    => $request->outbound_notes,
                    'is_preferred' => 1,
                ]);
            }

            if ($request->filled('inbound_airline') || $request->filled('inbound_origin') || $request->filled('inbound_destination') || $request->filled('inbound_flight_type')) {
                PackageTransportFlight::create([
                    'package_id'   => $package->id,
                    'airline'      => $request->inbound_airline,
                    'flight_class' => $request->inbound_flight_type,
                    'origin'       => $request->inbound_origin,
                    'destination'  => $request->inbound_destination,
                    'flight_no'    => $request->inbound_notes,
                    'is_preferred' => 0,
                ]);
            }

            if ($request->filled('terms_conditions')) {
                PackageTerm::create([
                    'package_id' => $package->id,
                    'content'    => $request->terms_conditions,
                ]);
            }

            if ($request->filled('transport_plan')) {
                PackageTransport::create([
                    'package_id' => $package->id,
                    'route'      => $request->transport_plan,
                    'type'       => 'bus',
                ]);
            }

            DB::commit();

            return redirect()->route('mobile-ops.umrah-packages.index')
                ->with('success', 'Umrah Package created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating Umrah Package: ' . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $package = Package::where('package_type', 'umrah')
            ->with(['accommodations', 'transportFlights', 'terms', 'transports'])
            ->findOrFail($id);

        $allHotels = Hotel::orderBy('name')->get();
        $makkahHotels = $allHotels->filter(fn($h) => strtolower(trim($h->place ?? '')) === 'makkah');
        $madinahHotels = $allHotels->filter(fn($h) => in_array(strtolower(trim($h->place ?? '')), ['medinah', 'madinah']));

        $dbCountries = VisaCountry::where('is_active', true)->pluck('country_name')->toArray();
        $countriesList = array_unique(array_merge($dbCountries, $this->allCountries));
        sort($countriesList);

        $durationOptions = $this->durationOptions;
        $mealPlans = $this->mealPlans;
        $flightTypes = $this->flightTypes;
        $hotelCategories = $this->hotelCategories;

        $makkahAcc = $package->accommodations->firstWhere('place', 'makkah');
        $madinahAcc = $package->accommodations->firstWhere('place', 'madinah');

        $outboundFlight = $package->transportFlights->firstWhere('is_preferred', 1)
            ?? $package->transportFlights->first();
        $inboundFlight = $package->transportFlights->where('id', '!=', optional($outboundFlight)->id)->first();

        $terms = $package->terms;
        $transport = $package->transports->first();

        $durationPricingIndexed = [];
        if (!empty($package->duration_pricing_options) && is_array($package->duration_pricing_options)) {
            foreach ($package->duration_pricing_options as $opt) {
                if (isset($opt['duration'])) {
                    $durationPricingIndexed[$opt['duration']] = $opt;
                }
            }
        }

        return view('mobile-ops.umrah-packages.edit', compact(
            'package',
            'allHotels',
            'makkahHotels',
            'madinahHotels',
            'countriesList',
            'durationOptions',
            'mealPlans',
            'flightTypes',
            'hotelCategories',
            'makkahAcc',
            'madinahAcc',
            'outboundFlight',
            'inboundFlight',
            'terms',
            'transport',
            'durationPricingIndexed'
        ));
    }

    public function update(Request $request, int $id)
    {
        $package = Package::where('package_type', 'umrah')->findOrFail($id);

        $request->validate([
            'package_title'          => 'required|string|max:255',
            'subtitle'               => 'nullable|string|max:255',
            'package_code'           => 'nullable|string|max:50',
            'status'                 => 'required|in:active,inactive,draft',
            'is_group_package'       => 'nullable|boolean',
            'visa_status'            => 'required|in:included,excluded',
            'is_dual_country'        => 'nullable|boolean',
            'additional_destinations' => 'nullable|array',
            'valid_from'             => 'nullable|date',
            'valid_until'            => 'nullable|date',
            'price_sharing'          => 'nullable|numeric|min:0',
            'price_quad'             => 'nullable|numeric|min:0',
            'price_triple'           => 'nullable|numeric|min:0',
            'price_double'           => 'nullable|numeric|min:0',
            'duration_pricing'       => 'nullable|array',
            'package_image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $package->package_image;
            if ($request->hasFile('package_image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('package_image')->store('packages/umrah', 'public');
            }

            $durationPricingOptions = [];
            if ($request->has('duration_pricing') && is_array($request->duration_pricing)) {
                foreach ($request->duration_pricing as $duration => $matrix) {
                    if (!empty($matrix['enabled'])) {
                        $durationPricingOptions[] = [
                            'duration' => $duration,
                            'sharing'  => isset($matrix['sharing']) ? (float)$matrix['sharing'] : 0,
                            'quad'     => isset($matrix['quad']) ? (float)$matrix['quad'] : 0,
                            'triple'   => isset($matrix['triple']) ? (float)$matrix['triple'] : 0,
                            'double'   => isset($matrix['double']) ? (float)$matrix['double'] : 0,
                        ];
                    }
                }
            }

            $additionalDestinations = [];
            if ($request->has('additional_destinations') && is_array($request->additional_destinations)) {
                foreach ($request->additional_destinations as $dest) {
                    if (!empty($dest['country'])) {
                        $additionalDestinations[] = [
                            'country'        => $dest['country'],
                            'hotel_name'     => $dest['hotel_name'] ?? null,
                            'hotel_distance' => $dest['hotel_distance'] ?? null,
                            'hotel_period'   => $dest['hotel_period'] ?? null,
                            'hotel_meal_plan' => $dest['hotel_meal_plan'] ?? null,
                            'hotel_category' => $dest['hotel_category'] ?? null,
                        ];
                    }
                }
            }

            $package->update([
                'package_title'            => $request->package_title,
                'name'                     => $request->package_title,
                'subtitle'                 => $request->subtitle,
                'package_code'             => $request->package_code,
                'code'                     => $request->package_code,
                'status'                   => $request->status ?? 'active',
                'is_group_package'         => (bool)$request->is_group_package,
                'visa_status'              => $request->visa_status ?? 'included',
                'is_dual_country'          => (bool)$request->is_dual_country,
                'additional_destinations'  => $additionalDestinations,
                'valid_from'               => $request->valid_from,
                'valid_until'              => $request->valid_until,
                'price_sharing'            => (float)($request->price_sharing ?? 0),
                'price_quad'               => (float)($request->price_quad ?? 0),
                'price_triple'             => (float)($request->price_triple ?? 0),
                'price_double'             => (float)($request->price_double ?? 0),
                'duration_pricing_options' => $durationPricingOptions,
                'key_features'             => $request->key_features,
                'inclusions'               => $request->inclusions,
                'exclusions'               => $request->exclusions,
                'requirements_text'        => $request->requirements_text,
                'package_image'            => $imagePath,
            ]);

            $package->accommodations()->delete();

            if ($request->filled('makkah_hotel') || $request->filled('makkah_distance')) {
                $package->accommodations()->create([
                    'place'              => 'makkah',
                    'hotel'              => $request->makkah_hotel,
                    'distance'           => is_numeric($request->makkah_distance) ? (int)$request->makkah_distance : null,
                    'note'               => $request->makkah_distance,
                    'days'               => is_numeric($request->makkah_period) ? (int)$request->makkah_period : null,
                    'food_package'       => $request->makkah_meal_plan,
                    'accommodation_type' => $request->makkah_room_category,
                ]);
            }

            if ($request->filled('madinah_hotel') || $request->filled('madinah_distance')) {
                $package->accommodations()->create([
                    'place'              => 'madinah',
                    'hotel'              => $request->madinah_hotel,
                    'distance'           => is_numeric($request->madinah_distance) ? (int)$request->madinah_distance : null,
                    'note'               => $request->madinah_distance,
                    'days'               => is_numeric($request->madinah_period) ? (int)$request->madinah_period : null,
                    'food_package'       => $request->madinah_meal_plan,
                    'accommodation_type' => $request->madinah_room_category,
                ]);
            }

            foreach ($additionalDestinations as $dest) {
                if (!empty($dest['hotel_name']) || !empty($dest['country'])) {
                    $package->accommodations()->create([
                        'place'              => $dest['country'],
                        'hotel'              => $dest['hotel_name'] ?? null,
                        'note'               => $dest['hotel_distance'] ?? null,
                        'food_package'       => $dest['hotel_meal_plan'] ?? null,
                        'accommodation_type' => $dest['hotel_category'] ?? null,
                    ]);
                }
            }

            $package->transportFlights()->delete();

            if ($request->filled('outbound_airline') || $request->filled('outbound_origin') || $request->filled('outbound_destination') || $request->filled('outbound_flight_type')) {
                PackageTransportFlight::create([
                    'package_id'   => $package->id,
                    'airline'      => $request->outbound_airline,
                    'flight_class' => $request->outbound_flight_type,
                    'origin'       => $request->outbound_origin,
                    'destination'  => $request->outbound_destination,
                    'flight_no'    => $request->outbound_notes,
                    'is_preferred' => 1,
                ]);
            }

            if ($request->filled('inbound_airline') || $request->filled('inbound_origin') || $request->filled('inbound_destination') || $request->filled('inbound_flight_type')) {
                PackageTransportFlight::create([
                    'package_id'   => $package->id,
                    'airline'      => $request->inbound_airline,
                    'flight_class' => $request->inbound_flight_type,
                    'origin'       => $request->inbound_origin,
                    'destination'  => $request->inbound_destination,
                    'flight_no'    => $request->inbound_notes,
                    'is_preferred' => 0,
                ]);
            }

            $term = $package->terms()->first();
            if ($request->filled('terms_conditions')) {
                if ($term) {
                    $term->update(['content' => $request->terms_conditions]);
                } else {
                    $package->terms()->create(['content' => $request->terms_conditions]);
                }
            } elseif ($term) {
                $term->delete();
            }

            $transport = $package->transports()->first();
            if ($request->filled('transport_plan')) {
                if ($transport) {
                    $transport->update(['route' => $request->transport_plan]);
                } else {
                    $package->transports()->create([
                        'route' => $request->transport_plan,
                        'type'  => 'bus',
                    ]);
                }
            } elseif ($transport) {
                $transport->delete();
            }

            DB::commit();

            return redirect()->route('mobile-ops.umrah-packages.index')
                ->with('success', 'Umrah Package updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating Umrah Package: ' . $e->getMessage());
        }
    }
    public function destroy(int $id)
    {
        $package = Package::where('package_type', 'umrah')->findOrFail($id);

        try {
            // Do NOT delete child relations.
            // Soft-deleting the parent package automatically hides the whole package and its children.
            $package->delete();

            return redirect()->route('mobile-ops.umrah-packages.index')
                ->with('success', 'Umrah Package moved to trash.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error moving package to trash: ' . $e->getMessage());
        }
    }
    /**
     * Display a dedicated view for soft-deleted Umrah packages.
     */
    public function trash(Request $request)
    {
        $search = $request->get('search');

        $query = Package::onlyTrashed()
            ->where('package_type', 'umrah')
            ->with(['accommodations', 'transportFlights'])
            ->latest('deleted_at');

        if (!empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('package_title', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('package_code', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%");
            });
        }

        $packages = $query->paginate(15)->withQueryString();
        $trashCount = Package::onlyTrashed()->where('package_type', 'umrah')->count();

        return view('mobile-ops.umrah-packages.trash', compact('packages', 'search', 'trashCount'));
    }

    /**
     * Restore a soft-deleted Umrah package and its child records.
     */
    public function restore(int $id)
    {
        $package = Package::onlyTrashed()
            ->where('package_type', 'umrah')
            ->findOrFail($id);

        try {
            // Simply restore the parent package. All child accommodations and flights are intact.
            $package->restore();

            return redirect()->route('mobile-ops.umrah-packages.trash')
                ->with('success', "'{$package->package_title}' has been restored successfully.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error restoring package: ' . $e->getMessage());
        }
    }
}
