@extends('layout.master')
@section('title', 'Hotel Clients')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Page Title & Header Actions -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-account-group-outline me-1"></i> Hotel Clients Management
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Manage corporate buyers, travel agents, and individual clients for Hotel Sale Invoices.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('hotel-sales.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="mdi mdi-file-document-outline me-1"></i> Hotel Invoices
                    </a>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createClientModal">
                        <i class="mdi mdi-plus-circle me-1"></i> + Add Hotel Client
                    </button>
                </div>
            </div>

            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show fs-13" role="alert">
                    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show fs-13" role="alert">
                    <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- KPI Summary Cards -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">Total Clients</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-dark">{{ number_format($totalCount) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-primary d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account-multiple text-primary fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">Active Clients</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-success">{{ number_format($activeCount) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-success d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-check-decagram text-success fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">Inactive</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-warning">{{ number_format($inactiveCount) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-warning d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-pause-circle text-warning fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('hotel-clients.index') }}" class="row g-2 align-items-center">
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search by Company Name, Client Name, Contact Number..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select name="status" class="form-select form-select-sm">
                                <option value="all">Status: All</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="mdi mdi-filter me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('hotel-clients.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                                    <i class="mdi mdi-close"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Clients Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0 fs-13 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>COMPANY NAME</th>
                                    <th>CONTACT PERSON (NAME)</th>
                                    <th>CONTACT / PHONE</th>
                                    <th class="text-center">INVOICES</th>
                                    <th class="text-center">STATUS</th>
                                    <th style="width: 120px;" class="text-center">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $index => $client)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">
                                            {{ $clients->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark fs-13">
                                                {{ $client->company_name ?: '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-primary">
                                                <i class="mdi mdi-account-circle me-1"></i>{{ $client->name }}
                                            </span>
                                            @if($client->notes)
                                                <div class="text-muted fs-11 mt-1">{{ Str::limit($client->notes, 40) }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->contact)
                                                <a href="tel:{{ $client->contact }}" class="text-decoration-none text-dark fw-semibold">
                                                    <i class="mdi mdi-phone me-1 text-success"></i>{{ $client->contact }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php $saleCount = $client->sales()->count(); @endphp
                                            @if($saleCount > 0)
                                                <span class="badge bg-soft-info text-info fs-12 px-2 py-1">
                                                    {{ $saleCount }} {{ Str::plural('Invoice', $saleCount) }}
                                                </span>
                                            @else
                                                <span class="text-muted fs-12">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($client->status === 'active')
                                                <span class="badge bg-soft-success text-success fs-11">Active</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger fs-11">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-soft-primary btn-sm px-2 edit-client-btn"
                                                    data-id="{{ $client->id }}"
                                                    data-company="{{ $client->company_name }}"
                                                    data-name="{{ $client->name }}"
                                                    data-contact="{{ $client->contact }}"
                                                    data-status="{{ $client->status }}"
                                                    data-notes="{{ $client->notes }}"
                                                    title="Edit Client">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>

                                                <form action="{{ route('hotel-clients.delete', $client->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this hotel client?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-soft-danger btn-sm px-2" title="Delete Client">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-account-off fs-24 d-block mb-1 text-secondary"></i>
                                            No hotel clients found. Click <strong>+ Add Hotel Client</strong> to create your first client.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($clients->hasPages())
                    <div class="card-footer bg-white border-0 py-2">
                        <div class="d-flex justify-content-end">
                            {{ $clients->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- Modal: Add New Hotel Client -->
<div class="modal fade" id="createClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('hotel-clients.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-2 px-3">
                    <h5 class="modal-title fs-15 fw-bold text-white">
                        <i class="mdi mdi-account-plus me-1"></i> Add New Hotel Client
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Company Name</label>
                        <input type="text" name="company_name" class="form-control form-control-sm" placeholder="e.g. AL MOAZZIN, AL-FAJR TRAVELS">
                        <small class="text-muted fs-11">Company / Agency name that appears on invoice header.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Client Name / Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" required placeholder="e.g. MUHAMMAD SALEEM">
                        <small class="text-muted fs-11">Person's name that appears under Attn.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Contact Number</label>
                        <input type="text" name="contact" class="form-control form-control-sm" placeholder="e.g. 0300-1234567 / +966 50 123 4567">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Notes / Remarks</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">
                        <i class="mdi mdi-check me-1"></i> Save Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Hotel Client -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editClientForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white py-2 px-3">
                    <h5 class="modal-title fs-15 fw-bold text-white">
                        <i class="mdi mdi-pencil me-1"></i> Edit Hotel Client
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Company Name</label>
                        <input type="text" name="company_name" id="edit_company_name" class="form-control form-control-sm">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Client Name / Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Contact Number</label>
                        <input type="text" name="contact" id="edit_contact" class="form-control form-control-sm">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Status</label>
                            <select name="status" id="edit_status" class="form-select form-select-sm">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-12 fw-bold text-uppercase text-dark mb-1">Notes / Remarks</label>
                        <textarea name="notes" id="edit_notes" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">
                        <i class="mdi mdi-check me-1"></i> Update Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
    const editForm = document.getElementById('editClientForm');

    document.querySelectorAll('.edit-client-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const company = this.getAttribute('data-company') || '';
            const name = this.getAttribute('data-name') || '';
            const contact = this.getAttribute('data-contact') || '';
            const status = this.getAttribute('data-status') || 'active';
            const notes = this.getAttribute('data-notes') || '';

            editForm.action = `{{ url('admin/hotel-clients/update') }}/${id}`;
            document.getElementById('edit_company_name').value = company;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_contact').value = contact;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_notes').value = notes;

            editModal.show();
        });
    });
});
</script>
@endsection
