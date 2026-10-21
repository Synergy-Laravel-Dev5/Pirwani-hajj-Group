@extends('layout.master')
@section('title', 'Booking Flight Report & Schedule')

@section('content')
<style>
    /* Official Manifest Table Styling matching Rooming, Maktab & Check-In Reports */
    .manifest-table {
        border-collapse: collapse;
        width: 100%;
        background-color: #ffffff;
        font-family: Arial, sans-serif;
    }
    .manifest-table th {
        background-color: #f8fafc;
        color: #000;
        font-weight: 800;
        font-size: 13.5px;
        text-align: center;
        vertical-align: middle;
        padding: 9px 6px;
        border: 2px solid #000000 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .manifest-table td {
        padding: 8px 6px;
        vertical-align: middle;
        font-size: 13.5px;
        border: 1.5px solid #000000 !important;
    }
    .booking-divider-row td {
        border-top: 3px solid #000000 !important;
    }
    .haji-photo {
        width: 48px;
        height: 52px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #999;
        display: block;
        margin: 0 auto;
    }
    .haji-photo-placeholder {
        width: 48px;
        height: 52px;
        background-color: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #64748b;
        font-weight: bold;
        font-size: 14px;
    }
    .gender-male {
        color: #1e3a8a;
        font-weight: 700;
    }
    .gender-female {
        color: #9d174d;
        font-weight: 700;
    }
    .animate-pulse {
        animation: pulse-animation 2s infinite;
    }
    @keyframes pulse-animation {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.85; }
        100% { transform: scale(1); opacity: 1; }
    }
    @media print {
        .no-print, .left-side-menu, .navbar-custom, .footer {
            display: none !important;
        }
        .content-page {
            margin: 0 !important;
            padding: 0 !important;
        }
        body {
            background-color: #fff !important;
            font-size: 11px;
        }
        .manifest-table th, .manifest-table td {
            border: 1.5px solid #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .col-hidden {
            display: none !important;
        }
        @page {
            size: A4 landscape;
            margin: 8mm 6mm;
        }
    }

    /* Column Visibility & Hidden Styles */
    .col-hidden {
        display: none !important;
    }

    .column-filter-menu {
        min-width: 220px;
        max-height: 420px;
        overflow-y: auto;
        padding: 8px 10px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border: 1px solid #e2e8f0;
    }

    .column-filter-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 5px 6px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
        user-select: none;
        transition: background 0.15s ease;
    }

    .column-filter-item:hover {
        background-color: #f1f5f9;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            {{-- Page Header & Actions --}}
            <div class="py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                <div>
                    <h4 class="fs-20 fw-bold m-0 text-dark">
                        <i class="mdi mdi-airplane text-primary me-2"></i>Booking Flight Report & Schedule
                    </h4>
                    <p class="text-muted mb-0 small">Flight departure and return schedules, airline manifests, PNRs, ticket charges, and passenger travel logs.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('report.flight.export-excel', request()->query()) }}" 
                       class="btn btn-success btn-sm"
                       title="Download official booking flight report as Excel spreadsheet">
                        <i class="mdi mdi-file-excel me-1"></i> Download Excel
                    </a>
                    <a href="{{ route('report.flight.export-pdf', request()->query()) }}" 
                       class="btn btn-danger btn-sm"
                       title="Download official booking flight report as PDF document">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-dark btn-sm">
                        <i class="mdi mdi-printer me-1"></i> Print Schedule
                    </button>
                    <a href="{{ route('report.flight') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </div>

            {{-- Summary Ribbon / KPI Cards --}}
            <div class="row g-2 mb-3 no-print">
                {{-- Flight Groups / Bookings --}}
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Flight Groups:</span>
                            <strong class="fs-18 text-primary">{{ number_format($totalBookings) }}</strong>
                        </div>
                        <span class="badge bg-primary text-white" style="font-size: 11px;">
                            Bookings
                        </span>
                    </div>
                </div>

                {{-- Passengers --}}
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Passengers:</span>
                            <strong class="fs-18 text-dark">{{ number_format($totalPassengers) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-dark bg-opacity-10 text-dark">
                            <i class="mdi mdi-account-group fs-20"></i>
                        </div>
                    </div>
                </div>

                {{-- Departures Scheduled --}}
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Departures:</span>
                            <strong class="fs-18 text-danger">{{ number_format($departuresScheduled) }}</strong>
                        </div>
                        <span class="badge bg-danger text-white" style="font-size: 11px;">
                            Outbound
                        </span>
                    </div>
                </div>

                {{-- Returns Scheduled --}}
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Return Flights:</span>
                            <strong class="fs-18 text-warning">{{ number_format($returnsScheduled) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning">
                            <i class="mdi mdi-airplane-landing fs-20"></i>
                        </div>
                    </div>
                </div>

                {{-- PNRs Issued --}}
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">PNRs Issued:</span>
                            <strong class="fs-18 text-success">{{ number_format($pnrsIssued) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                            <i class="mdi mdi-ticket-confirmation fs-20"></i>
                        </div>
                    </div>
                </div>

                {{-- Flight Charges --}}
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Flight Charges:</span>
                            <strong class="fs-15 text-dark">PKR {{ number_format($totalFlightCharges) }}</strong>
                        </div>
                        <span class="text-success small fw-bold"><i class="mdi mdi-check-decagram"></i> Active</span>
                    </div>
                </div>
            </div>

            {{-- Filter Card --}}
            <div class="card shadow-sm border-0 mb-3 no-print">
                <div class="card-header bg-white py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="m-0 fw-bold text-dark fs-14">
                            <i class="mdi mdi-filter-variant me-1 text-primary"></i> Filter Flight Criteria
                        </h6>
                        @if (!empty($search) || (!empty($airline) && $airline !== 'all') || (!empty($flightType) && $flightType !== 'all') || !empty($fromDate) || !empty($toDate) || (!empty($packageId) && $packageId !== 'all'))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Filtered</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.flight') }}" id="flightFilterForm">
                        <div class="row g-2 align-items-end">
                            {{-- Search Input --}}
                            <div class="col-xl-4 col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Search Flight / Passenger / PNR / Voucher</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-magnify text-muted"></i></span>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                           placeholder="Search Flight #, PNR, Pilgrim, Voucher, Airline..."
                                           value="{{ $search }}">
                                </div>
                            </div>

                            {{-- Airline Filter --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Airline</label>
                                <select name="airline" class="form-select form-select-sm">
                                    <option value="all" {{ ($airline == 'all' || empty($airline)) ? 'selected' : '' }}>All Airlines</option>
                                    @foreach ($allAirlines as $alName)
                                        <option value="{{ $alName }}" {{ $airline == $alName ? 'selected' : '' }}>{{ $alName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Flight Status Filter --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Flight Status</label>
                                <select name="flight_type" class="form-select form-select-sm">
                                    <option value="all" {{ ($flightType == 'all' || empty($flightType)) ? 'selected' : '' }}>All Flights</option>
                                    <option value="with_flight" {{ $flightType == 'with_flight' ? 'selected' : '' }}>✈️ Has Flight Assigned</option>
                                    <option value="pending" {{ $flightType == 'pending' ? 'selected' : '' }}>⏳ Flight Details Pending</option>
                                    <option value="upcoming_departure" {{ $flightType == 'upcoming_departure' ? 'selected' : '' }}>🛫 Upcoming Departures</option>
                                    <option value="upcoming_arrival" {{ $flightType == 'upcoming_arrival' ? 'selected' : '' }}>🛬 Upcoming Returns</option>
                                </select>
                            </div>

                            {{-- From Date --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Date From</label>
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                            </div>

                            {{-- To Date --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Date To</label>
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                            </div>

                            {{-- Package Filter --}}
                            <div class="col-xl-4 col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Package</label>
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
                            <div class="col-xl-4 col-md-6 d-flex gap-2 ms-auto">
                                <button type="submit" class="btn btn-sm fw-semibold flex-grow-1 text-white shadow-sm" style="background-color: #071527; border-color: #071527;">
                                    <i class="mdi mdi-filter me-1"></i> Apply Filter
                                </button>
                                <a href="{{ route('report.flight') }}" class="btn btn-outline-secondary btn-sm px-3">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- OFFICIAL MANIFEST TABLE CARD --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark fs-14">
                            <i class="mdi mdi-table-eye text-primary me-1"></i>Manifest Columns
                        </span>
                        <span class="badge bg-light text-muted border">Customizable View</span>
                        <span class="badge bg-primary text-white">{{ $bookings->count() }} Flight Bookings Listed</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        {{-- Columns Filter Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-sm text-white fw-bold dropdown-toggle d-flex align-items-center gap-1 shadow-sm" 
                                    type="button" 
                                    id="columnsDropdownBtn" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false" 
                                    style="background: #475569; border-color: #334155;">
                                <i class="mdi mdi-view-column-outline fs-15"></i> Columns
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow column-filter-menu" aria-labelledby="columnsDropdownBtn" onclick="event.stopPropagation();">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                    <span class="fw-bold text-dark small">Columns</span>
                                    <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 fw-semibold" id="btnResetColumns" style="font-size: 11px;">
                                        <i class="mdi mdi-backup-restore me-1"></i>Reset
                                    </button>
                                </div>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="sr" checked>
                                    <span>S.No (SR)</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="picture" checked>
                                    <span>Picture</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="hajj-id" checked>
                                    <span>Hajj ID</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="hb" checked>
                                    <span>HB #</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="pilgrim" checked>
                                    <span>Passenger Full Name</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="given-name">
                                    <span>Given Name</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="surname">
                                    <span>Surname</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="father-name">
                                    <span>Husband / Father Name</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="gender" checked>
                                    <span>Gender</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="passport" checked>
                                    <span>Passport No</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="passport-doi">
                                    <span>Date of Issue (DOI)</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="passport-exp">
                                    <span>Passport Expiry Date</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="voucher" checked>
                                    <span>Voucher & Booking #</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="dep-flight" checked>
                                    <span>Departure Flight</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="dep-pnr">
                                    <span>Departure PNR</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="ret-flight" checked>
                                    <span>Return Flight</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="ret-pnr">
                                    <span>Return PNR</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="duration" checked>
                                    <span>Duration</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="package" checked>
                                    <span>Package</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="maktab">
                                    <span>Maktab / Camp</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="client">
                                    <span>Client / Group</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="charges" checked>
                                    <span>Flight Charges</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="status" checked>
                                    <span>Flight Status</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="action" checked>
                                    <span>Action</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Printable Header (Visible only in Print) --}}
                <div class="print-only mb-3 p-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Logo" style="height: 60px; object-fit: contain;">
                            <div>
                                <h3 class="m-0 fw-bold" style="color:#000; letter-spacing:0.5px;">PIRWANI HAJJ GROUP</h3>
                                <p class="m-0 small text-muted">OFFICIAL BOOKING FLIGHT REPORT & AIRLINE SCHEDULE</p>
                            </div>
                        </div>
                        <div class="text-end" style="font-size: 11px;">
                            <div><strong>Printed On:</strong> {{ now()->format('d M Y, h:i A') }}</div>
                            <div><strong>Total Flight Groups:</strong> {{ $totalBookings }} ({{ $totalPassengers }} Passengers)</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="manifest-table" id="flightManifestTable">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="col-sr">SR</th>
                                    <th style="width: 60px;" class="col-picture">PICTURE</th>
                                    <th style="width: 95px;" class="col-hajj-id">HAJJ ID</th>
                                    <th style="width: 85px;" class="col-hb">HB #</th>
                                    <th style="min-width: 170px;" class="col-pilgrim">PASSENGER FULL NAME</th>
                                    <th style="min-width: 120px;" class="col-given-name col-hidden">GIVEN NAME</th>
                                    <th style="min-width: 120px;" class="col-surname col-hidden">SURNAME</th>
                                    <th style="min-width: 150px;" class="col-father-name col-hidden">HUSBAND / FATHER NAME</th>
                                    <th style="width: 75px;" class="col-gender">GENDER</th>
                                    <th style="width: 110px;" class="col-passport">PASSPORT NO</th>
                                    <th style="width: 95px;" class="col-passport-doi col-hidden">DOI</th>
                                    <th style="width: 95px;" class="col-passport-exp col-hidden">EXPIRY</th>
                                    <th style="min-width: 120px;" class="col-voucher">VOUCHER & BOOKING #</th>
                                    <th style="min-width: 170px;" class="col-dep-flight">DEPARTURE FLIGHT</th>
                                    <th style="width: 100px;" class="col-dep-pnr col-hidden">DEP PNR</th>
                                    <th style="min-width: 170px;" class="col-ret-flight">RETURN FLIGHT</th>
                                    <th style="width: 100px;" class="col-ret-pnr col-hidden">RET PNR</th>
                                    <th style="width: 90px;" class="col-duration">DURATION</th>
                                    <th style="min-width: 130px;" class="col-package">PACKAGE</th>
                                    <th style="width: 105px;" class="col-maktab col-hidden">MAKTAB</th>
                                    <th style="min-width: 120px;" class="col-client col-hidden">CLIENT / GROUP</th>
                                    <th style="width: 110px;" class="col-charges">CHARGES</th>
                                    <th style="width: 115px;" class="col-status">STATUS</th>
                                    <th style="width: 85px;" class="col-action no-print">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $globalSr = 1; @endphp
                                @forelse ($bookings as $b)
                                    @php
                                        $persons = $b->persons;
                                        $totalPax = $persons->count();
                                        $rowspan = max(1, $totalPax);

                                        $depDate = $b->departure_date ? \Carbon\Carbon::parse($b->departure_date) : null;
                                        $arrDate = $b->arrival_date ? \Carbon\Carbon::parse($b->arrival_date) : null;
                                        $durationDays = ($depDate && $arrDate) ? $depDate->diffInDays($arrDate) : null;

                                        // Status
                                        $statusText = 'Pending Flight';
                                        $statusBadge = 'bg-secondary';
                                        if ($depDate) {
                                            if ($depDate->isToday()) {
                                                $statusText = 'Departing Today';
                                                $statusBadge = 'bg-danger text-white animate-pulse';
                                            } elseif ($depDate->isTomorrow()) {
                                                $statusText = 'Tomorrow';
                                                $statusBadge = 'bg-warning text-dark';
                                            } elseif ($arrDate && $arrDate->isPast()) {
                                                $statusText = 'Returned';
                                                $statusBadge = 'bg-light text-muted border';
                                            } elseif ($depDate->isPast() && ($arrDate ? $arrDate->isFuture() || $arrDate->isToday() : true)) {
                                                $statusText = 'Travelling';
                                                $statusBadge = 'bg-success text-white';
                                            } elseif ($depDate->isFuture()) {
                                                $statusText = 'Upcoming (' . $depDate->diffForHumans() . ')';
                                                $statusBadge = 'bg-info text-white';
                                            }
                                        }

                                        $depSector = trim(($b->departure_from ?? '') . ($b->departure_to ? ' → ' . $b->departure_to : ''));
                                        $retSector = trim(($b->arrival_from ?? '') . ($b->arrival_to ? ' → ' . $b->arrival_to : ''));
                                        $pkgTitle = $b->package?->package_title ?? ($b->package?->name ?? '—');
                                        $campVal = $b->camp ?: ($b->package?->camp_category ? 'Maktab ' . $b->package->camp_category : null);
                                    @endphp

                                    @if ($totalPax === 0)
                                        {{-- Row without specific booking_persons --}}
                                        <tr class="booking-divider-row">
                                            <td class="text-center fw-bold text-muted col-sr">{{ $globalSr++ }}</td>
                                            <td class="text-center col-picture">—</td>
                                            <td class="text-center col-hajj-id">—</td>
                                            <td class="text-center col-hb">—</td>
                                            <td style="padding-left: 10px;" class="col-pilgrim">
                                                <strong class="text-dark fs-14">{{ $b->client?->name ?? ($b->company?->company_name ?? 'Direct Passenger') }}</strong>
                                            </td>
                                            <td class="text-center text-muted col-given-name col-hidden">{{ $b->given_name ?? '—' }}</td>
                                            <td class="text-center text-muted col-surname col-hidden">{{ $b->sur_name ?? '—' }}</td>
                                            <td class="text-center text-muted col-father-name col-hidden">—</td>
                                            <td class="text-center col-gender">—</td>
                                            <td class="text-center fw-bold col-passport">{{ $b->passport_number ?: '—' }}</td>
                                            <td class="text-center text-muted col-passport-doi col-hidden">—</td>
                                            <td class="text-center text-muted col-passport-exp col-hidden">—</td>

                                            {{-- Voucher & Booking # --}}
                                            <td class="text-center col-voucher">
                                                <a href="{{ route('booking.voucher', $b->id) }}" target="_blank" class="badge bg-primary text-white text-decoration-none px-2 py-1 fs-12">
                                                    {{ $b->voucher_number ?: ('UB' . str_pad($b->id, 6, '0', STR_PAD_LEFT)) }}
                                                </a>
                                                <div class="small text-muted mt-0.5">Booking #{{ $b->booking_number ?? $b->id }}</div>
                                            </td>

                                            {{-- Departure Flight --}}
                                            <td style="padding-left: 8px;" class="col-dep-flight">
                                                @if ($depDate || !empty($b->departure_flight) || !empty($b->departure_airline))
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        @if(!empty($b->departure_airline))
                                                            <span class="badge bg-dark text-white px-2 py-0.5" style="font-size: 11px;">
                                                                {{ $b->departure_airline }}
                                                            </span>
                                                        @endif
                                                        @if(!empty($b->departure_flight))
                                                            <span class="badge bg-primary text-white px-2 py-0.5 fw-bold" style="font-size: 11px;">
                                                                <i class="mdi mdi-airplane-takeoff me-1"></i>{{ $b->departure_flight }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($depSector))
                                                        <div class="small fw-bold text-dark mt-1">
                                                            <i class="mdi mdi-map-marker-distance text-muted me-1"></i>{{ $depSector }}
                                                        </div>
                                                    @endif
                                                    <div class="small text-muted mt-0.5">
                                                        <i class="mdi mdi-calendar text-primary me-1"></i>{{ $depDate ? $depDate->format('d M Y') : '—' }}
                                                        @if(!empty($b->departure_time))
                                                            <span class="ms-1">({{ $b->departure_time }})</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted border">Departure Pending</span>
                                                @endif
                                            </td>

                                            {{-- Departure PNR --}}
                                            <td class="text-center col-dep-pnr col-hidden">
                                                @if(!empty($b->departure_pnr))
                                                    <span class="badge bg-dark text-white px-2 py-1 fw-bold fs-12">{{ $b->departure_pnr }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Return Flight --}}
                                            <td style="padding-left: 8px;" class="col-ret-flight">
                                                @if ($arrDate || !empty($b->arrival_flight) || !empty($b->arrival_airline))
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        @if(!empty($b->arrival_airline))
                                                            <span class="badge bg-secondary text-white px-2 py-0.5" style="font-size: 11px;">
                                                                {{ $b->arrival_airline }}
                                                            </span>
                                                        @endif
                                                        @if(!empty($b->arrival_flight))
                                                            <span class="badge bg-success text-white px-2 py-0.5 fw-bold" style="font-size: 11px;">
                                                                <i class="mdi mdi-airplane-landing me-1"></i>{{ $b->arrival_flight }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($retSector))
                                                        <div class="small fw-bold text-dark mt-1">
                                                            <i class="mdi mdi-map-marker-distance text-muted me-1"></i>{{ $retSector }}
                                                        </div>
                                                    @endif
                                                    <div class="small text-muted mt-0.5">
                                                        <i class="mdi mdi-calendar text-success me-1"></i>{{ $arrDate ? $arrDate->format('d M Y') : '—' }}
                                                        @if(!empty($b->arrival_time))
                                                            <span class="ms-1">({{ $b->arrival_time }})</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted border">Return Pending</span>
                                                @endif
                                            </td>

                                            {{-- Return PNR --}}
                                            <td class="text-center col-ret-pnr col-hidden">
                                                @if(!empty($b->arrival_pnr))
                                                    <span class="badge bg-dark text-white px-2 py-1 fw-bold fs-12">{{ $b->arrival_pnr }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Duration --}}
                                            <td class="text-center col-duration">
                                                @if ($durationDays !== null)
                                                    <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-12">{{ $durationDays }} Days</span>
                                                    <div class="small text-muted mt-0.5">{{ $durationDays - 1 }} Nights</div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Package --}}
                                            <td style="padding-left: 8px;" class="col-package">
                                                <span class="fw-semibold text-dark fs-13 d-block">{{ $pkgTitle }}</span>
                                            </td>

                                            {{-- Maktab --}}
                                            <td class="text-center col-maktab col-hidden">
                                                @if($campVal)
                                                    <span class="badge bg-warning text-dark px-2 py-1 fw-bold" style="font-size: 11px;">
                                                        <i class="mdi mdi-tent me-0.5"></i>{{ $campVal }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Client / Group --}}
                                            <td style="padding-left: 8px;" class="col-client col-hidden">
                                                <span class="fw-semibold text-dark">{{ $b->client?->name ?? ($b->company?->company_name ?? 'Direct Passenger') }}</span>
                                            </td>

                                            {{-- Flight Charges --}}
                                            <td class="text-center col-charges">
                                                @if ($b->flight_charges > 0)
                                                    <strong class="text-dark fs-13">PKR {{ number_format($b->flight_charges) }}</strong>
                                                @else
                                                    <span class="badge bg-light text-muted border">Included</span>
                                                @endif
                                            </td>

                                            {{-- Status --}}
                                            <td class="text-center col-status">
                                                <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">
                                                    {{ $statusText }}
                                                </span>
                                            </td>

                                            {{-- Action --}}
                                            <td class="text-center col-action no-print">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('booking.show', $b->id) }}" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" title="View Booking">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('booking.voucher', $b->id) }}" target="_blank" class="btn btn-xs btn-outline-success py-0 px-2 fw-semibold" title="View Voucher">
                                                        <i class="mdi mdi-ticket-percent-outline"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Booking with Registered Pilgrims --}}
                                        @foreach ($persons as $oIdx => $person)
                                            <tr class="{{ $oIdx === 0 ? 'booking-divider-row' : '' }}">
                                                <td class="text-center fw-bold text-muted col-sr">{{ $globalSr++ }}</td>

                                                {{-- Photo Thumbnail --}}
                                                <td class="text-center p-1 col-picture">
                                                    @if(!empty($person->photo) && file_exists(public_path($person->photo)))
                                                        <img src="{{ asset($person->photo) }}" alt="{{ $person->full_name }}" class="haji-photo">
                                                    @else
                                                        <div class="haji-photo-placeholder">
                                                            <i class="mdi {{ strtoupper($person->gender ?? '') === 'FEMALE' ? 'mdi-account-female text-danger' : 'mdi-account text-primary' }} fs-20"></i>
                                                        </div>
                                                    @endif
                                                </td>

                                                {{-- Hajj ID --}}
                                                <td class="text-center fw-bold text-uppercase col-hajj-id" style="font-size: 14px; letter-spacing: 0.5px;">
                                                    {{ !empty($person->hajj_id) ? $person->hajj_id : '—' }}
                                                </td>

                                                {{-- HB Number --}}
                                                <td class="text-center fw-bold col-hb">
                                                    @if(!empty($person->hb_number))
                                                        <span class="badge bg-dark text-white px-2 py-1" style="font-size: 12.5px; font-weight: 700;">{{ $person->hb_number }}</span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>

                                                {{-- Passenger Full Name --}}
                                                <td style="text-align: left; padding-left: 10px;" class="col-pilgrim">
                                                    <span class="d-inline-block fw-bold text-dark" style="font-size: 14.5px; letter-spacing: 0.2px;">
                                                        {{ $person->full_name }}
                                                    </span>
                                                    @if(!empty($person->is_family_head))
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-1 py-0 ms-1" style="font-size: 10.5px; font-weight: 700;">
                                                            <i class="mdi mdi-crown text-warning me-0.5"></i>Head
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- Extra / Toggleable columns --}}
                                                <td style="text-align: left; padding-left: 10px;" class="col-given-name col-hidden fw-semibold text-muted text-uppercase">
                                                    {{ $person->given_name ?? '—' }}
                                                </td>
                                                <td style="text-align: left; padding-left: 10px;" class="col-surname col-hidden fw-semibold text-muted text-uppercase">
                                                    {{ $person->surname ?? '—' }}
                                                </td>
                                                <td style="text-align: left; padding-left: 10px;" class="col-father-name col-hidden fw-semibold text-muted text-uppercase">
                                                    {{ $person->father_name ?? ($person->husband_or_father_name ?? '—') }}
                                                </td>

                                                {{-- Gender --}}
                                                <td class="text-center {{ strtoupper($person->gender ?? '') === 'FEMALE' ? 'gender-female' : 'gender-male' }} text-uppercase col-gender" style="font-size: 13.5px;">
                                                    {{ strtoupper($person->gender ?? '—') }}
                                                </td>

                                                {{-- Passport --}}
                                                <td class="text-center fw-bold text-uppercase col-passport" style="font-size: 14px; letter-spacing: 0.5px;">
                                                    {{ $person->passport_number ?: '—' }}
                                                </td>

                                                {{-- Passport DOI --}}
                                                <td class="text-center col-passport-doi col-hidden">
                                                    {{ !empty($person->date_of_issue) ? \Carbon\Carbon::parse($person->date_of_issue)->format('d/m/Y') : '—' }}
                                                </td>

                                                {{-- Passport Expiry --}}
                                                <td class="text-center col-passport-exp col-hidden">
                                                    {{ !empty($person->passport_expiry_date) ? \Carbon\Carbon::parse($person->passport_expiry_date)->format('d/m/Y') : '—' }}
                                                </td>

                                                {{-- Booking-level columns with rowspan --}}
                                                @if ($oIdx === 0)
                                                    {{-- Voucher & Booking # --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-voucher">
                                                        <a href="{{ route('booking.voucher', $b->id) }}" target="_blank" class="badge bg-primary text-white text-decoration-none px-2 py-1 fs-12">
                                                            {{ $b->voucher_number ?: ('UB' . str_pad($b->id, 6, '0', STR_PAD_LEFT)) }}
                                                        </a>
                                                        <div class="small text-muted mt-0.5">Booking #{{ $b->booking_number ?? $b->id }}</div>
                                                        <div class="badge bg-dark text-white px-2 py-0 mt-1" style="font-size: 10px;">{{ $totalPax }} Pax</div>
                                                    </td>

                                                    {{-- Departure Flight --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 8px;" class="col-dep-flight">
                                                        @if ($depDate || !empty($b->departure_flight) || !empty($b->departure_airline))
                                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                                @if(!empty($b->departure_airline))
                                                                    <span class="badge bg-dark text-white px-2 py-0.5" style="font-size: 11px;">
                                                                        {{ $b->departure_airline }}
                                                                    </span>
                                                                @endif
                                                                @if(!empty($b->departure_flight))
                                                                    <span class="badge bg-primary text-white px-2 py-0.5 fw-bold" style="font-size: 11px;">
                                                                        <i class="mdi mdi-airplane-takeoff me-1"></i>{{ $b->departure_flight }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            @if(!empty($depSector))
                                                                <div class="small fw-bold text-dark mt-1">
                                                                    <i class="mdi mdi-map-marker-distance text-muted me-1"></i>{{ $depSector }}
                                                                </div>
                                                            @endif
                                                            <div class="small text-muted mt-0.5">
                                                                <i class="mdi mdi-calendar text-primary me-1"></i>{{ $depDate ? $depDate->format('d M Y') : '—' }}
                                                                @if(!empty($b->departure_time))
                                                                    <span class="ms-1">({{ $b->departure_time }})</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="badge bg-light text-muted border">Departure Pending</span>
                                                        @endif
                                                    </td>

                                                    {{-- Departure PNR --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-dep-pnr col-hidden">
                                                        @if(!empty($b->departure_pnr))
                                                            <span class="badge bg-dark text-white px-2 py-1 fw-bold fs-12">{{ $b->departure_pnr }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Return Flight --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 8px;" class="col-ret-flight">
                                                        @if ($arrDate || !empty($b->arrival_flight) || !empty($b->arrival_airline))
                                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                                @if(!empty($b->arrival_airline))
                                                                    <span class="badge bg-secondary text-white px-2 py-0.5" style="font-size: 11px;">
                                                                        {{ $b->arrival_airline }}
                                                                    </span>
                                                                @endif
                                                                @if(!empty($b->arrival_flight))
                                                                    <span class="badge bg-success text-white px-2 py-0.5 fw-bold" style="font-size: 11px;">
                                                                        <i class="mdi mdi-airplane-landing me-1"></i>{{ $b->arrival_flight }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            @if(!empty($retSector))
                                                                <div class="small fw-bold text-dark mt-1">
                                                                    <i class="mdi mdi-map-marker-distance text-muted me-1"></i>{{ $retSector }}
                                                                </div>
                                                            @endif
                                                            <div class="small text-muted mt-0.5">
                                                                <i class="mdi mdi-calendar text-success me-1"></i>{{ $arrDate ? $arrDate->format('d M Y') : '—' }}
                                                                @if(!empty($b->arrival_time))
                                                                    <span class="ms-1">({{ $b->arrival_time }})</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="badge bg-light text-muted border">Return Pending</span>
                                                        @endif
                                                    </td>

                                                    {{-- Return PNR --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-ret-pnr col-hidden">
                                                        @if(!empty($b->arrival_pnr))
                                                            <span class="badge bg-dark text-white px-2 py-1 fw-bold fs-12">{{ $b->arrival_pnr }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Duration --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-duration">
                                                        @if ($durationDays !== null)
                                                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-12">{{ $durationDays }} Days</span>
                                                            <div class="small text-muted mt-0.5">{{ $durationDays - 1 }} Nights</div>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Package --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 8px;" class="col-package">
                                                        <span class="fw-semibold text-dark fs-13 d-block">{{ $pkgTitle }}</span>
                                                    </td>

                                                    {{-- Maktab --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-maktab col-hidden">
                                                        @if($campVal)
                                                            <span class="badge bg-warning text-dark px-2 py-1 fw-bold" style="font-size: 11px;">
                                                                <i class="mdi mdi-tent me-0.5"></i>{{ $campVal }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Client / Group --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 8px;" class="col-client col-hidden">
                                                        <span class="fw-semibold text-dark">{{ $b->client?->name ?? ($b->company?->company_name ?? 'Direct Passenger') }}</span>
                                                    </td>

                                                    {{-- Flight Charges --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-charges">
                                                        @if ($b->flight_charges > 0)
                                                            <strong class="text-dark fs-13">PKR {{ number_format($b->flight_charges) }}</strong>
                                                        @else
                                                            <span class="badge bg-light text-muted border">Included</span>
                                                        @endif
                                                    </td>

                                                    {{-- Status --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-status">
                                                        <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">
                                                            {{ $statusText }}
                                                        </span>
                                                    </td>

                                                    {{-- Action --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-action no-print">
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('booking.show', $b->id) }}" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" title="View Booking">
                                                                <i class="mdi mdi-eye"></i>
                                                            </a>
                                                            <a href="{{ route('booking.voucher', $b->id) }}" target="_blank" class="btn btn-xs btn-outline-success py-0 px-2 fw-semibold" title="View Voucher">
                                                                <i class="mdi mdi-ticket-percent-outline"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="24" class="text-center py-5">
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ═══════════════════════════════════════
        // COLUMN VISIBILITY TOGGLE (WITH LOCALSTORAGE PERSISTENCE)
        // ═══════════════════════════════════════
        const storageKey = 'flight_report_columns_visibility_v1';
        const defaultHiddenCols = ['given-name', 'surname', 'father-name', 'passport-doi', 'passport-exp', 'dep-pnr', 'ret-pnr', 'maktab', 'client'];

        function applyColumnVisibility(colName, isVisible) {
            const cells = document.querySelectorAll('.col-' + colName);
            cells.forEach(el => {
                if (isVisible) el.classList.remove('col-hidden');
                else el.classList.add('col-hidden');
            });
        }

        function loadSavedColumnState() {
            try {
                const saved = localStorage.getItem(storageKey);
                if (saved) return JSON.parse(saved);
            } catch (e) {
                console.error('Failed to parse column settings from localStorage', e);
            }
            return null;
        }

        function updateExportLinksWithColumns() {
            const activeCols = [];
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    activeCols.push(cb.getAttribute('data-col'));
                }
            });
            const colStr = activeCols.join(',');

            document.querySelectorAll('a[href*="report/flight/export-pdf"]').forEach(btn => {
                try {
                    const url = new URL(btn.href, window.location.origin);
                    url.searchParams.set('columns', colStr);
                    btn.href = url.pathname + url.search;
                } catch(e) {}
            });

            document.querySelectorAll('a[href*="report/flight/export-excel"]').forEach(btn => {
                try {
                    const url = new URL(btn.href, window.location.origin);
                    url.searchParams.set('columns', colStr);
                    btn.href = url.pathname + url.search;
                } catch(e) {}
            });
        }

        function saveColumnState(state) {
            try {
                localStorage.setItem(storageKey, JSON.stringify(state));
            } catch (e) {
                console.error('Failed to save column settings to localStorage', e);
            }
            updateExportLinksWithColumns();
        }

        const checkboxes = document.querySelectorAll('.col-toggle-checkbox');
        let state = loadSavedColumnState();

        if (!state) {
            state = {};
            checkboxes.forEach(cb => {
                const col = cb.getAttribute('data-col');
                state[col] = !defaultHiddenCols.includes(col);
            });
            saveColumnState(state);
        }

        // Apply state
        checkboxes.forEach(cb => {
            const col = cb.getAttribute('data-col');
            const isChecked = state[col] !== undefined ? state[col] : !defaultHiddenCols.includes(col);
            cb.checked = isChecked;
            applyColumnVisibility(col, isChecked);

            cb.addEventListener('change', function () {
                const colName = this.getAttribute('data-col');
                const checked = this.checked;
                applyColumnVisibility(colName, checked);
                state[colName] = checked;
                saveColumnState(state);
            });
        });
        updateExportLinksWithColumns();

        // Reset Columns button
        document.getElementById('btnResetColumns')?.addEventListener('click', function () {
            localStorage.removeItem(storageKey);
            checkboxes.forEach(cb => {
                const col = cb.getAttribute('data-col');
                const defaultVisible = !defaultHiddenCols.includes(col);
                cb.checked = defaultVisible;
                applyColumnVisibility(col, defaultVisible);
            });
            const freshState = {};
            checkboxes.forEach(cb => {
                freshState[cb.getAttribute('data-col')] = cb.checked;
            });
            saveColumnState(freshState);
        });
    });
</script>
@endsection
