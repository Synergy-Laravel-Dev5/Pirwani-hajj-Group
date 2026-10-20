@extends('layout.master')
@section('title', 'Suppliers / Vendors')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Page Title & Header Actions -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-truck-delivery-outline me-1"></i> Suppliers & Vendors Management
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Manage hotel inventory suppliers, bed/room allotment vendors, Saudi transport & Maktab partners.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('room-inventory.create') }}" class="btn btn-outline-info btn-sm">
                        <i class="mdi mdi-plus-box-multiple me-1"></i> + Stock Allotment
                    </a>
                    <a href="{{ route('supplier.trash') }}" class="btn btn-outline-danger btn-sm">
                        <i class="mdi mdi-delete-outline me-1"></i> Trash
                        @if($trashCount > 0)
                            <span class="badge bg-danger ms-1">{{ $trashCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('supplier.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus-circle me-1"></i> + Add New Supplier
                    </a>
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
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">Total Suppliers</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-dark">{{ number_format($totalCount) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-primary d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-truck-check text-primary fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">Active Suppliers</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-success">{{ number_format($activeCount) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-success d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-check-decagram text-success fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
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

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">In Trash</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-danger">{{ number_format($trashCount) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-danger d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-delete-restore text-danger fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('supplier.index') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Search Supplier</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="Name, code, phone, email, contact person..."
                                        value="{{ request('search') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Service Type</label>
                                <select name="service_type" class="form-select form-select-sm">
                                    <option value="all">-- All Service Types --</option>
                                    @foreach($serviceTypes as $st)
                                        <option value="{{ $st }}" {{ request('service_type') == $st ? 'selected' : '' }}>
                                            {{ $st }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">City</label>
                                <select name="city" class="form-select form-select-sm">
                                    <option value="all">-- All Cities --</option>
                                    @foreach($cities as $ct)
                                        <option value="{{ $ct }}" {{ request('city') == $ct ? 'selected' : '' }}>
                                            {{ $ct }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="all">All</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="mdi mdi-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Suppliers Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fs-15 fw-semibold text-primary mb-0">
                        <i class="mdi mdi-format-list-bulleted me-1"></i> Suppliers & Vendors List
                    </h5>
                    <span class="fs-12 text-muted">Showing {{ $suppliers->total() }} suppliers</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Code</th>
                                    <th>Supplier / Vendor Name</th>
                                    <th>Service Type</th>
                                    <th>City / Country</th>
                                    <th>Contact Details</th>
                                    <th>Tax / CR #</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end" style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $supplier)
                                    <tr>
                                        <td>
                                            <span class="badge bg-soft-info text-info fw-bold">
                                                {{ $supplier->supplier_code ?: 'SUP-' . str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('supplier.show', $supplier->id) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $supplier->name }}
                                            </a>
                                            @if($supplier->contact_person)
                                                <div class="fs-12 text-muted">
                                                    <i class="mdi mdi-account-tie me-1"></i>{{ $supplier->contact_person }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary">
                                                {{ $supplier->service_type ?: 'Hotels & Accommodation' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div><i class="mdi mdi-map-marker text-danger me-1"></i>{{ $supplier->city ?: 'Saudi Arabia' }}</div>
                                            <span class="fs-11 text-muted">{{ $supplier->country }}</span>
                                        </td>
                                        <td>
                                            @if($supplier->phone)
                                                <div><i class="mdi mdi-phone me-1 text-success"></i>{{ $supplier->phone }}</div>
                                            @endif
                                            @if($supplier->mobile)
                                                <div><i class="mdi mdi-whatsapp me-1 text-success"></i>{{ $supplier->mobile }}</div>
                                            @endif
                                            @if($supplier->email)
                                                <div class="fs-11 text-muted"><i class="mdi mdi-email-outline me-1"></i>{{ $supplier->email }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($supplier->tax_number)
                                                <div class="fs-11 text-muted">VAT: <code>{{ $supplier->tax_number }}</code></div>
                                            @endif
                                            @if($supplier->commercial_reg_no)
                                                <div class="fs-11 text-muted">CR: <code>{{ $supplier->commercial_reg_no }}</code></div>
                                            @endif
                                            @if(!$supplier->tax_number && !$supplier->commercial_reg_no)
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($supplier->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-warning">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('supplier.show', $supplier->id) }}" class="btn btn-outline-info" title="View Details">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-outline-primary" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form action="{{ route('supplier.delete', $supplier->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Move supplier \'{{ $supplier->name }}\' to trash?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Trash">
                                                        <i class="mdi mdi-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-truck-alert-outline fs-36 text-muted mb-2 d-block"></i>
                                            <p class="mb-1 fw-semibold">No suppliers found.</p>
                                            <a href="{{ route('supplier.create') }}" class="btn btn-primary btn-sm mt-2">
                                                <i class="mdi mdi-plus-circle me-1"></i> + Add First Supplier
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($suppliers->hasPages())
                    <div class="card-footer bg-white border-top py-2">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
