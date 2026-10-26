<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Package;
use App\Models\PackageAccommodation;
use App\Models\PackageTerm;
use App\Models\PackageTransport;
use App\Models\PackageTransportFlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DomesticPackageController extends Controller
{
    protected array $popularDomesticCities = [
        'Karachi',
        'Lahore',
        'Islamabad',
        'Rawalpindi',
        'Peshawar',
        'Quetta',
        'Multan',
        'Faisalabad',
        'Sialkot',
        'Skardu',
        'Hunza',
        'Gilgit',
        'Naran',
        'Kaghan',
        'Swat',
        'Murree',
        'Chitral',
        'Gwadar',
        'Muzaffarabad',
        'Neelum Valley'
    ];

    protected array $mealPlans = [
        'Daily Breakfast',
        'Half Board (Breakfast & Dinner)',
        'Full Board (All Meals)',
        'Room Only (RO)',
        'Bed & Breakfast',
        'All Inclusive',
    ];

    protected array $flightTypes = [
        'Direct Flight',
        'Connecting Flight',
        'Ticket Included',
        'Ticket Not Included',
        'Economy Class',
        'Business Class',
    ];

    protected array $hotelCategories = [
        '5 Star',
        '4 Star',
        '3 Star',
        '2 Star',
        'Economy / Standard',
        'Resort / Glamping',
        'Guest House',
    ];

    protected array $groundFleets = [
        'Luxury AC Coaster',
        'Toyota Grand Cabin (Hiace)',
        'Prado / 4x4 Jeep',
        'Toyota Corolla / Sedan',
        'Executive Saloon Coach',
        'Shared Tourist Van',
        'Self Drive / Not Included',
    ];

    /**
     * Listing: Filter packages by category ('domestic') with search and quick trash links.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Package::where('package_type', 'domestic')
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
                    ->orWhere('destination_city', 'like', "%{$term}%");
            });
        }

        $packages = $query->latest('id')->paginate(15)->withQueryString();

        $activeCount = Package::where('package_type', 'domestic')->count();
        $trashCount  = Package::onlyTrashed()->where('package_type', 'domestic')->count();

        return view('mobile-ops.domestic-packages.index', compact('packages', 'search', 'activeCount', 'trashCount'));
    }

    /**
     * Show form for creating a new domestic package.
     */
    public function create()
    {
        $allHotels = Hotel::orderBy('name')->get();
        $cities = $this->popularDomesticCities;
        $mealPlans = $this->mealPlans;
        $flightTypes = $this->flightTypes;
        $hotelCategories = $this->hotelCategories;
        $groundFleets = $this->groundFleets;

        return view('mobile-ops.domestic-packages.create', compact(
            'allHotels',
            'cities',
            'mealPlans',
            'flightTypes',
            'hotelCategories',
            'groundFleets'
        ));
    }

    /**
     * Store newly created domestic package in database inside a transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_title'           => 'required|string|max:255',
            'subtitle'                => 'nullable|string|max:255',
            'package_code'            => 'nullable|string|max:50',
            'status'                  => 'required|in:active,inactive,draft',
            'is_group_package'        => 'nullable|boolean',
            'is_multi_city'           => 'nullable|boolean',
            'departure_city'          => 'nullable|string|max:100',
            'destination_city'        => 'nullable|string|max:100',
            'additional_destinations' => 'nullable|array',
            'stay_nights'             => 'nullable|integer|min:0',
            'primary_hotel'           => 'nullable|string|max:255',
            'hotel_star_rating'       => 'nullable|string|max:100',
            'meal_plan'               => 'nullable|string|max:100',
            'ground_transfer_fleet'   => 'nullable|string|max:255',
            'price_per_person'        => 'nullable|numeric|min:0',
            'price_sharing'           => 'nullable|numeric|min:0',
            'price_quad'              => 'nullable|numeric|min:0',
            'price_triple'            => 'nullable|numeric|min:0',
            'price_double'            => 'nullable|numeric|min:0',
            'valid_from'              => 'nullable|date',
            'valid_until'             => 'nullable|date',
            'package_image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'inclusions'              => 'nullable|array',
            'hotel_amenities'         => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $request->hasFile('package_image')
                ? $request->file('package_image')->store('packages/domestic', 'public')
                : null;

            // Process dynamic repeatable lists (filter empty strings)
            $inclusionsList = array_values(array_filter((array) $request->input('inclusions', []), fn($item) => filled(trim($item))));
            $amenitiesList  = array_values(array_filter((array) $request->input('hotel_amenities', []), fn($item) => filled(trim($item))));

            // Process additional destinations for Multi-City
            $additionalDestinations = [];
            if ($request->has('additional_destinations') && is_array($request->additional_destinations)) {
                foreach ($request->additional_destinations as $dest) {
                    if (!empty($dest['city']) || !empty($dest['country'])) {
                        $additionalDestinations[] = [
                            'city'            => $dest['city'] ?? null,
                            'country'         => $dest['country'] ?? 'Pakistan',
                            'hotel_name'      => $dest['hotel_name'] ?? null,
                            'hotel_distance'  => $dest['hotel_distance'] ?? null,
                            'hotel_period'    => $dest['hotel_period'] ?? null,
                            'hotel_meal_plan' => $dest['hotel_meal_plan'] ?? null,
                            'hotel_category'  => $dest['hotel_category'] ?? null,
                        ];
                    }
                }
            }

            $package = Package::create([
                'package_type'            => 'domestic',
                'package_title'           => $request->package_title,
                'name'                    => $request->package_title,
                'subtitle'                => $request->subtitle,
                'package_code'            => $request->package_code,
                'code'                    => $request->package_code,
                'status'                  => $request->status ?? 'active',
                'is_group_package'        => (bool) $request->is_group_package,
                'is_multi_city'           => (bool) $request->is_multi_city,
                'departure_city'          => $request->departure_city,
                'destination_city'        => $request->destination_city,
                'destination_country'     => 'Pakistan',
                'additional_destinations' => $additionalDestinations,
                'stay_nights'             => $request->stay_nights ? (int) $request->stay_nights : null,
                'days'                    => $request->stay_nights ? ((int) $request->stay_nights + 1) : null,
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
                'hotel_amenities'         => $amenitiesList,
                'key_features'            => $request->key_features,
                'requirements_text'       => $request->requirements_text,
                'description_notes'       => $request->description_notes,
                'notes'                   => $request->description_notes,
                'package_image'           => $imagePath,
            ]);

            // Save primary accommodation record
            if ($request->filled('primary_hotel') || $request->filled('destination_city')) {
                PackageAccommodation::create([
                    'package_id'         => $package->id,
                    'place'              => $request->destination_city ?? 'Domestic Stay',
                    'hotel'              => $request->primary_hotel,
                    'accommodation_type' => $request->hotel_star_rating,
                    'food_package'       => $request->meal_plan,
                    'nights'             => $request->stay_nights ? (int) $request->stay_nights : null,
                    'days'               => $request->stay_nights ? ((int) $request->stay_nights + 1) : null,
                ]);
            }

            // Save multi-city stop accommodations
            foreach ($additionalDestinations as $dest) {
                if (!empty($dest['hotel_name']) || !empty($dest['city'])) {
                    PackageAccommodation::create([
                        'package_id'         => $package->id,
                        'place'              => $dest['city'] ?? 'Additional Stop',
                        'hotel'              => $dest['hotel_name'] ?? null,
                        'note'               => $dest['hotel_distance'] ?? null,
                        'food_package'       => $dest['hotel_meal_plan'] ?? null,
                        'accommodation_type' => $dest['hotel_category'] ?? null,
                    ]);
                }
            }

            // Save outbound flight leg
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

            // Save inbound flight leg
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

            // Save ground transport fleet mode
            if ($request->filled('ground_transfer_fleet')) {
                PackageTransport::create([
                    'package_id' => $package->id,
                    'route'      => ($request->departure_city ?? 'Departure') . ' to ' . ($request->destination_city ?? 'Destination'),
                    'vehicle'    => $request->ground_transfer_fleet,
                    'type'       => 'road_transport',
                ]);
            }

            // Save terms & conditions
            if ($request->filled('terms_conditions')) {
                PackageTerm::create([
                    'package_id' => $package->id,
                    'content'    => $request->terms_conditions,
                ]);
            }

            DB::commit();

            return redirect()->route('mobile-ops.domestic-packages.index')
                ->with('success', 'Domestic Package created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating Domestic Package: ' . $e->getMessage());
        }
    }

    /**
     * Show form for editing an existing domestic package.
     */
    public function edit(int $id)
    {
        $package = Package::where('package_type', 'domestic')
            ->with(['accommodations', 'transportFlights', 'terms', 'transports'])
            ->findOrFail($id);

        $allHotels = Hotel::orderBy('name')->get();
        $cities = $this->popularDomesticCities;
        $mealPlans = $this->mealPlans;
        $flightTypes = $this->flightTypes;
        $hotelCategories = $this->hotelCategories;
        $groundFleets = $this->groundFleets;

        $primaryAccommodation = $package->accommodations->first();
        $outboundFlight = $package->transportFlights->firstWhere('is_preferred', 1) ?? $package->transportFlights->first();
        $inboundFlight = $package->transportFlights->where('id', '!=', optional($outboundFlight)->id)->first();
        $terms = $package->terms;
        $transport = $package->transports->first();

        // Parse JSON or text inclusions
        $inclusionsList = [];
        if (!empty($package->inclusions)) {
            $decoded = json_decode($package->inclusions, true);
            if (is_array($decoded)) {
                $inclusionsList = $decoded;
            } else {
                $inclusionsList = array_filter(array_map('trim', explode("\n", $package->inclusions)));
            }
        }

        $hotelAmenitiesList = is_array($package->hotel_amenities) ? $package->hotel_amenities : [];

        return view('mobile-ops.domestic-packages.edit', compact(
            'package',
            'allHotels',
            'cities',
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
            'hotelAmenitiesList'
        ));
    }

    /**
     * Update existing domestic package inside transaction.
     */
    public function update(Request $request, int $id)
    {
        $package = Package::where('package_type', 'domestic')->findOrFail($id);

        $request->validate([
            'package_title'           => 'required|string|max:255',
            'subtitle'                => 'nullable|string|max:255',
            'package_code'            => 'nullable|string|max:50',
            'status'                  => 'required|in:active,inactive,draft',
            'is_group_package'        => 'nullable|boolean',
            'is_multi_city'           => 'nullable|boolean',
            'departure_city'          => 'nullable|string|max:100',
            'destination_city'        => 'nullable|string|max:100',
            'additional_destinations' => 'nullable|array',
            'stay_nights'             => 'nullable|integer|min:0',
            'primary_hotel'           => 'nullable|string|max:255',
            'hotel_star_rating'       => 'nullable|string|max:100',
            'meal_plan'               => 'nullable|string|max:100',
            'ground_transfer_fleet'   => 'nullable|string|max:255',
            'price_per_person'        => 'nullable|numeric|min:0',
            'price_sharing'           => 'nullable|numeric|min:0',
            'price_quad'              => 'nullable|numeric|min:0',
            'price_triple'            => 'nullable|numeric|min:0',
            'price_double'            => 'nullable|numeric|min:0',
            'valid_from'              => 'nullable|date',
            'valid_until'             => 'nullable|date',
            'package_image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'inclusions'              => 'nullable|array',
            'hotel_amenities'         => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $package->package_image;
            if ($request->hasFile('package_image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('package_image')->store('packages/domestic', 'public');
            }

            $inclusionsList = array_values(array_filter((array) $request->input('inclusions', []), fn($item) => filled(trim($item))));
            $amenitiesList  = array_values(array_filter((array) $request->input('hotel_amenities', []), fn($item) => filled(trim($item))));

            $additionalDestinations = [];
            if ($request->has('additional_destinations') && is_array($request->additional_destinations)) {
                foreach ($request->additional_destinations as $dest) {
                    if (!empty($dest['city']) || !empty($dest['country'])) {
                        $additionalDestinations[] = [
                            'city'            => $dest['city'] ?? null,
                            'country'         => $dest['country'] ?? 'Pakistan',
                            'hotel_name'      => $dest['hotel_name'] ?? null,
                            'hotel_distance'  => $dest['hotel_distance'] ?? null,
                            'hotel_period'    => $dest['hotel_period'] ?? null,
                            'hotel_meal_plan' => $dest['hotel_meal_plan'] ?? null,
                            'hotel_category'  => $dest['hotel_category'] ?? null,
                        ];
                    }
                }
            }

            $package->update([
                'package_title'           => $request->package_title,
                'name'                    => $request->package_title,
                'subtitle'                => $request->subtitle,
                'package_code'            => $request->package_code,
                'code'                    => $request->package_code,
                'status'                  => $request->status ?? 'active',
                'is_group_package'        => (bool) $request->is_group_package,
                'is_multi_city'           => (bool) $request->is_multi_city,
                'departure_city'          => $request->departure_city,
                'destination_city'        => $request->destination_city,
                'destination_country'     => 'Pakistan',
                'additional_destinations' => $additionalDestinations,
                'stay_nights'             => $request->stay_nights ? (int) $request->stay_nights : null,
                'days'                    => $request->stay_nights ? ((int) $request->stay_nights + 1) : null,
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
                'hotel_amenities'         => $amenitiesList,
                'key_features'            => $request->key_features,
                'requirements_text'       => $request->requirements_text,
                'description_notes'       => $request->description_notes,
                'notes'                   => $request->description_notes,
                'package_image'           => $imagePath,
            ]);

            // Sync accommodations
            $package->accommodations()->delete();
            if ($request->filled('primary_hotel') || $request->filled('destination_city')) {
                $package->accommodations()->create([
                    'place'              => $request->destination_city ?? 'Domestic Stay',
                    'hotel'              => $request->primary_hotel,
                    'accommodation_type' => $request->hotel_star_rating,
                    'food_package'       => $request->meal_plan,
                    'nights'             => $request->stay_nights ? (int) $request->stay_nights : null,
                    'days'               => $request->stay_nights ? ((int) $request->stay_nights + 1) : null,
                ]);
            }

            foreach ($additionalDestinations as $dest) {
                if (!empty($dest['hotel_name']) || !empty($dest['city'])) {
                    $package->accommodations()->create([
                        'place'              => $dest['city'] ?? 'Additional Stop',
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
                        'route'   => ($request->departure_city ?? 'Departure') . ' to ' . ($request->destination_city ?? 'Destination'),
                        'vehicle' => $request->ground_transfer_fleet,
                        'type'    => 'road_transport',
                    ]);
                } else {
                    $package->transports()->create([
                        'route'   => ($request->departure_city ?? 'Departure') . ' to ' . ($request->destination_city ?? 'Destination'),
                        'vehicle' => $request->ground_transfer_fleet,
                        'type'    => 'road_transport',
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

            return redirect()->route('mobile-ops.domestic-packages.index')
                ->with('success', 'Domestic Package updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating Domestic Package: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete only the parent package.
     * Leave child relational records and physical storage files intact.
     */
    public function destroy(int $id)
    {
        $package = Package::where('package_type', 'domestic')->findOrFail($id);

        try {
            // Strictly soft-delete parent package only
            $package->delete();

            return redirect()->route('mobile-ops.domestic-packages.index')
                ->with('success', "'{$package->package_title}' moved to trash successfully.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error moving package to trash: ' . $e->getMessage());
        }
    }

    /**
     * Dedicated trash listing view for soft-deleted domestic packages.
     */
    public function trash(Request $request)
    {
        $search = $request->get('search');

        $query = Package::onlyTrashed()
            ->where('package_type', 'domestic')
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
                    ->orWhere('destination_city', 'like', "%{$term}%");
            });
        }

        $packages = $query->paginate(15)->withQueryString();
        $trashCount = Package::onlyTrashed()->where('package_type', 'domestic')->count();

        return view('mobile-ops.domestic-packages.trash', compact('packages', 'search', 'trashCount'));
    }

    /**
     * Single-click restore action that recovers the package back to active listing.
     */
    public function restore(int $id)
    {
        $package = Package::onlyTrashed()
            ->where('package_type', 'domestic')
            ->findOrFail($id);

        try {
            $package->restore();

            return redirect()->route('mobile-ops.domestic-packages.trash')
                ->with('success', "'{$package->package_title}' has been restored successfully.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error restoring package: ' . $e->getMessage());
        }
    }
}
