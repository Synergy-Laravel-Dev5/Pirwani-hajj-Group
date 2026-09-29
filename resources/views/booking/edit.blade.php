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

                {{-- Summary Bar --}}
                <div class="booking-summary-bar mb-3 px-3 py-2 d-flex flex-wrap gap-3 align-items-center rounded"
                    style="background:#f0f4ff; border:1px solid #d0d9f0; font-size:13px;">
                    <span>Persons: <strong id="bar_pax">{{ $booking->no_of_pax }}</strong></span>
                    <span>|</span>
                    <span>Adult Costing: <strong
                            id="bar_adult">{{ number_format($booking->package_cost * $booking->no_of_pax, 2) }}</strong></span>
                    <span>|</span>
                    <span>Visa: <strong id="bar_visa">{{ number_format($booking->visa_charges, 2) }}</strong></span>
                    <span>|</span>
                    <span>Flight: <strong id="bar_flight">{{ number_format($booking->flight_charges, 2) }}</strong></span>
                    <span>|</span>
                    <span class="text-primary fw-semibold">Total: <strong
                            id="bar_total">{{ number_format($booking->total_amount, 2) }}</strong></span>
                    <span>|</span>
                    <span class="text-danger fw-semibold">Balance: <strong
                            id="bar_balance">{{ number_format($booking->balance, 2) }}</strong></span>
                </div>

                <form action="{{ route('booking.update', $booking->id) }}" method="POST" id="bookingForm">
                    @csrf
                    @method('PUT')

                    {{-- TAB NAV --}}
                    <ul class="nav nav-tabs booking-tabs mb-0" id="bookingTabs">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-package"><i
                                    class="mdi mdi-tag me-1"></i>Package</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-details"><i
                                    class="mdi mdi-account me-1"></i>Booking Details</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-persons"><i
                                    class="mdi mdi-account-group me-1"></i>Persons</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-flight"><i
                                    class="mdi mdi-airplane me-1"></i>Flight</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-hotel"><i
                                    class="mdi mdi-hotel me-1"></i>Hotel</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-transport"><i
                                    class="mdi mdi-bus me-1"></i>Transport</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-visa"><i
                                    class="mdi mdi-passport me-1"></i>Visa</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-costing"><i
                                    class="mdi mdi-cash me-1"></i>Costing</a></li>
                    </ul>

                    <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white">

                        {{-- TAB 1: Package --}}
                        <div class="tab-pane fade show active" id="tab-package">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label">Package Type <span class="text-danger">*</span></label>
                                    <select name="package_type" class="form-select" required>
                                        <option value="umrah" {{ $booking->package_type == 'umrah' ? 'selected' : '' }}>
                                            Umrah</option>
                                        <option value="hajj" {{ $booking->package_type == 'hajj' ? 'selected' : '' }}>
                                            Hajj</option>
                                        <option value="other" {{ $booking->package_type == 'other' ? 'selected' : '' }}>
                                            Other Tour</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Year <span class="text-danger">*</span></label>
                                    <select name="package_year" class="form-select" required>
                                        @php $currentYear = date('Y'); @endphp
                                        @for ($year = $currentYear - 1; $year <= $currentYear + 5; $year++)
                                            <option value="{{ $year }}"
                                                {{ old('package_year', $booking->package_year ?? $currentYear) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Package Name</label>
                                    <input type="text" name="package_name" class="form-control"
                                        value="{{ old('package_name', $booking->package_name) }}"
                                        placeholder="e.g. Economy Umrah 2025">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Booking Status</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>
                                            Confirmed</option>
                                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                </div>

                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary btn-next">Next <i
                                        class="mdi mdi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        {{-- TAB 2: Booking Details --}}
                        <div class="tab-pane fade" id="tab-details">
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
                                    <small class="text-muted">Persons, Flight & Visa tabs will update automatically</small>
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
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev"><i
                                        class="mdi mdi-arrow-left me-1"></i> Prev</button>
                                <button type="button" class="btn btn-primary btn-next">Next <i
                                        class="mdi mdi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        {{-- TAB 3: Persons --}}
                        <div class="tab-pane fade" id="tab-persons">
                            <p class="text-muted small mb-3">
                                <i class="mdi mdi-information me-1"></i>
                                Rows are based on No. of Pax. Main passenger can be linked to a client.
                            </p>
                            <div id="personsBookingForNote"
                                class="alert alert-info py-2 px-3 {{ $booking->booking_for == 'company' ? '' : 'd-none' }}"
                                style="font-size:13px;">
                                <i class="mdi mdi-information me-1"></i>
                                Booking company ke liye hai — main passenger row par client dropdown nahi dikhega.
                            </div>
                            <div id="personsList">
                                @foreach ($booking->persons as $i => $person)
                                    <div class="person-card" id="person_card_{{ $i }}">
                                        <div class="mb-2">
                                            <strong class="text-primary" style="font-size:13px;">
                                                {{ $i === 0 ? 'Main Passenger' : 'Passenger ' . ($i + 1) }}
                                            </strong>
                                        </div>
                                        <div class="row g-2">
                                            @if ($i === 0 && $booking->booking_for !== 'company')
                                                <div class="col-md-4">
                                                    <label class="form-label" style="font-size:12px;">Select Client
                                                        (optional)</label>
                                                    <select class="form-select form-select-sm person-client-select"
                                                        data-idx="0" onchange="fillPersonFromClient(this, 0)">
                                                        <option value="">-- Manual Entry --</option>
                                                        @foreach ($clients as $c)
                                                            <option value="{{ $c->id }}"
                                                                data-passport="{{ $c->passport_number }}"
                                                                data-cnic="{{ $c->cnic }}"
                                                                data-phone="{{ $c->phone }}"
                                                                {{ $booking->client_id == $c->id ? 'selected' : '' }}>
                                                                {{ $c->name }}{{ $c->company_name ? ' (' . $c->company_name . ')' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" style="font-size:12px;">Full Name</label>
                                                    <input type="text" name="persons[0][full_name]" id="person_name_0"
                                                        class="form-control form-control-sm"
                                                        value="{{ $person->full_name }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" style="font-size:12px;">Passport #</label>
                                                    <input type="text" name="persons[0][passport_number]"
                                                        id="person_passport_0" class="form-control form-control-sm"
                                                        value="{{ $person->passport_number }}">
                                                </div>
                                            @elseif ($i === 0)
                                                <div class="col-md-6">
                                                    <label class="form-label" style="font-size:12px;">Full Name</label>
                                                    <input type="text" name="persons[0][full_name]" id="person_name_0"
                                                        class="form-control form-control-sm"
                                                        value="{{ $person->full_name }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" style="font-size:12px;">Passport #</label>
                                                    <input type="text" name="persons[0][passport_number]"
                                                        id="person_passport_0" class="form-control form-control-sm"
                                                        value="{{ $person->passport_number }}">
                                                </div>
                                            @else
                                                <div class="col-md-6">
                                                    <label class="form-label" style="font-size:12px;">Full Name</label>
                                                    <input type="text" name="persons[{{ $i }}][full_name]"
                                                        id="person_name_{{ $i }}"
                                                        class="form-control form-control-sm"
                                                        value="{{ $person->full_name }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" style="font-size:12px;">Passport #</label>
                                                    <input type="text"
                                                        name="persons[{{ $i }}][passport_number]"
                                                        id="person_passport_{{ $i }}"
                                                        class="form-control form-control-sm"
                                                        value="{{ $person->passport_number }}">
                                                </div>
                                            @endif
                                            <div class="col-md-4">
                                                <label class="form-label" style="font-size:12px;">CNIC</label>
                                                <input type="text" name="persons[{{ $i }}][cnic]"
                                                    id="person_cnic_{{ $i }}"
                                                    class="form-control form-control-sm"
                                                    value="{{ $person->cnic ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label" style="font-size:12px;">Phone</label>
                                                <input type="text" name="persons[{{ $i }}][phone]"
                                                    id="person_phone_{{ $i }}"
                                                    class="form-control form-control-sm"
                                                    value="{{ $person->phone ?? '' }}">
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

                        {{-- TAB 4: Flight --}}
                        <div class="tab-pane fade" id="tab-flight">

                            {{-- DEPARTURE --}}
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted border-bottom pb-2">
                                        <i class="mdi mdi-airplane-takeoff me-1"></i> Departure Info
                                    </h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date of Departure</label>
                                    <input type="date" name="departure_date" class="form-control"
                                        value="{{ old('departure_date', $booking->departure_date) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Departure Flight #</label>
                                    <input type="text" name="departure_flight" class="form-control"
                                        value="{{ old('departure_flight', $booking->departure_flight) }}"
                                        placeholder="PK-301">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Time of Departure</label>
                                    <input type="time" name="departure_time" class="form-control"
                                        value="{{ old('departure_time', $booking->departure_time) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Airline</label>
                                    <select name="departure_airline" class="form-select">
                                        <option value="">-- Select --</option>
                                        @foreach (['PIA', 'Air Arabia', 'Emirates', 'Qatar Airways', 'FlyDubai', 'Other'] as $air)
                                            <option
                                                {{ old('departure_airline', $booking->departure_airline) == $air ? 'selected' : '' }}>
                                                {{ $air }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Departure PNR / Ticket #</label>
                                    <input type="text" name="departure_pnr" class="form-control"
                                        value="{{ old('departure_pnr', $booking->departure_pnr) }}" placeholder="ABC123">
                                </div>
                            </div>

                            {{-- ARRIVAL --}}
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted border-bottom pb-2">
                                        <i class="mdi mdi-airplane-landing me-1"></i> Arrival Info
                                    </h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date of Arrival</label>
                                    <input type="date" name="arrival_date" class="form-control"
                                        value="{{ old('arrival_date', $booking->arrival_date) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Arrival Flight #</label>
                                    <input type="text" name="arrival_flight" class="form-control"
                                        value="{{ old('arrival_flight', $booking->arrival_flight) }}"
                                        placeholder="PK-302">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Time of Arrival</label>
                                    <input type="time" name="arrival_time" class="form-control"
                                        value="{{ old('arrival_time', $booking->arrival_time) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Airline</label>
                                    <select name="arrival_airline" class="form-select">
                                        <option value="">-- Select --</option>
                                        @foreach (['PIA', 'Air Arabia', 'Emirates', 'Qatar Airways', 'FlyDubai', 'Other'] as $air)
                                            <option
                                                {{ old('arrival_airline', $booking->arrival_airline) == $air ? 'selected' : '' }}>
                                                {{ $air }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Arrival PNR / Ticket #</label>
                                    <input type="text" name="arrival_pnr" class="form-control"
                                        value="{{ old('arrival_pnr', $booking->arrival_pnr) }}" placeholder="XYZ456">
                                </div>
                            </div>

                            {{-- PASSENGER TICKETS --}}
                            <h6 class="text-muted border-bottom pb-2 mb-3">
                                Passenger Tickets
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

                        {{-- TAB 5: Hotel --}}
                        <div class="tab-pane fade" id="tab-hotel">
                            <div id="hotelsList">
                                @foreach ($booking->hotels as $hotel)
                                    <div class="hotel-block border rounded p-3 mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h6 class="text-primary mb-0">
                                                {{ $hotel->location === 'makkah' ? 'Makkah' : ($hotel->location === 'madinah' ? 'Madinah' : 'Other Hotel') }}
                                            </h6>
                                            @if ($loop->index >= 2)
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm remove-hotel">× Remove</button>
                                            @endif
                                        </div>
                                        <input type="hidden" name="hotels[{{ $loop->index }}][location]"
                                            value="{{ $hotel->location }}">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Hotel Name</label>
                                                <input type="text" name="hotels[{{ $loop->index }}][hotel_name]"
                                                    class="form-control" value="{{ $hotel->hotel_name }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Nights</label>
                                                <input type="number" name="hotels[{{ $loop->index }}][no_of_nights]"
                                                    class="form-control" value="{{ $hotel->no_of_nights }}"
                                                    min="1">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Room Type</label>
                                                <select name="hotels[{{ $loop->index }}][room_type]"
                                                    class="form-select">
                                                    @foreach (['single', 'double', 'triple', 'quad', 'suite'] as $rt)
                                                        <option value="{{ $rt }}"
                                                            {{ $hotel->room_type == $rt ? 'selected' : '' }}>
                                                            {{ ucfirst($rt) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">No. of Rooms</label>
                                                <input type="number" name="hotels[{{ $loop->index }}][no_of_rooms]"
                                                    class="form-control" value="{{ $hotel->no_of_rooms }}"
                                                    min="1">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Check In</label>
                                                <input type="date" name="hotels[{{ $loop->index }}][check_in]"
                                                    class="form-control" value="{{ $hotel->check_in }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Check Out</label>
                                                <input type="date" name="hotels[{{ $loop->index }}][check_out]"
                                                    class="form-control" value="{{ $hotel->check_out }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addHotel">+ Add
                                Hotel</button>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev"><i
                                        class="mdi mdi-arrow-left me-1"></i> Prev</button>
                                <button type="button" class="btn btn-primary btn-next">Next <i
                                        class="mdi mdi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        {{-- TAB 6: Transport --}}
                        <div class="tab-pane fade" id="tab-transport">
                            <div id="routesList">
                                @forelse($booking->transports as $i => $t)
                                    <div class="route-block border rounded p-3 mb-2">
                                        @if ($loop->index > 0)
                                            <div class="d-flex justify-content-end mb-1">
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm remove-route">×</button>
                                            </div>
                                        @endif
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label">Route</label>
                                                <input type="text" name="transports[{{ $i }}][route]"
                                                    class="form-control" value="{{ $t->route }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Transport Type</label>
                                                <select name="transports[{{ $i }}][transport_type]"
                                                    class="form-select">
                                                    @foreach (['private_car' => 'Private Car', 'bus' => 'Bus / Coach', 'train' => 'Train', 'shared_van' => 'Shared Van', 'taxi' => 'Taxi', 'flight' => 'Flight'] as $val => $lbl)
                                                        <option value="{{ $val }}"
                                                            {{ $t->transport_type == $val ? 'selected' : '' }}>
                                                            {{ $lbl }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Notes</label>
                                                <input type="text" name="transports[{{ $i }}][notes]"
                                                    class="form-control" value="{{ $t->notes }}">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="route-block border rounded p-3 mb-2">
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label">Route</label>
                                                <input type="text" name="transports[0][route]" class="form-control"
                                                    placeholder="Karachi → Makkah">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Transport Type</label>
                                                <select name="transports[0][transport_type]" class="form-select">
                                                    <option value="private_car">Private Car</option>
                                                    <option value="bus">Bus / Coach</option>
                                                    <option value="train">Train</option>
                                                    <option value="shared_van">Shared Van</option>
                                                    <option value="taxi">Taxi</option>
                                                    <option value="flight">Flight</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Notes</label>
                                                <input type="text" name="transports[0][notes]" class="form-control"
                                                    placeholder="Optional">
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addRoute">+ Add
                                Route</button>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev"><i
                                        class="mdi mdi-arrow-left me-1"></i> Prev</button>
                                <button type="button" class="btn btn-primary btn-next">Next <i
                                        class="mdi mdi-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        {{-- TAB 7: Visa --}}
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

                        {{-- TAB 8: Costing --}}
                        <div class="tab-pane fade" id="tab-costing">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Package Cost (per person)</label>
                                    <input type="number" name="package_cost" id="package_cost"
                                        class="form-control calc"
                                        value="{{ old('package_cost', $booking->package_cost ?: '') }}"
                                        placeholder="Enter package cost" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Visa Charges</label>
                                    <input type="number" name="visa_charges" id="visa_charges"
                                        class="form-control calc"
                                        value="{{ old('visa_charges', $booking->visa_charges ?: '') }}"
                                        placeholder="Enter visa charges" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Flight Charges</label>
                                    <input type="number" name="flight_charges" id="flight_charges"
                                        class="form-control calc"
                                        value="{{ old('flight_charges', $booking->flight_charges ?: '') }}"
                                        placeholder="Enter flight charges" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Qurbani & Other Charges</label>
                                    <input type="number" name="other_charges" id="other_charges"
                                        class="form-control calc"
                                        value="{{ old('other_charges', $booking->other_charges ?: '') }}"
                                        placeholder="Enter other charges" step="0.01">
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
                                                    <th>Other</th>
                                                    <th class="text-success">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong id="sum_pax">{{ $booking->no_of_pax }}</strong></td>
                                                    <td id="sum_pkg">
                                                        {{ number_format(($booking->package_cost ?? 0) * ($booking->no_of_pax ?? 0), 2) }}
                                                    </td>
                                                    <td id="sum_visa">
                                                        {{ number_format($booking->visa_charges ?? 0, 2) }}</td>
                                                    <td id="sum_flight">
                                                        {{ number_format($booking->flight_charges ?? 0, 2) }}</td>
                                                    <td id="sum_other">
                                                        {{ number_format($booking->other_charges ?? 0, 2) }}</td>
                                                    <td class="text-success fw-bold" id="sum_total">
                                                        {{ number_format($booking->total_amount ?? 0, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Total Amount</label>
                                    <input type="number" name="total_amount" id="total_amount"
                                        class="form-control bg-light fw-bold"
                                        value="{{ old('total_amount', $booking->total_amount ?: '') }}"
                                        placeholder="Auto calculated" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Advance</label>
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

                                <div class="col-md-3">
                                    <label class="form-label">
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
                                        class="form-control bg-light fw-bold {{ $bookingBalance > 0 ? 'text-danger' : 'text-success' }}"
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

        .tab-content {
            min-height: 380px;
        }

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
        const existingVisas = @json($booking->visas);
        const transactionsPaid = {{ $transactionsPaid ?? 0 }};

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
                const existingDropdownCol = firstCard.querySelector('.person-client-select')?.closest('.col-md-4');
                if (isCompanyMode() && existingDropdownCol) {
                    existingDropdownCol.remove();
                } else if (!isCompanyMode() && !firstCard.querySelector('.person-client-select')) {
                    const row = firstCard.querySelector('.row.g-2');
                    row.insertAdjacentHTML('afterbegin', `
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12px;">Select Client (optional)</label>
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
        // PERSON FROM CLIENT DROPDOWN
        // ═══════════════════════════════════════
        function fillPersonFromClient(sel, idx) {
            const opt = sel.options[sel.selectedIndex];
            const passEl = document.getElementById(`person_passport_${idx}`);
            const cnicEl = document.getElementById(`person_cnic_${idx}`);
            const phoneEl = document.getElementById(`person_phone_${idx}`);
            const nameEl = document.getElementById(`person_name_${idx}`);
            if (passEl) passEl.value = opt.dataset.passport || '';
            if (cnicEl) cnicEl.value = opt.dataset.cnic || '';
            if (phoneEl) phoneEl.value = opt.dataset.phone || '';
            const c = clientsData.find(x => x.id == sel.value);
            if (c && nameEl) nameEl.value = c.name;
        }

        // ═══════════════════════════════════════
        // PERSONS LIST REBUILD
        // ═══════════════════════════════════════
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
                container.insertAdjacentHTML('beforeend', `
                    <div class="person-card" id="person_card_${i}">
                        <div class="mb-2">
                            <strong class="text-primary" style="font-size:13px;">Passenger ${i + 1}</strong>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;">Full Name</label>
                                <input type="text" name="persons[${i}][full_name]" id="person_name_${i}"
                                    class="form-control form-control-sm" placeholder="Full Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;">Passport #</label>
                                <input type="text" name="persons[${i}][passport_number]" id="person_passport_${i}"
                                    class="form-control form-control-sm" placeholder="Passport Number">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size:12px;">CNIC</label>
                                <input type="text" name="persons[${i}][cnic]" id="person_cnic_${i}"
                                    class="form-control form-control-sm" placeholder="CNIC">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size:12px;">Phone</label>
                                <input type="text" name="persons[${i}][phone]" id="person_phone_${i}"
                                    class="form-control form-control-sm" placeholder="Phone">
                            </div>
                        </div>
                    </div>
                `);
            }
        }

        // ═══════════════════════════════════════
        // VISA LIST REBUILD
        // ═══════════════════════════════════════
        function rebuildVisasList(newPax) {
            const container = document.getElementById('visasList');
            const currentCards = container.querySelectorAll('.visa-card');
            const currentCount = currentCards.length;

            if (newPax < currentCount) {
                for (let i = currentCount - 1; i >= newPax; i--) {
                    const card = document.getElementById('visa_card_' + i);
                    if (card) card.remove();
                }
                return;
            }

            for (let i = currentCount; i < newPax; i++) {
                const v = existingVisas[i] || {};
                container.insertAdjacentHTML('beforeend', `
                    <div class="visa-card" id="visa_card_${i}">
                        <strong class="text-warning" style="font-size:13px;">
                            <i class="mdi mdi-passport me-1"></i> Visa ${i + 1}
                        </strong>
                        <div class="row g-2 mt-1">
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:12px;">Passport Number</label>
                                <input type="text" name="visas[${i}][passport_number]"
                                    class="form-control form-control-sm"
                                    value="${v.passport_number || ''}" placeholder="Passport #">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:12px;">Full Name</label>
                                <input type="text" name="visas[${i}][given_name]"
                                    class="form-control form-control-sm"
                                    value="${v.given_name || ''}" placeholder="Full Name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:12px;">Date of Birth</label>
                                <input type="date" name="visas[${i}][date_of_birth]"
                                    class="form-control form-control-sm"
                                    value="${v.date_of_birth || ''}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size:12px;">Company</label>
                                <input type="text" name="visas[${i}][company]"
                                    class="form-control form-control-sm"
                                    value="${v.company || ''}" placeholder="Company">
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
                    </div>
                `);
            }
        }

        // ═══════════════════════════════════════
        // NO OF PAX CHANGE
        // ═══════════════════════════════════════
        document.getElementById('no_of_pax').addEventListener('input', function() {
            const val = parseInt(this.value);
            if (val >= 1) {
                rebuildPersonsList(val);
                rebuildVisasList(val);
            }
            calcTotal();
        });

        // ═══════════════════════════════════════
        // DYNAMIC HOTELS
        // ═══════════════════════════════════════
        let hotelIdx = {{ $booking->hotels->count() ?: 2 }};

        document.getElementById('addHotel').addEventListener('click', function() {
            document.getElementById('hotelsList').insertAdjacentHTML('beforeend', `
                <div class="hotel-block border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-primary mb-0">Additional Hotel</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-hotel">× Remove</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Location</label>
                            <select name="hotels[${hotelIdx}][location]" class="form-select">
                                <option value="makkah">Makkah</option>
                                <option value="madinah">Madinah</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hotel Name</label>
                            <input type="text" name="hotels[${hotelIdx}][hotel_name]" class="form-control" placeholder="Hotel name">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Nights</label>
                            <input type="number" name="hotels[${hotelIdx}][no_of_nights]" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Room Type</label>
                            <select name="hotels[${hotelIdx}][room_type]" class="form-select">
                                <option value="single">Single</option>
                                <option value="double">Double</option>
                                <option value="triple">Triple</option>
                                <option value="quad">Quad</option>
                                <option value="suite">Suite</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">No. of Rooms</label>
                            <input type="number" name="hotels[${hotelIdx}][no_of_rooms]" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Check In</label>
                            <input type="date" name="hotels[${hotelIdx}][check_in]" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Check Out</label>
                            <input type="date" name="hotels[${hotelIdx}][check_out]" class="form-control">
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
                <div class="route-block border rounded p-3 mb-2">
                    <div class="d-flex justify-content-end mb-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-route">×</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="transports[${routeIdx}][route]" class="form-control" placeholder="Route">
                        </div>
                        <div class="col-md-4">
                            <select name="transports[${routeIdx}][transport_type]" class="form-select">
                                <option value="private_car">Private Car</option>
                                <option value="bus">Bus / Coach</option>
                                <option value="train">Train</option>
                                <option value="shared_van">Shared Van</option>
                                <option value="taxi">Taxi</option>
                                <option value="flight">Flight</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="transports[${routeIdx}][notes]" class="form-control" placeholder="Notes">
                        </div>
                    </div>
                </div>`);
            routeIdx++;
        });

        document.getElementById('routesList').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-route')) e.target.closest('.route-block').remove();
        });

        // ═══════════════════════════════════════
        // CALCULATIONS
        // ═══════════════════════════════════════
        function calcTotal() {
            const pax = parseInt(document.getElementById('no_of_pax').value) || 1;
            const pkg = parseFloat(document.getElementById('package_cost').value) || 0;
            const visa = parseFloat(document.getElementById('visa_charges').value) || 0;
            const flight = parseFloat(document.getElementById('flight_charges').value) || 0;
            const other = parseFloat(document.getElementById('other_charges').value) || 0;
            const received = parseFloat(document.getElementById('total_received').value) || 0;

            const pkgTotal = pkg * pax;
            const total = pkgTotal + visa + flight + other;

            const balance = total - received - transactionsPaid;

            document.getElementById('total_amount').value = total.toFixed(2);
            document.getElementById('balance').value = balance.toFixed(2);

            document.getElementById('bar_pax').textContent = pax;
            document.getElementById('bar_adult').textContent = pkgTotal.toFixed(2);
            document.getElementById('bar_visa').textContent = visa.toFixed(2);
            document.getElementById('bar_flight').textContent = flight.toFixed(2);
            document.getElementById('bar_total').textContent = total.toFixed(2);
            document.getElementById('bar_balance').textContent = balance.toFixed(2);

            document.getElementById('sum_pax').textContent = pax;
            document.getElementById('sum_pkg').textContent = pkgTotal.toFixed(2);
            document.getElementById('sum_visa').textContent = visa.toFixed(2);
            document.getElementById('sum_flight').textContent = flight.toFixed(2);
            document.getElementById('sum_other').textContent = other.toFixed(2);
            document.getElementById('sum_total').textContent = total.toFixed(2);
        }

        document.querySelectorAll('.calc').forEach(el => el.addEventListener('input', calcTotal));

        document.addEventListener('DOMContentLoaded', function() {
            const initialPax = parseInt(document.getElementById('no_of_pax').value) || 1;
            rebuildPersonsList(initialPax);
            rebuildVisasList(initialPax);
            calcTotal();
        });
    </script>
@endsection
