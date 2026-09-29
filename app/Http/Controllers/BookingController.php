<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Company;
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

        $bookings = Booking::with(['client', 'company'])
            ->where('package_type', $package)
            ->where('package_year', $year)
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

        return view('booking.create', compact('clients', 'companies', 'years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_for'  => 'required|in:client,company',
            'client_id'    => 'required_if:booking_for,client|nullable|exists:clients,id',
            'company_id'   => 'required_if:booking_for,company|nullable|exists:companies,id',
            'package_type' => 'required|in:umrah,hajj,other',
        ]);

        $clientId  = $request->booking_for === 'client'  ? $request->client_id  : null;
        $companyId = $request->booking_for === 'company' ? $request->company_id : null;

        $total = (($request->package_cost ?? 0) * ($request->no_of_pax ?? 1))
            + ($request->visa_charges ?? 0)
            + ($request->flight_charges ?? 0)
            + ($request->other_charges ?? 0);

        $booking = Booking::create(array_merge(
            $request->except(['persons', 'hotels', 'transports', 'visas', 'flight_persons', '_token']),
            [
                'client_id'      => $clientId,
                'company_id'     => $companyId,
                'package_cost'   => $request->package_cost ?? 0,
                'visa_charges'   => $request->visa_charges ?? 0,
                'flight_charges' => $request->flight_charges ?? 0,
                'other_charges'  => $request->other_charges ?? 0,
                'total_received' => $request->total_received ?? 0,
                'total_amount'   => $total,
                'balance'        => $total - ($request->total_received ?? 0),
            ]
        ));

        if ($request->has('persons')) {
            foreach ($request->persons as $person) {
                if (!empty($person['full_name'])) {
                    $booking->persons()->create($person);
                }
            }
        }

        if ($request->has('hotels')) {
            foreach ($request->hotels as $hotel) {
                if (!empty($hotel['hotel_name'])) {
                    $booking->hotels()->create($hotel);
                }
            }
        }

        if ($request->has('transports')) {
            foreach ($request->transports as $transport) {
                if (!empty($transport['route'])) {
                    $booking->transports()->create($transport);
                }
            }
        }

        if ($request->has('visas')) {
            foreach ($request->visas as $visa) {
                if (!empty($visa['passport_number'])) {
                    $booking->visas()->create($visa);
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
        $booking = Booking::with(['client', 'company', 'persons', 'hotels', 'transports', 'visas'])->findOrFail($id);
        return view('booking.show', compact('booking'));
    }

    public function edit($id)
    {
        $booking   = Booking::with(['persons', 'hotels', 'transports', 'visas'])->findOrFail($id);
        $clients   = Client::where('status', 'active')->get();
        $companies = Company::all();
        $years     = [date('Y'), date('Y') + 1, date('Y') + 2];

        $transactionsPaid = Transaction::where('client_id', $booking->client_id)
            ->where('status', 'confirmed')
            ->sum('amount');

        return view('booking.edit', compact('booking', 'clients', 'companies', 'years', 'transactionsPaid'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'booking_for'  => 'required|in:client,company',
            'client_id'    => 'required_if:booking_for,client|nullable|exists:clients,id',
            'company_id'   => 'required_if:booking_for,company|nullable|exists:companies,id',
            'package_type' => 'required|in:umrah,hajj,other',
        ]);

        $clientId  = $request->booking_for === 'client'  ? $request->client_id  : null;
        $companyId = $request->booking_for === 'company' ? $request->company_id : null;

        $total = (($request->package_cost ?? 0) * ($request->no_of_pax ?? 1))
            + ($request->visa_charges ?? 0)
            + ($request->flight_charges ?? 0)
            + ($request->other_charges ?? 0);

        $booking->update(array_merge(
            $request->except(['persons', 'hotels', 'transports', 'visas', 'flight_persons', '_token', '_method']),
            [
                'client_id'      => $clientId,
                'company_id'     => $companyId,
                'package_cost'   => $request->package_cost ?? 0,
                'visa_charges'   => $request->visa_charges ?? 0,
                'flight_charges' => $request->flight_charges ?? 0,
                'other_charges'  => $request->other_charges ?? 0,
                'total_received' => $request->total_received ?? 0,
                'total_amount'   => $total,
                'balance'        => $total - ($request->total_received ?? 0),
            ]
        ));

        $booking->persons()->delete();
        if ($request->has('persons')) {
            foreach ($request->persons as $p) {
                if (!empty($p['full_name'])) $booking->persons()->create($p);
            }
        }

        $booking->hotels()->delete();
        if ($request->has('hotels')) {
            foreach ($request->hotels as $h) {
                if (!empty($h['hotel_name'])) $booking->hotels()->create($h);
            }
        }

        $booking->transports()->delete();
        if ($request->has('transports')) {
            foreach ($request->transports as $t) {
                if (!empty($t['route'])) $booking->transports()->create($t);
            }
        }

        $booking->visas()->delete();
        if ($request->has('visas')) {
            foreach ($request->visas as $v) {
                if (!empty($v['passport_number'])) $booking->visas()->create($v);
            }
        }

        logUserActivity('Booking Updated', 'Package: ' . $booking->package_type . ' | Total: ' . $booking->total_amount, $booking->id, 'Booking');

        return redirect()->route('booking.index')->with('success', 'Booking updated!');
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
        $booking->load(['client', 'company', 'persons', 'hotels']);
        return view('booking.voucher', compact('booking'));
    }
}
