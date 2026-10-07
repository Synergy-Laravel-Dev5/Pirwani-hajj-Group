@extends('layout.master')
@section('title', 'Edit Booking')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fs-18 fw-semibold m-0">Edit Booking</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-info btn-sm">
                            <i class="mdi mdi-eye me-1"></i> View
                        </a>
                        <a href="{{ route('booking.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Summary Bar --}}
                <div class="booking-summary-bar mb-3 px-3 py-2 d-flex flex-wrap gap-3 align-items-center rounded"
                    style="background:#f0f4ff; border:1px solid #d0d9f0; font-size:13px;">
                    <span>Persons: <strong id="bar_pax">{{ $booking->no_of_pax }}</strong></span>
                    <span>|</span>
                    <span>Adult Costing: <strong id="bar_adult">{{ number_format($booking->package_cost * $booking->no_of_pax, 2) }}</strong></span>
                    <span>|</span>
                    <span>Visa: <strong id="bar_visa">{{ number_format($booking->visa_charges, 2) }}</strong></span>
                    <span>|</span>
                    <span>Flight: <strong id="bar_flight">{{ number_format($booking->flight_charges, 2) }}</strong></span>
                    <span>|</span>
                    <span>Qurbani: <strong id="bar_qurbani">{{ number_format($booking->qurbani_charges ?? 0, 2) }}</strong></span>
                    <span>|</span>
                    <span class="text-danger">Discount: <strong id="bar_discount">{{ number_format($booking->discount ?? 0, 2) }}</strong></span>
                    <span>|</span>
                    <span class="text-primary fw-semibold">Total: <strong id="bar_total">{{ number_format($booking->total_amount, 2) }}</strong></span>
                    <span>|</span>
                    <span class="text-danger fw-semibold">Balance: <strong id="bar_balance">{{ number_format($booking->balance, 2) }}</strong></span>
                </div>

                <form action="{{ route('booking.update', $booking->id) }}" method="POST" id="bookingForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- TAB NAV --}}
                    <ul class="nav nav-tabs booking-tabs mb-0" id="bookingTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-details">
                                <i class="mdi mdi-account me-1"></i>Booking Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-package">
                                <i class="mdi mdi-cube-outline me-1"></i>Package
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-camp-qurbani">
                                <i class="mdi mdi-tent me-1"></i>Camp & Qurbani
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-persons">
                                <i class="mdi mdi-account-group me-1"></i>Persons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-flight">
                                <i class="mdi mdi-airplane me-1"></i>Flight
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-hotel">
                                <i class="mdi mdi-hotel me-1"></i>Hotel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-transport">
                                <i class="mdi mdi-bus me-1"></i>Transport
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-visa">
                                <i class="mdi mdi-passport me-1"></i>Visa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-costing">
                                <i class="mdi mdi-cash me-1"></i>Costing
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white" id="bookingTabsContent">

                        {{-- TAB 1: Booking Details --}}
                        <div class="tab-pane fade show active" id="tab-details">
                            <div class="row g-3">

                                {{-- BOOKING FOR TOGGLE --}}
                                <div class="col-md-12 mb-2">
                                    <label class="form-label d-block">Booking For <span
                                            class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="booking_for"
                                                id="for_client" value="client"
                                                {{ old('booking_for', $booking->booking_for) == 'client' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="for_client">Client</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="booking_for"
                                                id="for_company" value="company"
                                                {{ old('booking_for', $booking->booking_for) == 'company' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="for_company">Company</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- CLIENT BLOCK --}}
                                <div class="col-md-6" id="clientBlock">
                                    <label class="form-label">Client <span class="text-danger">*</span></label>
                                    <select name="client_id" id="clientSelect" class="form-select">
                                        <option value="">-- Select Client --</option>
                                        @foreach ($clients as $c)
                                            <option value="{{ $c->id }}"
                                                data-passport="{{ $c->passport_number }}"
                                                data-cnic="{{ $c->cnic }}" data-phone="{{ $c->phone }}"
                                                {{ $booking->client_id == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}{{ $c->company_name ? ' (' . $c->company_name . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- COMPANY BLOCK --}}
                                <div class="col-md-6 d-none" id="companyBlock">
                                    <label class="form-label">Company <span class="text-danger">*</span></label>
                                    <select name="company_id" id="companySelect" class="form-select">
                                        <option value="">-- Select Company --</option>
                                        @foreach ($companies as $co)
                                            <option value="{{ $co->id }}"
                                                {{ $booking->company_id == $co->id ? 'selected' : '' }}>
                                                {{ $co->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">No of Pax <span class="text-danger">*</span></label>
                                    <input type="number" name="no_of_pax" id="no_of_pax" class="form-control"
                                        value="{{ old('no_of_pax', $booking->no_of_pax) }}" min="1" required>
                                    <small class="text-muted">Auto-calculated from Room Sharing Pax</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Care Of</label>
                                    <input type="text" name="care_of" class="form-control"
                                        value="{{ old('care_of', $booking->care_of) }}" placeholder="Guardian / Agent">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Passport Number</label>
                                    <input type="text" name="passport_number" id="fill_passport" class="form-control"
                                        value="{{ old('passport_number', $booking->passport_number) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">CNIC Number</label>
                                    <input type="text" name="cnic" id="fill_cnic" class="form-control"
                                        value="{{ old('cnic', $booking->cnic) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" id="fill_phone" class="form-control"
                                        value="{{ old('phone', $booking->phone) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Emergency Phone</label>
                                    <input type="text" name="emergency_phone" class="form-control"
                                        value="{{ old('emergency_phone', $booking->emergency_phone) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Voucher Number</label>
                                    <input type="text" name="voucher_number" class="form-control"
                                        value="{{ old('voucher_number', $booking->voucher_number) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" name="card_number" class="form-control"
                                        value="{{ old('card_number', $booking->card_number) }}">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary btn-next">Next <i
                                        class="mdi mdi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        {{-- TAB 2: Package Selection & Details --}}
                        <div class="tab-pane fade" id="tab-package">
                            <input type="hidden" name="package_type" value="{{ $booking->package_type ?? 'hajj' }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        <i class="mdi mdi-cube-send me-1 text-primary"></i>Select Package
                                    </label>
                                    <select name="package_id" id="packageSelect" class="form-select border-primary">
                                        <option value="">-- Select Package --</option>
                                        @foreach ($packages as $pkg)
                                            @php
                                                $pCode = $pkg->code ?? $pkg->package_number ?? '';
                                                $pTitle = $pkg->package_title ?? $pkg->name ?? 'Hajj Package';
                                                $pYear = $pkg->year ?? $pkg->gregorian_year;
                                                $pStay = $pkg->stay_type ?? $pkg->category ?? 'Package';
                                            @endphp
                                            <option value="{{ $pkg->id }}" {{ old('package_id', $booking->package_id) == $pkg->id ? 'selected' : '' }}>
                                                {{ $pCode ? '[' . $pCode . '] ' : '' }}{{ $pTitle }} ({{ $pYear }}) - {{ $pStay }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Selecting a package will auto-load its camp rates, dates, and qurbani options.</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Package Year <span class="text-danger">*</span></label>
                                    <select name="package_year" id="package_year" class="form-select" required>
                                        @php $currentYear = date('Y'); @endphp
                                        @for ($year = $currentYear - 2; $year <= $currentYear + 5; $year++)
                                            <option value="{{ $year }}"
                                                {{ old('package_year', $booking->package_year ?? $currentYear) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Booking Status</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-primary"><i class="mdi mdi-barcode me-1"></i>Package Code</label>
                                    <input type="text" id="package_code_display" class="form-control bg-light fw-bold text-dark"
                                        value="{{ $booking->package ? ($booking->package->code ?? $booking->package->package_number ?? '') : '' }}"
                                        placeholder="e.g. PW001" readonly>
                                </div>

                                <div class="col-md-9">
                                    <label class="form-label fw-semibold">Package Name / Title</label>
                                    <input type="text" name="package_name" id="package_name" class="form-control"
                                        value="{{ old('package_name', $booking->package_name) }}"
                                        placeholder="e.g. Hajj Long Stay 29-30 Days">
                                </div>

                                {{-- Selected Package Preview Card --}}
                                <div class="col-md-12 {{ $booking->package_id ? '' : 'd-none' }}" id="packageInfoCard">
                                    <div class="card border border-primary-subtle bg-light mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                <h6 class="m-0 text-primary fw-bold">
                                                    <i class="mdi mdi-information-outline me-1"></i>Package Details & Rate Matrix
                                                </h6>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <span class="badge bg-dark px-2 py-1" id="preview_pkg_code">CODE: {{ $booking->package ? ($booking->package->code ?? $booking->package->package_number ?? '—') : '—' }}</span>
                                                    <span class="badge bg-primary px-2 py-1" id="preview_pkg_stay">LONG STAY</span>
                                                </div>
                                            </div>
                                            <div class="row g-2" style="font-size:12.5px;">
                                                <div class="col-md-3">
                                                    <span class="text-muted">Package Code:</span>
                                                    <strong class="d-block text-primary fw-bold" id="preview_pkg_code_text">{{ $booking->package ? ($booking->package->code ?? $booking->package->package_number ?? '—') : '—' }}</strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="text-muted">Camp / Category:</span>
                                                    <strong class="d-block text-dark" id="preview_pkg_camp">—</strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="text-muted">Duration:</span>
                                                    <strong class="d-block text-dark" id="preview_pkg_duration">—</strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="text-muted">Sectors:</span>
                                                    <strong class="d-block text-dark" id="preview_pkg_sectors">—</strong>
                                                </div>
                                                <div class="col-md-3 mt-2">
                                                    <span class="text-muted">Qurbani Policy:</span>
                                                    <strong class="d-block text-success" id="preview_pkg_qurbani">—</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary btn-next">
                                    Next <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- TAB 3: Camp & Qurbani (DEDICATED SEPARATE TAB) --}}
                        <div class="tab-pane fade" id="tab-camp-qurbani">
                            @php
                                $rb = $booking->room_breakdown ?? [];
                            @endphp
                            <div class="row g-3">

                                {{-- Camp / Maktab Select Top --}}
                                <div class="col-md-12">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center bg-light p-3 border rounded">
                                        <div class="flex-grow-1 me-3">
                                            <label class="form-label fw-bold mb-1"><i class="mdi mdi-tent me-1 text-primary"></i>Camp / Maktab Category</label>
                                            <select name="camp" id="campSelect" class="form-select">
                                                <option value="">-- Select Camp / Maktab --</option>
                                                <option value="Maktab A (Zone 1/2)" {{ old('camp', $booking->camp) == 'Maktab A (Zone 1/2)' ? 'selected' : '' }}>Maktab A (Zone 1 or 2 - VIP Close to Jamarat)</option>
                                                <option value="Maktab C (Zone 5)" {{ old('camp', $booking->camp) == 'Maktab C (Zone 5)' ? 'selected' : '' }}>Maktab C (Zone 5 - Standard)</option>
                                                <option value="Maktab A & C (Combo)" {{ old('camp', $booking->camp) == 'Maktab A & C (Combo)' ? 'selected' : '' }}>Maktab A & C (Combo)</option>
                                                <option value="VIP Mina Camp" {{ old('camp', $booking->camp) == 'VIP Mina Camp' ? 'selected' : '' }}>VIP Mina Camp</option>
                                                <option value="Standard Camp" {{ old('camp', $booking->camp) == 'Standard Camp' ? 'selected' : '' }}>Standard Camp</option>
                                            </select>
                                        </div>
                                        <div class="text-muted small mt-2 mt-md-0">
                                            <i class="mdi mdi-information-outline text-info"></i> You can select <strong>Quad, Triple & Double</strong> sharing together in a single booking!
                                        </div>
                                    </div>
                                </div>

                                {{-- Authentic Golden Pricing Cards (Brochure Style) --}}
                                <div class="col-12">
                                    <div class="row g-3">

                                        {{-- Maktab C Card --}}
                                        <div class="col-lg-6" id="maktabCCard">
                                            <div class="pkg-pricing-card h-100">
                                                <div class="pkg-pricing-header d-flex">
                                                    <div class="pkg-maktab-badge d-flex flex-column align-items-center justify-content-center">
                                                        <span class="maktab-letter">C</span>
                                                        <span class="maktab-text">MAKTAB</span>
                                                        <span class="maktab-zone" id="lbl_maktab_c_zone">ZONE 5</span>
                                                    </div>
                                                    <div class="pkg-pricing-cols d-flex flex-grow-1">
                                                        {{-- Quad --}}
                                                        <div class="pkg-col flex-1 text-center p-2 border-end">
                                                            <div class="pkg-col-title">QUAD / SHARING</div>
                                                            <div class="pkg-col-pkr" id="disp_c_quad_pkr">0.00</div>
                                                            <div class="pkg-col-usd" id="disp_c_quad_usd">$ 0.00</div>
                                                            <div class="pax-stepper mt-2">
                                                                <label class="small text-muted d-block" style="font-size:11px;">Pax in Quad</label>
                                                                <div class="input-group input-group-sm">
                                                                    <button class="btn btn-outline-secondary btn-step-minus" type="button" data-target="c_quad_pax">-</button>
                                                                    <input type="number" name="room_breakdown[c_quad_pax]" id="c_quad_pax" class="form-control text-center room-pax-input" value="{{ $rb['c_quad_pax'] ?? 0 }}" min="0">
                                                                    <button class="btn btn-outline-secondary btn-step-plus" type="button" data-target="c_quad_pax">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- Triple --}}
                                                        <div class="pkg-col flex-1 text-center p-2 border-end">
                                                            <div class="pkg-col-title">TRIPLE</div>
                                                            <div class="pkg-col-pkr" id="disp_c_triple_pkr">0.00</div>
                                                            <div class="pkg-col-usd" id="disp_c_triple_usd">$ 0.00</div>
                                                            <div class="pax-stepper mt-2">
                                                                <label class="small text-muted d-block" style="font-size:11px;">Pax in Triple</label>
                                                                <div class="input-group input-group-sm">
                                                                    <button class="btn btn-outline-secondary btn-step-minus" type="button" data-target="c_triple_pax">-</button>
                                                                    <input type="number" name="room_breakdown[c_triple_pax]" id="c_triple_pax" class="form-control text-center room-pax-input" value="{{ $rb['c_triple_pax'] ?? 0 }}" min="0">
                                                                    <button class="btn btn-outline-secondary btn-step-plus" type="button" data-target="c_triple_pax">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- Double --}}
                                                        <div class="pkg-col flex-1 text-center p-2">
                                                            <div class="pkg-col-title">DOUBLE</div>
                                                            <div class="pkg-col-pkr" id="disp_c_double_pkr">0.00</div>
                                                            <div class="pkg-col-usd" id="disp_c_double_usd">$ 0.00</div>
                                                            <div class="pax-stepper mt-2">
                                                                <label class="small text-muted d-block" style="font-size:11px;">Pax in Double</label>
                                                                <div class="input-group input-group-sm">
                                                                    <button class="btn btn-outline-secondary btn-step-minus" type="button" data-target="c_double_pax">-</button>
                                                                    <input type="number" name="room_breakdown[c_double_pax]" id="c_double_pax" class="form-control text-center room-pax-input" value="{{ $rb['c_double_pax'] ?? 0 }}" min="0">
                                                                    <button class="btn btn-outline-secondary btn-step-plus" type="button" data-target="c_double_pax">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Maktab A Card --}}
                                        <div class="col-lg-6" id="maktabACard">
                                            <div class="pkg-pricing-card h-100">
                                                <div class="pkg-pricing-header d-flex">
                                                    <div class="pkg-maktab-badge d-flex flex-column align-items-center justify-content-center">
                                                        <span class="maktab-letter">A</span>
                                                        <span class="maktab-text">MAKTAB</span>
                                                        <span class="maktab-zone" id="lbl_maktab_a_zone">ZONE 1 OR 2</span>
                                                    </div>
                                                    <div class="pkg-pricing-cols d-flex flex-grow-1">
                                                        {{-- Quad --}}
                                                        <div class="pkg-col flex-1 text-center p-2 border-end">
                                                            <div class="pkg-col-title">QUAD / SHARING</div>
                                                            <div class="pkg-col-pkr" id="disp_a_quad_pkr">0.00</div>
                                                            <div class="pkg-col-usd" id="disp_a_quad_usd">$ 0.00</div>
                                                            <div class="pax-stepper mt-2">
                                                                <label class="small text-muted d-block" style="font-size:11px;">Pax in Quad</label>
                                                                <div class="input-group input-group-sm">
                                                                    <button class="btn btn-outline-secondary btn-step-minus" type="button" data-target="a_quad_pax">-</button>
                                                                    <input type="number" name="room_breakdown[a_quad_pax]" id="a_quad_pax" class="form-control text-center room-pax-input" value="{{ $rb['a_quad_pax'] ?? 0 }}" min="0">
                                                                    <button class="btn btn-outline-secondary btn-step-plus" type="button" data-target="a_quad_pax">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- Triple --}}
                                                        <div class="pkg-col flex-1 text-center p-2 border-end">
                                                            <div class="pkg-col-title">TRIPLE</div>
                                                            <div class="pkg-col-pkr" id="disp_a_triple_pkr">0.00</div>
                                                            <div class="pkg-col-usd" id="disp_a_triple_usd">$ 0.00</div>
                                                            <div class="pax-stepper mt-2">
                                                                <label class="small text-muted d-block" style="font-size:11px;">Pax in Triple</label>
                                                                <div class="input-group input-group-sm">
                                                                    <button class="btn btn-outline-secondary btn-step-minus" type="button" data-target="a_triple_pax">-</button>
                                                                    <input type="number" name="room_breakdown[a_triple_pax]" id="a_triple_pax" class="form-control text-center room-pax-input" value="{{ $rb['a_triple_pax'] ?? 0 }}" min="0">
                                                                    <button class="btn btn-outline-secondary btn-step-plus" type="button" data-target="a_triple_pax">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- Double --}}
                                                        <div class="pkg-col flex-1 text-center p-2">
                                                            <div class="pkg-col-title">DOUBLE</div>
                                                            <div class="pkg-col-pkr" id="disp_a_double_pkr">0.00</div>
                                                            <div class="pkg-col-usd" id="disp_a_double_usd">$ 0.00</div>
                                                            <div class="pax-stepper mt-2">
                                                                <label class="small text-muted d-block" style="font-size:11px;">Pax in Double</label>
                                                                <div class="input-group input-group-sm">
                                                                    <button class="btn btn-outline-secondary btn-step-minus" type="button" data-target="a_double_pax">-</button>
                                                                    <input type="number" name="room_breakdown[a_double_pax]" id="a_double_pax" class="form-control text-center room-pax-input" value="{{ $rb['a_double_pax'] ?? 0 }}" min="0">
                                                                    <button class="btn btn-outline-secondary btn-step-plus" type="button" data-target="a_double_pax">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Azizia Separate Room Addon Card --}}
                                        <div class="col-12" id="aziziaAddonCard">
                                            <div class="pkg-azizia-card p-3 rounded">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between px-3 py-2 mb-2 rounded" style="background:#f1bf66;">
                                                    <strong class="text-dark fs-14" style="letter-spacing:1px;"><i class="mdi mdi-home-city me-1"></i>AZIZIA SEPARATE ROOM (OPTIONAL ADD-ON)</strong>
                                                    <small class="text-dark fw-bold">Separate Room Supplement</small>
                                                </div>
                                                <div class="row g-2 text-center">
                                                    <div class="col-md-4">
                                                        <div class="p-2 border rounded bg-white">
                                                            <span class="badge bg-warning text-dark px-2 py-1 mb-1">QUAD / SHARING</span>
                                                            <div class="fw-bold fs-16 text-dark" id="disp_az_quad_pkr">0.00 PKR</div>
                                                            <small class="text-muted d-block">Per Person <span id="disp_az_quad_usd">$ 0.00</span></small>
                                                            <div class="input-group input-group-sm mt-2">
                                                                <span class="input-group-text">Pax</span>
                                                                <input type="number" name="room_breakdown[az_quad_pax]" id="az_quad_pax" class="form-control text-center room-pax-input" value="{{ $rb['az_quad_pax'] ?? 0 }}" min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="p-2 border rounded bg-white">
                                                            <span class="badge bg-warning text-dark px-2 py-1 mb-1">TRIPLE</span>
                                                            <div class="fw-bold fs-16 text-dark" id="disp_az_triple_pkr">0.00 PKR</div>
                                                            <small class="text-muted d-block">Per Person <span id="disp_az_triple_usd">$ 0.00</span></small>
                                                            <div class="input-group input-group-sm mt-2">
                                                                <span class="input-group-text">Pax</span>
                                                                <input type="number" name="room_breakdown[az_triple_pax]" id="az_triple_pax" class="form-control text-center room-pax-input" value="{{ $rb['az_triple_pax'] ?? 0 }}" min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="p-2 border rounded bg-white">
                                                            <span class="badge bg-warning text-dark px-2 py-1 mb-1">DOUBLE</span>
                                                            <div class="fw-bold fs-16 text-dark" id="disp_az_double_pkr">0.00 PKR</div>
                                                            <small class="text-muted d-block">Per Person <span id="disp_az_double_usd">$ 0.00</span></small>
                                                            <div class="input-group input-group-sm mt-2">
                                                                <span class="input-group-text">Pax</span>
                                                                <input type="number" name="room_breakdown[az_double_pax]" id="az_double_pax" class="form-control text-center room-pax-input" value="{{ $rb['az_double_pax'] ?? 0 }}" min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Live Calculation Summary Box --}}
                                        <div class="col-12">
                                            <div class="alert alert-warning border-warning p-3 d-flex flex-wrap justify-content-between align-items-center mb-0" style="background:#fff9e6;">
                                                <div>
                                                    <div class="fw-bold text-dark fs-14">
                                                        <i class="mdi mdi-calculator me-1 text-primary"></i>Live Multi-Room Costing Breakdown:
                                                    </div>
                                                    <div id="room_calculation_breakdown_text" class="text-muted mt-1" style="font-size:13px;">
                                                        Enter passenger quantities in Quad, Triple, and/or Double cards above.
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-muted small">Total Package Amount:</div>
                                                    <div class="fw-bold fs-18 text-primary" id="room_total_cost_display">PKR 0.00</div>
                                                    <div class="text-muted" style="font-size:11.5px;">Assigned Pax: <strong id="room_total_pax_display">{{ $booking->no_of_pax }}</strong></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- Qurbani Section --}}
                                <div class="col-12">
                                    <div class="card border shadow-none mb-0">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="m-0 fw-bold text-success">
                                                <i class="mdi mdi-sheep me-1"></i>Qurbani Option & Charges
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Qurbani Option</label>
                                                    <select name="qurbani_option" id="qurbani_option" class="form-select">
                                                        <option value="not_included" {{ old('qurbani_option', $booking->qurbani_option) == 'not_included' ? 'selected' : '' }}>Not Included (Nusuk Masar Direct)</option>
                                                        <option value="included" {{ old('qurbani_option', $booking->qurbani_option) == 'included' ? 'selected' : '' }}>Included in Package</option>
                                                        <option value="separate_request" {{ old('qurbani_option', $booking->qurbani_option) == 'separate_request' ? 'selected' : '' }}>Separate Add-on</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Qurbani Qty (Heads)</label>
                                                    <input type="number" name="qurbani_qty" id="qurbani_qty" class="form-control"
                                                        value="{{ old('qurbani_qty', $booking->qurbani_qty ?? 0) }}" min="0">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Qurbani Total Charges (PKR)</label>
                                                    <input type="number" name="qurbani_charges" id="qurbani_charges" class="form-control calc"
                                                        value="{{ old('qurbani_charges', $booking->qurbani_charges ?? 0) }}" min="0" step="0.01" placeholder="0.00">
                                                    <small class="text-muted">This amount will be automatically included in the Costing tab.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary btn-next">
                                    Next <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- TAB 4: Persons --}}
                        <div class="tab-pane fade" id="tab-persons">
                            <p class="text-muted small mb-3">
                                <i class="mdi mdi-information me-1"></i>
                                Rows are based on No. of Pax. Main passenger can be linked to a client.
                            </p>
                            <div id="personsBookingForNote"
                                class="alert alert-info py-2 px-3 {{ $booking->booking_for == 'company' ? '' : 'd-none' }}"
                                style="font-size:13px;">
                                <i class="mdi mdi-information me-1"></i>
                                Booking is for Company — client dropdown is hidden for main passenger.
                            </div>
                            <div id="personsList">
                                @foreach ($booking->persons as $i => $person)
                                    <div class="person-card" id="person_card_{{ $i }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                            <strong class="text-primary" style="font-size:13px;">
                                                <i class="mdi mdi-account me-1"></i>{{ $i === 0 ? ($booking->booking_for === 'company' ? 'Main Passenger' : 'Main Passenger (Client)') : 'Passenger ' . ($i + 1) }}
                                            </strong>
                                            <span class="badge bg-light text-muted border">Person #{{ $i + 1 }}</span>
                                        </div>
                                        <div class="row g-2">
                                            @if ($i === 0 && $booking->booking_for !== 'company')
                                                <div class="col-md-12 mb-2">
                                                    <label class="form-label" style="font-size:12px;">Select Client to auto-fill</label>
                                                    <select class="form-select form-select-sm person-client-select"
                                                        data-idx="0" onchange="fillPersonFromClient(this, 0)">
                                                        <option value="">-- Manual Entry --</option>
                                                        @foreach ($clients as $c)
                                                            <option value="{{ $c->id }}"
                                                                data-passport="{{ $c->passport_number }}"
                                                                data-cnic="{{ $c->cnic }}"
                                                                data-phone="{{ $c->phone }}"
                                                                data-surname="{{ $c->surname ?? '' }}"
                                                                data-given-name="{{ $c->given_name ?? $c->name ?? '' }}"
                                                                data-dob="{{ $c->dob ?? '' }}"
                                                                data-passport-exp="{{ $c->passport_expiry_date ?? '' }}"
                                                                {{ $booking->client_id == $c->id ? 'selected' : '' }}>
                                                                {{ $c->name }}{{ $c->company_name ? ' (' . $c->company_name . ')' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Surname / Family Name</label>
                                                <input type="text" name="persons[{{ $i }}][surname]" id="person_surname_{{ $i }}"
                                                    class="form-control form-control-sm" placeholder="e.g. Khan"
                                                    value="{{ $person->surname ?? '' }}" oninput="updatePersonFullName({{ $i }})">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Given Name</label>
                                                <input type="text" name="persons[{{ $i }}][given_name]" id="person_given_name_{{ $i }}"
                                                    class="form-control form-control-sm" placeholder="e.g. Muhammad"
                                                    value="{{ $person->given_name ?? '' }}" oninput="updatePersonFullName({{ $i }})">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Full Name</label>
                                                <input type="text" name="persons[{{ $i }}][full_name]" id="person_name_{{ $i }}"
                                                    class="form-control form-control-sm" placeholder="Muhammad Khan"
                                                    value="{{ $person->full_name }}" oninput="onPersonManualName({{ $i }})">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Date of Birth</label>
                                                <input type="date" name="persons[{{ $i }}][dob]" id="person_dob_{{ $i }}"
                                                    class="form-control form-control-sm"
                                                    value="{{ $person->dob ? \Carbon\Carbon::parse($person->dob)->format('Y-m-d') : '' }}" onchange="syncVisas()">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Passport #</label>
                                                <input type="text" name="persons[{{ $i }}][passport_number]" id="person_passport_{{ $i }}"
                                                    class="form-control form-control-sm" placeholder="Passport #"
                                                    value="{{ $person->passport_number }}" oninput="onPersonPassportInput({{ $i }})">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Date of Issue</label>
                                                <input type="date" name="persons[{{ $i }}][date_of_issue]" id="person_passport_doi_{{ $i }}"
                                                    class="form-control form-control-sm"
                                                    value="{{ $person->date_of_issue ? \Carbon\Carbon::parse($person->date_of_issue)->format('Y-m-d') : ($person->passport_issue_date ? \Carbon\Carbon::parse($person->passport_issue_date)->format('Y-m-d') : '') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Passport Expiry Date</label>
                                                <input type="date" name="persons[{{ $i }}][passport_expiry_date]" id="person_passport_exp_{{ $i }}"
                                                    class="form-control form-control-sm"
                                                    value="{{ $person->passport_expiry_date ? \Carbon\Carbon::parse($person->passport_expiry_date)->format('Y-m-d') : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">CNIC</label>
                                                <input type="text" name="persons[{{ $i }}][cnic]" id="person_cnic_{{ $i }}"
                                                    class="form-control form-control-sm" placeholder="XXXXX-XXXXXXX-X"
                                                    value="{{ $person->cnic ?? '' }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label" style="font-size:12px;">Gender</label>
                                                <select name="persons[{{ $i }}][gender]" id="person_gender_{{ $i }}" class="form-select form-select-sm">
                                                    <option value="Male" {{ strtolower($person->gender ?? 'male') === 'male' ? 'selected' : '' }}>Male</option>
                                                    <option value="Female" {{ strtolower($person->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label" style="font-size:12px;">Hajj ID</label>
                                                <input type="text" name="persons[{{ $i }}][hajj_id]" id="person_hajj_id_{{ $i }}"
                                                    class="form-control form-control-sm" placeholder="e.g. PW26057"
                                                    value="{{ $person->hajj_id ?? '' }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label" style="font-size:12px;">HB #</label>
                                                <input type="text" name="persons[{{ $i }}][hb_number]" id="person_hb_number_{{ $i }}"
                                                    class="form-control form-control-sm" placeholder="e.g. HB0001"
                                                    value="{{ $person->hb_number ?? '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Phone</label>
                                                <input type="text" name="persons[{{ $i }}][phone]" id="person_phone_{{ $i }}"
                                                    class="form-control form-control-sm" placeholder="+92 300 0000000"
                                                    value="{{ $person->phone ?? '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;"><i class="mdi mdi-camera text-primary me-1"></i>Pilgrim Photo</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" name="persons[{{ $i }}][photo]" id="person_photo_{{ $i }}"
                                                        class="form-control form-control-sm" accept="image/*" onchange="previewPersonPhoto(this, {{ $i }})">
                                                    @if(!empty($person->photo))
                                                        <input type="hidden" name="persons[{{ $i }}][existing_photo]" value="{{ $person->photo }}">
                                                    @endif
                                                    <div id="person_photo_preview_{{ $i }}" class="flex-shrink-0" style="width:34px; height:34px; border-radius:4px; border:1px solid #ced4da; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8f9fa;">
                                                        @if(!empty($person->photo))
                                                            <img src="{{ asset($person->photo) }}" style="width:100%; height:100%; object-fit:cover;">
                                                        @else
                                                            <i class="mdi mdi-account text-muted fs-16"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev"><i
                                        class="mdi mdi-arrow-left me-1"></i> Prev</button>
                                <button type="button" class="btn btn-primary btn-next">Next <i
                                        class="mdi mdi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        {{-- TAB 5: Flight --}}
                        {{-- TAB 5: Flight --}}
                        <div class="tab-pane fade" id="tab-flight">

                            {{-- DEPARTURE --}}
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                        <h6 class="text-primary mb-0 fw-bold">
                                            <i class="mdi mdi-airplane-takeoff me-1"></i> Departure Flight Information
                                        </h6>
                                        <span class="badge bg-light text-muted border">Departure Sector</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Date of Departure</label>
                                    <input type="date" name="departure_date" id="departure_date" class="form-control"
                                        value="{{ old('departure_date', $booking->departure_date) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Departure Flight #</label>
                                    <input type="text" name="departure_flight" id="departure_flight" class="form-control"
                                        value="{{ old('departure_flight', $booking->departure_flight) }}"
                                        placeholder="e.g. SV-701 or PK-741">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Time of Departure</label>
                                    <input type="time" name="departure_time" id="departure_time" class="form-control"
                                        value="{{ old('departure_time', $booking->departure_time) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Departure Airline</label>
                                    <select name="departure_airline" id="departure_airline" class="form-select">
                                        <option value="">-- Select Airline --</option>
                                        @if(isset($airlines) && $airlines->count())
                                            @foreach($airlines as $al)
                                                <option value="{{ $al->name }}"
                                                    {{ old('departure_airline', $booking->departure_airline) == $al->name ? 'selected' : '' }}>
                                                    {{ $al->name }} ({{ $al->code ?? $al->iata_code ?? 'AIR' }})
                                                </option>
                                            @endforeach
                                        @else
                                            @foreach (['Saudi Arabian Airlines (Saudia)', 'Pakistan International Airlines (PIA)', 'Flynas', 'Air Arabia', 'Emirates', 'Qatar Airways', 'FlyDubai', 'Other'] as $air)
                                                <option value="{{ $air }}"
                                                    {{ old('departure_airline', $booking->departure_airline) == $air ? 'selected' : '' }}>
                                                    {{ $air }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Departure PNR / Ticket #</label>
                                    <input type="text" name="departure_pnr" id="departure_pnr" class="form-control"
                                        value="{{ old('departure_pnr', $booking->departure_pnr) }}" placeholder="e.g. ABC123">
                                </div>
                            </div>

                            {{-- ARRIVAL --}}
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                        <h6 class="text-primary mb-0 fw-bold">
                                            <i class="mdi mdi-airplane-landing me-1"></i> Return / Arrival Flight Information
                                        </h6>
                                        <span class="badge bg-light text-muted border">Arrival Sector</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Date of Return / Arrival</label>
                                    <input type="date" name="arrival_date" id="arrival_date" class="form-control"
                                        value="{{ old('arrival_date', $booking->arrival_date) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Arrival Flight #</label>
                                    <input type="text" name="arrival_flight" id="arrival_flight" class="form-control"
                                        value="{{ old('arrival_flight', $booking->arrival_flight) }}"
                                        placeholder="e.g. SV-702 or PK-742">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Time of Arrival</label>
                                    <input type="time" name="arrival_time" id="arrival_time" class="form-control"
                                        value="{{ old('arrival_time', $booking->arrival_time) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Arrival Airline</label>
                                    <select name="arrival_airline" id="arrival_airline" class="form-select">
                                        <option value="">-- Select Airline --</option>
                                        @if(isset($airlines) && $airlines->count())
                                            @foreach($airlines as $al)
                                                <option value="{{ $al->name }}"
                                                    {{ old('arrival_airline', $booking->arrival_airline) == $al->name ? 'selected' : '' }}>
                                                    {{ $al->name }} ({{ $al->code ?? $al->iata_code ?? 'AIR' }})
                                                </option>
                                            @endforeach
                                        @else
                                            @foreach (['Saudi Arabian Airlines (Saudia)', 'Pakistan International Airlines (PIA)', 'Flynas', 'Air Arabia', 'Emirates', 'Qatar Airways', 'FlyDubai', 'Other'] as $air)
                                                <option value="{{ $air }}"
                                                    {{ old('arrival_airline', $booking->arrival_airline) == $air ? 'selected' : '' }}>
                                                    {{ $air }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Arrival PNR / Ticket #</label>
                                    <input type="text" name="arrival_pnr" id="arrival_pnr" class="form-control"
                                        value="{{ old('arrival_pnr', $booking->arrival_pnr) }}" placeholder="e.g. XYZ456">
                                </div>
                            </div>

                            {{-- PASSENGER TICKETS --}}
                            <h6 class="text-muted border-bottom pb-2 mb-3">
                                <i class="mdi mdi-ticket-account me-1"></i> Passenger Tickets
                                <small class="text-info ms-2">(Auto-generated from No. of Pax)</small>
                            </h6>
                            <div id="flightPersonsList"></div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary btn-next">
                                    Next <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- TAB 6: Hotel --}}
                        <div class="tab-pane fade" id="tab-hotel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fs-16 fw-bold text-dark m-0">🏨 Hotel & Accommodation Schedule</h5>
                                    <small class="text-muted">Auto-loaded from selected package stay segments (Makkah, Azizia, Mina/Arafat, Madinah)</small>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addHotel">
                                    <i class="mdi mdi-plus"></i> Add Additional Hotel
                                </button>
                            </div>

                            <div id="hotelsList">
                                @forelse ($booking->hotels as $hotel)
                                    <div class="hotel-block border rounded p-3 mb-3 bg-light-subtle">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                            <h6 class="text-primary mb-0 fw-bold">
                                                <i class="mdi mdi-hotel me-1"></i>
                                                {{ $hotel->location === 'makkah' ? 'Makkah Hotel' : ($hotel->location === 'madinah' ? 'Madinah Hotel' : ($hotel->location === 'azizia' ? 'Azizia Hotel' : ($hotel->location === 'mina' ? 'Hajj Mina / Arafat Stay' : 'Hotel / Stay'))) }}
                                            </h6>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-hotel py-0 px-2" style="font-size:12px;">× Remove</button>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Location / Place</label>
                                                <select name="hotels[{{ $loop->index }}][location]" class="form-select form-select-sm hotel-loc-select">
                                                    <option value="makkah" {{ $hotel->location === 'makkah' ? 'selected' : '' }}>Makkah</option>
                                                    <option value="azizia" {{ $hotel->location === 'azizia' ? 'selected' : '' }}>Azizia</option>
                                                    <option value="mina" {{ $hotel->location === 'mina' ? 'selected' : '' }}>Hajj Days (Mina / Arafat)</option>
                                                    <option value="madinah" {{ $hotel->location === 'madinah' ? 'selected' : '' }}>Madinah</option>
                                                    <option value="other" {{ $hotel->location === 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Hotel / Building Name</label>
                                                <select class="form-select form-select-sm hotel-crud-select mb-1">
                                                    <option value="">-- Select from Hotel CRUD --</option>
                                                    @foreach ($hotels as $h)
                                                        @php
                                                            $hPlace = strtolower($h->place ?? '');
                                                            $match = false;
                                                            if ($hotel->location === 'makkah' && (str_contains($hPlace, 'makkah') || str_contains($hPlace, 'mecca'))) $match = true;
                                                            elseif ($hotel->location === 'madinah' && (str_contains($hPlace, 'madinah') || str_contains($hPlace, 'medina'))) $match = true;
                                                            elseif ($hotel->location === 'azizia' && str_contains($hPlace, 'azizia')) $match = true;
                                                            elseif ($hotel->location === 'mina' && (str_contains($hPlace, 'mina') || str_contains($hPlace, 'arafat') || str_contains($hPlace, 'hajj'))) $match = true;
                                                            elseif ($hotel->location === 'other' || empty($hotel->location)) $match = true;
                                                        @endphp
                                                        <option value="{{ $h->name }}" data-place="{{ $hPlace }}" {{ strtolower($hotel->hotel_name ?? '') === strtolower($h->name) ? 'selected' : '' }} {!! $match ? '' : 'style="display:none;"' !!}>
                                                            {{ $h->name }} ({{ $h->place ?? 'Hotel' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="hotels[{{ $loop->index }}][hotel_name]"
                                                    class="form-control form-control-sm hotel-name-input" value="{{ $hotel->hotel_name }}" placeholder="Hotel name">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Nights</label>
                                                <input type="number" name="hotels[{{ $loop->index }}][no_of_nights]"
                                                    class="form-control form-control-sm" value="{{ $hotel->no_of_nights ?: 1 }}" min="1">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Room Type</label>
                                                <select name="hotels[{{ $loop->index }}][room_type]" class="form-select form-select-sm">
                                                    @if(isset($roomTypes) && $roomTypes->count())
                                                        @foreach($roomTypes as $rt)
                                                            @php
                                                                $rtCode = $rt->code ?? strtolower($rt->name);
                                                                $isMatch = (strtolower($hotel->room_type ?? '') === strtolower($rt->name) || strtolower($hotel->room_type ?? '') === strtolower($rtCode));
                                                            @endphp
                                                            <option value="{{ $rtCode }}" {{ $isMatch ? 'selected' : '' }}>
                                                                {{ $rt->name }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        @foreach (['quad' => 'Quad', 'triple' => 'Triple', 'double' => 'Double', 'single' => 'Single', 'suite' => 'Suite'] as $rt => $rtl)
                                                            <option value="{{ $rt }}" {{ $hotel->room_type == $rt ? 'selected' : '' }}>
                                                                {{ $rtl }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Room Number</label>
                                                <input type="text" name="hotels[{{ $loop->index }}][room_number]"
                                                    class="form-control form-control-sm hotel-room-number-input" value="{{ $hotel->room_number ?? '' }}" placeholder="e.g. 101, 204">
                                                <div class="room-capacity-feedback mt-1 small"></div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Room Gender</label>
                                                <select name="hotels[{{ $loop->index }}][gender]" class="form-select form-select-sm">
                                                    <option value="Any" {{ ($hotel->gender ?? 'Any') === 'Any' ? 'selected' : '' }}>Any / Mixed</option>
                                                    <option value="Male" {{ ($hotel->gender ?? '') === 'Male' ? 'selected' : '' }}>Male Room (Men)</option>
                                                    <option value="Female" {{ ($hotel->gender ?? '') === 'Female' ? 'selected' : '' }}>Female Room (Women)</option>
                                                    <option value="Family" {{ ($hotel->gender ?? '') === 'Family' ? 'selected' : '' }}>Family / Couple</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">No. of Rooms</label>
                                                <input type="number" name="hotels[{{ $loop->index }}][no_of_rooms]"
                                                    class="form-control form-control-sm" value="{{ $hotel->no_of_rooms ?: 1 }}" min="1">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Check In</label>
                                                <input type="date" name="hotels[{{ $loop->index }}][check_in]"
                                                    class="form-control form-control-sm" value="{{ $hotel->check_in }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Check Out</label>
                                                <input type="date" name="hotels[{{ $loop->index }}][check_out]"
                                                    class="form-control form-control-sm" value="{{ $hotel->check_out }}">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="hotel-block border rounded p-3 mb-3 bg-light-subtle">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                            <h6 class="text-primary mb-0 fw-bold"><i class="mdi mdi-hotel me-1"></i>Hotel Stay</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Location / Place</label>
                                                <select name="hotels[0][location]" class="form-select form-select-sm hotel-loc-select">
                                                    <option value="makkah">Makkah</option>
                                                    <option value="azizia">Azizia</option>
                                                    <option value="mina">Hajj Days (Mina / Arafat)</option>
                                                    <option value="madinah">Madinah</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Hotel / Building Name</label>
                                                <select class="form-select form-select-sm hotel-crud-select mb-1">
                                                    <option value="">-- Select from Hotel CRUD --</option>
                                                    @foreach ($hotels as $h)
                                                        <option value="{{ $h->name }}" data-place="{{ strtolower($h->place ?? '') }}">
                                                            {{ $h->name }} ({{ $h->place ?? 'Hotel' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="hotels[0][hotel_name]" class="form-control form-control-sm hotel-name-input" placeholder="Hotel name">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Nights</label>
                                                <input type="number" name="hotels[0][no_of_nights]" class="form-control form-control-sm" value="1" min="1">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Room Type</label>
                                                <select name="hotels[0][room_type]" class="form-select form-select-sm">
                                                    <option value="quad">Quad</option>
                                                    <option value="triple">Triple</option>
                                                    <option value="double">Double</option>
                                                    <option value="single">Single</option>
                                                    <option value="suite">Suite</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Room Number</label>
                                                <input type="text" name="hotels[0][room_number]" class="form-control form-control-sm hotel-room-number-input" placeholder="e.g. 101, 204">
                                                <div class="room-capacity-feedback mt-1 small"></div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Room Gender</label>
                                                <select name="hotels[0][gender]" class="form-select form-select-sm">
                                                    <option value="Any">Any / Mixed</option>
                                                    <option value="Male">Male Room (Men)</option>
                                                    <option value="Female">Female Room (Women)</option>
                                                    <option value="Family">Family / Couple</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">No. of Rooms</label>
                                                <input type="number" name="hotels[0][no_of_rooms]" class="form-control form-control-sm" value="1" min="1">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Check In</label>
                                                <input type="date" name="hotels[0][check_in]" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Check Out</label>
                                                <input type="date" name="hotels[0][check_out]" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev"><i
                                        class="mdi mdi-arrow-left me-1"></i> Prev</button>
                                <button type="button" class="btn btn-primary btn-next">Next <i
                                        class="mdi mdi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        {{-- TAB 7: Transport --}}
                        <div class="tab-pane fade" id="tab-transport">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fs-16 fw-bold text-dark m-0">🚌 Transportation & Route Schedule</h5>
                                    <small class="text-muted">Auto-loaded from selected package transportation sectors</small>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addRoute">
                                    <i class="mdi mdi-plus"></i> Add Route Segment
                                </button>
                            </div>

                            <div id="routesList">
                                @forelse($booking->transports as $i => $t)
                                    <div class="route-block border rounded p-3 mb-2 bg-light-subtle">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-primary fw-semibold small">
                                                <i class="mdi mdi-bus me-1"></i>Route Segment #{{ $loop->iteration }}
                                            </span>
                                            @if ($loop->index > 0)
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-route py-0 px-2" style="font-size:12px;">×</button>
                                            @endif
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Route</label>
                                                <input type="text" name="transports[{{ $i }}][route]"
                                                    class="form-control form-control-sm" value="{{ $t->route }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Transport Type</label>
                                                <select name="transports[{{ $i }}][transport_type]" class="form-select form-select-sm">
                                                    @foreach (['bus' => 'Bus / Coach', 'private_car' => 'Private Car', 'train' => 'Train', 'shared_van' => 'Shared Van', 'taxi' => 'Taxi', 'flight' => 'Flight'] as $val => $lbl)
                                                        <option value="{{ $val }}" {{ $t->transport_type == $val ? 'selected' : '' }}>
                                                            {{ $lbl }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Notes</label>
                                                <input type="text" name="transports[{{ $i }}][notes]"
                                                    class="form-control form-control-sm" value="{{ $t->notes }}">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="route-block border rounded p-3 mb-2 bg-light-subtle">
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Route</label>
                                                <input type="text" name="transports[0][route]" class="form-control form-control-sm"
                                                    placeholder="e.g. Karachi → Jeddah → Makkah → Madinah">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Transport Type</label>
                                                <select name="transports[0][transport_type]" class="form-select form-select-sm">
                                                    <option value="bus">Bus / Coach</option>
                                                    <option value="private_car">Private Car</option>
                                                    <option value="train">Train</option>
                                                    <option value="shared_van">Shared Van</option>
                                                    <option value="taxi">Taxi</option>
                                                    <option value="flight">Flight</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold" style="font-size:12px;">Notes</label>
                                                <input type="text" name="transports[0][notes]" class="form-control form-control-sm"
                                                    placeholder="e.g. Air Conditioned Private Buses">
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev"><i
                                        class="mdi mdi-arrow-left me-1"></i> Prev</button>
                                <button type="button" class="btn btn-primary btn-next">Next <i
                                        class="mdi mdi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        {{-- TAB 8: Visa --}}
                        <div class="tab-pane fade" id="tab-visa">
                            <p class="text-muted small mb-3">
                                <i class="mdi mdi-information me-1"></i>
                                Edit visa details for each passenger below.
                            </p>
                            <div id="visasList">
                                @foreach ($booking->visas as $i => $visa)
                                    <div class="visa-card" id="visa_card_{{ $i }}">
                                        <strong class="text-warning" style="font-size:13px;">
                                            <i class="mdi mdi-passport me-1"></i> Visa {{ $i + 1 }}
                                        </strong>
                                        <div class="row g-2 mt-1">
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Passport Number</label>
                                                <input type="text" name="visas[{{ $i }}][passport_number]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $visa->passport_number }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Full Name</label>
                                                <input type="text" name="visas[{{ $i }}][given_name]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $visa->given_name }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Date of Birth</label>
                                                <input type="date" name="visas[{{ $i }}][date_of_birth]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $visa->date_of_birth }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Company</label>
                                                <input type="text" name="visas[{{ $i }}][company]"
                                                    class="form-control form-control-sm" value="{{ $visa->company }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Send To</label>
                                                <select name="visas[{{ $i }}][send_to]"
                                                    class="form-select form-select-sm">
                                                    <option value="">-- Select --</option>
                                                    @foreach (['shirka' => 'Shirka', 'consulate' => 'Consulate', 'both' => 'Both'] as $val => $lbl)
                                                        <option value="{{ $val }}"
                                                            {{ $visa->send_to == $val ? 'selected' : '' }}>
                                                            {{ $lbl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" style="font-size:12px;">Status</label>
                                                <select name="visas[{{ $i }}][status]"
                                                    class="form-select form-select-sm">
                                                    @foreach (['pending' => 'Pending', 'submitted' => 'Submitted', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $lbl)
                                                        <option value="{{ $val }}"
                                                            {{ $visa->status == $val ? 'selected' : '' }}>
                                                            {{ $lbl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev"><i
                                        class="mdi mdi-arrow-left me-1"></i> Prev</button>
                                <button type="button" class="btn btn-primary btn-next">Next <i
                                        class="mdi mdi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        {{-- TAB 9: Costing --}}
                        <div class="tab-pane fade" id="tab-costing">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-primary">Package Cost (per person avg)</label>
                                    <input type="number" name="package_cost" id="package_cost"
                                        class="form-control calc fw-semibold"
                                        value="{{ old('package_cost', $booking->package_cost ?: '') }}"
                                        placeholder="0.00" step="0.01">
                                    <small class="text-muted">Auto-calculated from Room Sharing Breakdown.</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Visa Charges</label>
                                    <input type="number" name="visa_charges" id="visa_charges"
                                        class="form-control calc"
                                        value="{{ old('visa_charges', $booking->visa_charges ?: '') }}"
                                        placeholder="Enter visa charges" step="0.01">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Flight Charges</label>
                                    <input type="number" name="flight_charges" id="flight_charges"
                                        class="form-control calc"
                                        value="{{ old('flight_charges', $booking->flight_charges ?: '') }}"
                                        placeholder="Enter flight charges" step="0.01">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Other Charges</label>
                                    <input type="number" name="other_charges" id="other_charges"
                                        class="form-control calc"
                                        value="{{ old('other_charges', $booking->other_charges ?: '') }}"
                                        placeholder="Enter other charges" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-danger"><i class="mdi mdi-tag-minus me-1"></i>Discount (Minus)</label>
                                    <input type="number" name="discount" id="discount"
                                        class="form-control calc border-danger text-danger fw-bold"
                                        value="{{ old('discount', $booking->discount ?: '') }}"
                                        placeholder="0.00" step="0.01">
                                    <small class="text-danger">Subtracted from Total Amount</small>
                                </div>

                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm bg-light">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Persons</th>
                                                    <th>Pkg × Pax</th>
                                                    <th>Visa</th>
                                                    <th>Flight</th>
                                                    <th>Qurbani</th>
                                                    <th>Other</th>
                                                    <th class="text-danger">Discount (-)</th>
                                                    <th class="text-success">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong id="sum_pax">{{ $booking->no_of_pax }}</strong></td>
                                                    <td id="sum_pkg">{{ number_format(($booking->package_cost ?? 0) * ($booking->no_of_pax ?? 0), 2) }}</td>
                                                    <td id="sum_visa">{{ number_format($booking->visa_charges ?? 0, 2) }}</td>
                                                    <td id="sum_flight">{{ number_format($booking->flight_charges ?? 0, 2) }}</td>
                                                    <td id="sum_qurbani">{{ number_format($booking->qurbani_charges ?? 0, 2) }}</td>
                                                    <td id="sum_other">{{ number_format($booking->other_charges ?? 0, 2) }}</td>
                                                    <td class="text-danger fw-bold" id="sum_discount">{{ ($booking->discount ?? 0) > 0 ? ('-' . number_format($booking->discount, 2)) : '0.00' }}</td>
                                                    <td class="text-success fw-bold" id="sum_total">{{ number_format($booking->total_amount ?? 0, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Total Amount</label>
                                    <input type="number" name="total_amount" id="total_amount"
                                        class="form-control bg-light fw-bold fs-5 text-primary"
                                        value="{{ old('total_amount', $booking->total_amount ?: '') }}"
                                        placeholder="Auto calculated" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Total Received / Advance</label>
                                    <input type="number" name="total_received" id="total_received"
                                        class="form-control calc"
                                        value="{{ old('total_received', $booking->total_received ?: '') }}"
                                        placeholder="Enter amount received" step="0.01">
                                </div>

                                @php
                                    $bookingBalance = $booking->balance ?? 0;
                                    $bookingAbsBalance = abs($bookingBalance);
                                    $bookingBalanceSign = $bookingBalance < 0 ? '+' : '';
                                    $bookingBalanceText =
                                        $bookingBalance != 0
                                            ? $bookingBalanceSign . number_format($bookingAbsBalance, 0)
                                            : '';
                                @endphp

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        Balance Remaining
                                        @if ($bookingBalance > 0)
                                            <span class="badge bg-danger ms-1" style="font-size:10px;">Due</span>
                                        @elseif ($bookingBalance < 0)
                                            <span class="badge bg-success ms-1" style="font-size:10px;">Advance</span>
                                        @elseif ($booking->balance !== null)
                                            <span class="badge bg-success ms-1" style="font-size:10px;">Clear</span>
                                        @endif
                                    </label>

                                    <input type="text"
                                        class="form-control bg-light fw-bold fs-5 {{ $bookingBalance > 0 ? 'text-danger' : 'text-success' }}"
                                        value="{{ $bookingBalanceText }}" placeholder="Auto calculated" readonly>

                                    <input type="hidden" name="balance" id="balance"
                                        value="{{ old('balance', $booking->balance ?: '') }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('booking.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-success px-5">
                                        <i class="mdi mdi-content-save me-1"></i> Update Booking
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end tab-content --}}
                </form>
            </div>
        </div>
    </div>

    <style>
        .booking-tabs .nav-link {
            color: #555;
            font-size: 13px;
            padding: 8px 14px;
            border-bottom: none;
        }
        .booking-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
            border-color: #dee2e6 #dee2e6 #fff;
            background: #fff;
        }
        .tab-content { min-height: 380px; }

        /* Brochure Style Gold Cards */
        .pkg-pricing-card {
            border: 2px solid #e0c885;
            border-radius: 12px;
            overflow: hidden;
            background: #fffdf8;
            box-shadow: 0 2px 8px rgba(201,168,76,0.12);
        }
        .pkg-maktab-badge {
            background: #f1bf66;
            padding: 12px 14px;
            min-width: 90px;
            color: #1c1508;
            text-align: center;
        }
        .maktab-letter {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            font-family: 'Outfit', sans-serif;
        }
        .maktab-text {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .maktab-zone {
            font-size: 9.5px;
            font-weight: 800;
            white-space: nowrap;
        }
        .pkg-col { flex: 1; min-width: 100px; }
        .pkg-col-title {
            font-size: 11px;
            font-weight: 700;
            color: #1c1508;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .pkg-col-pkr {
            font-size: 15px;
            font-weight: 800;
            color: #1c1508;
            font-family: 'Outfit', sans-serif;
        }
        .pkg-col-usd {
            font-size: 11px;
            font-weight: 600;
            color: #826017;
        }
        .pkg-azizia-card {
            border: 2px solid #f1bf66;
            background: #fffdf5;
        }
        .pax-stepper .input-group-sm input { font-weight: 700; }

        .person-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 10px;
        }
        .visa-card {
            background: #fff8f0;
            border: 1px solid #fde8c8;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 10px;
        }
    </style>

    <script>
        const clientsData = @json($clients);
        const packagesData = @json($packages);
        const hotelsData = @json($hotels);
        const roomTypesData = @json($roomTypes ?? []);
        const existingVisas = @json($booking->visas);
        const transactionsPaid = {{ $transactionsPaid ?? 0 }};
        let pkgQurbaniPerHeadRate = {{ ($booking->package && $booking->package->qurbani_charges > 0) ? (float)$booking->package->qurbani_charges : (($booking->qurbani_qty > 0 && $booking->qurbani_charges > 0) ? (float)($booking->qurbani_charges / $booking->qurbani_qty) : 0) }};

        // ═══════════════════════════════════════
        // BOOKING FOR TOGGLE
        // ═══════════════════════════════════════
        const forClient = document.getElementById('for_client');
        const forCompany = document.getElementById('for_company');
        const clientBlock = document.getElementById('clientBlock');
        const companyBlock = document.getElementById('companyBlock');
        const clientSelectEl = document.getElementById('clientSelect');
        const companySelectEl = document.getElementById('companySelect');
        const personsNote = document.getElementById('personsBookingForNote');

        function isCompanyMode() {
            return forCompany.checked;
        }

        function applyBookingForVisibility() {
            if (forClient.checked) {
                clientBlock.classList.remove('d-none');
                companyBlock.classList.add('d-none');
                personsNote.classList.add('d-none');
            } else {
                companyBlock.classList.remove('d-none');
                clientBlock.classList.add('d-none');
                personsNote.classList.remove('d-none');
            }

            const firstCard = document.getElementById('person_card_0');
            if (firstCard) {
                const existingDropdownCol = firstCard.querySelector('.person-client-select')?.closest('.col-md-12');
                if (isCompanyMode() && existingDropdownCol) {
                    existingDropdownCol.remove();
                } else if (!isCompanyMode() && !firstCard.querySelector('.person-client-select')) {
                    const row = firstCard.querySelector('.row.g-2');
                    row.insertAdjacentHTML('afterbegin', `
                        <div class="col-md-12 mb-2">
                            <label class="form-label" style="font-size:12px;">Select Client to auto-fill</label>
                            <select class="form-select form-select-sm person-client-select" data-idx="0" onchange="fillPersonFromClient(this, 0)">
                                ${buildClientOptions(clientSelectEl.value)}
                            </select>
                        </div>`);
                }
            }
        }

        function toggleBookingFor() {
            if (forClient.checked) {
                companySelectEl.value = '';
            } else {
                clientSelectEl.value = '';
                document.getElementById('fill_passport').value = '';
                document.getElementById('fill_cnic').value = '';
                document.getElementById('fill_phone').value = '';
            }
            applyBookingForVisibility();
        }

        forClient.addEventListener('change', toggleBookingFor);
        forCompany.addEventListener('change', toggleBookingFor);

        applyBookingForVisibility();

        function buildClientOptions(selectedVal = '') {
            let opts = `<option value="">-- Manual Entry --</option>`;
            clientsData.forEach(c => {
                const name = c.name + (c.company_name ? ` (${c.company_name})` : '');
                opts += `<option value="${c.id}"
                    data-passport="${c.passport_number || ''}"
                    data-cnic="${c.cnic || ''}"
                    data-phone="${c.phone || ''}"
                    data-surname="${c.surname || ''}"
                    data-given-name="${c.given_name || c.name || ''}"
                    data-dob="${c.dob || ''}"
                    data-passport-exp="${c.passport_expiry_date || ''}"
                    ${selectedVal == c.id ? 'selected' : ''}>${name}</option>`;
            });
            return opts;
        }

        // ═══════════════════════════════════════
        // TAB NAVIGATION
        // ═══════════════════════════════════════
        const tabLinks = Array.from(document.querySelectorAll('#bookingTabs .nav-link'));

        function activateTab(idx) {
            if (idx < 0 || idx >= tabLinks.length) return;
            tabLinks[idx].click();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-next, .btn-prev');
            if (!btn) return;
            const cur = tabLinks.findIndex(t => t.classList.contains('active'));
            btn.classList.contains('btn-next') ? activateTab(cur + 1) : activateTab(cur - 1);
        });

        // ═══════════════════════════════════════
        // CLIENT SELECT → AUTO FILL
        // ═══════════════════════════════════════
        clientSelectEl.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            document.getElementById('fill_passport').value = opt.dataset.passport || '';
            document.getElementById('fill_cnic').value = opt.dataset.cnic || '';
            document.getElementById('fill_phone').value = opt.dataset.phone || '';

            const firstSel = document.querySelector('.person-client-select[data-idx="0"]');
            if (firstSel && this.value) {
                firstSel.value = this.value;
                fillPersonFromClient(firstSel, 0);
            }
        });

        // ═══════════════════════════════════════
        // PACKAGE SELECTION & BROCHURE RATE CARDS
        // ═══════════════════════════════════════
        const packageSelect = document.getElementById('packageSelect');
        const packageNameInput = document.getElementById('package_name');
        const packageYearInput = document.getElementById('package_year');
        const campSelect = document.getElementById('campSelect');
        const packageInfoCard = document.getElementById('packageInfoCard');
        const qurbaniOption = document.getElementById('qurbani_option');
        const qurbaniQty = document.getElementById('qurbani_qty');
        const qurbaniCharges = document.getElementById('qurbani_charges');
        const packageCostInput = document.getElementById('package_cost');

        let currentPkgRates = {
            c_quad: 0, c_triple: 0, c_double: 0,
            a_quad: 0, a_triple: 0, a_double: 0,
            az_quad: 0, az_triple: 0, az_double: 0
        };

        function formatPkr(num) {
            return Number(num || 0).toLocaleString('en-US');
        }

        function updateBrochureRates(pkg) {
            if (!pkg) {
                currentPkgRates = {
                    c_quad: 0, c_triple: 0, c_double: 0,
                    a_quad: 0, a_triple: 0, a_double: 0,
                    az_quad: 0, az_triple: 0, az_double: 0
                };

                document.getElementById('lbl_maktab_c_zone').textContent = 'ZONE —';
                document.getElementById('disp_c_quad_pkr').textContent = '0.00';
                document.getElementById('disp_c_triple_pkr').textContent = '0.00';
                document.getElementById('disp_c_double_pkr').textContent = '0.00';
                document.getElementById('disp_c_quad_usd').textContent = '$ 0.00';
                document.getElementById('disp_c_triple_usd').textContent = '$ 0.00';
                document.getElementById('disp_c_double_usd').textContent = '$ 0.00';

                document.getElementById('disp_a_quad_pkr').textContent = '0.00';
                document.getElementById('disp_a_triple_pkr').textContent = '0.00';
                document.getElementById('disp_a_double_pkr').textContent = '0.00';
                document.getElementById('disp_a_quad_usd').textContent = '$ 0.00';
                document.getElementById('disp_a_triple_usd').textContent = '$ 0.00';
                document.getElementById('disp_a_double_usd').textContent = '$ 0.00';

                document.getElementById('disp_az_quad_pkr').textContent = '0.00 PKR';
                document.getElementById('disp_az_triple_pkr').textContent = '0.00 PKR';
                document.getElementById('disp_az_double_pkr').textContent = '0.00 PKR';
                document.getElementById('disp_az_quad_usd').textContent = '$ 0.00';
                document.getElementById('disp_az_triple_usd').textContent = '$ 0.00';
                document.getElementById('disp_az_double_usd').textContent = '$ 0.00';

                calculateMultiRoomCost();
                return;
            }

            // Maktab C rates
            let cQuad = Number(pkg.maktab_c_quad_pkr || 0);
            if (cQuad === 0 && Number(pkg.adult_pkr || 0) > 0) cQuad = Number(pkg.adult_pkr);
            let cTriple = Number(pkg.maktab_c_triple_pkr || 0);
            let cDouble = Number(pkg.maktab_c_double_pkr || 0);

            let cQuadUsd = Number(pkg.maktab_c_quad_usd || 0);
            if (cQuadUsd === 0 && Number(pkg.adult_usd || 0) > 0) cQuadUsd = Number(pkg.adult_usd);
            let cTripleUsd = Number(pkg.maktab_c_triple_usd || 0);
            let cDoubleUsd = Number(pkg.maktab_c_double_usd || 0);

            // Maktab A rates
            let aQuad = Number(pkg.maktab_a_quad_pkr || 0);
            let aTriple = Number(pkg.maktab_a_triple_pkr || 0);
            let aDouble = Number(pkg.maktab_a_double_pkr || 0);
            let aQuadUsd = Number(pkg.maktab_a_quad_usd || 0);
            let aTripleUsd = Number(pkg.maktab_a_triple_usd || 0);
            let aDoubleUsd = Number(pkg.maktab_a_double_usd || 0);

            // Azizia Separate Room rates
            let azQuad = Number(pkg.azizia_quad_pkr || 0);
            let azTriple = Number(pkg.azizia_triple_pkr || 0);
            let azDouble = Number(pkg.azizia_double_pkr || 0);
            let azQuadUsd = Number(pkg.azizia_quad_usd || 0);
            let azTripleUsd = Number(pkg.azizia_triple_usd || 0);
            let azDoubleUsd = Number(pkg.azizia_double_usd || 0);

            currentPkgRates = {
                c_quad: cQuad,
                c_triple: cTriple,
                c_double: cDouble,
                a_quad: aQuad,
                a_triple: aTriple,
                a_double: aDouble,
                az_quad: azQuad,
                az_triple: azTriple,
                az_double: azDouble
            };

            // Update Maktab C Zone label
            const zoneLabel = pkg.camp_zone || pkg.zone || 'ZONE 5';
            document.getElementById('lbl_maktab_c_zone').textContent = zoneLabel;

            // Update Maktab C displays
            document.getElementById('disp_c_quad_pkr').textContent = cQuad > 0 ? (formatPkr(cQuad) + '.') : '0.00';
            document.getElementById('disp_c_triple_pkr').textContent = cTriple > 0 ? (formatPkr(cTriple) + '.') : '0.00';
            document.getElementById('disp_c_double_pkr').textContent = cDouble > 0 ? (formatPkr(cDouble) + '.') : '0.00';
            document.getElementById('disp_c_quad_usd').textContent = cQuadUsd > 0 ? ('$ ' + formatPkr(cQuadUsd) + '.') : '$ 0.00';
            document.getElementById('disp_c_triple_usd').textContent = cTripleUsd > 0 ? ('$ ' + formatPkr(cTripleUsd) + '.') : '$ 0.00';
            document.getElementById('disp_c_double_usd').textContent = cDoubleUsd > 0 ? ('$ ' + formatPkr(cDoubleUsd) + '.') : '$ 0.00';

            // Update Maktab A displays
            document.getElementById('disp_a_quad_pkr').textContent = aQuad > 0 ? (formatPkr(aQuad) + '.') : '0.00';
            document.getElementById('disp_a_triple_pkr').textContent = aTriple > 0 ? (formatPkr(aTriple) + '.') : '0.00';
            document.getElementById('disp_a_double_pkr').textContent = aDouble > 0 ? (formatPkr(aDouble) + '.') : '0.00';
            document.getElementById('disp_a_quad_usd').textContent = aQuadUsd > 0 ? ('$ ' + formatPkr(aQuadUsd) + '.') : '$ 0.00';
            document.getElementById('disp_a_triple_usd').textContent = aTripleUsd > 0 ? ('$ ' + formatPkr(aTripleUsd) + '.') : '$ 0.00';
            document.getElementById('disp_a_double_usd').textContent = aDoubleUsd > 0 ? ('$ ' + formatPkr(aDoubleUsd) + '.') : '$ 0.00';

            // Update Azizia displays
            document.getElementById('disp_az_quad_pkr').textContent = azQuad > 0 ? (formatPkr(azQuad) + '. PKR') : '0.00 PKR';
            document.getElementById('disp_az_triple_pkr').textContent = azTriple > 0 ? (formatPkr(azTriple) + '. PKR') : '0.00 PKR';
            document.getElementById('disp_az_double_pkr').textContent = azDouble > 0 ? (formatPkr(azDouble) + '. PKR') : '0.00 PKR';
            document.getElementById('disp_az_quad_usd').textContent = azQuadUsd > 0 ? ('Per Person $ ' + formatPkr(azQuadUsd)) : '$ 0.00';
            document.getElementById('disp_az_triple_usd').textContent = azTripleUsd > 0 ? ('Per Person $ ' + formatPkr(azTripleUsd)) : '$ 0.00';
            document.getElementById('disp_az_double_usd').textContent = azDoubleUsd > 0 ? ('Per Person $ ' + formatPkr(azDoubleUsd)) : '$ 0.00';

            calculateMultiRoomCost();
        }

        function calculateMultiRoomCost() {
            const cq = parseInt(document.getElementById('c_quad_pax').value) || 0;
            const ct = parseInt(document.getElementById('c_triple_pax').value) || 0;
            const cd = parseInt(document.getElementById('c_double_pax').value) || 0;

            const aq = parseInt(document.getElementById('a_quad_pax').value) || 0;
            const at = parseInt(document.getElementById('a_triple_pax').value) || 0;
            const ad = parseInt(document.getElementById('a_double_pax').value) || 0;

            const azq = parseInt(document.getElementById('az_quad_pax').value) || 0;
            const azt = parseInt(document.getElementById('az_triple_pax').value) || 0;
            const azd = parseInt(document.getElementById('az_double_pax').value) || 0;

            const totalMainPax = cq + ct + cd + aq + at + ad;

            const totalCost = (cq * currentPkgRates.c_quad)
                + (ct * currentPkgRates.c_triple)
                + (cd * currentPkgRates.c_double)
                + (aq * currentPkgRates.a_quad)
                + (at * currentPkgRates.a_triple)
                + (ad * currentPkgRates.a_double)
                + (azq * currentPkgRates.az_quad)
                + (azt * currentPkgRates.az_triple)
                + (azd * currentPkgRates.az_double);

            let parts = [];
            if (cq > 0) parts.push(`Maktab C Quad (${cq} × PKR ${formatPkr(currentPkgRates.c_quad)})`);
            if (ct > 0) parts.push(`Maktab C Triple (${ct} × PKR ${formatPkr(currentPkgRates.c_triple)})`);
            if (cd > 0) parts.push(`Maktab C Double (${cd} × PKR ${formatPkr(currentPkgRates.c_double)})`);
            if (aq > 0) parts.push(`Maktab A Quad (${aq} × PKR ${formatPkr(currentPkgRates.a_quad)})`);
            if (at > 0) parts.push(`Maktab A Triple (${at} × PKR ${formatPkr(currentPkgRates.a_triple)})`);
            if (ad > 0) parts.push(`Maktab A Double (${ad} × PKR ${formatPkr(currentPkgRates.a_double)})`);
            if (azq > 0 || azt > 0 || azd > 0) {
                const azTotal = (azq * currentPkgRates.az_quad) + (azt * currentPkgRates.az_triple) + (azd * currentPkgRates.az_double);
                parts.push(`Azizia Room Addon (PKR ${formatPkr(azTotal)})`);
            }

            const breakdownText = parts.length > 0 ? parts.join(' + ') : 'No room sharing pax selected.';
            document.getElementById('room_calculation_breakdown_text').textContent = breakdownText;
            document.getElementById('room_total_cost_display').textContent = 'PKR ' + formatPkr(totalCost);
            document.getElementById('room_total_pax_display').textContent = totalMainPax + ' Pax';

            if (totalMainPax > 0) {
                document.getElementById('no_of_pax').value = totalMainPax;
                const avgPerPax = totalCost / totalMainPax;
                packageCostInput.value = avgPerPax.toFixed(2);

                const hasC = (cq + ct + cd) > 0;
                const hasA = (aq + at + ad) > 0;
                if (hasC && hasA) {
                    campSelect.value = 'Maktab A & C (Combo)';
                } else if (hasA) {
                    campSelect.value = 'Maktab A (Zone 1/2)';
                } else if (hasC) {
                    campSelect.value = 'Maktab C (Zone 5)';
                }
                syncCampCategoryPreview();

                if (qurbaniOption.value !== 'not_included' && pkgQurbaniPerHeadRate > 0) {
                    qurbaniQty.value = totalMainPax;
                    qurbaniCharges.value = (pkgQurbaniPerHeadRate * totalMainPax).toFixed(2);
                }

                rebuildPersonsList(totalMainPax);
                syncFlightPersons();
                syncVisas();
            } else {
                syncCampCategoryPreview();
            }

            calcTotal();
        }

        function syncCampCategoryPreview() {
            const previewCampEl = document.getElementById('preview_pkg_camp');
            if (!previewCampEl) return;

            const selectedCampVal = (campSelect ? campSelect.value : '').trim();

            const cq = parseInt(document.getElementById('c_quad_pax')?.value) || 0;
            const ct = parseInt(document.getElementById('c_triple_pax')?.value) || 0;
            const cd = parseInt(document.getElementById('c_double_pax')?.value) || 0;
            const aq = parseInt(document.getElementById('a_quad_pax')?.value) || 0;
            const at = parseInt(document.getElementById('a_triple_pax')?.value) || 0;
            const ad = parseInt(document.getElementById('a_double_pax')?.value) || 0;

            const hasC = (cq + ct + cd) > 0;
            const hasA = (aq + at + ad) > 0;

            if (selectedCampVal) {
                if (selectedCampVal.toLowerCase().includes('combo') || (hasC && hasA)) {
                    previewCampEl.textContent = 'Maktab A & C (Combo)';
                } else if (selectedCampVal.toLowerCase().includes('maktab a') || selectedCampVal.toLowerCase().includes('zone 1') || (hasA && !hasC)) {
                    previewCampEl.textContent = 'Maktab A (Zone 1/2)';
                } else if (selectedCampVal.toLowerCase().includes('maktab c') || selectedCampVal.toLowerCase().includes('zone 5') || (hasC && !hasA)) {
                    previewCampEl.textContent = 'Maktab C (Zone 5)';
                } else {
                    previewCampEl.textContent = selectedCampVal;
                }
            } else if (hasC && hasA) {
                previewCampEl.textContent = 'Maktab A & C (Combo)';
            } else if (hasA) {
                previewCampEl.textContent = 'Maktab A (Zone 1/2)';
            } else if (hasC) {
                previewCampEl.textContent = 'Maktab C (Zone 5)';
            } else {
                const pkgId = parseInt(packageSelect?.value);
                const pkg = (typeof packagesData !== 'undefined') ? packagesData.find(p => p.id === pkgId) : null;
                if (pkg && pkg.camp_category) {
                    previewCampEl.textContent = 'Maktab ' + pkg.camp_category;
                } else {
                    previewCampEl.textContent = '—';
                }
            }
        }

        if (campSelect) {
            campSelect.addEventListener('change', syncCampCategoryPreview);
        }

        // Stepper buttons
        document.addEventListener('click', function(e) {
            const plusBtn = e.target.closest('.btn-step-plus');
            const minusBtn = e.target.closest('.btn-step-minus');
            if (plusBtn) {
                const targetId = plusBtn.dataset.target;
                const input = document.getElementById(targetId);
                if (input) {
                    input.value = (parseInt(input.value) || 0) + 1;
                    calculateMultiRoomCost();
                }
            } else if (minusBtn) {
                const targetId = minusBtn.dataset.target;
                const input = document.getElementById(targetId);
                if (input && (parseInt(input.value) || 0) > 0) {
                    input.value = parseInt(input.value) - 1;
                    calculateMultiRoomCost();
                }
            }
        });

        document.querySelectorAll('.room-pax-input').forEach(input => {
            input.addEventListener('input', calculateMultiRoomCost);
        });

        // Qurbani Option & Qty Interactions
        qurbaniOption.addEventListener('change', function() {
            const pax = parseInt(document.getElementById('no_of_pax').value) || 1;
            if (this.value === 'not_included') {
                qurbaniQty.value = 0;
                qurbaniCharges.value = (0).toFixed(2);
            } else {
                if (parseInt(qurbaniQty.value) === 0) {
                    qurbaniQty.value = pax;
                }
                const qty = parseInt(qurbaniQty.value) || pax;
                if (pkgQurbaniPerHeadRate > 0) {
                    qurbaniCharges.value = (pkgQurbaniPerHeadRate * qty).toFixed(2);
                }
            }
            calcTotal();
        });

        qurbaniQty.addEventListener('input', function() {
            const qty = parseInt(this.value) || 0;
            if (pkgQurbaniPerHeadRate > 0) {
                qurbaniCharges.value = (pkgQurbaniPerHeadRate * qty).toFixed(2);
            }
            calcTotal();
        });

        qurbaniCharges.addEventListener('input', function() {
            const totalChg = parseFloat(this.value) || 0;
            const qty = parseInt(qurbaniQty.value) || 1;
            if (qty > 0) {
                pkgQurbaniPerHeadRate = totalChg / qty;
            }
            calcTotal();
        });

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function selectOrAddAirline(selectEl, airlineName) {
            if (!selectEl || !airlineName) return;
            const nameClean = airlineName.trim().toLowerCase();
            let found = false;
            for (let opt of selectEl.options) {
                const optVal = (opt.value || '').toLowerCase();
                const optText = (opt.text || '').toLowerCase();
                if (optVal === nameClean || optText.includes(nameClean) || nameClean.includes(optVal)) {
                    opt.selected = true;
                    found = true;
                    break;
                }
            }
            if (!found && airlineName.trim()) {
                const newOpt = new Option(airlineName.trim(), airlineName.trim(), true, true);
                selectEl.add(newOpt);
            }
        }

        // ═══════════════════════════════════════
        // DYNAMIC HOTEL CRUD SELECTION & FILTER
        // ═══════════════════════════════════════
        function buildHotelOptions(placeFilter = '', selectedHotel = '') {
            let opts = `<option value="">-- Select from Hotel CRUD --</option>`;
            const filter = (placeFilter || '').toLowerCase().trim();
            hotelsData.forEach(h => {
                const hPlace = (h.place || '').toLowerCase();
                let match = true;
                if (filter) {
                    if (filter === 'makkah') match = hPlace.includes('makkah') || hPlace.includes('mecca');
                    else if (filter === 'madinah') match = hPlace.includes('madinah') || hPlace.includes('medina');
                    else if (filter === 'azizia') match = hPlace.includes('azizia');
                    else if (filter === 'mina') match = hPlace.includes('mina') || hPlace.includes('arafat') || hPlace.includes('hajj');
                }
                const selected = (selectedHotel && (h.name.toLowerCase() === selectedHotel.toLowerCase())) ? 'selected' : '';
                opts += `<option value="${escapeHtml(h.name)}" data-place="${escapeHtml(hPlace)}" ${selected} ${match ? '' : 'style="display:none;"'}>
                    ${escapeHtml(h.name)} (${escapeHtml(h.place || 'Hotel')})
                </option>`;
            });
            return opts;
        }

        function buildRoomTypeOptions(selectedVal = 'quad') {
            let opts = '';
            const cleanSel = (selectedVal || 'quad').toLowerCase().trim();
            if (roomTypesData && roomTypesData.length > 0) {
                roomTypesData.forEach(rt => {
                    const val = rt.code || rt.name.toLowerCase();
                    const isSel = (val === cleanSel || rt.name.toLowerCase() === cleanSel) ? 'selected' : '';
                    opts += `<option value="${escapeHtml(val)}" ${isSel}>${escapeHtml(rt.name)}</option>`;
                });
            } else {
                const defaults = ['Quad', 'Triple', 'Double', 'Single', 'Suite', 'Sharing'];
                defaults.forEach(d => {
                    const val = d.toLowerCase();
                    const isSel = val === cleanSel ? 'selected' : '';
                    opts += `<option value="${val}" ${isSel}>${d}</option>`;
                });
            }
            return opts;
        }

        // Global hotel change listener
        document.getElementById('hotelsList').addEventListener('change', function(e) {
            if (e.target.classList.contains('hotel-loc-select')) {
                const block = e.target.closest('.hotel-block');
                const crudSel = block.querySelector('.hotel-crud-select');
                if (crudSel) {
                    const loc = e.target.value;
                    const curVal = crudSel.value;
                    crudSel.innerHTML = buildHotelOptions(loc, curVal);
                }
            } else if (e.target.classList.contains('hotel-crud-select')) {
                const block = e.target.closest('.hotel-block');
                const nameInput = block.querySelector('.hotel-name-input');
                if (nameInput && e.target.value) {
                    nameInput.value = e.target.value;
                }
            }
        });

        function populateHotelsFromPackage(pkg) {
            const list = document.getElementById('hotelsList');
            if (!list) return;

            if (pkg && pkg.accommodations && pkg.accommodations.length > 0) {
                list.innerHTML = '';
                pkg.accommodations.forEach((acc, idx) => {
                    const place = acc.place || '';
                    const hotelName = acc.package_a_hotel || acc.hotel || acc.package_b_hotel || place || '';

                    // Determine location enum
                    let loc = 'other';
                    const placeLower = (place + ' ' + hotelName).toLowerCase();
                    if (placeLower.includes('makkah') || placeLower.includes('mecca')) {
                        loc = 'makkah';
                    } else if (placeLower.includes('madinah') || placeLower.includes('medina')) {
                        loc = 'madinah';
                    } else if (placeLower.includes('azizia')) {
                        loc = 'azizia';
                    } else if (placeLower.includes('mina') || placeLower.includes('arafat')) {
                        loc = 'mina';
                    }

                    const checkIn = acc.check_in ? acc.check_in.substring(0, 10) : '';
                    const checkOut = acc.check_out ? acc.check_out.substring(0, 10) : '';

                    let nights = 1;
                    if (checkIn && checkOut) {
                        const diffTime = Math.abs(new Date(checkOut) - new Date(checkIn));
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        if (diffDays > 0) nights = diffDays;
                    } else if (acc.nights) {
                        nights = acc.nights;
                    } else if (acc.days) {
                        nights = acc.days;
                    }

                    const segmentTitle = place || (loc === 'makkah' ? 'Makkah Hotel' : (loc === 'madinah' ? 'Madinah Hotel' : (loc === 'azizia' ? 'Azizia Hotel' : 'Hotel / Stay')));
                    const extraNotes = [acc.note, acc.sharing, acc.food_package, acc.sharing_type].filter(Boolean).join(' · ');

                    const html = `
                    <div class="hotel-block border rounded p-3 mb-3 bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                            <div>
                                <h6 class="text-primary mb-0 d-inline-block fw-bold">
                                    <i class="mdi mdi-hotel me-1"></i>${escapeHtml(segmentTitle)}
                                </h6>
                                ${extraNotes ? `<span class="badge bg-warning-subtle text-dark ms-2 border" style="font-size:11px;">${escapeHtml(extraNotes)}</span>` : ''}
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-hotel py-0 px-2" style="font-size:12px;">× Remove</button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" style="font-size:12px;">Location / Place</label>
                                <select name="hotels[${idx}][location]" class="form-select form-select-sm hotel-loc-select">
                                    <option value="makkah" ${loc === 'makkah' ? 'selected' : ''}>Makkah</option>
                                    <option value="azizia" ${loc === 'azizia' ? 'selected' : ''}>Azizia</option>
                                    <option value="mina" ${loc === 'mina' ? 'selected' : ''}>Hajj Days (Mina / Arafat)</option>
                                    <option value="madinah" ${loc === 'madinah' ? 'selected' : ''}>Madinah</option>
                                    <option value="other" ${loc === 'other' ? 'selected' : ''}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" style="font-size:12px;">Hotel / Building Name</label>
                                <select class="form-select form-select-sm hotel-crud-select mb-1">
                                    ${buildHotelOptions(loc, hotelName)}
                                </select>
                                <input type="text" name="hotels[${idx}][hotel_name]" class="form-control form-control-sm hotel-name-input"
                                       value="${escapeHtml(hotelName)}" placeholder="Hotel name">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold" style="font-size:12px;">Nights</label>
                                <input type="number" name="hotels[${idx}][no_of_nights]" class="form-control form-control-sm"
                                       value="${nights}" min="1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold" style="font-size:12px;">Room Type</label>
                                <select name="hotels[${idx}][room_type]" class="form-select form-select-sm">
                                    ${buildRoomTypeOptions('quad')}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold" style="font-size:12px;">Room Number</label>
                                <input type="text" name="hotels[${idx}][room_number]" class="form-control form-control-sm hotel-room-number-input"
                                       placeholder="e.g. 101, 204">
                                <div class="room-capacity-feedback mt-1 small"></div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold" style="font-size:12px;">Room Gender</label>
                                <select name="hotels[${idx}][gender]" class="form-select form-select-sm">
                                    <option value="Any">Any / Mixed</option>
                                    <option value="Male">Male Room (Men)</option>
                                    <option value="Female">Female Room (Women)</option>
                                    <option value="Family">Family / Couple</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold" style="font-size:12px;">No. of Rooms</label>
                                <input type="number" name="hotels[${idx}][no_of_rooms]" class="form-control form-control-sm"
                                       value="1" min="1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold" style="font-size:12px;">Check In</label>
                                <input type="date" name="hotels[${idx}][check_in]" class="form-control form-control-sm"
                                       value="${checkIn}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold" style="font-size:12px;">Check Out</label>
                                <input type="date" name="hotels[${idx}][check_out]" class="form-control form-control-sm"
                                       value="${checkOut}">
                            </div>
                        </div>
                    </div>`;
                    list.insertAdjacentHTML('beforeend', html);
                });
                hotelIdx = pkg.accommodations.length;
            }
        }

        function populateFlightsFromPackage(pkg) {
            if (!pkg) return;

            const depDate = document.getElementById('departure_date');
            const depFlight = document.getElementById('departure_flight');
            const depTime = document.getElementById('departure_time');
            const depAirline = document.getElementById('departure_airline');
            const depPnr = document.getElementById('departure_pnr');

            const arrDate = document.getElementById('arrival_date');
            const arrFlight = document.getElementById('arrival_flight');
            const arrTime = document.getElementById('arrival_time');
            const arrAirline = document.getElementById('arrival_airline');
            const arrPnr = document.getElementById('arrival_pnr');

            const flights = pkg.transport_flights || pkg.transportFlights || [];

            if (flights.length > 0) {
                const f1 = flights[0];
                if (f1) {
                    if (f1.departure_date && depDate) depDate.value = f1.departure_date.substring(0, 10);
                    if (f1.flight_no && depFlight) depFlight.value = f1.flight_no;
                    if (f1.departure_time && depTime) depTime.value = f1.departure_time.substring(0, 5);
                    if (f1.airline && depAirline) selectOrAddAirline(depAirline, f1.airline);
                    if (f1.pnr_no && depPnr) depPnr.value = f1.pnr_no;
                }

                const f2 = flights.length > 1 ? flights[flights.length - 1] : f1;
                if (f2) {
                    if (f2.arrival_date && arrDate) arrDate.value = f2.arrival_date.substring(0, 10);
                    else if (f2.departure_date && arrDate && flights.length > 1) arrDate.value = f2.departure_date.substring(0, 10);
                    if (f2.flight_no && arrFlight) arrFlight.value = f2.flight_no;
                    if (f2.arrival_time && arrTime) arrTime.value = f2.arrival_time.substring(0, 5);
                    else if (f2.departure_time && arrTime && flights.length > 1) arrTime.value = f2.departure_time.substring(0, 5);
                    if (f2.airline && arrAirline) selectOrAddAirline(arrAirline, f2.airline);
                    if (f2.pnr_no && arrPnr) arrPnr.value = f2.pnr_no;
                }
            } else {
                if (pkg.accommodations && pkg.accommodations.length > 0) {
                    const firstAcc = pkg.accommodations[0];
                    const lastAcc = pkg.accommodations[pkg.accommodations.length - 1];

                    if (firstAcc && firstAcc.check_in && depDate && !depDate.value) {
                        depDate.value = firstAcc.check_in.substring(0, 10);
                    }
                    if (lastAcc && lastAcc.check_out && arrDate && !arrDate.value) {
                        arrDate.value = lastAcc.check_out.substring(0, 10);
                    }
                }
            }
        }

        function populateTransportsFromPackage(pkg) {
            const list = document.getElementById('routesList');
            if (!list) return;

            const transports = pkg.transports || [];
            if (transports.length > 0) {
                list.innerHTML = '';
                transports.forEach((t, idx) => {
                    const route = t.route || '';
                    let tType = 'bus';
                    const rawType = ((t.type || '') + ' ' + (t.vehicle || '')).toLowerCase();
                    if (rawType.includes('train')) tType = 'train';
                    else if (rawType.includes('car') || rawType.includes('private')) tType = 'private_car';
                    else if (rawType.includes('flight') || rawType.includes('air')) tType = 'flight';
                    else if (rawType.includes('taxi')) tType = 'taxi';
                    else if (rawType.includes('van')) tType = 'shared_van';

                    const notes = [t.vehicle, t.type].filter(Boolean).join(' - ');

                    const html = `
                    <div class="route-block border rounded p-3 mb-2 bg-light-subtle">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-primary fw-semibold small">
                                <i class="mdi mdi-bus me-1"></i>Route Segment #${idx + 1}
                            </span>
                            ${idx > 0 ? '<button type="button" class="btn btn-outline-danger btn-sm remove-route py-0 px-2" style="font-size:12px;">×</button>' : ''}
                        </div>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label" style="font-size:12px;">Route</label>
                                <input type="text" name="transports[${idx}][route]" class="form-control form-control-sm"
                                       value="${escapeHtml(route)}" placeholder="Karachi → Makkah">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size:12px;">Transport Type</label>
                                <select name="transports[${idx}][transport_type]" class="form-select form-select-sm">
                                    <option value="bus" ${tType === 'bus' ? 'selected' : ''}>Bus / Coach</option>
                                    <option value="private_car" ${tType === 'private_car' ? 'selected' : ''}>Private Car</option>
                                    <option value="train" ${tType === 'train' ? 'selected' : ''}>Train</option>
                                    <option value="shared_van" ${tType === 'shared_van' ? 'selected' : ''}>Shared Van</option>
                                    <option value="taxi" ${tType === 'taxi' ? 'selected' : ''}>Taxi</option>
                                    <option value="flight" ${tType === 'flight' ? 'selected' : ''}>Flight</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:12px;">Notes</label>
                                <input type="text" name="transports[${idx}][notes]" class="form-control form-control-sm"
                                       value="${escapeHtml(notes)}" placeholder="Optional">
                            </div>
                        </div>
                    </div>`;
                    list.insertAdjacentHTML('beforeend', html);
                });
                routeIdx = transports.length;
            }
        }

        packageSelect.addEventListener('change', function() {
            const pkgId = parseInt(this.value);
            const pkg = packagesData.find(p => p.id === pkgId);

            if (pkg) {
                const pkgCode = pkg.code || pkg.package_number || '';
                const codeDisplay = document.getElementById('package_code_display');
                if (codeDisplay) codeDisplay.value = pkgCode;

                const previewCodeBadge = document.getElementById('preview_pkg_code');
                if (previewCodeBadge) previewCodeBadge.textContent = pkgCode ? 'CODE: ' + pkgCode : 'CODE: —';

                const previewCodeText = document.getElementById('preview_pkg_code_text');
                if (previewCodeText) previewCodeText.textContent = pkgCode || '—';

                packageInfoCard.classList.remove('d-none');
                packageNameInput.value = pkg.package_title || pkg.name || '';
                packageYearInput.value = pkg.year || pkg.gregorian_year || (new Date()).getFullYear();

                document.getElementById('preview_pkg_stay').textContent = pkg.stay_type || 'PACKAGE';
                syncCampCategoryPreview();
                document.getElementById('preview_pkg_duration').textContent = pkg.stay_duration || (pkg.days ? pkg.days + ' Days' : '—');
                document.getElementById('preview_pkg_sectors').textContent = (pkg.departure_sector || 'KHI') + ' ➔ ' + (pkg.arrival_sector || 'JED/MED');
                document.getElementById('preview_pkg_qurbani').textContent = pkg.qurbani_status || 'Not Included (Nusuk Masar)';

                if (pkg.qurbani_status && pkg.qurbani_status.toLowerCase().includes('included')) {
                    qurbaniOption.value = 'included';
                }

                if (pkg.qurbani_charges && Number(pkg.qurbani_charges) > 0) {
                    pkgQurbaniPerHeadRate = Number(pkg.qurbani_charges);
                    const pax = parseInt(document.getElementById('no_of_pax').value) || 1;
                    qurbaniQty.value = pax;
                    qurbaniCharges.value = (pkgQurbaniPerHeadRate * pax).toFixed(2);
                } else {
                    pkgQurbaniPerHeadRate = 0;
                }

                updateBrochureRates(pkg);

                // 🌟 AUTO SELECT HOTELS, FLIGHTS & TRANSPORTS FROM PACKAGE 🌟
                populateHotelsFromPackage(pkg);
                populateFlightsFromPackage(pkg);
                populateTransportsFromPackage(pkg);
            } else {
                const codeDisplay = document.getElementById('package_code_display');
                if (codeDisplay) codeDisplay.value = '';

                const previewCodeBadge = document.getElementById('preview_pkg_code');
                if (previewCodeBadge) previewCodeBadge.textContent = 'CODE: —';

                const previewCodeText = document.getElementById('preview_pkg_code_text');
                if (previewCodeText) previewCodeText.textContent = '—';

                packageInfoCard.classList.add('d-none');
                updateBrochureRates(null);
            }

            calcTotal();
        });

        // ═══════════════════════════════════════
        // BUILD PERSON ROWS (WITH FULL DETAILS)
        // ═══════════════════════════════════════
        function buildPersonRow(idx, isFirst = false) {
            const companyMode = isCompanyMode();
            const label = isFirst ?
                (companyMode ? 'Main Passenger' : 'Main Passenger (Client)') :
                `Passenger ${idx + 1}`;

            const clientDropdown = (isFirst && !companyMode) ? `
                <div class="col-md-12 mb-2">
                    <label class="form-label" style="font-size:12px;">Select Client to auto-fill</label>
                    <select class="form-select form-select-sm person-client-select" data-idx="${idx}" onchange="fillPersonFromClient(this, ${idx})">
                        ${buildClientOptions(clientSelectEl.value)}
                    </select>
                </div>` : '';

            return `
            <div class="person-card" id="person_card_${idx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <strong class="text-primary" style="font-size:13px;">
                        <i class="mdi mdi-account me-1"></i>${label}
                    </strong>
                    <span class="badge bg-light text-muted border">Person #${idx + 1}</span>
                </div>
                <div class="row g-2">
                    ${clientDropdown}
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Surname / Family Name</label>
                        <input type="text" name="persons[${idx}][surname]" id="person_surname_${idx}"
                               class="form-control form-control-sm" placeholder="e.g. Khan" oninput="updatePersonFullName(${idx})">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Given Name</label>
                        <input type="text" name="persons[${idx}][given_name]" id="person_given_name_${idx}"
                               class="form-control form-control-sm" placeholder="e.g. Muhammad" oninput="updatePersonFullName(${idx})">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Full Name</label>
                        <input type="text" name="persons[${idx}][full_name]" id="person_name_${idx}"
                               class="form-control form-control-sm" placeholder="Muhammad Khan" oninput="onPersonManualName(${idx})">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Date of Birth</label>
                        <input type="date" name="persons[${idx}][dob]" id="person_dob_${idx}"
                               class="form-control form-control-sm" onchange="syncVisas()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Passport #</label>
                        <input type="text" name="persons[${idx}][passport_number]" id="person_passport_${idx}"
                               class="form-control form-control-sm" placeholder="Passport #" oninput="onPersonPassportInput(${idx})">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Date of Issue</label>
                        <input type="date" name="persons[${idx}][date_of_issue]" id="person_passport_doi_${idx}"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Passport Expiry Date</label>
                        <input type="date" name="persons[${idx}][passport_expiry_date]" id="person_passport_exp_${idx}"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">CNIC</label>
                        <input type="text" name="persons[${idx}][cnic]" id="person_cnic_${idx}"
                               class="form-control form-control-sm" placeholder="XXXXX-XXXXXXX-X">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;">Gender</label>
                        <select name="persons[${idx}][gender]" id="person_gender_${idx}" class="form-select form-select-sm">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;">Hajj ID</label>
                        <input type="text" name="persons[${idx}][hajj_id]" id="person_hajj_id_${idx}"
                               class="form-control form-control-sm" placeholder="e.g. PW26057">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:12px;">HB #</label>
                        <input type="text" name="persons[${idx}][hb_number]" id="person_hb_number_${idx}"
                               class="form-control form-control-sm" placeholder="e.g. HB0001">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Phone</label>
                        <input type="text" name="persons[${idx}][phone]" id="person_phone_${idx}"
                               class="form-control form-control-sm" placeholder="+92 300 0000000">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;"><i class="mdi mdi-camera text-primary me-1"></i>Pilgrim Photo</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="file" name="persons[${idx}][photo]" id="person_photo_${idx}"
                                   class="form-control form-control-sm" accept="image/*" onchange="previewPersonPhoto(this, ${idx})">
                            <div id="person_photo_preview_${idx}" class="flex-shrink-0" style="width:34px; height:34px; border-radius:4px; border:1px dashed #ced4da; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8f9fa;">
                                <i class="mdi mdi-account text-muted fs-16"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        }

        function previewPersonPhoto(input, idx) {
            const previewBox = document.getElementById(`person_photo_preview_${idx}`);
            if (input.files && input.files[0] && previewBox) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewBox.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function updatePersonFullName(idx) {
            const sName = (document.getElementById(`person_surname_${idx}`)?.value || '').trim();
            const gName = (document.getElementById(`person_given_name_${idx}`)?.value || '').trim();
            const fullEl = document.getElementById(`person_name_${idx}`);
            if (fullEl) {
                fullEl.value = [gName, sName].filter(Boolean).join(' ');
            }
            syncFlightPersons();
            syncVisas();
        }

        function onPersonManualName(idx) {
            syncFlightPersons();
            syncVisas();
        }

        function onPersonPassportInput(idx) {
            syncFlightPersons();
            syncVisas();
        }

        function fillPersonFromClient(sel, idx) {
            const opt = sel.options[sel.selectedIndex];
            document.getElementById(`person_passport_${idx}`).value = opt.dataset.passport || '';
            document.getElementById(`person_cnic_${idx}`).value = opt.dataset.cnic || '';
            document.getElementById(`person_phone_${idx}`).value = opt.dataset.phone || '';
            if (opt.dataset.dob) document.getElementById(`person_dob_${idx}`).value = opt.dataset.dob;
            if (opt.dataset.passportExp) document.getElementById(`person_passport_exp_${idx}`).value = opt.dataset.passportExp;
            if (opt.dataset.surname) document.getElementById(`person_surname_${idx}`).value = opt.dataset.surname;
            if (opt.dataset.givenName) document.getElementById(`person_given_name_${idx}`).value = opt.dataset.givenName;

            const id = parseInt(sel.value);
            const c = clientsData.find(x => x.id === id);
            if (c) {
                document.getElementById(`person_name_${idx}`).value = c.name;
            }
            syncFlightPersons();
            syncVisas();
        }

        function rebuildPersonsList(newPax) {
            const container = document.getElementById('personsList');
            const currentCards = container.querySelectorAll('.person-card');
            const currentCount = currentCards.length;

            if (newPax < currentCount) {
                for (let i = currentCount - 1; i >= newPax; i--) {
                    const card = document.getElementById('person_card_' + i);
                    if (card) card.remove();
                }
                return;
            }

            for (let i = currentCount; i < newPax; i++) {
                container.insertAdjacentHTML('beforeend', buildPersonRow(i, i === 0));
            }
        }

        // ═══════════════════════════════════════
        // FLIGHT PERSONS
        // ═══════════════════════════════════════
        function syncFlightPersons() {
            const pax = parseInt(document.getElementById('no_of_pax').value) || 1;
            const list = document.getElementById('flightPersonsList');
            if (!list) return;
            list.innerHTML = '';
            for (let i = 0; i < pax; i++) {
                const personName = document.getElementById(`person_name_${i}`)?.value || `Passenger ${i + 1}`;
                const personPass = document.getElementById(`person_passport_${i}`)?.value || '';
                list.insertAdjacentHTML('beforeend', `
                <div class="border rounded p-3 mb-2 bg-light-subtle">
                    <strong class="text-primary" style="font-size:13px;">Passenger ${i + 1}: ${escapeHtml(personName)}</strong>
                    <div class="row g-2 mt-1">
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;">Passenger Name</label>
                            <input type="text" name="flight_persons[${i}][name]" class="form-control form-control-sm"
                                   value="${escapeHtml(personName)}" placeholder="Name on ticket">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;">Passport #</label>
                            <input type="text" name="flight_persons[${i}][passport]" class="form-control form-control-sm"
                                   value="${escapeHtml(personPass)}" placeholder="Passport #">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;">Ticket / Seat #</label>
                            <input type="text" name="flight_persons[${i}][ticket]" class="form-control form-control-sm"
                                   placeholder="e.g. 24A">
                        </div>
                    </div>
                </div>`);
            }
        }

        // ═══════════════════════════════════════
        // VISA
        // ═══════════════════════════════════════
        function syncVisas() {
            const pax = parseInt(document.getElementById('no_of_pax').value) || 1;
            const list = document.getElementById('visasList');
            if (!list) return;
            list.innerHTML = '';
            for (let i = 0; i < pax; i++) {
                const personName = document.getElementById(`person_name_${i}`)?.value || '';
                const personPass = document.getElementById(`person_passport_${i}`)?.value || '';
                const personDob = document.getElementById(`person_dob_${i}`)?.value || '';
                const v = existingVisas[i] || {};
                list.insertAdjacentHTML('beforeend', `
                <div class="visa-card" id="visa_card_${i}">
                    <strong class="text-warning" style="font-size:13px;">
                        <i class="mdi mdi-passport me-1"></i> Visa ${i + 1}: ${escapeHtml(personName || 'Passenger ' + (i + 1))}
                    </strong>
                    <div class="row g-2 mt-1">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Passport Number</label>
                            <input type="text" name="visas[${i}][passport_number]"
                                   class="form-control form-control-sm" value="${escapeHtml(personPass || v.passport_number || '')}" placeholder="Passport #">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Full Name</label>
                            <input type="text" name="visas[${i}][given_name]"
                                   class="form-control form-control-sm" value="${escapeHtml(personName || v.given_name || '')}" placeholder="Full Name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Date of Birth</label>
                            <input type="date" name="visas[${i}][date_of_birth]" class="form-control form-control-sm" value="${escapeHtml(personDob || (v.date_of_birth ? v.date_of_birth.substring(0, 10) : ''))}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Company</label>
                            <input type="text" name="visas[${i}][company]" class="form-control form-control-sm" value="${escapeHtml(v.company || '')}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Send To</label>
                            <select name="visas[${i}][send_to]" class="form-select form-select-sm">
                                <option value="">-- Select --</option>
                                <option value="shirka"    ${v.send_to === 'shirka'    ? 'selected' : ''}>Shirka</option>
                                <option value="consulate" ${v.send_to === 'consulate' ? 'selected' : ''}>Consulate</option>
                                <option value="both"      ${v.send_to === 'both'      ? 'selected' : ''}>Both</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Status</label>
                            <select name="visas[${i}][status]" class="form-select form-select-sm">
                                <option value="pending"   ${(v.status || 'pending') === 'pending'   ? 'selected' : ''}>Pending</option>
                                <option value="submitted" ${v.status === 'submitted' ? 'selected' : ''}>Submitted</option>
                                <option value="approved"  ${v.status === 'approved'  ? 'selected' : ''}>Approved</option>
                                <option value="rejected"  ${v.status === 'rejected'  ? 'selected' : ''}>Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>`);
            }
        }

        // ═══════════════════════════════════════
        // NO OF PAX CHANGE
        // ═══════════════════════════════════════
        document.getElementById('no_of_pax').addEventListener('input', function() {
            const val = parseInt(this.value) || 1;
            if (qurbaniOption.value !== 'not_included' && pkgQurbaniPerHeadRate > 0) {
                qurbaniQty.value = val;
                qurbaniCharges.value = (pkgQurbaniPerHeadRate * val).toFixed(2);
            }
            rebuildPersonsList(val);
            syncFlightPersons();
            syncVisas();
            calcTotal();
        });

        // ═══════════════════════════════════════
        // DYNAMIC HOTELS
        // ═══════════════════════════════════════
        let hotelIdx = {{ $booking->hotels->count() ?: 2 }};

        document.getElementById('addHotel').addEventListener('click', function() {
            document.getElementById('hotelsList').insertAdjacentHTML('beforeend', `
                <div class="hotel-block border rounded p-3 mb-3 bg-light-subtle">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                        <h6 class="text-primary mb-0 fw-bold"><i class="mdi mdi-hotel me-1"></i>Additional Hotel</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-hotel py-0 px-2" style="font-size:12px;">× Remove</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" style="font-size:12px;">Location / Place</label>
                            <select name="hotels[${hotelIdx}][location]" class="form-select form-select-sm hotel-loc-select">
                                <option value="makkah">Makkah</option>
                                <option value="azizia">Azizia</option>
                                <option value="mina">Hajj Days (Mina / Arafat)</option>
                                <option value="madinah">Madinah</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:12px;">Hotel / Building Name</label>
                            <select class="form-select form-select-sm hotel-crud-select mb-1">
                                ${buildHotelOptions('makkah')}
                            </select>
                            <input type="text" name="hotels[${hotelIdx}][hotel_name]" class="form-control form-control-sm hotel-name-input" placeholder="Hotel name">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold" style="font-size:12px;">Nights</label>
                            <input type="number" name="hotels[${hotelIdx}][no_of_nights]" class="form-control form-control-sm" value="1" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold" style="font-size:12px;">Room Type</label>
                            <select name="hotels[${hotelIdx}][room_type]" class="form-select form-select-sm">
                                ${buildRoomTypeOptions('quad')}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold" style="font-size:12px;">Room Number</label>
                            <input type="text" name="hotels[${hotelIdx}][room_number]" class="form-control form-control-sm hotel-room-number-input" placeholder="e.g. 101, 204">
                            <div class="room-capacity-feedback mt-1 small"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold" style="font-size:12px;">Room Gender</label>
                            <select name="hotels[${hotelIdx}][gender]" class="form-select form-select-sm">
                                <option value="Any">Any / Mixed</option>
                                <option value="Male">Male Room (Men)</option>
                                <option value="Female">Female Room (Women)</option>
                                <option value="Family">Family / Couple</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold" style="font-size:12px;">No. of Rooms</label>
                            <input type="number" name="hotels[${hotelIdx}][no_of_rooms]" class="form-control form-control-sm" value="1" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold" style="font-size:12px;">Check In</label>
                            <input type="date" name="hotels[${hotelIdx}][check_in]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold" style="font-size:12px;">Check Out</label>
                            <input type="date" name="hotels[${hotelIdx}][check_out]" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>`);
            hotelIdx++;
        });

        document.getElementById('hotelsList').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-hotel')) e.target.closest('.hotel-block').remove();
        });

        // ═══════════════════════════════════════
        // DYNAMIC ROUTES
        // ═══════════════════════════════════════
        let routeIdx = {{ $booking->transports->count() ?: 1 }};

        document.getElementById('addRoute').addEventListener('click', function() {
            document.getElementById('routesList').insertAdjacentHTML('beforeend', `
                <div class="route-block border rounded p-3 mb-2 bg-light-subtle">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-primary fw-semibold small">
                            <i class="mdi mdi-bus me-1"></i>Route Segment #${routeIdx + 1}
                        </span>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-route py-0 px-2" style="font-size:12px;">×</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label" style="font-size:12px;">Route</label>
                            <input type="text" name="transports[${routeIdx}][route]" class="form-control form-control-sm" placeholder="Route">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;">Transport Type</label>
                            <select name="transports[${routeIdx}][transport_type]" class="form-select form-select-sm">
                                <option value="bus">Bus / Coach</option>
                                <option value="private_car">Private Car</option>
                                <option value="train">Train</option>
                                <option value="shared_van">Shared Van</option>
                                <option value="taxi">Taxi</option>
                                <option value="flight">Flight</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Notes</label>
                            <input type="text" name="transports[${routeIdx}][notes]" class="form-control form-control-sm" placeholder="Notes">
                        </div>
                    </div>
                </div>`);
            routeIdx++;
        });

        document.getElementById('routesList').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-route')) e.target.closest('.route-block').remove();
        });

        // ═══════════════════════════════════════
        // FINANCIAL CALCULATIONS
        // ═══════════════════════════════════════
        function calcTotal() {
            const pax = parseInt(document.getElementById('no_of_pax').value) || 1;
            const pkg = parseFloat(document.getElementById('package_cost').value) || 0;
            const visa = parseFloat(document.getElementById('visa_charges').value) || 0;
            const flight = parseFloat(document.getElementById('flight_charges').value) || 0;
            const qurbani = parseFloat(document.getElementById('qurbani_charges')?.value) || 0;
            const other = parseFloat(document.getElementById('other_charges').value) || 0;
            const discount = parseFloat(document.getElementById('discount')?.value) || 0;
            const received = parseFloat(document.getElementById('total_received').value) || 0;

            const pkgTotal = pkg * pax;
            const subtotal = pkgTotal + visa + flight + qurbani + other;
            const total = Math.max(0, subtotal - discount);
            const balance = total - received - transactionsPaid;

            document.getElementById('total_amount').value = total.toFixed(2);
            document.getElementById('balance').value = balance.toFixed(2);

            if (document.getElementById('bar_pax')) document.getElementById('bar_pax').textContent = pax;
            if (document.getElementById('bar_adult')) document.getElementById('bar_adult').textContent = pkgTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('bar_visa')) document.getElementById('bar_visa').textContent = visa.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('bar_flight')) document.getElementById('bar_flight').textContent = flight.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('bar_qurbani')) document.getElementById('bar_qurbani').textContent = qurbani.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('bar_discount')) document.getElementById('bar_discount').textContent = discount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('bar_total')) document.getElementById('bar_total').textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('bar_balance')) document.getElementById('bar_balance').textContent = balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            if (document.getElementById('sum_pax')) document.getElementById('sum_pax').textContent = pax;
            if (document.getElementById('sum_pkg')) document.getElementById('sum_pkg').textContent = pkgTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('sum_visa')) document.getElementById('sum_visa').textContent = visa.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('sum_flight')) document.getElementById('sum_flight').textContent = flight.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('sum_qurbani')) document.getElementById('sum_qurbani').textContent = qurbani.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('sum_other')) document.getElementById('sum_other').textContent = other.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (document.getElementById('sum_discount')) document.getElementById('sum_discount').textContent = discount > 0 ? ('-' + discount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})) : '0.00';
            if (document.getElementById('sum_total')) document.getElementById('sum_total').textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        document.querySelectorAll('.calc').forEach(el => el.addEventListener('input', calcTotal));

        document.addEventListener('DOMContentLoaded', function() {
            if (packageSelect.value) {
                const initialPkg = packagesData.find(p => p.id === parseInt(packageSelect.value));
                if (initialPkg) {
                    const pkgCode = initialPkg.code || initialPkg.package_number || '';
                    const codeDisplay = document.getElementById('package_code_display');
                    if (codeDisplay) codeDisplay.value = pkgCode;

                    const previewCodeBadge = document.getElementById('preview_pkg_code');
                    if (previewCodeBadge) previewCodeBadge.textContent = pkgCode ? 'CODE: ' + pkgCode : 'CODE: —';

                    const previewCodeText = document.getElementById('preview_pkg_code_text');
                    if (previewCodeText) previewCodeText.textContent = pkgCode || '—';

                    packageInfoCard.classList.remove('d-none');
                    document.getElementById('preview_pkg_stay').textContent = initialPkg.stay_type || 'PACKAGE';
                    syncCampCategoryPreview();
                    document.getElementById('preview_pkg_duration').textContent = initialPkg.stay_duration || (initialPkg.days ? initialPkg.days + ' Days' : '—');
                    document.getElementById('preview_pkg_sectors').textContent = (initialPkg.departure_sector || 'KHI') + ' ➔ ' + (initialPkg.arrival_sector || 'JED/MED');
                    document.getElementById('preview_pkg_qurbani').textContent = initialPkg.qurbani_status || 'Not Included (Nusuk Masar)';
                    updateBrochureRates(initialPkg);
                }
            }
            syncFlightPersons();
            syncVisas();
            calcTotal();

            // Run initial capacity check for already loaded hotel blocks
            document.querySelectorAll('.hotel-block').forEach(block => {
                checkHotelRoomCapacity(block);
            });
        });

        // ═══════════════════════════════════════
        // LIVE HOTEL ROOM CAPACITY CHECK
        // ═══════════════════════════════════════
        let capacityCheckTimeout = null;

        function checkHotelRoomCapacity(hotelBlock) {
            if (!hotelBlock) return;
            const hotelNameInput = hotelBlock.querySelector('.hotel-name-input');
            const roomNumberInput = hotelBlock.querySelector('.hotel-room-number-input');
            const roomTypeSelect = hotelBlock.querySelector('select[name*="[room_type]"]');
            const checkInInput = hotelBlock.querySelector('input[name*="[check_in]"]');
            const checkOutInput = hotelBlock.querySelector('input[name*="[check_out]"]');
            const feedbackDiv = hotelBlock.querySelector('.room-capacity-feedback');

            const locationSelect = hotelBlock.querySelector('select[name*="[location]"]');
            const location = locationSelect ? locationSelect.value : '';
            const hotelSelect = hotelBlock.querySelector('.hotel-crud-select');
            let hotelName = hotelNameInput ? hotelNameInput.value.trim() : '';
            if (!hotelName && hotelSelect && hotelSelect.value) {
                hotelName = hotelSelect.options[hotelSelect.selectedIndex]?.text?.split('(')[0]?.trim() || hotelSelect.value;
            }
            const roomNumber = roomNumberInput.value.trim();
            const roomType = roomTypeSelect ? roomTypeSelect.value : '';
            const checkIn = checkInInput ? checkInInput.value : '';
            const checkOut = checkOutInput ? checkOutInput.value : '';
            const bookingId = '{{ $booking->id ?? '' }}';

            if (!roomNumber || (!hotelName && !location)) {
                feedbackDiv.innerHTML = '';
                roomNumberInput.style.borderColor = '';
                roomNumberInput.style.borderWidth = '';
                roomNumberInput.style.backgroundColor = '';
                roomNumberInput.removeAttribute('data-is-full');
                return;
            }

            feedbackDiv.innerHTML = '<span class="text-muted small"><i class="mdi mdi-loading mdi-spin me-1"></i>Checking room status...</span>';

            const params = new URLSearchParams({
                hotel_name: hotelName,
                room_number: roomNumber,
                location: location,
                room_type: roomType,
                check_in: checkIn,
                check_out: checkOut,
                booking_id: bookingId
            });

            fetch(`{{ route('api.hotel-room.check-capacity') }}?${params.toString()}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        feedbackDiv.innerHTML = '';
                        roomNumberInput.style.borderColor = '';
                        roomNumberInput.style.backgroundColor = '';
                        roomNumberInput.removeAttribute('data-is-full');
                        return;
                    }

                    if (data.is_full || data.is_overbooked) {
                        roomNumberInput.style.borderColor = '#dc3545';
                        roomNumberInput.style.borderWidth = '2px';
                        roomNumberInput.style.backgroundColor = '#fff5f5';
                        roomNumberInput.setAttribute('data-is-full', 'true');

                        feedbackDiv.innerHTML = `
                        <div class="alert alert-danger p-2 mt-2 mb-1 border-danger shadow-sm" style="font-size:12px; line-height:1.4;">
                            <div class="fw-bold text-danger d-flex align-items-center mb-1">
                                <i class="mdi mdi-alert-octagon fs-16 me-1"></i> ROOM IS FULL!
                            </div>
                            <div class="text-dark mb-2">${data.message}</div>
                            <div class="pt-1 border-top border-danger-subtle d-flex flex-wrap justify-content-between align-items-center gap-1">
                                <span class="text-muted small">To assign more pilgrims to this room:</span>
                                <a href="{{ route('report.rooming-list') }}" target="_blank" class="btn btn-xs btn-danger text-white py-0 px-2 fw-bold" style="font-size:11px;">
                                    <i class="mdi mdi-bed-empty me-1"></i> Increase Bed Capacity
                                </a>
                            </div>
                        </div>`;
                    } else if (data.occupied > 0) {
                        roomNumberInput.style.borderColor = '#ffc107';
                        roomNumberInput.style.borderWidth = '2px';
                        roomNumberInput.style.backgroundColor = '#fffdf0';
                        roomNumberInput.removeAttribute('data-is-full');

                        feedbackDiv.innerHTML = `
                        <div class="alert alert-warning p-2 mt-2 mb-1 border-warning" style="font-size:12px; line-height:1.4;">
                            <div class="fw-bold text-dark d-flex align-items-center mb-1">
                                <i class="mdi mdi-information me-1 text-warning"></i> Room Sharing Status:
                            </div>
                            <div class="text-dark">${data.message}</div>
                        </div>`;
                    } else {
                        roomNumberInput.style.borderColor = '#198754';
                        roomNumberInput.style.borderWidth = '1.5px';
                        roomNumberInput.style.backgroundColor = '#f0fff4';
                        roomNumberInput.removeAttribute('data-is-full');

                        feedbackDiv.innerHTML = `
                        <div class="text-success small mt-1 fw-bold">
                            <i class="mdi mdi-check-circle me-1"></i> ${data.message}
                        </div>`;
                    }
                })
                .catch(() => {
                    feedbackDiv.innerHTML = '';
                    roomNumberInput.removeAttribute('data-is-full');
                });
        }

        // ═══════════════════════════════════════
        // AUTOMATIC HOTEL NIGHTS & DATES RECALCULATION
        // ═══════════════════════════════════════
        function recalculateHotelNights(hotelBlock, source) {
            if (!hotelBlock) return;
            const checkInInput = hotelBlock.querySelector('input[name*="[check_in]"]');
            const checkOutInput = hotelBlock.querySelector('input[name*="[check_out]"]');
            const nightsInput = hotelBlock.querySelector('input[name*="[no_of_nights]"]');
            if (!checkInInput || !checkOutInput || !nightsInput) return;

            const inVal = checkInInput.value;
            const outVal = checkOutInput.value;

            if (source === 'check_in' || source === 'check_out') {
                if (inVal && outVal) {
                    const dIn = new Date(inVal + 'T00:00:00');
                    const dOut = new Date(outVal + 'T00:00:00');
                    if (!isNaN(dIn.getTime()) && !isNaN(dOut.getTime())) {
                        const diffTime = dOut.getTime() - dIn.getTime();
                        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
                        if (diffDays > 0) {
                            nightsInput.value = diffDays;
                        } else if (source === 'check_in') {
                            // If check_in is on or after check_out, advance check_out
                            const n = Math.max(1, parseInt(nightsInput.value) || 1);
                            const nextOut = new Date(dIn);
                            nextOut.setDate(nextOut.getDate() + n);
                            const yyyy = nextOut.getFullYear();
                            const mm = String(nextOut.getMonth() + 1).padStart(2, '0');
                            const dd = String(nextOut.getDate()).padStart(2, '0');
                            checkOutInput.value = `${yyyy}-${mm}-${dd}`;
                            nightsInput.value = n;
                        } else {
                            nightsInput.value = 1;
                        }
                    }
                } else if (inVal && !outVal) {
                    const n = Math.max(1, parseInt(nightsInput.value) || 1);
                    const dIn = new Date(inVal + 'T00:00:00');
                    if (!isNaN(dIn.getTime())) {
                        dIn.setDate(dIn.getDate() + n);
                        const yyyy = dIn.getFullYear();
                        const mm = String(dIn.getMonth() + 1).padStart(2, '0');
                        const dd = String(dIn.getDate()).padStart(2, '0');
                        checkOutInput.value = `${yyyy}-${mm}-${dd}`;
                    }
                }
            } else if (source === 'nights') {
                const n = parseInt(nightsInput.value);
                if (n && n > 0 && inVal) {
                    const dIn = new Date(inVal + 'T00:00:00');
                    if (!isNaN(dIn.getTime())) {
                        dIn.setDate(dIn.getDate() + n);
                        const yyyy = dIn.getFullYear();
                        const mm = String(dIn.getMonth() + 1).padStart(2, '0');
                        const dd = String(dIn.getDate()).padStart(2, '0');
                        checkOutInput.value = `${yyyy}-${mm}-${dd}`;
                    }
                }
            }
        }

        const hotelsContainer = document.getElementById('hotelsList');
        if (hotelsContainer) {
            hotelsContainer.addEventListener('input', function(e) {
                const block = e.target.closest('.hotel-block');
                if (!block) return;

                if (e.target.name?.includes('[check_in]')) {
                    recalculateHotelNights(block, 'check_in');
                } else if (e.target.name?.includes('[check_out]')) {
                    recalculateHotelNights(block, 'check_out');
                } else if (e.target.name?.includes('[no_of_nights]')) {
                    recalculateHotelNights(block, 'nights');
                }

                if (e.target.classList.contains('hotel-room-number-input') || 
                    e.target.classList.contains('hotel-name-input') || 
                    e.target.name?.includes('[check_in]') || 
                    e.target.name?.includes('[check_out]')) {
                    clearTimeout(capacityCheckTimeout);
                    capacityCheckTimeout = setTimeout(() => checkHotelRoomCapacity(block), 400);
                }
            });

            hotelsContainer.addEventListener('change', function(e) {
                const block = e.target.closest('.hotel-block');
                if (!block) return;

                if (e.target.name?.includes('[check_in]')) {
                    recalculateHotelNights(block, 'check_in');
                } else if (e.target.name?.includes('[check_out]')) {
                    recalculateHotelNights(block, 'check_out');
                } else if (e.target.name?.includes('[no_of_nights]')) {
                    recalculateHotelNights(block, 'nights');
                }

                if (e.target.classList.contains('hotel-crud-select') || 
                    e.target.name?.includes('[room_type]') ||
                    e.target.name?.includes('[check_in]') ||
                    e.target.name?.includes('[check_out]')) {
                    clearTimeout(capacityCheckTimeout);
                    capacityCheckTimeout = setTimeout(() => checkHotelRoomCapacity(block), 200);
                }
            });
        }

        // Initialize and check initial rooms and nights on page load
        document.querySelectorAll('#hotelsList .hotel-block').forEach(b => {
            recalculateHotelNights(b, 'check_in');
            checkHotelRoomCapacity(b);
        });

        // Form Submit Check for Full Rooms - STRICT HARD BLOCK
        const mainBookingForm = document.querySelector('form[action*="booking"]');
        if (mainBookingForm) {
            mainBookingForm.addEventListener('submit', function(e) {
                const fullRoomInputs = document.querySelectorAll('.hotel-room-number-input[data-is-full="true"]');
                if (fullRoomInputs.length > 0) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    let roomList = [];
                    fullRoomInputs.forEach(inp => {
                        const val = inp.value.trim();
                        if (val) roomList.push(val);
                    });
                    
                    alert(`⛔ BOOKING BLOCKED: Room ${roomList.join(', ')} is FULL!\n\nYou cannot save this booking because the selected room has reached maximum bed capacity.\n\nPlease choose a different room or increase the bed capacity in the Rooming List report before proceeding.`);
                    
                    // Switch to Hotel tab
                    const hotelTabLink = document.querySelector('a[href="#tab-hotel"], a[data-bs-target="#tab-hotel"]');
                    if (hotelTabLink) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                            bootstrap.Tab.getOrCreateInstance(hotelTabLink).show();
                        } else {
                            hotelTabLink.click();
                        }
                    }
                    
                    fullRoomInputs[0].focus();
                    return false;
                }
            });
        }
    </script>
@endsection
