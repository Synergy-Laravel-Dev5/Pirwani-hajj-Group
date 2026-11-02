@extends('layout.mobile-master')

@section('content')
    <div class="pt-3 pb-2">
        <h4 class="fs-18 fw-bold text-dark m-0">Edit Visa Country</h4>
        <p class="text-muted fs-13 mb-0">Update country visa rates, processing times, and document requirements.</p>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('mobile-ops.visa-countries.update', $country->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf

        <!-- Country Details Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h5 class="fs-15 fw-bold text-dark mb-3">Country Details</h5>

                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Country Name <span class="text-danger">*</span></label>
                        <input type="text" name="country_name" class="form-control"
                            placeholder="e.g. United Arab Emirates"
                            value="{{ old('country_name', $country->country_name) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Country Code <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="country_code" id="country_code" class="form-control text-uppercase" placeholder="e.g. PK, US, SA, AE"
                                maxlength="10" value="{{ old('country_code', $country->country_code) }}" required>
                            <span class="input-group-text bg-white p-1" style="min-width: 48px; display: flex; align-items: center; justify-content: center;">
                                <img id="flagPreview" src="{{ $country->country_flag_url ?? '' }}" alt="Flag Preview"
                                    class="rounded border {{ $country->country_flag_url ? '' : 'd-none' }}" style="width: 32px; height: 22px; object-fit: cover;">
                                <span id="flagPlaceholder" class="text-muted fs-11 {{ $country->country_flag_url ? 'd-none' : '' }}"><i class="mdi mdi-flag-outline fs-16"></i></span>
                            </span>
                        </div>
                        <small class="text-muted fs-11">2-letter ISO code (e.g. PK, US, TR, SA).</small>
                        <div id="flagStatusBadge" class="mt-1 d-none">
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-11 py-1 px-2 text-wrap text-start">
                                <i class="mdi mdi-check-circle-outline me-1"></i> Flag auto-detected. You can leave the upload blank to use this flag, or choose a file to override.
                            </span>
                        </div>
                    </div>

                    <!-- Universal Active Switch -->
                    <div class="col-md-3 d-flex align-items-center pt-md-3">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="universalStatus"
                                value="1" {{ old('is_active', $country->is_active) ? 'checked' : '' }}
                                style="cursor: pointer; width: 45px; height: 22px;">
                            <label class="form-check-label fw-bold ms-2" for="universalStatus">Active</label>
                        </div>
                    </div>

                    <!-- Monument Picture -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Monument Picture (Optional)</label>
                        @if ($country->monument_picture_url)
                            <div class="mb-2 d-flex align-items-center gap-2" id="currentMonumentBox">
                                <img src="{{ $country->monument_picture_url }}" class="rounded shadow-sm border"
                                    style="width: 70px; height: 48px; object-fit: cover;">
                                <span class="text-muted fs-12">Current image</span>
                            </div>
                        @endif
                        <div id="monumentPreviewContainer" class="mb-2 d-none align-items-center gap-2">
                            <img id="monumentPreview" src="" alt="Monument Preview" class="rounded border shadow-sm" style="width: 70px; height: 48px; object-fit: cover;">
                            <span class="text-muted fs-12">New monument preview</span>
                        </div>
                        <input type="file" name="monument_picture" id="monument_picture_input" class="form-control" accept="image/*">
                        <small class="text-muted fs-11">Leave empty to keep existing. JPG, PNG, WEBP. Max 5MB.</small>
                    </div>

                    <!-- Country Flag -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Country Flag (Optional)</label>
                        @if ($country->country_flag_url)
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <img src="{{ $country->country_flag_url }}" id="currentFlagThumbnail" class="rounded shadow-sm"
                                    style="width: 50px; height: 32px; object-fit: cover;">
                                <span class="text-muted fs-12">Current flag</span>
                            </div>
                        @endif
                        <input type="file" name="country_flag" id="country_flag_input" class="form-control" accept="image/*">
                        <small class="text-muted fs-11">Leave empty to keep existing (or auto-fetch if changing country code). JPG, PNG, WEBP, or SVG. Max
                            2MB.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visa Types Section -->
        <div id="visaTypesContainer">
            @forelse($country->visaTypes as $index => $type)
                <div class="card border-0 shadow-sm mb-4 visa-type-card" data-index="{{ $index }}"
                    style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fs-15 fw-bold text-dark m-0">Visa Type #{{ $index + 1 }}</h5>
                            <button type="button"
                                class="btn btn-outline-danger btn-sm remove-type-btn {{ $loop->first && $loop->count == 1 ? 'd-none' : '' }}">
                                <i class="mdi mdi-close"></i> Remove
                            </button>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Visa Name <span class="text-danger">*</span></label>
                                <input type="text" name="types[{{ $index }}][visa_name]" class="form-control"
                                    placeholder="e.g. 30 Days Tourist Visa" value="{{ $type->visa_name }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">B2B Rate (PKR) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="types[{{ $index }}][b2b_rate]"
                                    class="form-control" placeholder="15000" value="{{ $type->b2b_rate_pkr }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Visa Fee (USD) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="types[{{ $index }}][visa_fee]"
                                    class="form-control" placeholder="90" value="{{ $type->visa_fee_usd }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Process Time</label>
                                <input type="text" name="types[{{ $index }}][process_time]"
                                    class="form-control" placeholder="e.g. 5 to 7 Working Days"
                                    value="{{ $type->process_time }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <input type="text" name="types[{{ $index }}][notes]" class="form-control"
                                    placeholder="Additional guidelines or terms..." value="{{ $type->notes }}">
                            </div>
                        </div>

                        <!-- Requirements Section -->
                        <div class="border-top pt-3">
                            <label class="form-label fw-semibold mb-2">Requirements</label>
                            <div class="requirements-list" data-type-index="{{ $index }}">
                                @if (!empty($type->requirements) && is_array($type->requirements))
                                    @foreach ($type->requirements as $req)
                                        <div class="input-group mb-2 requirement-item">
                                            <input type="text" name="types[{{ $index }}][requirements][]"
                                                class="form-control form-control-sm" value="{{ $req }}">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-req-btn">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 requirement-item">
                                        <input type="text" name="types[{{ $index }}][requirements][]"
                                            class="form-control form-control-sm" value="Valid Passport">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-req-btn"><i
                                                class="mdi mdi-close"></i></button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-dark mt-1 add-requirement-btn"
                                data-type-index="{{ $index }}">
                                + Add Requirement
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Fallback if country had no visa types -->
                <div class="card border-0 shadow-sm mb-4 visa-type-card" data-index="0" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fs-15 fw-bold text-dark m-0">Visa Type</h5>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-type-btn d-none">
                                <i class="mdi mdi-close"></i> Remove
                            </button>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Visa Name <span class="text-danger">*</span></label>
                                <input type="text" name="types[0][visa_name]" class="form-control"
                                    placeholder="e.g. Standard" value="Standard" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">B2B Rate (PKR) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="types[0][b2b_rate]" class="form-control"
                                    placeholder="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Visa Fee (USD) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="types[0][visa_fee]" class="form-control"
                                    placeholder="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Process Time</label>
                                <input type="text" name="types[0][process_time]" class="form-control"
                                    placeholder="e.g. 5 to 7 Days">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <input type="text" name="types[0][notes]" class="form-control"
                                    placeholder="Additional guidelines...">
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <label class="form-label fw-semibold mb-2">Requirements</label>
                            <div class="requirements-list" data-type-index="0">
                                <div class="input-group mb-2 requirement-item">
                                    <input type="text" name="types[0][requirements][]"
                                        class="form-control form-control-sm" value="Valid Passport">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-req-btn"><i
                                            class="mdi mdi-close"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-dark mt-1 add-requirement-btn"
                                data-type-index="0">
                                + Add Requirement
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Actions -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-outline-primary" id="addAnotherTypeBtn">
                + Add Another Visa Type
            </button>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4"
                    style="background-color: #1e7456; border-color: #1e7456;">
                    Update Country
                </button>
                <a href="{{ route('mobile-ops.visa-countries.index') }}" class="btn btn-light px-3">Cancel</a>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Track the current maximum index
            let existingCards = document.querySelectorAll(".visa-type-card");
            let typeIndex = existingCards.length > 0 ? existingCards.length - 1 : 0;

            const container = document.getElementById("visaTypesContainer");
            const addTypeBtn = document.getElementById("addAnotherTypeBtn");

            // Add Requirement Row
            document.addEventListener("click", function(e) {
                if (e.target.closest(".add-requirement-btn")) {
                    const btn = e.target.closest(".add-requirement-btn");
                    const currentTypeIndex = btn.getAttribute("data-type-index");
                    const reqList = btn.previousElementSibling;

                    const row = document.createElement("div");
                    row.className = "input-group mb-2 requirement-item";
                    row.innerHTML = `
                    <input type="text" name="types[${currentTypeIndex}][requirements][]" class="form-control form-control-sm" placeholder="e.g. Hotel Booking / Return Ticket">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-req-btn">
                        <i class="mdi mdi-close"></i>
                    </button>
                `;
                    reqList.appendChild(row);
                }

                // Remove Requirement Row
                if (e.target.closest(".remove-req-btn")) {
                    e.target.closest(".requirement-item").remove();
                }

                // Remove Visa Type Card
                if (e.target.closest(".remove-type-btn")) {
                    const cards = document.querySelectorAll(".visa-type-card");
                    if (cards.length > 1) {
                        e.target.closest(".visa-type-card").remove();
                    }
                }
            });

            // Add Another Visa Type Card
            addTypeBtn.addEventListener("click", function() {
                typeIndex++;
                const template = `
            <div class="card border-0 shadow-sm mb-4 visa-type-card" data-index="${typeIndex}" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fs-15 fw-bold text-dark m-0">Visa Type</h5>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-type-btn">
                            <i class="mdi mdi-close"></i> Remove
                        </button>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Visa Name <span class="text-danger">*</span></label>
                            <input type="text" name="types[${typeIndex}][visa_name]" class="form-control" placeholder="e.g. 60 Days Multiple Entry" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">B2B Rate (PKR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="types[${typeIndex}][b2b_rate]" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Visa Fee (USD) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="types[${typeIndex}][visa_fee]" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Process Time</label>
                            <input type="text" name="types[${typeIndex}][process_time]" class="form-control" placeholder="e.g. 3 to 5 Days">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" name="types[${typeIndex}][notes]" class="form-control" placeholder="Additional guidelines or terms...">
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <label class="form-label fw-semibold mb-2">Requirements</label>
                        <div class="requirements-list" data-type-index="${typeIndex}">
                            <div class="input-group mb-2 requirement-item">
                                <input type="text" name="types[${typeIndex}][requirements][]" class="form-control form-control-sm" value="Valid Passport">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-req-btn"><i class="mdi mdi-close"></i></button>
                            </div>
                            <div class="input-group mb-2 requirement-item">
                                <input type="text" name="types[${typeIndex}][requirements][]" class="form-control form-control-sm" value="Picture with White Background">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-req-btn"><i class="mdi mdi-close"></i></button>
                            </div>
                            <div class="input-group mb-2 requirement-item">
                                <input type="text" name="types[${typeIndex}][requirements][]" class="form-control form-control-sm" value="CNIC Front & Back">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-req-btn"><i class="mdi mdi-close"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-dark mt-1 add-requirement-btn" data-type-index="${typeIndex}">
                            + Add Requirement
                        </button>
                    </div>
                </div>
            </div>`;

                container.insertAdjacentHTML("beforeend", template);
            });

            // Flag Auto-detection & Dynamic Preview
            const countryCodeInput = document.getElementById("country_code");
            const flagPreview = document.getElementById("flagPreview");
            const flagPlaceholder = document.getElementById("flagPlaceholder");
            const flagStatusBadge = document.getElementById("flagStatusBadge");
            const flagFileInput = document.getElementById("country_flag_input");
            const initialCountryCode = @json($country->country_code);
            const initialFlagUrl = @json($country->country_flag_url);

            function updateFlagFromCode() {
                if (flagFileInput && flagFileInput.files && flagFileInput.files[0]) {
                    return; // Custom file uploaded, keep local file preview
                }

                const code = (countryCodeInput.value || "").trim().toLowerCase();

                // If code is same as initial and we have an initial flag url, restore that
                if (countryCodeInput.value.trim().toUpperCase() === initialCountryCode && initialFlagUrl) {
                    flagPreview.src = initialFlagUrl;
                    flagPreview.classList.remove("d-none");
                    if (flagPlaceholder) flagPlaceholder.classList.add("d-none");
                    if (flagStatusBadge) flagStatusBadge.classList.add("d-none");
                    return;
                }

                if (code.length === 2 && /^[a-z]{2}$/.test(code)) {
                    flagPreview.src = `https://flagcdn.com/w80/${code}.png`;
                    flagPreview.onload = function() {
                        flagPreview.classList.remove("d-none");
                        if (flagPlaceholder) flagPlaceholder.classList.add("d-none");
                        if (flagStatusBadge) flagStatusBadge.classList.remove("d-none");
                    };
                    flagPreview.onerror = function() {
                        flagPreview.classList.add("d-none");
                        if (flagPlaceholder) flagPlaceholder.classList.remove("d-none");
                        if (flagStatusBadge) flagStatusBadge.classList.add("d-none");
                    };
                } else {
                    if (initialFlagUrl && !code) {
                        flagPreview.src = initialFlagUrl;
                        flagPreview.classList.remove("d-none");
                        if (flagPlaceholder) flagPlaceholder.classList.add("d-none");
                    } else {
                        flagPreview.classList.add("d-none");
                        if (flagPlaceholder) flagPlaceholder.classList.remove("d-none");
                    }
                    if (flagStatusBadge) flagStatusBadge.classList.add("d-none");
                }
            }

            if (countryCodeInput) {
                countryCodeInput.addEventListener("input", function() {
                    this.value = this.value.toUpperCase();
                    updateFlagFromCode();
                });
            }

            if (flagFileInput) {
                flagFileInput.addEventListener("change", function(e) {
                    const file = e.target.files && e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(evt) {
                            flagPreview.src = evt.target.result;
                            flagPreview.classList.remove("d-none");
                            if (flagPlaceholder) flagPlaceholder.classList.add("d-none");
                            if (flagStatusBadge) flagStatusBadge.classList.add("d-none");
                        };
                        reader.readAsDataURL(file);
                    } else {
                        updateFlagFromCode();
                    }
                });
            }

            // Monument Picture Preview
            const monumentInput = document.getElementById("monument_picture_input");
            const monumentPreview = document.getElementById("monumentPreview");
            const monumentContainer = document.getElementById("monumentPreviewContainer");
            if (monumentInput) {
                monumentInput.addEventListener("change", function(e) {
                    const file = e.target.files && e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(evt) {
                            monumentPreview.src = evt.target.result;
                            monumentContainer.classList.remove("d-none");
                            monumentContainer.classList.add("d-flex");
                        };
                        reader.readAsDataURL(file);
                    } else {
                        monumentContainer.classList.add("d-none");
                        monumentContainer.classList.remove("d-flex");
                    }
                });
            }
        });
    </script>
@endsection
