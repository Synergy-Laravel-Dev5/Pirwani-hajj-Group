@extends('layout.master')
@section('title', 'Maktab Report')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                {{-- Page Title & Actions --}}
                <div class="py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                    <div>
                        <h4 class="fs-20 fw-bold m-0 text-dark">
                            <i class="mdi mdi-tent text-primary me-2"></i>Maktab Report & Manifest
                        </h4>
                        <p class="text-muted mb-0 small">Maktab-wise rooming manifests, gender breakdown, pilgrim details, and stay schedules.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('report.maktab') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-refresh me-1"></i> Reset Filters
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Print Manifest
                        </button>
                        <a href="{{ route('report.maktab.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Export PDF
                        </a>
                        <a href="{{ route('report.maktab.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-file-excel me-1"></i> Export Excel
                        </a>
                    </div>
                </div>

                {{-- KPI Summary Cards --}}
                <div class="row g-3 mb-4 no-print">
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Total Stays</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalStays) }}</h3>
                                        <span class="badge bg-white text-primary px-2 py-0" style="font-size:11px;">{{ $totalRooms }} Rooms</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-domain fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Total Pilgrims</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalPilgrims) }}</h3>
                                        <span class="badge bg-white text-success px-2 py-0" style="font-size:11px;" title="Male / Female breakdown">
                                            {{ $totalMalePilgrims }} M / {{ $totalFemalePilgrims }} F
                                        </span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-account-group fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Today's Check-in</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($todayCheckins) }}</h3>
                                        <span class="badge bg-white text-danger px-2 py-0" style="font-size:11px;">{{ \Carbon\Carbon::today()->format('d M') }}</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-calendar-today fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #f09819 0%, #edde5d 100%); color: #333;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small fw-bold text-uppercase">Next 7 Days</span>
                                        <h3 class="fw-bold my-1 text-dark">{{ number_format($upcoming7Days) }}</h3>
                                        <span class="badge bg-dark text-white px-2 py-0" style="font-size:11px;">Upcoming</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-dark bg-opacity-10">
                                        <i class="mdi mdi-calendar-clock fs-24 text-dark"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #4776e6 0%, #8e54e9 100%); color: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-white-50 small fw-semibold text-uppercase">Active In-House</span>
                                        <h3 class="fw-bold my-1 text-white">{{ number_format($activeInHouse) }}</h3>
                                        <span class="badge bg-white text-primary px-2 py-0" style="font-size:11px;">Currently Staying</span>
                                    </div>
                                    <div class="rounded-circle p-2 bg-white bg-opacity-25">
                                        <i class="mdi mdi-bed-king fs-24 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="card shadow-sm border-0 h-100 bg-light border">
                            <div class="card-body p-3 d-flex flex-column justify-content-center text-center">
                                <span class="text-muted small fw-semibold">Report Timestamp</span>
                                <strong class="fs-13 text-dark mt-1">{{ now()->format('d M Y, h:i A') }}</strong>
                                <span class="text-success small fw-bold mt-1"><i class="mdi mdi-check-decagram me-1"></i>Live Synchronized</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Card (Maktab Wise Filter & Gender Filter ONLY) --}}
                <div class="card shadow-sm border-0 mb-4 no-print">
                    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="m-0 fw-bold text-dark">
                            <i class="mdi mdi-filter-variant me-1 text-primary"></i> Filter Maktab Report Criteria
                        </h6>
                        @if ((!empty($maktabFilter) && $maktabFilter !== 'all') || (!empty($genderFilter) && $genderFilter !== 'all'))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                <i class="mdi mdi-check me-1"></i>Filters Active
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('report.maktab') }}" id="maktabFilterForm">
                            <div class="row g-3 align-items-end">
                                {{-- 1. Maktab Filter (with All Maktabs Option) --}}
                                <div class="col-lg-5 col-md-5">
                                    <label class="form-label small fw-bold mb-1">
                                        <i class="mdi mdi-tent text-warning me-1"></i>Maktab / Camp Category
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-tent text-warning"></i></span>
                                        <select name="maktab" class="form-select form-select-sm">
                                            <option value="all" {{ ($maktabFilter == 'all' || empty($maktabFilter)) ? 'selected' : '' }}>All Maktabs / Camps</option>
                                            <option value="maktab_a" {{ $maktabFilter == 'maktab_a' ? 'selected' : '' }}>⛺ Maktab A (Zone 1 or 2 - VIP Close to Jamarat)</option>
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
                                </div>

                                {{-- 2. Gender Filter (Male / Female / All Option) --}}
                                <div class="col-lg-4 col-md-4">
                                    <label class="form-label small fw-bold mb-1">
                                        <i class="mdi mdi-gender-male-female text-primary me-1"></i>Gender
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-gender-male-female text-primary"></i></span>
                                        <select name="gender" class="form-select form-select-sm">
                                            <option value="all" {{ ($genderFilter == 'all' || empty($genderFilter)) ? 'selected' : '' }}>All Genders (Male & Female)</option>
                                            <option value="male" {{ strtolower($genderFilter) == 'male' ? 'selected' : '' }}>👨 Male</option>
                                            <option value="female" {{ strtolower($genderFilter) == 'female' ? 'selected' : '' }}>👩 Female</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Filter Action Buttons --}}
                                <div class="col-lg-3 col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-sm fw-semibold flex-grow-1 text-white shadow-sm" style="background-color: #071527; border-color: #071527;">
                                        <i class="mdi mdi-filter me-1"></i> Apply Filter
                                    </button>
                                    <a href="{{ route('report.maktab') }}" class="btn btn-light btn-sm border px-3 d-inline-flex align-items-center justify-content-center">
                                        Reset
                                    </a>
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
                                <p class="m-0 small text-muted">MAKTAB REPORT & ROOMING MANIFEST</p>
                            </div>
                        </div>
                        <div class="text-end" style="font-size: 11px;">
                            <div><strong>Printed On:</strong> {{ now()->format('d M Y, h:i A') }}</div>
                            <div><strong>Maktab Filter:</strong> {{ (!empty($maktabFilter) && $maktabFilter !== 'all') ? ucfirst(str_replace('_', ' ', $maktabFilter)) : 'All Maktabs' }}</div>
                            <div><strong>Gender Filter:</strong> {{ (!empty($genderFilter) && $genderFilter !== 'all') ? ucfirst($genderFilter) : 'All Genders' }}</div>
                            <div><strong>Total Records:</strong> {{ $totalStays }} Stays ({{ $totalPilgrims }} Pilgrims)</div>
                        </div>
                    </div>
                </div>

                {{-- Report Results Table Card --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="m-0 fw-bold text-dark">
                                Maktab Records ({{ $hotelStays->count() }})
                            </h6>
                            @if ((!empty($maktabFilter) && $maktabFilter !== 'all') || (!empty($genderFilter) && $genderFilter !== 'all'))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Filtered</span>
                            @endif
                        </div>
                        <div class="text-muted small">
                            Showing all matched hotel rooming allocations & Maktab details
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0" id="maktabManifestTable" style="font-size: 13px;">
                                <thead class="table-light text-uppercase text-dark fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">#</th>
                                        <th style="min-width: 240px;">Pilgrim / Hajji Details</th>
                                        <th style="min-width: 190px;">Hotel & Location</th>
                                        <th style="min-width: 140px;">Room Details</th>
                                        <th style="min-width: 180px;">Package & Voucher</th>
                                        <th class="text-center" style="min-width: 110px;">Status</th>
                                        <th class="text-center no-print" style="width: 90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $displayRowIdx = 0; @endphp
                                    @forelse ($hotelStays as $idx => $stay)
                                        @php
                                            $booking = $stay->booking;
                                            $rawPersons = $booking ? $booking->persons : collect([]);
                                            
                                            // If gender filter is active, strictly filter pilgrims of the chosen gender
                                            if (!empty($genderFilter) && $genderFilter !== 'all') {
                                                $gLower = strtolower(trim($genderFilter));
                                                $persons = $rawPersons->filter(function($p) use ($gLower) {
                                                    return strtolower(trim($p->gender ?? '')) === $gLower;
                                                })->values();

                                                if ($persons->isEmpty()) {
                                                    continue;
                                                }
                                                $paxCount = $persons->count();
                                            } else {
                                                $persons = $rawPersons;
                                                $paxCount = $booking ? ($booking->no_of_pax ?: $rawPersons->count()) : 1;
                                            }

                                            $mainPerson = $persons->first();
                                            $otherPersons = $persons->slice(1);
                                            $displayRowIdx++;
                                            
                                            $checkIn = $stay->check_in ? \Carbon\Carbon::parse($stay->check_in) : null;
                                            $checkOut = $stay->check_out ? \Carbon\Carbon::parse($stay->check_out) : null;
                                            $todayC = \Carbon\Carbon::today();

                                            // Determine Status
                                            $statusText = 'Scheduled';
                                            $statusBadge = 'bg-secondary';
                                            if ($checkIn) {
                                                if ($checkIn->isToday()) {
                                                    $statusText = 'Checking In Today';
                                                    $statusBadge = 'bg-danger text-white animate-pulse';
                                                } elseif ($checkIn->isTomorrow()) {
                                                    $statusText = 'Arriving Tomorrow';
                                                    $statusBadge = 'bg-warning text-dark';
                                                } elseif ($checkIn->isFuture()) {
                                                    $statusText = 'Upcoming (' . $checkIn->diffForHumans() . ')';
                                                    $statusBadge = 'bg-info text-white';
                                                } elseif ($checkOut && $checkOut->isPast()) {
                                                    $statusText = 'Checked Out';
                                                    $statusBadge = 'bg-light text-muted border';
                                                } elseif ($checkIn->isPast() && ($checkOut ? $checkOut->isFuture() || $checkOut->isToday() : true)) {
                                                    $statusText = 'In-House (Staying)';
                                                    $statusBadge = 'bg-success text-white';
                                                }
                                            }

                                            $locBadge = match(strtolower($stay->location ?? '')) {
                                                'makkah' => 'background:#e3f2fd; color:#0d47a1; border:1px solid #bbdefb;',
                                                'madinah' => 'background:#e8f5e9; color:#1b5e20; border:1px solid #c8e6c9;',
                                                'azizia' => 'background:#fff8e1; color:#f57f17; border:1px solid #ffe082;',
                                                'jeddah' => 'background:#e0f2f1; color:#004d40; border:1px solid #b2dfdb;',
                                                default => 'background:#f5f5f5; color:#424242; border:1px solid #e0e0e0;'
                                            };
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $displayRowIdx }}</td>

                                            {{-- Pilgrim / Hajji Details with Photo, Gender Badge & Accompanying Persons --}}
                                            <td>
                                                {{-- Main Pilgrim --}}
                                                <div class="d-flex align-items-start gap-2">
                                                    {{-- Photo Thumbnail --}}
                                                    <div class="flex-shrink-0">
                                                        @php
                                                            $photoPath = $mainPerson?->photo;
                                                            $photoUrl = null;
                                                            if (!empty($photoPath)) {
                                                                if (file_exists(public_path($photoPath))) {
                                                                    $photoUrl = asset($photoPath);
                                                                } else {
                                                                    $photoUrl = 'https://pirwanitravels.com.pk/' . ltrim($photoPath, '/');
                                                                }
                                                            }
                                                        @endphp
                                                        @if ($photoUrl)
                                                            <a href="{{ $photoUrl }}" target="_blank" title="View Full Photo">
                                                                <img src="{{ $photoUrl }}" alt="" 
                                                                     class="rounded shadow-sm border" 
                                                                     style="width: 42px; height: 42px; object-fit: cover; border-color: #c9a84c !important;"
                                                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                                                <div class="rounded d-none align-items-center justify-content-center fw-bold text-primary border" 
                                                                     style="width: 42px; height: 42px; background: #eef2ff; font-size: 14px;">
                                                                    {{ strtoupper(substr($mainPerson->full_name ?? ($booking->client->name ?? 'G'), 0, 1)) }}
                                                                </div>
                                                            </a>
                                                        @else
                                                            <div class="rounded d-flex align-items-center justify-content-center fw-bold text-primary border" 
                                                                 style="width: 42px; height: 42px; background: #eef2ff; font-size: 14px;">
                                                                {{ strtoupper(substr($mainPerson->full_name ?? ($booking->client->name ?? 'G'), 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Pilgrim Info --}}
                                                    <div class="flex-grow-1" style="line-height: 1.3;">
                                                        <strong class="text-dark d-block" style="font-size: 13.5px;">
                                                            {{ $mainPerson->full_name ?? ($booking?->client?->name ?? ($booking?->company?->company_name ?? 'N/A')) }}
                                                        </strong>
                                                        <div class="text-muted small mt-1 d-flex flex-wrap gap-1 align-items-center">
                                                            {{-- Gender Badge for Main Pax --}}
                                                            @if (!empty($mainPerson?->gender))
                                                                @php
                                                                    $isFemale = strtolower($mainPerson->gender) === 'female';
                                                                @endphp
                                                                <span class="badge {{ $isFemale ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-1 py-0" style="font-size: 10px;">
                                                                    <i class="mdi {{ $isFemale ? 'mdi-gender-female' : 'mdi-gender-male' }} me-0.5"></i>{{ ucfirst($mainPerson->gender) }}
                                                                </span>
                                                            @endif

                                                            @if ($mainPerson && $mainPerson->passport_number)
                                                                <span class="badge bg-light text-dark border px-1 py-0">
                                                                    <i class="mdi mdi-passport me-1"></i>{{ $mainPerson->passport_number }}
                                                                </span>
                                                            @elseif ($booking && $booking->passport_number)
                                                                <span class="badge bg-light text-dark border px-1 py-0">
                                                                    <i class="mdi mdi-passport me-1"></i>{{ $booking->passport_number }}
                                                                </span>
                                                            @endif

                                                            @if (!empty($mainPerson->hajj_id))
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-1 py-0">
                                                                    ID: {{ $mainPerson->hajj_id }}
                                                                </span>
                                                            @endif

                                                            @if ($paxCount > 1)
                                                                <span class="badge bg-success-subtle text-success px-1 py-0" title="Total Pilgrims in Booking">
                                                                    +{{ $paxCount - 1 }} more (Total {{ $paxCount }})
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Other Pilgrims in Booking --}}
                                                @if ($otherPersons->count() > 0)
                                                    <div class="mt-2 pt-2 border-top border-light-subtle d-flex flex-column gap-2">
                                                        @foreach ($otherPersons as $oIdx => $otherP)
                                                            @php
                                                                $oPhotoPath = $otherP->photo;
                                                                $oPhotoUrl = null;
                                                                if (!empty($oPhotoPath)) {
                                                                    if (file_exists(public_path($oPhotoPath))) {
                                                                        $oPhotoUrl = asset($oPhotoPath);
                                                                    } else {
                                                                        $oPhotoUrl = 'https://pirwanitravels.com.pk/' . ltrim($oPhotoPath, '/');
                                                                    }
                                                                }
                                                            @endphp
                                                            <div class="d-flex align-items-start gap-2">
                                                                {{-- Other Pax Photo --}}
                                                                <div class="flex-shrink-0">
                                                                    @if ($oPhotoUrl)
                                                                        <a href="{{ $oPhotoUrl }}" target="_blank" title="View Full Photo">
                                                                            <img src="{{ $oPhotoUrl }}" alt="" 
                                                                                 class="rounded shadow-sm border" 
                                                                                 style="width: 36px; height: 36px; object-fit: cover; border-color: #dee2e6 !important;"
                                                                                 onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                                                            <div class="rounded d-none align-items-center justify-content-center fw-bold text-secondary border" 
                                                                                 style="width: 36px; height: 36px; background: #f8fafc; font-size: 13px;">
                                                                                {{ strtoupper(substr($otherP->full_name ?: 'P', 0, 1)) }}
                                                                            </div>
                                                                        </a>
                                                                    @else
                                                                        <div class="rounded d-flex align-items-center justify-content-center fw-bold text-secondary border" 
                                                                             style="width: 36px; height: 36px; background: #f8fafc; font-size: 13px;">
                                                                            {{ strtoupper(substr($otherP->full_name ?: 'P', 0, 1)) }}
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                {{-- Other Pax Info --}}
                                                                <div class="flex-grow-1" style="line-height: 1.3;">
                                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                                        <strong class="text-dark" style="font-size: 12.5px;">
                                                                            {{ $otherP->full_name }}
                                                                        </strong>
                                                                    </div>
                                                                    <div class="text-muted small mt-1 d-flex flex-wrap gap-1 align-items-center">
                                                                        {{-- Gender Badge for Other Pax --}}
                                                                        @if (!empty($otherP->gender))
                                                                            @php
                                                                                $oIsFemale = strtolower($otherP->gender) === 'female';
                                                                            @endphp
                                                                            <span class="badge {{ $oIsFemale ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-1 py-0" style="font-size: 10px;">
                                                                                <i class="mdi {{ $oIsFemale ? 'mdi-gender-female' : 'mdi-gender-male' }} me-0.5"></i>{{ ucfirst($otherP->gender) }}
                                                                            </span>
                                                                        @endif

                                                                        @if ($otherP->passport_number)
                                                                            <span class="badge bg-light text-dark border px-1 py-0">
                                                                                <i class="mdi mdi-passport me-1"></i>{{ $otherP->passport_number }}
                                                                            </span>
                                                                        @endif

                                                                        @if (!empty($otherP->hajj_id))
                                                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-1 py-0">
                                                                                ID: {{ $otherP->hajj_id }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Hotel & Location --}}
                                            <td>
                                                <div class="fw-bold text-dark fs-13">
                                                    {{ strtoupper($stay->hotel_name ?: 'Hotel Not Specified') }}
                                                </div>
                                                <div class="mt-1">
                                                    <span class="badge px-2 py-1 rounded-pill" style="{{ $locBadge }}; font-size:11px; font-weight:600;">
                                                        <i class="mdi mdi-map-marker me-1"></i>{{ strtoupper($stay->location ?: 'Location') }}
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- Room Details --}}
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    <span class="badge bg-dark text-white px-2 py-1" style="font-size:11px;">
                                                        {{ ucfirst($stay->room_type ?: 'Standard') }} Room
                                                    </span>
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    <strong>{{ $stay->no_of_rooms ?: 1 }}</strong> Room(s)
                                                    @if(!empty($stay->gender) && $stay->gender !== 'Any')
                                                        <span class="badge bg-light text-secondary border ms-1" style="font-size: 10px;">{{ $stay->gender }}</span>
                                                    @endif
                                                </div>
                                            </td>



                                            {{-- Package & Voucher Ref --}}
                                            <td>
                                                <div class="fw-semibold text-primary">
                                                    @if ($booking && $booking->package)
                                                        @if ($booking->package->package_code)
                                                            <span class="badge bg-dark text-white me-1" style="font-size:10px;">{{ $booking->package->package_code }}</span>
                                                        @endif
                                                        {{ $booking->package->package_title ?: ($booking->package->name ?: 'Package') }}
                                                    @elseif ($booking)
                                                        {{ strtoupper($booking->package_type ?? 'Hajj / Umrah') }}
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <span class="fw-bold text-dark">Voucher:</span> 
                                                    @if ($booking)
                                                        <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="text-decoration-none fw-semibold">
                                                            {{ $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)) }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                                @if ($booking && $booking->client)
                                                    <div class="text-muted" style="font-size: 11px;">
                                                        Client: {{ $booking->client->name }}
                                                    </div>
                                                @endif

                                                @php
                                                    $campVal = $booking?->camp ?: ($booking?->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : null);
                                                @endphp
                                                @if($campVal)
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1 shadow-sm" style="font-size: 11px; font-weight: 700;">
                                                            <i class="mdi mdi-tent me-1 text-primary"></i>{{ $campVal }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Status --}}
                                            <td class="text-center">
                                                <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">
                                                    {{ $statusText }}
                                                </span>
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
                                            <td colspan="10" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="mdi mdi-tent text-muted" style="font-size: 48px;"></i>
                                                    <h5 class="fw-bold text-muted mt-2">No Maktab Records Found</h5>
                                                    <p class="text-muted small mb-3">No bookings match the selected Maktab or Gender filter criteria.</p>
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

    {{-- Custom CSS for Printing and Animations --}}
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
