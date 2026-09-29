@extends('layout.master')
@section('title', 'New Booking')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fs-18 fw-semibold m-0">New Booking</h4>
                    <a href="{{ route('booking.index') }}" class="btn btn-secondary btn-sm">Back</a>
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
                    <span>Persons: <strong id="bar_pax">1</strong></span>
                    <span>|</span>
                    <span>Adult Costing: <strong id="bar_adult">0.00</strong></span>
                    <span>|</span>
                    <span>Visa: <strong id="bar_visa">0.00</strong></span>
                    <span>|</span>
                    <span>Flight: <strong id="bar_flight">0.00</strong></span>
                    <span>|</span>
                    <span class="text-primary fw-semibold">Total: <strong id="bar_total">0.00</strong></span>
                    <span>|</span>
                    <span class="text-danger fw-semibold">Balance: <strong id="bar_balance">0.00</strong></span>
                </div>

                <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                    @csrf

                    {{-- TAB NAV --}}
                    <ul class="nav nav-tabs booking-tabs mb-0" id="bookingTabs" role="tablist">
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

                    <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white" id="bookingTabsContent">

                        {{-- TAB 1: Package --}}
                        <div class="tab-pane fade show active" id="tab-package">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Package Type <span class="text-danger">*</span></label>
                                    <select name="package_type" class="form-select" required>
                                        <option value="umrah">Umrah</option>
                                        <option value="hajj">Hajj</option>
                                        <option value="other">Other Tour</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Year <span class="text-danger">*</span></label>
                                    <input type="number" name="package_year" class="form-control"
                                        value="{{ old('package_year', date('Y')) }}" min="2000"
                                        max="{{ date('Y') + 5 }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Package Name</label>
                                    <input type="text" name="package_name" class="form-control"
                                        placeholder="e.g. Economy Umrah 2025">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Booking Status</label>
                                    <select name="status" class="form-select">
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="cancelled">Cancelled</option>
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
                                                id="for_client" value="client" checked>
                                            <label class="form-check-label" for="for_client">Client</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="booking_for"
                                                id="for_company" value="company">
                                            <label class="form-check-label" for="for_company">Company</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- CLIENT BLOCK --}}
                                <div class="col-md-6" id="clientBlock">
                                    <label class="form-label">Client <span class="text-danger">*</span></label>
                                    <select name="client_id" id="clientSelect" class="form-select" required>
                                        <option value="">-- Select Client --</option>
                                        @foreach ($clients as $c)
                                            <option value="{{ $c->id }}">
                                                {{ $c->name }}
                                                {{ $c->company_name ? '(' . $c->company_name . ')' : '' }}
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
                                            <option value="{{ $co->id }}">{{ $co->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">No of Pax <span class="text-danger">*</span></label>
                                    <input type="number" name="no_of_pax" id="no_of_pax" class="form-control"
                                        value="1" min="1" required>
                                    <small class="text-muted">Persons, Flight & Visa tabs will update automatically</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Care Of</label>
                                    <input type="text" name="care_of" class="form-control"
                                        placeholder="Guardian / Agent">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Passport Number</label>
                                    <input type="text" name="passport_number" id="fill_passport" class="form-control"
                                        placeholder="Auto-filled from client">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">CNIC Number</label>
                                    <input type="text" name="cnic" id="fill_cnic" class="form-control"
                                        placeholder="Auto-filled from client">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" id="fill_phone" class="form-control"
                                        placeholder="Auto-filled from client">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Emergency Phone</label>
                                    <input type="text" name="emergency_phone" class="form-control"
                                        placeholder="+92 300 0000000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Voucher Number</label>
                                    <input type="text" name="voucher_number" class="form-control"
                                        placeholder="VCH-XXXX">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" name="card_number" class="form-control">
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
                            <div id="personsBookingForNote" class="alert alert-info py-2 px-3 d-none"
                                style="font-size:13px;">
                                <i class="mdi mdi-information me-1"></i>
                                This is a company booking. The client dropdown is hidden for the main passenger. Please
                                enter the name and passport details manually.
                            </div>
                            <div id="personsList"></div>

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
                                    <input type="date" name="departure_date" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Departure Flight #</label>
                                    <input type="text" name="departure_flight" class="form-control"
                                        placeholder="PK-301">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Time of Departure</label>
                                    <input type="time" name="departure_time" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Airline</label>
                                    <select name="departure_airline" class="form-select">
                                        <option value="">-- Select --</option>
                                        <option>PIA</option>
                                        <option>Air Arabia</option>
                                        <option>Emirates</option>
                                        <option>Qatar Airways</option>
                                        <option>FlyDubai</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Departure PNR / Ticket #</label>
                                    <input type="text" name="departure_pnr" class="form-control"
                                        placeholder="ABC123">
                                </div>
                            </div>

                            {{-- ARRIVAL --}}
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted border-bottom pb-2">
                                        <i class="mdi mdi-airplane-landing me-1"></i> Arrival Info
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date of Arrival</label>
                                    <input type="date" name="arrival_date" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Arrival Flight #</label>
                                    <input type="text" name="arrival_flight" class="form-control"
                                        placeholder="PK-302">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Time of Arrival</label>
                                    <input type="time" name="arrival_time" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Airline</label>
                                    <select name="arrival_airline" class="form-select">
                                        <option value="">-- Select --</option>
                                        <option>PIA</option>
                                        <option>Air Arabia</option>
                                        <option>Emirates</option>
                                        <option>Qatar Airways</option>
                                        <option>FlyDubai</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Arrival PNR / Ticket #</label>
                                    <input type="text" name="arrival_pnr" class="form-control" placeholder="XYZ456">
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
                                @foreach ([['makkah', 'Makkah'], ['madinah', 'Madinah']] as [$val, $label])
                                    <div class="hotel-block border rounded p-3 mb-3">
                                        <h6 class="text-primary mb-3">{{ $label }}</h6>
                                        <input type="hidden" name="hotels[{{ $loop->index }}][location]"
                                            value="{{ $val }}">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Hotel Name</label>
                                                <input type="text" name="hotels[{{ $loop->index }}][hotel_name]"
                                                    class="form-control" placeholder="Hotel name">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Nights</label>
                                                <input type="number" name="hotels[{{ $loop->index }}][no_of_nights]"
                                                    class="form-control" value="1" min="1">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Room Type</label>
                                                <select name="hotels[{{ $loop->index }}][room_type]"
                                                    class="form-select">
                                                    <option value="single">Single</option>
                                                    <option value="double">Double</option>
                                                    <option value="triple">Triple</option>
                                                    <option value="quad">Quad</option>
                                                    <option value="suite">Suite</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">No. of Rooms</label>
                                                <input type="number" name="hotels[{{ $loop->index }}][no_of_rooms]"
                                                    class="form-control" value="1" min="1">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Check In</label>
                                                <input type="date" name="hotels[{{ $loop->index }}][check_in]"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Check Out</label>
                                                <input type="date" name="hotels[{{ $loop->index }}][check_out]"
                                                    class="form-control">
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
                                Names and passport numbers filled in Persons tab will auto-fill here. You can edit them.
                            </p>
                            <div id="visasList"></div>

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
                                        class="form-control calc" placeholder="Enter package cost" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Visa Charges (total)</label>
                                    <input type="number" name="visa_charges" id="visa_charges"
                                        class="form-control calc" placeholder="Enter visa charges" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Flight Charges (total)</label>
                                    <input type="number" name="flight_charges" id="flight_charges"
                                        class="form-control calc" placeholder="Enter flight charges" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Qurbani & Other Charges</label>
                                    <input type="number" name="other_charges" id="other_charges"
                                        class="form-control calc" placeholder="Enter other charges" step="0.01">
                                </div>

                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm bg-light">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Persons</th>
                                                    <th>Pkg Cost × Pax</th>
                                                    <th>Visa</th>
                                                    <th>Flight</th>
                                                    <th>Other</th>
                                                    <th class="text-success">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong id="sum_pax">1</strong></td>
                                                    <td id="sum_pkg">0.00</td>
                                                    <td id="sum_visa">0.00</td>
                                                    <td id="sum_flight">0.00</td>
                                                    <td id="sum_other">0.00</td>
                                                    <td class="text-success fw-bold" id="sum_total">0.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Total Amount</label>
                                    <input type="number" name="total_amount" id="total_amount"
                                        class="form-control bg-light fw-bold" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Total Received</label>
                                    <input type="number" name="total_received" id="total_received"
                                        class="form-control calc" placeholder="Enter amount received" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Balance Remaining</label>
                                    <input type="number" name="balance" id="balance"
                                        class="form-control bg-light text-danger fw-bold" readonly>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('booking.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-success px-5">
                                        <i class="mdi mdi-content-save me-1"></i> Save Booking
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
        // ═══════════════════════════════════════
        // CLIENTS DATA
        // ═══════════════════════════════════════
        const clientsData = @json($clients);

        // ═══════════════════════════════════════
        // BOOKING FOR TOGGLE (client / company)
        // ═══════════════════════════════════════
        const forClient = document.getElementById('for_client');
        const forCompany = document.getElementById('for_company');
        const clientBlock = document.getElementById('clientBlock');
        const companyBlock = document.getElementById('companyBlock');
        const clientSelect = document.getElementById('clientSelect');
        const companySelect = document.getElementById('companySelect');
        const personsNote = document.getElementById('personsBookingForNote');

        function isCompanyMode() {
            return forCompany.checked;
        }

        function toggleBookingFor() {
            if (forClient.checked) {
                clientBlock.classList.remove('d-none');
                companyBlock.classList.add('d-none');
                clientSelect.setAttribute('required', 'required');
                companySelect.removeAttribute('required');
                companySelect.value = '';
                personsNote.classList.add('d-none');
            } else {
                companyBlock.classList.remove('d-none');
                clientBlock.classList.add('d-none');
                companySelect.setAttribute('required', 'required');
                clientSelect.removeAttribute('required');
                clientSelect.value = '';
                document.getElementById('fill_passport').value = '';
                document.getElementById('fill_cnic').value = '';
                document.getElementById('fill_phone').value = '';
                personsNote.classList.remove('d-none');
            }
            // Persons tab ko naye mode ke hisab se rebuild karo
            rebuildPersons();
        }

        forClient.addEventListener('change', toggleBookingFor);
        forCompany.addEventListener('change', toggleBookingFor);

        // ═══════════════════════════════════════
        // TAB NAVIGATION
        // ═══════════════════════════════════════
        const tabLinks = Array.from(document.querySelectorAll('#bookingTabs .nav-link'));

        function activateTab(idx) {
            if (idx < 0 || idx >= tabLinks.length) return;
            tabLinks[idx].click();
            if (tabLinks[idx].getAttribute('href') === '#tab-visa') syncVisas();
            if (tabLinks[idx].getAttribute('href') === '#tab-flight') syncFlightPersons();
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
        clientSelect.addEventListener('change', function() {
            const id = parseInt(this.value);
            const client = clientsData.find(c => c.id === id);
            if (client) {
                document.getElementById('fill_passport').value = client.passport_number || '';
                document.getElementById('fill_cnic').value = client.cnic || '';
                document.getElementById('fill_phone').value = client.phone || '';
            } else {
                document.getElementById('fill_passport').value = '';
                document.getElementById('fill_cnic').value = '';
                document.getElementById('fill_phone').value = '';
            }
            // Persons tab mein bhi person 0 update karo agar already built ho (client mode only)
            const firstSel = document.querySelector('.person-client-select[data-idx="0"]');
            if (firstSel && this.value) {
                firstSel.value = this.value;
                fillPersonFromClient(firstSel, 0);
            }
        });

        // ═══════════════════════════════════════
        // BUILD PERSON ROWS
        // ═══════════════════════════════════════
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

        function buildPersonRow(idx, isFirst = false) {
            const companyMode = isCompanyMode();
            const label = isFirst ?
                (companyMode ? 'Main Passenger' : 'Main Passenger (Client)') :
                `Passenger ${idx + 1}`;

            // Client dropdown sirf tab dikhao jab isFirst ho AUR company mode na ho
            const clientDropdown = (isFirst && !companyMode) ? `
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12px;">Select Client (optional)</label>
                    <select class="form-select form-select-sm person-client-select" data-idx="${idx}" onchange="fillPersonFromClient(this, ${idx})">
                        ${buildClientOptions(clientSelect.value)}
                    </select>
                </div>` : '';

            const nameCol = (isFirst && !companyMode) ? 'col-md-4' : 'col-md-6';
            const passCol = (isFirst && !companyMode) ? 'col-md-4' : 'col-md-6';

            return `
            <div class="person-card" id="person_card_${idx}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong class="text-primary" style="font-size:13px;">${label}</strong>
                </div>
                <div class="row g-2">
                    ${clientDropdown}
                    <div class="${nameCol}">
                        <label class="form-label" style="font-size:12px;">Full Name</label>
                        <input type="text" name="persons[${idx}][full_name]" id="person_name_${idx}"
                               class="form-control form-control-sm" placeholder="Full Name">
                    </div>
                    <div class="${passCol}">
                        <label class="form-label" style="font-size:12px;">Passport #</label>
                        <input type="text" name="persons[${idx}][passport_number]" id="person_passport_${idx}"
                               class="form-control form-control-sm" placeholder="Passport #">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12px;">CNIC</label>
                        <input type="text" name="persons[${idx}][cnic]" id="person_cnic_${idx}"
                               class="form-control form-control-sm" placeholder="XXXXX-XXXXXXX-X">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12px;">Phone</label>
                        <input type="text" name="persons[${idx}][phone]" id="person_phone_${idx}"
                               class="form-control form-control-sm" placeholder="+92 300 0000000">
                    </div>
                </div>
            </div>`;
        }

        function fillPersonFromClient(sel, idx) {
            const opt = sel.options[sel.selectedIndex];
            document.getElementById(`person_passport_${idx}`).value = opt.dataset.passport || '';
            document.getElementById(`person_cnic_${idx}`).value = opt.dataset.cnic || '';
            document.getElementById(`person_phone_${idx}`).value = opt.dataset.phone || '';
            const id = parseInt(sel.value);
            const c = clientsData.find(x => x.id === id);
            if (c) document.getElementById(`person_name_${idx}`).value = c.name;
        }

        function rebuildPersons() {
            const pax = parseInt(document.getElementById('no_of_pax').value) || 1;
            const list = document.getElementById('personsList');
            list.innerHTML = '';
            for (let i = 0; i < pax; i++) {
                list.insertAdjacentHTML('beforeend', buildPersonRow(i, i === 0));
            }
            // Person 0 mein selected client auto-fill karo (sirf client mode mein)
            if (!isCompanyMode()) {
                const clientId = clientSelect.value;
                if (clientId) {
                    const firstSel = document.querySelector('.person-client-select[data-idx="0"]');
                    if (firstSel) {
                        firstSel.value = clientId;
                        fillPersonFromClient(firstSel, 0);
                    }
                }
            }
            calcTotal();
        }

        document.getElementById('no_of_pax').addEventListener('input', function() {
            rebuildPersons();
            syncFlightPersons();
            syncVisas();
        });

        rebuildPersons();

        // ═══════════════════════════════════════
        // FLIGHT PERSONS
        // ═══════════════════════════════════════
        function syncFlightPersons() {
            const pax = parseInt(document.getElementById('no_of_pax').value) || 1;
            const list = document.getElementById('flightPersonsList');
            list.innerHTML = '';
            for (let i = 0; i < pax; i++) {
                const personName = document.getElementById(`person_name_${i}`)?.value || `Passenger ${i + 1}`;
                const personPass = document.getElementById(`person_passport_${i}`)?.value || '';
                list.insertAdjacentHTML('beforeend', `
                <div class="border rounded p-3 mb-2">
                    <strong class="text-primary" style="font-size:13px;">Passenger ${i + 1}: ${personName}</strong>
                    <div class="row g-2 mt-1">
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;">Passenger Name</label>
                            <input type="text" name="flight_persons[${i}][name]" class="form-control form-control-sm"
                                   value="${personName}" placeholder="Name on ticket">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:12px;">Passport #</label>
                            <input type="text" name="flight_persons[${i}][passport]" class="form-control form-control-sm"
                                   value="${personPass}" placeholder="Passport #">
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
            list.innerHTML = '';
            for (let i = 0; i < pax; i++) {
                const personName = document.getElementById(`person_name_${i}`)?.value || '';
                const personPass = document.getElementById(`person_passport_${i}`)?.value || '';
                list.insertAdjacentHTML('beforeend', `
                <div class="visa-card" id="visa_card_${i}">
                    <strong class="text-warning" style="font-size:13px;">
                        <i class="mdi mdi-passport me-1"></i> Visa ${i + 1}: ${personName || 'Passenger ' + (i + 1)}
                    </strong>
                    <div class="row g-2 mt-1">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Passport Number</label>
                            <input type="text" name="visas[${i}][passport_number]"
                                   class="form-control form-control-sm" value="${personPass}" placeholder="Passport #">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Full Name</label>
                            <input type="text" name="visas[${i}][given_name]"
                                   class="form-control form-control-sm" value="${personName}" placeholder="Full Name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Date of Birth</label>
                            <input type="date" name="visas[${i}][date_of_birth]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Company</label>
                            <input type="text" name="visas[${i}][company]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Send To</label>
                            <select name="visas[${i}][send_to]" class="form-select form-select-sm">
                                <option value="">-- Select --</option>
                                <option value="shirka">Shirka</option>
                                <option value="consulate">Consulate</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:12px;">Status</label>
                            <select name="visas[${i}][status]" class="form-select form-select-sm">
                                <option value="pending">Pending</option>
                                <option value="submitted">Submitted</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>`);
            }
        }

        document.querySelector('a[href="#tab-visa"]').addEventListener('click', syncVisas);
        document.querySelector('a[href="#tab-flight"]').addEventListener('click', syncFlightPersons);

        // ═══════════════════════════════════════
        // DYNAMIC HOTELS
        // ═══════════════════════════════════════
        let hotelIdx = 2;
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
        let routeIdx = 1;
        document.getElementById('addRoute').addEventListener('click', function() {
            document.getElementById('routesList').insertAdjacentHTML('beforeend', `
            <div class="route-block border rounded p-3 mb-2">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Additional Route</span>
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
            const balance = total - received;

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
        document.getElementById('no_of_pax').addEventListener('input', calcTotal);

        syncFlightPersons();
        syncVisas();
    </script>
@endsection
