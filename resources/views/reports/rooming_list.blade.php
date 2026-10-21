@extends('layout.master')
@section('title', 'Official Rooming List & Manifest Report')

@section('content')
<style>
    /* Official Manifest Table Styling matching PDF */
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
    .room-typ-cell {
        font-weight: 800;
        font-size: 14px;
        text-align: center;
        text-transform: uppercase;
        background-color: #ffffff;
        border-left: 2px solid #000 !important;
        border-right: 1.5px solid #000 !important;
    }
    .room-no-cell {
        font-weight: 900;
        font-size: 22px;
        color: #e51c23;
        text-align: center;
        background-color: #ffffff;
        border-left: 1.5px solid #000 !important;
        border-right: 2px solid #000 !important;
        letter-spacing: 1px;
    }
    .room-divider-row td {
        border-top: 3px solid #000000 !important;
    }
    .haji-photo {
        width: 55px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #999;
        display: block;
        margin: 0 auto;
    }
    .haji-photo-placeholder {
        width: 55px;
        height: 60px;
        background-color: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    .gender-male {
        color: #1e3a8a;
        font-weight: 700;
    }
    .gender-female {
        color: #9d174d;
        font-weight: 700;
    }
    .selected-pilgrim-row {
        background-color: #eff6ff !important;
    }
    @media print {
        .no-print, .left-side-menu, .navbar-custom, .footer, .form-check-input {
            display: none !important;
        }
        .content-page {
            margin: 0 !important;
            padding: 0 !important;
        }
        body {
            background-color: #fff !important;
            font-size: 12px;
        }
        .manifest-table th, .manifest-table td {
            border: 1.5px solid #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .room-no-cell {
            color: #e51c23 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .col-hidden {
            display: none !important;
        }
    }

    /* Column Visibility & Hidden Styles */
    .col-hidden {
        display: none !important;
    }

    .column-filter-menu {
        min-width: 210px;
        max-height: 380px;
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

    /* Custom Multi-Select Dropdown Styles */
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
                        <i class="mdi mdi-clipboard-text-multiple text-primary me-2"></i>Official Rooming List & Manifest Report
                    </h4>
                    <p class="text-muted mb-0 small">Official multi-pilgrim room allocation manifest matching airline/hotel printout format.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('report.rooming-list', array_merge(request()->query(), ['view' => 'manifest'])) }}" 
                           class="btn {{ ($viewMode ?? 'manifest') === 'manifest' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="mdi mdi-table me-1"></i> Official Manifest View
                        </a>
                        <a href="{{ route('report.rooming-list', array_merge(request()->query(), ['view' => 'cards'])) }}" 
                           class="btn {{ ($viewMode ?? '') === 'cards' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="mdi mdi-view-grid-outline me-1"></i> Room Cards View
                        </a>
                    </div>
                    <button type="button" class="btn btn-warning btn-sm fw-bold text-dark shadow-sm" id="btnHeaderAssignByHajiId" title="Assign a room to pilgrims by typing Haji ID / HB Number">
                        <i class="mdi mdi-door-open me-1"></i> Quick Assign Room (By Haji ID)
                    </button>
                    <a href="{{ route('report.rooming-list.export-excel', request()->query()) }}" 
                       class="btn btn-success btn-sm btn-export-excel" id="btnExportExcel"
                       title="Download official room manifest as Excel spreadsheet">
                        <i class="mdi mdi-file-excel me-1"></i> Download Excel
                    </a>
                    <a href="{{ route('report.rooming-list.export-pdf', request()->query()) }}" 
                       class="btn btn-danger btn-sm btn-export-pdf" id="btnExportPdf"
                       title="Download official room manifest as PDF document">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-dark btn-sm">
                        <i class="mdi mdi-printer me-1"></i> Print Manifest
                    </button>
                    <a href="{{ route('report.rooming-list') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Bulk Action Sticky Bar --}}
            <div id="bulkActionBar" class="card border-primary shadow-sm mb-3 no-print d-none" style="position: sticky; top: 70px; z-index: 1020; background: #f0f7ff; border-width: 2px;">
                <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary fs-13 px-3 py-2">
                            <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> <span id="selectedPilgrimsCount">0</span> Pilgrims Selected
                        </span>
                        <span class="text-dark small fw-semibold">Select checkboxes to divide and allocate pilgrims into rooms.</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" id="btnOpenAssignModal">
                            <i class="mdi mdi-door-open me-1"></i> 🏨 Assign Selected to Room
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm px-3 fw-bold" id="btnOpenAssignByHajiId">
                            <i class="mdi mdi-account-search me-1"></i> 🆔 Assign by Haji ID
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnBulkUnassign">
                            <i class="mdi mdi-close-circle-outline me-1"></i> Unassign from Room
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnDeselectAll">
                            Deselect All
                        </button>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="card shadow-sm border-0 mb-3 no-print">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.rooming-list') }}" id="filterForm">
                        <input type="hidden" name="view" value="{{ $viewMode ?? 'manifest' }}">
                        <div class="row g-2 align-items-end">

                            {{-- Hotel Filter (Multi-Select) --}}
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
                                                @foreach((array)$hotelFilter as $hf)
                                                    <span class="multiselect-tag-pill">
                                                        <span class="text-truncate" style="max-width: 120px;">{{ $hf }}</span>
                                                        <span class="remove-tag" data-target="hotel" data-val="{{ $hf }}">&times;</span>
                                                    </span>
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
                                            @php
                                                $allHotelItems = [];
                                                foreach($registeredHotels as $h) {
                                                    $allHotelItems[$h->name] = $h->name . ' (' . ($h->city ?? $h->place ?? 'Hotel') . ')';
                                                }
                                                foreach($distinctHotelNames as $dhn) {
                                                    if (!isset($allHotelItems[$dhn])) {
                                                        $allHotelItems[$dhn] = $dhn;
                                                    }
                                                }
                                            @endphp
                                            @foreach($allHotelItems as $hVal => $hText)
                                                @php $isHChecked = in_array($hVal, (array)$hotelFilter); @endphp
                                                <label class="dropdown-item d-flex align-items-center gap-2 py-1.5 px-2 rounded multiselect-option-row {{ $isHChecked ? 'checked-row' : '' }}" style="font-size: 12px;">
                                                    <input type="checkbox" name="hotel_name[]" value="{{ $hVal }}" class="form-check-input mt-0 multiselect-checkbox hotel-cb" {{ $isHChecked ? 'checked' : '' }}>
                                                    <span class="option-text text-truncate">{{ $hText }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Location Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Location</label>
                                <select name="location" class="form-select form-select-sm">
                                    <option value="all">All Locations</option>
                                    <option value="makkah" {{ $locationFilter === 'makkah' ? 'selected' : '' }}>Makkah</option>
                                    <option value="madinah" {{ $locationFilter === 'madinah' ? 'selected' : '' }}>Madinah</option>
                                    <option value="azizia" {{ $locationFilter === 'azizia' ? 'selected' : '' }}>Azizia</option>
                                    <option value="mina" {{ $locationFilter === 'mina' ? 'selected' : '' }}>Mina / Arafat</option>
                                    <option value="other" {{ $locationFilter === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            {{-- Room Type Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Room Type</label>
                                <select name="room_type" class="form-select form-select-sm">
                                    <option value="all">All Room Types</option>
                                    <option value="double" {{ strtolower($roomTypeFilter ?? '') === 'double' ? 'selected' : '' }}>DOUBLE (2 Beds)</option>
                                    <option value="triple" {{ strtolower($roomTypeFilter ?? '') === 'triple' ? 'selected' : '' }}>TRIPLE (3 Beds)</option>
                                    <option value="quad" {{ strtolower($roomTypeFilter ?? '') === 'quad' ? 'selected' : '' }}>QUAD (4 Beds)</option>
                                    <option value="quint" {{ strtolower($roomTypeFilter ?? '') === 'quint' ? 'selected' : '' }}>QUINT (5 Beds)</option>
                                    <option value="six" {{ in_array(strtolower($roomTypeFilter ?? ''), ['six', 'sharing']) ? 'selected' : '' }}>SIX (6 Beds)</option>
                                    <option value="single" {{ strtolower($roomTypeFilter ?? '') === 'single' ? 'selected' : '' }}>SINGLE (1 Bed)</option>
                                    <option value="suite" {{ strtolower($roomTypeFilter ?? '') === 'suite' ? 'selected' : '' }}>SUITE</option>
                                </select>
                            </div>

                            {{-- Status Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Occupancy / Allocation</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="all">All Rooms & Pilgrims</option>
                                    <option value="available" {{ $statusFilter === 'available' ? 'selected' : '' }}>🟢 Free Beds Available</option>
                                    <option value="full" {{ $statusFilter === 'full' ? 'selected' : '' }}>🔵 Room Full (100%)</option>
                                    <option value="overbooked" {{ $statusFilter === 'overbooked' ? 'selected' : '' }}>🔴 Overbooked</option>
                                    <option value="unassigned" {{ $statusFilter === 'unassigned' ? 'selected' : '' }}>🟠 Pending Room Allocation</option>
                                </select>
                            </div>

                            {{-- Package Filter (Multi-Select) --}}
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

                            {{-- From Date Filter --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate ?? '' }}">
                            </div>

                            {{-- To Date Filter --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate ?? '' }}">
                            </div>

                            {{-- Search Bar --}}
                            <div class="col-xl-5 col-md-6 col-12">
                                <label class="form-label small fw-semibold text-muted mb-1">Search Room / Pilgrim / Passport / HB</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Search by Room Number (e.g. 101, R102), Pilgrim Name, Passport #, HB #..." value="{{ $search }}">
                                    @if(!empty($search))
                                        <a href="{{ route('report.rooming-list', request()->except('search')) }}" class="btn btn-outline-secondary">× Clear</a>
                                    @endif
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="col-xl-3 col-md-6 col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="mdi mdi-filter me-1"></i> Apply Filter
                                </button>
                                <a href="{{ route('report.rooming-list') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- Summary Ribbon --}}
            <div class="row g-2 mb-3 no-print">
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                        <span class="text-muted small fw-semibold">Total Allocated Rooms:</span>
                        <strong class="fs-16 text-dark">{{ number_format($totalRoomsInUse) }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                        <span class="text-muted small fw-semibold">Total Pilgrims (Pax):</span>
                        <strong class="fs-16 text-primary">{{ number_format($totalBedsOccupied) }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                        <span class="text-muted small fw-semibold">Total Bed Capacity:</span>
                        <strong class="fs-16 text-info">{{ number_format($totalBedsCapacity) }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                        <span class="text-muted small fw-semibold">Pending Allocation:</span>
                        <strong class="fs-16 text-warning">{{ number_format($unassignedPilgrimsCount ?? 0) }}</strong>
                    </div>
                </div>
            </div>

            {{-- Quick Divide / Fast Selection Toolbar --}}
            <div class="card bg-light border mb-3 no-print shadow-none">
                <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="text-dark fw-bold small"><i class="mdi mdi-lightning-bolt text-warning me-1"></i>⚡ Quick Divide Unassigned:</span>
                        <button type="button" class="btn btn-outline-primary btn-xs quick-select-btn fw-bold" data-type="double" data-count="2" title="Select next 2 unassigned Double (Couple) pilgrims">
                            👥 Next 2 Double (Couple)
                        </button>
                        <button type="button" class="btn btn-outline-success btn-xs quick-select-btn fw-bold" data-type="six" data-count="6" title="Select next 6 unassigned Sharing pilgrims">
                            👥 Next 6 Sharing
                        </button>
                        <button type="button" class="btn btn-outline-info btn-xs quick-select-btn fw-bold" data-type="triple" data-count="3" title="Select next 3 unassigned Triple pilgrims">
                            👥 Next 3 Triple
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-xs quick-select-btn fw-bold" data-type="quad" data-count="4" title="Select next 4 unassigned Quad pilgrims">
                            👥 Next 4 Quad
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-xs fw-bold" id="btnSelectAllPending" title="Select all pending pilgrims">
                            ⚡ Select All Pending ({{ $unassignedPilgrimsCount ?? 0 }})
                        </button>
                    </div>
                    <div class="text-muted small">
                        <i class="mdi mdi-information-outline text-primary me-1"></i>Tick checkboxes to divide pilgrims into rooms (e.g. 2 for Double, 6 for Sharing).
                    </div>
                </div>
            </div>

            {{-- OFFICIAL MANIFEST TABLE VIEW (EXACT MATCH TO PDF) --}}
            @if(($viewMode ?? 'manifest') === 'manifest')
                <div class="card shadow-sm border-0 mb-4">
                    @php
                        $activeLocKey = strtolower(trim($locationFilter ?? 'all'));
                        $activeLocTitle = match($activeLocKey) {
                            'makkah'  => 'MAKKAH HOTEL ALLOCATION',
                            'madinah' => 'MADINAH HOTEL ALLOCATION',
                            'azizia'  => 'AZIZIA BUILDING ALLOCATION',
                            'mina'    => 'MINA / ARAFAT CAMPS ALLOCATION',
                            'other'   => 'OTHER / JEDDAH ALLOCATION',
                            default   => 'ALL LOCATIONS & STAGES (MAKKAH / MADINAH / AZIZIA / MINA)',
                        };
                        $activeLocBadge = match($activeLocKey) {
                            'makkah'  => 'bg-success text-white',
                            'madinah' => 'bg-info text-dark',
                            'azizia'  => 'bg-warning text-dark',
                            'mina'    => 'bg-dark text-white',
                            default   => 'bg-primary text-white',
                        };
                    @endphp
                    {{-- Prominent Location & Stage Header --}}
                    <div class="px-3 py-2 bg-light border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="badge {{ $activeLocBadge }} fs-13 px-3 py-1.5 shadow-sm">
                                <i class="mdi mdi-map-marker-radius me-1"></i>{{ $activeLocTitle }}
                            </span>
                            @if(!empty($hotelFilter) && count((array)$hotelFilter) > 0)
                                <span class="badge bg-dark text-white fs-12 px-2 py-1 shadow-sm">
                                    <i class="mdi mdi-city me-1"></i>{{ implode(', ', (array)$hotelFilter) }}
                                </span>
                            @endif
                            @if(!empty($roomTypeFilter) && $roomTypeFilter !== 'all')
                                <span class="badge bg-secondary text-white fs-12 px-2 py-1 shadow-sm">
                                    <i class="mdi mdi-door me-1"></i>{{ strtoupper($roomTypeFilter) }} ROOM
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-semibold">
                                Total Pax: <strong class="text-primary fs-14">{{ number_format($totalBedsOccupied) }}</strong>
                                &bull; Pending: <strong class="text-warning fs-14">{{ number_format($unassignedPilgrimsCount ?? 0) }}</strong>
                            </span>
                        </div>
                    </div>
                    <div class="card-header bg-white border-bottom py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark fs-14">
                                <i class="mdi mdi-table-eye text-primary me-1"></i>Manifest Columns
                            </span>
                            <span class="badge bg-light text-muted border">Customizable View</span>
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
                                        <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="dob">
                                        <span>Date of Birth (DOB)</span>
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
                                        <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="cnic">
                                        <span>CNIC</span>
                                    </label>
                                    <label class="column-filter-item">
                                        <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="phone">
                                        <span>Contact / Phone</span>
                                    </label>
                                    <label class="column-filter-item">
                                        <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="booking-no">
                                        <span>Booking #</span>
                                    </label>
                                    <label class="column-filter-item">
                                        <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="client">
                                        <span>Client / Group</span>
                                    </label>
                                    <label class="column-filter-item">
                                        <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="room-typ" checked>
                                        <span>Room Type / Hotel</span>
                                    </label>
                                    <label class="column-filter-item">
                                        <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="room-no" checked>
                                        <span>Room No</span>
                                    </label>
                                    <label class="column-filter-item">
                                        <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="gender" checked>
                                        <span>Gender</span>
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
                                        <th style="width: 40px;" class="no-print text-center">
                                            <input type="checkbox" id="selectAllPilgrims" class="form-check-input" title="Select / Deselect All">
                                        </th>
                                        <th style="width: 45px;" class="col-sr">SR</th>
                                        <th style="width: 80px;" class="col-picture">PICTURE</th>
                                        <th style="width: 95px;" class="col-hajj-id">HAJJ ID</th>
                                        <th style="width: 90px;" class="col-hb">HB</th>
                                        <th style="width: 220px; text-align: left; padding-left: 10px;" class="col-name">FULL NAME</th>
                                        <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-given-name col-hidden">GIVEN NAME</th>
                                        <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-surname col-hidden">SURNAME</th>
                                        <th style="width: 150px; text-align: left; padding-left: 10px;" class="col-father-name col-hidden">HUSBAND / FATHER NAME</th>
                                        <th style="width: 100px;" class="col-dob col-hidden">DOB</th>
                                        <th style="width: 110px;" class="col-passport">PASSPORT</th>
                                        <th style="width: 110px;" class="col-passport-doi col-hidden">DATE OF ISSUE</th>
                                        <th style="width: 115px;" class="col-passport-exp col-hidden">EXPIRY DATE</th>
                                        <th style="width: 120px;" class="col-cnic col-hidden">CNIC</th>
                                        <th style="width: 110px;" class="col-phone col-hidden">PHONE</th>
                                        <th style="width: 100px;" class="col-booking-no col-hidden">BOOKING #</th>
                                        <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-client col-hidden">CLIENT / GROUP</th>
                                        <th style="width: 125px;" class="col-room-typ">ROOM TYP</th>
                                        <th style="width: 125px;" class="col-room-no">ROOM NO</th>
                                        <th style="width: 80px;" class="col-gender">GENDER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $globalSr = 1; @endphp
                                    @forelse($groupedRooms as $roomKey => $room)
                                        @php
                                            $occupants = array_values($room['occupants']);
                                            $totalOccupants = count($occupants);
                                            $rowspan = max(1, $totalOccupants);
                                            
                                            $rawNum = trim($room['room_number']);
                                            $isPending = ($rawNum === 'PENDING' || $rawNum === 'UNASSIGNED' || empty($rawNum));
                                            $roomDisplayNo = $isPending ? 'PENDING' : (str_starts_with(strtoupper($rawNum), 'R') ? strtoupper($rawNum) : ('R' . $rawNum));
                                            
                                            $rawRoomType = trim($room['room_type'] ?? '');
                                            if (empty($rawRoomType) || in_array(strtolower($rawRoomType), ['pending allocation', 'pending', 'unassigned'])) {
                                                $firstOcc = $occupants[0] ?? null;
                                                $rawRoomType = $firstOcc['booked_room_type'] ?? ($firstOcc['room_type'] ?? 'Quad');
                                            }
                                            $roomTypeDisplay = strtoupper($rawRoomType);
                                            if (in_array(strtolower($roomTypeDisplay), ['sharing', '6', 'six-bed', '6-bed'])) {
                                                $roomTypeDisplay = 'SIX';
                                            }

                                            $loc = strtolower(trim($room['location'] ?? 'makkah'));
                                            $locLabel = match($loc) {
                                                'makkah'  => 'MAKKAH HOTEL',
                                                'azizia'  => 'AZIZIA BUILDING',
                                                'mina'    => 'MINA / ARAFAT',
                                                'arafat'  => 'ARAFAT CAMP',
                                                'madinah' => 'MADINAH HOTEL',
                                                'unassigned' => 'UNASSIGNED',
                                                default   => strtoupper($loc),
                                            };
                                            $locBadgeClass = match($loc) {
                                                'makkah'  => 'bg-success text-white',
                                                'azizia'  => 'bg-warning text-dark',
                                                'mina', 'arafat' => 'bg-dark text-white',
                                                'madinah' => 'bg-info text-dark',
                                                'unassigned' => 'bg-warning text-dark',
                                                default   => 'bg-secondary text-white',
                                            };
                                        @endphp

                                        @if($totalOccupants === 0)
                                            {{-- Empty Room Row --}}
                                            <tr class="room-divider-row">
                                                <td class="text-center no-print">—</td>
                                                <td class="text-center text-muted fw-bold col-sr">{{ $globalSr++ }}</td>
                                                <td class="text-center text-muted col-picture">—</td>
                                                <td class="text-center text-muted col-hajj-id">—</td>
                                                <td class="text-center text-muted col-hb">—</td>
                                                <td class="text-muted fst-italic col-name"><em>(Empty Room - {{ $room['total_capacity'] }} Beds Available)</em></td>
                                                <td class="text-center text-muted col-given-name col-hidden">—</td>
                                                <td class="text-center text-muted col-surname col-hidden">—</td>
                                                <td class="text-center text-muted col-father-name col-hidden">—</td>
                                                <td class="text-center text-muted col-dob col-hidden">—</td>
                                                <td class="text-center text-muted col-passport">—</td>
                                                <td class="text-center text-muted col-passport-doi col-hidden">—</td>
                                                <td class="text-center text-muted col-passport-exp col-hidden">—</td>
                                                <td class="text-center text-muted col-cnic col-hidden">—</td>
                                                <td class="text-center text-muted col-phone col-hidden">—</td>
                                                <td class="text-center text-muted col-booking-no col-hidden">—</td>
                                                <td class="text-center text-muted col-client col-hidden">—</td>
                                                <td class="room-typ-cell col-room-typ">
                                                    <div class="mb-1">
                                                        <span class="badge {{ $locBadgeClass }} px-2 py-1" style="font-size: 10px; font-weight: 700;">
                                                             {{ $locLabel }}
                                                        </span>
                                                    </div>
                                                    <div class="fw-bold text-dark mb-1" style="font-size: 11px;">{{ $room['hotel_name'] }}</div>
                                                    <div class="fw-bold text-primary">{{ $roomTypeDisplay }} ({{ $room['total_capacity'] }} Beds)</div>
                                                </td>
                                                <td class="room-no-cell col-room-no">
                                                    <span class="fs-14 fw-bold text-danger">{{ $roomDisplayNo }}</span>
                                                    <small class="d-block text-muted" style="font-size: 10px;">0/{{ $room['total_capacity'] }} Beds</small>
                                                </td>
                                                <td class="text-center text-muted col-gender">—</td>
                                            </tr>
                                        @else
                                            @foreach($occupants as $oIdx => $occ)
                                                <tr class="{{ $loop->first ? 'room-divider-row' : '' }} {{ $isPending ? 'bg-warning-subtle' : '' }}" id="row-pilgrim-{{ $occ['person_id'] }}-{{ $occ['allocation_id'] ?? 'pending' }}">
                                                    <td class="text-center no-print">
                                                        @if(!empty($occ['real_person_id']))
                                                            <input type="checkbox" class="form-check-input pilgrim-check" 
                                                                   value="{{ $occ['real_person_id'] }}"
                                                                   data-allocation-id="{{ $occ['allocation_id'] ?? '' }}"
                                                                   data-hotel="{{ $room['hotel_name'] ?? '' }}"
                                                                   data-room="{{ $room['room_number'] ?? '' }}"
                                                                   data-location="{{ $room['location'] ?? '' }}"
                                                                   data-name="{{ $occ['name'] }}"
                                                                   data-haji="{{ $occ['hajj_id'] ?? '' }}"
                                                                   data-hb="{{ $occ['hb_number'] }}"
                                                                   data-passport="{{ $occ['passport'] }}"
                                                                   data-gender="{{ $occ['gender'] }}"
                                                                   data-booking="{{ $occ['booking_number'] }}"
                                                                   data-booked-type="{{ (!empty($occ['booked_room_type']) && !in_array(strtolower($occ['booked_room_type']), ['pending allocation', 'pending'])) ? $occ['booked_room_type'] : ($occ['room_type'] ?? 'Quad') }}"
                                                                   data-status="{{ $isPending ? 'pending' : 'assigned' }}">
                                                        @else
                                                            <span class="text-muted" style="font-size: 10px;">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center fw-bold col-sr">{{ $globalSr++ }}</td>
                                                    <td class="text-center p-1 col-picture">
                                                        @if(!empty($occ['photo']) && file_exists(public_path($occ['photo'])))
                                                            <img src="{{ asset($occ['photo']) }}" alt="{{ $occ['name'] }}" class="haji-photo">
                                                        @else
                                                            <div class="haji-photo-placeholder">
                                                                 <i class="mdi {{ strtoupper($occ['gender']) === 'FEMALE' ? 'mdi-account-female text-danger' : 'mdi-account text-primary' }} fs-24"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center fw-semibold text-uppercase col-hajj-id">{{ !empty($occ['hajj_id']) ? $occ['hajj_id'] : '—' }}</td>
                                                    <td class="text-center fw-semibold text-uppercase col-hb">
                                                        @if(!empty($occ['hb_number']))
                                                            <span class="badge bg-dark text-white px-2 py-1" style="font-size: 11px;">{{ $occ['hb_number'] }}</span>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td style="text-align: left; padding-left: 10px;" class="fw-bold text-uppercase text-dark col-name">
                                                        <span class="d-inline-block">{{ $occ['name'] }}</span>
                                                        @if($isPending)
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 9.5px; font-weight: 700;">
                                                                {{ strtoupper($occ['booked_room_type'] ?? ($occ['room_type'] ?? 'QUAD')) }}
                                                            </span>
                                                        @endif
                                                        @if(!$isPending && !empty($occ['real_person_id']))
                                                            <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-single-unassign no-print ms-1"
                                                                    data-id="{{ $occ['real_person_id'] }}"
                                                                    data-allocation-id="{{ $occ['allocation_id'] ?? '' }}"
                                                                    data-hotel="{{ $room['hotel_name'] ?? '' }}"
                                                                    data-room="{{ $room['room_number'] ?? '' }}"
                                                                    data-location="{{ $room['location'] ?? '' }}"
                                                                    data-name="{{ $occ['name'] }}"
                                                                    title="Unassign this pilgrim from room" style="font-size: 9.5px; border-radius: 4px;">
                                                                <i class="mdi mdi-close-circle-outline"></i> Unassign
                                                            </button>
                                                        @elseif($isPending && !empty($occ['real_person_id']))
                                                            <button type="button" class="btn btn-xs btn-primary py-0 px-2 btn-individual-assign no-print ms-1 shadow-sm"
                                                                    data-id="{{ $occ['real_person_id'] }}"
                                                                    data-haji="{{ $occ['hajj_id'] }}"
                                                                    data-hb="{{ $occ['hb_number'] }}"
                                                                    data-name="{{ $occ['name'] }}"
                                                                    data-hotel="{{ $occ['hotel_name'] ?? ($occ['booked_hotel_name'] ?? '') }}"
                                                                    data-location="{{ $occ['location'] ?? 'makkah' }}"
                                                                    data-type="{{ (!empty($occ['booked_room_type']) && !in_array(strtolower($occ['booked_room_type']), ['pending allocation', 'pending'])) ? $occ['booked_room_type'] : ($occ['room_type'] ?? 'Quad') }}"
                                                                    data-gender="{{ $occ['gender'] ?? 'Any' }}"
                                                                    title="Assign Room to {{ $occ['name'] }} ({{ $occ['hajj_id'] }})" style="font-size: 10px; font-weight: 700; border-radius: 4px;">
                                                                <i class="mdi mdi-door-open me-1"></i> Assign
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: left; padding-left: 10px;" class="col-given-name col-hidden fw-semibold text-muted text-uppercase">
                                                        {{ $occ['given_name'] ?? '—' }}
                                                    </td>
                                                    <td style="text-align: left; padding-left: 10px;" class="col-surname col-hidden fw-semibold text-muted text-uppercase">
                                                        {{ $occ['surname'] ?? '—' }}
                                                    </td>
                                                    <td style="text-align: left; padding-left: 10px;" class="col-father-name col-hidden fw-semibold text-muted text-uppercase">
                                                        {{ $occ['father_name'] ?? '—' }}
                                                    </td>
                                                    <td class="text-center col-dob col-hidden text-muted small">
                                                        {{ $occ['dob'] ?? '—' }}
                                                    </td>
                                                    <td class="text-center fw-bold text-uppercase col-passport">{{ $occ['passport'] }}</td>
                                                    <td class="text-center col-passport-doi col-hidden small">
                                                        {{ $occ['date_of_issue'] ?? '—' }}
                                                    </td>
                                                    <td class="text-center col-passport-exp col-hidden small">
                                                        {{ $occ['passport_expiry_date'] ?? '—' }}
                                                    </td>
                                                    <td class="text-center col-cnic col-hidden font-monospace small">
                                                        {{ $occ['cnic'] ?: '—' }}
                                                    </td>
                                                    <td class="text-center col-phone col-hidden small">
                                                        {{ $occ['phone'] ?: '—' }}
                                                    </td>
                                                    <td class="text-center col-booking-no col-hidden font-monospace fw-bold small text-primary">
                                                        {{ $occ['booking_number'] ?: '—' }}
                                                    </td>
                                                    <td style="text-align: left; padding-left: 10px;" class="col-client col-hidden small fw-semibold text-truncate" style="max-width: 140px;" title="{{ $occ['client_name'] }}">
                                                        {{ $occ['client_name'] ?: '—' }}
                                                    </td>
                                                    
                                                    @if($loop->first)
                                                        <td rowspan="{{ $rowspan }}" class="room-typ-cell col-room-typ">
                                                            <div class="mb-1">
                                                                <span class="badge {{ $locBadgeClass }} px-2 py-1" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">
                                                                    <i class="mdi mdi-map-marker me-1"></i>{{ $locLabel }}
                                                                </span>
                                                            </div>
                                                            <div class="fw-bold text-dark mb-1" style="font-size: 11px; line-height: 1.2;">
                                                                {{ $room['hotel_name'] }}
                                                            </div>
                                                            <div class="fw-bold text-primary" style="font-size: 13px;">
                                                                {{ $roomTypeDisplay }} 
                                                                @if(!$isPending)
                                                                    <span class="text-muted" style="font-size: 10px;">({{ $room['total_capacity'] }} Beds)</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark px-1.5 py-0.5 ms-1" style="font-size: 9px; font-weight: 700;">PENDING #</span>
                                                                @endif
                                                            </div>
                                                            @if(!empty($room['room_gender']) && $room['room_gender'] !== 'Any' && !$isPending)
                                                                <div class="mt-1">
                                                                    <span class="badge {{ strtolower($room['room_gender']) === 'female' ? 'bg-danger text-white' : (strtolower($room['room_gender']) === 'male' ? 'bg-primary text-white' : 'bg-success text-white') }}" style="font-size: 9px; letter-spacing: 0.5px;">
                                                                        {{ strtoupper($room['room_gender']) }} ROOM
                                                                    </span>
                                                                </div>
                                                            @endif
                                                            @if(!$isPending)
                                                                <div class="no-print mt-1 d-flex gap-1 flex-wrap">
                                                                    <button type="button" 
                                                                            class="btn btn-xs btn-outline-secondary btn-adjust-bed py-0 px-1"
                                                                            style="font-size: 10px;"
                                                                            data-hotel="{{ $room['hotel_name'] }}"
                                                                            data-room="{{ $room['room_number'] }}"
                                                                            data-type="{{ $room['room_type'] }}"
                                                                            data-gender="{{ $room['room_gender'] ?? 'Any' }}"
                                                                            data-location="{{ $room['location'] }}"
                                                                            data-capacity="{{ $room['total_capacity'] }}"
                                                                            data-extra="{{ $room['extra_beds'] }}"
                                                                            data-notes="{{ $room['notes'] }}"
                                                                            title="Adjust/Increase Bed Capacity & Gender">
                                                                        <i class="mdi mdi-pencil"></i> Adjust Beds
                                                                    </button>
                                                                    @php
                                                                        $realRoomIds = array_filter(array_column($room['occupants'], 'real_person_id'));
                                                                    @endphp
                                                                    @if(!empty($realRoomIds))
                                                                        <button type="button" 
                                                                                class="btn btn-xs btn-outline-danger btn-unassign-room-all py-0 px-1"
                                                                                style="font-size: 10px;"
                                                                                data-room="{{ $room['room_number'] }}"
                                                                                data-hotel="{{ $room['hotel_name'] }}"
                                                                                data-ids="{{ implode(',', $realRoomIds) }}"
                                                                                title="Unassign all pilgrims in Room {{ $room['room_number'] }}">
                                                                            <i class="mdi mdi-close-circle-outline"></i> Unassign All
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="room-no-cell col-room-no">
                                                            @if($isPending)
                                                                <span class="badge bg-warning text-dark fs-12 px-2 py-1 fw-bold">PENDING</span>
                                                                <small class="d-block text-muted mt-1 fw-semibold" style="font-size: 10px;">{{ $totalOccupants }} Pilgrims</small>
                                                                <button type="button" 
                                                                        class="btn btn-xs btn-primary btn-quick-assign-room mt-2 py-1 px-2 fw-bold w-100 shadow-sm no-print"
                                                                        title="Assign Pilgrims by Haji ID / HB Number">
                                                                    <i class="mdi mdi-door-open me-1"></i> Assign by Haji ID
                                                                </button>
                                                            @else
                                                                <span class="fs-14 fw-bold text-danger">{{ $roomDisplayNo }}</span>
                                                                <small class="d-block text-muted fw-semibold" style="font-size: 10px;">{{ $room['occupied_beds'] }}/{{ $room['total_capacity'] }} Booked</small>
                                                            @endif
                                                        </td>
                                                    @endif

                                                    <td class="text-center {{ strtoupper($occ['gender']) === 'FEMALE' ? 'gender-female' : 'gender-male' }} text-uppercase col-gender">
                                                        {{ strtoupper($occ['gender']) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="20" class="text-center py-5 text-muted">
                                                <i class="mdi mdi-bed-empty fs-48 d-block mb-2 text-muted"></i>
                                                <h5>No Pilgrims Found</h5>
                                                <p>Try clearing filters above to view all bookings.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                {{-- ROOM CARDS VIEW --}}
                <div class="row g-3">
                    @forelse($groupedRooms as $room)
                        @php
                            $rawNum = trim($room['room_number']);
                            $isPending = ($rawNum === 'PENDING' || $rawNum === 'UNASSIGNED' || empty($rawNum));
                            $roomDisplayNo = $isPending ? 'PENDING' : (str_starts_with(strtoupper($rawNum), 'R') ? strtoupper($rawNum) : ('R' . $rawNum));
                            
                            $badgeColor = 'bg-success';
                            if ($isPending) $badgeColor = 'bg-warning text-dark';
                            elseif ($room['is_overbooked']) $badgeColor = 'bg-danger';
                            elseif ($room['is_full']) $badgeColor = 'bg-primary';
                        @endphp
                        <div class="col-xl-6 col-12">
                            <div class="card shadow-sm border h-100 {{ $isPending ? 'border-warning' : '' }}">
                                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $isPending ? 'bg-warning text-dark' : 'bg-danger' }} fs-16 px-2 py-1 fw-bold">{{ $roomDisplayNo }}</span>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $room['hotel_name'] }}</h6>
                                            <small class="text-muted text-uppercase fw-semibold">{{ $room['room_type'] }} &bull; {{ $room['location'] }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge {{ $badgeColor }} fs-11 px-2 py-1">
                                            @if($isPending)
                                                {{ count($room['occupants']) }} Pilgrims Pending
                                            @else
                                                {{ $room['occupied_beds'] }} / {{ $room['total_capacity'] }} Beds
                                            @endif
                                        </span>
                                        @if($isPending)
                                            <button type="button" 
                                                    class="btn btn-primary btn-xs ms-2 btn-quick-assign-room py-1 px-2 fw-bold"
                                                    title="Assign Pilgrims by Haji ID / HB Number">
                                                <i class="mdi mdi-door-open me-1"></i> Assign by Haji ID
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-xs ms-2 btn-adjust-bed py-0 px-2"
                                                    data-hotel="{{ $room['hotel_name'] }}"
                                                    data-room="{{ $room['room_number'] }}"
                                                    data-type="{{ $room['room_type'] }}"
                                                    data-gender="{{ $room['room_gender'] ?? 'Any' }}"
                                                    data-location="{{ $room['location'] }}"
                                                    data-capacity="{{ $room['total_capacity'] }}"
                                                    data-extra="{{ $room['extra_beds'] }}"
                                                    data-notes="{{ $room['notes'] }}">
                                                + Adjust Beds
                                            </button>
                                            @php
                                                $cardRealRoomIds = array_filter(array_column($room['occupants'], 'real_person_id'));
                                                $cardAllocIds = array_filter(array_column($room['occupants'], 'allocation_id'));
                                            @endphp
                                            @if(!empty($cardRealRoomIds))
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-xs ms-1 btn-unassign-room-all py-0 px-2"
                                                        data-room="{{ $room['room_number'] }}"
                                                        data-hotel="{{ $room['hotel_name'] }}"
                                                        data-location="{{ $room['location'] }}"
                                                        data-ids="{{ implode(',', $cardRealRoomIds) }}"
                                                        data-allocation-ids="{{ implode(',', $cardAllocIds) }}"
                                                        title="Unassign all pilgrims in Room {{ $room['room_number'] }}">
                                                    <i class="mdi mdi-close-circle-outline"></i> Unassign Room
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle mb-0" style="font-size: 12px;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="no-print" style="width: 30px;"></th>
                                                    <th>Photo</th>
                                                    <th>HB #</th>
                                                    <th>Passport</th>
                                                    <th>Name</th>
                                                    <th>Gender</th>
                                                    <th>Booking</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($room['occupants'] as $occ)
                                                    <tr id="card-pilgrim-{{ $occ['person_id'] }}-{{ $occ['allocation_id'] ?? 'pending' }}">
                                                        <td class="no-print text-center">
                                                            @if(!empty($occ['real_person_id']))
                                                                <input type="checkbox" class="form-check-input pilgrim-check" 
                                                                       value="{{ $occ['real_person_id'] }}"
                                                                       data-allocation-id="{{ $occ['allocation_id'] ?? '' }}"
                                                                       data-hotel="{{ $room['hotel_name'] ?? '' }}"
                                                                       data-room="{{ $room['room_number'] ?? '' }}"
                                                                       data-location="{{ $room['location'] ?? '' }}"
                                                                       data-name="{{ $occ['name'] }}"
                                                                       data-hb="{{ $occ['hb_number'] }}"
                                                                       data-passport="{{ $occ['passport'] }}"
                                                                       data-gender="{{ $occ['gender'] }}"
                                                                       data-booking="{{ $occ['booking_number'] }}"
                                                                       data-booked-type="{{ $occ['booked_room_type'] ?? 'Quad' }}"
                                                                       data-status="{{ $isPending ? 'pending' : 'assigned' }}">
                                                            @endif
                                                        </td>
                                                        <td style="width: 40px;">
                                                            @if(!empty($occ['photo']) && file_exists(public_path($occ['photo'])))
                                                                <img src="{{ asset($occ['photo']) }}" class="rounded" style="width: 32px; height: 36px; object-fit: cover;">
                                                            @else
                                                                <i class="mdi {{ strtoupper($occ['gender']) === 'FEMALE' ? 'mdi-account-female text-danger' : 'mdi-account text-primary' }} fs-18"></i>
                                                            @endif
                                                        </td>
                                                        <td class="fw-semibold">
                                                            @if(!empty($occ['hb_number']))
                                                                <span class="badge bg-dark text-white px-2 py-1" style="font-size: 11px;">{{ $occ['hb_number'] }}</span>
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td class="fw-bold">{{ $occ['passport'] }}</td>
                                                        <td class="fw-bold text-dark">
                                                            <span class="d-inline-block">{{ $occ['name'] }}</span>
                                                            @if(!$isPending && !empty($occ['real_person_id']))
                                                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-single-unassign no-print ms-1"
                                                                        data-id="{{ $occ['real_person_id'] }}"
                                                                        data-allocation-id="{{ $occ['allocation_id'] ?? '' }}"
                                                                        data-hotel="{{ $room['hotel_name'] ?? '' }}"
                                                                        data-room="{{ $room['room_number'] ?? '' }}"
                                                                        data-location="{{ $room['location'] ?? '' }}"
                                                                        data-name="{{ $occ['name'] }}"
                                                                        title="Unassign this pilgrim from room" style="font-size: 9px; border-radius: 4px;">
                                                                    <i class="mdi mdi-close-circle-outline"></i> Unassign
                                                                </button>
                                                            @elseif($isPending && !empty($occ['real_person_id']))
                                                                <button type="button" class="btn btn-xs btn-primary py-0 px-2 btn-individual-assign no-print ms-1 shadow-sm"
                                                                        data-id="{{ $occ['real_person_id'] }}"
                                                                        data-haji="{{ $occ['hajj_id'] }}"
                                                                        data-hb="{{ $occ['hb_number'] }}"
                                                                        data-name="{{ $occ['name'] }}"
                                                                        data-hotel="{{ $occ['hotel_name'] ?? ($occ['booked_hotel_name'] ?? '') }}"
                                                                        data-location="{{ $occ['location'] ?? 'makkah' }}"
                                                                        data-type="{{ $occ['room_type'] ?? ($occ['booked_room_type'] ?? 'Quad') }}"
                                                                        data-gender="{{ $occ['gender'] ?? 'Any' }}"
                                                                        title="Assign Room to {{ $occ['name'] }} ({{ $occ['hajj_id'] }})" style="font-size: 9.5px; font-weight: 700; border-radius: 4px;">
                                                                    <i class="mdi mdi-door-open me-1"></i> Assign
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td class="{{ strtoupper($occ['gender']) === 'FEMALE' ? 'gender-female' : 'gender-male' }} fw-semibold">{{ strtoupper($occ['gender']) }}</td>
                                                        <td>
                                                            <a href="{{ route('booking.show', $occ['booking_id']) }}" class="text-primary fw-bold" target="_blank">{{ $occ['booking_number'] }}</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-2"><em>No pilgrims assigned yet.</em></td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">No room allocations found.</p>
                        </div>
                    @endforelse
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Bulk Assign Room Modal --}}
<div class="modal fade" id="assignRoomModal" tabindex="-1" aria-labelledby="assignRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('report.rooming-list.assign-room') }}" id="assignRoomForm">
                @csrf
                <div id="hiddenPersonInputs"></div>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="assignRoomModalLabel">
                        <i class="mdi mdi-door-open me-2"></i>Assign Selected Pilgrims into a Room
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Selected Pilgrims Badges --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark small mb-0">
                                Selected Pilgrims (<span id="modalSelectedCount">0</span>):
                            </label>
                            <span class="badge bg-info-subtle text-info border border-info-subtle small px-2" id="modalTypesSummary"></span>
                        </div>
                        <div id="selectedPilgrimsPills" class="p-2 border rounded bg-light d-flex flex-wrap gap-1" style="max-height: 100px; overflow-y: auto;">
                            <span class="text-muted small">Select pilgrims via checkboxes or type Haji ID below</span>
                        </div>
                    </div>

                    {{-- Assign by Haji ID Input with Live Search --}}
                    <div class="mb-3 p-3 bg-primary-subtle border border-primary-subtle rounded">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark small mb-0">
                                <i class="mdi mdi-card-account-details-outline text-primary me-1"></i>Haji ID / HB Number / Pilgrim Name:
                            </label>
                            <span id="hajiLookupStatus" class="small fw-semibold text-muted"></span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold text-primary"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" name="haji_ids" id="assignModalHajiIds" class="form-control" 
                                   placeholder="Type Haji ID (e.g. PW27001 or 27001), HB# (HB0005), or multiple comma-separated...">
                            <button type="button" class="btn btn-primary btn-sm px-3" id="btnTriggerHajiLookup">
                                <i class="mdi mdi-account-search me-1"></i> Find
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Supports formats like <code>PW27001</code>, <code>27001</code>, <code>HB0005</code>, <code>27001-27004</code>, or multiple comma/space separated IDs.
                        </small>
                        <div id="liveLookupResults" class="mt-2 d-none"></div>
                    </div>

                    <div class="row g-3">
                        {{-- Location --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Location / Stage <span class="text-danger">*</span></label>
                            <select name="location" id="assignModalLocation" class="form-select" required>
                                <option value="makkah" selected>Makkah Hotel</option>
                                <option value="azizia">Azizia Building</option>
                                <option value="mina">Mina / Arafat Camps</option>
                                <option value="madinah">Madinah Hotel</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        {{-- Hotel / Building Name --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Hotel / Building Name <span class="text-danger">*</span></label>
                            <select id="assignModalHotelSelect" class="form-select form-select-sm mb-1">
                                <option value="">-- Select Registered Hotel --</option>
                                @foreach($registeredHotels as $h)
                                    <option value="{{ $h->name }}">{{ $h->name }} ({{ $h->city ?? $h->place ?? 'Hotel' }})</option>
                                @endforeach
                            </select>
                            <input type="text" name="hotel_name" id="assignModalHotelInput" class="form-control" placeholder="Type Hotel Name (e.g. Anjum Makkah)" required>
                        </div>

                        {{-- Room Type --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Room Type <span class="text-danger">*</span></label>
                            <select name="room_type" id="assignModalRoomType" class="form-select" required>
                                <option value="Double">Double (2 Beds)</option>
                                <option value="Triple">Triple (3 Beds)</option>
                                <option value="Quad" selected>Quad (4 Beds)</option>
                                <option value="Quint">Quint (5 Beds)</option>
                                <option value="Six">Six / Sharing (6 Beds)</option>
                                <option value="Single">Single (1 Bed)</option>
                                <option value="Suite">Suite</option>
                            </select>
                        </div>

                        {{-- Room Number --}}
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold small text-dark mb-0">
                                    Room Number <span class="text-danger">*</span>
                                </label>
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="checkbox" id="chkRoomPendingToggle" style="cursor: pointer;">
                                    <label class="form-check-label text-muted small" for="chkRoomPendingToggle" style="font-size: 11px; cursor: pointer;">Pending</label>
                                </div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text fw-bold text-danger">R</span>
                                <input type="text" name="room_number" id="assignModalRoomNumber" class="form-control fw-bold fs-16 text-danger" placeholder="e.g. 101, 204, 402" required>
                            </div>
                            <small class="text-muted d-block mt-1" id="roomNumberHelpText">
                                Enter room number (e.g. 402) to immediately allocate room.
                            </small>
                        </div>

                        {{-- Room Gender --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Room Gender Designation</label>
                            <select name="gender" id="assignModalGender" class="form-select">
                                <option value="Any">Any / Mixed</option>
                                <option value="Male">Male Room (Men)</option>
                                <option value="Female">Female Room (Women)</option>
                                <option value="Family">Family / Couple</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="mdi mdi-check-all me-1"></i> Save Room Allocation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Adjust Bed Capacity Modal --}}
<div class="modal fade" id="adjustBedModal" tabindex="-1" aria-labelledby="adjustBedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('report.rooming-list.adjust-bed') }}" id="adjustBedForm">
                @csrf
                <input type="hidden" name="hotel_name" id="modalHotelName">
                <input type="hidden" name="room_number" id="modalRoomNumber">
                <input type="hidden" name="room_type" id="modalRoomType">
                <input type="hidden" name="location" id="modalLocation">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="adjustBedModalLabel">
                        <i class="mdi mdi-bed-king me-2"></i>Adjust Bed Capacity for Room <span id="modalRoomDisplay" class="badge bg-white text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 mb-3 small">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Hotel: <strong id="modalHotelDisplay"></strong> | Type: <strong id="modalTypeDisplay"></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Total Bed Capacity for this Room <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-bed"></i></span>
                            <input type="number" name="bed_capacity" id="modalBedCapacity" class="form-control form-control-lg fw-bold" min="1" max="50" required>
                        </div>
                        <small class="text-muted">Set how many pilgrims can stay in this room (e.g., 2 for Double, 3 for Triple, 5 for Quint, 6 for Six/Sharing, or increase for extra beds).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Designated Room Gender</label>
                        <select name="gender" id="modalGender" class="form-select">
                            <option value="Any">Any / Mixed</option>
                            <option value="Male">Male Room (Men)</option>
                            <option value="Female">Female Room (Women)</option>
                            <option value="Family">Family / Couple</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Extra Beds Added (Optional Note)</label>
                        <input type="number" name="extra_beds" id="modalExtraBeds" class="form-control" min="0" max="20" value="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Notes / Reason for bed adjustment</label>
                        <textarea name="notes" id="modalNotes" class="form-control" rows="2" placeholder="e.g. Extra mattress added for family"></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="mdi mdi-check me-1"></i> Save Room Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden Unassign Form --}}
<form method="POST" action="{{ route('report.rooming-list.unassign-room') }}" id="bulkUnassignForm" style="display:none;">
    @csrf
    <div id="unassignHiddenInputs"></div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ═══════════════════════════════════════
        // ADJUST BED MODAL
        // ═══════════════════════════════════════
        const adjustModalEl = document.getElementById('adjustBedModal');
        const adjustModal = adjustModalEl ? new bootstrap.Modal(adjustModalEl) : null;

        document.querySelectorAll('.btn-adjust-bed').forEach(btn => {
            btn.addEventListener('click', function() {
                const hotel = this.getAttribute('data-hotel');
                const room = this.getAttribute('data-room');
                const type = this.getAttribute('data-type');
                const gender = this.getAttribute('data-gender') || 'Any';
                const loc = this.getAttribute('data-location');
                const cap = this.getAttribute('data-capacity');
                const extra = this.getAttribute('data-extra') || '0';
                const notes = this.getAttribute('data-notes') || '';

                document.getElementById('modalHotelName').value = hotel;
                document.getElementById('modalRoomNumber').value = room;
                document.getElementById('modalRoomType').value = type;
                document.getElementById('modalLocation').value = loc;

                document.getElementById('modalRoomDisplay').innerText = room;
                document.getElementById('modalHotelDisplay').innerText = hotel;
                document.getElementById('modalTypeDisplay').innerText = type || 'Standard';

                document.getElementById('modalBedCapacity').value = cap;
                document.getElementById('modalGender').value = gender;
                document.getElementById('modalExtraBeds').value = extra;
                document.getElementById('modalNotes').value = notes;

                if (adjustModal) adjustModal.show();
            });
        });

        // ═══════════════════════════════════════
        // CHECKBOX SELECTION & BULK ROOM ASSIGNMENT
        // ═══════════════════════════════════════
        const selectAllEl = document.getElementById('selectAllPilgrims');
        const pilgrimCheckboxes = document.querySelectorAll('.pilgrim-check');
        const bulkActionBar = document.getElementById('bulkActionBar');
        const selectedPilgrimsCountEl = document.getElementById('selectedPilgrimsCount');
        const assignModalEl = document.getElementById('assignRoomModal');
        const assignModal = assignModalEl ? new bootstrap.Modal(assignModalEl) : null;

        function updateSelectionState() {
            const checked = document.querySelectorAll('.pilgrim-check:checked');
            const count = checked.length;

            if (selectedPilgrimsCountEl) selectedPilgrimsCountEl.innerText = count;

            if (count > 0) {
                bulkActionBar.classList.remove('d-none');
            } else {
                bulkActionBar.classList.add('d-none');
            }

            // Update row background
            pilgrimCheckboxes.forEach(cb => {
                const tr = cb.closest('tr');
                if (tr) {
                    if (cb.checked) tr.classList.add('selected-pilgrim-row');
                    else tr.classList.remove('selected-pilgrim-row');
                }
            });

            if (selectAllEl) {
                selectAllEl.checked = (count > 0 && count === pilgrimCheckboxes.length);
            }
        }

        if (selectAllEl) {
            selectAllEl.addEventListener('change', function() {
                const state = this.checked;
                pilgrimCheckboxes.forEach(cb => cb.checked = state);
                updateSelectionState();
            });
        }

        pilgrimCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectionState);
        });

        document.getElementById('btnDeselectAll')?.addEventListener('click', function() {
            pilgrimCheckboxes.forEach(cb => cb.checked = false);
            if (selectAllEl) selectAllEl.checked = false;
            updateSelectionState();
        });

        // ═══════════════════════════════════════
        // QUICK DIVIDE / SELECTION HELPERS
        // ═══════════════════════════════════════
        document.querySelectorAll('.quick-select-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetType = (this.getAttribute('data-type') || '').toLowerCase();
                const targetCount = parseInt(this.getAttribute('data-count') || '1', 10);

                // Uncheck all first
                pilgrimCheckboxes.forEach(cb => cb.checked = false);

                const pendingCheckboxes = Array.from(pilgrimCheckboxes).filter(cb => {
                    const status = cb.getAttribute('data-status') || '';
                    return status === 'pending';
                });

                // First try to find pending pilgrims matching targetType in booked type
                let matched = pendingCheckboxes.filter(cb => {
                    const bType = (cb.getAttribute('data-booked-type') || '').toLowerCase();
                    if (targetType === 'six' || targetType === 'sharing') {
                        return bType.includes('six') || bType.includes('sharing') || bType.includes('6');
                    }
                    return bType.includes(targetType);
                });

                // If not enough matched, fill up from remaining pending
                if (matched.length < targetCount) {
                    const remaining = pendingCheckboxes.filter(cb => !matched.includes(cb));
                    matched = matched.concat(remaining.slice(0, targetCount - matched.length));
                }

                // Check first N
                const toSelect = matched.slice(0, targetCount);
                toSelect.forEach(cb => cb.checked = true);

                updateSelectionState();

                if (toSelect.length > 0) {
                    toSelect[0].closest('tr')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        });

        document.getElementById('btnSelectAllPending')?.addEventListener('click', function() {
            pilgrimCheckboxes.forEach(cb => {
                const status = cb.getAttribute('data-status') || '';
                cb.checked = (status === 'pending');
            });
            updateSelectionState();
        });

        // Reusable Helper to Populate and Open Assign Modal for Pilgrims
        function populateAndOpenAssignModal(checkboxesList, fallbackBtn = null) {
            const hiddenInputs = document.getElementById('hiddenPersonInputs');
            const pillsContainer = document.getElementById('selectedPilgrimsPills');
            const modalCount = document.getElementById('modalSelectedCount');
            const modalTypesSummary = document.getElementById('modalTypesSummary');
            const hInp = document.getElementById('assignModalHajiIds');
            const roomTypeSelect = document.getElementById('assignModalRoomType');
            const locSelect = document.getElementById('assignModalLocation');
            const hSelect = document.getElementById('assignModalHotelSelect');
            const hInput = document.getElementById('assignModalHotelInput');
            const genderSelect = document.getElementById('assignModalGender');

            if (hiddenInputs) hiddenInputs.innerHTML = '';
            if (pillsContainer) pillsContainer.innerHTML = '';

            const hajiIds = [];
            let femaleCount = 0;
            let maleCount = 0;
            const bookedTypes = {};
            let prefilledHotel = '';
            let prefilledLocation = '';
            let prefilledType = '';

            const seenPids = new Set();
            checkboxesList.forEach(cb => {
                const pid = cb.value;
                if (!pid || seenPids.has(pid)) return;
                seenPids.add(pid);

                const name = cb.getAttribute('data-name') || '';
                const haji = cb.getAttribute('data-haji') || '';
                const hb = cb.getAttribute('data-hb') || '';
                const gender = cb.getAttribute('data-gender') || 'Male';
                const bType = cb.getAttribute('data-booked-type') || 'Quad';
                const hotel = cb.getAttribute('data-hotel') || '';
                const loc = cb.getAttribute('data-location') || '';

                if (haji) hajiIds.push(haji);
                if (hotel && !prefilledHotel && hotel !== 'Pending Room Allocation' && hotel !== 'Unspecified Hotel') {
                    prefilledHotel = hotel;
                }
                if (loc && !prefilledLocation && loc !== 'unassigned') {
                    prefilledLocation = loc;
                }
                if (bType && !prefilledType) prefilledType = bType;

                if (gender.toLowerCase() === 'female') femaleCount++;
                else maleCount++;

                bookedTypes[bType] = (bookedTypes[bType] || 0) + 1;

                if (hiddenInputs) {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'person_ids[]';
                    inp.value = pid;
                    hiddenInputs.appendChild(inp);
                }

                if (pillsContainer) {
                    const pill = document.createElement('span');
                    pill.className = `badge ${gender.toLowerCase() === 'female' ? 'bg-danger text-white' : 'bg-primary text-white'} p-1 px-2 me-1 mb-1 shadow-sm`;
                    pill.innerHTML = `<i class="mdi ${gender.toLowerCase() === 'female' ? 'mdi-account-female' : 'mdi-account'} me-1"></i>${name} <strong>(${haji || '—'})</strong> <small class="opacity-75">[${hb || bType}]</small>`;
                    pillsContainer.appendChild(pill);
                }
            });

            if (fallbackBtn && seenPids.size === 0) {
                const pid = fallbackBtn.getAttribute('data-id');
                const haji = fallbackBtn.getAttribute('data-haji') || '';
                const name = fallbackBtn.getAttribute('data-name') || '';
                const hb = fallbackBtn.getAttribute('data-hb') || '';
                const hotel = fallbackBtn.getAttribute('data-hotel') || '';
                const location = fallbackBtn.getAttribute('data-location') || 'makkah';
                const type = fallbackBtn.getAttribute('data-type') || 'Quad';
                const gender = fallbackBtn.getAttribute('data-gender') || 'Any';

                if (haji) hajiIds.push(haji);
                prefilledHotel = hotel;
                prefilledLocation = location;
                prefilledType = type;
                if (gender.toLowerCase() === 'female') femaleCount++;
                else maleCount++;

                if (hiddenInputs) {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'person_ids[]';
                    inp.value = pid;
                    hiddenInputs.appendChild(inp);
                }

                if (pillsContainer) {
                    const pill = document.createElement('span');
                    pill.className = `badge ${gender.toLowerCase() === 'female' ? 'bg-danger text-white' : 'bg-primary text-white'} p-1 px-2 me-1 mb-1 shadow-sm`;
                    pill.innerHTML = `<i class="mdi ${gender.toLowerCase() === 'female' ? 'mdi-account-female' : 'mdi-account'} me-1"></i>${name} <strong>(${haji})</strong> <small class="opacity-75">[${hb}]</small>`;
                    pillsContainer.appendChild(pill);
                }
            }

            const totalSelected = seenPids.size || (fallbackBtn ? 1 : 0);
            if (modalCount) modalCount.innerText = totalSelected;

            // Populate Haji IDs in text box (e.g. PW27001, PW27002)
            if (hInp) hInp.value = hajiIds.join(', ');

            // Types summary
            if (modalTypesSummary) {
                const typeArr = Object.entries(bookedTypes).map(([k, v]) => `${v} ${k}`);
                modalTypesSummary.innerText = typeArr.length ? ('Booked: ' + typeArr.join(', ')) : (hajiIds.join(', '));
            }

            // Location
            if (locSelect) {
                const curFilter = "{{ strtolower(trim($locationFilter ?? 'makkah')) }}";
                locSelect.value = (prefilledLocation && prefilledLocation !== 'unassigned') ? prefilledLocation.toLowerCase() : ((curFilter && curFilter !== 'all') ? curFilter : 'makkah');
            }

            // Hotel
            if (!prefilledHotel || prefilledHotel === 'Pending Room Allocation' || prefilledHotel === 'Unspecified Hotel') {
                if (hSelect && hSelect.options.length > 1) {
                    hSelect.selectedIndex = 1;
                    prefilledHotel = hSelect.value;
                }
            }
            if (hInput) hInput.value = prefilledHotel;
            if (hSelect && prefilledHotel) {
                for (let opt of hSelect.options) {
                    if (opt.value && opt.value.toLowerCase() === prefilledHotel.toLowerCase()) {
                        hSelect.value = opt.value;
                        break;
                    }
                }
            }

            // Smart suggest room type based on count or booked type
            if (roomTypeSelect) {
                const distinctTypes = Object.keys(bookedTypes);
                if (distinctTypes.length === 1) {
                    const onlyType = distinctTypes[0].toLowerCase();
                    if (onlyType.includes('double')) roomTypeSelect.value = 'Double';
                    else if (onlyType.includes('triple')) roomTypeSelect.value = 'Triple';
                    else if (onlyType.includes('quad')) roomTypeSelect.value = 'Quad';
                    else if (onlyType.includes('quint')) roomTypeSelect.value = 'Quint';
                    else if (onlyType.includes('six') || onlyType.includes('sharing')) roomTypeSelect.value = 'Six';
                    else if (onlyType.includes('single')) roomTypeSelect.value = 'Single';
                } else if (prefilledType) {
                    const pt = prefilledType.toLowerCase();
                    if (pt.includes('double')) roomTypeSelect.value = 'Double';
                    else if (pt.includes('triple')) roomTypeSelect.value = 'Triple';
                    else if (pt.includes('quad')) roomTypeSelect.value = 'Quad';
                    else if (pt.includes('quint')) roomTypeSelect.value = 'Quint';
                    else if (pt.includes('six') || pt.includes('sharing')) roomTypeSelect.value = 'Six';
                    else if (pt.includes('single')) roomTypeSelect.value = 'Single';
                } else {
                    if (totalSelected === 1) roomTypeSelect.value = 'Single';
                    else if (totalSelected === 2) roomTypeSelect.value = 'Double';
                    else if (totalSelected === 3) roomTypeSelect.value = 'Triple';
                    else if (totalSelected === 4) roomTypeSelect.value = 'Quad';
                    else if (totalSelected === 5) roomTypeSelect.value = 'Quint';
                    else if (totalSelected >= 6) roomTypeSelect.value = 'Six';
                }
            }

            // Smart suggest gender
            if (genderSelect) {
                if (femaleCount > 0 && maleCount === 0) genderSelect.value = 'Female';
                else if (maleCount > 0 && femaleCount === 0) genderSelect.value = 'Male';
                else if (maleCount > 0 && femaleCount > 0) genderSelect.value = 'Family';
            }

            // Reset Room Number input
            const rInput = document.getElementById('assignModalRoomNumber');
            const chkPending = document.getElementById('chkRoomPendingToggle');
            const rHelp = document.getElementById('roomNumberHelpText');
            if (chkPending) chkPending.checked = false;
            if (rInput) {
                rInput.value = '';
                rInput.disabled = false;
                rInput.required = true;
                rInput.placeholder = 'e.g. 101, 204, 402';
            }
            if (rHelp) rHelp.innerText = `Enter room number to allocate ${totalSelected} pilgrim(s) (${hajiIds.join(', ')}).`;

            const lookupStatus = document.getElementById('hajiLookupStatus');
            const lookupResults = document.getElementById('liveLookupResults');
            if (lookupStatus) lookupStatus.innerHTML = '';
            if (lookupResults) {
                lookupResults.classList.add('d-none');
                lookupResults.innerHTML = '';
            }

            if (assignModal) {
                assignModal.show();
                setTimeout(() => {
                    if (rInput) rInput.focus();
                }, 350);
            }
        }

        // Open Assign Modal from Bulk Action Bar
        document.getElementById('btnOpenAssignModal')?.addEventListener('click', function() {
            const checked = Array.from(document.querySelectorAll('.pilgrim-check:checked'));
            if (checked.length === 0) {
                alert('Please select at least one pilgrim first.');
                return;
            }
            populateAndOpenAssignModal(checked);
        });

        // Open Assign by Haji ID Modal (from Bulk Action Bar or Header Button)
        function openAssignByHajiIdModal() {
            const checked = Array.from(document.querySelectorAll('.pilgrim-check:checked'));
            if (checked.length > 0) {
                populateAndOpenAssignModal(checked);
                return;
            }

            const hiddenInputs = document.getElementById('hiddenPersonInputs');
            const pillsContainer = document.getElementById('selectedPilgrimsPills');
            const modalCount = document.getElementById('modalSelectedCount');
            const modalTypesSummary = document.getElementById('modalTypesSummary');

            if (hiddenInputs) hiddenInputs.innerHTML = '';
            if (pillsContainer) pillsContainer.innerHTML = '<span class="text-muted small">Enter Haji ID(s) or name below to find and assign pilgrims</span>';
            if (modalCount) modalCount.innerText = '0';
            if (modalTypesSummary) modalTypesSummary.innerText = 'Assign by Haji ID';

            const hInp = document.getElementById('assignModalHajiIds');
            if (hInp) hInp.value = '';

            const rInput = document.getElementById('assignModalRoomNumber');
            const chkPending = document.getElementById('chkRoomPendingToggle');
            const rHelp = document.getElementById('roomNumberHelpText');
            if (chkPending) chkPending.checked = false;
            if (rInput) {
                rInput.value = '';
                rInput.disabled = false;
                rInput.required = true;
                rInput.placeholder = 'e.g. 101, 204, 402';
            }
            if (rHelp) rHelp.innerText = 'Enter room number (e.g. 402) to immediately allocate room.';

            // Pre-select first registered hotel if hotel input is currently empty or placeholder
            const hSelect = document.getElementById('assignModalHotelSelect');
            const hInput = document.getElementById('assignModalHotelInput');
            if (hInput && (!hInput.value || hInput.value === 'Pending Room Allocation' || hInput.value === 'Unspecified Hotel')) {
                if (hSelect && hSelect.options.length > 1) {
                    hSelect.selectedIndex = 1;
                    hInput.value = hSelect.value;
                }
            }

            const lookupStatus = document.getElementById('hajiLookupStatus');
            const lookupResults = document.getElementById('liveLookupResults');
            if (lookupStatus) lookupStatus.innerHTML = '';
            if (lookupResults) {
                lookupResults.classList.add('d-none');
                lookupResults.innerHTML = '';
            }

            if (assignModal) {
                assignModal.show();
                setTimeout(() => {
                    if (hInp) hInp.focus();
                }, 350);
            }
        }

        document.getElementById('btnOpenAssignByHajiId')?.addEventListener('click', openAssignByHajiIdModal);
        document.getElementById('btnHeaderAssignByHajiId')?.addEventListener('click', openAssignByHajiIdModal);

        // Direct "Assign by Haji ID" Button on Pending Room Group / Row / Card
        document.querySelectorAll('.btn-quick-assign-room').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                openAssignByHajiIdModal();
            });
        });

        // Individual Pilgrim Direct "Assign Room" Button
        document.querySelectorAll('.btn-individual-assign').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const pid = this.getAttribute('data-id');
                const checked = Array.from(document.querySelectorAll('.pilgrim-check:checked'));

                // If some checkboxes are already checked:
                // Check if current row's checkbox is among them; if not, include it!
                const currentCb = document.querySelector(`.pilgrim-check[value="${pid}"]`);
                if (currentCb && !currentCb.checked) {
                    currentCb.checked = true;
                    checked.push(currentCb);
                    updateSelectionState();
                }

                if (checked.length > 0) {
                    populateAndOpenAssignModal(checked, this);
                } else {
                    populateAndOpenAssignModal([], this);
                }
            });
        });

        // Toggle Room Pending Checkbox in Modal
        const chkRoomPending = document.getElementById('chkRoomPendingToggle');
        const modalRoomInput = document.getElementById('assignModalRoomNumber');
        const roomHelp = document.getElementById('roomNumberHelpText');
        if (chkRoomPending && modalRoomInput) {
            chkRoomPending.addEventListener('change', function() {
                if (this.checked) {
                    modalRoomInput.value = '';
                    modalRoomInput.disabled = true;
                    modalRoomInput.required = false;
                    modalRoomInput.placeholder = 'Room will remain PENDING';
                    if (roomHelp) roomHelp.innerText = 'Pilgrim will be allocated to Hotel only, with Room Number kept as Pending.';
                } else {
                    modalRoomInput.disabled = false;
                    modalRoomInput.required = true;
                    modalRoomInput.placeholder = 'e.g. 101, 204, 402';
                    if (roomHelp) roomHelp.innerText = 'Enter room number (e.g. 402) to immediately assign room and clear Pending.';
                    modalRoomInput.focus();
                }
            });
        }

        // Live Haji ID / HB Number Lookup
        let lookupTimer = null;
        const hajiInp = document.getElementById('assignModalHajiIds');
        const lookupStatus = document.getElementById('hajiLookupStatus');
        const lookupResults = document.getElementById('liveLookupResults');

        function performLookup(customTerm) {
            const term = (typeof customTerm === 'string' ? customTerm : (hajiInp?.value || '')).trim();
            if (!term || term.length < 2) {
                if (lookupStatus) lookupStatus.innerHTML = '';
                if (lookupResults) {
                    lookupResults.classList.add('d-none');
                    lookupResults.innerHTML = '';
                }
                return;
            }

            if (lookupStatus) lookupStatus.innerHTML = '<span class="text-primary"><i class="mdi mdi-loading mdi-spin"></i> Searching...</span>';

            fetch(`{{ route('report.rooming-list.lookup-pilgrims') }}?term=${encodeURIComponent(term)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success || !data.pilgrims || data.pilgrims.length === 0) {
                        if (lookupStatus) lookupStatus.innerHTML = '<span class="text-danger small"><i class="mdi mdi-alert-circle-outline"></i> No matching pilgrims found</span>';
                        if (lookupResults) {
                            lookupResults.classList.add('d-none');
                            lookupResults.innerHTML = '';
                        }
                        return;
                    }

                    if (lookupStatus) lookupStatus.innerHTML = `<span class="text-success fw-bold small"><i class="mdi mdi-check-circle"></i> Found ${data.count} Pilgrim(s)</span>`;

                    // Sync found pilgrims directly into hiddenPersonInputs so they are always submitted
                    const hiddenInputs = document.getElementById('hiddenPersonInputs');
                    if (hiddenInputs) {
                        hiddenInputs.innerHTML = '';
                        data.pilgrims.forEach(p => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden';
                            inp.name = 'person_ids[]';
                            inp.value = p.id;
                            hiddenInputs.appendChild(inp);
                        });
                    }

                    // Update selected pills
                    const pillsContainer = document.getElementById('selectedPilgrimsPills');
                    const modalCount = document.getElementById('modalSelectedCount');
                    if (pillsContainer) {
                        pillsContainer.innerHTML = '';
                        data.pilgrims.forEach(p => {
                            const pill = document.createElement('span');
                            pill.className = `badge ${p.gender.toLowerCase() === 'female' ? 'bg-danger text-white' : 'bg-primary text-white'} p-1 px-2 me-1 mb-1 shadow-sm`;
                            pill.innerHTML = `<i class="mdi ${p.gender.toLowerCase() === 'female' ? 'mdi-account-female' : 'mdi-account'} me-1"></i>${p.name} <strong class="ms-1">(${p.hajj_id})</strong> <small class="opacity-75">[${p.hb_number}]</small>`;
                            pillsContainer.appendChild(pill);
                        });
                    }
                    if (modalCount) modalCount.innerText = data.count;

                    // Results detail preview
                    if (lookupResults) {
                        lookupResults.classList.remove('d-none');
                        let html = '<div class="p-2 border rounded bg-white shadow-sm">';
                        data.pilgrims.forEach(p => {
                            const statusBadge = p.is_pending 
                                ? '<span class="badge bg-warning text-dark ms-1">Room: Pending</span>' 
                                : `<span class="badge bg-success ms-1">Room: R${p.room_number}</span>`;
                            html += `
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom last-border-0">
                                    <div>
                                        <strong class="text-dark">${p.name}</strong> 
                                        <span class="badge bg-primary-subtle text-primary border ms-1">${p.hajj_id}</span>
                                        <span class="badge bg-dark ms-1">${p.hb_number}</span>
                                        ${statusBadge}
                                        <small class="text-muted d-block">${p.hotel_name} &bull; ${p.room_type}</small>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        lookupResults.innerHTML = html;
                    }

                    // Auto-fill Hotel, Location, Room Type from first pilgrim if currently unselected
                    const first = data.pilgrims[0];
                    const hInput = document.getElementById('assignModalHotelInput');
                    const hSelect = document.getElementById('assignModalHotelSelect');
                    const locSelect = document.getElementById('assignModalLocation');
                    const typeSelect = document.getElementById('assignModalRoomType');

                    if (first.hotel_name && first.hotel_name !== 'Pending Room Allocation' && first.hotel_name !== 'Unspecified Hotel') {
                        hInput.value = first.hotel_name;
                        if (hSelect) {
                            for (let opt of hSelect.options) {
                                if (opt.value && opt.value.toLowerCase() === first.hotel_name.toLowerCase()) {
                                    hSelect.value = opt.value;
                                    break;
                                }
                            }
                        }
                    } else if (!hInput.value || hInput.value === 'Pending Room Allocation' || hInput.value === 'Unspecified Hotel') {
                        if (hSelect && hSelect.options.length > 1) {
                            hSelect.selectedIndex = 1;
                            hInput.value = hSelect.value;
                        }
                    }
                    if (first.location && locSelect) {
                        locSelect.value = (first.location && first.location !== 'unassigned') ? first.location.toLowerCase() : 'makkah';
                    }
                    if (first.room_type && typeSelect) {
                        for (let opt of typeSelect.options) {
                            if (opt.value.toLowerCase() === first.room_type.toLowerCase()) {
                                typeSelect.value = opt.value;
                                break;
                            }
                        }
                    }
                })
                .catch(err => {
                    console.error('Pilgrim lookup error:', err);
                    if (lookupStatus) lookupStatus.innerHTML = '';
                });
        }

        if (hajiInp) {
            hajiInp.addEventListener('input', function() {
                clearTimeout(lookupTimer);
                lookupTimer = setTimeout(performLookup, 300);
            });
            hajiInp.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performLookup();
                }
            });
        }
        document.getElementById('btnTriggerHajiLookup')?.addEventListener('click', () => performLookup());

        // Validate form submit: Must have either selected pilgrims or Haji ID, valid Hotel, and Room Number unless Pending toggle checked
        document.getElementById('assignRoomForm')?.addEventListener('submit', function(e) {
            const hajiInput = (document.getElementById('assignModalHajiIds')?.value || '').trim();
            const personInputs = document.querySelectorAll('#hiddenPersonInputs input');
            if (personInputs.length === 0 && !hajiInput) {
                e.preventDefault();
                alert('Please select at least one pilgrim checkbox or enter a Haji ID to assign.');
                return false;
            }

            const hInputVal = (document.getElementById('assignModalHotelInput')?.value || '').trim();
            if (!hInputVal || hInputVal.toLowerCase() === 'pending room allocation' || hInputVal.toLowerCase() === 'unspecified hotel') {
                e.preventDefault();
                alert('Please select or type a valid Hotel Name (e.g. Anjum Makkah) to assign.');
                document.getElementById('assignModalHotelSelect')?.focus();
                return false;
            }

            const chkPending = document.getElementById('chkRoomPendingToggle');
            const roomNo = (document.getElementById('assignModalRoomNumber')?.value || '').trim();
            if (!chkPending?.checked && !roomNo) {
                e.preventDefault();
                alert('Please enter a Room Number (e.g. 402) to assign the room. If you want to keep the room pending, check the "Pending" box.');
                document.getElementById('assignModalRoomNumber')?.focus();
                return false;
            }
        });

        // Hotel Select Sync in Modal
        const modalHotelSelect = document.getElementById('assignModalHotelSelect');
        const modalHotelInput = document.getElementById('assignModalHotelInput');
        if (modalHotelSelect && modalHotelInput) {
            modalHotelSelect.addEventListener('change', function() {
                if (this.value) {
                    modalHotelInput.value = this.value;
                }
            });
        }

        // Bulk Unassign Button
        document.getElementById('btnBulkUnassign')?.addEventListener('click', function() {
            const checked = document.querySelectorAll('.pilgrim-check:checked');
            if (checked.length === 0) return;

            const assignedChecked = Array.from(checked).filter(cb => cb.getAttribute('data-status') !== 'pending');
            const targetList = assignedChecked.length > 0 ? assignedChecked : Array.from(checked);

            if (confirm(`Are you sure you want to unassign ${targetList.length} selected pilgrim room allocation(s) and move them back to Pending Room Allocation?`)) {
                const unassignInputs = document.getElementById('unassignHiddenInputs');
                unassignInputs.innerHTML = '';
                targetList.forEach(cb => {
                    const aId = cb.getAttribute('data-allocation-id');
                    if (aId) {
                        const inpA = document.createElement('input');
                        inpA.type = 'hidden';
                        inpA.name = 'allocation_ids[]';
                        inpA.value = aId;
                        unassignInputs.appendChild(inpA);
                    }
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'person_ids[]';
                    inp.value = cb.value;
                    unassignInputs.appendChild(inp);
                });
                document.getElementById('bulkUnassignForm').submit();
            }
        });

        // Single Pilgrim Unassign Button
        document.querySelectorAll('.btn-single-unassign').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const pid = this.getAttribute('data-id');
                const allocId = this.getAttribute('data-allocation-id');
                const hotelName = this.getAttribute('data-hotel');
                const roomNo = this.getAttribute('data-room');
                const location = this.getAttribute('data-location');
                const name = this.getAttribute('data-name');
                if (confirm(`Are you sure you want to unassign "${name}" from this room and move back to Pending Room Allocation?`)) {
                    const unassignInputs = document.getElementById('unassignHiddenInputs');
                    unassignInputs.innerHTML = '';
                    if (allocId) {
                        const inpA = document.createElement('input');
                        inpA.type = 'hidden';
                        inpA.name = 'allocation_ids[]';
                        inpA.value = allocId;
                        unassignInputs.appendChild(inpA);
                    }
                    if (pid) {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = 'person_ids[]';
                        inp.value = pid;
                        unassignInputs.appendChild(inp);
                    }
                    if (hotelName) {
                        const inpH = document.createElement('input');
                        inpH.type = 'hidden';
                        inpH.name = 'hotel_name';
                        inpH.value = hotelName;
                        unassignInputs.appendChild(inpH);
                    }
                    if (roomNo) {
                        const inpR = document.createElement('input');
                        inpR.type = 'hidden';
                        inpR.name = 'room_number';
                        inpR.value = roomNo;
                        unassignInputs.appendChild(inpR);
                    }
                    if (location) {
                        const inpL = document.createElement('input');
                        inpL.type = 'hidden';
                        inpL.name = 'location';
                        inpL.value = location;
                        unassignInputs.appendChild(inpL);
                    }
                    document.getElementById('bulkUnassignForm').submit();
                }
            });
        });

        // Unassign Entire Room Button
        document.querySelectorAll('.btn-unassign-room-all').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const roomNo = this.getAttribute('data-room');
                const hotelName = this.getAttribute('data-hotel');
                const location = this.getAttribute('data-location');
                const rawIds = this.getAttribute('data-ids') || '';
                const ids = rawIds.split(',').map(x => x.trim()).filter(x => x.length > 0);
                const rawAllocIds = this.getAttribute('data-allocation-ids') || '';
                const allocIds = rawAllocIds.split(',').map(x => x.trim()).filter(x => x.length > 0);

                if (ids.length === 0 && allocIds.length === 0) return;

                if (confirm(`Are you sure you want to unassign all pilgrims from Room ${roomNo} and move them to Pending Room Allocation?`)) {
                    const unassignInputs = document.getElementById('unassignHiddenInputs');
                    unassignInputs.innerHTML = '';
                    allocIds.forEach(aId => {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = 'allocation_ids[]';
                        inp.value = aId;
                        unassignInputs.appendChild(inp);
                    });
                    ids.forEach(id => {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = 'person_ids[]';
                        inp.value = id;
                        unassignInputs.appendChild(inp);
                    });
                    if (hotelName) {
                        const inpH = document.createElement('input');
                        inpH.type = 'hidden';
                        inpH.name = 'hotel_name';
                        inpH.value = hotelName;
                        unassignInputs.appendChild(inpH);
                    }
                    if (roomNo) {
                        const inpR = document.createElement('input');
                        inpR.type = 'hidden';
                        inpR.name = 'room_number';
                        inpR.value = roomNo;
                        unassignInputs.appendChild(inpR);
                    }
                    if (location) {
                        const inpL = document.createElement('input');
                        inpL.type = 'hidden';
                        inpL.name = 'location';
                        inpL.value = location;
                        unassignInputs.appendChild(inpL);
                    }
                    document.getElementById('bulkUnassignForm').submit();
                }
            });
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

        // ==========================================
        // DYNAMIC COLUMN VISIBILITY CONTROLLER
        // ==========================================
        const colCheckboxes = document.querySelectorAll('.col-toggle-checkbox');
        const defaultVisibleCols = ['sr', 'picture', 'hajj-id', 'hb', 'name', 'passport', 'room-typ', 'room-no', 'gender'];
        const storageKey = 'rooming_manifest_columns_pref_v5';

        // Purge any corrupted old storage keys
        try {
            localStorage.removeItem('rooming_manifest_columns_pref_v4');
            localStorage.removeItem('rooming_manifest_columns_pref_v3');
        } catch(e) {}

        function applyColumnVisibility(colName, isVisible) {
            const cells = document.querySelectorAll(`.col-${colName}`);
            cells.forEach(el => {
                if (isVisible) {
                    el.classList.remove('col-hidden');
                    el.style.display = '';
                } else {
                    el.classList.add('col-hidden');
                    el.style.display = 'none';
                }
            });
        }

        function getActiveColumnsString() {
            const activeCols = [];
            colCheckboxes.forEach(cb => {
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

        // Live click listener to guarantee latest active columns at moment of download click
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

        function saveColumnPreferences() {
            const prefs = {};
            colCheckboxes.forEach(cb => {
                const col = cb.getAttribute('data-col');
                prefs[col] = cb.checked;
            });
            try {
                localStorage.setItem(storageKey, JSON.stringify(prefs));
            } catch(e) {}
            updateExportLinksWithColumns();
        }

        function loadColumnPreferences() {
            try {
                const saved = localStorage.getItem(storageKey);
                if (saved) {
                    const prefs = JSON.parse(saved);
                    // Critical safety check: ensure core columns are not all false!
                    const hasVisibleCore = ['name', 'sr', 'passport', 'room-no', 'room-typ'].some(c => prefs[c] === true);
                    if (hasVisibleCore) {
                        colCheckboxes.forEach(cb => {
                            const col = cb.getAttribute('data-col');
                            if (typeof prefs[col] !== 'undefined') {
                                cb.checked = !!prefs[col];
                                applyColumnVisibility(col, !!prefs[col]);
                            }
                        });
                        updateExportLinksWithColumns();
                        return;
                    }
                }
            } catch(e) {}

            // By default: ONLY show the original default columns, keep extra columns unchecked & hidden
            colCheckboxes.forEach(cb => {
                const col = cb.getAttribute('data-col');
                const isDefault = defaultVisibleCols.includes(col);
                cb.checked = isDefault;
                applyColumnVisibility(col, isDefault);
            });
            updateExportLinksWithColumns();
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
            updateExportLinksWithColumns();
        });

        // Load saved column preferences on page load
        loadColumnPreferences();
    });
</script>
@endsection

