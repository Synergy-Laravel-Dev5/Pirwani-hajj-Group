@extends('layout.master')
@section('title', 'Hotel Check-In Report & Manifest')

@section('content')
<style>
    /* Official Manifest Table Styling matching Rooming & Maktab Reports */
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
    .room-divider-row td {
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
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            {{-- Page Header & Actions --}}
            <div class="py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                <div>
                    <h4 class="fs-20 fw-bold m-0 text-dark">
                        <i class="mdi mdi-hotel text-primary me-2"></i>Official Hotel Check-In Report & Manifest
                    </h4>
                    <p class="text-muted mb-0 small">Official hotel check-in manifests, rooming schedules, daily arrivals, and guest allocations.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('report.check-in.export-excel', request()->query()) }}" 
                       class="btn btn-success btn-sm"
                       title="Download official hotel check-in report as Excel spreadsheet">
                        <i class="mdi mdi-file-excel me-1"></i> Download Excel
                    </a>
                    <a href="{{ route('report.check-in.export-pdf', request()->query()) }}" 
                       class="btn btn-danger btn-sm"
                       title="Download official hotel check-in report as PDF document">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-dark btn-sm">
                        <i class="mdi mdi-printer me-1"></i> Print Manifest
                    </button>
                    <a href="{{ route('report.check-in') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </div>

            {{-- Summary Ribbon / KPI Cards --}}
            <div class="row g-2 mb-3 no-print">
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Total Stays:</span>
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
                            <span class="text-muted small fw-semibold d-block">Today's Check-in:</span>
                            <strong class="fs-18 text-danger">{{ number_format($todayCheckins) }}</strong>
                        </div>
                        <span class="badge bg-danger text-white" style="font-size: 11px;">
                            {{ \Carbon\Carbon::today()->format('d M') }}
                        </span>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between shadow-sm">
                        <div>
                            <span class="text-muted small fw-semibold d-block">Next 7 Days:</span>
                            <strong class="fs-18 text-warning">{{ number_format($upcoming7Days) }}</strong>
                        </div>
                        <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning">
                            <i class="mdi mdi-calendar-clock fs-20"></i>
                        </div>
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
                            <span class="text-muted small fw-semibold d-block">Report Sync:</span>
                            <strong class="fs-14 text-dark">{{ now()->format('d M, h:i A') }}</strong>
                        </div>
                        <span class="text-success small fw-bold"><i class="mdi mdi-check-decagram"></i> Live</span>
                    </div>
                </div>
            </div>

            {{-- Filter Card --}}
            <div class="card shadow-sm border-0 mb-3 no-print">
                <div class="card-header bg-white py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="m-0 fw-bold text-dark fs-14">
                            <i class="mdi mdi-filter-variant me-1 text-primary"></i> Filter Check-In Criteria
                        </h6>
                        @if (!empty($fromDate) || !empty($toDate) || !empty($hotelFilter) || (!empty($locationFilter) && $locationFilter !== 'all') || !empty($search) || !empty($packageFilter) || (!empty($maktabFilter) && $maktabFilter !== 'all'))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Filtered</span>
                        @endif
                    </div>
                    {{-- Quick Filter Pills --}}
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'all', 'from_date' => null, 'to_date' => null])) }}" 
                           class="btn {{ ($quickFilter == 'all' || empty($quickFilter)) && empty($fromDate) ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                        <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'today', 'from_date' => null, 'to_date' => null])) }}" 
                           class="btn {{ $quickFilter == 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">Today</a>
                        <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'tomorrow', 'from_date' => null, 'to_date' => null])) }}" 
                           class="btn {{ $quickFilter == 'tomorrow' ? 'btn-primary' : 'btn-outline-secondary' }}">Tomorrow</a>
                        <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'this_week', 'from_date' => null, 'to_date' => null])) }}" 
                           class="btn {{ $quickFilter == 'this_week' ? 'btn-primary' : 'btn-outline-secondary' }}">Next 7 Days</a>
                        <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'this_month', 'from_date' => null, 'to_date' => null])) }}" 
                           class="btn {{ $quickFilter == 'this_month' ? 'btn-primary' : 'btn-outline-secondary' }}">This Month</a>
                        <a href="{{ route('report.check-in', array_merge(request()->query(), ['quick_filter' => 'upcoming', 'from_date' => null, 'to_date' => null])) }}" 
                           class="btn {{ $quickFilter == 'upcoming' ? 'btn-primary' : 'btn-outline-secondary' }}">All Upcoming</a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.check-in') }}" id="checkinFilterForm">
                        <div class="row g-2 align-items-end">
                            {{-- Hotel Name --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Hotel Name</label>
                                <input type="text" name="hotel_name" class="form-control form-control-sm" list="hotelList" 
                                       placeholder="Type or choose hotel..." value="{{ $hotelFilter }}">
                                <datalist id="hotelList">
                                    @foreach ($registeredHotels as $rh)
                                        <option value="{{ $rh->name }}">{{ $rh->place ? ' (' . ucfirst($rh->place) . ')' : '' }}</option>
                                    @endforeach
                                    @foreach ($distinctHotelNames as $dName)
                                        <option value="{{ $dName }}"></option>
                                    @endforeach
                                </datalist>
                            </div>

                            {{-- City / Location --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">City / Location</label>
                                <select name="location" class="form-select form-select-sm">
                                    <option value="all" {{ ($locationFilter == 'all' || empty($locationFilter)) ? 'selected' : '' }}>All Locations</option>
                                    <option value="makkah" {{ $locationFilter == 'makkah' ? 'selected' : '' }}>🕋 Makkah</option>
                                    <option value="madinah" {{ $locationFilter == 'madinah' ? 'selected' : '' }}>🕌 Madinah</option>
                                    <option value="azizia" {{ $locationFilter == 'azizia' ? 'selected' : '' }}>🏢 Azizia</option>
                                    <option value="jeddah" {{ $locationFilter == 'jeddah' ? 'selected' : '' }}>🌊 Jeddah</option>
                                    <option value="mina" {{ $locationFilter == 'mina' ? 'selected' : '' }}>⛺ Mina</option>
                                </select>
                            </div>

                            {{-- Check-In Date From --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Check-In From</label>
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                            </div>

                            {{-- Check-In Date To --}}
                            <div class="col-xl-2 col-md-3 col-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Check-In To</label>
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                            </div>

                            {{-- Package --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Package</label>
                                <select name="package_id" class="form-select form-select-sm">
                                    <option value="">-- All Packages --</option>
                                    @foreach ($packages as $pkg)
                                        @php
                                            $pkgCode = $pkg->code ?? $pkg->package_number ?? '';
                                            $pkgTitle = $pkg->package_title ?: ($pkg->name ?: 'Package #' . $pkg->id);
                                            $displayText = $pkgCode ? $pkgTitle . ' (' . $pkgCode . ')' : $pkgTitle;
                                        @endphp
                                        <option value="{{ $pkg->id }}" {{ $packageFilter == $pkg->id ? 'selected' : '' }}>
                                            {{ $displayText }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Maktab / Camp Category Filter --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Maktab / Camp</label>
                                <select name="maktab" class="form-select form-select-sm">
                                    <option value="all" {{ ($maktabFilter == 'all' || empty($maktabFilter)) ? 'selected' : '' }}>All Maktabs / Camps</option>
                                    <option value="maktab_a" {{ $maktabFilter == 'maktab_a' ? 'selected' : '' }}>⛺ Maktab A (VIP)</option>
                                    <option value="maktab_c" {{ $maktabFilter == 'maktab_c' ? 'selected' : '' }}>⛺ Maktab C (Standard)</option>
                                    <option value="combo" {{ $maktabFilter == 'combo' ? 'selected' : '' }}>⛺ Maktab A & C (Combo)</option>
                                </select>
                            </div>

                            {{-- Search keyword --}}
                            <div class="col-xl-6 col-md-5 col-sm-12">
                                <label class="form-label small fw-semibold text-muted mb-1">Search Pilgrim / Passport / HB / Voucher</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-magnify text-muted"></i></span>
                                    <input type="text" name="search" class="form-control form-control-sm" 
                                           placeholder="Search Pilgrim Name, Passport #, CNIC, Phone, Haji ID, Voucher (UB...)..." 
                                           value="{{ $search }}">
                                </div>
                            </div>

                            {{-- Filter Action Buttons --}}
                            <div class="col-xl-3 col-md-3 col-sm-12 d-flex gap-2">
                                <button type="submit" class="btn btn-sm fw-semibold flex-grow-1 text-white shadow-sm" style="background-color: #071527; border-color: #071527;">
                                    <i class="mdi mdi-filter me-1"></i> Apply Filter
                                </button>
                                <a href="{{ route('report.check-in') }}" class="btn btn-outline-secondary btn-sm px-3">
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
                        <span class="badge bg-primary text-white">{{ $hotelStays->count() }} Stays Listed</span>
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
                                    <input type="checkbox" class="col-toggle-checkbox form-check-input mt-0" data-col="maktab" checked>
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
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="manifest-table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="col-sr">SR</th>
                                    <th style="width: 75px;" class="col-picture">PICTURE</th>
                                    <th style="width: 105px;" class="col-hajj-id">HAJJ ID</th>
                                    <th style="width: 90px;" class="col-hb">HB</th>
                                    <th style="width: 220px; text-align: left; padding-left: 10px;" class="col-pilgrim">PILGRIM FULL NAME</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-given-name col-hidden">GIVEN NAME</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-surname col-hidden">SURNAME</th>
                                    <th style="width: 150px; text-align: left; padding-left: 10px;" class="col-father-name col-hidden">HUSBAND / FATHER NAME</th>
                                    <th style="width: 80px;" class="col-gender">GENDER</th>
                                    <th style="width: 115px;" class="col-passport">PASSPORT</th>
                                    <th style="width: 110px;" class="col-passport-doi col-hidden">DATE OF ISSUE</th>
                                    <th style="width: 115px;" class="col-passport-exp col-hidden">EXPIRY DATE</th>
                                    <th style="width: 180px; text-align: left; padding-left: 10px;" class="col-hotel">HOTEL & LOCATION</th>
                                    <th style="width: 120px;" class="col-room">ROOM DETAILS</th>
                                    <th style="width: 100px;" class="col-room-no">ROOM NO</th>
                                    <th style="width: 120px;" class="col-checkin">CHECK-IN</th>
                                    <th style="width: 120px;" class="col-checkout">CHECK-OUT</th>
                                    <th style="width: 75px;" class="col-nights">NIGHTS</th>
                                    <th style="width: 125px;" class="col-maktab">MAKTAB / CAMP</th>
                                    <th style="width: 150px; text-align: left; padding-left: 10px;" class="col-package">PACKAGE</th>
                                    <th style="width: 110px;" class="col-voucher">VOUCHER #</th>
                                    <th style="width: 140px; text-align: left; padding-left: 10px;" class="col-client col-hidden">CLIENT / GROUP</th>
                                    <th style="width: 110px;" class="col-status">STATUS</th>
                                    <th style="width: 80px;" class="col-action no-print">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $globalSr = 1; @endphp
                                @forelse ($hotelStays as $idx => $stay)
                                    @php
                                        $booking = $stay->booking;
                                        $persons = $booking ? $booking->persons : collect([]);
                                        $totalPax = $persons->count();
                                        $rowspan = max(1, $totalPax);

                                        $checkIn = $stay->check_in ? \Carbon\Carbon::parse($stay->check_in) : null;
                                        $checkOut = $stay->check_out ? \Carbon\Carbon::parse($stay->check_out) : null;
                                        $nights = ($checkIn && $checkOut) ? $checkIn->diffInDays($checkOut) : '—';

                                        // Status
                                        $statusText = 'Scheduled';
                                        $statusBadge = 'bg-secondary text-white';
                                        if ($checkIn) {
                                            if ($checkIn->isToday()) {
                                                $statusText = 'Checking In';
                                                $statusBadge = 'bg-danger text-white animate-pulse';
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

                                    @if($totalPax === 0)
                                        {{-- Row without specific persons --}}
                                        <tr class="room-divider-row">
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
                                                <div class="small text-muted mt-1">{{ $stay->no_of_rooms ?: 1 }} Room(s)</div>
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
                                                <small class="text-muted">{{ $checkIn ? $checkIn->format('l') : '' }}</small>
                                            </td>

                                            {{-- Check-Out --}}
                                            <td class="text-center col-checkout">
                                                <div class="fw-bold text-dark">{{ $checkOut ? $checkOut->format('d M Y') : '—' }}</div>
                                                <small class="text-muted">{{ $checkOut ? $checkOut->format('l') : '' }}</small>
                                            </td>

                                            {{-- Nights --}}
                                            <td class="text-center col-nights">
                                                <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-12">{{ $nights }} N</span>
                                            </td>

                                            {{-- Maktab --}}
                                            <td class="text-center col-maktab">
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
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" style="font-size: 11px;">
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
                                            <td style="padding-left: 10px;" class="col-client col-hidden text-truncate" style="max-width: 140px;" title="{{ $booking?->client?->name ?? ($booking?->company?->company_name ?? '—') }}">
                                                <span class="fw-semibold text-dark">{{ $booking?->client?->name ?? ($booking?->company?->company_name ?? '—') }}</span>
                                            </td>

                                            {{-- Status --}}
                                            <td class="text-center col-status">
                                                <span class="badge {{ $statusBadge }} px-2 py-1" style="font-size: 11px;">{{ $statusText }}</span>
                                            </td>

                                            {{-- Action --}}
                                            <td class="text-center col-action no-print">
                                                @if($booking)
                                                    <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" title="View Booking Voucher">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Multiple Pilgrims Loop --}}
                                        @foreach($persons as $oIdx => $person)
                                            <tr class="{{ $oIdx === 0 ? 'room-divider-row' : '' }}">
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

                                                {{-- Pilgrim Full Name --}}
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

                                                <td class="text-center col-passport-doi col-hidden small">
                                                    {{ !empty($person->date_of_issue) ? date('d/m/Y', strtotime($person->date_of_issue)) : '—' }}
                                                </td>
                                                <td class="text-center col-passport-exp col-hidden small">
                                                    {{ !empty($person->passport_expiry_date) ? date('d/m/Y', strtotime($person->passport_expiry_date)) : '—' }}
                                                </td>

                                                {{-- Shared Stay Columns with Rowspan --}}
                                                @if($oIdx === 0)
                                                    {{-- Hotel & Location --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 10px;" class="col-hotel">
                                                        <strong class="text-dark d-block fs-14">{{ strtoupper($stay->hotel_name ?: 'Hotel Not Specified') }}</strong>
                                                        <span class="badge {{ $locBadgeClass }} px-2 py-0.5 mt-1" style="font-size: 11px; font-weight: 700;">
                                                            {{ strtoupper($stay->location ?: 'Location') }}
                                                        </span>
                                                    </td>

                                                    {{-- Room Details --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-room">
                                                        <span class="badge bg-dark text-white px-2 py-1" style="font-size: 11.5px; font-weight: 700;">
                                                            {{ ucfirst($stay->room_type ?: 'Standard') }}
                                                        </span>
                                                        <div class="small text-muted mt-1 fw-semibold">{{ $stay->no_of_rooms ?: 1 }} Room(s)</div>
                                                    </td>

                                                    {{-- Room No --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center fw-bold col-room-no">
                                                        @if(!empty($stay->room_number))
                                                            <span class="badge bg-danger text-white px-2 py-1 fs-13 font-monospace fw-bold">{{ $stay->room_number }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Check-In --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-checkin">
                                                        <div class="fw-bold text-dark" style="font-size: 13.5px;">{{ $checkIn ? $checkIn->format('d M Y') : '—' }}</div>
                                                        <small class="text-muted fw-semibold">{{ $checkIn ? $checkIn->format('l') : '' }}</small>
                                                    </td>

                                                    {{-- Check-Out --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-checkout">
                                                        <div class="fw-bold text-dark" style="font-size: 13.5px;">{{ $checkOut ? $checkOut->format('d M Y') : '—' }}</div>
                                                        <small class="text-muted fw-semibold">{{ $checkOut ? $checkOut->format('l') : '' }}</small>
                                                    </td>

                                                    {{-- Nights --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-nights">
                                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-12">{{ $nights }} N</span>
                                                    </td>

                                                    {{-- Maktab --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-maktab">
                                                        @if($campVal)
                                                            <span class="badge bg-warning text-dark px-2 py-1 fw-bold" style="font-size: 11.5px;">
                                                                <i class="mdi mdi-tent me-0.5"></i>{{ $campVal }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Package --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 10px;" class="col-package">
                                                        @if ($booking?->package)
                                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fw-bold" style="font-size: 11px;">
                                                                {{ $booking->package->package_title ?: ($booking->package->name ?: 'Package #' . $booking->package->id) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Voucher # --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center fw-bold col-voucher">
                                                        <a href="{{ route('booking.voucher', $booking->id ?? 0) }}" target="_blank" class="badge bg-primary text-white text-decoration-none px-2 py-1 font-monospace" style="font-size: 11.5px;">
                                                            {{ $booking->voucher_number ?? 'UB' . str_pad($booking->id ?? 0, 6, '0', STR_PAD_LEFT) }}
                                                        </a>
                                                    </td>

                                                    {{-- Client / Group --}}
                                                    <td rowspan="{{ $rowspan }}" style="padding-left: 10px;" class="col-client col-hidden text-truncate" style="max-width: 140px;" title="{{ $booking?->client?->name ?? ($booking?->company?->company_name ?? '—') }}">
                                                        <span class="fw-semibold text-dark">{{ $booking?->client?->name ?? ($booking?->company?->company_name ?? '—') }}</span>
                                                    </td>

                                                    {{-- Status --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-status">
                                                        <span class="badge {{ $statusBadge }} px-2 py-1 fw-bold" style="font-size: 11px;">{{ $statusText }}</span>
                                                    </td>

                                                    {{-- Action --}}
                                                    <td rowspan="{{ $rowspan }}" class="text-center col-action no-print">
                                                        @if($booking)
                                                            <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" title="View Booking Voucher">
                                                                <i class="mdi mdi-eye"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="24" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-hotel fs-48 d-block mb-2 text-muted"></i>
                                            <h5>No Check-In Records Found</h5>
                                            <p>Try clearing filters or date ranges above to view all records.</p>
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
        const storageKey = 'checkin_report_columns_visibility_v1';
        const defaultHiddenCols = ['given-name', 'surname', 'father-name', 'passport-doi', 'passport-exp', 'client'];

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

        function saveColumnState(state) {
            try {
                localStorage.setItem(storageKey, JSON.stringify(state));
            } catch (e) {
                console.error('Failed to save column settings to localStorage', e);
            }
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
