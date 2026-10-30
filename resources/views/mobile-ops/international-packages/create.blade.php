@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fs-18 fw-bold text-dark m-0">Create International Package</h4>
                <p class="text-muted fs-13 mb-0">Build an international holiday package with hotel options, flights, multi-country stops, and rates.</p>
            </div>
            <a href="{{ route('mobile-ops.international-packages.index') }}"
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

    <form action="{{ route('mobile-ops.international-packages.store') }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @csrf

        <!-- 1. Basic Package Information & Dual Country / Multi-Country Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fs-15 fw-bold text-dark m-0">Basic Package Information</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="package_title" class="form-control"
                            placeholder="e.g. 7-Day Enchanting Baku & Gabala Premium Holiday" value="{{ old('package_title') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Package Code</label>
                        <input type="text" name="package_code" class="form-control" placeholder="e.g. INT-BAK-01"
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
                            placeholder="e.g. Flame Towers, Fire Mountain, Cable Car & Luxury 5★ Stay"
                            value="{{ old('subtitle') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sightseeing & Excursions Summary</label>
                        <input type="text" name="sightseeing_summary" class="form-control"
                            placeholder="e.g. Tashkent City Tour, Chimgan Mountains, Samarkand High-Speed Train Excursion"
                            value="{{ old('sightseeing_summary') }}">
                        <small class="text-muted fs-11">Appears as highlight badge row on mobile tour card.</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Destination Country <span class="text-danger">*</span></label>
                        <select name="destination_country" class="form-select" required>
                            <option value="">Select Country</option>
                            @foreach ($countriesList as $cntry)
                                <option value="{{ $cntry }}" {{ old('destination_country') === $cntry ? 'selected' : '' }}>{{ $cntry }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Destination City</label>
                        <input type="text" name="destination_city" class="form-control" placeholder="e.g. Baku / Dubai / Istanbul"
                            value="{{ old('destination_city') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Departure City</label>
                        <select name="departure_city" class="form-select">
                            <option value="">Any Pakistan Airport</option>
                            @foreach($departureCities as $dCity)
                                <option value="{{ $dCity }}" {{ old('departure_city') === $dCity ? 'selected' : '' }}>{{ $dCity }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Visa Status <span class="text-danger">*</span></label>
                        <select name="visa_status" class="form-select" required>
                            <option value="included" {{ old('visa_status', 'included') === 'included' ? 'selected' : '' }}>Visa Included in Package</option>
                            <option value="excluded" {{ old('visa_status') === 'excluded' ? 'selected' : '' }}>Visa Excluded (Customer Arranges)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Duration Days</label>
                        <input type="number" id="stay_days_input" class="form-control" placeholder="e.g. 7" readonly style="background-color: #f8f9fa;">
                        <small class="text-muted fs-11">Auto-calculated: Nights + 1</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Duration Nights</label>
                        <input type="number" name="stay_nights" id="stay_nights_input" class="form-control" placeholder="e.g. 6" min="1"
                            value="{{ old('stay_nights', 6) }}" oninput="updateDaysFromNights(this.value)">
                        <input type="hidden" name="duration_nights" id="duration_nights_hidden" value="{{ old('stay_nights', 6) }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_group_package" value="1" id="groupPkgSwitch"
                                {{ old('is_group_package') ? 'checked' : '' }} style="cursor: pointer; width: 44px; height: 22px;">
                            <label class="form-check-label fw-semibold ms-2 fs-13 text-dark" for="groupPkgSwitch">Group Tour</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Valid From</label>
                        <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
                    </div>

                    <!-- Dual Country / Multi-Country Package Toggle Section (Merged per design) -->
                    <div class="col-12 mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded-3 border">
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch me-3 mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_dual_country" id="dualCountrySwitch" value="1"
                                        {{ old('is_dual_country') ? 'checked' : '' }} style="cursor: pointer; width: 48px; height: 24px;">
                                </div>
                                <label class="form-check-label fw-bold text-dark fs-14 mb-0 cursor-pointer d-flex align-items-center gap-1" for="dualCountrySwitch">
                                    <i class="mdi mdi-earth text-success fs-18"></i>
                                    <span>Dual Country / Multi-Country Package</span>
                                </label>
                            </div>
                            <span class="text-muted fs-12 d-none d-md-inline">Enable to add 2nd or multiple destination countries with separate hotel details</span>
                        </div>

                        <!-- Dual Country Container -->
                        <div id="dualCountrySection" style="{{ old('is_dual_country') ? '' : 'display: none;' }}">
                            <label class="form-label fw-semibold text-secondary mb-2">Additional Destination Countries / Cities</label>

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
                        <input type="text" name="primary_hotel" class="form-control" placeholder="e.g. Hilton Baku / Grand Hotel Europe"
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
                                    <input type="text" name="outbound_airline" class="form-control form-control-sm" placeholder="e.g. Azerbaijan Airlines / Emirates"
                                        value="{{ old('outbound_airline') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-semibold">Departure Airport</label>
                                    <input type="text" name="outbound_origin" class="form-control form-control-sm" placeholder="e.g. Karachi (KHI)"
                                        value="{{ old('outbound_origin') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-semibold">Arrival Airport</label>
                                    <input type="text" name="outbound_destination" class="form-control form-control-sm" placeholder="e.g. Baku (GYD)"
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
                                    <input type="text" name="inbound_airline" class="form-control form-control-sm" placeholder="e.g. Azerbaijan Airlines / Emirates"
                                        value="{{ old('inbound_airline') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-semibold">Departure Airport</label>
                                    <input type="text" name="inbound_origin" class="form-control form-control-sm" placeholder="e.g. Baku (GYD)"
                                        value="{{ old('inbound_origin') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-semibold">Arrival Airport</label>
                                    <input type="text" name="inbound_destination" class="form-control form-control-sm" placeholder="e.g. Karachi (KHI)"
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
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center">
                    <i class="mdi mdi-currency-usd text-success me-2 fs-18"></i>
                    <span>Pricing Matrix & Ground Fleet Logistics</span>
                </h5>
                <div class="d-flex align-items-center bg-warning-subtle px-3 py-1 rounded-pill border border-warning">
                    <div class="form-check form-switch m-0 d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" name="is_price_on_request" value="1" id="priceOnRequestSwitch"
                            {{ old('is_price_on_request') ? 'checked' : '' }} style="cursor: pointer; width: 42px; height: 21px;">
                        <label class="form-check-label fw-bold text-dark fs-12 mb-0" for="priceOnRequestSwitch">
                            Price on Request (Brochure Mode)
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info py-2 px-3 mb-3 fs-12 d-none" id="priceOnRequestNotice">
                    <i class="mdi mdi-information-outline me-1"></i>
                    <strong>Price on Request Enabled:</strong> Best Available Price will show <em>"On Request / Final price depends on travel dates, hotel category, and flight availability"</em> in the mobile app. Numeric room rate fields below become optional.
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13">Ground Transfer Fleet Mode</label>
                        <select name="ground_transfer_fleet" class="form-select">
                            <option value="">Select Vehicle / Transfer Mode</option>
                            @foreach ($groundFleets as $gf)
                                <option value="{{ $gf }}" {{ old('ground_transfer_fleet') === $gf ? 'selected' : '' }}>{{ $gf }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13 text-success">Per-Person Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_per_person" id="price_per_person" class="form-control fw-bold text-success rate-input" placeholder="0.00"
                            value="{{ old('price_per_person') }}">
                    </div>

                    <!-- Room Rates Matrix -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold fs-13">Sharing Room Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_sharing" class="form-control rate-input" placeholder="0.00"
                            value="{{ old('price_sharing') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold fs-13">Quad Room Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_quad" class="form-control rate-input" placeholder="0.00"
                            value="{{ old('price_quad') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold fs-13">Triple Room Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_triple" class="form-control rate-input" placeholder="0.00"
                            value="{{ old('price_triple') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold fs-13">Double Room Rate (PKR)</label>
                        <input type="number" step="0.01" name="price_double" class="form-control rate-input" placeholder="0.00"
                            value="{{ old('price_double') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Package Inclusions & Package Excludes Builders (Mirrored Brochure Repeaters) -->
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
                        <p class="fs-12 text-muted mb-3">Add items included in this international holiday package (e.g. Visa assistance, travel insurance, daily breakfast).</p>
                        <div id="inclusionsContainer" class="d-flex flex-column gap-2">
                            <div class="input-group input-group-sm inclusion-row">
                                <span class="input-group-text bg-light text-success"><i class="mdi mdi-check-circle"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" placeholder="e.g. Azerbaijan Official Tourist E-Visa with Processing">
                                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                            </div>
                            <div class="input-group input-group-sm inclusion-row">
                                <span class="input-group-text bg-light text-success"><i class="mdi mdi-check-circle"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" placeholder="e.g. Return International Air Tickets on Economy Class">
                                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                            </div>
                            <div class="input-group input-group-sm inclusion-row">
                                <span class="input-group-text bg-light text-success"><i class="mdi mdi-check-circle"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" placeholder="e.g. 6 Nights 4-Star Hotel Stay with Daily Buffet Breakfast">
                                <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Repeatable Excludes (Mirror of Inclusions) -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center gap-1">
                            <i class="mdi mdi-close-octagon text-danger fs-18"></i>
                            <span class="text-danger">Package Excludes</span>
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 rounded" onclick="addExclusionRow()">
                            <i class="mdi mdi-plus me-1"></i> Add Exclusion
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <p class="fs-12 text-muted mb-3">Add items strictly excluded per promotional brochure (e.g. International air tickets unless confirmed, personal expenses, meals not mentioned).</p>
                        <div id="excludesContainer" class="d-flex flex-column gap-2">
                            <div class="border rounded p-2 bg-light-subtle exclusion-block">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="text-danger fs-14"><i class="mdi mdi-minus-circle"></i></span>
                                    <input type="text" name="package_excludes[0][title]" class="form-control form-control-sm fw-semibold" placeholder="Exclusion Title (e.g. International Air Tickets)" value="International Air Tickets">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBlock(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                </div>
                                <input type="text" name="package_excludes[0][description]" class="form-control form-control-sm text-muted" placeholder="Optional details (e.g. Unless confirmed / issued separately)" value="Unless confirmed / issued separately">
                            </div>

                            <div class="border rounded p-2 bg-light-subtle exclusion-block">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="text-danger fs-14"><i class="mdi mdi-minus-circle"></i></span>
                                    <input type="text" name="package_excludes[1][title]" class="form-control form-control-sm fw-semibold" placeholder="Exclusion Title (e.g. Meals not mentioned in itinerary)" value="Meals not mentioned in itinerary">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBlock(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                </div>
                                <input type="text" name="package_excludes[1][description]" class="form-control form-control-sm text-muted" placeholder="Optional details (e.g. Lunch & Dinners unless stated)" value="Lunch & Dinners unless stated">
                            </div>

                            <div class="border rounded p-2 bg-light-subtle exclusion-block">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="text-danger fs-14"><i class="mdi mdi-minus-circle"></i></span>
                                    <input type="text" name="package_excludes[2][title]" class="form-control form-control-sm fw-semibold" placeholder="Exclusion Title (e.g. Personal Expenses & Tips)" value="Personal Expenses & Tips">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBlock(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                </div>
                                <input type="text" name="package_excludes[2][description]" class="form-control form-control-sm text-muted" placeholder="Optional details (e.g. Laundry, telephone calls, porterage, optional activities)" value="Laundry, telephone calls, porterage, optional tours">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Day-by-Day Suggested Itinerary Repeater -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="fs-15 fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="mdi mdi-map-marker-path text-primary fs-18"></i>
                    <span>Day-by-Day Suggested Itinerary</span>
                    <span class="badge bg-primary-subtle text-primary fs-11 fw-normal">Brochure Structured View</span>
                </h5>
                <button type="button" class="btn btn-sm btn-primary px-3 py-1 rounded" style="background-color: #0d4b43; border-color: #0d4b43;" onclick="addItineraryDayRow()">
                    <i class="mdi mdi-plus me-1"></i> Add Itinerary Day
                </button>
            </div>
            <div class="card-body p-4">
                <p class="fs-12 text-muted mb-3">Structured day-by-day tour itinerary rendered as clean cards in the Flutter mobile application instead of an unformatted text block.</p>
                <div id="itineraryDaysContainer" class="d-flex flex-column gap-3">
                    <!-- Day 1 Card -->
                    <div class="card border border-light-subtle shadow-none itinerary-day-card" data-day="1" style="background-color: #fbfbfb; border-radius: 10px;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fs-13 fw-bold text-dark m-0 d-flex align-items-center gap-1">
                                    <span class="badge bg-success me-1 day-badge">Day 1</span>
                                    <span>Arrival & Check-in</span>
                                </h6>
                                <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="removeItineraryDay(this)" title="Remove Day">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <label class="form-label fs-11 fw-semibold mb-1">Day #</label>
                                    <input type="number" name="itinerary_days[0][day_number]" class="form-control form-control-sm itin-day-number" value="1" min="1">
                                </div>
                                <div class="col-md-10">
                                    <label class="form-label fs-11 fw-semibold mb-1">Day Title / Theme</label>
                                    <input type="text" name="itinerary_days[0][title]" class="form-control form-control-sm itin-title" placeholder="e.g. Arrival in Baku - Welcome & Leisure Evening" value="Arrival & Hotel Check-in">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-11 fw-semibold mb-1">Description / Activities Brief</label>
                                    <textarea name="itinerary_days[0][description]" rows="2" class="form-control form-control-sm" placeholder="Detailed day activities, pickup timings, meal schedules...">Airport pickup and transfer to hotel. Rest of the day at leisure to explore the local surroundings.</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-11 fw-semibold mb-1">Highlights / Badges (Comma-separated or sub-points)</label>
                                    <input type="text" name="itinerary_days[0][highlights]" class="form-control form-control-sm" placeholder="e.g. VIP Airport Transfer, Hotel Check-in, Evening Stroll" value="Airport Transfer, Check-in, Free Evening">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Repeatable Hotel Amenities -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
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
                <p class="fs-12 text-muted mb-3">Add hotel amenities surfaced to guest app (e.g. Swimming Pool, Spa & Sauna, Central Location).</p>
                <div id="amenitiesContainer" class="d-flex flex-column gap-2">
                    <div class="input-group input-group-sm amenity-row">
                        <span class="input-group-text bg-light text-primary"><i class="mdi mdi-star-circle"></i></span>
                        <input type="text" name="hotel_amenities[]" class="form-control" placeholder="e.g. High-Speed Wi-Fi & 24/7 Room Service">
                        <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                    </div>
                    <div class="input-group input-group-sm amenity-row">
                        <span class="input-group-text bg-light text-primary"><i class="mdi mdi-star-circle"></i></span>
                        <input type="text" name="hotel_amenities[]" class="form-control" placeholder="e.g. Indoor Swimming Pool, Spa & Gym">
                        <button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. Rich Content Sections -->
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
                            placeholder="e.g. Heydar Aliyev Center, Maiden Tower, Yanardag Fire Mountain, Tufandag Mountain Ropeway Cable Car.">{{ old('key_features') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13">Requirements & Mandatory Documents</label>
                        <textarea name="requirements_text" rows="4" class="form-control"
                            placeholder="e.g. Machine-readable Passport with at least 6 months validity. 2 Passport-sized photos on white background.">{{ old('requirements_text') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13">Detailed Description / Itinerary Notes</label>
                        <textarea name="description_notes" rows="4" class="form-control"
                            placeholder="Comprehensive itinerary notes, day-by-day briefing, or guide instructions.">{{ old('description_notes') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-13">Terms & Cancellation Policy</label>
                        <textarea name="terms_conditions" rows="4" class="form-control"
                            placeholder="International airline cancellation penalties, visa rejection terms, embassy guidelines.">{{ old('terms_conditions') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="d-flex justify-content-end gap-2 pb-5">
            <a href="{{ route('mobile-ops.international-packages.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #0d4b43; border-color: #0d4b43;">
                <i class="mdi mdi-content-save me-1"></i> Save International Package
            </button>
        </div>
    </form>

    <script>
        const allCountries = @json($countriesList);
        const mealPlans = @json($mealPlans);
        const hotelCategories = @json($hotelCategories);

        document.addEventListener('DOMContentLoaded', function() {
            const dualSwitch = document.getElementById('dualCountrySwitch');
            const dualSection = document.getElementById('dualCountrySection');
            const addDestBtn = document.getElementById('addDestinationBtn');
            const destContainer = document.getElementById('destinationsContainer');
            const hotelCardsContainer = document.getElementById('destinationHotelCardsContainer');

            let destIndex = 0;

            dualSwitch.addEventListener('change', function() {
                if (this.checked) {
                    dualSection.style.display = 'block';
                    if (destContainer.children.length === 0) {
                        addDestinationRow();
                    }
                } else {
                    dualSection.style.display = 'none';
                }
            });

            addDestBtn.addEventListener('click', function() {
                addDestinationRow();
            });

            function addDestinationRow(preSelectedCountry = '', preData = {}) {
                const currentIndex = destIndex++;

                const row = document.createElement('div');
                row.className = 'd-flex align-items-center gap-2 dest-row';
                row.id = `dest_row_${currentIndex}`;

                let optionsHtml = '<option value="">Select Destination Country / City</option>';
                allCountries.forEach(c => {
                    const isSelected = (preSelectedCountry === c) ? 'selected' : '';
                    optionsHtml += `<option value="${c}" ${isSelected}>${c}</option>`;
                });

                row.innerHTML = `
                    <div class="flex-grow-1">
                        <select name="additional_destinations[${currentIndex}][country]" class="form-select dest-country-select" data-index="${currentIndex}">
                            ${optionsHtml}
                        </select>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-remove-dest" data-index="${currentIndex}" title="Remove Destination">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                `;

                destContainer.appendChild(row);
                createHotelCardForDestination(currentIndex, preSelectedCountry, preData);

                const selectElem = row.querySelector('.dest-country-select');
                selectElem.addEventListener('change', function() {
                    updateHotelCardHeader(currentIndex, this.value);
                });

                row.querySelector('.btn-remove-dest').addEventListener('click', function() {
                    removeDestination(currentIndex);
                });
            }

            function createHotelCardForDestination(index, countryName = '', preData = {}) {
                const card = document.createElement('div');
                card.className = 'card border border-success-subtle shadow-none dest-hotel-card';
                card.id = `dest_hotel_card_${index}`;
                card.style.borderRadius = '10px';

                const displayName = countryName || 'Destination';

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
                                <i class="mdi mdi-office-building me-1"></i> <span class="country-label">${displayName}</span> Hotel Details
                            </h6>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-dest-card-btn" data-index="${index}">
                                <i class="mdi mdi-trash-can-outline me-1"></i> Remove Destination
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Name</label>
                                <input type="text" name="additional_destinations[${index}][hotel_name]" class="form-control" placeholder="e.g. B&B Hotel Milano Ornato (3★)" value="${preData.hotel_name || ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Distance / Address</label>
                                <input type="text" name="additional_destinations[${index}][hotel_distance]" class="form-control" placeholder="e.g. Via Luigi Ornato or 100m" value="${preData.hotel_distance || ''}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Period</label>
                                <input type="text" name="additional_destinations[${index}][hotel_period]" class="form-control" placeholder="e.g. 3 Nights or Dates" value="${preData.hotel_period || ''}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Meal Plan</label>
                                <select name="additional_destinations[${index}][hotel_meal_plan]" class="form-select">
                                    ${mealOptions}
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13"><span class="country-label">${displayName}</span> Hotel Category</label>
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

            function updateHotelCardHeader(index, countryName) {
                const card = document.getElementById(`dest_hotel_card_${index}`);
                if (card) {
                    const labels = card.querySelectorAll('.country-label');
                    labels.forEach(lbl => lbl.textContent = countryName || 'Destination');
                }
            }

            function removeDestination(index) {
                const row = document.getElementById(`dest_row_${index}`);
                const card = document.getElementById(`dest_hotel_card_${index}`);
                if (row) row.remove();
                if (card) card.remove();
            }

            // Price on Request Switch Handler
            const priceReqSwitch = document.getElementById('priceOnRequestSwitch');
            const priceNotice = document.getElementById('priceOnRequestNotice');
            const rateInputs = document.querySelectorAll('.rate-input');

            function togglePriceOnRequest(enabled) {
                if (enabled) {
                    priceNotice.classList.remove('d-none');
                    rateInputs.forEach(input => {
                        input.placeholder = 'Optional (On Request)';
                        input.classList.add('bg-light');
                    });
                } else {
                    priceNotice.classList.add('d-none');
                    rateInputs.forEach(input => {
                        input.placeholder = '0.00';
                        input.classList.remove('bg-light');
                    });
                }
            }

            if (priceReqSwitch) {
                togglePriceOnRequest(priceReqSwitch.checked);
                priceReqSwitch.addEventListener('change', function() {
                    togglePriceOnRequest(this.checked);
                });
            }

            // Duration Days Calculation
            const nightsInput = document.getElementById('stay_nights_input');
            if (nightsInput) {
                updateDaysFromNights(nightsInput.value);
            }
        });

        function updateDaysFromNights(nightsVal) {
            const daysInput = document.getElementById('stay_days_input');
            const durationHidden = document.getElementById('duration_nights_hidden');
            const val = parseInt(nightsVal, 10);
            if (!isNaN(val) && val > 0) {
                if (daysInput) daysInput.value = val + 1;
                if (durationHidden) durationHidden.value = val;
            } else {
                if (daysInput) daysInput.value = '';
                if (durationHidden) durationHidden.value = '';
            }
        }

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

        let excludeIndex = 3;
        function addExclusionRow() {
            const container = document.getElementById('excludesContainer');
            const idx = excludeIndex++;
            const div = document.createElement('div');
            div.className = 'border rounded p-2 bg-light-subtle exclusion-block';
            div.innerHTML = `
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="text-danger fs-14"><i class="mdi mdi-minus-circle"></i></span>
                    <input type="text" name="package_excludes[${idx}][title]" class="form-control form-control-sm fw-semibold" placeholder="Exclusion Title (e.g. Visa Fee / City Tax)">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBlock(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                </div>
                <input type="text" name="package_excludes[${idx}][description]" class="form-control form-control-sm text-muted" placeholder="Optional details (e.g. Payable directly at hotel reception)">
            `;
            container.appendChild(div);
        }

        let itineraryIndex = 1;
        function addItineraryDayRow() {
            const container = document.getElementById('itineraryDaysContainer');
            const idx = itineraryIndex++;
            const dayNum = container.querySelectorAll('.itinerary-day-card').length + 1;
            const div = document.createElement('div');
            div.className = 'card border border-light-subtle shadow-none itinerary-day-card';
            div.style.backgroundColor = '#fbfbfb';
            div.style.borderRadius = '10px';
            div.dataset.day = dayNum;
            div.innerHTML = `
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fs-13 fw-bold text-dark m-0 d-flex align-items-center gap-1">
                            <span class="badge bg-success me-1 day-badge">Day ${dayNum}</span>
                            <span class="day-title-text">New Tour Day</span>
                        </h6>
                        <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="removeItineraryDay(this)" title="Remove Day">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label fs-11 fw-semibold mb-1">Day #</label>
                            <input type="number" name="itinerary_days[${idx}][day_number]" class="form-control form-control-sm itin-day-number" value="${dayNum}" min="1">
                        </div>
                        <div class="col-md-10">
                            <label class="form-label fs-11 fw-semibold mb-1">Day Title / Theme</label>
                            <input type="text" name="itinerary_days[${idx}][title]" class="form-control form-control-sm itin-title" placeholder="e.g. Full Day Excursion & Sightseeing Tour" oninput="updateDayTitle(this)">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fs-11 fw-semibold mb-1">Description / Activities Brief</label>
                            <textarea name="itinerary_days[${idx}][description]" rows="2" class="form-control form-control-sm" placeholder="Detailed day activities, pickup timings, meal schedules..."></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fs-11 fw-semibold mb-1">Highlights / Badges (Comma-separated or sub-points)</label>
                            <input type="text" name="itinerary_days[${idx}][highlights]" class="form-control form-control-sm" placeholder="e.g. City Tour, Cable Car, Lunch Included">
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        function updateDayTitle(input) {
            const card = input.closest('.itinerary-day-card');
            const titleSpan = card.querySelector('.day-title-text');
            if (titleSpan) {
                titleSpan.textContent = input.value.trim() || 'New Tour Day';
            }
        }

        function removeItineraryDay(btn) {
            const card = btn.closest('.itinerary-day-card');
            if (card) {
                card.remove();
                reindexItineraryDays();
            }
        }

        function reindexItineraryDays() {
            const container = document.getElementById('itineraryDaysContainer');
            const cards = container.querySelectorAll('.itinerary-day-card');
            cards.forEach((card, index) => {
                const dayNum = index + 1;
                const badge = card.querySelector('.day-badge');
                const numInput = card.querySelector('.itin-day-number');
                if (badge) badge.textContent = `Day ${dayNum}`;
                if (numInput) numInput.value = dayNum;
            });
        }

        function removeBlock(btn) {
            const block = btn.closest('.exclusion-block');
            if (block) block.remove();
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
