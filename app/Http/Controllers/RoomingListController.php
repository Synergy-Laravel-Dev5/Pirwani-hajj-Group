<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHotel;
use App\Models\BookingPerson;
use App\Models\Hotel;
use App\Models\HotelRoomCapacity;
use App\Models\RoomType;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomingListController extends Controller
{
    /**
     * Rooming List & Bed Allocation Report (Matching Official Manifest Format)
     */
    public function index(Request $request)
    {
        $data = $this->getFilteredRoomingData($request);

        // Dropdown Data
        $registeredHotels = Hotel::orderBy('name')->get();
        $distinctHotelNames = BookingHotel::select('hotel_name')->distinct()->whereNotNull('hotel_name')->pluck('hotel_name');
        $roomTypes = RoomType::where('status', 'active')->orderBy('capacity')->get();
        if ($roomTypes->isEmpty()) {
            $roomTypes = collect([
                (object)['name' => 'Single', 'code' => 'single', 'capacity' => 1],
                (object)['name' => 'Double', 'code' => 'double', 'capacity' => 2],
                (object)['name' => 'Triple', 'code' => 'triple', 'capacity' => 3],
                (object)['name' => 'Quad', 'code' => 'quad', 'capacity' => 4],
                (object)['name' => 'Quint', 'code' => 'quint', 'capacity' => 5],
                (object)['name' => 'Six', 'code' => 'six', 'capacity' => 6],
                (object)['name' => 'Sharing', 'code' => 'sharing', 'capacity' => 4],
                (object)['name' => 'Suite', 'code' => 'suite', 'capacity' => 2],
            ]);
        }

        return view('reports.rooming_list', array_merge($data, compact(
            'registeredHotels',
            'distinctHotelNames',
            'roomTypes'
        )));
    }

    /**
     * Export Official Rooming List & Manifest as PDF
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredRoomingData($request);

        $pdf = Pdf::loadView('reports.rooming_list_pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $hotelPart = !empty($data['hotelFilter']) ? '_' . preg_replace('/[^A-Za-z0-9]/', '_', $data['hotelFilter']) : '';
        $locPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName = 'Official_Rooming_List_Manifest' . $locPart . $hotelPart . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export Official Rooming List & Manifest as Excel (CSV with UTF-8 BOM for MS Excel)
     */
    public function exportExcel(Request $request)
    {
        $data = $this->getFilteredRoomingData($request);
        $groupedRooms = $data['groupedRooms'];

        $hotelPart = !empty($data['hotelFilter']) ? '_' . preg_replace('/[^A-Za-z0-9]/', '_', $data['hotelFilter']) : '';
        $locPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName = 'Official_Rooming_List_Manifest' . $locPart . $hotelPart . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($groupedRooms) {
            $handle = fopen('php://output', 'w');
            
            // Output UTF-8 BOM so Microsoft Excel cleanly renders characters
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Column Headers
            fputcsv($handle, [
                'SR',
                'HAJJ ID',
                'HB NUMBER',
                'PASSPORT NUMBER',
                'FULL NAME',
                'GENDER',
                'LOCATION',
                'HOTEL / BUILDING',
                'ROOM TYPE',
                'ROOM NUMBER',
                'TOTAL BEDS',
                'OCCUPIED BEDS',
                'AVAILABLE BEDS',
                'ROOM GENDER',
                'BOOKING NUMBER',
                'CLIENT / GROUP NAME',
                'PHONE NUMBER',
                'CHECK IN',
                'CHECK OUT',
                'ROOM NOTES'
            ]);

            $sr = 1;
            foreach ($groupedRooms as $room) {
                $rNum = trim($room['room_number']);
                $roomDisplayNo = str_starts_with(strtoupper($rNum), 'R') ? strtoupper($rNum) : ('R' . $rNum);
                $locLabel = strtoupper($room['location'] ?? 'MAKKAH');

                if (empty($room['occupants'])) {
                    fputcsv($handle, [
                        $sr++,
                        '—',
                        '—',
                        '—',
                        '(Empty Room)',
                        '—',
                        $locLabel,
                        $room['hotel_name'],
                        $room['room_type'],
                        $roomDisplayNo,
                        $room['total_capacity'],
                        0,
                        $room['total_capacity'],
                        $room['room_gender'] ?? 'Any',
                        '—',
                        '—',
                        '—',
                        $room['check_in_min'] ? date('d M Y', strtotime($room['check_in_min'])) : '—',
                        $room['check_out_max'] ? date('d M Y', strtotime($room['check_out_max'])) : '—',
                        $room['notes'] ?? ''
                    ]);
                } else {
                    foreach ($room['occupants'] as $occ) {
                        fputcsv($handle, [
                            $sr++,
                            !empty($occ['hajj_id']) ? $occ['hajj_id'] : '—',
                            !empty($occ['hb_number']) ? $occ['hb_number'] : '—',
                            $occ['passport'] ?? '—',
                            $occ['name'] ?? '—',
                            strtoupper($occ['gender'] ?? 'Male'),
                            $locLabel,
                            $room['hotel_name'],
                            $room['room_type'],
                            $roomDisplayNo,
                            $room['total_capacity'],
                            $room['occupied_beds'],
                            $room['available_beds'],
                            $room['room_gender'] ?? 'Any',
                            $occ['booking_number'] ?? '—',
                            $occ['client_name'] ?? '—',
                            $occ['phone'] ?? '—',
                            $occ['check_in'] ?? '—',
                            $occ['check_out'] ?? '—',
                            $room['notes'] ?? ''
                        ]);
                    }
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Shared filter logic for Web view, PDF export, and Excel export
     */
    private function getFilteredRoomingData(Request $request): array
    {
        $hotelFilter    = $request->get('hotel_name');
        $locationFilter = $request->get('location');
        $roomTypeFilter = $request->get('room_type');
        $statusFilter   = $request->get('status'); // all, available, full, overbooked
        $search         = $request->get('search');
        $fromDate       = $request->get('from_date');
        $toDate         = $request->get('to_date');
        $viewMode       = $request->get('view', 'manifest'); // manifest (PDF table) or cards

        // Query active BookingHotel records that have a room_number set
        $query = BookingHotel::with([
            'booking.client',
            'booking.company',
            'booking.persons'
        ])->whereHas('booking', function ($q) {
            $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
        })->whereNotNull('room_number')
          ->where('room_number', '!=', '');

        // Hotel name filter
        if (!empty($hotelFilter)) {
            $query->where('hotel_name', 'LIKE', '%' . $hotelFilter . '%');
        }

        // Location filter
        if (!empty($locationFilter) && $locationFilter !== 'all') {
            $query->where('location', $locationFilter);
        }

        // Room Type filter
        if (!empty($roomTypeFilter) && $roomTypeFilter !== 'all') {
            $cleanType = strtolower(trim($roomTypeFilter));
            if ($cleanType === 'six') {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(TRIM(room_type)) = ?', ['six'])
                      ->orWhereRaw('LOWER(TRIM(room_type)) = ?', ['sharing']);
                });
            } else {
                $query->whereRaw('LOWER(TRIM(room_type)) = ?', [$cleanType]);
            }
        }

        // Date overlap filter
        if (!empty($fromDate) && !empty($toDate)) {
            $cIn = date('Y-m-d', strtotime($fromDate));
            $cOut = date('Y-m-d', strtotime($toDate));
            $query->where(function ($q) use ($cIn, $cOut) {
                $q->where(function ($sub) {
                    $sub->whereNull('check_in')->whereNull('check_out');
                })->orWhere(function ($sub) use ($cIn, $cOut) {
                    $sub->where('check_in', '<=', $cOut)->where('check_out', '>=', $cIn);
                });
            });
        } elseif (!empty($fromDate)) {
            $query->whereDate('check_in', '>=', $fromDate);
        } elseif (!empty($toDate)) {
            $query->whereDate('check_out', '<=', $toDate);
        }

        // Search by Pilgrim name, passport, room number, or booking number
        if (!empty($search)) {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('room_number', 'LIKE', $term)
                  ->orWhere('hotel_name', 'LIKE', $term)
                  ->orWhereHas('booking', function ($bq) use ($term) {
                      $bq->where('voucher_number', 'LIKE', $term)
                         ->orWhere('passport_number', 'LIKE', $term)
                         ->orWhereHas('client', function ($cq) use ($term) {
                             $cq->where('name', 'LIKE', $term)
                                ->orWhere('passport_number', 'LIKE', $term);
                         })
                         ->orWhereHas('persons', function ($pq) use ($term) {
                             $pq->where('full_name', 'LIKE', $term)
                                ->orWhere('passport_number', 'LIKE', $term)
                                ->orWhere('cnic', 'LIKE', $term)
                                ->orWhere('hajj_id', 'LIKE', $term)
                                ->orWhere('hb_number', 'LIKE', $term);
                         });
                  });
            });
        }

        $allHotelBookings = $query->orderBy('hotel_name')->orderBy('room_number')->get();

        // Group stays by hotel_name and room_number
        $groupedRooms = [];

        foreach ($allHotelBookings as $bh) {
            $hName = trim($bh->hotel_name ?: 'Unspecified Hotel');
            $rNum  = trim($bh->room_number);
            $rType = ucfirst(trim($bh->room_type ?: 'Quad'));
            $loc   = $bh->location ?: 'makkah';

            $groupKey = strtolower($loc . '___' . $hName . '___' . $rNum);

            if (!isset($groupedRooms[$groupKey])) {
                $baseCap = HotelRoomCapacity::resolveCapacity($hName, $rNum, $rType, $loc);
                $customCap = HotelRoomCapacity::where(function ($q) use ($rNum) {
                    foreach (HotelRoomCapacity::normalizeRoomNumber($rNum) as $v) {
                        $q->orWhereRaw('LOWER(TRIM(room_number)) = ?', [$v]);
                    }
                })->first();

                $extraBeds = $customCap ? (int) $customCap->extra_beds : 0;
                $effectiveCap = max(1, $baseCap);
                $roomGender = !empty($customCap->gender) && $customCap->gender !== 'Any' ? $customCap->gender : (!empty($bh->gender) ? $bh->gender : 'Any');

                $groupedRooms[$groupKey] = [
                    'hotel_name'      => $hName,
                    'room_number'     => $rNum,
                    'room_type'       => $rType,
                    'room_gender'     => $roomGender,
                    'location'        => $loc,
                    'capacity_id'     => $customCap ? $customCap->id : null,
                    'base_capacity'   => $baseCap,
                    'extra_beds'      => $extraBeds,
                    'total_capacity'  => $effectiveCap,
                    'occupied_beds'   => 0,
                    'available_beds'  => $effectiveCap,
                    'is_full'         => false,
                    'is_overbooked'   => false,
                    'notes'           => $customCap ? $customCap->notes : null,
                    'occupants'       => [],
                    'check_in_min'    => $bh->check_in,
                    'check_out_max'   => $bh->check_out,
                    'booking_ids'     => [],
                ];
            }

            // Update stay dates
            if ($bh->check_in && (!$groupedRooms[$groupKey]['check_in_min'] || $bh->check_in < $groupedRooms[$groupKey]['check_in_min'])) {
                $groupedRooms[$groupKey]['check_in_min'] = $bh->check_in;
            }
            if ($bh->check_out && (!$groupedRooms[$groupKey]['check_out_max'] || $bh->check_out > $groupedRooms[$groupKey]['check_out_max'])) {
                $groupedRooms[$groupKey]['check_out_max'] = $bh->check_out;
            }

            $b = $bh->booking;
            if (!$b) continue;

            if (!in_array($b->id, $groupedRooms[$groupKey]['booking_ids'])) {
                $groupedRooms[$groupKey]['booking_ids'][] = $b->id;
            }

            $persons = $b->persons;
            if ($persons && $persons->count() > 0) {
                foreach ($persons as $p) {
                    $groupedRooms[$groupKey]['occupied_beds']++;

                    // Resolve Hajj ID, HB Number, Gender, and Photo (Do NOT auto-generate if empty)
                    $hajjId = !empty($p->hajj_id) ? trim($p->hajj_id) : '';
                    $hbNumber = !empty($p->hb_number) ? trim($p->hb_number) : '';
                    $gender = !empty($p->gender) ? ucfirst(strtolower($p->gender)) : 'Male';
                    
                    // Female detection fallback if gender is default
                    if (empty($p->gender) || $p->gender === 'Male') {
                        $fCheck = strtolower($p->full_name . ' ' . $p->given_name . ' ' . $p->surname);
                        if (preg_match('/\b(bibi|khatoon|fatima|ayesha|aisha|maryam|begum|bano|naz|uzma|zahra|zainab|amna|rabia|samina|sumaira|hira|sana|madiha|tooba|javeria|rubab|shagufta|roshan|safia)\b/i', $fCheck)) {
                            $gender = 'Female';
                        }
                    }

                    $groupedRooms[$groupKey]['occupants'][] = [
                        'person_id'      => $p->id,
                        'hajj_id'        => $hajjId,
                        'hb_number'      => $hbNumber,
                        'name'           => $p->full_name ?: trim(($p->given_name ?? '') . ' ' . ($p->surname ?? 'Pilgrim')),
                        'passport'       => $p->passport_number ?: '—',
                        'gender'         => $gender,
                        'photo'          => $p->photo,
                        'cnic'           => $p->cnic,
                        'phone'          => $p->phone ?: $b->phone,
                        'booking_id'     => $b->id,
                        'booking_number' => $b->booking_number ?? ('#BK-' . $b->id),
                        'client_name'    => $b->client->name ?? ($b->company->company_name ?? 'N/A'),
                        'check_in'       => $bh->check_in ? date('d M Y', strtotime($bh->check_in)) : '—',
                        'check_out'      => $bh->check_out ? date('d M Y', strtotime($bh->check_out)) : '—',
                        'room_type'      => $bh->room_type,
                    ];
                }
            } else {
                $pax = max(1, (int) ($b->no_of_pax ?: 1));
                $groupedRooms[$groupKey]['occupied_beds'] += $pax;
                for ($i = 1; $i <= $pax; $i++) {
                    $groupedRooms[$groupKey]['occupants'][] = [
                        'person_id'      => null,
                        'hajj_id'        => '',
                        'hb_number'      => '',
                        'name'           => ($b->client->name ?? 'Guest') . ($pax > 1 ? " (#{$i})" : ''),
                        'passport'       => $b->passport_number ?: '—',
                        'gender'         => 'Male',
                        'photo'          => null,
                        'cnic'           => $b->cnic,
                        'phone'          => $b->phone,
                        'booking_id'     => $b->id,
                        'booking_number' => $b->booking_number ?? ('#BK-' . $b->id),
                        'client_name'    => $b->client->name ?? ($b->company->company_name ?? 'N/A'),
                        'check_in'       => $bh->check_in ? date('d M Y', strtotime($bh->check_in)) : '—',
                        'check_out'      => $bh->check_out ? date('d M Y', strtotime($bh->check_out)) : '—',
                        'room_type'      => $bh->room_type,
                    ];
                }
            }
        }

        // Finalize occupancy flags & stats
        $totalRoomsInUse = 0;
        $totalBedsCapacity = 0;
        $totalBedsOccupied = 0;
        $totalBedsAvailable = 0;
        $fullRoomsCount = 0;
        $partialRoomsCount = 0;
        $overbookedRoomsCount = 0;

        foreach ($groupedRooms as $key => &$room) {
            $cap = $room['total_capacity'];
            $occ = $room['occupied_beds'];

            $room['available_beds'] = max(0, $cap - $occ);
            $room['is_full'] = ($occ >= $cap && $cap > 0);
            $room['is_overbooked'] = ($occ > $cap);

            // Calculate KPI aggregates
            $totalRoomsInUse++;
            $totalBedsCapacity += $cap;
            $totalBedsOccupied += $occ;
            $totalBedsAvailable += $room['available_beds'];

            if ($room['is_overbooked']) {
                $overbookedRoomsCount++;
            } elseif ($room['is_full']) {
                $fullRoomsCount++;
            } else {
                $partialRoomsCount++;
            }
        }
        unset($room);

        // Filter by Status if requested
        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $groupedRooms = array_filter($groupedRooms, function ($room) use ($statusFilter) {
                if ($statusFilter === 'available') {
                    return !$room['is_full'] && !$room['is_overbooked'];
                } elseif ($statusFilter === 'full') {
                    return $room['is_full'] && !$room['is_overbooked'];
                } elseif ($statusFilter === 'overbooked') {
                    return $room['is_overbooked'];
                }
                return true;
            });
        }

        return compact(
            'groupedRooms',
            'totalRoomsInUse',
            'totalBedsCapacity',
            'totalBedsOccupied',
            'totalBedsAvailable',
            'fullRoomsCount',
            'partialRoomsCount',
            'overbookedRoomsCount',
            'hotelFilter',
            'locationFilter',
            'roomTypeFilter',
            'statusFilter',
            'search',
            'fromDate',
            'toDate',
            'viewMode'
        );
    }

    /**
     * Adjust Bed Capacity for a Room (Increase beds, add extra beds)
     */
    public function adjustBed(Request $request)
    {
        $request->validate([
            'hotel_name'   => 'required|string|max:191',
            'room_number'  => 'required|string|max:100',
            'bed_capacity' => 'required|integer|min:1|max:50',
            'extra_beds'   => 'nullable|integer|min:0|max:20',
            'notes'        => 'nullable|string|max:500',
        ]);

        $hotelName = trim($request->hotel_name);
        $roomNumber = trim($request->room_number);
        $bedCapacity = (int) $request->bed_capacity;
        $extraBeds = (int) ($request->extra_beds ?? 0);
        $roomType = $request->room_type ? trim($request->room_type) : null;
        $location = $request->location ? trim($request->location) : null;
        $gender = $request->gender ? trim($request->gender) : 'Any';

        $record = HotelRoomCapacity::whereRaw('LOWER(TRIM(hotel_name)) = ?', [strtolower($hotelName)])
            ->whereRaw('LOWER(TRIM(room_number)) = ?', [strtolower($roomNumber)])
            ->first();

        if (!$record) {
            $record = new HotelRoomCapacity();
            $record->hotel_name = $hotelName;
            $record->room_number = $roomNumber;
        }

        $record->room_type = $roomType;
        $record->location = $location;
        $record->gender = $gender;
        $record->bed_capacity = $bedCapacity;
        $record->extra_beds = $extraBeds;
        $record->notes = $request->notes;
        $record->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Bed capacity for Room {$roomNumber} ({$hotelName}) updated to {$bedCapacity} beds successfully!",
                'data'    => $record,
            ]);
        }

        return back()->with('success', "Bed capacity for Room {$roomNumber} ({$hotelName}) updated to {$bedCapacity} beds successfully!");
    }

    /**
     * Real-time Live Capacity Check API for Booking Create & Edit
     */
    public function checkCapacityApi(Request $request)
    {
        $hotelName = $request->get('hotel_name');
        $roomNumber = $request->get('room_number');
        $roomType = $request->get('room_type');
        $checkIn = $request->get('check_in');
        $checkOut = $request->get('check_out');
        $excludeBookingId = $request->get('booking_id');
        $location = $request->get('location');

        if (empty($roomNumber) || (empty($hotelName) && empty($location))) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide hotel name and room number.',
            ]);
        }

        $occupancy = HotelRoomCapacity::getRoomOccupancy(
            $hotelName,
            $roomNumber,
            $roomType,
            $checkIn,
            $checkOut,
            $excludeBookingId,
            $location
        );

        $cap = $occupancy['capacity'];
        $occ = $occupancy['occupied_beds'];
        $avail = $occupancy['available_beds'];
        $isFull = $occupancy['is_full'];
        $isOver = $occupancy['is_overbooked'];

        $occupantsNames = array_column($occupancy['occupants'], 'name');
        $summaryNames = implode(', ', array_slice($occupantsNames, 0, 3));
        if (count($occupantsNames) > 3) {
            $summaryNames .= ' +' . (count($occupantsNames) - 3) . ' more';
        }

        $message = '';
        if ($isOver) {
            $message = "⚠️ OVERBOOKED: Room {$roomNumber} has {$occ}/{$cap} beds allocated ({$summaryNames}). No more pilgrims can be assigned to this room.";
        } elseif ($isFull) {
            $message = "🔴 ROOM IS FULL: Room {$roomNumber} is at maximum capacity ({$occ}/{$cap} beds occupied by {$summaryNames}). You cannot add more pilgrims to this room unless bed capacity is increased in the Rooming List.";
        } elseif ($occ > 0) {
            $message = "🟡 Room {$roomNumber}: {$occ}/{$cap} beds occupied ({$avail} beds available). Current occupants: {$summaryNames}.";
        } else {
            // Check if current booking itself occupies this room (Edit Mode clarity)
            if ($excludeBookingId) {
                $selfOcc = HotelRoomCapacity::getRoomOccupancy($hotelName, $roomNumber, $roomType, $checkIn, $checkOut, null, $location);
                if ($selfOcc['occupied_beds'] > 0) {
                    $selfNames = implode(', ', array_slice(array_column($selfOcc['occupants'], 'name'), 0, 3));
                    $message = "🔵 Room {$roomNumber}: {$selfOcc['occupied_beds']}/{$cap} beds assigned to this current booking ({$selfNames}).";
                } else {
                    $message = "🟢 Room {$roomNumber}: Fully available ({$cap} total beds).";
                }
            } else {
                $message = "🟢 Room {$roomNumber}: Fully available ({$cap} total beds).";
            }
        }

        return response()->json([
            'success'           => true,
            'room_number'       => $roomNumber,
            'hotel_name'        => $hotelName,
            'location'          => $location,
            'room_type'         => $roomType,
            'capacity'          => $cap,
            'occupied'          => $occ,
            'available'         => $avail,
            'is_full'           => $isFull,
            'is_overbooked'     => $isOver,
            'occupants_count'   => $occupancy['occupants_count'],
            'occupants_summary' => $summaryNames,
            'message'           => $message,
        ]);
    }
}
