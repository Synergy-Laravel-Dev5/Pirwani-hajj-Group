@extends('layout.master')
@section('title', 'Booking Person Report & Pilgrim Manifest')

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
                        <i class="mdi mdi-account-group text-primary me-2"></i>Official Booking Person Report & Pilgrim Manifest
                    </h4>
                    <p class="text-muted mb-0 small">Official comprehensive manifest of all registered pilgrims, photos, IDs, passports, and Maktab allocations.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('report.person.export-excel', request()->query()) }}" 
                       class="btn btn-success btn-sm"
                       title="Download person manifest as Excel spreadsheet">
                        <i class="mdi mdi-file-excel me-1"></i> Download Excel
                    </a>
                    <a href="{{ route('report.person.export-pdf', request()->query()) }}" 
                       class="btn btn-danger btn-sm"
                       title="Download person manifest as PDF document">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-dark btn-sm">
                        <i class="mdi mdi-printer me-1"></i> Print Manifest
                    </button>
                    <a href="{{ route('report.person') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </div>

            {{-- Summary Ribbon / KPI Cards --}}
            <div class="row g-2 mb-3 no-print">
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Total Pilgrims:</span>
                            <strong class="fs-18 text-primary">{{ number_format($totalPersons) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary">
                            <i class="mdi mdi-account-multiple fs-20"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">👨 Male Pilgrims:</span>
                            <strong class="fs-18 text-dark">{{ number_format($maleCount) }}</strong>
                        </div>
                        <span class="badge bg-primary text-white" style="font-size: 11px;">
                            {{ $totalPersons > 0 ? round(($maleCount / $totalPersons) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">👩 Female Pilgrims:</span>
                            <strong class="fs-18 text-danger">{{ number_format($femaleCount) }}</strong>
                        </div>
                        <span class="badge bg-danger text-white" style="font-size: 11px;">
                            {{ $totalPersons > 0 ? round(($femaleCount / $totalPersons) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Passports Recorded:</span>
                            <strong class="fs-18 text-info">{{ number_format($passportCount) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-info bg-opacity-10 text-info">
                            <i class="mdi mdi-passport fs-20"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">CNICs Identified:</span>
                            <strong class="fs-18 text-secondary">{{ number_format($cnicCount) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-secondary bg-opacity-10 text-secondary">
                            <i class="mdi mdi-card-account-details-outline fs-20"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Photos Attached:</span>
                            <strong class="fs-18 text-success">{{ number_format($photoCount) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                            <i class="mdi mdi-camera fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="card shadow-sm border-0 mb-3 no-print">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.person') }}" id="filterForm">
                        <div class="row g-2 align-items-end">

                            {{-- Search Input --}}
                            <div class="col-xl-4 col-md-5 col-12">
                                <label class="form-label small fw-semibold text-muted mb-1">Search Pilgrim</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Name, Passport #, CNIC, Phone, Hajj ID, HB #, Voucher..." 
                                           value="{{ $search }}">
                                    @if(!empty($search))
                                        <a href="{{ route('report.person', request()->except('search')) }}" class="btn btn-outline-secondary">× Clear</a>
                                    @endif
                                </div>
                            </div>

                            {{-- Gender Filter --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Gender</label>
                                <select name="gender" class="form-select form-select-sm">
                                    <option value="all" {{ ($gender == 'all' || empty($gender)) ? 'selected' : '' }}>All Genders</option>
                                    <option value="male" {{ strtolower($gender) == 'male' ? 'selected' : '' }}>👨 Male</option>
                                    <option value="female" {{ strtolower($gender) == 'female' ? 'selected' : '' }}>👩 Female</option>
                                </select>
                            </div>

                            {{-- Maktab Filter --}}
                            <div class="col-xl-3 col-md-4 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Maktab / Camp</label>
                                <select name="maktab" class="form-select form-select-sm">
                                    <option value="all" {{ ($maktab == 'all' || empty($maktab)) ? 'selected' : '' }}>All Maktabs / Camps</option>
                                    <option value="maktab_a" {{ $maktab == 'maktab_a' ? 'selected' : '' }}>⛺ Maktab A (VIP)</option>
                                    <option value="maktab_c" {{ $maktab == 'maktab_c' ? 'selected' : '' }}>⛺ Maktab C (Standard)</option>
                                    <option value="combo" {{ $maktab == 'combo' ? 'selected' : '' }}>⛺ Maktab A & C (Combo)</option>
                                    <option value="VIP Mina Camp" {{ $maktab == 'VIP Mina Camp' ? 'selected' : '' }}>⛺ VIP Mina Camp</option>
                                    <option value="Standard Camp" {{ $maktab == 'Standard Camp' ? 'selected' : '' }}>⛺ Standard Camp</option>
                                    @foreach ($distinctCamps as $campName)
                                        @if (!in_array($campName, ['Maktab A (Zone 1/2)', 'Maktab C (Zone 5)', 'Maktab A & C (Combo)', 'VIP Mina Camp', 'Standard Camp']))
                                            <option value="{{ $campName }}" {{ $maktab == $campName ? 'selected' : '' }}>⛺ {{ $campName }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- Package Filter --}}
                            <div class="col-xl-3 col-md-6 col-12">
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
                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <a href="{{ route('report.person') }}" class="btn btn-outline-secondary btn-sm px-3">
                                    Reset
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
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
                            <p class="m-0 small text-muted">OFFICIAL BOOKING PERSON & PILGRIM MANIFEST</p>
                        </div>
                    </div>
                    <div class="text-end" style="font-size: 11px;">
                        <div><strong>Printed On:</strong> {{ now()->format('d M Y, h:i A') }}</div>
                        <div><strong>Total Pilgrims:</strong> {{ $totalPersons }} ({{ $maleCount }} Male, {{ $femaleCount }} Female)</div>
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
                        <span class="badge bg-primary-subtle text-primary ms-1">{{ $persons->count() }} Pilgrims</span>
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
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="hb" checked>
                                    <span>HB Number</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="name" checked>
                                    <span>Full Name / Pilgrim</span>
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
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="dob">
                                    <span>Date of Birth / Age</span>
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
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="cnic" checked>
                                    <span>CNIC</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="phone" checked>
                                    <span>Contact / Phone</span>
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
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="package" checked>
                                    <span>Package</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="maktab" checked>
                                    <span>Maktab / Camp</span>
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
                                    <th style="width: 90px;" class="col-hb">HB</th>
                                    <th style="width: 220px; text-align: left; padding-left: 10px;" class="col-name">FULL NAME</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-given-name col-hidden">GIVEN NAME</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-surname col-hidden">SURNAME</th>
                                    <th style="width: 150px; text-align: left; padding-left: 10px;" class="col-father-name col-hidden">HUSBAND / FATHER NAME</th>
                                    <th style="width: 80px;" class="col-gender">GENDER</th>
                                    <th style="width: 100px;" class="col-dob col-hidden">DOB / AGE</th>
                                    <th style="width: 110px;" class="col-passport">PASSPORT</th>
                                    <th style="width: 110px;" class="col-passport-doi col-hidden">DATE OF ISSUE</th>
                                    <th style="width: 115px;" class="col-passport-exp col-hidden">EXPIRY DATE</th>
                                    <th style="width: 120px;" class="col-cnic">CNIC</th>
                                    <th style="width: 110px;" class="col-phone">PHONE</th>
                                    <th style="width: 110px;" class="col-voucher">VOUCHER #</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-client col-hidden">CLIENT / GROUP</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-package">PACKAGE</th>
                                    <th style="width: 120px;" class="col-maktab">MAKTAB / CAMP</th>
                                    <th style="width: 80px;" class="col-action no-print">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($persons as $idx => $person)
                                    @php
                                        $booking = $person->booking;
                                        $isFemale = strtolower($person->gender ?? '') === 'female';
                                        
                                        // Photo URL resolution
                                        $photoUrl = null;
                                        if (!empty($person->photo)) {
                                            if (file_exists(public_path($person->photo))) {
                                                $photoUrl = asset($person->photo);
                                            } else {
                                                $photoUrl = 'https://pirwanitravels.com.pk/' . ltrim($person->photo, '/');
                                            }
                                        }

                                        // Age calculation
                                        $age = null;
                                        if (!empty($person->dob)) {
                                            try {
                                                $age = \Carbon\Carbon::parse($person->dob)->age;
                                            } catch (\Exception $e) {}
                                        }

                                        // Passport expiry validity check
                                        $passportExp = null;
                                        $isExpiringSoon = false;
                                        if (!empty($person->passport_expiry_date)) {
                                            try {
                                                $passportExp = \Carbon\Carbon::parse($person->passport_expiry_date);
                                                $isExpiringSoon = $passportExp->diffInMonths(now()) < 6 && $passportExp->isFuture();
                                            } catch (\Exception $e) {}
                                        }

                                        $campVal = $booking?->camp ?: ($booking?->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : null);
                                    @endphp
                                    <tr>
                                        {{-- SR --}}
                                        <td class="text-center fw-bold text-muted col-sr">{{ $idx + 1 }}</td>

                                        {{-- Photo --}}
                                        <td class="text-center col-picture">
                                            @if ($photoUrl)
                                                <a href="{{ $photoUrl }}" target="_blank" title="View Full Photo">
                                                    <img src="{{ $photoUrl }}" alt="" class="haji-photo"
                                                         onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                                    <div class="haji-photo-placeholder" style="display:none;">
                                                        {{ strtoupper(substr($person->full_name ?: 'P', 0, 1)) }}
                                                    </div>
                                                </a>
                                            @else
                                                <div class="haji-photo-placeholder">
                                                    {{ strtoupper(substr($person->full_name ?: 'P', 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Hajj ID --}}
                                        <td class="text-center fw-bold col-hajj-id">
                                            {{ $person->hajj_id ?: '—' }}
                                        </td>

                                        {{-- HB --}}
                                        <td class="text-center col-hb">
                                            @if (!empty($person->hb_number))
                                                <span class="badge bg-warning-subtle text-dark border border-warning px-1 py-0" style="font-size: 11px; font-weight: 700;">
                                                    {{ $person->hb_number }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Full Name --}}
                                        <td style="padding-left: 10px;" class="col-name">
                                            <strong class="text-dark d-block" style="font-size: 13.5px;">
                                                {{ $person->full_name }}
                                            </strong>
                                            @if (!empty($person->surname) && $person->surname !== $person->full_name)
                                                <small class="text-muted">{{ $person->given_name }} {{ $person->surname }}</small>
                                            @endif
                                        </td>

                                        {{-- Given Name --}}
                                        <td style="padding-left: 10px;" class="col-given-name col-hidden">
                                            <span class="text-dark fw-semibold text-uppercase">{{ $person->given_name ?: '—' }}</span>
                                        </td>

                                        {{-- Surname --}}
                                        <td style="padding-left: 10px;" class="col-surname col-hidden">
                                            <span class="text-dark fw-semibold text-uppercase">{{ $person->surname ?: '—' }}</span>
                                        </td>

                                        {{-- Father Name --}}
                                        <td style="padding-left: 10px;" class="col-father-name col-hidden">
                                            <span class="text-dark">{{ $person->father_name ?: '—' }}</span>
                                        </td>

                                        {{-- Gender --}}
                                        <td class="text-center fw-bold col-gender {{ $isFemale ? 'gender-female' : 'gender-male' }}">
                                            {{ strtoupper($person->gender ?: 'MALE') }}
                                        </td>

                                        {{-- DOB / Age --}}
                                        <td class="text-center col-dob col-hidden">
                                            @if (!empty($person->dob))
                                                <div>{{ \Carbon\Carbon::parse($person->dob)->format('d M Y') }}</div>
                                                @if ($age !== null)
                                                    <small class="text-muted">({{ $age }} yrs)</small>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Passport --}}
                                        <td class="text-center fw-bold text-dark col-passport">
                                            {{ $person->passport_number ?: '—' }}
                                        </td>

                                        {{-- Date of Issue --}}
                                        @php
                                            $rawDoi = $person->date_of_issue ?: $person->passport_issue_date;
                                            $passportDoi = null;
                                            if (!empty($rawDoi)) {
                                                try {
                                                    $passportDoi = \Carbon\Carbon::parse($rawDoi);
                                                } catch (\Exception $e) {}
                                            }
                                        @endphp
                                        <td class="text-center col-passport-doi col-hidden">
                                            @if ($passportDoi)
                                                <span>{{ $passportDoi->format('d M Y') }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Passport Expiry --}}
                                        <td class="text-center col-passport-exp col-hidden">
                                            @if ($passportExp)
                                                <span class="{{ $isExpiringSoon ? 'text-danger fw-bold' : '' }}">{{ $passportExp->format('d M Y') }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- CNIC --}}
                                        <td class="text-center col-cnic">
                                            {{ $person->cnic ?: '—' }}
                                        </td>

                                        {{-- Phone --}}
                                        <td class="text-center col-phone">
                                            {{ $person->phone ?: '—' }}
                                        </td>

                                        {{-- Voucher / Booking # --}}
                                        <td class="text-center col-voucher">
                                            @if ($booking)
                                                <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                                    {{ $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)) }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Client / Group --}}
                                        <td style="padding-left: 10px;" class="col-client col-hidden">
                                            @if ($booking)
                                                <span class="text-dark">{{ $booking->client ? $booking->client->name : ($booking->company ? $booking->company->company_name : 'Direct Booking') }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Package --}}
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

                                        {{-- Maktab / Camp --}}
                                        <td class="text-center col-maktab">
                                            @if($campVal)
                                                <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1" style="font-size: 11px; font-weight: 700;">
                                                    <i class="mdi mdi-tent me-1 text-primary"></i>{{ $campVal }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Action --}}
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
                                @empty
                                    <tr>
                                        <td colspan="20" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="mdi mdi-account-search text-muted" style="font-size: 48px;"></i>
                                                <h5 class="fw-bold text-muted mt-2">No Booking Persons Found</h5>
                                                <p class="text-muted small mb-3">No pilgrims match your search criteria or selected filters.</p>
                                                <a href="{{ route('report.person') }}" class="btn btn-primary btn-sm">
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
        const defaultVisibleCols = ['sr', 'picture', 'hajj-id', 'hb', 'name', 'gender', 'passport', 'cnic', 'phone', 'voucher', 'package', 'maktab', 'action'];
        const storageKey = 'person_manifest_columns_pref_v1';

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
