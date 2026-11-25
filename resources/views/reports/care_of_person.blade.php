@extends('layout.master')
@section('title', 'Care of Persons Report & Booking Details')

@section('content')
    <style>
        /* Official Report Styling */
        .careof-table {
            border-collapse: collapse;
            width: 100%;
            background-color: #ffffff;
            font-family: Arial, sans-serif;
        }

        .careof-table th {
            background-color: #f8fafc;
            color: #000;
            font-weight: 800;
            font-size: 12.5px;
            text-align: center;
            vertical-align: middle;
            padding: 8px 6px;
            border: 1.5px solid #000000 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .careof-table td {
            padding: 7px 6px;
            vertical-align: middle;
            font-size: 12.5px;
            border: 1px solid #000000 !important;
        }

        .careof-banner {
            background: linear-gradient(135deg, #071527 0%, #0E233E 55%, #15325B 100%);
            color: #F3DC9B;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(7, 21, 39, 0.15);
        }

        .careof-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .booking-subcard {
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 18px;
            background: #fff;
            overflow: hidden;
        }

        .booking-subhead {
            background-color: #f1f5f9;
            border-bottom: 1.5px solid #cbd5e1;
            padding: 9px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .haji-photo {
            width: 44px;
            height: 48px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #999;
            display: block;
            margin: 0 auto;
        }

        .haji-photo-placeholder {
            width: 44px;
            height: 48px;
            background-color: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #64748b;
            font-weight: bold;
            font-size: 13px;
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

            .no-print,
            .left-side-menu,
            .navbar-custom,
            .footer {
                display: none !important;
            }

            .content-page {
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                background-color: #fff !important;
                font-size: 10px;
            }

            .careof-table th,
            .careof-table td {
                border: 1px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .careof-banner {
                background: #071527 !important;
                color: #F3DC9B !important;
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
                            <i class="mdi mdi-account-switch text-primary me-2"></i>Care of Persons Report & Booking Manifest
                        </h4>
                        <p class="text-muted mb-0 small">Official breakdown of bookings, pilgrims, packages, and accounting
                            details organized by Care of person / agent.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('report.care-of-person.export-excel', request()->query()) }}"
                            class="btn btn-success btn-sm" id="btnExportExcel" title="Download Care of report as Excel">
                            <i class="mdi mdi-file-excel me-1"></i> Download Excel
                        </a>
                        <a href="{{ route('report.care-of-person.export-pdf', request()->query()) }}"
                            class="btn btn-danger btn-sm" id="btnExportPdf" title="Download Care of report as PDF">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                        </a>
                        <button onclick="window.print()" class="btn btn-dark btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Print Manifest
                        </button>
                        <a href="{{ route('report.care-of-person') }}" class="btn btn-outline-secondary btn-sm"
                            title="Reset Filters">
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="card mb-3 no-print shadow-sm">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('report.care-of-person') }}" id="filterForm">
                            <div class="row g-2 align-items-end">

                                {{-- Search --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted mb-1">Search Keyword</label>
                                    <div class="input-group input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-magnify"></i></span>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Care Of, Name, Passport, Hajj ID, HB #, Phone..."
                                            value="{{ $search ?? '' }}">
                                    </div>
                                </div>

                                {{-- Care Of Dropdown --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted mb-1">Care Of Person /
                                        Agent</label>
                                    <select name="care_of" id="filterCareOf"
                                        class="form-select form-select select2-searchable"
                                        data-placeholder="-- All Care Of Persons --">
                                        <option value="all">-- All Care Of Persons ({{ $distinctCareOfList->count() }})
                                            --</option>
                                        @foreach ($distinctCareOfList as $co)
                                            <option value="{{ $co }}"
                                                {{ strtoupper(trim($selectedCareOf ?? '')) == strtoupper(trim($co)) ? 'selected' : '' }}>
                                                {{ $co }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Package Dropdown --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted mb-1">Package</label>
                                    <select name="package_id" id="filterPackage"
                                        class="form-select form-select select2-searchable"
                                        data-placeholder="-- All Packages --">
                                        <option value="all">-- All Packages --</option>
                                        @foreach ($packages as $pkg)
                                            <option value="{{ $pkg->id }}"
                                                {{ $packageId == $pkg->id ? 'selected' : '' }}>
                                                {{ $pkg->package_title ?: $pkg->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Gender Filter --}}
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted mb-1">Gender</label>
                                    <select name="gender" id="filterGender"
                                        class="form-select form-select select2-searchable" data-placeholder="All Genders">
                                        <option value="all" {{ $gender == 'all' || empty($gender) ? 'selected' : '' }}>
                                            All Genders
                                        </option>
                                        <option value="male" {{ $gender == 'male' ? 'selected' : '' }}>Male Only
                                        </option>
                                        <option value="female" {{ $gender == 'female' ? 'selected' : '' }}>Female Only
                                        </option>
                                    </select>
                                </div>

                                {{-- Actions & Column Selector --}}
                                <div class="col-md-3 d-flex gap-1">
                                    <button type="submit" class="btn btn-primary btn flex-grow-1">
                                        <i class="mdi mdi-filter me-1"></i> Apply
                                    </button>

                                    <div class="dropdown">
                                        <button class="btn btn-outline-dark btn dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false" title="Show/Hide Columns">
                                            <i class="mdi mdi-view-column-outline"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end column-filter-menu"
                                            onclick="event.stopPropagation()">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                <span class="fw-bold small text-dark">Toggle Columns</span>
                                                <div>
                                                    <button type="button"
                                                        class="btn btn-link btn-xs p-0 text-primary text-decoration-none me-2"
                                                        id="selectAllCols">All</button>
                                                    <button type="button"
                                                        class="btn btn-link btn-xs p-0 text-muted text-decoration-none"
                                                        id="resetCols">Reset</button>
                                                </div>
                                            </div>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="sr" checked> <span># Number</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="picture" checked> <span>Photo</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="hajj-id" checked> <span>Hajj ID</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="hb" checked> <span>HB #</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="name" checked> <span>Pilgrim Name</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="gender" checked> <span>Gender</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="passport" checked> <span>Passport</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="doi" checked> <span>Issue Date</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="expiry" checked> <span>Expiry Date</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="cnic" checked> <span>CNIC</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="phone" checked> <span>Phone</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="booking-ref" checked> <span>Booking Ref /
                                                    Client</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="package" checked> <span>Package</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="room" checked> <span>Room Allotment</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="hotel" checked> <span>Hotel</span></label>
                                            <label class="column-filter-item"><input type="checkbox" class="col-toggle"
                                                    data-col="camp" checked> <span>Maktab / Camp</span></label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                {{-- REPORT CONTENT: GROUPED BY CARE OF (SINGLE UNIFIED TABLE PER CARE OF) --}}
                @if ($groupedByCareOf->isEmpty())
                    <div class="card shadow-sm text-center py-5">
                        <div class="card-body">
                            <i class="mdi mdi-folder-search-outline text-muted" style="font-size: 54px;"></i>
                            <h5 class="mt-3 text-dark">No Care of Person records found</h5>
                            <p class="text-muted small">No bookings or pilgrims match the specified Care of person,
                                package, gender, or search keyword.</p>
                            <a href="{{ route('report.care-of-person') }}" class="btn btn-outline-primary btn-sm">Clear
                                All Filters</a>
                        </div>
                    </div>
                @else
                    @foreach ($groupedByCareOf as $careOfName => $bookings)
                        @php
                            // Flatten all persons across all bookings for this Care Of into one continuous list
                            $allGroupPersons = $bookings->flatMap(function ($b) {
                                return $b->persons->map(function ($p) use ($b) {
                                    $p->parent_booking = $b;
                                    return $p;
                                });
                            });
                            $groupTotalPax = $allGroupPersons->count();
                            $groupTotalAmount = $bookings->sum('total_amount');
                            $groupTotalReceived = $bookings->sum('total_received');
                            $groupTotalBalance = $bookings->sum('balance');
                        @endphp

                        {{-- Care Of Group Container --}}
                        <div class="card shadow-sm mb-4 border" style="border-radius: 8px; overflow: hidden;">
                            {{-- Group Header Banner --}}
                            <div class="careof-banner" style="border-radius: 0; margin-bottom: 0;">
                                <div class="careof-title">
                                    <i class="mdi mdi-account-tie fs-20 text-warning"></i>
                                    <span>CARE OF: <strong>{{ strtoupper($careOfName) }}</strong></span>
                                    <span class="badge bg-warning text-dark ms-2 fs-12">{{ $bookings->count() }}
                                        {{ Str::plural('Booking', $bookings->count()) }}</span>
                                    <span class="badge bg-light text-dark ms-1 fs-12">{{ $groupTotalPax }}
                                        {{ Str::plural('Pilgrim', $groupTotalPax) }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 fs-13 flex-wrap">
                                    <div>Total Amount: <strong>PKR {{ number_format($groupTotalAmount) }}</strong></div>
                                    <div>Received: <strong class="text-success">PKR
                                            {{ number_format($groupTotalReceived) }}</strong></div>
                                    <div>Balance: <strong
                                            class="{{ $groupTotalBalance > 0 ? 'text-danger' : 'text-success' }}">PKR
                                            {{ number_format($groupTotalBalance) }}</strong></div>
                                </div>
                            </div>

                            {{-- Unified Single Table for All Pilgrims under this Care Of --}}
                            <div class="table-responsive">
                                <table class="careof-table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="col-sr" style="width: 35px;">#</th>
                                            <th class="col-picture" style="width: 50px;">PHOTO</th>
                                            <th class="col-hajj-id" style="width: 80px;">HAJJ ID</th>
                                            <th class="col-hb" style="width: 75px;">HB #</th>
                                            <th class="col-name" style="min-width: 170px;">PILGRIM NAME</th>
                                            <th class="col-gender" style="width: 70px;">GENDER</th>
                                            <th class="col-passport" style="width: 100px;">PASSPORT NO</th>
                                            <th class="col-doi" style="width: 85px;">DOI</th>
                                            <th class="col-expiry" style="width: 85px;">EXPIRY</th>
                                            <th class="col-cnic" style="width: 120px;">CNIC</th>
                                            <th class="col-phone" style="width: 105px;">PHONE</th>
                                            <th class="col-booking-ref" style="min-width: 150px;">BOOKING REF / CLIENT
                                            </th>
                                            <th class="col-package" style="min-width: 120px;">PACKAGE</th>
                                            <th class="col-room" style="min-width: 120px;">ROOM ALLOTMENT</th>
                                            <th class="col-hotel" style="min-width: 120px;">HOTEL</th>
                                            <th class="col-camp" style="min-width: 110px;">CAMP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($allGroupPersons as $person)
                                            @php
                                                $booking = $person->parent_booking;
                                                $pGender = strtoupper($person->gender ?? 'MALE');
                                                $genderClass = $pGender === 'FEMALE' ? 'gender-female' : 'gender-male';
                                                $hbFormatted = \App\Models\BookingPerson::formatHbNumber(
                                                    $person->hb_number,
                                                );
                                                $doi = $person->date_of_issue ?: ($person->passport_issue_date ?: '—');
                                                $roomInfo =
                                                    ($person->room_number ? 'Room ' . $person->room_number : '') .
                                                    ($person->room_type ? ' (' . $person->room_type . ')' : '');

                                                $clientName = $booking
                                                    ? ($booking->booking_for === 'company'
                                                        ? $booking->company->company_name ?? 'N/A'
                                                        : $booking->client->name ?? 'N/A')
                                                    : '—';
                                                $voucherNo = $booking
                                                    ? ($booking->voucher_number ?:
                                                    'UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT))
                                                    : '—';
                                                $pkgName = $booking
                                                    ? $booking->package->package_title ??
                                                        ($booking->package->name ??
                                                            ($booking->package_name ??
                                                                ucfirst($booking->package_type ?? 'N/A')))
                                                    : '—';
                                                $campName = $booking ? ($booking->camp ?: '—') : '—';
                                            @endphp
                                            <tr>
                                                <td class="col-sr text-center fw-bold">{{ $loop->iteration }}</td>

                                                <td class="col-picture text-center">
                                                    @if (!empty($person->photo) && !empty($person->photo_exists))
                                                        <img src="{{ asset($person->photo) }}" class="haji-photo"
                                                            alt="Photo" width="44" height="48" loading="lazy" decoding="async"
                                                            onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                                        <div class="haji-photo-placeholder" style="display: none;">
                                                            <i class="mdi mdi-account"></i>
                                                        </div>
                                                    @else
                                                        <div class="haji-photo-placeholder">
                                                            <i class="mdi mdi-account"></i>
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="col-hajj-id text-center">
                                                    @if ($person->hajj_id)
                                                        <span class="badge bg-dark text-white fw-bold px-2 py-1"
                                                            style="font-size:11px;">
                                                            {{ $person->hajj_id }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                <td class="col-hb text-center">
                                                    @if ($hbFormatted)
                                                        <span class="badge bg-primary text-white fw-bold px-2 py-1"
                                                            style="font-size:11px;">
                                                            {{ $hbFormatted }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                <td class="col-name">
                                                    <strong
                                                        class="text-dark d-block">{{ $person->full_name ?: $person->given_name . ' ' . $person->surname }}</strong>
                                                    @if ($person->father_name)
                                                        @php
                                                            $pGenderLower = strtolower($person->gender ?? '');
                                                            $relationLabel =
                                                                $pGenderLower === 'female' ? 'D/O, W/O' : 'S/O';
                                                        @endphp
                                                        <span class="text-muted small">{{ $relationLabel }}:
                                                            {{ $person->father_name }}</span>
                                                    @endif
                                                </td>

                                                <td class="col-gender text-center">
                                                    <span class="{{ $genderClass }}">
                                                        {{ strtoupper($person->gender ?? '—') }}
                                                    </span>
                                                </td>

                                                <td class="col-passport text-center">
                                                    @if ($person->passport_number)
                                                        <strong class="text-dark">{{ $person->passport_number }}</strong>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                <td class="col-doi text-center">
                                                    {{ $doi }}
                                                </td>

                                                <td class="col-expiry text-center">
                                                    {{ $person->passport_expiry_date ?: '—' }}
                                                </td>

                                                <td class="col-cnic text-center">
                                                    {{ $person->cnic ?: '—' }}
                                                </td>

                                                <td class="col-phone text-center">
                                                    {{ $person->phone ?: $booking->phone ?? '—' }}
                                                </td>

                                                <td class="col-booking-ref">
                                                    @if ($booking)
                                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                                            <span class="badge bg-dark text-white fw-bold"
                                                                style="font-size: 10.5px;">{{ $voucherNo }}</span>
                                                            <span
                                                                class="fw-semibold text-dark small">{{ $clientName }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                <td class="col-package text-center">
                                                    <span
                                                        class="badge bg-primary-subtle text-primary border border-primary-subtle"
                                                        style="font-size: 11px;">
                                                        {{ $pkgName }}
                                                    </span>
                                                </td>

                                                <td class="col-room text-center">
                                                    @if ($roomInfo)
                                                        <span
                                                            class="badge bg-light text-dark border">{{ $roomInfo }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                <td class="col-hotel text-center">
                                                    {{ $person->hotel_name ?: '—' }}
                                                </td>

                                                <td class="col-camp text-center">
                                                    {{ $campName }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="16" class="text-center py-4 text-muted">
                                                    No pilgrims found for this Care of person.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize searchable Select2 dropdowns
            if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                $('.select2-searchable').each(function() {
                    const $el = $(this);
                    $el.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: $el.data('placeholder') || 'Select an option',
                        allowClear: false
                    });
                });
            }

            const STORAGE_KEY = 'care_of_report_visible_columns';
            const toggles = document.querySelectorAll('.col-toggle');

            function getActiveColumns() {
                const visibleCols = [];
                toggles.forEach(t => {
                    if (t.checked) visibleCols.push(t.dataset.col);
                });
                return visibleCols;
            }

            function applyColumnVisibility(colKey, isVisible) {
                document.querySelectorAll('.col-' + colKey).forEach(el => {
                    if (isVisible) {
                        el.classList.remove('col-hidden');
                    } else {
                        el.classList.add('col-hidden');
                    }
                });
            }

            function savePreferences() {
                const visibleCols = getActiveColumns();
                localStorage.setItem(STORAGE_KEY, JSON.stringify(visibleCols));
                updateExportLinks(visibleCols);
            }

            function updateExportLinks(visibleCols) {
                const pdfBtn = document.getElementById('btnExportPdf');
                const excelBtn = document.getElementById('btnExportExcel');
                const colParam = visibleCols.length === 0 ? 'none' : visibleCols.join(',');

                if (pdfBtn) {
                    const u = new URL(pdfBtn.href, window.location.origin);
                    u.searchParams.set('columns', colParam);
                    pdfBtn.href = u.toString();
                }
                if (excelBtn) {
                    const u = new URL(excelBtn.href, window.location.origin);
                    u.searchParams.set('columns', colParam);
                    excelBtn.href = u.toString();
                }
            }

            let saved = null;
            try {
                saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
            } catch (e) {}

            if (Array.isArray(saved)) {
                toggles.forEach(t => {
                    const col = t.dataset.col;
                    const isVisible = saved.includes(col);
                    t.checked = isVisible;
                    applyColumnVisibility(col, isVisible);
                });
                updateExportLinks(saved);
            } else {
                const initial = getActiveColumns();
                updateExportLinks(initial);
            }

            toggles.forEach(t => {
                t.addEventListener('change', function() {
                    applyColumnVisibility(this.dataset.col, this.checked);
                    savePreferences();
                });
            });

            document.getElementById('selectAllCols')?.addEventListener('click', function(e) {
                e.preventDefault();
                toggles.forEach(t => {
                    t.checked = true;
                    applyColumnVisibility(t.dataset.col, true);
                });
                savePreferences();
            });

            document.getElementById('resetCols')?.addEventListener('click', function(e) {
                e.preventDefault();
                localStorage.removeItem(STORAGE_KEY);
                toggles.forEach(t => {
                    t.checked = true;
                    applyColumnVisibility(t.dataset.col, true);
                });
                savePreferences();
            });

            // ==========================================
            // DOWNLOAD PROGRESS LOADER MODAL (PDF / EXCEL)
            // ==========================================
            const downloadModalHtml = `
        <div class="modal fade" id="exportProgressModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <div class="avatar-lg mx-auto d-flex align-items-center justify-content-center rounded-circle" id="exportModalIconBg" style="width: 64px; height: 64px; background-color: #fee2e2;">
                                <i class="mdi mdi-file-pdf-box text-danger" id="exportModalIcon" style="font-size: 36px;"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-1" id="exportModalTitle">Generating Official Care Of PDF</h5>
                        <p class="text-muted small mb-3" id="exportModalSubtitle">Processing Care of groups, pilgrim records, and manifests...</p>
                        
                        <div class="progress mb-2" style="height: 12px; border-radius: 6px; background-color: #f1f5f9;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" id="exportProgressBar" role="progressbar" style="width: 15%;"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted px-1 mb-3">
                            <span id="exportProgressPhase">Compiling data & layout...</span>
                            <span class="fw-bold" id="exportProgressPercent">15%</span>
                        </div>

                        <div class="alert alert-light border small text-muted py-2 px-3 mb-0 text-start d-flex align-items-center gap-2">
                            <i class="mdi mdi-information-outline fs-16 text-primary"></i>
                            <span>Please keep this window open. Your file download will trigger automatically when ready.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
            document.body.insertAdjacentHTML('beforeend', downloadModalHtml);

            const exportModalEl = document.getElementById('exportProgressModal');
            const exportModal = (typeof bootstrap !== 'undefined' && bootstrap.Modal) ?
                new bootstrap.Modal(exportModalEl, {
                    backdrop: 'static',
                    keyboard: false
                }) :
                null;
            const exportModalTitle = document.getElementById('exportModalTitle');
            const exportModalSubtitle = document.getElementById('exportModalSubtitle');
            const exportModalIcon = document.getElementById('exportModalIcon');
            const exportModalIconBg = document.getElementById('exportModalIconBg');
            const exportProgressBar = document.getElementById('exportProgressBar');
            const exportProgressPercent = document.getElementById('exportProgressPercent');
            const exportProgressPhase = document.getElementById('exportProgressPhase');

            let exportProgressInterval = null;

            function startExportProgress(type) {
                clearInterval(exportProgressInterval);
                const isPdf = type === 'pdf';

                if (isPdf) {
                    exportModalTitle.textContent = 'Generating Care of Persons PDF';
                    exportModalSubtitle.textContent =
                        'Processing Care of groups, bookings, and pilgrim manifests...';
                    exportModalIcon.className = 'mdi mdi-file-pdf-box text-danger';
                    exportModalIconBg.style.backgroundColor = '#fee2e2';
                    exportProgressBar.className =
                        'progress-bar progress-bar-striped progress-bar-animated bg-danger';
                } else {
                    exportModalTitle.textContent = 'Generating Care of Persons Excel';
                    exportModalSubtitle.textContent = 'Structuring spreadsheet, bookings, and pilgrim columns...';
                    exportModalIcon.className = 'mdi mdi-file-excel text-success';
                    exportModalIconBg.style.backgroundColor = '#dcfce7';
                    exportProgressBar.className =
                        'progress-bar progress-bar-striped progress-bar-animated bg-success';
                }

                let currentPercent = 15;
                exportProgressBar.style.width = '15%';
                exportProgressPercent.textContent = '15%';
                exportProgressPhase.textContent = 'Loading dataset & records...';
                if (exportModal) exportModal.show();

                exportProgressInterval = setInterval(() => {
                    if (currentPercent < 45) {
                        currentPercent += 5;
                        exportProgressPhase.textContent = 'Optimizing manifest layout & details...';
                    } else if (currentPercent < 75) {
                        currentPercent += 3;
                        exportProgressPhase.textContent = 'Building pages & styling tables...';
                    } else if (currentPercent < 90) {
                        currentPercent += 1;
                        exportProgressPhase.textContent = 'Finalizing file stream & compression...';
                    }
                    exportProgressBar.style.width = currentPercent + '%';
                    exportProgressPercent.textContent = currentPercent + '%';
                }, 600);
            }

            function finishExportProgress() {
                clearInterval(exportProgressInterval);
                exportProgressBar.style.width = '100%';
                exportProgressPercent.textContent = '100%';
                exportProgressPhase.textContent = 'Download started!';
                setTimeout(() => {
                    if (exportModal) exportModal.hide();
                }, 800);
            }

            async function triggerDownloadWithLoader(url, type) {
                startExportProgress(type);
                try {
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        let errorMsg = `Server returned status: ${response.status} ${response.statusText}`;
                        try {
                            const errJson = await response.json();
                            if (errJson.error || errJson.message) {
                                errorMsg += ' - ' + (errJson.error || errJson.message);
                            }
                        } catch (e) {
                            try {
                                const errTxt = await response.text();
                                if (errTxt && errTxt.length < 300) {
                                    errorMsg += ' - ' + errTxt.replace(/<[^>]+>/g, ' ').trim();
                                }
                            } catch (e2) {}
                        }
                        throw new Error(errorMsg);
                    }

                    const blob = await response.blob();

                    // Get filename from header if available
                    const contentType = response.headers.get('content-type') || '';
                    let filename = (type === 'pdf') ? 'Care_Of_Persons_Report.pdf' :
                        ((contentType.includes('ms-excel') || contentType.includes('html')) ? 'Care_Of_Persons_Report.xls' : 'Care_Of_Persons_Report.xlsx');
                    const disposition = response.headers.get('content-disposition');
                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    // Trigger browser save
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = downloadUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(downloadUrl);
                    a.remove();

                    finishExportProgress();
                } catch (err) {
                    clearInterval(exportProgressInterval);
                    if (exportModal) exportModal.hide();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Export Failed',
                            text: err.message ||
                                'An error occurred while generating the export. Please check your network and filters.',
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        alert('Export Failed: ' + (err.message || 'Error occurred while generating export.'));
                    }
                }
            }

            // Attach loader triggers to PDF & Excel export links
            document.querySelectorAll('a[href*="export-pdf"], #btnExportPdf').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const cols = getActiveColumns();
                    const colParam = cols.length === 0 ? 'none' : cols.join(',');
                    const url = new URL(this.href, window.location.href);
                    url.searchParams.set('columns', colParam);
                    triggerDownloadWithLoader(url.href, 'pdf');
                });
            });

            document.querySelectorAll('a[href*="export-excel"], #btnExportExcel').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const cols = getActiveColumns();
                    const colParam = cols.length === 0 ? 'none' : cols.join(',');
                    const url = new URL(this.href, window.location.href);
                    url.searchParams.set('columns', colParam);
                    triggerDownloadWithLoader(url.href, 'excel');
                });
            });
        });
    </script>
@endsection
