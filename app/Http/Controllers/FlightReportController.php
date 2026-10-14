<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use App\Models\Booking;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FlightReportController extends Controller
{
    public function index(Request $request)
    {
        $search      = $request->get('search');
        $airline     = $request->get('airline', 'all');
        $flightType  = $request->get('flight_type', 'all');
        $fromDate    = $request->get('from_date');
        $toDate      = $request->get('to_date');
        $packageId   = $request->get('package_id');

        $query = Booking::with(['client', 'company', 'package', 'persons'])
            ->whereNull('deleted_at')
            ->whereNotIn('status', ['cancelled', 'rejected']);

        $today = Carbon::today()->format('Y-m-d');

        // Search Filter
        if (!empty($search)) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('voucher_number', 'LIKE', $term)
                  ->orWhere('departure_flight', 'LIKE', $term)
                  ->orWhere('arrival_flight', 'LIKE', $term)
                  ->orWhere('departure_airline', 'LIKE', $term)
                  ->orWhere('arrival_airline', 'LIKE', $term)
                  ->orWhere('departure_pnr', 'LIKE', $term)
                  ->orWhere('arrival_pnr', 'LIKE', $term)
                  ->orWhere('given_name', 'LIKE', $term)
                  ->orWhere('sur_name', 'LIKE', $term)
                  ->orWhere('passport_number', 'LIKE', $term)
                  ->orWhereHas('client', function ($cq) use ($term) {
                      $cq->where('name', 'LIKE', $term)->orWhere('phone', 'LIKE', $term);
                  })
                  ->orWhereHas('company', function ($comQ) use ($term) {
                      $comQ->where('company_name', 'LIKE', $term);
                  })
                  ->orWhereHas('persons', function ($pq) use ($term) {
                      $pq->where('full_name', 'LIKE', $term)
                         ->orWhere('passport_number', 'LIKE', $term)
                         ->orWhere('hajj_id', 'LIKE', $term)
                         ->orWhere('hb_number', 'LIKE', $term);
                  });
            });
        }

        // Airline Filter
        if (!empty($airline) && $airline !== 'all') {
            $query->where(function ($q) use ($airline) {
                $q->where('departure_airline', 'LIKE', '%' . $airline . '%')
                  ->orWhere('arrival_airline', 'LIKE', '%' . $airline . '%');
            });
        }

        // Flight Type / Direction Filter
        if ($flightType === 'with_flight') {
            $query->where(function ($q) {
                $q->whereNotNull('departure_flight')->where('departure_flight', '!=', '')
                  ->orWhereNotNull('departure_pnr')->where('departure_pnr', '!=', '')
                  ->orWhereNotNull('arrival_flight')->where('arrival_flight', '!=', '');
            });
        } elseif ($flightType === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('departure_flight')->orWhere('departure_flight', '')
                  ->orWhereNull('departure_pnr')->orWhere('departure_pnr', '');
            });
        } elseif ($flightType === 'upcoming_departure') {
            $query->whereDate('departure_date', '>=', $today);
        } elseif ($flightType === 'upcoming_arrival') {
            $query->whereDate('arrival_date', '>=', $today);
        }

        // Date Range (filters departure or arrival dates)
        if (!empty($fromDate)) {
            $query->where(function ($q) use ($fromDate) {
                $q->whereDate('departure_date', '>=', $fromDate)
                  ->orWhereDate('arrival_date', '>=', $fromDate);
            });
        }
        if (!empty($toDate)) {
            $query->where(function ($q) use ($toDate) {
                $q->whereDate('departure_date', '<=', $toDate)
                  ->orWhereDate('arrival_date', '<=', $toDate);
            });
        }

        // Package Filter
        if (!empty($packageId) && $packageId !== 'all') {
            $query->where('package_id', $packageId);
        }

        // Order by departure date or booking ID
        $bookings = $query->orderByRaw('COALESCE(departure_date, "9999-12-31") ASC')
                          ->orderBy('id', 'desc')
                          ->get();

        // KPI Calculations
        $totalBookings       = $bookings->count();
        $totalPassengers     = $bookings->sum(function ($b) {
            return $b->no_of_pax ?: ($b->persons->count() ?: 1);
        });
        $departuresScheduled = $bookings->filter(fn($b) => !empty($b->departure_date))->count();
        $returnsScheduled    = $bookings->filter(fn($b) => !empty($b->arrival_date))->count();
        $pnrsIssued          = $bookings->filter(fn($b) => !empty($b->departure_pnr) || !empty($b->arrival_pnr))->count();
        $totalFlightCharges  = $bookings->sum('flight_charges');

        // Dropdown Data
        $packages = Package::latest()->get();
        $registeredAirlines = Airline::orderBy('name')->pluck('name')->toArray();
        $bookingAirlines = Booking::whereNotNull('departure_airline')->where('departure_airline', '!=', '')->pluck('departure_airline')
            ->merge(Booking::whereNotNull('arrival_airline')->where('arrival_airline', '!=', '')->pluck('arrival_airline'))
            ->unique()->toArray();
        $allAirlines = array_values(array_unique(array_filter(array_merge($registeredAirlines, $bookingAirlines))));

        return view('reports.flight', compact(
            'bookings',
            'totalBookings',
            'totalPassengers',
            'departuresScheduled',
            'returnsScheduled',
            'pnrsIssued',
            'totalFlightCharges',
            'packages',
            'allAirlines',
            'search',
            'airline',
            'flightType',
            'fromDate',
            'toDate',
            'packageId'
        ));
    }
}
