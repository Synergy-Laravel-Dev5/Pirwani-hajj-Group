<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Company;
use App\Models\Package;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index()
    {
        $package = session('dashboard_package', 'hajj');
        $year    = (int) session('dashboard_year', Carbon::now()->year);

        $bookings = Booking::with(['client', 'company', 'package'])
            ->latest()
            ->get();

        $trashCount = Booking::onlyTrashed()->count();

        return view('booking.index', compact('bookings', 'trashCount', 'package', 'year'));
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

        return view('booking.create', compact('clients', 'companies', 'years', 'airlines', 'hotels', 'roomTypes', 'packages'));
    }

    public function store(Request $request)
    {
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
                $checkIn = $hotel['check_in'] ?? null;
                $checkOut = $hotel['check_out'] ?? null;

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

        $total = (($request->package_cost ?? 0) * ($request->no_of_pax ?? 1))
            + ($request->visa_charges ?? 0)
            + ($request->flight_charges ?? 0)
            + ($request->other_charges ?? 0)
            + ($request->qurbani_charges ?? 0)
            - ($request->discount ?? 0);

        $booking = Booking::create(array_merge(
            $request->except(['persons', 'hotels', 'transports', 'visas', 'flight_persons', '_token']),
            [
                'package_id'      => $request->package_id ?: null,
                'client_id'       => $clientId,
                'company_id'      => $companyId,
                'package_type'    => $request->package_type ?? 'hajj',
                'camp'            => $request->camp,
                'room_breakdown'  => $request->room_breakdown ?: null,
                'qurbani_option'  => $request->qurbani_option ?? 'not_included',
                'qurbani_qty'     => $request->qurbani_qty ?? 0,
                'qurbani_charges' => $request->qurbani_charges ?? 0,
                'package_cost'    => $request->package_cost ?? 0,
                'visa_charges'    => $request->visa_charges ?? 0,
                'flight_charges'  => $request->flight_charges ?? 0,
                'other_charges'   => $request->other_charges ?? 0,
                'discount'        => $request->discount ?? 0,
                'total_received'  => $request->total_received ?? 0,
                'total_amount'    => $total,
                'balance'         => $total - ($request->total_received ?? 0),
            ]
        ));

        if ($request->has('persons')) {
            $uploadDir = public_path('uploads/booking_persons');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($request->persons as $index => $person) {
                $name = !empty($person['full_name']) ? trim($person['full_name']) : trim(($person['given_name'] ?? '') . ' ' . ($person['surname'] ?? ''));
                if (!empty($name) || !empty($person['passport_number'])) {
                    $personData = $person;
                    $personData['full_name'] = $name ?: 'Passenger';
                    $issueDate = !empty($person['date_of_issue']) ? $person['date_of_issue'] : (!empty($person['passport_issue_date']) ? $person['passport_issue_date'] : null);
                    $personData['date_of_issue'] = $issueDate;
                    $personData['passport_issue_date'] = $issueDate;

                    if ($request->hasFile("persons.{$index}.photo")) {
                        $file = $request->file("persons.{$index}.photo");
                        $filename = time() . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($uploadDir, $filename);
                        $personData['photo'] = 'uploads/booking_persons/' . $filename;
                    }

                    unset($personData['existing_photo']);
                    $booking->persons()->create($personData);
                }
            }
        }

        if ($request->has('hotels')) {
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
                    $booking->hotels()->create($hotelData);
                }
            }
        }

        if ($request->has('transports')) {
            foreach ($request->transports as $transport) {
                if (!empty($transport['route'])) {
                    $tData = $transport;
                    $tData['transport_type'] = !empty($transport['transport_type']) ? $transport['transport_type'] : 'bus';
                    $tData['notes'] = $transport['notes'] ?? null;
                    $booking->transports()->create($tData);
                }
            }
        }

        if ($request->has('visas')) {
            foreach ($request->visas as $visa) {
                if (!empty($visa['passport_number']) || !empty($visa['given_name'])) {
                    $vData = $visa;
                    $vData['status'] = !empty($visa['status']) ? $visa['status'] : 'pending';
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

        return redirect()->route('booking.index')->with('success', 'Booking created successfully!');
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

        return view('booking.edit', compact('booking', 'clients', 'companies', 'years', 'transactionsPaid', 'airlines', 'hotels', 'roomTypes', 'packages'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

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
                $checkIn = $h['check_in'] ?? null;
                $checkOut = $h['check_out'] ?? null;

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

        $total = (($request->package_cost ?? 0) * ($request->no_of_pax ?? 1))
            + ($request->visa_charges ?? 0)
            + ($request->flight_charges ?? 0)
            + ($request->other_charges ?? 0)
            + ($request->qurbani_charges ?? 0)
            - ($request->discount ?? 0);

        $booking->update(array_merge(
            $request->except(['persons', 'hotels', 'transports', 'visas', 'flight_persons', '_token', '_method']),
            [
                'package_id'      => $request->package_id ?: null,
                'client_id'       => $clientId,
                'company_id'      => $companyId,
                'package_type'    => $request->package_type ?? ($booking->package_type ?? 'hajj'),
                'camp'            => $request->camp,
                'room_breakdown'  => $request->room_breakdown ?: null,
                'qurbani_option'  => $request->qurbani_option ?? 'not_included',
                'qurbani_qty'     => $request->qurbani_qty ?? 0,
                'qurbani_charges' => $request->qurbani_charges ?? 0,
                'package_cost'    => $request->package_cost ?? 0,
                'visa_charges'    => $request->visa_charges ?? 0,
                'flight_charges'  => $request->flight_charges ?? 0,
                'other_charges'   => $request->other_charges ?? 0,
                'discount'        => $request->discount ?? 0,
                'total_received'  => $request->total_received ?? 0,
                'total_amount'    => $total,
                'balance'         => $total - ($request->total_received ?? 0),
            ]
        ));

        $booking->persons()->delete();
        if ($request->has('persons')) {
            $uploadDir = public_path('uploads/booking_persons');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($request->persons as $index => $p) {
                $name = !empty($p['full_name']) ? trim($p['full_name']) : trim(($p['given_name'] ?? '') . ' ' . ($p['surname'] ?? ''));
                if (!empty($name) || !empty($p['passport_number'])) {
                    $pData = $p;
                    $pData['full_name'] = $name ?: 'Passenger';
                    $issueDate = !empty($p['date_of_issue']) ? $p['date_of_issue'] : (!empty($p['passport_issue_date']) ? $p['passport_issue_date'] : null);
                    $pData['date_of_issue'] = $issueDate;
                    $pData['passport_issue_date'] = $issueDate;

                    if ($request->hasFile("persons.{$index}.photo")) {
                        $file = $request->file("persons.{$index}.photo");
                        $filename = time() . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($uploadDir, $filename);
                        $pData['photo'] = 'uploads/booking_persons/' . $filename;
                    } elseif (!empty($p['existing_photo'])) {
                        $pData['photo'] = $p['existing_photo'];
                    }

                    unset($pData['existing_photo']);
                    $booking->persons()->create($pData);
                }
            }
        }

        $booking->hotels()->delete();
        if ($request->has('hotels')) {
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
                    $booking->hotels()->create($hData);
                }
            }
        }

        $booking->transports()->delete();
        if ($request->has('transports')) {
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
        if ($request->has('visas')) {
            foreach ($request->visas as $v) {
                if (!empty($v['passport_number']) || !empty($v['given_name'])) {
                    $vData = $v;
                    $vData['status'] = !empty($v['status']) ? $v['status'] : 'pending';
                    $booking->visas()->create($vData);
                }
            }
        }

        logUserActivity('Booking Updated', 'Package: ' . $booking->package_type . ' | Total: ' . $booking->total_amount, $booking->id, 'Booking');

        return redirect()->route('booking.index')->with('success', 'Booking updated successfully!');
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
}
