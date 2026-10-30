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

class InternationalPackageController extends Controller
{
    protected array $popularCountries = [
        'United Arab Emirates',
        'Saudi Arabia',
        'Turkey',
        'Malaysia',
        'Thailand',
        'Azerbaijan',
        'Singapore',
        'United Kingdom',
        'United States',
        'Canada',
        'Georgia',
        'Egypt',
        'Maldives',
        'Sri Lanka',
        'Indonesia',
        'Uzbekistan',
        'Switzerland',
        'France',
        'Italy',
        'Spain',
        'Germany',
        'Australia',
        'Qatar',
        'Oman',
        'Bahrain',
        'Jordan',
        'Morocco',
        'Japan',
        'China',
        'Vietnam',
        'Phu Quoc (Vietnam)',
    ];

    protected array $departureCities = [
        'Karachi',
        'Lahore',
        'Islamabad',
        'Peshawar',
        'Multan',
        'Sialkot',
        'Faisalabad',
        'Quetta'
    ];

    protected array $mealPlans = [
        'Daily Breakfast',
        'Room Only (RO)',
        'Bed & Breakfast (BB)',
        'Half Board (Breakfast & Dinner)',
        'Full Board (All Meals)',
        'All Inclusive',
    ];

    protected array $flightTypes = [
        'Direct Flight',
        'Connecting Flight',
        'Ticket Included',
        'Ticket Not Included',
        'Economy Class',
        'Premium Economy',
        'Business Class',
        'First Class',
    ];

    protected array $hotelCategories = [
        '5 Star',
        '4 Star',
        '3 Star',
        '2 Star',
        'Economy / Standard',
        'Resort / Villa',
        'Boutique Hotel',
        'Standard Apartment',
    ];

    protected array $groundFleets = [
        'Private VIP Sedan (Camry/Mercedes)',
        'Luxury SUV / Prado',
        'Executive Mercedes Sprinter',
        'Air-Conditioned Tourist Coach',
        'Shared Airport Shuttle Bus',
        'Speedboat / Ferry Transfer',
        'Public Transit Card Included',
        'Not Included',
    ];

    /**
     * Listing: Filter packages by category ('international') with search and quick trash links.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Package::where('package_type', 'international')
            ->with(['accommodations', 'transportFlights', 'transports']);

        if (!empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('package_title', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('subtitle', 'like', "%{$term}%")
                    ->orWhere('package_code', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('departure_city', 'like', "%{$term}%")
                    ->orWhere('destination_city', 'like', "%{$term}%")
                    ->orWhere('destination_country', 'like', "%{$term}%");
            });
        }

        $packages = $query->latest('id')->paginate(15)->withQueryString();

        $activeCount = Package::where('package_type', 'international')->count();
        $trashCount  = Package::onlyTrashed()->where('package_type', 'international')->count();

        return view('mobile-ops.international-packages.index', compact('packages', 'search', 'activeCount', 'trashCount'));
    }

    /**
     * Show form for creating a new international package.
     */
    public function create()
    {
        $allHotels = Hotel::orderBy('name')->get();

        $dbCountries = VisaCountry::where('is_active', true)->pluck('country_name')->toArray();
        $countriesList = array_unique(array_merge($dbCountries, $this->popularCountries));
        sort($countriesList);

        $departureCities = $this->departureCities;
        $mealPlans = $this->mealPlans;
        $flightTypes = $this->flightTypes;
        $hotelCategories = $this->hotelCategories;
        $groundFleets = $this->groundFleets;

        return view('mobile-ops.international-packages.create', compact(
            'allHotels',
            'countriesList',
            'departureCities',
            'mealPlans',
            'flightTypes',
            'hotelCategories',
            'groundFleets'
        ));
    }

    /**
     * Store newly created international package in database inside a transaction.
     */
    public function store(Request $request)
    {
        $isPriceOnRequest = $request->boolean('is_price_on_request');

        $priceRule = $isPriceOnRequest ? 'nullable|numeric|min:0' : 'nullable|numeric|min:0';

        $request->validate([
            'package_title'           => 'required|string|max:255',
            'subtitle'                => 'nullable|string|max:255',
            'sightseeing_summary'     => 'nullable|string|max:255',
            'package_code'            => 'nullable|string|max:50',
            'status'                  => 'required|in:active,inactive,draft',
            'is_price_on_request'     => 'nullable|boolean',
            'is_group_package'        => 'nullable|boolean',
            'visa_status'             => 'required|in:included,excluded',
            'is_dual_country'         => 'nullable|boolean',
            'is_multi_city'           => 'nullable|boolean',
            'departure_city'          => 'nullable|string|max:100',
            'destination_city'        => 'nullable|string|max:100',
            'destination_country'     => 'required|string|max:100',
            'additional_destinations' => 'nullable|array',
            'stay_nights'             => 'nullable|integer|min:0',
            'duration_nights'         => 'nullable|integer|min:0',
            'primary_hotel'           => 'nullable|string|max:255',
            'hotel_star_rating'       => 'nullable|string|max:100',
            'meal_plan'               => 'nullable|string|max:100',
            'ground_transfer_fleet'   => 'nullable|string|max:255',
            'price_per_person'        => $priceRule,
            'price_sharing'           => $priceRule,
            'price_quad'              => $priceRule,
            'price_triple'            => $priceRule,
            'price_double'            => $priceRule,
            'valid_from'              => 'nullable|date',
            'valid_until'             => 'nullable|date',
            'package_image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'inclusions'              => 'nullable|array',
            'package_excludes'        => 'nullable|array',
            'itinerary_days'          => 'nullable|array',
            'hotel_amenities'         => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $request->hasFile('package_image')
                ? $request->file('package_image')->store('packages/international', 'public')
                : null;

            $inclusionsList = array_values(array_filter((array) $request->input('inclusions', []), fn($item) => filled(trim($item))));
            $amenitiesList  = array_values(array_filter((array) $request->input('hotel_amenities', []), fn($item) => filled(trim($item))));

            // Process exclusions repeater: [{title, description}]
            $excludesList = [];
            if ($request->has('package_excludes') && is_array($request->package_excludes)) {
                foreach ($request->package_excludes as $exc) {
                    if (is_array($exc)) {
                        $title = trim($exc['title'] ?? '');
                        $desc  = trim($exc['description'] ?? '');
                        if ($title !== '' || $desc !== '') {
                            $excludesList[] = [
                                'title'       => $title,
                                'description' => $desc,
                            ];
                        }
                    } elseif (is_string($exc) && trim($exc) !== '') {
                        $excludesList[] = [
                            'title'       => trim($exc),
                            'description' => '',
                        ];
                    }
                }
            }

            // Process itinerary days repeater: [{day_number, title, description, highlights}]
            $itineraryList = [];
            if ($request->has('itinerary_days') && is_array($request->itinerary_days)) {
                $dayCounter = 1;
                foreach ($request->itinerary_days as $itin) {
                    if (!is_array($itin)) continue;
                    $title = trim($itin['title'] ?? '');
                    $desc  = trim($itin['description'] ?? '');
                    $rawHighlights = $itin['highlights'] ?? [];

                    $highlights = [];
                    if (is_array($rawHighlights)) {
                        $highlights = array_values(array_filter(array_map('trim', $rawHighlights)));
                    } elseif (is_string($rawHighlights) && trim($rawHighlights) !== '') {
                        $highlights = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $rawHighlights))));
                    }

                    if ($title !== '' || $desc !== '' || !empty($highlights)) {
                        $itineraryList[] = [
                            'day_number'  => !empty($itin['day_number']) ? (int) $itin['day_number'] : $dayCounter,
                            'title'       => $title,
                            'description' => $desc,
                            'highlights'  => $highlights,
                        ];
                        $dayCounter++;
                    }
                }
            }

            // Dynamic destinations list for Dual Country / Multi-Country Package
            $additionalDestinations = [];
            if ($request->has('additional_destinations') && is_array($request->additional_destinations)) {
                foreach ($request->additional_destinations as $dest) {
                    if (!empty($dest['country']) || !empty($dest['city'])) {
                        $additionalDestinations[] = [
                            'country'         => $dest['country'] ?? null,
                            'city'            => $dest['city'] ?? null,
                            'hotel_name'      => $dest['hotel_name'] ?? null,
                            'hotel_distance'  => $dest['hotel_distance'] ?? null,
                            'hotel_period'    => $dest['hotel_period'] ?? null,
                            'hotel_meal_plan' => $dest['hotel_meal_plan'] ?? null,
                            'hotel_category'  => $dest['hotel_category'] ?? null,
                        ];
                    }
                }
            }

            $isDualOrMulti = (bool) $request->is_dual_country || (bool) $request->is_multi_city;
            $nights = $request->stay_nights ? (int) $request->stay_nights : ($request->duration_nights ? (int) $request->duration_nights : null);

            $package = Package::create([
                'package_type'            => 'international',
                'package_title'           => $request->package_title,
                'name'                    => $request->package_title,
                'subtitle'                => $request->subtitle,
                'sightseeing_summary'     => $request->sightseeing_summary,
                'package_code'            => $request->package_code,
                'code'                    => $request->package_code,
                'status'                  => $request->status ?? 'active',
                'is_price_on_request'     => $isPriceOnRequest,
                'is_group_package'        => (bool) $request->is_group_package,
                'visa_status'             => $request->visa_status ?? 'included',
                'is_dual_country'         => $isDualOrMulti,
                'is_multi_city'           => $isDualOrMulti,
                'departure_city'          => $request->departure_city,
                'destination_city'        => $request->destination_city,
                'destination_country'     => $request->destination_country,
                'additional_destinations' => $additionalDestinations,
                'stay_nights'             => $nights,
                'duration_nights'         => $request->duration_nights ? (int) $request->duration_nights : $nights,
                'days'                    => $nights ? ($nights + 1) : null,
                'primary_hotel'           => $request->primary_hotel,
                'hotel_star_rating'       => $request->hotel_star_rating,
                'meal_plan'               => $request->meal_plan,
                'ground_transfer_fleet'   => $request->ground_transfer_fleet,
                'valid_from'              => $request->valid_from,
                'valid_until'             => $request->valid_until,
                'price_per_person'        => $isPriceOnRequest ? (float) ($request->price_per_person ?? 0) : (float) ($request->price_per_person ?? 0),
                'price_sharing'           => $isPriceOnRequest ? (float) ($request->price_sharing ?? 0) : (float) ($request->price_sharing ?? 0),
                'price_quad'              => $isPriceOnRequest ? (float) ($request->price_quad ?? 0) : (float) ($request->price_quad ?? 0),
                'price_triple'            => $isPriceOnRequest ? (float) ($request->price_triple ?? 0) : (float) ($request->price_triple ?? 0),
                'price_double'            => $isPriceOnRequest ? (float) ($request->price_double ?? 0) : (float) ($request->price_double ?? 0),
                'inclusions'              => !empty($inclusionsList) ? json_encode($inclusionsList) : null,
                'package_excludes_list'   => $excludesList,
                'exclusions'              => !empty($excludesList) ? implode("\n", array_map(fn($e) => $e['title'] . ($e['description'] ? ' - ' . $e['description'] : ''), $excludesList)) : null,
                'itinerary_days'          => $itineraryList,
                'hotel_amenities'         => $amenitiesList,
                'key_features'            => $request->key_features,
                'requirements_text'       => $request->requirements_text,
                'description_notes'       => $request->description_notes,
                'notes'                   => $request->description_notes,
                'package_image'           => $imagePath,
            ]);

            // Relational primary accommodation
            if ($request->filled('primary_hotel') || $request->filled('destination_city') || $request->filled('destination_country')) {
                PackageAccommodation::create([
                    'package_id'         => $package->id,
                    'place'              => $request->destination_city ? ($request->destination_city . ', ' . $request->destination_country) : $request->destination_country,
                    'hotel'              => $request->primary_hotel,
                    'accommodation_type' => $request->hotel_star_rating,
                    'food_package'       => $request->meal_plan,
                    'nights'             => $request->stay_nights ? (int) $request->stay_nights : null,
                    'days'               => $request->stay_nights ? ((int) $request->stay_nights + 1) : null,
                ]);
            }

            // Relational accommodations for additional destinations
            foreach ($additionalDestinations as $dest) {
                if (!empty($dest['hotel_name']) || !empty($dest['country'])) {
                    PackageAccommodation::create([
                        'package_id'         => $package->id,
                        'place'              => $dest['country'] ?? 'Additional Destination',
                        'hotel'              => $dest['hotel_name'] ?? null,
                        'note'               => $dest['hotel_distance'] ?? null,
                        'food_package'       => $dest['hotel_meal_plan'] ?? null,
                        'accommodation_type' => $dest['hotel_category'] ?? null,
                    ]);
                }
            }

            // Relational outbound flight
            if ($request->filled('outbound_airline') || $request->filled('outbound_origin') || $request->filled('outbound_destination') || $request->filled('outbound_flight_type')) {
                PackageTransportFlight::create([
                    'package_id'   => $package->id,
                    'airline'      => $request->outbound_airline,
                    'flight_class' => $request->outbound_flight_type,
                    'origin'       => $request->outbound_origin,
                    'destination'  => $request->outbound_destination,
                    'is_preferred' => 1,
                ]);
            }

            // Relational inbound flight
            if ($request->filled('inbound_airline') || $request->filled('inbound_origin') || $request->filled('inbound_destination') || $request->filled('inbound_flight_type')) {
                PackageTransportFlight::create([
                    'package_id'   => $package->id,
                    'airline'      => $request->inbound_airline,
                    'flight_class' => $request->inbound_flight_type,
                    'origin'       => $request->inbound_origin,
                    'destination'  => $request->inbound_destination,
                    'is_preferred' => 0,
                ]);
            }

            // Relational ground transport
            if ($request->filled('ground_transfer_fleet')) {
                PackageTransport::create([
                    'package_id' => $package->id,
                    'route'      => ($request->departure_city ?? 'Departure') . ' to ' . ($request->destination_country ?? 'Destination'),
                    'vehicle'    => $request->ground_transfer_fleet,
                    'type'       => 'airport_transfer',
                ]);
            }

            // Terms
            if ($request->filled('terms_conditions')) {
                PackageTerm::create([
                    'package_id' => $package->id,
                    'content'    => $request->terms_conditions,
                ]);
            }

            DB::commit();

            return redirect()->route('mobile-ops.international-packages.index')
                ->with('success', 'International Package created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating International Package: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing an existing international package.
     */
    public function edit(int $id)
    {
        $package = Package::where('package_type', 'international')
            ->with(['accommodations', 'transportFlights', 'terms', 'transports'])
            ->findOrFail($id);

        $allHotels = Hotel::orderBy('name')->get();

        $dbCountries = VisaCountry::where('is_active', true)->pluck('country_name')->toArray();
        $countriesList = array_unique(array_merge($dbCountries, $this->popularCountries));
        sort($countriesList);

        $departureCities = $this->departureCities;
        $mealPlans = $this->mealPlans;
        $flightTypes = $this->flightTypes;
        $hotelCategories = $this->hotelCategories;
        $groundFleets = $this->groundFleets;

        $primaryAccommodation = $package->accommodations->first();
        $outboundFlight = $package->transportFlights->firstWhere('is_preferred', 1) ?? $package->transportFlights->first();
        $inboundFlight = $package->transportFlights->where('id', '!=', optional($outboundFlight)->id)->first();
        $terms = $package->terms;
        $transport = $package->transports->first();

        $inclusionsList = [];
        if (!empty($package->inclusions)) {
            $decoded = json_decode($package->inclusions, true);
            if (is_array($decoded)) {
                $inclusionsList = $decoded;
            } else {
                $inclusionsList = array_filter(array_map('trim', explode("\n", $package->inclusions)));
            }
        }

        $excludesList = [];
        if (!empty($package->package_excludes_list)) {
            $excludesList = is_array($package->package_excludes_list)
                ? $package->package_excludes_list
                : (json_decode($package->package_excludes_list, true) ?? []);
        }

        $itineraryDays = [];
        if (!empty($package->itinerary_days)) {
            $itineraryDays = is_array($package->itinerary_days)
                ? $package->itinerary_days
                : (json_decode($package->itinerary_days, true) ?? []);
        }

        $hotelAmenitiesList = is_array($package->hotel_amenities) ? $package->hotel_amenities : [];

        return view('mobile-ops.international-packages.edit', compact(
            'package',
            'allHotels',
            'countriesList',
            'departureCities',
            'mealPlans',
            'flightTypes',
            'hotelCategories',
            'groundFleets',
            'primaryAccommodation',
            'outboundFlight',
            'inboundFlight',
            'terms',
            'transport',
            'inclusionsList',
            'excludesList',
            'itineraryDays',
            'hotelAmenitiesList'
        ));
    }

    /**
     * Update existing international package inside transaction.
     */
    public function update(Request $request, int $id)
    {
        $package = Package::where('package_type', 'international')->findOrFail($id);

        $isPriceOnRequest = $request->boolean('is_price_on_request');
        $priceRule = $isPriceOnRequest ? 'nullable|numeric|min:0' : 'nullable|numeric|min:0';

        $request->validate([
            'package_title'           => 'required|string|max:255',
            'subtitle'                => 'nullable|string|max:255',
            'sightseeing_summary'     => 'nullable|string|max:255',
            'package_code'            => 'nullable|string|max:50',
            'status'                  => 'required|in:active,inactive,draft',
            'is_price_on_request'     => 'nullable|boolean',
            'is_group_package'        => 'nullable|boolean',
            'visa_status'             => 'required|in:included,excluded',
            'is_dual_country'         => 'nullable|boolean',
            'is_multi_city'           => 'nullable|boolean',
            'departure_city'          => 'nullable|string|max:100',
            'destination_city'        => 'nullable|string|max:100',
            'destination_country'     => 'required|string|max:100',
            'additional_destinations' => 'nullable|array',
            'stay_nights'             => 'nullable|integer|min:0',
            'duration_nights'         => 'nullable|integer|min:0',
            'primary_hotel'           => 'nullable|string|max:255',
            'hotel_star_rating'       => 'nullable|string|max:100',
            'meal_plan'               => 'nullable|string|max:100',
            'ground_transfer_fleet'   => 'nullable|string|max:255',
            'price_per_person'        => $priceRule,
            'price_sharing'           => $priceRule,
            'price_quad'              => $priceRule,
            'price_triple'            => $priceRule,
            'price_double'            => $priceRule,
            'valid_from'              => 'nullable|date',
            'valid_until'             => 'nullable|date',
            'package_image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'inclusions'              => 'nullable|array',
            'package_excludes'        => 'nullable|array',
            'itinerary_days'          => 'nullable|array',
            'hotel_amenities'         => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $package->package_image;
            if ($request->hasFile('package_image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('package_image')->store('packages/international', 'public');
            }

            $inclusionsList = array_values(array_filter((array) $request->input('inclusions', []), fn($item) => filled(trim($item))));
            $amenitiesList  = array_values(array_filter((array) $request->input('hotel_amenities', []), fn($item) => filled(trim($item))));

            // Process exclusions repeater: [{title, description}]
            $excludesList = [];
            if ($request->has('package_excludes') && is_array($request->package_excludes)) {
                foreach ($request->package_excludes as $exc) {
                    if (is_array($exc)) {
                        $title = trim($exc['title'] ?? '');
                        $desc  = trim($exc['description'] ?? '');
                        if ($title !== '' || $desc !== '') {
                            $excludesList[] = [
                                'title'       => $title,
                                'description' => $desc,
                            ];
                        }
                    } elseif (is_string($exc) && trim($exc) !== '') {
                        $excludesList[] = [
                            'title'       => trim($exc),
                            'description' => '',
                        ];
                    }
                }
            }

            // Process itinerary days repeater: [{day_number, title, description, highlights}]
            $itineraryList = [];
            if ($request->has('itinerary_days') && is_array($request->itinerary_days)) {
                $dayCounter = 1;
                foreach ($request->itinerary_days as $itin) {
                    if (!is_array($itin)) continue;
                    $title = trim($itin['title'] ?? '');
                    $desc  = trim($itin['description'] ?? '');
                    $rawHighlights = $itin['highlights'] ?? [];

                    $highlights = [];
                    if (is_array($rawHighlights)) {
                        $highlights = array_values(array_filter(array_map('trim', $rawHighlights)));
                    } elseif (is_string($rawHighlights) && trim($rawHighlights) !== '') {
                        $highlights = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $rawHighlights))));
                    }

                    if ($title !== '' || $desc !== '' || !empty($highlights)) {
                        $itineraryList[] = [
                            'day_number'  => !empty($itin['day_number']) ? (int) $itin['day_number'] : $dayCounter,
                            'title'       => $title,
                            'description' => $desc,
                            'highlights'  => $highlights,
                        ];
                        $dayCounter++;
                    }
                }
            }

            $additionalDestinations = [];
            if ($request->has('additional_destinations') && is_array($request->additional_destinations)) {
                foreach ($request->additional_destinations as $dest) {
                    if (!empty($dest['country']) || !empty($dest['city'])) {
                        $additionalDestinations[] = [
                            'country'         => $dest['country'] ?? null,
                            'city'            => $dest['city'] ?? null,
                            'hotel_name'      => $dest['hotel_name'] ?? null,
                            'hotel_distance'  => $dest['hotel_distance'] ?? null,
                            'hotel_period'    => $dest['hotel_period'] ?? null,
                            'hotel_meal_plan' => $dest['hotel_meal_plan'] ?? null,
                            'hotel_category'  => $dest['hotel_category'] ?? null,
                        ];
                    }
                }
            }

            $isDualOrMulti = (bool) $request->is_dual_country || (bool) $request->is_multi_city;
            $nights = $request->stay_nights ? (int) $request->stay_nights : ($request->duration_nights ? (int) $request->duration_nights : null);

            $package->update([
                'package_title'           => $request->package_title,
                'name'                    => $request->package_title,
                'subtitle'                => $request->subtitle,
                'sightseeing_summary'     => $request->sightseeing_summary,
                'package_code'            => $request->package_code,
                'code'                    => $request->package_code,
                'status'                  => $request->status ?? 'active',
                'is_price_on_request'     => $isPriceOnRequest,
                'is_group_package'        => (bool) $request->is_group_package,
                'visa_status'             => $request->visa_status ?? 'included',
                'is_dual_country'         => $isDualOrMulti,
                'is_multi_city'           => $isDualOrMulti,
                'departure_city'          => $request->departure_city,
                'destination_city'        => $request->destination_city,
                'destination_country'     => $request->destination_country,
                'additional_destinations' => $additionalDestinations,
                'stay_nights'             => $nights,
                'duration_nights'         => $request->duration_nights ? (int) $request->duration_nights : $nights,
                'days'                    => $nights ? ($nights + 1) : null,
                'primary_hotel'           => $request->primary_hotel,
                'hotel_star_rating'       => $request->hotel_star_rating,
                'meal_plan'               => $request->meal_plan,
                'ground_transfer_fleet'   => $request->ground_transfer_fleet,
                'valid_from'              => $request->valid_from,
                'valid_until'             => $request->valid_until,
                'price_per_person'        => (float) ($request->price_per_person ?? 0),
                'price_sharing'           => (float) ($request->price_sharing ?? 0),
                'price_quad'              => (float) ($request->price_quad ?? 0),
                'price_triple'            => (float) ($request->price_triple ?? 0),
                'price_double'            => (float) ($request->price_double ?? 0),
                'inclusions'              => !empty($inclusionsList) ? json_encode($inclusionsList) : null,
                'package_excludes_list'   => $excludesList,
                'exclusions'              => !empty($excludesList) ? implode("\n", array_map(fn($e) => $e['title'] . ($e['description'] ? ' - ' . $e['description'] : ''), $excludesList)) : null,
                'itinerary_days'          => $itineraryList,
                'hotel_amenities'         => $amenitiesList,
                'key_features'            => $request->key_features,
                'requirements_text'       => $request->requirements_text,
                'description_notes'       => $request->description_notes,
                'notes'                   => $request->description_notes,
                'package_image'           => $imagePath,
            ]);

            // Sync accommodation
            $package->accommodations()->delete();
            if ($request->filled('primary_hotel') || $request->filled('destination_city') || $request->filled('destination_country')) {
                $package->accommodations()->create([
                    'place'              => $request->destination_city ? ($request->destination_city . ', ' . $request->destination_country) : $request->destination_country,
                    'hotel'              => $request->primary_hotel,
                    'accommodation_type' => $request->hotel_star_rating,
                    'food_package'       => $request->meal_plan,
                    'nights'             => $request->stay_nights ? (int) $request->stay_nights : null,
                    'days'               => $request->stay_nights ? ((int) $request->stay_nights + 1) : null,
                ]);
            }

            foreach ($additionalDestinations as $dest) {
                if (!empty($dest['hotel_name']) || !empty($dest['country'])) {
                    $package->accommodations()->create([
                        'place'              => $dest['country'] ?? 'Additional Destination',
                        'hotel'              => $dest['hotel_name'] ?? null,
                        'note'               => $dest['hotel_distance'] ?? null,
                        'food_package'       => $dest['hotel_meal_plan'] ?? null,
                        'accommodation_type' => $dest['hotel_category'] ?? null,
                    ]);
                }
            }

            // Sync flights
            $package->transportFlights()->delete();
            if ($request->filled('outbound_airline') || $request->filled('outbound_origin') || $request->filled('outbound_destination') || $request->filled('outbound_flight_type')) {
                PackageTransportFlight::create([
                    'package_id'   => $package->id,
                    'airline'      => $request->outbound_airline,
                    'flight_class' => $request->outbound_flight_type,
                    'origin'       => $request->outbound_origin,
                    'destination'  => $request->outbound_destination,
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
                    'is_preferred' => 0,
                ]);
            }

            // Sync transport fleet
            $transport = $package->transports()->first();
            if ($request->filled('ground_transfer_fleet')) {
                if ($transport) {
                    $transport->update([
                        'route'   => ($request->departure_city ?? 'Departure') . ' to ' . ($request->destination_country ?? 'Destination'),
                        'vehicle' => $request->ground_transfer_fleet,
                        'type'    => 'airport_transfer',
                    ]);
                } else {
                    $package->transports()->create([
                        'route'   => ($request->departure_city ?? 'Departure') . ' to ' . ($request->destination_country ?? 'Destination'),
                        'vehicle' => $request->ground_transfer_fleet,
                        'type'    => 'airport_transfer',
                    ]);
                }
            } elseif ($transport) {
                $transport->delete();
            }

            // Sync terms
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

            DB::commit();

            return redirect()->route('mobile-ops.international-packages.index')
                ->with('success', 'International Package updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating International Package: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete only the parent package.
     */
    public function destroy(int $id)
    {
        $package = Package::where('package_type', 'international')->findOrFail($id);

        try {
            $package->delete();

            return redirect()->route('mobile-ops.international-packages.index')
                ->with('success', "'{$package->package_title}' moved to trash successfully.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error moving package to trash: ' . $e->getMessage());
        }
    }

    /**
     * Dedicated trash listing view for soft-deleted international packages.
     */
    public function trash(Request $request)
    {
        $search = $request->get('search');

        $query = Package::onlyTrashed()
            ->where('package_type', 'international')
            ->with(['accommodations', 'transportFlights'])
            ->latest('deleted_at');

        if (!empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('package_title', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('package_code', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('departure_city', 'like', "%{$term}%")
                    ->orWhere('destination_city', 'like', "%{$term}%")
                    ->orWhere('destination_country', 'like', "%{$term}%");
            });
        }

        $packages = $query->paginate(15)->withQueryString();
        $trashCount = Package::onlyTrashed()->where('package_type', 'international')->count();

        return view('mobile-ops.international-packages.trash', compact('packages', 'search', 'trashCount'));
    }

    /**
     * Single-click restore action that recovers the package back to active listing.
     */
    public function restore(int $id)
    {
        $package = Package::onlyTrashed()
            ->where('package_type', 'international')
            ->findOrFail($id);

        try {
            $package->restore();

            return redirect()->route('mobile-ops.international-packages.trash')
                ->with('success', "'{$package->package_title}' has been restored successfully.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error restoring package: ' . $e->getMessage());
        }
    }
}
