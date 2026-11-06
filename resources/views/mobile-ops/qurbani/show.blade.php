@extends('layout.mobile-master')

@section('content')
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-bold text-dark m-0">{{ $batch->batch_name }}</h4>
            <p class="text-muted fs-13 mb-0">Batch details, announcement text, and assigned Hajjis.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('mobile-ops.qurbani.edit', $batch->id) }}"
                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="mdi mdi-pencil me-1"></i> Edit Batch
            </a>
            <a href="{{ route('mobile-ops.qurbani.index') }}" class="btn btn-sm btn-secondary rounded-pill px-3">
                <i class="mdi mdi-arrow-left me-1"></i> All Batches
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        <!-- Batch Info & Mobile Announcement Preview -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title m-0 fs-15 text-primary fw-bold">
                        <i class="mdi mdi-information-outline me-1"></i> Batch Schedule
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush fs-13">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Qurbani Date:</span>
                            <span
                                class="fw-bold">{{ \Carbon\Carbon::parse($batch->qurbani_date)->format('l, d M, Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Start Time:</span>
                            <span class="fw-bold text-success">{{ substr((string) $batch->start_time, 0, 5) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">End Time:</span>
                            <span class="fw-bold text-danger">{{ substr((string) $batch->end_time, 0, 5) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Total Assigned:</span>
                            <span class="badge bg-primary rounded-pill px-2 py-1">{{ $batch->hajjis->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Completed:</span>
                            <span class="badge bg-success rounded-pill px-2 py-1">
                                {{ $batch->hajjis->where('status', 'completed')->count() }} Done
                            </span>
                        </li>
                    </ul>

                    @if ($batch->notes)
                        <div class="mt-3 p-2 bg-light rounded text-muted fs-12">
                            <strong>Note:</strong> {{ $batch->notes }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mobile Announcement Card Preview -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #C9A84C !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark"><i class="mdi mdi-cellphone"></i> Mobile Preview</span>
                        <small class="text-muted">What Hajjis see on the app</small>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $batch->title ?: "Qurbani Announcement: {$batch->batch_name}" }}</h6>
                    <p class="text-muted fs-13 mb-2">{{ $batch->message }}</p>
                    <div class="badge bg-light text-dark border p-2 w-100 text-start">
                        <i class="mdi mdi-clock-check-outline text-success me-1"></i>
                        Window: {{ substr((string) $batch->start_time, 0, 5) }} -
                        {{ substr((string) $batch->end_time, 0, 5) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Hajjis DataTables Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div
                    class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="card-title m-0 fs-15 text-primary fw-bold">
                            <i class="mdi mdi-account-group me-1"></i> Assigned Hajjis in this Batch
                            ({{ $batch->hajjis->count() }})
                        </h5>
                    </div>
                    <div>
                        <button type="button"
                            class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#assignHajjisModal">
                            <i class="mdi mdi-account-plus fs-15"></i> Add Hajjis from Bookings
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="assigned-hajjis-datatable"
                            class="table table-bordered dt-responsive nowrap align-middle mb-0 w-100">
                            <thead class="table-light fs-12 text-uppercase text-muted">
                                <tr>
                                    <th class="ps-3">Hajji Name</th>
                                    <th>HB Number</th>
                                    <th>Hajj ID</th>
                                    <th>Passport</th>
                                    <th>Booking Ref</th>
                                    <th>Qurbani Status</th>
                                    <th class="text-end pe-3">Remove</th>
                                </tr>
                            </thead>
                            <tbody class="fs-13">
                                @forelse ($batch->hajjis as $assigned)
                                    @php
                                        $person = $assigned->person;
                                        $bk = $assigned->booking;
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark">
                                                {{ $person?->given_name ? $person->given_name . ' ' . $person->surname : $person?->full_name ?? 'Pilgrim' }}
                                            </div>
                                            @if ($person?->gender)
                                                <small class="text-muted">{{ ucfirst($person->gender) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace">
                                                {{ $person?->hb_number ?: $bk?->hb_number ?? 'HB' . str_pad($bk?->id, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-info-subtle text-info border border-info-subtle fw-semibold">
                                                {{ $person?->hajj_id ?: '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $person?->passport_number ?: '—' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">#{{ $bk?->id }}</span>
                                            @if ($bk?->client?->name)
                                                <div class="fs-11 text-muted text-truncate" style="max-width: 120px;">
                                                    {{ $bk->client->name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($assigned->status === 'completed')
                                                <span class="badge bg-success text-white px-2 py-1">
                                                    <i class="mdi mdi-check-circle me-1"></i> Completed by Hajji
                                                </span>
                                                @if ($assigned->completed_at)
                                                    <div class="fs-11 text-muted mt-1">
                                                        {{ $assigned->completed_at->format('d M, h:i A') }}</div>
                                                @endif
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-1">
                                                    <i class="mdi mdi-clock-outline me-1"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <form
                                                action="{{ route('mobile-ops.qurbani.remove-hajji', [$batch->id, $assigned->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Remove this Hajji from this batch?');"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Remove Hajji">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-account-alert-outline fs-24 d-block mb-1 text-muted"></i>
                                            No Hajjis added to this batch yet.
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

    <!-- Enhanced Modal to Assign Hajjis from Bookings -->
    <div class="modal fade" id="assignHajjisModal" tabindex="-1" aria-labelledby="assignHajjisModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius: 14px; max-height: 85vh; display: flex; flex-direction: column;">
                <form action="{{ route('mobile-ops.qurbani.assign', $batch->id) }}" method="POST"
                    id="assignHajjisForm"
                    style="display: flex; flex-direction: column; height: 100%; overflow: hidden; margin: 0;">
                    @csrf

                    <!-- Modal Header -->
                    <div class="modal-header bg-light border-bottom px-4 py-2 flex-shrink-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 34px; height: 34px;">
                                <i class="mdi mdi-account-multiple-plus fs-18"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fs-15 fw-bold text-dark m-0" id="assignHajjisModalLabel">
                                    Assign Hajjis to {{ $batch->batch_name }}
                                </h5>
                                <span class="text-muted fs-11">Click anywhere on a row or checkbox to select</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-3" style="overflow-y: auto; flex: 1 1 auto; min-height: 0;">
                        <!-- Top Toolbar Controls -->
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                    <input type="text" id="hajjiSearchFilter" class="form-control border-start-0"
                                        placeholder="Search by Name, HB #, Passport, or Hajj ID...">
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end d-flex gap-2 justify-content-md-end align-items-center">
                                <span class="badge bg-primary rounded-pill px-2 py-1 fs-11" id="selectedCounterBadge">
                                    0 Hajjis Selected
                                </span>
                                <button type="button" class="btn btn-xs btn-outline-primary" id="selectAllBtn">
                                    <i class="mdi mdi-checkbox-marked-outline me-1"></i> Select All
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary" id="deselectAllBtn">
                                    <i class="mdi mdi-checkbox-blank-outline me-1"></i> Deselect All
                                </button>
                            </div>
                        </div>

                        <!-- Hajjis Table with Large Clickable Target -->
                        <div class="table-responsive border rounded" style="max-height: 340px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 selectable-table" id="hajjiAssignTable">
                                <thead class="table-light position-sticky top-0 shadow-sm fs-12 text-uppercase text-muted"
                                    style="z-index: 10;">
                                    <tr>
                                        <th class="ps-3 py-2" style="width: 50px; text-align: center;">
                                            <input type="checkbox" id="masterCheckbox"
                                                class="form-check-input custom-big-checkbox" style="cursor: pointer;">
                                        </th>
                                        <th class="py-2">Hajji Full Name</th>
                                        <th class="py-2">HB Number</th>
                                        <th class="py-2">Hajj ID</th>
                                        <th class="py-2">Passport No</th>
                                        <th class="py-2">Booking ID & Head</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-12">
                                    @forelse($availablePersons as $p)
                                        <tr class="hajji-row" style="cursor: pointer;"
                                            data-person-id="{{ $p->id }}">
                                            <td class="ps-3 py-2 text-center checkbox-cell" style="cursor: pointer;">
                                                <input type="checkbox" name="person_ids[]" value="{{ $p->id }}"
                                                    class="form-check-input hajji-checkbox custom-big-checkbox"
                                                    id="chk_person_{{ $p->id }}">
                                            </td>
                                            <td class="py-2">
                                                <span class="m-0 fw-bold text-dark d-block">
                                                    {{ $p->given_name ? $p->given_name . ' ' . $p->surname : $p->full_name ?? 'Pilgrim #' . $p->id }}
                                                </span>
                                                @if ($p->gender)
                                                    <small class="text-muted text-capitalize">{{ $p->gender }}</small>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                                    {{ $p->hb_number ?: $p->booking?->hb_number ?? 'HB' . str_pad($p->booking_id, 4, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </td>
                                            <td class="py-2">
                                                <span
                                                    class="badge bg-info-subtle text-info border border-info-subtle fw-semibold px-2 py-1">
                                                    {{ $p->hajj_id ?: '—' }}
                                                </span>
                                            </td>
                                            <td class="py-2">
                                                <span class="text-muted">{{ $p->passport_number ?: '—' }}</span>
                                            </td>
                                            <td class="py-2">
                                                <span class="fw-semibold text-dark">#{{ $p->booking_id }}</span>
                                                <div class="fs-11 text-muted text-truncate" style="max-width: 140px;">
                                                    {{ $p->booking?->client?->name ?? 'Head of Family' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="mdi mdi-check-all fs-24 text-success d-block mb-1"></i>
                                                All registered Hajjis in bookings have already been assigned to this batch.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer bg-light px-4 py-2 border-top flex-shrink-0">
                        <button type="button" class="btn btn-sm btn-secondary px-3"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" id="submitAssignBtn">
                            <i class="mdi mdi-plus-circle me-1"></i> Assign Selected Hajjis (<span
                                id="submitCount">0</span>)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .custom-big-checkbox {
            width: 22px !important;
            height: 22px !important;
            margin: 0 !important;
            cursor: pointer !important;
            border: 2px solid #6c757d !important;
            border-radius: 4px !important;
            display: inline-block !important;
            pointer-events: auto !important;
        }

        .custom-big-checkbox:checked {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .selectable-table tbody tr {
            transition: background-color 0.15s ease-in-out;
            user-select: none;
        }

        .selectable-table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.08) !important;
        }

        .selectable-table tbody tr.table-active-row {
            background-color: rgba(13, 110, 253, 0.15) !important;
        }

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }
    </style>

    <!-- Modal and Table Interaction Logic (Vanilla JS with jQuery Fallback) -->
    <script>
        (function() {
            function initQurbaniModal() {
                const modal = document.getElementById('assignHajjisModal');
                const table = document.getElementById('hajjiAssignTable');
                const masterCheckbox = document.getElementById('masterCheckbox');
                const counterBadge = document.getElementById('selectedCounterBadge');
                const submitCount = document.getElementById('submitCount');
                const searchInput = document.getElementById('hajjiSearchFilter');
                const selectAllBtn = document.getElementById('selectAllBtn');
                const deselectAllBtn = document.getElementById('deselectAllBtn');
                const form = document.getElementById('assignHajjisForm');

                if (!table) return;

                function getCheckboxes() {
                    return Array.from(table.querySelectorAll('.hajji-checkbox'));
                }

                function getVisibleRows() {
                    return Array.from(table.querySelectorAll('tbody tr.hajji-row')).filter(tr => tr.style.display !== 'none');
                }

                function updateCount() {
                    const allCheckboxes = getCheckboxes();
                    const checkedCheckboxes = allCheckboxes.filter(cb => cb.checked);
                    const count = checkedCheckboxes.length;

                    if (counterBadge) counterBadge.textContent = count + ' Hajjis Selected';
                    if (submitCount) submitCount.textContent = count;

                    // Update row highlighting
                    allCheckboxes.forEach(cb => {
                        const tr = cb.closest('tr');
                        if (tr) {
                            if (cb.checked) {
                                tr.classList.add('table-active-row');
                            } else {
                                tr.classList.remove('table-active-row');
                            }
                        }
                    });

                    // Update master checkbox
                    if (masterCheckbox) {
                        const visibleRows = getVisibleRows();
                        if (visibleRows.length > 0) {
                            const allVisibleChecked = visibleRows.every(tr => {
                                const cb = tr.querySelector('.hajji-checkbox');
                                return cb && cb.checked;
                            });
                            masterCheckbox.checked = allVisibleChecked;
                        } else {
                            masterCheckbox.checked = false;
                        }
                    }
                }

                // Row click handler (clicks anywhere on the row toggle the checkbox)
                table.addEventListener('click', function(e) {
                    const row = e.target.closest('tr.hajji-row');
                    if (!row) return;

                    const checkbox = row.querySelector('.hajji-checkbox');
                    if (!checkbox) return;

                    // If user clicked directly on the checkbox, let native event happen, then update
                    if (e.target === checkbox) {
                        setTimeout(updateCount, 10);
                        return;
                    }

                    // Otherwise toggle checkbox state
                    checkbox.checked = !checkbox.checked;
                    updateCount();
                });

                // Checkbox direct change
                table.addEventListener('change', function(e) {
                    if (e.target.classList.contains('hajji-checkbox')) {
                        updateCount();
                    }
                });

                // Master checkbox
                if (masterCheckbox) {
                    masterCheckbox.addEventListener('change', function() {
                        const isChecked = this.checked;
                        getVisibleRows().forEach(tr => {
                            const cb = tr.querySelector('.hajji-checkbox');
                            if (cb) cb.checked = isChecked;
                        });
                        updateCount();
                    });
                }

                // Select All Button
                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        getVisibleRows().forEach(tr => {
                            const cb = tr.querySelector('.hajji-checkbox');
                            if (cb) cb.checked = true;
                        });
                        updateCount();
                    });
                }

                // Deselect All Button
                if (deselectAllBtn) {
                    deselectAllBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        getCheckboxes().forEach(cb => {
                            cb.checked = false;
                        });
                        updateCount();
                    });
                }

                // Live Search Filter
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase().trim();
                        const allRows = table.querySelectorAll('tbody tr.hajji-row');

                        allRows.forEach(tr => {
                            const text = tr.innerText.toLowerCase();
                            if (text.indexOf(query) !== -1) {
                                tr.style.display = '';
                            } else {
                                tr.style.display = 'none';
                            }
                        });
                        updateCount();
                    });
                }

                // Validation on submit
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const checkedCount = getCheckboxes().filter(cb => cb.checked).length;
                        if (checkedCount === 0) {
                            e.preventDefault();
                            alert('Please select at least one Hajji to assign to this batch.');
                            return false;
                        }
                    });
                }

                // Initial count calculation
                updateCount();
            }

            // Bind after DOM loaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initQurbaniModal);
            } else {
                initQurbaniModal();
            }
        })();
    </script>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($('#assigned-hajjis-datatable').length) {
                $('#assigned-hajjis-datatable').DataTable({
                    paging: true,
                    info: true,
                    searching: true,
                    lengthChange: true,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    pageLength: 10,
                    responsive: true,
                    order: [],
                    columnDefs: [{
                        orderable: false,
                        targets: [6]
                    }],
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                        search: "Search hajjis:",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            previous: "<i class='mdi mdi-chevron-left'>",
                            next: "<i class='mdi mdi-chevron-right'>"
                        }
                    }
                });
            }
        });
    </script>
@endpush
