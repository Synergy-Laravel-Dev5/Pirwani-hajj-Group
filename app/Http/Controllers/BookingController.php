<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Company;
use App\Models\Package;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class BookingController extends Controller
{
    public function index()
    {
        $package = session('dashboard_package', 'hajj');
        $year    = (int) session('dashboard_year', Carbon::now()->year);

        $bookings = Booking::with(['client', 'company', 'package', 'persons'])
            ->orderBy('id', 'asc')
            ->get();

        $trashCount = Booking::onlyTrashed()->count();

        $totalPax = $bookings->sum(function ($b) {
            return max($b->persons->count(), (int)($b->no_of_pax ?: 0));
        });

        return view('booking.index', compact('bookings', 'trashCount', 'package', 'year', 'totalPax'));
    }

    public function create()
    {
        $clients   = Client::where('status', 'active')->get();
        $companies = Company::all();
        $years     = [date('Y'), date('Y') + 1, date('Y') + 2];
        $airlines  = \App\Models\Airline::all();
        $hotels    = \App\Models\Hotel::all();
        $roomTypes = \App\Models\RoomType::where('status', 'active')->orderBy('capacity')->get();
        $packages  = Package::with(['accommodations', 'transportFlights', 'transports', 'transportTrains'])->latest()->get();
        $nextHbNumber = \App\Models\BookingPerson::generateNextHbNumber();
        $nextHajjId   = \App\Models\BookingPerson::generateNextHajjId();
        $vehicles  = \App\Models\Vehicle::where('status', 'active')->orderBy('vehicle_type')->orderBy('brand_name')->get();
        if ($vehicles->isEmpty()) {
            $vehicles = \App\Models\Vehicle::all();
        }
        $routes = \App\Models\Route::where('status', 'active')->orderBy('start_place')->get();
        if ($routes->isEmpty()) {
            $routes = \App\Models\Route::all();
        }
        $travelRoutes = \App\Models\TravelRoute::where('status', 'active')->orderBy('name')->get();
        if ($travelRoutes->isEmpty()) {
            $travelRoutes = \App\Models\TravelRoute::all();
        }

        return view('booking.create', compact('clients', 'companies', 'years', 'airlines', 'hotels', 'roomTypes', 'packages', 'nextHbNumber', 'nextHajjId', 'vehicles', 'routes', 'travelRoutes'));
    }

    public function store(Request $request)
    {
        // Sanitize package_type to lowercase before validation
        if ($request->has('package_type')) {
            $request->merge(['package_type' => strtolower(trim((string)$request->package_type)) ?: 'hajj']);
        }

        $request->validate([
            'booking_for'     => 'required|in:client,company',
            'client_id'       => 'required_if:booking_for,client|nullable|exists:clients,id',
            'company_id'      => 'required_if:booking_for,company|nullable|exists:companies,id',
            'package_id'      => 'nullable|exists:packages,id',
            'package_type'    => 'nullable|in:umrah,hajj,other',
            'camp'            => 'nullable|string|max:150',
            'qurbani_option'  => 'nullable|string|max:100',
            'qurbani_qty'     => 'nullable|integer|min:0',
            'qurbani_charges' => 'nullable|numeric|min:0',
        ]);

        // Calculate actual booking pax count
        $paxCount = 0;
        if ($request->has('persons') && is_array($request->persons)) {
            foreach ($request->persons as $p) {
                $name = !empty($p['full_name']) ? trim($p['full_name']) : trim(($p['given_name'] ?? '') . ' ' . ($p['surname'] ?? ''));
                if (!empty($name) || !empty($p['passport_number'])) {
                    $paxCount++;
                }
            }
        }
        if ($paxCount === 0) {
            $paxCount = max(1, (int) ($request->no_of_pax ?: 1));
        }

        // Validate Room Capacities - Block if room is full
        if ($request->has('hotels') && is_array($request->hotels)) {
            foreach ($request->hotels as $hotel) {
                $hotelName = trim($hotel['hotel_name'] ?? '');
                $roomNumber = trim($hotel['room_number'] ?? '');
                $roomType = $hotel['room_type'] ?? null;
                $checkIn = !empty($hotel['check_in']) ? $hotel['check_in'] : null;
                $checkOut = !empty($hotel['check_out']) ? $hotel['check_out'] : null;
                $location = $hotel['location'] ?? null;

                if (!empty($hotelName) && !empty($roomNumber)) {
                    $occ = \App\Models\HotelRoomCapacity::getRoomOccupancy($hotelName, $roomNumber, $roomType, $checkIn, $checkOut, null, $location);

                    if (($occ['occupied_beds'] + $paxCount) > $occ['capacity']) {
                        $available = max(0, $occ['capacity'] - $occ['occupied_beds']);
                        $errorMsg = "Cannot create booking: Room {$roomNumber} at {$hotelName} is FULL! (Capacity: {$occ['capacity']} beds, Already Booked: {$occ['occupied_beds']} beds, Available: {$available} beds, Booking Pax: {$paxCount}). Please select a different room or increase bed capacity in the Rooming List report.";
                        return back()->withInput()->with('error', $errorMsg);
                    }
                }
            }
        }

        $clientId  = $request->booking_for === 'client'  ? $request->client_id  : null;
        $companyId = $request->booking_for === 'company' ? $request->company_id : null;

        $packageCost    = (float) ($request->package_cost ?? 0);
        $noOfPax        = max(1, (int) ($request->no_of_pax ?: 1));
        $visaCharges    = (float) ($request->visa_charges ?? 0);
        $flightCharges  = (float) ($request->flight_charges ?? 0);
        $otherCharges   = (float) ($request->other_charges ?? 0);
        $qurbaniCharges = (float) ($request->qurbani_charges ?? 0);
        $discount       = (float) ($request->discount ?? 0);
        $totalReceived  = (float) ($request->total_received ?? 0);

        $total = ($packageCost * $noOfPax)
            + $visaCharges
            + $flightCharges
            + $otherCharges
            + $qurbaniCharges
            - $discount;
        $balance = $total - $totalReceived;

        $bookingData = array_merge(
            $request->except(['persons', 'hotels', 'transports', 'visas', 'flight_persons', '_token']),
            [
                'package_id'      => $request->package_id ?: null,
                'client_id'       => $clientId,
                'company_id'      => $companyId,
                'package_type'    => $request->package_type ?? 'hajj',
                'camp'            => $request->camp ?: null,
                'room_breakdown'  => $request->room_breakdown ?: null,
                'qurbani_option'  => $request->qurbani_option ?? 'not_included',
                'qurbani_qty'     => (int) ($request->qurbani_qty ?? 0),
                'qurbani_charges' => $qurbaniCharges,
                'package_cost'    => $packageCost,
                'no_of_pax'       => $noOfPax,
                'visa_charges'    => $visaCharges,
                'flight_charges'  => $flightCharges,
                'other_charges'   => $otherCharges,
                'discount'        => $discount,
                'total_received'  => $totalReceived,
                'total_amount'    => $total,
                'balance'         => $balance,
                'remarks'         => $request->remarks ?: null,
                'departure_date'  => !empty($request->departure_date) ? $request->departure_date : null,
                'arrival_date'    => !empty($request->arrival_date) ? $request->arrival_date : null,
                'departure_time'  => !empty($request->departure_time) ? $request->departure_time : null,
                'arrival_time'    => !empty($request->arrival_time) ? $request->arrival_time : null,
                'visa_dob'        => !empty($request->visa_dob) ? $request->visa_dob : null,
            ]
        );

        $plainAppPassword = '123456@';
        $bookingData['app_password'] = \Illuminate\Support\Facades\Hash::make($plainAppPassword);
        $bookingData['app_password_plain'] = $plainAppPassword;

        DB::beginTransaction();
        try {
            $booking = Booking::create($bookingData);

            if ($request->has('persons') && is_array($request->persons)) {
                $uploadDir = public_path('uploads/booking_persons');
                if (!file_exists($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }

                // Determine unified HB number for all persons in this booking
                $bookingHbNumber = null;
                foreach ($request->persons as $p) {
                    if (!empty($p['hb_number'])) {
                        $bookingHbNumber = \App\Models\BookingPerson::formatHbNumber($p['hb_number']);
                        break;
                    }
                }
                if (empty($bookingHbNumber)) {
                    $bookingHbNumber = \App\Models\BookingPerson::generateNextHbNumber();
                }

                foreach ($request->persons as $index => $person) {
                    $name = !empty($person['full_name']) ? trim($person['full_name']) : trim(($person['given_name'] ?? '') . ' ' . ($person['surname'] ?? ''));
                    if (!empty($name) || !empty($person['passport_number'])) {
                        $personData = $person;
                        $personData['full_name'] = $name ?: 'Passenger';
                        $personData['given_name'] = !empty($person['given_name']) ? trim($person['given_name']) : null;
                        $personData['surname'] = !empty($person['surname']) ? trim($person['surname']) : null;
                        $personData['hb_number'] = $bookingHbNumber;
                        if (!empty($person['hajj_id'])) {
                            $personData['hajj_id'] = \App\Models\BookingPerson::formatHajjId($person['hajj_id']);
                        } else {
                            $personData['hajj_id'] = \App\Models\BookingPerson::generateNextHajjId();
                        }
                        $issueDate = !empty($person['date_of_issue']) ? $person['date_of_issue'] : (!empty($person['passport_issue_date']) ? $person['passport_issue_date'] : null);
                        $personData['date_of_issue'] = $issueDate;
                        $personData['passport_issue_date'] = $issueDate;
                        $personData['dob'] = !empty($person['dob']) ? $person['dob'] : null;
                        $personData['passport_expiry_date'] = !empty($person['passport_expiry_date']) ? $person['passport_expiry_date'] : null;

                        $personData['ticket_status'] = !empty($person['ticket_status']) ? $person['ticket_status'] : 'on_ticket';

                        if ($request->hasFile("persons.{$index}.photo")) {
                            try {
                                $file = $request->file("persons.{$index}.photo");
                                $filename = time() . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                $file->move($uploadDir, $filename);
                                $personData['photo'] = 'uploads/booking_persons/' . $filename;
                            } catch (\Throwable $fe) {
                                Log::warning('Person photo upload skipped: ' . $fe->getMessage());
                            }
                        }

                        if ($request->hasFile("persons.{$index}.passport_image")) {
                            try {
                                $pFile = $request->file("persons.{$index}.passport_image");
                                $pFilename = 'pass_' . time() . '_' . $index . '_' . uniqid() . '.' . $pFile->getClientOriginalExtension();
                                $pFile->move($uploadDir, $pFilename);
                                $personData['passport_image'] = 'uploads/booking_persons/' . $pFilename;
                            } catch (\Throwable $pe) {
                                Log::warning('Person passport upload skipped: ' . $pe->getMessage());
                            }
                        }

                        unset($personData['existing_photo'], $personData['existing_passport_image'], $personData['father_name']);
                        $booking->persons()->create($personData);
                    }
                }
            }

            if ($request->has('hotels') && is_array($request->hotels)) {
                foreach ($request->hotels as $hotel) {
                    if (!empty($hotel['hotel_name'])) {
                        $hotelData = $hotel;
                        $hotelData['no_of_rooms'] = (!empty($hotel['no_of_rooms']) && is_numeric($hotel['no_of_rooms'])) ? (int)$hotel['no_of_rooms'] : 1;
                        $hotelData['no_of_nights'] = (!empty($hotel['no_of_nights']) && is_numeric($hotel['no_of_nights'])) ? (int)$hotel['no_of_nights'] : 1;
                        $hotelData['check_in'] = !empty($hotel['check_in']) ? $hotel['check_in'] : null;
                        $hotelData['check_out'] = !empty($hotel['check_out']) ? $hotel['check_out'] : null;
                        $hotelData['room_type'] = !empty($hotel['room_type']) ? $hotel['room_type'] : 'quad';
                        $hotelData['room_number'] = !empty($hotel['room_number']) ? trim($hotel['room_number']) : null;
                        $hotelData['gender'] = !empty($hotel['gender']) ? trim($hotel['gender']) : 'Any';
                        $hotelData['location'] = !empty($hotel['location']) ? $hotel['location'] : 'makkah';
                        $hotelData['allotment_basis'] = !empty($hotel['allotment_basis']) ? $hotel['allotment_basis'] : 'room';
                        $hotelData['meal_plan'] = !empty($hotel['meal_plan']) ? $hotel['meal_plan'] : 'Half Board';
                        $hotelData['hijri_check_in'] = !empty($hotel['hijri_check_in']) ? trim($hotel['hijri_check_in']) : null;
                        $hotelData['hijri_check_out'] = !empty($hotel['hijri_check_out']) ? trim($hotel['hijri_check_out']) : null;
                        if ($hotelData['allotment_basis'] === 'bed') {
                            $hotelData['no_of_beds'] = (!empty($hotel['no_of_beds']) && is_numeric($hotel['no_of_beds'])) ? (int)$hotel['no_of_beds'] : (int)$hotelData['no_of_rooms'];
                        } else {
                            $hotelData['no_of_beds'] = (!empty($hotel['no_of_beds']) && is_numeric($hotel['no_of_beds'])) ? (int)$hotel['no_of_beds'] : 0;
                        }
                        unset($hotelData['hotel_voucher']);
                        $booking->hotels()->create($hotelData);
                    }
                }
            }

            if ($request->has('transports') && is_array($request->transports)) {
                foreach ($request->transports as $transport) {
                    if (!empty($transport['route'])) {
                        $tData = $transport;
                        $tData['transport_type'] = !empty($transport['transport_type']) ? $transport['transport_type'] : 'bus';
                        $tData['notes'] = $transport['notes'] ?? null;
                        $booking->transports()->create($tData);
                    }
                }
            }

            if ($request->has('visas') && is_array($request->visas)) {
                foreach ($request->visas as $visa) {
                    if (!empty($visa['passport_number']) || !empty($visa['given_name'])) {
                        $vData = $visa;
                        $vData['status'] = !empty($visa['status']) ? $visa['status'] : 'pending';
                        $vData['date_of_birth'] = !empty($visa['date_of_birth']) ? $visa['date_of_birth'] : null;
                        $booking->visas()->create($vData);
                    }
                }
            }

            logUserActivity(
                'Booking Created',
                'Type: ' . $request->booking_for . ' | Package: ' . $booking->package_type . ' | Total: ' . $booking->total_amount,
                $booking->id,
                'Booking'
            );

            DB::commit();

            return redirect()->route('booking.index')->with('success', 'Booking created successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Booking store error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace'     => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Booking save failed: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $booking = Booking::with(['client', 'company', 'package', 'persons', 'hotels', 'transports', 'visas'])->findOrFail($id);
        return view('booking.show', compact('booking'));
    }

    public function edit($id)
    {
        $booking   = Booking::with(['package', 'persons', 'hotels', 'transports', 'visas'])->findOrFail($id);
        $clients   = Client::where('status', 'active')->get();
        $companies = Company::all();
        $years     = [date('Y'), date('Y') + 1, date('Y') + 2];
        $airlines  = \App\Models\Airline::all();
        $hotels    = \App\Models\Hotel::all();
        $roomTypes = \App\Models\RoomType::where('status', 'active')->orderBy('capacity')->get();
        $packages  = Package::with(['accommodations', 'transportFlights', 'transports', 'transportTrains'])->latest()->get();

        $transactionsPaid = Transaction::where('client_id', $booking->client_id)
            ->where('status', 'confirmed')
            ->sum('amount');

        $bookingHbNumber = $booking->persons->pluck('hb_number')->filter()->first();
        if (empty($bookingHbNumber)) {
            $bookingHbNumber = \App\Models\BookingPerson::generateNextHbNumber($booking->id);
        }
        $vehicles  = \App\Models\Vehicle::where('status', 'active')->orderBy('vehicle_type')->orderBy('brand_name')->get();
        if ($vehicles->isEmpty()) {
            $vehicles = \App\Models\Vehicle::all();
        }
        $routes = \App\Models\Route::where('status', 'active')->orderBy('start_place')->get();
        if ($routes->isEmpty()) {
            $routes = \App\Models\Route::all();
        }
        $travelRoutes = \App\Models\TravelRoute::where('status', 'active')->orderBy('name')->get();
        if ($travelRoutes->isEmpty()) {
            $travelRoutes = \App\Models\TravelRoute::all();
        }

        return view('booking.edit', compact('booking', 'clients', 'companies', 'years', 'transactionsPaid', 'airlines', 'hotels', 'roomTypes', 'packages', 'bookingHbNumber', 'vehicles', 'routes', 'travelRoutes'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Sanitize package_type to lowercase before validation
        if ($request->has('package_type')) {
            $request->merge(['package_type' => strtolower(trim((string)$request->package_type)) ?: 'hajj']);
        }

        $request->validate([
            'booking_for'     => 'required|in:client,company',
            'client_id'       => 'required_if:booking_for,client|nullable|exists:clients,id',
            'company_id'      => 'required_if:booking_for,company|nullable|exists:companies,id',
            'package_id'      => 'nullable|exists:packages,id',
            'package_type'    => 'nullable|in:umrah,hajj,other',
            'camp'            => 'nullable|string|max:150',
            'qurbani_option'  => 'nullable|string|max:100',
            'qurbani_qty'     => 'nullable|integer|min:0',
            'qurbani_charges' => 'nullable|numeric|min:0',
        ]);

        // Calculate actual booking pax count
        $paxCount = 0;
        if ($request->has('persons') && is_array($request->persons)) {
            foreach ($request->persons as $p) {
                $name = !empty($p['full_name']) ? trim($p['full_name']) : trim(($p['given_name'] ?? '') . ' ' . ($p['surname'] ?? ''));
                if (!empty($name) || !empty($p['passport_number'])) {
                    $paxCount++;
                }
            }
        }
        if ($paxCount === 0) {
            $paxCount = max(1, (int) ($request->no_of_pax ?: 1));
        }

        // Validate Room Capacities - Block if room is full
        if ($request->has('hotels') && is_array($request->hotels)) {
            foreach ($request->hotels as $h) {
                $hotelName = trim($h['hotel_name'] ?? '');
                $roomNumber = trim($h['room_number'] ?? '');
                $roomType = $h['room_type'] ?? null;
                $checkIn = !empty($h['check_in']) ? $h['check_in'] : null;
                $checkOut = !empty($h['check_out']) ? $h['check_out'] : null;
                $location = $h['location'] ?? null;

                if (!empty($hotelName) && !empty($roomNumber)) {
                    $occ = \App\Models\HotelRoomCapacity::getRoomOccupancy($hotelName, $roomNumber, $roomType, $checkIn, $checkOut, $booking->id, $location);

                    if (($occ['occupied_beds'] + $paxCount) > $occ['capacity']) {
                        $available = max(0, $occ['capacity'] - $occ['occupied_beds']);
                        $errorMsg = "Cannot update booking: Room {$roomNumber} at {$hotelName} is FULL! (Capacity: {$occ['capacity']} beds, Already Booked: {$occ['occupied_beds']} beds, Available: {$available} beds, Booking Pax: {$paxCount}). Please select a different room or increase bed capacity in the Rooming List report.";
                        return back()->withInput()->with('error', $errorMsg);
                    }
                }
            }
        }

        $clientId  = $request->booking_for === 'client'  ? $request->client_id  : null;
        $companyId = $request->booking_for === 'company' ? $request->company_id : null;

        $packageCost    = (float) ($request->package_cost ?? 0);
        $noOfPax        = max(1, (int) ($request->no_of_pax ?: 1));
        $visaCharges    = (float) ($request->visa_charges ?? 0);
        $flightCharges  = (float) ($request->flight_charges ?? 0);
        $otherCharges   = (float) ($request->other_charges ?? 0);
        $qurbaniCharges = (float) ($request->qurbani_charges ?? 0);
        $discount       = (float) ($request->discount ?? 0);
        $totalReceived  = (float) ($request->total_received ?? 0);

        $total = ($packageCost * $noOfPax)
            + $visaCharges
            + $flightCharges
            + $otherCharges
            + $qurbaniCharges
            - $discount;
        $balance = $total - $totalReceived;

        $bookingData = array_merge(
            $request->except(['persons', 'hotels', 'transports', 'visas', 'flight_persons', '_token', '_method']),
            [
                'package_id'      => $request->package_id ?: null,
                'client_id'       => $clientId,
                'company_id'      => $companyId,
                'package_type'    => $request->package_type ?? ($booking->package_type ?? 'hajj'),
                'camp'            => $request->camp ?: null,
                'room_breakdown'  => $request->room_breakdown ?: null,
                'qurbani_option'  => $request->qurbani_option ?? 'not_included',
                'qurbani_qty'     => (int) ($request->qurbani_qty ?? 0),
                'qurbani_charges' => $qurbaniCharges,
                'package_cost'    => $packageCost,
                'no_of_pax'       => $noOfPax,
                'visa_charges'    => $visaCharges,
                'flight_charges'  => $flightCharges,
                'other_charges'   => $otherCharges,
                'discount'        => $discount,
                'total_received'  => $totalReceived,
                'total_amount'    => $total,
                'balance'         => $balance,
                'remarks'         => $request->remarks ?: null,
                'departure_date'  => !empty($request->departure_date) ? $request->departure_date : null,
                'arrival_date'    => !empty($request->arrival_date) ? $request->arrival_date : null,
                'departure_time'  => !empty($request->departure_time) ? $request->departure_time : null,
                'arrival_time'    => !empty($request->arrival_time) ? $request->arrival_time : null,
                'visa_dob'        => !empty($request->visa_dob) ? $request->visa_dob : null,
            ]
        );

        DB::beginTransaction();
        try {
            $booking->update($bookingData);

            $booking->persons()->delete();
            if ($request->has('persons') && is_array($request->persons)) {
                $uploadDir = public_path('uploads/booking_persons');
                if (!file_exists($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }

                // Determine unified HB number for all persons in this booking
                $bookingHbNumber = null;
                foreach ($request->persons as $p) {
                    if (!empty($p['hb_number'])) {
                        $bookingHbNumber = \App\Models\BookingPerson::formatHbNumber($p['hb_number']);
                        break;
                    }
                }
                if (empty($bookingHbNumber)) {
                    $bookingHbNumber = $booking->persons()->whereNotNull('hb_number')->where('hb_number', '!=', '')->value('hb_number');
                }
                if (empty($bookingHbNumber)) {
                    $bookingHbNumber = \App\Models\BookingPerson::generateNextHbNumber($booking->id);
                }
                $bookingHbNumber = \App\Models\BookingPerson::formatHbNumber($bookingHbNumber);

                foreach ($request->persons as $index => $p) {
                    $name = !empty($p['full_name']) ? trim($p['full_name']) : trim(($p['given_name'] ?? '') . ' ' . ($p['surname'] ?? ''));
                    if (!empty($name) || !empty($p['passport_number'])) {
                        $pData = $p;
                        $pData['full_name'] = $name ?: 'Passenger';
                        $pData['given_name'] = !empty($p['given_name']) ? trim($p['given_name']) : null;
                        $pData['surname'] = !empty($p['surname']) ? trim($p['surname']) : null;
                        $pData['hb_number'] = $bookingHbNumber;
                        if (!empty($p['hajj_id'])) {
                            $pData['hajj_id'] = \App\Models\BookingPerson::formatHajjId($p['hajj_id']);
                        } else {
                            $pData['hajj_id'] = \App\Models\BookingPerson::generateNextHajjId();
                        }
                        $issueDate = !empty($p['date_of_issue']) ? $p['date_of_issue'] : (!empty($p['passport_issue_date']) ? $p['passport_issue_date'] : null);
                        $pData['date_of_issue'] = $issueDate;
                        $pData['passport_issue_date'] = $issueDate;
                        $pData['dob'] = !empty($p['dob']) ? $p['dob'] : null;
                        $pData['passport_expiry_date'] = !empty($p['passport_expiry_date']) ? $p['passport_expiry_date'] : null;

                        $pData['ticket_status'] = !empty($p['ticket_status']) ? $p['ticket_status'] : 'on_ticket';

                        if ($request->hasFile("persons.{$index}.photo")) {
                            try {
                                $file = $request->file("persons.{$index}.photo");
                                $filename = time() . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                $file->move($uploadDir, $filename);
                                $pData['photo'] = 'uploads/booking_persons/' . $filename;
                            } catch (\Throwable $fe) {
                                Log::warning('Person photo upload skipped on update: ' . $fe->getMessage());
                            }
                        } elseif (!empty($p['existing_photo'])) {
                            $pData['photo'] = $p['existing_photo'];
                        }

                        if ($request->hasFile("persons.{$index}.passport_image")) {
                            try {
                                $pFile = $request->file("persons.{$index}.passport_image");
                                $pFilename = 'pass_' . time() . '_' . $index . '_' . uniqid() . '.' . $pFile->getClientOriginalExtension();
                                $pFile->move($uploadDir, $pFilename);
                                $pData['passport_image'] = 'uploads/booking_persons/' . $pFilename;
                            } catch (\Throwable $pe) {
                                Log::warning('Person passport upload skipped on update: ' . $pe->getMessage());
                            }
                        } elseif (!empty($p['existing_passport_image'])) {
                            $pData['passport_image'] = $p['existing_passport_image'];
                        }

                        unset($pData['existing_photo'], $pData['existing_passport_image']);
                        $booking->persons()->create($pData);
                    }
                }
            }

            $booking->hotels()->delete();
            if ($request->has('hotels') && is_array($request->hotels)) {
                foreach ($request->hotels as $h) {
                    if (!empty($h['hotel_name'])) {
                        $hData = $h;
                        $hData['no_of_rooms'] = (!empty($h['no_of_rooms']) && is_numeric($h['no_of_rooms'])) ? (int)$h['no_of_rooms'] : 1;
                        $hData['no_of_nights'] = (!empty($h['no_of_nights']) && is_numeric($h['no_of_nights'])) ? (int)$h['no_of_nights'] : 1;
                        $hData['check_in'] = !empty($h['check_in']) ? $h['check_in'] : null;
                        $hData['check_out'] = !empty($h['check_out']) ? $h['check_out'] : null;
                        $hData['room_type'] = !empty($h['room_type']) ? $h['room_type'] : 'quad';
                        $hData['room_number'] = !empty($h['room_number']) ? trim($h['room_number']) : null;
                        $hData['gender'] = !empty($h['gender']) ? trim($h['gender']) : 'Any';
                        $hData['location'] = !empty($h['location']) ? $h['location'] : 'makkah';
                        $hData['allotment_basis'] = !empty($h['allotment_basis']) ? $h['allotment_basis'] : 'room';
                        $hData['meal_plan'] = !empty($h['meal_plan']) ? $h['meal_plan'] : 'Half Board';
                        $hData['hijri_check_in'] = !empty($h['hijri_check_in']) ? trim($h['hijri_check_in']) : null;
                        $hData['hijri_check_out'] = !empty($h['hijri_check_out']) ? trim($h['hijri_check_out']) : null;
                        if ($hData['allotment_basis'] === 'bed') {
                            $hData['no_of_beds'] = (!empty($h['no_of_beds']) && is_numeric($h['no_of_beds'])) ? (int)$h['no_of_beds'] : (int)$hData['no_of_rooms'];
                        } else {
                            $hData['no_of_beds'] = (!empty($h['no_of_beds']) && is_numeric($h['no_of_beds'])) ? (int)$h['no_of_beds'] : 0;
                        }
                        unset($hData['hotel_voucher']);
                        $booking->hotels()->create($hData);
                    }
                }
            }

            $booking->transports()->delete();
            if ($request->has('transports') && is_array($request->transports)) {
                foreach ($request->transports as $t) {
                    if (!empty($t['route'])) {
                        $tData = $t;
                        $tData['transport_type'] = !empty($t['transport_type']) ? $t['transport_type'] : 'bus';
                        $tData['notes'] = $t['notes'] ?? null;
                        $booking->transports()->create($tData);
                    }
                }
            }

            $booking->visas()->delete();
            if ($request->has('visas') && is_array($request->visas)) {
                foreach ($request->visas as $v) {
                    if (!empty($v['passport_number']) || !empty($v['given_name'])) {
                        $vData = $v;
                        $vData['status'] = !empty($v['status']) ? $v['status'] : 'pending';
                        $vData['date_of_birth'] = !empty($v['date_of_birth']) ? $v['date_of_birth'] : null;
                        $booking->visas()->create($vData);
                    }
                }
            }

            logUserActivity('Booking Updated', 'Package: ' . $booking->package_type . ' | Total: ' . $booking->total_amount, $booking->id, 'Booking');

            DB::commit();

            return redirect()->route('booking.index')->with('success', 'Booking updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Booking update error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace'     => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Booking update failed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $booking = Booking::with(['client', 'company'])->findOrFail($id);

        logUserActivity(
            'Booking Deleted',
            'Client: ' . ($booking->client->name ?? $booking->company->name ?? 'N/A') . ' | Package: ' . $booking->package_type,
            $booking->id,
            'Booking'
        );

        $booking->delete();

        return redirect()->route('booking.index')->with('success', 'Moved to trash.');
    }

    public function trash()
    {
        $bookings = Booking::onlyTrashed()->with(['client', 'company'])->latest()->get();
        return view('booking.trash', compact('bookings'));
    }

    public function restore($id)
    {
        $booking = Booking::onlyTrashed()->with(['client', 'company'])->findOrFail($id);
        $booking->restore();

        logUserActivity(
            'Booking Restored',
            'Client: ' . ($booking->client->name ?? $booking->company->name ?? 'N/A') . ' | Package: ' . $booking->package_type,
            $booking->id,
            'Booking'
        );

        return redirect()->route('booking.index')->with('success', 'Booking restored.');
    }

    public function agreement(Booking $booking)
    {
        $booking->load(['client', 'company', 'persons', 'hotels', 'transports', 'visas']);
        return view('booking.agreement', compact('booking'));
    }

    public function saveAgreementSignature(Request $request)
    {
        $booking = Booking::findOrFail($request->booking_id);

        $signature = str_replace('data:image/png;base64,', '', $request->signature);
        $signature = str_replace(' ', '+', $signature);

        $fileName = 'signatures/' . uniqid() . '.png';

        Storage::disk('public')->put($fileName, base64_decode($signature));

        $booking->update(['agreement_signature' => $fileName]);

        return response()->json(['success' => true, 'path' => $fileName]);
    }

    public function voucher(Booking $booking)
    {
        $booking->load([
            'client',
            'company.addresses',
            'company.contactNumbers',
            'company.emails',
            'company.licenses',
            'package.accommodations',
            'package.transports',
            'package.transportFlights',
            'persons',
            'hotels',
            'transports',
            'visas'
        ]);
        return view('booking.voucher', compact('booking'));
    }

    /**
     * Delete a single person from booking (e.g. from booking details)
     */
    public function deletePerson($bookingId, $personId)
    {
        $booking = Booking::findOrFail($bookingId);
        $person = \App\Models\BookingPerson::where('booking_id', $booking->id)->where('id', $personId)->firstOrFail();

        if ($booking->persons()->count() <= 1) {
            return back()->with('error', 'Cannot remove passenger: A booking must have at least one passenger.');
        }

        $personName = $person->full_name;
        $person->delete();

        // Update pax count
        $newPax = max(1, $booking->persons()->count());
        $booking->no_of_pax = $newPax;

        // Recalculate total if package cost is set
        if (!empty($booking->package_cost) && $booking->package_cost > 0) {
            $total = ($booking->package_cost * $newPax)
                + ($booking->visa_charges ?? 0)
                + ($booking->flight_charges ?? 0)
                + ($booking->other_charges ?? 0)
                + ($booking->qurbani_charges ?? 0)
                - ($booking->discount ?? 0);
            $booking->total_amount = $total;
            $booking->balance = $total - ($booking->total_received ?? 0);
        }
        $booking->save();

        return back()->with('success', "Passenger {$personName} removed from booking successfully.");
    }
}
