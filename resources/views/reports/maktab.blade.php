@extends('layout.master')
@section('title', 'Maktab Report & Manifest')

@section('content')
<style>
    /* Official Manifest Table Styling matching Rooming List */
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
        font-size: 13px;
        text-align: center;
        vertical-align: middle;
        padding: 10px 6px;
        border: 2px solid #000000 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .manifest-table td {
        padding: 8px 6px;
        vertical-align: middle;
        font-size: 13px;
        border: 1.5px solid #000000 !important;
    }
    .room-divider-row td {
        border-top: 3px solid #000000 !important;
    }
    .haji-photo {
        width: 50px;
        height: 55px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #999;
        display: block;
        margin: 0 auto;
    }
    .haji-photo-placeholder {
        width: 50px;
        height: 55px;
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
                        <i class="mdi mdi-tent text-primary me-2"></i>Official Maktab Report & Manifest
                    </h4>
                    <p class="text-muted mb-0 small">Official Maktab-wise manifests, multi-stay hotel schedules, gender breakdown, and pilgrim allocations.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('report.maktab.export-excel', request()->query()) }}" 
                       class="btn btn-success btn-sm"
                       title="Download official maktab report as Excel spreadsheet">
                        <i class="mdi mdi-file-excel me-1"></i> Download Excel
                    </a>
                    <a href="{{ route('report.maktab.export-pdf', request()->query()) }}" 
                       class="btn btn-danger btn-sm"
                       title="Download official maktab report as PDF document">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-dark btn-sm">
                        <i class="mdi mdi-printer me-1"></i> Print Manifest
                    </button>
                    <a href="{{ route('report.maktab') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </div>

            {{-- Summary Ribbon / KPI Cards --}}
            <div class="row g-2 mb-3 no-print">
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">
                                {{ (empty($locationFilter) || $locationFilter === 'all') ? 'Total Bookings:' : 'Total Stays:' }}
                            </span>
                            <strong class="fs-18 text-primary">{{ number_format($totalStays) }}</strong>
                        </div>
                        <span class="badge bg-primary text-white" style="font-size: 11px;">
                            {{ $totalRooms }} Rooms
                        </span>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Total Pilgrims:</span>
                            <strong class="fs-18 text-dark">{{ number_format($totalPilgrims) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-dark bg-opacity-10 text-dark">
                            <i class="mdi mdi-account-group fs-20"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">👨 Male Pilgrims:</span>
                            <strong class="fs-18 text-primary">{{ number_format($totalMalePilgrims) }}</strong>
                        </div>
                        <span class="badge bg-primary text-white" style="font-size: 11px;">
                            {{ $totalPilgrims > 0 ? round(($totalMalePilgrims / $totalPilgrims) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">👩 Female Pilgrims:</span>
                            <strong class="fs-18 text-danger">{{ number_format($totalFemalePilgrims) }}</strong>
                        </div>
                        <span class="badge bg-danger text-white" style="font-size: 11px;">
                            {{ $totalPilgrims > 0 ? round(($totalFemalePilgrims / $totalPilgrims) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Active In-House:</span>
                            <strong class="fs-18 text-success">{{ number_format($activeInHouse) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                            <i class="mdi mdi-bed-king fs-20"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Today's Check-in:</span>
                            <strong class="fs-18 text-warning">{{ number_format($todayCheckins) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning">
                            <i class="mdi mdi-calendar-today fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Bar (Maktab, Location, Gender) --}}
            <div class="card shadow-sm border-0 mb-3 no-print">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.maktab') }}" id="filterForm">
                        <div class="row g-2 align-items-end">

                            {{-- Maktab Filter --}}
                            <div class="col-xl-4 col-md-4 col-12">
                                <label class="form-label small fw-semibold text-muted mb-1">
                                    <i class="mdi mdi-tent text-warning me-1"></i>Maktab / Camp Category
                                </label>
                                <select name="maktab" class="form-select form-select-sm">
                                    <option value="all" {{ ($maktabFilter == 'all' || empty($maktabFilter)) ? 'selected' : '' }}>All Maktabs / Camps</option>
                                    <option value="maktab_a" {{ $maktabFilter == 'maktab_a' ? 'selected' : '' }}>⛺ Maktab A (Zone 1 or 2 - VIP)</option>
                                    <option value="maktab_c" {{ $maktabFilter == 'maktab_c' ? 'selected' : '' }}>⛺ Maktab C (Zone 5 - Standard)</option>
                                    <option value="combo" {{ $maktabFilter == 'combo' ? 'selected' : '' }}>⛺ Maktab A & C (Combo)</option>
                                    <option value="VIP Mina Camp" {{ $maktabFilter == 'VIP Mina Camp' ? 'selected' : '' }}>⛺ VIP Mina Camp</option>
                                    <option value="Standard Camp" {{ $maktabFilter == 'Standard Camp' ? 'selected' : '' }}>⛺ Standard Camp</option>
                                    @foreach ($distinctCamps as $campName)
                                        @if (!in_array($campName, ['Maktab A (Zone 1/2)', 'Maktab C (Zone 5)', 'Maktab A & C (Combo)', 'VIP Mina Camp', 'Standard Camp']))
                                            <option value="{{ $campName }}" {{ $maktabFilter == $campName ? 'selected' : '' }}>⛺ {{ $campName }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- Location Filter --}}
                            <div class="col-xl-3 col-md-3 col-12">
                                <label class="form-label small fw-semibold text-muted mb-1">
                                    <i class="mdi mdi-map-marker text-danger me-1"></i>Location Filter
                                </label>
                                <select name="location" class="form-select form-select-sm">
                                    <option value="all" {{ ($locationFilter == 'all' || empty($locationFilter)) ? 'selected' : '' }}>
                                        All Locations (Single Row / Pilgrim)
                                    </option>
                                    @foreach ($distinctLocations as $locName)
                                        @php
                                            $locDisplay = match(strtolower($locName)) {
                                                'makkah'  => 'Makkah',
                                                'madinah' => 'Madinah',
                                                'mina'    => 'Mina',
                                                'azizia'  => 'Azizia',
                                                'jeddah'  => 'Jeddah',
                                                default   => ucfirst($locName),
                                            };
                                        @endphp
                                        <option value="{{ $locName }}" {{ strtolower($locationFilter) == strtolower($locName) ? 'selected' : '' }}>
                                            📍 {{ $locDisplay }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Gender Filter --}}
                            <div class="col-xl-3 col-md-3 col-12">
                                <label class="form-label small fw-semibold text-muted mb-1">
                                    <i class="mdi mdi-gender-male-female text-primary me-1"></i>Gender Filter
                                </label>
                                <select name="gender" class="form-select form-select-sm">
                                    <option value="all" {{ ($genderFilter == 'all' || empty($genderFilter)) ? 'selected' : '' }}>All Genders (Male & Female)</option>
                                    <option value="male" {{ strtolower($genderFilter) == 'male' ? 'selected' : '' }}>👨 Male Only</option>
                                    <option value="female" {{ strtolower($genderFilter) == 'female' ? 'selected' : '' }}>👩 Female Only</option>
                                </select>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="col-xl-2 col-md-2 col-12 d-flex gap-2">
                                <a href="{{ route('report.maktab') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                                    Reset
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold">
                                    <i class="mdi mdi-filter me-1"></i> Apply
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
                            <p class="m-0 small text-muted">OFFICIAL MAKTAB REPORT & ROOMING MANIFEST</p>
                        </div>
                    </div>
                    <div class="text-end" style="font-size: 11px;">
                        <div><strong>Printed On:</strong> {{ now()->format('d M Y, h:i A') }}</div>
                        <div><strong>Maktab:</strong> {{ (!empty($maktabFilter) && $maktabFilter !== 'all') ? ucfirst(str_replace('_', ' ', $maktabFilter)) : 'All Maktabs' }}</div>
                        <div><strong>Location:</strong> {{ (!empty($locationFilter) && $locationFilter !== 'all') ? ucfirst($locationFilter) : 'All Locations' }}</div>
                        <div><strong>Gender:</strong> {{ (!empty($genderFilter) && $genderFilter !== 'all') ? ucfirst($genderFilter) : 'All Genders' }}</div>
                        <div><strong>Total Records:</strong> {{ $totalStays }} Records ({{ $totalPilgrims }} Pilgrims)</div>
                    </div>
                </div>
            </div>

            {{-- OFFICIAL MANIFEST TABLE VIEW (MATCHING ROOMING LIST) --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark fs-14">
                            <i class="mdi mdi-table-eye text-primary me-1"></i>Manifest Columns
                        </span>
                        <span class="badge bg-light text-muted border">Customizable View</span>
                        <span class="badge bg-primary-subtle text-primary ms-1">
                            {{ $totalStays }} {{ (empty($locationFilter) || $locationFilter === 'all') ? 'Bookings' : 'Stays' }} ({{ $totalPilgrims }} Pilgrims)
                        </span>
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
                                    <span>Haji Picture</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="hajj-id" checked>
                                    <span>Hajj ID</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="hb">
                                    <span>HB Number</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="pilgrim" checked>
                                    <span>Pilgrim Full Name</span>
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
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="gender" checked>
                                    <span>Gender</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="passport" checked>
                                    <span>Passport No</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="hotel" checked>
                                    <span>Hotel & Location</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="room" checked>
                                    <span>Room Details</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="maktab" checked>
                                    <span>Maktab / Camp</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="package" checked>
                                    <span>Package</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="voucher" checked>
                                    <span>Voucher / Booking #</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="client">
                                    <span>Client / Group</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="status" checked>
                                    <span>Status</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="action" checked>
                                    <span>Action</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="manifest-table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="col-sr">SR</th>
                                    <th style="width: 80px;" class="col-picture">PICTURE</th>
                                    <th style="width: 95px;" class="col-hajj-id">HAJJ ID</th>
                                    <th style="width: 90px;" class="col-hb col-hidden">HB</th>
                                    <th style="width: 220px; text-align: left; padding-left: 10px;" class="col-pilgrim">PILGRIM FULL NAME</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-given-name col-hidden">GIVEN NAME</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-surname col-hidden">SURNAME</th>
                                    <th style="width: 80px;" class="col-gender">GENDER</th>
                                    <th style="width: 110px;" class="col-passport">PASSPORT</th>
                                    <th style="width: 200px; text-align: left; padding-left: 10px;" class="col-hotel">HOTEL & LOCATION</th>
                                    <th style="width: 140px;" class="col-room">ROOM DETAILS</th>
                                    <th style="width: 130px;" class="col-maktab">MAKTAB / CAMP</th>
                                    <th style="width: 150px; text-align: left; padding-left: 10px;" class="col-package">PACKAGE</th>
                                    <th style="width: 110px;" class="col-voucher">VOUCHER #</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-client col-hidden">CLIENT / GROUP</th>
                                    <th style="width: 100px;" class="col-status">STATUS</th>
                                    <th style="width: 80px;" class="col-action no-print">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $globalSr = 1;
                                    $isAllLoc = empty($locationFilter) || $locationFilter === 'all';
                                @endphp
                                @forelse ($hotelStays as $idx => $stay)
                                    @php
                                        $booking = $stay->booking;
                                        $rawPersons = $booking ? $booking->persons : collect([]);
                                        
                                        // Strictly filter pilgrims of the chosen gender
                                        if (!empty($genderFilter) && $genderFilter !== 'all') {
                                            $gLower = strtolower(trim($genderFilter));
                                            $persons = $rawPersons->filter(function($p) use ($gLower) {
                                                return strtolower(trim($p->gender ?? '')) === $gLower;
                                            })->values();

                                            if ($persons->isEmpty()) {
                                                continue;
                                            }
                                        } else {
                                            $persons = $rawPersons;
                                        }

                                        $totalPax = $persons->count();
                                        $rowspan = max(1, $totalPax);
                                        $allStays = $stay->all_stays ?? collect([$stay]);

                                        $checkIn = $stay->check_in ? \Carbon\Carbon::parse($stay->check_in) : null;
                                        $checkOut = $stay->check_out ? \Carbon\Carbon::parse($stay->check_out) : null;

                                        // Determine Status
                                        $statusText = 'Scheduled';
                                        $statusBadge = 'bg-secondary text-white';
                                        if ($checkIn) {
                                            if ($checkIn->isToday()) {
                                                $statusText = 'Checking In';
                                                $statusBadge = 'bg-danger text-white';
                                            } elseif ($checkIn->isTomorrow()) {
                                                $statusText = 'Tomorrow';
                                                $statusBadge = 'bg-warning text-dark';
                                            } elseif ($checkIn->isFuture()) {
                                                $statusText = 'Upcoming';
                                                $statusBadge = 'bg-info text-white';
                                            } elseif ($checkOut && $checkOut->isPast()) {
                                                $statusText = 'Checked Out';
                                                $statusBadge = 'bg-light text-muted border';
                                            } elseif ($checkIn->isPast() && ($checkOut ? $checkOut->isFuture() || $checkOut->isToday() : true)) {
                                                $statusText = 'In-House';
                                                $statusBadge = 'bg-success text-white';
                                            }
                                        }

                                        $campVal = $booking?->camp ?: ($booking?->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : null);
                                    @endphp

                                    @if($totalPax === 0)
                                        {{-- Stay without detailed persons --}}
                                        <tr class="room-divider-row">
                                            <td class="text-center fw-bold text-muted col-sr">{{ $globalSr++ }}</td>
                                            <td class="text-center col-picture">—</td>
                                            <td class="text-center col-hajj-id">—</td>
                                            <td class="text-center col-hb col-hidden">—</td>
                                            <td style="padding-left: 10px;" class="col-pilgrim">
                                                <strong class="text-dark">{{ $booking?->client?->name ?? 'Direct Pilgrim' }}</strong>
                                            </td>
                                            <td class="text-center text-muted col-given-name col-hidden">—</td>
                                            <td class="text-center text-muted col-surname col-hidden">—</td>
                                            <td class="text-center col-gender">—</td>
                                            <td class="text-center col-passport">{{ $booking?->passport_number ?: '—' }}</td>

                                            {{-- Hotel & Location Cell --}}
                                            <td style="padding-left: 10px;" class="col-hotel">
                                                @if($isAllLoc)
                                                    <div class="d-flex flex-column gap-1">
                                                        @foreach($allStays as $st)
                                                            @php
                                                                $stLoc = strtolower(trim($st->location ?? ''));
                                                                $stBadge = match($stLoc) {
                                                                    'makkah'  => 'bg-success text-white',
                                                                    'madinah' => 'bg-info text-dark',
                                                                    'azizia'  => 'bg-warning text-dark',
                                                                    'mina'    => 'bg-dark text-white',
                                                                    'jeddah'  => 'bg-primary text-white',
                                                                    default   => 'bg-secondary text-white',
                                                                };
                                                            @endphp
                                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                                <span class="badge {{ $stBadge }} px-1.5 py-0.5" style="font-size: 9.5px; font-weight: 700;">
                                                                    {{ strtoupper($st->location ?: 'LOC') }}
                                                                </span>
                                                                <strong class="text-dark" style="font-size: 12px;">
                                                                    {{ strtoupper($st->hotel_name ?: 'Hotel') }}
                                                                </strong>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    @php
                                                        $loc = strtolower(trim($stay->location ?? ''));
                                                        $locBadgeClass = match($loc) {
                                                            'makkah'  => 'bg-success text-white',
                                                            'madinah' => 'bg-info text-dark',
                                                            'azizia'  => 'bg-warning text-dark',
                                                            'mina'    => 'bg-dark text-white',
                                                            'jeddah'  => 'bg-primary text-white',
                                                            default   => 'bg-secondary text-white',
                                                        };
                                                    @endphp
                                                    <strong class="text-dark d-block fs-13">{{ strtoupper($stay->hotel_name ?: 'Hotel Not Specified') }}</strong>
                                                    <span class="badge {{ $locBadgeClass }} px-2 py-0 mt-1" style="font-size: 10px;">{{ strtoupper($stay->location ?: 'Location') }}</span>
                                                @endif
                                            </td>

                                            {{-- Room Details Cell --}}
                                            <td class="text-center col-room">
                                                @if($isAllLoc)
                                                    <div class="d-flex flex-column gap-1">
                                                        @foreach($allStays as $st)
                                                            <div class="small">
                                                                <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 10px;">
                                                                    {{ ucfirst($st->room_type ?: 'Standard') }}
                                                                </span>
                                                                <span class="text-muted" style="font-size: 10.5px;">{{ $st->no_of_rooms ?: 1 }} R</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="badge bg-dark text-white px-2 py-1" style="font-size: 11px;">{{ ucfirst($stay->room_type ?: 'Standard') }}</span>
                                                    <div class="small text-muted mt-1">{{ $stay->no_of_rooms ?: 1 }} Room(s)</div>
                                                @endif
                                            </td>

                                            <td class="text-center col-maktab">
                                                @if($campVal)
                                                    <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1" style="font-size: 11px; font-weight: 700;">
                                                        <i class="mdi mdi-tent me-1 text-primary"></i>{{ $campVal }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td style="padding-left: 10px;" class="col-package">
                                                <div class="fw-semibold text-primary fs-12">
                                                    @if ($booking && $booking->package)
                                                        @if ($booking->package->package_code)
                                                            <span class="badge bg-dark text-white me-1" style="font-size: 9.5px;">{{ $booking->package->package_code }}</span>
                                                        @endif
                                                        {{ $booking->package->package_title ?: ($booking->package->name ?: 'Package') }}
                                                    @elseif ($booking)
                                                        {{ strtoupper($booking->package_type ?? 'Hajj / Umrah') }}
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="text-center col-voucher">
                                                @if ($booking)
                                                    <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                                        {{ $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td style="padding-left: 10px;" class="col-client col-hidden">
                                                <span class="text-dark">{{ $booking?->client?->name ?? 'Direct' }}</span>
                                            </td>

                                            <td class="text-center col-status">
                                                <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">{{ $statusText }}</span>
                                            </td>

                                            <td class="text-center no-print col-action">
                                                @if ($booking)
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-light border text-primary" title="View Booking Details">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="btn btn-light border text-success" title="Print / Download Voucher">
                                                            <i class="mdi mdi-ticket-percent-outline"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Iterate over occupants --}}
                                        @foreach($persons as $occIdx => $p)
                                            @php
                                                $pIsFemale = strtolower($p->gender ?? '') === 'female';
                                                $pPhotoUrl = null;
                                                if (!empty($p->photo)) {
                                                    if (file_exists(public_path($p->photo))) {
                                                        $pPhotoUrl = asset($p->photo);
                                                    } else {
                                                        $pPhotoUrl = 'https://pirwanitravels.com.pk/' . ltrim($p->photo, '/');
                                                    }
                                                }
                                            @endphp
                                            <tr class="{{ $occIdx === 0 ? 'room-divider-row' : '' }}">
                                                {{-- SR --}}
                                                <td class="text-center fw-bold text-muted col-sr">{{ $globalSr++ }}</td>

                                                {{-- Photo --}}
                                                <td class="text-center col-picture">
                                                    @if ($pPhotoUrl)
                                                        <a href="{{ $pPhotoUrl }}" target="_blank" title="View Full Photo">
                                                            <img src="{{ $pPhotoUrl }}" alt="" class="haji-photo"
                                                                 onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                                            <div class="haji-photo-placeholder" style="display:none;">
                                                                {{ strtoupper(substr($p->full_name ?: 'P', 0, 1)) }}
                                                            </div>
                                                        </a>
                                                    @else
                                                        <div class="haji-photo-placeholder">
                                                            {{ strtoupper(substr($p->full_name ?: 'P', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </td>

                                                {{-- Hajj ID --}}
                                                <td class="text-center fw-bold col-hajj-id">
                                                    {{ $p->hajj_id ?: '—' }}
                                                </td>

                                                {{-- HB --}}
                                                <td class="text-center col-hb col-hidden">
                                                    @if (!empty($p->hb_number))
                                                        <span class="badge bg-warning-subtle text-dark border border-warning px-1 py-0" style="font-size: 11px; font-weight: 700;">
                                                            {{ $p->hb_number }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                {{-- Full Name --}}
                                                <td style="padding-left: 10px;" class="col-pilgrim">
                                                    <strong class="text-dark d-block" style="font-size: 13.5px;">
                                                        {{ $p->full_name }}
                                                    </strong>
                                                    @if (!empty($p->surname) && $p->surname !== $p->full_name)
                                                        <small class="text-muted">{{ $p->given_name }} {{ $p->surname }}</small>
                                                    @endif
                                                </td>

                                                {{-- Given Name --}}
                                                <td style="padding-left: 10px;" class="col-given-name col-hidden">
                                                    <span class="text-dark fw-semibold text-uppercase">{{ $p->given_name ?: '—' }}</span>
                                                </td>

                                                {{-- Surname --}}
                                                <td style="padding-left: 10px;" class="col-surname col-hidden">
                                                    <span class="text-dark fw-semibold text-uppercase">{{ $p->surname ?: '—' }}</span>
                                                </td>

                                                {{-- Gender --}}
                                                <td class="text-center fw-bold col-gender {{ $pIsFemale ? 'gender-female' : 'gender-male' }}">
                                                    {{ strtoupper($p->gender ?: 'MALE') }}
                                                </td>

                                                {{-- Passport --}}
                                                <td class="text-center fw-bold text-dark col-passport">
                                                    {{ $p->passport_number ?: '—' }}
                                                </td>

                                                {{-- Spanned Columns --}}
                                                @if($occIdx === 0)
                                                    {{-- Hotel & Location Cell --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 10px;" class="col-hotel">
                                                        @if($isAllLoc)
                                                            <div class="d-flex flex-column gap-1">
                                                                @foreach($allStays as $st)
                                                                    @php
                                                                        $stLoc = strtolower(trim($st->location ?? ''));
                                                                        $stBadge = match($stLoc) {
                                                                            'makkah'  => 'bg-success text-white',
                                                                            'madinah' => 'bg-info text-dark',
                                                                            'azizia'  => 'bg-warning text-dark',
                                                                            'mina'    => 'bg-dark text-white',
                                                                            'jeddah'  => 'bg-primary text-white',
                                                                            default   => 'bg-secondary text-white',
                                                                        };
                                                                    @endphp
                                                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                                        <span class="badge {{ $stBadge }} px-1.5 py-0.5" style="font-size: 9.5px; font-weight: 700;">
                                                                            {{ strtoupper($st->location ?: 'LOC') }}
                                                                        </span>
                                                                        <strong class="text-dark" style="font-size: 12px;">
                                                                            {{ strtoupper($st->hotel_name ?: 'Hotel') }}
                                                                        </strong>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            @php
                                                                $loc = strtolower(trim($stay->location ?? ''));
                                                                $locBadgeClass = match($loc) {
                                                                    'makkah'  => 'bg-success text-white',
                                                                    'madinah' => 'bg-info text-dark',
                                                                    'azizia'  => 'bg-warning text-dark',
                                                                    'mina'    => 'bg-dark text-white',
                                                                    'jeddah'  => 'bg-primary text-white',
                                                                    default   => 'bg-secondary text-white',
                                                                };
                                                            @endphp
                                                            <strong class="text-dark d-block fs-13">{{ strtoupper($stay->hotel_name ?: 'Hotel Not Specified') }}</strong>
                                                            <span class="badge {{ $locBadgeClass }} px-2 py-0 mt-1" style="font-size: 10px;">{{ strtoupper($stay->location ?: 'Location') }}</span>
                                                        @endif
                                                    </td>

                                                    {{-- Room Details Cell --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-room">
                                                        @if($isAllLoc)
                                                            <div class="d-flex flex-column gap-1">
                                                                @foreach($allStays as $st)
                                                                    <div class="small">
                                                                        <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 10px;">
                                                                            {{ ucfirst($st->room_type ?: 'Standard') }}
                                                                        </span>
                                                                        <span class="text-muted" style="font-size: 10.5px;">{{ $st->no_of_rooms ?: 1 }} R</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="badge bg-dark text-white px-2 py-1" style="font-size: 11px;">{{ ucfirst($stay->room_type ?: 'Standard') }}</span>
                                                            <div class="small text-muted mt-1">{{ $stay->no_of_rooms ?: 1 }} Room(s)</div>
                                                        @endif
                                                    </td>

                                                    <td rowspan="{{ $rowspan }}" class="text-center col-maktab">
                                                        @if($campVal)
                                                            <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1" style="font-size: 11px; font-weight: 700;">
                                                                <i class="mdi mdi-tent me-1 text-primary"></i>{{ $campVal }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 10px;" class="col-package">
                                                        <div class="fw-semibold text-primary fs-12">
                                                            @if ($booking && $booking->package)
                                                                @if ($booking->package->package_code)
                                                                    <span class="badge bg-dark text-white me-1" style="font-size: 9.5px;">{{ $booking->package->package_code }}</span>
                                                                @endif
                                                                {{ $booking->package->package_title ?: ($booking->package->name ?: 'Package') }}
                                                            @elseif ($booking)
                                                                {{ strtoupper($booking->package_type ?? 'Hajj / Umrah') }}
                                                            @else
                                                                —
                                                            @endif
                                                        </div>
                                                    </td>

                                                    <td rowspan="{{ $rowspan }}" class="text-center col-voucher">
                                                        @if ($booking)
                                                            <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                                                {{ $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)) }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 10px;" class="col-client col-hidden">
                                                        <span class="text-dark">{{ $booking?->client?->name ?? 'Direct' }}</span>
                                                    </td>

                                                    <td rowspan="{{ $rowspan }}" class="text-center col-status">
                                                        <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">{{ $statusText }}</span>
                                                    </td>

                                                    <td rowspan="{{ $rowspan }}" class="text-center no-print col-action">
                                                        @if ($booking)
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-light border text-primary" title="View Booking Details">
                                                                    <i class="mdi mdi-eye"></i>
                                                                </a>
                                                                <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="btn btn-light border text-success" title="Print / Download Voucher">
                                                                    <i class="mdi mdi-ticket-percent-outline"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="17" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="mdi mdi-tent text-muted" style="font-size: 48px;"></i>
                                                <h5 class="fw-bold text-muted mt-2">No Maktab Records Found</h5>
                                                <p class="text-muted small mb-3">No bookings match the selected Maktab, Location, or Gender filter criteria.</p>
                                                <a href="{{ route('report.maktab') }}" class="btn btn-primary btn-sm">
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
    document.addEventListener('DOMContentLoaded', function() {
        // ==========================================
        // DYNAMIC COLUMN VISIBILITY CONTROLLER
        // ==========================================
        const colCheckboxes = document.querySelectorAll('.col-toggle-checkbox');
        const defaultVisibleCols = ['sr', 'picture', 'hajj-id', 'pilgrim', 'gender', 'passport', 'hotel', 'room', 'maktab', 'package', 'voucher', 'status', 'action'];
        const storageKey = 'maktab_manifest_columns_pref_v1';

        function applyColumnVisibility(colName, isVisible) {
            const cells = document.querySelectorAll(`.col-${colName}`);
            cells.forEach(el => {
                if (isVisible) {
                    el.classList.remove('col-hidden');
                } else {
                    el.classList.add('col-hidden');
                }
            });
        }

        function saveColumnPreferences() {
            const prefs = {};
            colCheckboxes.forEach(cb => {
                const col = cb.getAttribute('data-col');
                prefs[col] = cb.checked;
            });
            try {
                localStorage.setItem(storageKey, JSON.stringify(prefs));
            } catch(e) {}
        }

        function loadColumnPreferences() {
            try {
                const saved = localStorage.getItem(storageKey);
                if (saved) {
                    const prefs = JSON.parse(saved);
                    colCheckboxes.forEach(cb => {
                        const col = cb.getAttribute('data-col');
                        if (typeof prefs[col] !== 'undefined') {
                            cb.checked = !!prefs[col];
                            applyColumnVisibility(col, !!prefs[col]);
                        }
                    });
                    return;
                }
            } catch(e) {}

            // By default: ONLY show the original default columns, keep extra columns unchecked & hidden
            colCheckboxes.forEach(cb => {
                const col = cb.getAttribute('data-col');
                const isDefault = defaultVisibleCols.includes(col);
                cb.checked = isDefault;
                applyColumnVisibility(col, isDefault);
            });
        }

        colCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const col = this.getAttribute('data-col');
                applyColumnVisibility(col, this.checked);
                saveColumnPreferences();
            });
        });

        // Reset Columns button (Revert to original default columns)
        document.getElementById('btnResetColumns')?.addEventListener('click', function(e) {
            e.stopPropagation();
            colCheckboxes.forEach(cb => {
                const col = cb.getAttribute('data-col');
                const isDefault = defaultVisibleCols.includes(col);
                cb.checked = isDefault;
                applyColumnVisibility(col, isDefault);
            });
            try {
                localStorage.removeItem(storageKey);
            } catch(e) {}
        });

        // Load saved column preferences on page load
        loadColumnPreferences();
    });
</script>
@endsection
