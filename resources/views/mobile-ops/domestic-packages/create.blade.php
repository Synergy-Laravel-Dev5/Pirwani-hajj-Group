@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fs-18 fw-bold text-dark m-0">Create Domestic Package</h4>
                <p class="text-muted fs-13 mb-0">Build a domestic tour package with hotels, flights, multi-city stops, and pricing.</p>
            </div>
            <a href="{{ route('mobile-ops.domestic-packages.index') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="mdi mdi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('mobile-ops.domestic-packages.store') }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @csrf

        <!-- 1. Basic Package Information & Multi-City Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0">Basic Package Information</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="package_title" class="form-control"
                            placeholder="e.g. 5 Days Exotic Skardu Valley Luxury Tour" value="{{ old('package_title') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Package Code</label>
                        <input type="text" name="package_code" class="form-control" placeholder="e.g. DOM-SKD-01"
                            value="{{ old('package_code') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (Visible on App)</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subtitle / Catchphrase</label>
                        <input type="text" name="subtitle" class="form-control"
                            placeholder="e.g. Experience Shangrila Resort, Katpana Cold Desert & Deosai Plains"
                            value="{{ old('subtitle') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Departure City</label>
                        <input type="text" name="departure_city" list="domesticCities" class="form-control" placeholder="e.g. Islamabad"
                            value="{{ old('departure_city', 'Islamabad') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Destination City / Valley</label>
                        <input type="text" name="destination_city" list="domesticCities" class="form-control" placeholder="e.g. Skardu"
                            value="{{ old('destination_city') }}">
                    </div>

                    <datalist id="domesticCities">
                        @foreach ($cities as $c)
                            <option value="{{ $c }}"></option>
                        @endforeach
                    </datalist>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Stay Duration (Nights)</label>
                        <input type="number" name="stay_nights" class="form-control" placeholder="e.g. 4" min="1"
                            value="{{ old('stay_nights', 4) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Valid From</label>
                        <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_group_package" value="1" id="groupPkgSwitch"
                                {{ old('is_group_package') ? 'checked' : '' }} style="cursor: pointer; width: 44px; height: 22px;">
                            <label class="form-check-label fw-semibold ms-2 fs-13 text-dark" for="groupPkgSwitch">Group Tour</label>
                        </div>
                    </div>

                    <!-- Multi-City Package Toggle Section -->
                    <div class="col-12 mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded-3 border">
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch me-3 mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_multi_city" id="multiCitySwitch" value="1"
                                        {{ old('is_multi_city') ? 'checked' : '' }} style="cursor: pointer; width: 48px; height: 24px;">
                                </div>
                                <label class="form-check-label fw-bold text-dark fs-14 mb-0 cursor-pointer d-flex align-items-center gap-1" for="multiCitySwitch">
                                    <i class="mdi mdi-map-marker-multiple text-success fs-18"></i>
                                    <span>Multi-City / Additional Stops Package</span>
                                </label>
                            </div>
                            <span class="text-muted fs-12 d-none d-md-inline">Enable to add 2nd or multiple destination stops with separate hotel details</span>
                        </div>

                        <!-- Multi-City Container -->
                        <div id="multiCitySection" style="{{ old('is_multi_city') ? '' : 'display: none;' }}">
                            <label class="form-label fw-semibold text-secondary mb-2">Additional Destination Cities / Stops</label>

                            <div id="destinationsContainer" class="d-flex flex-column gap-2 mb-3">
                                <!-- Destination rows injected by JS -->
                            </div>

                            <button type="button" id="addDestinationBtn" class="btn btn-sm btn-outline-success px-3 py-1 rounded-pill">
                                <i class="mdi mdi-plus me-1"></i> Add More Destination
                            </button>

                            <!-- Dynamic Hotel Cards for each Additional Destination -->
                            <div id="destinationHotelCardsContainer" class="mt-4 d-flex flex-column gap-3">
                                <!-- Dynamic Hotel Details will be rendered here -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label class="form-label fw-semibold">Package Image</label>
                        <input type="file" name="package_image" class="form-control" accept="image/*" onchange="previewImage(this, 'thumbPreview')">
                        <small class="text-muted fs-11">Recommended: 1200x600 JPG, PNG, WEBP. Max 5MB.</small>
                        <div class="mt-2" id="thumbPreviewBox" style="display:none;">
                            <img id="thumbPreview" src="" class="rounded shadow-sm" style="max-height: 120px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Primary Hotel Stay Details -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center">
                    <i class="mdi mdi-office-building text-success me-2 fs-18"></i>
                    <span>Primary Hotel Stay</span>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-13">Primary Hotel / Resort</label>
                        <input type="text" name="primary_hotel" class="form-control" placeholder="e.g. Shangrila Resort / Serena Hotel"
                            value="{{ old('primary_hotel') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-13">Hotel Category / Star Rating</label>
                        <select name="hotel_star_rating" class="form-select">
                            <option value="">Select Rating / Class</option>
                            @foreach ($hotelCategories as $cat)
                                <option value="{{ $cat }}" {{ old('hotel_star_rating') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-13">Meal Plan</label>
                        <select name="meal_plan" class="form-select">
                            <option value="">Select Meal Plan</option>
                            @foreach ($mealPlans as $plan)
                                <option value="{{ $plan }}" {{ old('meal_plan') === $plan ? 'selected' : '' }}>{{ $plan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Flights & Logistics Itinerary Cards -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center">
                    <i class="mdi mdi-airplane text-primary me-2 fs-18"></i>
                    <span>Flight Details</span>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Outbound Flight Card -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light bg-opacity-25 h-100">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-1">
                                <i class="mdi mdi-airplane-takeoff text-primary"></i> Outbound Flight
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label fs-12 fw-semibold">Airline</label>
                                    <input type="text" name="outbound_airline" class="form-control form-control-sm" placeholder="e.g. PIA / Airblue"
                                        value="{{ old('outbound_airline') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-semibold">Departure Airport</label>
                                    <input type="text" name="outbound_origin" class="form-control form-control-sm" placeholder="e.g. Islamabad (ISB)"
                                        value="{{ old('outbound_origin') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-semibold">Arrival Airport</label>
                                    <input type="text" name="outbound_destination" class="form-control form-control-sm" placeholder="e.g. Skardu (KDU)"
                                        value="{{ old('outbound_destination') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-12 fw-semibold">Flight Type</label>
                                    <select name="outbound_flight_type" class="form-select form-select-sm">
                                        <option value="">Select Flight Type</option>
                                        @foreach($flightTypes as $ft)
                                            <option value="{{ $ft }}" {{ old('outbound_flight_type') === $ft ? 'selected' : '' }}>{{ $ft }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inbound Flight Card -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light bg-opacity-25 h-100">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-1">
                                <i class="mdi mdi-airplane-landing text-success"></i> Inbound Flight
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label fs-12 fw-semibold">Airline</label>
                                    <input type="text" name="inbound_airline" class="form-control form-control-sm" placeholder="e.g. PIA / Airblue"
                                        value="{{ old('inbound_airline') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-semibold">Departure Airport</label>
                                    <input type="text" name="inbound_origin" class="form-control form-control-sm" placeholder="e.g. Skardu (KDU)"
                                        value="{{ old('inbound_origin') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-semibold">Arrival Airport</label>
                                    <input type="text" name="inbound_destination" class="form-control form-control-sm" placeholder="e.g. Islamabad (ISB)"
                                        value="{{ old('inbound_destination') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-12 fw-semibold">Return Flight Type</label>
                                    <select name="inbound_flight_type" class="form-select form-select-sm">
                                        <option value="">Select Return Flight Type</option>
                                        @foreach($flightTypes as $ft)
                                            <option value="{{ $ft }}" {{ old('inbound_flight_type') === $ft ? 'selected' : '' }}>{{ $ft }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Rates & Ground Transport Fleet -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center">
                    <i class="mdi mdi-currency-usd text-success me-2 fs-18"></i>
                    <span>Pricing Matrix & Ground Fleet Logistics</span>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13">Ground Transport Fleet Mode</label>
                        <select name="ground_transfer_fleet" class="form-select">
                            <option value="">Select Vehicle / Transfer Mode</option>
                            @foreach ($groundFleets as $gf)
                                <option value="{{ $gf }}" {{ old('ground_transfer_fleet') === $gf ? 'selected' : '' }}>{{ $gf }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13 text-success">Per-Person Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_per_person" class="form-control fw-bold text-success" placeholder="0.00"
                            value="{{ old('price_per_person') }}">
                    </div>

                    <!-- Room Rates Matrix -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold fs-13">Sharing Room Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_sharing" class="form-control" placeholder="0.00"
                            value="{{ old('price_sharing') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold fs-13">Quad Room Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_quad" class="form-control" placeholder="0.00"
                            value="{{ old('price_quad') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold fs-13">Triple Room Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_triple" class="form-control" placeholder="0.00"
                            value="{{ old('price_triple') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold fs-13">Double Room Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_double" class="form-control" placeholder="0.00"
                            value="{{ old('price_double') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Repeatable Inclusions & Repeatable Amenities Builders -->
        <div class="row g-4 mb-4">
            <!-- Repeatable Inclusions -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center gap-1">
                            <i class="mdi mdi-check-all text-success fs-18"></i>
                            <span>Package Inclusions</span>
                        </h5>
                        <button type="button" class="btn btn-sm btn-dark px-3 py-1 rounded" style="background-color: #0d4b43; border-color: #0d4b43;" onclick="addInclusionRow()">
                            <i class="mdi mdi-plus me-1"></i> Add Point
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <p class="fs-12 text-muted mb-3">Add items included in this tour (e.g. daily breakfast, toll taxes, 4x4 jeep safari, guide fees).</p>
                        <div id="inclusionsContainer" class="d-flex flex-column gap-2">
                            <div class="input-group input-group-sm inclusion-row">
                                <span class="input-group-text bg-light text-success"><i class="mdi mdi-check-circle"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" placeholder="e.g. Return Airfare with 20kg Baggage Allowance">
                                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                            </div>
                            <div class="input-group input-group-sm inclusion-row">
                                <span class="input-group-text bg-light text-success"><i class="mdi mdi-check-circle"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" placeholder="e.g. 4 Nights Stay in 4-Star Resort on Twin Sharing">
                                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                            </div>
                            <div class="input-group input-group-sm inclusion-row">
                                <span class="input-group-text bg-light text-success"><i class="mdi mdi-check-circle"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" placeholder="e.g. Dedicated AC Prado for Sightseeing and Transfers">
                                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Repeatable Hotel Amenities -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center gap-1">
                            <i class="mdi mdi-wifi text-primary fs-18"></i>
                            <span>Hotel Amenities</span>
                        </h5>
                        <button type="button" class="btn btn-sm btn-dark px-3 py-1 rounded" style="background-color: #0d4b43; border-color: #0d4b43;" onclick="addAmenityRow()">
                            <i class="mdi mdi-plus me-1"></i> Add Amenity
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <p class="fs-12 text-muted mb-3">Add hotel amenities surfaced to guest app (e.g. Free High-Speed WiFi, Room Heating, Mountain View Balcony).</p>
                        <div id="amenitiesContainer" class="d-flex flex-column gap-2">
                            <div class="input-group input-group-sm amenity-row">
                                <span class="input-group-text bg-light text-primary"><i class="mdi mdi-star-circle"></i></span>
                                <input type="text" name="hotel_amenities[]" class="form-control" placeholder="e.g. High-Speed Wi-Fi & 24/7 Room Service">
                                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                            </div>
                            <div class="input-group input-group-sm amenity-row">
                                <span class="input-group-text bg-light text-primary"><i class="mdi mdi-star-circle"></i></span>
                                <input type="text" name="hotel_amenities[]" class="form-control" placeholder="e.g. Mountain View Deluxe Balcony">
                                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Rich Content Sections -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center">
                    <i class="mdi mdi-file-document-edit-outline text-primary me-2"></i>
                    <span>Package Details & Rich Content</span>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13">Key Highlights / Tour Features</label>
                        <textarea name="key_features" rows="4" class="form-control"
                            placeholder="e.g. Visit Lower Kachura Lake, Shangrila Resort, Katpana Cold Desert, Manthal Buddha Rock & Khaplu Palace.">{{ old('key_features') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13">Requirements & Mandatory Documents</label>
                        <textarea name="requirements_text" rows="4" class="form-control"
                            placeholder="e.g. Original CNIC / Passport required for boarding. Warm clothing recommended for evening excursions.">{{ old('requirements_text') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13">Detailed Description / Tour Notes</label>
                        <textarea name="description_notes" rows="4" class="form-control"
                            placeholder="Comprehensive itinerary notes, day-by-day briefing, or customized guide instructions.">{{ old('description_notes') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13">Terms & Cancellation Policy</label>
                        <textarea name="terms_conditions" rows="4" class="form-control"
                            placeholder="Non-refundable deposits, cancellation terms, weather contingency protocols.">{{ old('terms_conditions') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="d-flex justify-content-end gap-2 pb-5">
            <a href="{{ route('mobile-ops.domestic-packages.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #0d4b43; border-color: #0d4b43;">
                <i class="mdi mdi-content-save me-1"></i> Save Domestic Package
            </button>
        </div>
    </form>

    <script>
        const domesticCities = @json($cities);
        const mealPlans = @json($mealPlans);
        const hotelCategories = @json($hotelCategories);

        document.addEventListener('DOMContentLoaded', function() {
            const multiSwitch = document.getElementById('multiCitySwitch');
            const multiSection = document.getElementById('multiCitySection');
            const addDestBtn = document.getElementById('addDestinationBtn');
            const destContainer = document.getElementById('destinationsContainer');
            const hotelCardsContainer = document.getElementById('destinationHotelCardsContainer');

            let destIndex = 0;

            multiSwitch.addEventListener('change', function() {
                if (this.checked) {
                    multiSection.style.display = 'block';
                    if (destContainer.children.length === 0) {
                        addDestinationRow();
                    }
                } else {
                    multiSection.style.display = 'none';
                }
            });

            addDestBtn.addEventListener('click', function() {
                addDestinationRow();
            });

            function addDestinationRow(preSelectedCity = '', preData = {}) {
                const currentIndex = destIndex++;

                const row = document.createElement('div');
                row.className = 'd-flex align-items-center gap-2 dest-row';
                row.id = `dest_row_${currentIndex}`;

                let optionsHtml = '<option value="">Select Destination City / Stop</option>';
                domesticCities.forEach(c => {
                    const isSelected = (preSelectedCity === c) ? 'selected' : '';
                    optionsHtml += `<option value="${c}" ${isSelected}>${c}</option>`;
                });

                row.innerHTML = `
                    <div class="flex-grow-1">
                        <select name="additional_destinations[${currentIndex}][city]" class="form-select dest-city-select" data-index="${currentIndex}">
                            ${optionsHtml}
                        </select>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-remove-dest" data-index="${currentIndex}" title="Remove Destination">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                `;

                destContainer.appendChild(row);
                createHotelCardForDestination(currentIndex, preSelectedCity, preData);

                const selectElem = row.querySelector('.dest-city-select');
                selectElem.addEventListener('change', function() {
                    updateHotelCardHeader(currentIndex, this.value);
                });

                row.querySelector('.btn-remove-dest').addEventListener('click', function() {
                    removeDestination(currentIndex);
                });
            }

            function createHotelCardForDestination(index, cityName = '', preData = {}) {
                const card = document.createElement('div');
                card.className = 'card border border-success-subtle shadow-none dest-hotel-card';
                card.id = `dest_hotel_card_${index}`;
                card.style.borderRadius = '10px';

                const displayName = cityName || 'Stop';

                let mealOptions = '<option value="">Select Meal Plan</option>';
                mealPlans.forEach(m => {
                    const isSelected = (preData.hotel_meal_plan === m) ? 'selected' : '';
                    mealOptions += `<option value="${m}" ${isSelected}>${m}</option>`;
                });

                let catOptions = '<option value="">Select Category</option>';
                hotelCategories.forEach(cat => {
                    const isSelected = (preData.hotel_category === cat) ? 'selected' : '';
                    catOptions += `<option value="${cat}" ${isSelected}>${cat}</option>`;
                });

                card.innerHTML = `
                    <div class="card-body p-3 bg-white" style="border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fs-14 fw-bold text-success m-0 dest-card-title">
                                <i class="mdi mdi-office-building me-1"></i> <span class="city-label">${displayName}</span> Hotel Details
                            </h6>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-dest-card-btn" data-index="${index}">
                                <i class="mdi mdi-trash-can-outline me-1"></i> Remove Stop
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13"><span class="city-label">${displayName}</span> Hotel Name</label>
                                <input type="text" name="additional_destinations[${index}][hotel_name]" class="form-control" placeholder="e.g. Serena Inn / PTDC Motel" value="${preData.hotel_name || ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13"><span class="city-label">${displayName}</span> Distance / Location</label>
                                <input type="text" name="additional_destinations[${index}][hotel_distance]" class="form-control" placeholder="e.g. Main Valley Road" value="${preData.hotel_distance || ''}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13"><span class="city-label">${displayName}</span> Stay Period</label>
                                <input type="text" name="additional_destinations[${index}][hotel_period]" class="form-control" placeholder="e.g. 2 Nights" value="${preData.hotel_period || ''}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13"><span class="city-label">${displayName}</span> Meal Plan</label>
                                <select name="additional_destinations[${index}][hotel_meal_plan]" class="form-select">
                                    ${mealOptions}
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13"><span class="city-label">${displayName}</span> Hotel Category</label>
                                <select name="additional_destinations[${index}][hotel_category]" class="form-select">
                                    ${catOptions}
                                </select>
                            </div>
                        </div>
                    </div>
                `;

                hotelCardsContainer.appendChild(card);
                card.querySelector('.remove-dest-card-btn').addEventListener('click', function() {
                    removeDestination(index);
                });
            }

            function updateHotelCardHeader(index, cityName) {
                const card = document.getElementById(`dest_hotel_card_${index}`);
                if (card) {
                    const labels = card.querySelectorAll('.city-label');
                    labels.forEach(lbl => lbl.textContent = cityName || 'Stop');
                }
            }

            function removeDestination(index) {
                const row = document.getElementById(`dest_row_${index}`);
                const card = document.getElementById(`dest_hotel_card_${index}`);
                if (row) row.remove();
                if (card) card.remove();
            }
        });

        function addInclusionRow() {
            const container = document.getElementById('inclusionsContainer');
            const div = document.createElement('div');
            div.className = 'input-group input-group-sm inclusion-row';
            div.innerHTML = `
                <span class="input-group-text bg-light text-success"><i class="mdi mdi-check-circle"></i></span>
                <input type="text" name="inclusions[]" class="form-control" placeholder="New inclusion item...">
                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
            `;
            container.appendChild(div);
        }

        function addAmenityRow() {
            const container = document.getElementById('amenitiesContainer');
            const div = document.createElement('div');
            div.className = 'input-group input-group-sm amenity-row';
            div.innerHTML = `
                <span class="input-group-text bg-light text-primary"><i class="mdi mdi-star-circle"></i></span>
                <input type="text" name="hotel_amenities[]" class="form-control" placeholder="New hotel amenity...">
                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
            `;
            container.appendChild(div);
        }

        function removeRow(btn) {
            const row = btn.closest('.input-group');
            if (row) row.remove();
        }

        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(previewId);
                    img.src = e.target.result;
                    img.parentElement.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
