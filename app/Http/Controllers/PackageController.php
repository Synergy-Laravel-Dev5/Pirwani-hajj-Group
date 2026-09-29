<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Giveaway;
use App\Models\Package;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    protected $itineraryImagePath = 'assets/images/packages/itinerary';

    public function index()
    {
        $packages = Package::latest()->paginate(20);
        $trashCount = Package::onlyTrashed()->count();

        return view('package.index', compact('packages', 'trashCount'));
    }
    public function create()
    {
        $package = new Package();
        $giveaways = Giveaway::all();
        $companies = Company::all();
        $trainingSessions = TrainingSession::all();
        $hotels = \App\Models\Hotel::all();
        $airlines = \App\Models\Airline::all();
        $trains = \App\Models\Train::all();

        return view('package.create', compact('package', 'giveaways', 'companies', 'trainingSessions', 'hotels', 'airlines', 'trains'));
    }
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $package = DB::transaction(function () use ($request, $data) {
            $package = new Package();
            $package->fill($data['package']);
            $package->save();

            $this->saveRelations($request, $package, $data);
            return $package;
        });

        return redirect()->route('package.show', $package->id)->with('success', 'Package created successfully.');
    }
    public function edit($id)
    {
        $package = Package::with([
            'accommodations',
            'itinerary',
            'terms',
            'maktabAddress',
            'transports',
            'transportFlights',
            'transportTrains',
            'giveaways',
            'trainingSessions',
        ])->findOrFail($id);

        $giveaways = Giveaway::all();
        $companies = Company::all();
        $trainingSessions = TrainingSession::all();
        $hotels = \App\Models\Hotel::all();
        $airlines = \App\Models\Airline::all();
        $trains = \App\Models\Train::all();

        return view('package.edit', compact('package', 'giveaways', 'companies', 'trainingSessions', 'hotels', 'airlines', 'trains'));
    }
    public function show($id)
    {
        $package = Package::with([
            'company',
            'accommodations',
            'itinerary',
            'terms',
            'maktabAddress',
            'transports',
            'transportFlights',
            'transportTrains',
            'trainingSessions',
            'giveaways',
        ])->findOrFail($id);

        return view('package.show', compact('package'));
    }
    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $package, $data) {
            $package->fill($data['package']);
            $package->save();

            $package->accommodations()->delete();
            $package->transports()->delete();
            $package->transportFlights()->delete();
            $package->transportTrains()->delete();

            $this->saveRelations($request, $package, $data);
        });

        return redirect()->route('package.show', $package->id)->with('success', 'Package updated successfully.');
    }
    public function destroy($id)
    {
        Package::findOrFail($id)->delete();

        return back()->with('success', 'Package moved to trash.');
    }
    public function trash()
    {
        $packages = Package::onlyTrashed()->latest()->paginate(20);

        return view('package.trash', compact('packages'));
    }
    public function restore($id)
    {
        Package::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('package.trash')->with('success', 'Package restored successfully.');
    }
    private function storeFile($file, string $folder): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($folder), $filename);

        return $filename;
    }
    private function deleteFile(?string $filename, string $folder): void
    {
        if ($filename && file_exists(public_path($folder . '/' . $filename))) {
            unlink(public_path($folder . '/' . $filename));
        }
    }
    private function validated(Request $request): array
    {
        $package = $request->validate([
            'year' => 'nullable|string|max:20',
            'company_id' => 'nullable|integer|exists:companies,id',
            'package_number' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:150',
            'zone' => 'nullable|string|max:150',
            'package_title' => 'nullable|string|max:200',
            'name' => 'nullable|string|max:200',
            'code' => 'nullable|string|max:100',
            'days' => 'nullable|integer',
            'maktab' => 'nullable|string|max:150',
            'maktab_number' => 'nullable|string|max:100',
            'medina_arrival' => 'nullable|in:before_hajj,after_hajj',
            'hajj_duration' => 'nullable|in:short,long',

            'hijri_start_day' => 'nullable|integer|min:1|max:30',
            'hijri_start_month' => 'nullable|integer|min:1|max:12',

            'room_type' => 'nullable|string|max:100',
            'azizia_room_type' => 'nullable|string|max:100',
            'makkah_type' => 'nullable|string|max:100',
            'medinah_type' => 'nullable|string|max:100',
            'azizia_type' => 'nullable|string|max:100',
            'mina_type' => 'nullable|string|max:100',
            'giveaway_note' => 'nullable|string',

            'makkah_a' => 'nullable|array',
            'makkah_a.double' => 'nullable|integer|min:0',
            'makkah_a.triple' => 'nullable|integer|min:0',
            'makkah_a.quad' => 'nullable|integer|min:0',
            'makkah_a.sharing' => 'nullable|integer|min:0',

            'makkah_b' => 'nullable|array',
            'makkah_b.double' => 'nullable|integer|min:0',
            'makkah_b.triple' => 'nullable|integer|min:0',
            'makkah_b.quad' => 'nullable|integer|min:0',
            'makkah_b.sharing' => 'nullable|integer|min:0',

            'madinah_a' => 'nullable|array',
            'madinah_a.double' => 'nullable|integer|min:0',
            'madinah_a.triple' => 'nullable|integer|min:0',
            'madinah_a.quad' => 'nullable|integer|min:0',
            'madinah_a.sharing' => 'nullable|integer|min:0',

            'madinah_b' => 'nullable|array',
            'madinah_b.double' => 'nullable|integer|min:0',
            'madinah_b.triple' => 'nullable|integer|min:0',
            'madinah_b.quad' => 'nullable|integer|min:0',
            'madinah_b.sharing' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'price_disclaimer' => 'nullable|string',
            'jeddah_taxi_fare' => 'nullable|string|max:100',
            'madinah_taxi_fare' => 'nullable|string|max:100',
            'services_title' => 'nullable|string|max:255',
            'services_content' => 'nullable|string',
            'feature_icons' => 'nullable|array',
        ]);

        if ($request->hasFile('feature_icons_files')) {
            $featureIcons = $package['feature_icons'] ?? [];
            foreach ($request->file('feature_icons_files') as $key => $file) {
                if ($file && $file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/package_icons'), $filename);
                    $featureIcons[$key]['icon'] = 'uploads/package_icons/' . $filename;
                }
            }
            $package['feature_icons'] = $featureIcons;
        }

        $accommodations = $request->validate([
            'accommodations' => 'nullable|array',
            'accommodations.*.place' => 'nullable|string|max:150',
            'accommodations.*.check_in' => 'nullable|date',
            'accommodations.*.check_out' => 'nullable|date',
            'accommodations.*.same_for_both' => 'nullable|boolean',
            'accommodations.*.azizia_date' => 'nullable|date',

            'accommodations.*.package_a' => 'nullable|array',
            'accommodations.*.package_a.accommodation_type' => 'nullable|string|max:150',
            'accommodations.*.package_a.saudi_star_rating' => 'nullable|string|max:50',
            'accommodations.*.package_a.hotel' => 'nullable|string|max:150',
            'accommodations.*.package_a.food_package' => 'nullable|string|max:100',

            'accommodations.*.package_b' => 'nullable|array',
            'accommodations.*.package_b.accommodation_type' => 'nullable|string|max:150',
            'accommodations.*.package_b.saudi_star_rating' => 'nullable|string|max:50',
            'accommodations.*.package_b.hotel' => 'nullable|string|max:150',
            'accommodations.*.package_b.food_package' => 'nullable|string|max:100',

            'accommodations.*.distance' => 'nullable|integer',
            'accommodations.*.food_package' => 'nullable|string|max:100',
            'accommodations.*.actual_check_in_time' => 'nullable|date',
            'accommodations.*.actual_check_out_time' => 'nullable|date',
            'accommodations.*.days' => 'nullable|integer',
            'accommodations.*.nights' => 'nullable|integer',
            'accommodations.*.makkah_ziarat' => 'nullable|in:yes,no',
            'accommodations.*.madinah_ziarat' => 'nullable|in:yes,no',
            'accommodations.*.distribution' => 'nullable|string|max:150',
            'accommodations.*.camp' => 'nullable|string|max:150',
            'accommodations.*.arafat' => 'nullable|string|max:150',
            'accommodations.*.shuttle' => 'nullable|string|max:150',
            'accommodations.*.bedding' => 'nullable|string|max:150',
            'accommodations.*.sharing' => 'nullable|string|max:150',
            'accommodations.*.sharing_type' => 'nullable|string|max:150',
            'accommodations.*.note' => 'nullable|string',
        ])['accommodations'] ?? [];

        $itinerary = $request->validate([
            'itinerary_description' => 'nullable|string',
        ]);

        $terms = $request->validate([
            'terms_content' => 'nullable|string',
        ]);

        $transports = $request->validate([
            'transports' => 'nullable|array',
            'transports.*.route' => 'nullable|string|max:150',
            'transports.*.arrival' => 'nullable|string|max:100',
            'transports.*.departure' => 'nullable|string|max:100',
            'transports.*.type' => 'nullable|string|max:100',
            'transports.*.vehicle' => 'nullable|string|max:100',
        ])['transports'] ?? [];

        $flights = $request->validate([
            'flights' => 'nullable|array',
            'flights.*.airline' => 'nullable|string|max:150',
            'flights.*.flight_no' => 'nullable|string|max:100',
            'flights.*.flight_class' => 'nullable|string|max:100',
            'flights.*.origin' => 'nullable|string|max:150',
            'flights.*.destination' => 'nullable|string|max:150',
            'flights.*.departure_date' => 'nullable|date',
            'flights.*.departure_time' => 'nullable|date_format:H:i',
            'flights.*.arrival_date' => 'nullable|date',
            'flights.*.arrival_time' => 'nullable|date_format:H:i',
            'flights.*.pnr_no' => 'nullable|string|max:100',
            'flights.*.ticket_amount' => 'nullable|numeric',
            'flights.*.is_preferred' => 'nullable|boolean',
        ])['flights'] ?? [];

        $trains = $request->validate([
            'trains' => 'nullable|array',
            'trains.*.railway' => 'nullable|string|max:150',
            'trains.*.train_no' => 'nullable|string|max:100',
            'trains.*.train_class' => 'nullable|string|max:100',
            'trains.*.origin' => 'nullable|string|max:150',
            'trains.*.destination' => 'nullable|string|max:150',
            'trains.*.departure_date' => 'nullable|date',
            'trains.*.departure_time' => 'nullable|date_format:H:i',
            'trains.*.arrival_date' => 'nullable|date',
            'trains.*.arrival_time' => 'nullable|date_format:H:i',
            'trains.*.pnr_no' => 'nullable|string|max:100',
            'trains.*.ticket_amount' => 'nullable|numeric',
        ])['trains'] ?? [];

        $maktabAddress = $request->validate([
            'maktab_address' => 'nullable|string|max:200',
            'office_address' => 'nullable|string|max:200',
        ]);

        $giveaways = $request->validate([
            'giveaways' => 'nullable|array',
            'giveaways.*' => 'integer|exists:giveaways,id',
        ])['giveaways'] ?? [];

        $trainingSessions = $request->validate([
            'training_sessions' => 'nullable|array',
            'training_sessions.*' => 'integer|exists:training_sessions,id',
        ])['training_sessions'] ?? [];

        return compact(
            'package',
            'accommodations',
            'itinerary',
            'terms',
            'transports',
            'flights',
            'trains',
            'maktabAddress',
            'giveaways',
            'trainingSessions'
        );
    }
    private function saveRelations(Request $request, Package $package, array $data): void
    {
        foreach ($data['accommodations'] as $row) {
            $sameForBoth = filter_var($row['same_for_both'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $packageA = $row['package_a'] ?? [];
            $packageB = $sameForBoth ? $packageA : ($row['package_b'] ?? []);

            $accommodationData = [
                'place' => $row['place'] ?? null,
                'check_in' => $row['check_in'] ?? null,
                'check_out' => $row['check_out'] ?? null,
                'same_for_both' => $sameForBoth,
                'azizia_date' => $row['azizia_date'] ?? null,

                'package_a_accommodation_type' => $packageA['accommodation_type'] ?? null,
                'package_a_saudi_star_rating' => $packageA['saudi_star_rating'] ?? null,
                'package_a_hotel' => $packageA['hotel'] ?? null,
                'package_a_food_package' => $packageA['food_package'] ?? ($row['food_package'] ?? null),

                'package_b_accommodation_type' => $packageB['accommodation_type'] ?? null,
                'package_b_saudi_star_rating' => $packageB['saudi_star_rating'] ?? null,
                'package_b_hotel' => $packageB['hotel'] ?? null,
                'package_b_food_package' => $packageB['food_package'] ?? ($row['food_package'] ?? null),

                'distance' => $row['distance'] ?? null,
                'food_package' => $row['food_package'] ?? null,
                'actual_check_in_time' => $row['actual_check_in_time'] ?? null,
                'actual_check_out_time' => $row['actual_check_out_time'] ?? null,
                'days' => $row['days'] ?? null,
                'nights' => $row['nights'] ?? null,
                'makkah_ziarat' => $row['makkah_ziarat'] ?? null,
                'madinah_ziarat' => $row['madinah_ziarat'] ?? null,
                'distribution' => $row['distribution'] ?? null,
                'camp' => $row['camp'] ?? null,
                'arafat' => $row['arafat'] ?? null,
                'shuttle' => $row['shuttle'] ?? null,
                'bedding' => $row['bedding'] ?? null,
                'sharing' => $row['sharing'] ?? null,
                'sharing_type' => $row['sharing_type'] ?? null,
                'note' => $row['note'] ?? null,
            ];

            $package->accommodations()->create($accommodationData);
        }

        $existingItinerary = $package->itinerary;
        $itineraryData = ['description' => $data['itinerary']['itinerary_description'] ?? null];

        $imageFields = [
            'mina_image',
            'arafat_image',
            'muzdalifah_image',
            'makkah_mina_rami_day_one_image',
            'mina_rami_day_two_image',
            'mina_makkah_rami_day_three_image',
        ];

        foreach ($imageFields as $imageField) {
            if ($request->hasFile($imageField)) {
                if ($existingItinerary && $existingItinerary->$imageField) {
                    $this->deleteFile($existingItinerary->$imageField, $this->itineraryImagePath);
                }
                $itineraryData[$imageField] = $this->storeFile($request->file($imageField), $this->itineraryImagePath);
            }
        }

        $package->itinerary()->updateOrCreate(['package_id' => $package->id], $itineraryData);

        $package->terms()->updateOrCreate(
            ['package_id' => $package->id],
            ['content' => $data['terms']['terms_content'] ?? null]
        );

        foreach ($data['transports'] as $row) {
            $package->transports()->create($row);
        }

        foreach ($data['flights'] as $row) {
            $row['is_preferred'] = filter_var($row['is_preferred'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $package->transportFlights()->create($row);
        }

        foreach ($data['trains'] as $row) {
            $package->transportTrains()->create($row);
        }

        $package->maktabAddress()->updateOrCreate(
            ['package_id' => $package->id],
            $data['maktabAddress']
        );

        $package->giveaways()->sync($data['giveaways']);

        $package->trainingSessions()->sync($data['trainingSessions']);
    }
}
