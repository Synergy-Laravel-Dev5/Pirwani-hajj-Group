@extends('layout.master')
@section('title', 'Booking Person Report')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                {{-- Page Title & Actions --}}
                <div class="py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                    <div>
                        <h4 class="fs-20 fw-bold m-0 text-dark">
                            <i class="mdi mdi-account-group text-primary me-2"></i>Booking Person Report & Pilgrim Manifest
                        </h4>
                        <p class="text-muted mb-0 small">Comprehensive database of all registered pilgrims, passport details, CNIC, Maktab allocations, and vouchers.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('report.person') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-refresh me-1"></i> Reset Filters
                        </a>
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Print Manifest
                        </button>
                    </div>
                </div>

                {{-- KPI Summary Cards --}}
                <div class="row g-3 mb-4 no-print">
                    {{-- Total Pilgrims --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Total Pilgrims</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalPersons) }}</h3>
                                        <span class="badge bg-white text-primary px-2 py-0" style="font-size:11px;">Hajji / Guests</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-account-multiple fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Male Pilgrims --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Male Pilgrims</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($maleCount) }}</h3>
                                        <span class="badge bg-white text-success px-2 py-0" style="font-size:11px;">
                                            {{ $totalPersons > 0 ? round(($maleCount / $totalPersons) * 100) : 0 }}% of Total
                                        </span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-gender-male fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Female Pilgrims --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Female Pilgrims</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($femaleCount) }}</h3>
                                        <span class="badge bg-white text-danger px-2 py-0" style="font-size:11px;">
                                            {{ $totalPersons > 0 ? round(($femaleCount / $totalPersons) * 100) : 0 }}% of Total
                                        </span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-gender-female fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Passports Attached --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #f09819 0%, #edde5d 100%); color: #333;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase">Passports</span>
                                        <h3 class="fw-bold my-1 text-dark">{{ number_format($passportCount) }}</h3>
                                        <span class="badge bg-dark text-white px-2 py-0" style="font-size:11px;">Recorded</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-dark bg-opacity-10">
                                        <i class="mdi mdi-passport fs-24 text-dark"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CNICs Attached --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #4776e6 0%, #8e54e9 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">CNICs</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($cnicCount) }}</h3>
                                        <span class="badge bg-white text-primary px-2 py-0" style="font-size:11px;">Identified</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-card-account-details-outline fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Photos Attached / Timestamp --}}
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100 bg-light border">
                            <div class="card-body p-3 d-flex flex-column justify-content-center text-center">
                                <span class="text-muted small fw-semibold">Photos Uploaded</span>
                                <h4 class="fw-bold text-dark my-1">{{ number_format($photoCount) }} <span class="fs-12 text-muted fw-normal">/ {{ $totalPersons }}</span></h4>
                                <span class="text-success small fw-bold"><i class="mdi mdi-check-decagram me-1"></i>Active Records</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Card --}}
                <div class="card shadow-sm border-0 mb-4 no-print">
                    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="m-0 fw-bold text-dark">
                            <i class="mdi mdi-filter-variant me-1 text-primary"></i> Filter Person Criteria
                        </h6>
                        @if (!empty($search) || (!empty($gender) && $gender !== 'all') || (!empty($maktab) && $maktab !== 'all') || (!empty($packageId) && $packageId !== 'all'))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                <i class="mdi mdi-check me-1"></i>Filters Active
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('report.person') }}" id="personFilterForm">
                            <div class="row g-3 align-items-end">
                                {{-- Search Input --}}
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label small fw-bold mb-1">Search Pilgrim</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-magnify text-muted"></i></span>
                                        <input type="text" name="search" class="form-control form-control-sm"
                                               placeholder="Name, Passport #, CNIC, Phone, Hajj ID, HB #, Voucher..."
                                               value="{{ $search }}">
                                    </div>
                                </div>

                                {{-- Gender Filter --}}
                                <div class="col-lg-2 col-md-3 col-6">
                                    <label class="form-label small fw-bold mb-1">Gender</label>
                                    <select name="gender" class="form-select form-select-sm">
                                        <option value="all" {{ ($gender == 'all' || empty($gender)) ? 'selected' : '' }}>All Genders</option>
                                        <option value="male" {{ strtolower($gender) == 'male' ? 'selected' : '' }}>👨 Male</option>
                                        <option value="female" {{ strtolower($gender) == 'female' ? 'selected' : '' }}>👩 Female</option>
                                    </select>
                                </div>

                                {{-- Maktab Filter --}}
                                <div class="col-lg-3 col-md-3 col-6">
                                    <label class="form-label small fw-bold mb-1">Maktab / Camp</label>
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
                                <div class="col-lg-3 col-md-6">
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
                                <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                    <a href="{{ route('report.person') }}" class="btn btn-light btn-sm border px-3">
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
                                <p class="m-0 small text-muted">BOOKING PERSON REPORT & PILGRIM MANIFEST</p>
                            </div>
                        </div>
                        <div class="text-end" style="font-size: 11px;">
                            <div><strong>Printed On:</strong> {{ now()->format('d M Y, h:i A') }}</div>
                            <div><strong>Total Pilgrims:</strong> {{ $totalPersons }} ({{ $maleCount }} Male, {{ $femaleCount }} Female)</div>
                        </div>
                    </div>
                </div>

                {{-- Results Table Card --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="m-0 fw-bold text-dark">
                                Registered Pilgrims ({{ $persons->count() }})
                            </h6>
                            @if (!empty($search) || (!empty($gender) && $gender !== 'all') || (!empty($maktab) && $maktab !== 'all') || (!empty($packageId) && $packageId !== 'all'))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Filtered</span>
                            @endif
                        </div>
                        <div class="text-muted small">
                            Showing complete booking passenger database
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0" id="personManifestTable" style="font-size: 13px;">
                                <thead class="table-light text-uppercase text-dark fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">#</th>
                                        <th style="min-width: 230px;">Pilgrim Details</th>
                                        <th style="min-width: 170px;">Passport & CNIC</th>
                                        <th style="min-width: 150px;">Age & Contact</th>
                                        <th style="min-width: 160px;">Voucher & Booking</th>
                                        <th style="min-width: 170px;">Package & Maktab</th>
                                        <th class="text-center no-print" style="width: 90px;">Action</th>
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
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>

                                            {{-- Pilgrim Info (Photo, Full Name, Father Name, HB, Hajj ID, Gender) --}}
                                            <td>
                                                <div class="d-flex align-items-start gap-2">
                                                    {{-- Photo Thumbnail --}}
                                                    <div class="flex-shrink-0">
                                                        @if ($photoUrl)
                                                            <a href="{{ $photoUrl }}" target="_blank" title="View Full Photo">
                                                                <img src="{{ $photoUrl }}" alt=""
                                                                     class="rounded shadow-sm border"
                                                                     style="width: 42px; height: 42px; object-fit: cover; border-color: #c9a84c !important;"
                                                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                                                <div class="rounded d-none align-items-center justify-content-center fw-bold text-primary border"
                                                                     style="width: 42px; height: 42px; background: #eef2ff; font-size: 14px;">
                                                                    {{ strtoupper(substr($person->full_name ?: 'P', 0, 1)) }}
                                                                </div>
                                                            </a>
                                                        @else
                                                            <div class="rounded d-flex align-items-center justify-content-center fw-bold text-primary border"
                                                                 style="width: 42px; height: 42px; background: #eef2ff; font-size: 14px;">
                                                                {{ strtoupper(substr($person->full_name ?: 'P', 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Name & Badges --}}
                                                    <div class="flex-grow-1" style="line-height: 1.3;">
                                                        <strong class="text-dark d-block" style="font-size: 13.5px;">
                                                            {{ $person->full_name }}
                                                        </strong>
                                                        @if (!empty($person->father_name))
                                                            <small class="text-muted d-block" style="font-size: 11px;">
                                                                S/O, D/O: {{ $person->father_name }}
                                                            </small>
                                                        @endif
                                                        <div class="text-muted small mt-1 d-flex flex-wrap gap-1 align-items-center">
                                                            {{-- Gender Badge --}}
                                                            <span class="badge {{ $isFemale ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-1 py-0" style="font-size: 10px;">
                                                                <i class="mdi {{ $isFemale ? 'mdi-gender-female' : 'mdi-gender-male' }} me-0.5"></i>{{ ucfirst($person->gender ?: 'Male') }}
                                                            </span>

                                                            {{-- HB Number --}}
                                                            @if (!empty($person->hb_number))
                                                                <span class="badge bg-warning-subtle text-dark border border-warning px-1 py-0" style="font-size: 10px; font-weight: 600;">
                                                                    {{ $person->hb_number }}
                                                                </span>
                                                            @endif

                                                            {{-- Hajj ID --}}
                                                            @if (!empty($person->hajj_id))
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-1 py-0" style="font-size: 10px;">
                                                                    ID: {{ $person->hajj_id }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Passport & CNIC --}}
                                            <td>
                                                @if (!empty($person->passport_number) && $person->passport_number !== '-')
                                                    <div class="fw-bold text-dark fs-13">
                                                        <i class="mdi mdi-passport text-primary me-1"></i>{{ $person->passport_number }}
                                                    </div>
                                                    <div class="small text-muted mt-0.5">
                                                        @if ($passportExp)
                                                            <span>Exp: {{ $passportExp->format('d M Y') }}</span>
                                                            @if ($isExpiringSoon)
                                                                <span class="badge bg-danger text-white py-0 px-1" style="font-size: 9px;">Expiring Soon</span>
                                                            @endif
                                                        @elseif (!empty($person->date_of_issue))
                                                            <span>DOI: {{ \Carbon\Carbon::parse($person->date_of_issue)->format('d M Y') }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif

                                                @if (!empty($person->cnic))
                                                    <div class="small text-dark mt-1">
                                                        <i class="mdi mdi-card-account-details-outline text-muted me-1"></i>{{ $person->cnic }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Age & Contact --}}
                                            <td>
                                                @if (!empty($person->dob))
                                                    <div class="text-dark">
                                                        <i class="mdi mdi-cake-variant text-secondary me-1"></i>{{ \Carbon\Carbon::parse($person->dob)->format('d M Y') }}
                                                        @if ($age !== null)
                                                            <span class="badge bg-light text-muted border ms-1" style="font-size: 10px;">{{ $age }} yrs</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif

                                                @if (!empty($person->phone))
                                                    <div class="small text-dark mt-1">
                                                        <i class="mdi mdi-phone text-success me-1"></i>{{ $person->phone }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Voucher & Booking --}}
                                            <td>
                                                @if ($booking)
                                                    <div>
                                                        <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                                            <i class="mdi mdi-ticket-percent-outline me-1"></i>{{ $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)) }}
                                                        </a>
                                                    </div>
                                                    <div class="small text-muted mt-1">
                                                        {{ $booking->client ? $booking->client->name : ($booking->company ? $booking->company->company_name : 'Direct Booking') }}
                                                    </div>
                                                    <div class="small text-secondary">
                                                        Pax Group: <strong>{{ $booking->no_of_pax ?: $booking->persons->count() }}</strong>
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Package & Maktab --}}
                                            <td>
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

                                                @php
                                                    $campVal = $booking?->camp ?: ($booking?->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : null);
                                                @endphp
                                                @if($campVal)
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-0" style="font-size: 10.5px; font-weight: 600;">
                                                            <i class="mdi mdi-tent me-1 text-primary"></i>{{ $campVal }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Action --}}
                                            <td class="text-center no-print">
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
                                            <td colspan="7" class="text-center py-5">
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

    {{-- Custom CSS for Printing --}}
    <style>
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
