@extends('layout.master')
@section('title', 'Booking Flight Report')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                {{-- Page Title & Actions --}}
                <div class="py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                    <div>
                        <h4 class="fs-20 fw-bold m-0 text-dark">
                            <i class="mdi mdi-airplane text-primary me-2"></i>Booking Flight Report & Schedule
                        </h4>
                        <p class="text-muted mb-0 small">Flight departure and return schedules, airline manifests, PNRs, ticket charges, and passenger travel logs.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('report.flight') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-refresh me-1"></i> Reset Filters
                        </a>
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Print Schedule
                        </button>
                    </div>
                </div>

                {{-- KPI Summary Cards --}}
                <div class="row g-3 mb-4 no-print">
                    {{-- Total Bookings / Flight Groups --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Flight Groups</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalBookings) }}</h3>
                                        <span class="badge bg-white text-primary px-2 py-0" style="font-size:11px;">Bookings</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-airplane-takeoff fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Flying Passengers --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Passengers</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalPassengers) }}</h3>
                                        <span class="badge bg-white text-success px-2 py-0" style="font-size:11px;">Flying Pax</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-account-group fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Departures Scheduled --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Departures</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($departuresScheduled) }}</h3>
                                        <span class="badge bg-white text-danger px-2 py-0" style="font-size:11px;">Outbound</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-airplane-takeoff fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Returns Scheduled --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #f09819 0%, #edde5d 100%); color: #333;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase">Return Flights</span>
                                        <h3 class="fw-bold my-1 text-dark">{{ number_format($returnsScheduled) }}</h3>
                                        <span class="badge bg-dark text-white px-2 py-0" style="font-size:11px;">Inbound</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-dark bg-opacity-10">
                                        <i class="mdi mdi-airplane-landing fs-24 text-dark"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PNRs Issued --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #4776e6 0%, #8e54e9 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">PNRs Issued</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($pnrsIssued) }}</h3>
                                        <span class="badge bg-white text-primary px-2 py-0" style="font-size:11px;">Tickets Issued</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-ticket-confirmation fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Flight Charges --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100 bg-light border">
                            <div class="card-body p-3 d-flex flex-column justify-content-center text-center">
                                <span class="text-muted small fw-semibold">Flight Charges</span>
                                <h4 class="fw-bold text-dark my-1 fs-16">PKR {{ number_format($totalFlightCharges) }}</h4>
                                <span class="text-success small fw-bold"><i class="mdi mdi-check-decagram me-1"></i>Active Flights</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Card --}}
                <div class="card shadow-sm border-0 mb-4 no-print">
                    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="m-0 fw-bold text-dark">
                            <i class="mdi mdi-filter-variant me-1 text-primary"></i> Filter Flight Criteria
                        </h6>
                        @if (!empty($search) || (!empty($airline) && $airline !== 'all') || (!empty($flightType) && $flightType !== 'all') || !empty($fromDate) || !empty($toDate) || (!empty($packageId) && $packageId !== 'all'))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                <i class="mdi mdi-check me-1"></i>Filters Active
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('report.flight') }}" id="flightFilterForm">
                            <div class="row g-3 align-items-end">
                                {{-- Search Input --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small fw-bold mb-1">Search Flight</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-magnify text-muted"></i></span>
                                        <input type="text" name="search" class="form-control form-control-sm"
                                               placeholder="Flight #, PNR, Pilgrim, Voucher, Airline..."
                                               value="{{ $search }}">
                                    </div>
                                </div>

                                {{-- Airline Filter --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small fw-bold mb-1">Airline</label>
                                    <select name="airline" class="form-select form-select-sm">
                                        <option value="all" {{ ($airline == 'all' || empty($airline)) ? 'selected' : '' }}>All Airlines</option>
                                        @foreach ($allAirlines as $alName)
                                            <option value="{{ $alName }}" {{ $airline == $alName ? 'selected' : '' }}>{{ $alName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Flight Type / Status Filter --}}
                                <div class="col-lg-2 col-md-4 col-6">
                                    <label class="form-label small fw-bold mb-1">Flight Status</label>
                                    <select name="flight_type" class="form-select form-select-sm">
                                        <option value="all" {{ ($flightType == 'all' || empty($flightType)) ? 'selected' : '' }}>All Flights</option>
                                        <option value="with_flight" {{ $flightType == 'with_flight' ? 'selected' : '' }}>✈️ Has Flight Assigned</option>
                                        <option value="pending" {{ $flightType == 'pending' ? 'selected' : '' }}>⏳ Flight Details Pending</option>
                                        <option value="upcoming_departure" {{ $flightType == 'upcoming_departure' ? 'selected' : '' }}>🛫 Upcoming Departures</option>
                                        <option value="upcoming_arrival" {{ $flightType == 'upcoming_arrival' ? 'selected' : '' }}>🛬 Upcoming Returns</option>
                                    </select>
                                </div>

                                {{-- From Date --}}
                                <div class="col-lg-2 col-md-4 col-6">
                                    <label class="form-label small fw-bold mb-1">From Date</label>
                                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                                </div>

                                {{-- To Date --}}
                                <div class="col-lg-2 col-md-4 col-6">
                                    <label class="form-label small fw-bold mb-1">To Date</label>
                                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                                </div>

                                {{-- Package Filter --}}
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label small fw-bold mb-1">Package</label>
                                    <select name="package_id" class="form-select form-select-sm">
                                        <option value="">-- All Packages --</option>
                                        @foreach ($packages as $pkg)
                                            @php
                                                $pkgCode = $pkg->code ?? $pkg->package_number ?? '';
                                                $pkgTitle = $pkg->package_title ?: ($pkg->name ?: 'Package #' . $pkg->id);
                                                $displayText = $pkgCode ? $pkgTitle . ' (' . $pkgCode . ')' : $pkgTitle;
                                            @endphp
                                            <option value="{{ $pkg->id }}" {{ $packageId == $pkg->id ? 'selected' : '' }}>
                                                {{ $displayText }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="col-lg-8 col-md-6 d-flex justify-content-end gap-2 mt-2">
                                    <a href="{{ route('report.flight') }}" class="btn btn-light btn-sm border px-3">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </a>
                                    <button type="submit" class="btn btn-sm fw-semibold text-white shadow-sm px-4" style="background-color: #071527; border-color: #071527;">
                                        <i class="mdi mdi-filter me-1"></i> Apply Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Printable Header (Visible only in Print) --}}
                <div class="print-only mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Logo" style="height: 60px; object-fit: contain;">
                            <div>
                                <h3 class="m-0 fw-bold" style="color:#000; letter-spacing:0.5px;">PIRWANI HAJJ GROUP</h3>
                                <p class="m-0 small text-muted">BOOKING FLIGHT REPORT & AIRLINE SCHEDULE</p>
                            </div>
                        </div>
                        <div class="text-end" style="font-size: 11px;">
                            <div><strong>Printed On:</strong> {{ now()->format('d M Y, h:i A') }}</div>
                            <div><strong>Total Bookings:</strong> {{ $totalBookings }} ({{ $totalPassengers }} Passengers)</div>
                        </div>
                    </div>
                </div>

                {{-- Flight Results Table Card --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="m-0 fw-bold text-dark">
                                Flight Schedules ({{ $bookings->count() }})
                            </h6>
                            @if (!empty($search) || (!empty($airline) && $airline !== 'all') || (!empty($flightType) && $flightType !== 'all') || !empty($fromDate) || !empty($toDate) || (!empty($packageId) && $packageId !== 'all'))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Filtered</span>
                            @endif
                        </div>
                        <div class="text-muted small">
                            Showing booking departure & return flight manifests
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0" id="flightManifestTable" style="font-size: 13px;">
                                <thead class="table-light text-uppercase text-dark fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">#</th>
                                        <th style="min-width: 170px;">Voucher & Booking</th>
                                        <th style="min-width: 200px;">Passengers (Pax)</th>
                                        <th style="min-width: 190px;">Departure Flight</th>
                                        <th style="min-width: 190px;">Return / Arrival Flight</th>
                                        <th class="text-center" style="min-width: 120px;">Stay & Charges</th>
                                        <th class="text-center" style="min-width: 110px;">Status</th>
                                        <th class="text-center no-print" style="width: 90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bookings as $idx => $b)
                                        @php
                                            $persons = $b->persons;
                                            $paxCount = $b->no_of_pax ?: ($persons->count() ?: 1);

                                            $depDate = $b->departure_date ? \Carbon\Carbon::parse($b->departure_date) : null;
                                            $arrDate = $b->arrival_date ? \Carbon\Carbon::parse($b->arrival_date) : null;
                                            $todayC = \Carbon\Carbon::today();

                                            // Determine Flight Status
                                            $statusText = 'Pending Flight';
                                            $statusBadge = 'bg-secondary';
                                            if ($depDate) {
                                                if ($depDate->isToday()) {
                                                    $statusText = 'Departing Today';
                                                    $statusBadge = 'bg-danger text-white animate-pulse';
                                                } elseif ($depDate->isTomorrow()) {
                                                    $statusText = 'Departing Tomorrow';
                                                    $statusBadge = 'bg-warning text-dark';
                                                } elseif ($depDate->isFuture()) {
                                                    $statusText = 'Upcoming (' . $depDate->diffForHumans() . ')';
                                                    $statusBadge = 'bg-info text-white';
                                                } elseif ($arrDate && $arrDate->isPast()) {
                                                    $statusText = 'Returned';
                                                    $statusBadge = 'bg-light text-muted border';
                                                } elseif ($depDate->isPast() && ($arrDate ? $arrDate->isFuture() || $arrDate->isToday() : true)) {
                                                    $statusText = 'Currently Travelling';
                                                    $statusBadge = 'bg-success text-white';
                                                }
                                            }

                                            // Calculate Duration
                                            $durationDays = ($depDate && $arrDate) ? $depDate->diffInDays($arrDate) : null;
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>

                                            {{-- Voucher & Booking --}}
                                            <td>
                                                <div>
                                                    <a href="{{ route('booking.voucher', $b->id) }}" target="_blank" class="fw-bold text-primary text-decoration-none fs-13">
                                                        <i class="mdi mdi-ticket-percent-outline me-1"></i>{{ $b->voucher_number ?: ('UB' . str_pad($b->id, 6, '0', STR_PAD_LEFT)) }}
                                                    </a>
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    {{ $b->client ? $b->client->name : ($b->company ? $b->company->company_name : 'Direct Booking') }}
                                                </div>

                                                {{-- Package --}}
                                                <div class="fw-semibold text-secondary mt-1" style="font-size: 11.5px;">
                                                    @if ($b->package)
                                                        {{ $b->package->package_title ?: ($b->package->name ?: 'Package #' . $b->package->id) }}
                                                    @endif
                                                </div>

                                                {{-- Maktab badge --}}
                                                @php
                                                    $campVal = $b->camp ?: ($b->package?->camp_category ? 'Maktab ' . $b->package->camp_category : null);
                                                @endphp
                                                @if($campVal)
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning-subtle text-dark border border-warning px-1 py-0" style="font-size: 10px; font-weight: 600;">
                                                            <i class="mdi mdi-tent me-0.5 text-primary"></i>{{ $campVal }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Passengers (Pax) --}}
                                            <td>
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    <span class="badge bg-dark text-white px-2 py-0.5" style="font-size: 11px;">
                                                        <i class="mdi mdi-account-group me-1"></i>{{ $paxCount }} Pax
                                                    </span>
                                                </div>
                                                @if ($persons->count() > 0)
                                                    <div class="d-flex flex-column gap-1 mt-1">
                                                        @foreach ($persons->take(3) as $p)
                                                            <div class="d-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                                                <i class="mdi mdi-account text-muted" style="font-size: 12px;"></i>
                                                                <strong class="text-dark">{{ $p->full_name }}</strong>
                                                                @if (!empty($p->passport_number))
                                                                    <span class="badge bg-light text-muted border px-1 py-0" style="font-size: 9.5px;">
                                                                        {{ $p->passport_number }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                        @if ($persons->count() > 3)
                                                            <small class="text-muted ps-3">+ {{ $persons->count() - 3 }} more passengers</small>
                                                        @endif
                                                    </div>
                                                @elseif (!empty($b->given_name) || !empty($b->sur_name))
                                                    <div class="text-dark fw-semibold small">
                                                        {{ trim($b->given_name . ' ' . $b->sur_name) }}
                                                        @if(!empty($b->passport_number))
                                                            <span class="badge bg-light text-muted border ms-1">{{ $b->passport_number }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Passenger list pending</span>
                                                @endif
                                            </td>

                                            {{-- Departure Flight --}}
                                            <td>
                                                @if ($depDate || !empty($b->departure_flight) || !empty($b->departure_airline))
                                                    <div>
                                                        <span class="fw-bold text-dark fs-13">
                                                            <i class="mdi mdi-calendar text-primary me-1"></i>{{ $depDate ? $depDate->format('d M Y') : '—' }}
                                                        </span>
                                                        @if (!empty($b->departure_time))
                                                            <small class="text-muted ms-1">({{ $b->departure_time }})</small>
                                                        @endif
                                                    </div>

                                                    {{-- Flight # & Airline --}}
                                                    <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                                                        @if (!empty($b->departure_flight))
                                                            <span class="badge bg-primary text-white px-2 py-0" style="font-size: 10.5px;">
                                                                <i class="mdi mdi-airplane-takeoff me-1"></i>{{ $b->departure_flight }}
                                                            </span>
                                                        @endif
                                                        @if (!empty($b->departure_airline))
                                                            <span class="text-muted small">
                                                                {{ $b->departure_airline }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Departure PNR --}}
                                                    @if (!empty($b->departure_pnr))
                                                        <div class="mt-1 small">
                                                            <span class="text-muted">PNR:</span>
                                                            <span class="badge bg-light text-dark border px-1 py-0 fw-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">
                                                                {{ $b->departure_pnr }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge bg-light text-muted border">Departure Pending</span>
                                                @endif
                                            </td>

                                            {{-- Return / Arrival Flight --}}
                                            <td>
                                                @if ($arrDate || !empty($b->arrival_flight) || !empty($b->arrival_airline))
                                                    <div>
                                                        <span class="fw-bold text-dark fs-13">
                                                            <i class="mdi mdi-calendar text-success me-1"></i>{{ $arrDate ? $arrDate->format('d M Y') : '—' }}
                                                        </span>
                                                        @if (!empty($b->arrival_time))
                                                            <small class="text-muted ms-1">({{ $b->arrival_time }})</small>
                                                        @endif
                                                    </div>

                                                    {{-- Flight # & Airline --}}
                                                    <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                                                        @if (!empty($b->arrival_flight))
                                                            <span class="badge bg-success text-white px-2 py-0" style="font-size: 10.5px;">
                                                                <i class="mdi mdi-airplane-landing me-1"></i>{{ $b->arrival_flight }}
                                                            </span>
                                                        @endif
                                                        @if (!empty($b->arrival_airline))
                                                            <span class="text-muted small">
                                                                {{ $b->arrival_airline }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Arrival PNR --}}
                                                    @if (!empty($b->arrival_pnr))
                                                        <div class="mt-1 small">
                                                            <span class="text-muted">PNR:</span>
                                                            <span class="badge bg-light text-dark border px-1 py-0 fw-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">
                                                                {{ $b->arrival_pnr }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge bg-light text-muted border">Return Pending</span>
                                                @endif
                                            </td>

                                            {{-- Stay Duration & Charges --}}
                                            <td class="text-center">
                                                @if ($durationDays !== null)
                                                    <div class="badge bg-light text-dark border px-2 py-1 fs-12 fw-bold">
                                                        {{ $durationDays }} Days ({{ $durationDays - 1 }}N)
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif

                                                <div class="small mt-1 text-dark fw-semibold">
                                                    @if ($b->flight_charges > 0)
                                                        PKR {{ number_format($b->flight_charges) }}
                                                    @else
                                                        <span class="text-muted font-normal" style="font-size: 10.5px;">Included</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Status --}}
                                            <td class="text-center">
                                                <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">
                                                    {{ $statusText }}
                                                </span>
                                            </td>

                                            {{-- Action --}}
                                            <td class="text-center no-print">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('booking.show', $b->id) }}" class="btn btn-light border text-primary" title="View Booking Details">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('booking.voucher', $b->id) }}" target="_blank" class="btn btn-light border text-success" title="Print / Download Voucher">
                                                        <i class="mdi mdi-ticket-percent-outline"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="mdi mdi-airplane-off text-muted" style="font-size: 48px;"></i>
                                                    <h5 class="fw-bold text-muted mt-2">No Booking Flight Records Found</h5>
                                                    <p class="text-muted small mb-3">No bookings match the selected flight, airline, or date range criteria.</p>
                                                    <a href="{{ route('report.flight') }}" class="btn btn-primary btn-sm">
                                                        <i class="mdi mdi-refresh me-1"></i> Clear Filters
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Custom CSS for Printing and Pulse --}}
    <style>
        .animate-pulse {
            animation: pulse-animation 2s infinite;
        }
        @keyframes pulse-animation {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.85; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            .content-page {
                margin: 0 !important;
                padding: 0 !important;
            }
            body {
                background: #fff !important;
                color: #000 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            table {
                font-size: 10px !important;
                width: 100% !important;
            }
            th, td {
                padding: 4px 6px !important;
            }
            @page {
                size: A4 landscape;
                margin: 8mm 8mm;
            }
        }
    </style>
@endsection
