@extends('layout.master')
@section('title', 'Hotel & Location Person Occupancy Report')

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
    .hotel-divider-row td {
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

    .occupancy-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
    }
    .occupancy-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }

    /* Custom Checkbox Multi-Select Dropdown */
    .custom-multiselect-wrapper {
        position: relative;
    }
    .multiselect-trigger-box {
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
        min-height: 38px !important;
        padding: 4px 8px !important;
        cursor: pointer !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        user-select: none;
    }
    .multiselect-trigger-box:focus,
    .multiselect-trigger-box.active {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        outline: 0;
    }
    .multiselect-dropdown-menu {
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #cbd5e1 !important;
        background: #ffffff !important;
        z-index: 1060 !important;
    }
    .multiselect-option-row {
        cursor: pointer;
        padding: 6px 10px;
        transition: background-color 0.12s ease;
        border-radius: 4px;
        margin: 1px 0;
    }
    .multiselect-option-row:hover {
        background-color: #f1f5f9;
    }
    .multiselect-option-row.checked-row {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        font-weight: 600;
    }
    .multiselect-tag-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background-color: #e0e7ff;
        border: 1px solid #c7d2fe;
        color: #3730a3;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.2;
    }
    .multiselect-tag-pill .remove-tag {
        cursor: pointer;
        font-weight: bold;
        color: #dc2626;
        margin-left: 2px;
    }
    .multiselect-tag-pill .remove-tag:hover {
        color: #991b1b;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            {{-- Page Header & Actions --}}
            <div class="py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                <div>
                    <h4 class="fs-20 fw-bold m-0 text-dark">
                        <i class="mdi mdi-office-building text-primary me-2"></i>Hotel & Location Person Occupancy Report
                    </h4>
                    <p class="text-muted mb-0 small">Pilgrim counts and passenger distribution per hotel and city (Anjum, Azizia, Madinah, Makkah, Mina).</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('report.hotel-person.export-excel', request()->query()) }}" 
                       class="btn btn-success btn-sm btn-export-excel" id="btnExportExcel"
                       title="Download hotel person report as Excel spreadsheet">
                        <i class="mdi mdi-file-excel me-1"></i> Download Excel
                    </a>
                    <a href="{{ route('report.hotel-person.export-pdf', request()->query()) }}" 
                       class="btn btn-danger btn-sm btn-export-pdf" id="btnExportPdf"
                       title="Download hotel person report as PDF document">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-dark btn-sm">
                        <i class="mdi mdi-printer me-1"></i> Print Manifest
                    </button>
                    <a href="{{ route('report.hotel-person') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </div>

            {{-- Top KPI Summary Ribbon (Filtered Data) --}}
            <div class="row g-2 mb-3 no-print">
                <div class="col-xl-3 col-md-6 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Filtered Pilgrims:</span>
                            <strong class="fs-20 text-primary">{{ number_format($totalPersons) }}</strong>
                            <small class="text-muted ms-1">Persons</small>
                        </div>
                        <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary">
                            <i class="mdi mdi-account-group fs-22"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Total Hotel Stays:</span>
                            <strong class="fs-20 text-dark">{{ number_format($totalStays) }}</strong>
                            <small class="text-muted ms-1">Stays</small>
                        </div>
                        <span class="badge bg-dark text-white" style="font-size: 11px;">
                            {{ $totalRooms }} Rooms
                        </span>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Active Hotel Filter:</span>
                            @php
                                $hCount = count((array)$hotelFilter);
                                $hDisplay = $hCount === 0 ? 'All Hotels' : ($hCount === 1 ? (is_array($hotelFilter) ? reset($hotelFilter) : $hotelFilter) : $hCount . ' Hotels Selected');
                            @endphp
                            <strong class="fs-14 text-dark text-truncate d-block" style="max-width: 170px;" title="{{ is_array($hotelFilter) ? implode(', ', $hotelFilter) : ($hotelFilter ?: 'All Hotels') }}">
                                {{ $hDisplay }}
                            </strong>
                        </div>
                        <span class="badge {{ !empty($hotelFilter) && $hotelFilter !== 'all' ? 'bg-primary' : 'bg-secondary' }} text-white">
                            {{ !empty($hotelFilter) && $hotelFilter !== 'all' ? 'Selected' : 'All' }}
                        </span>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Active Location:</span>
                            <strong class="fs-15 text-success text-uppercase">
                                {{ (!empty($locationFilter) && $locationFilter !== 'all') ? $locationFilter : 'All Locations' }}
                            </strong>
                        </div>
                        <span class="text-success small fw-bold"><i class="mdi mdi-check-decagram"></i> Live Sync</span>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- HOTEL & LOCATION PERSON SUMMARY (Quick Cards answering Anjum / Azizia counts) --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            <div class="card shadow-sm border-0 mb-3 no-print">
                <div class="card-header bg-white py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="m-0 fw-bold text-dark fs-14">
                            <i class="mdi mdi-chart-box-outline me-1 text-primary"></i> Person Counts by Location & Hotel (Click any card to filter)
                        </h6>
                    </div>
                    <span class="text-muted small">Total system occupancy across all active bookings</span>
                </div>
                <div class="card-body p-3">
                    {{-- Location Cards --}}
                    <div class="row g-2 mb-3">
                        @php
                            $locMeta = [
                                'makkah'  => ['label' => 'Makkah', 'icon' => '🕋', 'border' => 'border-success', 'bg' => 'bg-success-subtle', 'text' => 'text-success'],
                                'azizia'  => ['label' => 'Azizia', 'icon' => '🏢', 'border' => 'border-warning', 'bg' => 'bg-warning-subtle', 'text' => 'text-warning-emphasis'],
                                'madinah' => ['label' => 'Madinah', 'icon' => '🕌', 'border' => 'border-info', 'bg' => 'bg-info-subtle', 'text' => 'text-info-emphasis'],
                                'mina'    => ['label' => 'Mina / Camp', 'icon' => '⛺', 'border' => 'border-primary', 'bg' => 'bg-primary-subtle', 'text' => 'text-primary'],
                                'other'   => ['label' => 'Other / Jeddah', 'icon' => '🌊', 'border' => 'border-secondary', 'bg' => 'bg-secondary-subtle', 'text' => 'text-secondary'],
                            ];
                        @endphp

                        @foreach ($locMeta as $lKey => $meta)
                            @php
                                $locStat = $locationBreakdown[$lKey] ?? ['persons' => 0, 'rooms' => 0, 'stays' => 0];
                                $isActive = ($locationFilter === $lKey);
                            @endphp
                            <div class="col-xl col-md-4 col-sm-6">
                                <a href="{{ route('report.hotel-person', array_merge(request()->query(), ['location' => ($isActive ? 'all' : $lKey)])) }}" 
                                   class="text-decoration-none d-block">
                                    <div class="p-2.5 rounded border occupancy-card {{ $isActive ? 'border-2 border-dark shadow' : 'border-light shadow-sm' }} {{ $meta['bg'] }} h-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold fs-13 text-dark">
                                                <span class="me-1">{{ $meta['icon'] }}</span> {{ $meta['label'] }}
                                            </span>
                                            @if($isActive)
                                                <span class="badge bg-dark text-white px-1 py-0" style="font-size: 9.5px;">ACTIVE</span>
                                            @endif
                                        </div>
                                        <div class="mt-2 d-flex align-items-baseline justify-content-between">
                                            <span class="fs-19 fw-bolder {{ $meta['text'] }}">{{ number_format($locStat['persons']) }}</span>
                                            <span class="small text-muted fw-semibold">{{ $locStat['rooms'] }} Rooms ({{ $locStat['stays'] }} Stays)</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Popular Hotels Quick Breakdown Chips --}}
                    <div>
                        <div class="small fw-bold text-muted text-uppercase mb-1.5" style="letter-spacing: 0.5px; font-size: 11px;">
                            <i class="mdi mdi-domain me-1"></i>Hotel Wise Person Breakdown:
                        </div>
                        <div class="d-flex flex-wrap gap-1.5 align-items-center">
                            @foreach (array_slice($hotelBreakdown, 0, 10, true) as $hName => $hStat)
                                @php
                                    $isHActive = ($hotelFilter === $hName);
                                @endphp
                                <a href="{{ route('report.hotel-person', array_merge(request()->query(), ['hotel_name' => ($isHActive ? '' : $hName)])) }}" 
                                   class="btn btn-xs rounded-pill {{ $isHActive ? 'btn-dark' : 'btn-outline-secondary bg-white' }} px-2.5 py-1 text-decoration-none shadow-sm"
                                   style="font-size: 12px; font-weight: 600;">
                                    <span>{{ $hName }}</span>
                                    <span class="badge {{ $isHActive ? 'bg-white text-dark' : 'bg-primary text-white' }} ms-1 px-1.5 py-0.5" style="font-size: 11px;">
                                        {{ number_format($hStat['persons']) }} Pax
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Criteria Card --}}
            <div class="card shadow-sm border-0 mb-3 no-print">
                <div class="card-header bg-white py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="m-0 fw-bold text-dark fs-14">
                            <i class="mdi mdi-filter-variant me-1 text-primary"></i> Filter Hotel & Person Criteria
                        </h6>
                        @if (!empty($hotelFilter) || (!empty($locationFilter) && $locationFilter !== 'all') || !empty($search) || !empty($packageFilter) || !empty($fromDate) || !empty($toDate))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Filtered</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.hotel-person') }}" id="hotelPersonFilterForm">
                        <div class="row g-2 align-items-end">
                            {{-- Hotel Name (Multi-Select) --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <div class="custom-multiselect-wrapper position-relative" id="hotelMultiSelectWrapper">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small fw-semibold text-muted mb-0">Hotel Name (Multi-Select)</label>
                                        <span class="badge bg-primary-subtle text-primary multiselect-count" id="hotelCountBadge" style="font-size: 10px;">
                                            {{ count((array)$hotelFilter) > 0 ? count((array)$hotelFilter) . ' selected' : 'All Hotels' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Trigger Box -->
                                    <div class="multiselect-trigger-box form-control form-control-sm d-flex justify-content-between align-items-center cursor-pointer" 
                                         id="hotelTriggerBtn" tabindex="0">
                                        <div class="multiselect-tags-container d-flex flex-wrap gap-1 align-items-center flex-grow-1" id="hotelTagsContainer" style="max-height: 58px; overflow-y: auto;">
                                            @php $hSelectedCount = count((array)$hotelFilter); @endphp
                                            @if($hSelectedCount === 0)
                                                <span class="text-muted small placeholder-text">Choose Hotel(s)...</span>
                                            @elseif($hSelectedCount <= 2)
                                                @foreach((array)$hotelFilter as $selH)
                                                    @if(!empty($selH))
                                                        <span class="multiselect-tag-pill">
                                                            <span class="text-truncate" style="max-width: 120px;" title="{{ $selH }}">{{ $selH }}</span>
                                                            <span class="remove-tag" data-target="hotel" data-val="{{ $selH }}">&times;</span>
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="badge bg-primary text-white px-2 py-1" style="font-size: 11px;">
                                                    <i class="mdi mdi-city me-1"></i>{{ $hSelectedCount }} Hotels Selected
                                                </span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-1 ms-1 text-muted">
                                            @if($hSelectedCount > 0)
                                                <span class="multiselect-quick-clear cursor-pointer text-secondary px-1 fw-bold" data-target="hotel" title="Clear all" style="font-size: 14px;">&times;</span>
                                            @endif
                                            <i class="mdi mdi-chevron-down fs-14"></i>
                                        </div>
                                    </div>

                                    <!-- Dropdown Menu -->
                                    <div class="multiselect-dropdown-menu card shadow border position-absolute w-100 mt-1 d-none" 
                                         id="hotelDropdownMenu">
                                        <div class="p-2 border-bottom bg-light">
                                            <div class="input-group input-group-sm mb-1">
                                                <span class="input-group-text bg-white border-end-0 py-0"><i class="mdi mdi-magnify text-muted"></i></span>
                                                <input type="text" class="form-control form-control-sm border-start-0 multiselect-search py-1" data-target="hotel" placeholder="Search hotel name...">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center pt-1">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary py-0 px-2 btn-multiselect-all" data-target="hotel" style="font-size: 11px;">Select All</button>
                                                    <button type="button" class="btn btn-outline-secondary py-0 px-2 btn-multiselect-none" data-target="hotel" style="font-size: 11px;">Clear</button>
                                                </div>
                                                <small class="text-muted multiselect-status-label" id="hotelStatusLabel" style="font-size: 11px;">{{ $hSelectedCount }} selected</small>
                                            </div>
                                        </div>
                                        <div class="overflow-auto p-1 multiselect-options-container" id="hotelOptionsContainer" style="max-height: 220px;">
                                            @foreach($allHotelOptions as $optHotel)
                                                @php $isHChecked = in_array($optHotel, (array)$hotelFilter); @endphp
                                                <label class="dropdown-item d-flex align-items-center gap-2 py-1.5 px-2 rounded multiselect-option-row {{ $isHChecked ? 'checked-row' : '' }}" style="font-size: 12px;">
                                                    <input type="checkbox" name="hotel_name[]" value="{{ $optHotel }}" class="form-check-input mt-0 multiselect-checkbox hotel-cb" {{ $isHChecked ? 'checked' : '' }}>
                                                    <span class="option-text text-truncate">{{ $optHotel }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- City / Location --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">City / Location</label>
                                <select name="location" class="form-select form-select-sm" id="hotelPersonLocationSelect">
                                    <option value="all" {{ ($locationFilter == 'all' || empty($locationFilter)) ? 'selected' : '' }}>All Locations</option>
                                    <option value="makkah" {{ $locationFilter == 'makkah' ? 'selected' : '' }}>🕋 Makkah</option>
                                    <option value="madinah" {{ $locationFilter == 'madinah' ? 'selected' : '' }}>🕌 Madinah</option>
                                    <option value="azizia" {{ ($locationFilter == 'azizia' && ($aziziaPhase ?? 'both') == 'both') ? 'selected' : '' }}>🏢 Azizia (Both Check-Ins)</option>
                                    <option value="azizia_first" {{ ($locationFilter == 'azizia' && ($aziziaPhase ?? '') == 'first') ? 'selected' : '' }}>🏢 Azizia - 1st Check-In (Pre-Hajj)</option>
                                    <option value="azizia_second" {{ ($locationFilter == 'azizia' && ($aziziaPhase ?? '') == 'second') ? 'selected' : '' }}>🏢 Azizia - 2nd Check-In (Post-Hajj)</option>
                                    <option value="mina" {{ $locationFilter == 'mina' ? 'selected' : '' }}>⛺ Mina</option>
                                    <option value="jeddah" {{ $locationFilter == 'jeddah' ? 'selected' : '' }}>🌊 Jeddah</option>
                                    <option value="other" {{ $locationFilter == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <input type="hidden" name="azizia_phase" value="{{ $aziziaPhase ?? 'both' }}">

                            {{-- Stay Date From --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Stay Date From</label>
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                            </div>

                            {{-- Stay Date To --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Stay Date To</label>
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                            </div>

                            {{-- Package (Multi-Select) --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <div class="custom-multiselect-wrapper position-relative" id="packageMultiSelectWrapper">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small fw-semibold text-muted mb-0">Package (Multi-Select)</label>
                                        <span class="badge bg-primary-subtle text-primary multiselect-count" id="packageCountBadge" style="font-size: 10px;">
                                            {{ count((array)$packageFilter) > 0 ? count((array)$packageFilter) . ' selected' : 'All Packages' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Trigger Box -->
                                    <div class="multiselect-trigger-box form-control form-control-sm d-flex justify-content-between align-items-center cursor-pointer" 
                                         id="packageTriggerBtn" tabindex="0">
                                        <div class="multiselect-tags-container d-flex flex-wrap gap-1 align-items-center flex-grow-1" id="packageTagsContainer" style="max-height: 58px; overflow-y: auto;">
                                            @php $pSelectedCount = count((array)$packageFilter); @endphp
                                            @if($pSelectedCount === 0)
                                                <span class="text-muted small placeholder-text">-- All Packages --</span>
                                            @elseif($pSelectedCount <= 2)
                                                @foreach($packages as $pkg)
                                                    @if(in_array((string)$pkg->id, (array)$packageFilter) || in_array($pkg->id, (array)$packageFilter))
                                                        @php
                                                            $pkgCode = $pkg->code ?? $pkg->package_number ?? '';
                                                            $pkgTitle = $pkg->package_title ?: ($pkg->name ?: 'Package #' . $pkg->id);
                                                            $shortTitle = $pkgCode ? $pkgTitle . ' (' . $pkgCode . ')' : $pkgTitle;
                                                        @endphp
                                                        <span class="multiselect-tag-pill">
                                                            <span class="text-truncate" style="max-width: 120px;">{{ $shortTitle }}</span>
                                                            <span class="remove-tag" data-target="package" data-val="{{ $pkg->id }}">&times;</span>
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="badge bg-primary text-white px-2 py-1" style="font-size: 11px;">
                                                    <i class="mdi mdi-package-variant-closed me-1"></i>{{ $pSelectedCount }} Packages Selected
                                                </span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-1 ms-1 text-muted">
                                            @if($pSelectedCount > 0)
                                                <span class="multiselect-quick-clear cursor-pointer text-secondary px-1 fw-bold" data-target="package" title="Clear all" style="font-size: 14px;">&times;</span>
                                            @endif
                                            <i class="mdi mdi-chevron-down fs-14"></i>
                                        </div>
                                    </div>

                                    <!-- Dropdown Menu -->
                                    <div class="multiselect-dropdown-menu card shadow border position-absolute w-100 mt-1 d-none" 
                                         id="packageDropdownMenu">
                                        <div class="p-2 border-bottom bg-light">
                                            <div class="input-group input-group-sm mb-1">
                                                <span class="input-group-text bg-white border-end-0 py-0"><i class="mdi mdi-magnify text-muted"></i></span>
                                                <input type="text" class="form-control form-control-sm border-start-0 multiselect-search py-1" data-target="package" placeholder="Search package name or code...">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center pt-1">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary py-0 px-2 btn-multiselect-all" data-target="package" style="font-size: 11px;">Select All</button>
                                                    <button type="button" class="btn btn-outline-secondary py-0 px-2 btn-multiselect-none" data-target="package" style="font-size: 11px;">Clear</button>
                                                </div>
                                                <small class="text-muted multiselect-status-label" id="packageStatusLabel" style="font-size: 11px;">{{ $pSelectedCount }} selected</small>
                                            </div>
                                        </div>
                                        <div class="overflow-auto p-1 multiselect-options-container" id="packageOptionsContainer" style="max-height: 220px;">
                                            @foreach($packages as $pkg)
                                                @php
                                                    $pkgCode = $pkg->code ?? $pkg->package_number ?? '';
                                                    $pkgTitle = $pkg->package_title ?: ($pkg->name ?: 'Package #' . $pkg->id);
                                                    $isPChecked = in_array((string)$pkg->id, (array)$packageFilter) || in_array($pkg->id, (array)$packageFilter);
                                                @endphp
                                                <label class="dropdown-item d-flex align-items-center gap-2 py-1.5 px-2 rounded multiselect-option-row {{ $isPChecked ? 'checked-row' : '' }}" style="font-size: 12px;">
                                                    <input type="checkbox" name="package_id[]" value="{{ $pkg->id }}" class="form-check-input mt-0 multiselect-checkbox package-cb" {{ $isPChecked ? 'checked' : '' }}>
                                                    <span class="option-text text-truncate">
                                                        @if($pkgCode)
                                                            <span class="badge bg-dark text-white me-1 px-1 py-0" style="font-size: 10px;">{{ $pkgCode }}</span>
                                                        @endif
                                                        {{ $pkgTitle }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Search keyword --}}
                            <div class="col-xl-8 col-md-8 col-sm-12">
                                <label class="form-label small fw-semibold text-muted mb-1">Search Pilgrim / Passport / CNIC / Hajj ID / HB / Voucher</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-magnify text-muted"></i></span>
                                    <input type="text" name="search" class="form-control form-control-sm" 
                                           placeholder="Search Pilgrim Name, Passport #, CNIC, Phone, Haji ID (PW27...), HB #, Voucher (UB...)..." 
                                           value="{{ $search }}">
                                </div>
                            </div>

                            {{-- Filter Action Buttons --}}
                            <div class="col-xl-4 col-md-4 col-sm-12 d-flex gap-2">
                                <button type="submit" class="btn btn-sm fw-semibold flex-grow-1 text-white shadow-sm" style="background-color: #071527; border-color: #071527;">
                                    <i class="mdi mdi-filter me-1"></i> Apply Filter
                                </button>
                                <a href="{{ route('report.hotel-person') }}" class="btn btn-outline-secondary btn-sm px-3">
                                    Reset
                                </a>
                            </div>

                            @if($locationFilter === 'azizia')
                                <div class="col-12 mt-2">
                                    <div class="p-2 rounded bg-warning-subtle border border-warning d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 fs-12"><i class="mdi mdi-office-building me-1"></i>Azizia Check-In Filter</span>
                                            <span class="small text-dark fw-semibold">Haji Azizia mein 2 dafa stay karte hain (Pre-Mina &amp; Post-Mina). Konsi check-in dekhni hai:</span>
                                        </div>
                                        <div class="btn-group btn-group-sm shadow-sm">
                                            <a href="{{ request()->fullUrlWithQuery(['location' => 'azizia', 'azizia_phase' => 'both']) }}" class="btn btn-sm {{ ($aziziaPhase ?? 'both') === 'both' ? 'btn-dark text-white fw-bold' : 'btn-outline-dark bg-white' }}">
                                                <i class="mdi mdi-check-all me-1"></i>Dono Check-Ins (Both)
                                            </a>
                                            <a href="{{ request()->fullUrlWithQuery(['location' => 'azizia', 'azizia_phase' => 'first']) }}" class="btn btn-sm {{ ($aziziaPhase ?? '') === 'first' ? 'btn-dark text-white fw-bold' : 'btn-outline-dark bg-white' }}">
                                                <i class="mdi mdi-numeric-1-circle me-1"></i>1st Check-In (Pre-Hajj)
                                            </a>
                                            <a href="{{ request()->fullUrlWithQuery(['location' => 'azizia', 'azizia_phase' => 'second']) }}" class="btn btn-sm {{ ($aziziaPhase ?? '') === 'second' ? 'btn-dark text-white fw-bold' : 'btn-outline-dark bg-white' }}">
                                                <i class="mdi mdi-numeric-2-circle me-1"></i>2nd Check-In (Post-Hajj)
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                        <span class="badge bg-primary text-white">{{ $totalPersons }} Pilgrims Listed</span>
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
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="hotel" checked>
                                    <span>Hotel & Location</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="room" checked>
                                    <span>Room Details</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="room-no" checked>
                                    <span>Room No</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="checkin" checked>
                                    <span>Check-In Date</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="checkout" checked>
                                    <span>Check-Out Date</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="nights" checked>
                                    <span>Nights</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="maktab">
                                    <span>Maktab / Camp</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="package" checked>
                                    <span>Package</span>
                                </label>
                                <label class="column-filter-item">
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="voucher" checked>
                                    <span>Voucher #</span>
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

                {{-- Printable Header (Visible only in Print) --}}
                <div class="print-only mb-3 p-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Logo" style="height: 60px; object-fit: contain;">
                            <div>
                                <h3 class="m-0 fw-bold" style="color:#000; letter-spacing:0.5px;">PIRWANI HAJJ GROUP</h3>
                                <p class="m-0 small text-muted">OFFICIAL HOTEL & LOCATION PERSON OCCUPANCY REPORT</p>
                            </div>
                        </div>
                        <div class="text-end" style="font-size: 11px;">
                            <div><strong>Printed On:</strong> {{ now()->format('d M Y, h:i A') }}</div>
                            <div><strong>Hotel:</strong> {{ $hotelFilter ?: 'ALL HOTELS' }} | <strong>Location:</strong> {{ strtoupper($locationFilter ?: 'ALL') }}</div>
                            <div><strong>Total Pilgrims:</strong> {{ number_format($totalPersons) }} Persons ({{ number_format($totalStays) }} Stays)</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="manifest-table" id="hotelPersonManifestTable">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="col-sr">SR</th>
                                    <th style="width: 60px;" class="col-picture">PICTURE</th>
                                    <th style="width: 95px;" class="col-hajj-id">HAJJ ID</th>
                                    <th style="width: 85px;" class="col-hb">HB #</th>
                                    <th style="min-width: 175px;" class="col-pilgrim">PILGRIM FULL NAME</th>
                                    <th style="min-width: 120px;" class="col-given-name col-hidden">GIVEN NAME</th>
                                    <th style="min-width: 120px;" class="col-surname col-hidden">SURNAME</th>
                                    <th style="min-width: 150px;" class="col-father-name col-hidden">HUSBAND / FATHER NAME</th>
                                    <th style="width: 75px;" class="col-gender">GENDER</th>
                                    <th style="width: 110px;" class="col-passport">PASSPORT NO</th>
                                    <th style="width: 95px;" class="col-passport-doi col-hidden">DOI</th>
                                    <th style="width: 95px;" class="col-passport-exp col-hidden">EXPIRY</th>
                                    <th style="min-width: 160px;" class="col-hotel">HOTEL & LOCATION</th>
                                    <th style="width: 90px;" class="col-room">ROOM TYPE</th>
                                    <th style="width: 85px;" class="col-room-no">ROOM NO</th>
                                    <th style="width: 105px;" class="col-checkin">CHECK-IN</th>
                                    <th style="width: 105px;" class="col-checkout">CHECK-OUT</th>
                                    <th style="width: 65px;" class="col-nights">NIGHTS</th>
                                    <th style="width: 105px;" class="col-maktab col-hidden">MAKTAB</th>
                                    <th style="min-width: 130px;" class="col-package">PACKAGE</th>
                                    <th style="width: 105px;" class="col-voucher">VOUCHER #</th>
                                    <th style="min-width: 120px;" class="col-client col-hidden">CLIENT / GROUP</th>
                                    <th style="width: 100px;" class="col-status">STATUS</th>
                                    <th style="width: 80px;" class="col-action no-print">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $globalSr = 1; @endphp
                                @forelse ($hotelStays as $stay)
                                    @php
                                        $booking = $stay->booking;
                                        $persons = $booking ? $booking->persons : collect([]);
                                        $totalPax = $persons->count();
                                        $rowspan = max(1, $totalPax);

                                        $checkIn = $stay->check_in ? \Carbon\Carbon::parse($stay->check_in) : null;
                                        $checkOut = $stay->check_out ? \Carbon\Carbon::parse($stay->check_out) : null;
                                        $nights = ($checkIn && $checkOut) ? $checkIn->diffInDays($checkOut) : '—';

                                        // Status calculation
                                        $statusText = 'Scheduled';
                                        $statusBadge = 'bg-secondary';
                                        if ($checkIn) {
                                            if ($checkIn->isToday()) {
                                                $statusText = 'Check-in Today';
                                                $statusBadge = 'bg-danger text-white';
                                            } elseif ($checkIn->isTomorrow()) {
                                                $statusText = 'Tomorrow';
                                                $statusBadge = 'bg-warning text-dark';
                                            } elseif ($checkOut && $checkOut->isPast()) {
                                                $statusText = 'Checked Out';
                                                $statusBadge = 'bg-light text-muted border';
                                            } elseif ($checkIn->isPast() && ($checkOut ? $checkOut->isFuture() || $checkOut->isToday() : true)) {
                                                $statusText = 'In-House';
                                                $statusBadge = 'bg-success text-white';
                                            } elseif ($checkIn->isFuture()) {
                                                $statusText = 'Upcoming';
                                                $statusBadge = 'bg-info text-white';
                                            }
                                        }

                                        $campVal = $booking?->camp ?: ($booking?->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : null);

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

                                    @if ($totalPax === 0)
                                        {{-- Row when no booking_persons registered yet --}}
                                        <tr class="hotel-divider-row">
                                            <td class="text-center fw-bold text-muted col-sr">{{ $globalSr++ }}</td>
                                            <td class="text-center col-picture">—</td>
                                            <td class="text-center col-hajj-id">—</td>
                                            <td class="text-center col-hb">—</td>
                                            <td style="padding-left: 10px;" class="col-pilgrim">
                                                <strong class="text-dark fs-14">{{ $booking?->client?->name ?? 'Direct Pilgrim' }}</strong>
                                            </td>
                                            <td class="text-center text-muted col-given-name col-hidden">—</td>
                                            <td class="text-center text-muted col-surname col-hidden">—</td>
                                            <td class="text-center text-muted col-father-name col-hidden">—</td>
                                            <td class="text-center col-gender">—</td>
                                            <td class="text-center fw-bold col-passport">{{ $booking?->passport_number ?: '—' }}</td>
                                            <td class="text-center text-muted col-passport-doi col-hidden">—</td>
                                            <td class="text-center text-muted col-passport-exp col-hidden">—</td>

                                            {{-- Hotel & Location --}}
                                            <td style="padding-left: 10px;" class="col-hotel">
                                                <strong class="text-dark d-block fs-14">{{ strtoupper($stay->hotel_name ?: 'Hotel Not Specified') }}</strong>
                                                <span class="badge {{ $locBadgeClass }} px-2 py-0 mt-1" style="font-size: 10.5px;">{{ strtoupper($stay->location ?: 'Location') }}</span>
                                            </td>

                                            {{-- Room Details --}}
                                            <td class="text-center col-room">
                                                <span class="badge bg-dark text-white px-2 py-1" style="font-size: 11px;">{{ ucfirst($stay->room_type ?: 'Standard') }}</span>
                                                <div class="small text-muted mt-0.5">{{ $stay->no_of_rooms ?: 1 }} Room(s)</div>
                                            </td>

                                            {{-- Room No --}}
                                            <td class="text-center fw-bold col-room-no">
                                                @if(!empty($stay->room_number))
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-12">{{ $stay->room_number }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Check-In --}}
                                            <td class="text-center col-checkin">
                                                <div class="fw-bold text-dark">{{ $checkIn ? $checkIn->format('d M Y') : '—' }}</div>
                                            </td>

                                            {{-- Check-Out --}}
                                            <td class="text-center col-checkout">
                                                <div class="fw-bold text-dark">{{ $checkOut ? $checkOut->format('d M Y') : '—' }}</div>
                                            </td>

                                            {{-- Nights --}}
                                            <td class="text-center col-nights">
                                                <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-12">{{ $nights }} N</span>
                                            </td>

                                            {{-- Maktab --}}
                                            <td class="text-center col-maktab col-hidden">
                                                @if($campVal)
                                                    <span class="badge bg-warning text-dark px-2 py-0.5 fw-bold" style="font-size: 11px;">
                                                        <i class="mdi mdi-tent me-0.5"></i>{{ $campVal }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Package --}}
                                            <td style="padding-left: 10px;" class="col-package">
                                                @if ($booking?->package)
                                                    <span class="fw-semibold text-dark fs-13 d-block">
                                                        {{ $booking->package->package_title ?: ($booking->package->name ?: 'Package #' . $booking->package->id) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Voucher # --}}
                                            <td class="text-center fw-bold col-voucher">
                                                <a href="{{ route('booking.voucher', $booking->id ?? 0) }}" target="_blank" class="badge bg-primary text-white text-decoration-none px-2 py-1" style="font-size: 11px;">
                                                    {{ $booking->voucher_number ?? 'UB' . str_pad($booking->id ?? 0, 6, '0', STR_PAD_LEFT) }}
                                                </a>
                                            </td>

                                            {{-- Client / Group --}}
                                            <td style="padding-left: 10px;" class="col-client col-hidden">
                                                <span class="fw-semibold text-dark">{{ $booking?->client?->name ?? ($booking?->company?->company_name ?? '—') }}</span>
                                            </td>

                                            {{-- Status --}}
                                            <td class="text-center col-status">
                                                <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">{{ $statusText }}</span>
                                            </td>

                                            {{-- Action --}}
                                            <td class="text-center col-action no-print">
                                                @if($booking)
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" title="View Booking">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="btn btn-xs btn-outline-success py-0 px-2 fw-semibold" title="View Voucher">
                                                            <i class="mdi mdi-ticket-percent-outline"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Multiple Pilgrims Loop --}}
                                        @foreach($persons as $oIdx => $person)
                                            <tr class="{{ $oIdx === 0 ? 'hotel-divider-row' : '' }}">
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

                                                {{-- Pilgrim Full Name (Clean without Family Head badge) --}}
                                                <td style="text-align: left; padding-left: 10px;" class="col-pilgrim">
                                                    <span class="d-inline-block fw-bold text-dark" style="font-size: 14.5px; letter-spacing: 0.2px;">
                                                        {{ $person->full_name }}
                                                    </span>
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

                                                {{-- Hotel Stay details with rowspan --}}
                                                @if($oIdx === 0)
                                                    {{-- Hotel & Location --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 10px;" class="col-hotel">
                                                        <strong class="text-dark d-block fs-14">{{ strtoupper($stay->hotel_name ?: 'Hotel Not Specified') }}</strong>
                                                        <span class="badge {{ $locBadgeClass }} px-2 py-0 mt-1" style="font-size: 10.5px;">
                                                            {{ strtoupper($stay->location ?: 'Location') }}
                                                        </span>
                                                    </td>

                                                    {{-- Room Details --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-room">
                                                        <span class="badge bg-dark text-white px-2 py-1" style="font-size: 11px;">
                                                            {{ ucfirst($stay->room_type ?: 'Standard') }}
                                                        </span>
                                                        <div class="small text-muted mt-0.5">{{ $stay->no_of_rooms ?: 1 }} Room(s)</div>
                                                        <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0 mt-1" style="font-size: 10px;">
                                                            {{ $totalPax }} Pax
                                                        </div>
                                                    </td>

                                                    {{-- Room No --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center fw-bold col-room-no">
                                                        @if(!empty($stay->room_number))
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-12">{{ $stay->room_number }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Check-In --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-checkin">
                                                        <div class="fw-bold text-dark">{{ $checkIn ? $checkIn->format('d M Y') : '—' }}</div>
                                                    </td>

                                                    {{-- Check-Out --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-checkout">
                                                        <div class="fw-bold text-dark">{{ $checkOut ? $checkOut->format('d M Y') : '—' }}</div>
                                                    </td>

                                                    {{-- Nights --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-nights">
                                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-12">{{ $nights }} N</span>
                                                    </td>

                                                    {{-- Maktab --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-maktab col-hidden">
                                                        @if($campVal)
                                                            <span class="badge bg-warning text-dark px-2 py-0.5 fw-bold" style="font-size: 11px;">
                                                                <i class="mdi mdi-tent me-0.5"></i>{{ $campVal }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Package --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 10px;" class="col-package">
                                                        @if ($booking?->package)
                                                            <span class="fw-semibold text-dark fs-13 d-block">
                                                                {{ $booking->package->package_title ?: ($booking->package->name ?: 'Package #' . $booking->package->id) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Voucher # --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center fw-bold col-voucher">
                                                        <a href="{{ route('booking.voucher', $booking->id ?? 0) }}" target="_blank" class="badge bg-primary text-white text-decoration-none px-2 py-1" style="font-size: 11px;">
                                                            {{ $booking->voucher_number ?? 'UB' . str_pad($booking->id ?? 0, 6, '0', STR_PAD_LEFT) }}
                                                        </a>
                                                        <div class="small text-muted mt-0.5">Booking #{{ $booking->booking_number ?? $booking->id }}</div>
                                                    </td>

                                                    {{-- Client / Group --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 10px;" class="col-client col-hidden">
                                                        <span class="fw-semibold text-dark">{{ $booking?->client?->name ?? ($booking?->company?->company_name ?? '—') }}</span>
                                                    </td>

                                                    {{-- Status --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-status">
                                                        <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">{{ $statusText }}</span>
                                                    </td>

                                                    {{-- Action --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-action no-print">
                                                        @if($booking)
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" title="View Booking">
                                                                    <i class="mdi mdi-eye"></i>
                                                                </a>
                                                                <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="btn btn-xs btn-outline-success py-0 px-2 fw-semibold" title="View Voucher">
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
                                        <td colspan="24" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="mdi mdi-office-building-marker-outline text-muted" style="font-size: 48px;"></i>
                                                <h5 class="fw-bold text-muted mt-2">No Hotel Person Records Found</h5>
                                                <p class="text-muted small mb-3">No pilgrims match the selected hotel, location, or filter criteria.</p>
                                                <a href="{{ route('report.hotel-person') }}" class="btn btn-primary btn-sm">
                                                    <i class="mdi mdi-refresh me-1"></i> Reset Filters
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
        const storageKey = 'hotel_person_report_columns_visibility_v1';
        const defaultHiddenCols = ['given-name', 'surname', 'father-name', 'passport-doi', 'passport-exp', 'maktab', 'client'];

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

        function getActiveColumnsString() {
            const activeCols = [];
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    activeCols.push(cb.getAttribute('data-col'));
                }
            });
            return activeCols.length > 0 ? activeCols.join(',') : 'none';
        }

        function updateExportLinksWithColumns() {
            const colStr = getActiveColumnsString();

            document.querySelectorAll('a[href*="export-pdf"], .btn-export-pdf, #btnExportPdf').forEach(btn => {
                try {
                    const url = new URL(btn.href, window.location.href);
                    url.searchParams.set('columns', colStr);
                    btn.href = url.href;
                } catch(e) {}
            });

            document.querySelectorAll('a[href*="export-excel"], .btn-export-excel, #btnExportExcel').forEach(btn => {
                try {
                    const url = new URL(btn.href, window.location.href);
                    url.searchParams.set('columns', colStr);
                    btn.href = url.href;
                } catch(e) {}
            });
        }

        document.querySelectorAll('a[href*="export-pdf"], .btn-export-pdf, #btnExportPdf, a[href*="export-excel"], .btn-export-excel, #btnExportExcel').forEach(btn => {
            btn.addEventListener('click', function() {
                try {
                    const colStr = getActiveColumnsString();
                    const url = new URL(this.href, window.location.href);
                    url.searchParams.set('columns', colStr);
                    this.href = url.href;
                } catch(e) {}
            });
        });

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

        // ==========================================
        // CUSTOM CHECKBOX MULTI-SELECT CONTROLLER (Hotel & Package)
        // ==========================================
        function setupMultiSelect(type, emptyPlaceholder, itemLabel) {
            const wrapper = document.getElementById(type + 'MultiSelectWrapper');
            if (!wrapper) return;

            const triggerBtn = document.getElementById(type + 'TriggerBtn');
            const dropdownMenu = document.getElementById(type + 'DropdownMenu');
            const searchInput = wrapper.querySelector('.multiselect-search');
            const optionsContainer = document.getElementById(type + 'OptionsContainer');
            const countBadge = document.getElementById(type + 'CountBadge');
            const statusLabel = document.getElementById(type + 'StatusLabel');
            const tagsContainer = document.getElementById(type + 'TagsContainer');
            const selectAllBtn = wrapper.querySelector('.btn-multiselect-all');
            const selectNoneBtn = wrapper.querySelector('.btn-multiselect-none');
            const quickClearBtn = wrapper.querySelector('.multiselect-quick-clear');

            function toggleDropdown(show) {
                const isCurrentlyShown = !dropdownMenu.classList.contains('d-none');
                const shouldShow = typeof show === 'boolean' ? show : !isCurrentlyShown;
                
                // Close other dropdowns first
                document.querySelectorAll('.multiselect-dropdown-menu').forEach(menu => {
                    if (menu !== dropdownMenu) menu.classList.add('d-none');
                });
                document.querySelectorAll('.multiselect-trigger-box').forEach(box => {
                    if (box !== triggerBtn) box.classList.remove('active');
                });

                if (shouldShow) {
                    dropdownMenu.classList.remove('d-none');
                    triggerBtn.classList.add('active');
                    if (searchInput) {
                        setTimeout(() => searchInput.focus(), 50);
                    }
                } else {
                    dropdownMenu.classList.add('d-none');
                    triggerBtn.classList.remove('active');
                    if (searchInput) searchInput.value = '';
                    filterOptions('');
                }
            }

            // Click trigger toggles dropdown
            triggerBtn.addEventListener('click', function(e) {
                if (e.target.closest('.remove-tag') || e.target.closest('.multiselect-quick-clear')) {
                    return;
                }
                toggleDropdown();
            });

            // Close on click outside
            document.addEventListener('click', function(e) {
                if (!wrapper.contains(e.target)) {
                    dropdownMenu.classList.add('d-none');
                    triggerBtn.classList.remove('active');
                }
            });

            // Filter options on search
            function filterOptions(term) {
                const query = term.toLowerCase().trim();
                const optionRows = optionsContainer.querySelectorAll('.multiselect-option-row');
                optionRows.forEach(row => {
                    const text = row.querySelector('.option-text').textContent.toLowerCase();
                    if (!query || text.includes(query)) {
                        row.classList.remove('d-none');
                    } else {
                        row.classList.add('d-none');
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    filterOptions(this.value);
                });
            }

            // Update UI State from checkboxes
            function updateState() {
                const allCheckboxes = optionsContainer.querySelectorAll('.multiselect-checkbox');
                const checkedBoxes = Array.from(allCheckboxes).filter(cb => cb.checked);
                const count = checkedBoxes.length;

                // Update Row Highlight Classes
                allCheckboxes.forEach(cb => {
                    const row = cb.closest('.multiselect-option-row');
                    if (cb.checked) {
                        row.classList.add('checked-row');
                    } else {
                        row.classList.remove('checked-row');
                    }
                });

                // Update Count Badges
                if (countBadge) {
                    countBadge.textContent = count > 0 ? (count + ' selected') : ('All ' + itemLabel);
                }
                if (statusLabel) {
                    statusLabel.textContent = count + ' of ' + allCheckboxes.length + ' selected';
                }

                // Update Tags Container in closed box
                if (tagsContainer) {
                    tagsContainer.innerHTML = '';
                    if (count === 0) {
                        const span = document.createElement('span');
                        span.className = 'text-muted small placeholder-text';
                        span.textContent = emptyPlaceholder;
                        tagsContainer.appendChild(span);
                    } else if (count <= 2) {
                        checkedBoxes.forEach(cb => {
                            const val = cb.value;
                            const text = cb.closest('.multiselect-option-row').querySelector('.option-text').textContent.trim();
                            const pill = document.createElement('span');
                            pill.className = 'multiselect-tag-pill';
                            pill.innerHTML = `<span class="text-truncate" style="max-width: 120px;" title="${text}">${text}</span><span class="remove-tag" data-target="${type}" data-val="${val}">&times;</span>`;
                            tagsContainer.appendChild(pill);
                        });
                    } else {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-primary text-white px-2 py-1';
                        badge.style.fontSize = '11px';
                        badge.innerHTML = `<i class="mdi mdi-check-all me-1"></i>${count} ${itemLabel} Selected`;
                        tagsContainer.appendChild(badge);
                    }
                }
            }

            // Checkbox change listener
            optionsContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('multiselect-checkbox')) {
                    updateState();
                }
            });

            // Select All Button
            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const visibleRows = optionsContainer.querySelectorAll('.multiselect-option-row:not(.d-none) .multiselect-checkbox');
                    visibleRows.forEach(cb => cb.checked = true);
                    updateState();
                });
            }

            // Clear All Button
            if (selectNoneBtn) {
                selectNoneBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    optionsContainer.querySelectorAll('.multiselect-checkbox').forEach(cb => cb.checked = false);
                    updateState();
                });
            }

            // Quick Clear in trigger box
            if (quickClearBtn) {
                quickClearBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    optionsContainer.querySelectorAll('.multiselect-checkbox').forEach(cb => cb.checked = false);
                    updateState();
                });
            }

            // Remove Individual Tag
            wrapper.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-tag');
                if (removeBtn && removeBtn.getAttribute('data-target') === type) {
                    e.stopPropagation();
                    const val = removeBtn.getAttribute('data-val');
                    const cb = optionsContainer.querySelector(`.multiselect-checkbox[value="${val}"]`);
                    if (cb) {
                        cb.checked = false;
                        updateState();
                    }
                }
            });

            // Initial UI sync
            updateState();
        }

        setupMultiSelect('hotel', 'Choose Hotel(s)...', 'Hotels');
        setupMultiSelect('package', '-- All Packages --', 'Packages');
    });
</script>
@endsection
