<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPerson;
use App\Models\HajiGroup;
use App\Models\HajiGroupPerson;
use App\Models\Package;
use App\Models\Route as BusRoute;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusAssignmentController extends Controller
{
    /**
     * Standard Hajj Routes / Sectors
     */
    public static function getDefaultSectors(): array
    {
        return [
            'Jeddah Airport ➔ Makkah Hotel'          => 'Jeddah Airport ➔ Makkah Hotel',
            'Makkah Hotel ➔ Madinah Hotel'           => 'Makkah Hotel ➔ Madinah Hotel',
            'Madinah Hotel ➔ Madinah/Jeddah Airport' => 'Madinah Hotel ➔ Madinah/Jeddah Airport',
            'Makkah / Azizia ➔ Mina Mashair Bus'     => 'Makkah / Azizia ➔ Mina Mashair Bus',
            'Mina ➔ Arafat ➔ Muzdalifah (Mashair)'   => 'Mina ➔ Arafat ➔ Muzdalifah (Mashair)',
            'Muzdalifah ➔ Mina / Azizia Building'    => 'Muzdalifah ➔ Mina / Azizia Building',
            'Makkah Ziyarat Tour'                    => 'Makkah Ziyarat Tour',
            'Madinah Ziyarat Tour'                   => 'Madinah Ziyarat Tour',
        ];
    }

    /**
     * Display Bus Assignment Dashboard
     */
    public function index(Request $request)
    {
        $sectors = self::getDefaultSectors();

        // Also merge any custom route sectors from database
        $dbRoutes = BusRoute::all()->map(function ($r) {
            return trim($r->start_place . ' ➔ ' . $r->end_place);
        })->filter()->toArray();
        foreach ($dbRoutes as $dbr) {
            if (!isset($sectors[$dbr])) {
                $sectors[$dbr] = $dbr;
            }
        }

        $selectedSector = $request->get('sector', array_key_first($sectors));

        // Fetch all active bus groups for this sector
        $busesQuery = HajiGroup::with(['groupPersons.person.booking.package', 'groupPersons.booking.client', 'vehicle'])
            ->where('group_type', 'bus')
            ->where('route_name', $selectedSector);

        if (isCompanyUser()) {
            $busesQuery->where('company_id', getAuthCompanyId());
        }

        $buses = $busesQuery->orderBy('id', 'asc')->get();

        // Selected active bus
        $selectedBusId = $request->get('bus_id', $buses->first()?->id);
        $activeBus = $buses->firstWhere('id', $selectedBusId) ?: $buses->first();

        // Get all pilgrims currently assigned to ANY bus in this sector
        $assignedPersonIdsInSector = HajiGroupPerson::whereHas('group', function ($gq) use ($selectedSector) {
            $gq->where('group_type', 'bus')->where('route_name', $selectedSector)->whereNull('deleted_at');
        })->pluck('booking_person_id')->filter()->toArray();

        // Query all active pilgrims in system
        $pilgrimsQuery = BookingPerson::with(['booking.package', 'booking.client'])
            ->whereHas('booking', function ($bq) {
                $bq->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
                if (isCompanyUser()) {
                    $bq->where('company_id', getAuthCompanyId());
                }
            });

        // Search / Package / Gender filters for unassigned list
        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $pilgrimsQuery->where(function ($q) use ($term) {
                $q->where('full_name', 'LIKE', $term)
                  ->orWhere('passport_number', 'LIKE', $term)
                  ->orWhere('hajj_id', 'LIKE', $term)
                  ->orWhere('hb_number', 'LIKE', $term)
                  ->orWhereHas('booking', function ($bq) use ($term) {
                      $bq->where('voucher_number', 'LIKE', $term)
                         ->orWhere('booking_number', 'LIKE', $term);
                  });
            });
        }

        if ($request->filled('package_id') && $request->package_id !== 'all') {
            $pilgrimsQuery->whereHas('booking', function ($bq) use ($request) {
                $bq->where('package_id', $request->package_id);
            });
        }

        if ($request->filled('gender') && $request->gender !== 'all') {
            $pilgrimsQuery->whereRaw('LOWER(gender) = ?', [strtolower($request->gender)]);
        }

        $allPilgrims = $pilgrimsQuery->orderBy('booking_id', 'asc')->orderBy('id', 'asc')->get();

        // Partition into unassigned for this sector vs assigned
        $unassignedPilgrims = $allPilgrims->reject(function ($p) use ($assignedPersonIdsInSector) {
            return in_array($p->id, $assignedPersonIdsInSector);
        })->values();

        // Group unassigned pilgrims by booking (Family units)
        $unassignedFamilies = $unassignedPilgrims->groupBy('booking_id');

        // Stats calculation
        $totalPilgrimsInSystem = $allPilgrims->count();
        $totalAssignedInSector = count($assignedPersonIdsInSector);
        $totalUnassignedInSector = $unassignedPilgrims->count();
        $totalBusesCount = $buses->count();
        $totalBusCapacity = $buses->sum('bus_capacity');

        $packages = Package::latest()->get();
        $vehicles = Vehicle::where('status', 'active')->orderBy('brand_name')->get();

        return view('transport.bus_assignment', compact(
            'sectors',
            'selectedSector',
            'buses',
            'activeBus',
            'unassignedPilgrims',
            'unassignedFamilies',
            'totalPilgrimsInSystem',
            'totalAssignedInSector',
            'totalUnassignedInSector',
            'totalBusesCount',
            'totalBusCapacity',
            'packages',
            'vehicles'
        ));
    }

    /**
     * Create / Register a New Bus for the Sector
     */
    public function createBus(Request $request)
    {
        $request->validate([
            'bus_name'     => 'required|string|max:150',
            'bus_capacity' => 'required|integer|min:1|max:100',
            'route_name'   => 'required|string',
        ]);

        $sector = $request->route_name;
        $capacity = intval($request->bus_capacity);

        $bus = HajiGroup::create([
            'group_name'      => $request->bus_name,
            'bus_name'        => $request->bus_name,
            'group_type'      => 'bus',
            'company_id'      => isCompanyUser() ? getAuthCompanyId() : null,
            'bus_capacity'    => $capacity,
            'route_name'      => $sector,
            'vehicle_id'      => $request->vehicle_id ?: null,
            'driver_name'     => $request->driver_name ?: null,
            'driver_phone'    => $request->driver_phone ?: null,
            'plate_number'    => $request->plate_number ?: null,
            'departure_date'  => $request->departure_date ?: null,
            'departure_time'  => $request->departure_time ?: null,
            'notes'           => $request->notes ?: null,
            'status'          => 'active',
        ]);

        return redirect()->route('transport.bus-assignment', [
            'sector' => $sector,
            'bus_id' => $bus->id,
        ])->with('success', "Bus '{$bus->group_name}' (Capacity: {$capacity} seats) added to {$sector} successfully!");
    }

    /**
     * Assign Selected Pilgrims to Bus with Auto / Consecutive Seat Numbers
     */
    public function assignBus(Request $request)
    {
        $request->validate([
            'bus_id'       => 'required|exists:haji_groups,id',
            'person_ids'   => 'required|array|min:1',
            'person_ids.*' => 'exists:booking_persons,id',
        ]);

        $bus = HajiGroup::with('groupPersons')->findOrFail($request->bus_id);
        $personIds = array_values(array_unique($request->person_ids));
        $countToAssign = count($personIds);

        $currentAssigned = $bus->groupPersons->count();
        $capacity = intval($bus->bus_capacity ?: 45);
        $availableSeats = max(0, $capacity - $currentAssigned);

        if ($countToAssign > $availableSeats) {
            return back()->with('error', "Cannot Assign! You selected {$countToAssign} pilgrims, but Bus '{$bus->group_name}' only has {$availableSeats} available seats (Capacity: {$capacity}, Already Occupied: {$currentAssigned}).");
        }

        // Determine next available seat numbers
        $existingSeats = $bus->groupPersons->pluck('seat_number')->filter()->map(function($s) {
            return is_numeric($s) ? intval($s) : null;
        })->filter()->toArray();

        $startSeat = intval($request->starting_seat ?: 1);
        if ($startSeat < 1) $startSeat = 1;

        // Auto-increment seat numbering avoiding existing occupied seats
        $currentSeat = $startSeat;

        DB::transaction(function () use ($bus, $personIds, $existingSeats, &$currentSeat) {
            $persons = BookingPerson::whereIn('id', $personIds)->orderBy('booking_id', 'asc')->orderBy('id', 'asc')->get();

            foreach ($persons as $person) {
                // Find next free seat integer
                while (in_array($currentSeat, $existingSeats)) {
                    $currentSeat++;
                }

                HajiGroupPerson::create([
                    'haji_group_id'     => $bus->id,
                    'booking_id'        => $person->booking_id,
                    'booking_person_id' => $person->id,
                    'seat_number'       => (string)$currentSeat,
                ]);

                $existingSeats[] = $currentSeat;
                $currentSeat++;
            }
        });

        return redirect()->route('transport.bus-assignment', [
            'sector' => $bus->route_name,
            'bus_id' => $bus->id,
        ])->with('success', "Successfully assigned {$countToAssign} pilgrim(s) to {$bus->group_name}! Seats allocated.");
    }

    /**
     * Unassign Pilgrim(s) from Bus
     */
    public function unassignBus(Request $request)
    {
        $request->validate([
            'haji_group_person_ids' => 'required|array',
        ]);

        $gpIds = $request->haji_group_person_ids;
        $count = count($gpIds);

        $bus = null;
        $firstGp = HajiGroupPerson::find($gpIds[0] ?? 0);
        if ($firstGp) {
            $bus = HajiGroup::find($firstGp->haji_group_id);
        }

        HajiGroupPerson::whereIn('id', $gpIds)->delete();

        $sector = $bus ? $bus->route_name : null;
        $busId  = $bus ? $bus->id : null;

        return redirect()->route('transport.bus-assignment', [
            'sector' => $sector,
            'bus_id' => $busId,
        ])->with('success', "Removed {$count} pilgrim(s) from bus successfully. They are now in the unassigned list.");
    }

    /**
     * Update Seat Number for a Passenger
     */
    public function updateSeat(Request $request)
    {
        $request->validate([
            'haji_group_person_id' => 'required|exists:haji_group_persons,id',
            'seat_number'          => 'required|string|max:50',
        ]);

        $gp = HajiGroupPerson::findOrFail($request->haji_group_person_id);
        $gp->update(['seat_number' => trim($request->seat_number)]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Seat number updated!']);
        }

        return back()->with('success', "Seat number updated to {$gp->seat_number}.");
    }

    /**
     * Export Official Bus Passenger Manifest PDF
     */
    public function exportPdf(Request $request)
    {
        $busId = $request->get('bus_id');
        $bus = HajiGroup::with([
            'groupPersons.person.booking.package',
            'groupPersons.booking.client',
            'vehicle',
            'company'
        ])->findOrFail($busId);

        $passengers = $bus->groupPersons->sortBy(function ($gp) {
            $s = $gp->seat_number;
            return is_numeric($s) ? intval($s) : 9999;
        })->values();

        $pdf = Pdf::loadView('transport.bus_manifest_pdf', compact('bus', 'passengers'));
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $cleanBusName = preg_replace('/[^A-Za-z0-9]/', '_', $bus->group_name ?: 'Bus');
        $fileName = 'Bus_Passenger_Manifest_' . $cleanBusName . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export Official Bus Passenger Manifest Excel
     */
    public function exportExcel(Request $request)
    {
        $busId = $request->get('bus_id');
        $bus = HajiGroup::with([
            'groupPersons.person.booking.package',
            'groupPersons.booking.client',
            'vehicle',
            'company'
        ])->findOrFail($busId);

        $passengers = $bus->groupPersons->sortBy(function ($gp) {
            $s = $gp->seat_number;
            return is_numeric($s) ? intval($s) : 9999;
        })->values();

        $cleanBusName = preg_replace('/[^A-Za-z0-9]/', '_', $bus->group_name ?: 'Bus');
        $fileName = 'Bus_Passenger_Manifest_' . $cleanBusName . '_' . date('Ymd_His') . '.xls';

        return response()->streamDownload(function () use ($bus, $passengers) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;width:100%;} th,td{border:1px solid #000;padding:6px;font-family:Arial,sans-serif;font-size:11px;} th{background-color:#0f172a;color:#fff;font-weight:bold;text-align:center;} .text-center{text-align:center;} .fw-bold{font-weight:bold;}</style></head><body>';
            echo '<h2>PIRWANI HAJJ GROUP - OFFICIAL BUS PASSENGER MANIFEST</h2>';
            echo '<p><strong>Sector:</strong> ' . htmlspecialchars($bus->route_name ?? '—') . ' | <strong>Bus:</strong> ' . htmlspecialchars($bus->group_name) . ' | <strong>Driver:</strong> ' . htmlspecialchars($bus->driver_name ?? '—') . ' (' . htmlspecialchars($bus->driver_phone ?? '') . ') | <strong>Plate:</strong> ' . htmlspecialchars($bus->plate_number ?? '—') . '</p>';
            echo '<table><thead><tr>';
            echo '<th>SEAT #</th><th>HAJJ ID</th><th>HB #</th><th>PILGRIM FULL NAME</th><th>GENDER</th><th>PASSPORT NO</th><th>CONTACT NO</th><th>BOOKING / VOUCHER #</th><th>PACKAGE</th><th>MAKTAB / CAMP</th>';
            echo '</tr></thead><tbody>';

            foreach ($passengers as $gp) {
                $p = $gp->person;
                $b = $gp->booking;
                $maktab = $b?->camp ?: ($b?->package?->camp_category ? 'Maktab ' . $b->package->camp_category : '—');

                echo '<tr>';
                echo '<td class="text-center fw-bold">' . htmlspecialchars($gp->seat_number ?? '—') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($p?->hajj_id ?? '—') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($p?->hb_number ?? '—') . '</td>';
                echo '<td class="fw-bold">' . htmlspecialchars($p?->full_name ?? ($b?->client?->name ?? 'Guest')) . '</td>';
                echo '<td class="text-center">' . strtoupper($p?->gender ?? '—') . '</td>';
                echo '<td class="text-center fw-bold">' . htmlspecialchars($p?->passport_number ?? ($b?->passport_number ?? '—')) . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($p?->phone ?? ($b?->client?->phone ?? '—')) . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($b?->voucher_number ?? ($b?->booking_number ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars($b?->package?->package_title ?? ($b?->package?->name ?? '—')) . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($maktab) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table></body></html>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
