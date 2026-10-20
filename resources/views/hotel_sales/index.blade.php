@extends('layout.master')
@section('title', 'Hotel Sale Invoices & Allotments')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Page Title & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-file-document-outline me-1"></i> Hotel Sale Invoices & Reservations
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        Manage B2B Agent & Client hotel reservation vouchers (Room Basis & Bed Basis) and track balances.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('room-inventory.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="mdi mdi-warehouse me-1"></i> Room & Bed Stock
                    </a>
                    <a href="{{ route('hotel-sales.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus-circle me-1"></i> + Create Hotel Sale Invoice
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
            @if($errors->any())
                <div class="alert alert-danger fs-13">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- KPI Summary Cards -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">Total Invoices</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-dark">{{ number_format($totalSalesCount) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-primary d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-receipt text-primary fs-20"></i>
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
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">Total Invoiced Amount</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-primary">SAR {{ number_format($totalSalesAmount, 2) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-info d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-cash-multiple text-info fs-20"></i>
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
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">Till Received</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-success">SAR {{ number_format($totalTillReceived, 2) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-success d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-check-circle-outline text-success fs-20"></i>
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
                                    <span class="fs-12 text-muted fw-semibold text-uppercase">Balance Remaining</span>
                                    <h3 class="fs-20 fw-bold mb-0 mt-1 text-danger">SAR {{ number_format($totalBalanceRemaining, 2) }}</h3>
                                </div>
                                <div class="avatar-sm rounded-circle bg-soft-danger d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-alert-circle-outline text-danger fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('hotel-sales.index') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Customer Type</label>
                                <select name="sale_for" class="form-select form-select-sm">
                                    <option value="">-- All (Agents & Clients) --</option>
                                    <option value="company" {{ request('sale_for') == 'company' ? 'selected' : '' }}>Agent / Company (B2B)</option>
                                    <option value="client" {{ request('sale_for') == 'client' ? 'selected' : '' }}>Direct Client (B2C)</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Agent / Company</label>
                                <select name="company_id" class="form-select form-select-sm">
                                    <option value="">-- All Agents --</option>
                                    @foreach($companies as $comp)
                                        <option value="{{ $comp->id }}" {{ request('company_id') == $comp->id ? 'selected' : '' }}>
                                            {{ $comp->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Search Invoice / Client / Title</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Search invoice number, client name, title..."
                                    value="{{ request('search') }}">
                            </div>

                            <div class="col-md-2 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="mdi mdi-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('hotel-sales.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fs-15 fw-semibold text-primary mb-0">
                        <i class="mdi mdi-format-list-bulleted me-1"></i> Hotel Reservation Invoices List
                    </h5>
                    <span class="fs-12 text-muted">Showing {{ $sales->total() }} confirmed reservations</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Customer / Agent</th>
                                    <th>Title</th>
                                    <th>Reserved Hotels & Allotment</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Till Received</th>
                                    <th class="text-end">Balance</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>
                                            <a href="{{ route('hotel-sales.show', $sale->id) }}" class="fw-bold text-primary">
                                                {{ $sale->invoice_number }}
                                            </a>
                                        </td>
                                        <td>{{ $sale->invoice_date ? $sale->invoice_date->format('d-M-Y') : '—' }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $sale->customer_name }}</div>
                                            <div class="fs-11 text-muted">
                                                @if($sale->sale_for === 'company')
                                                    <span class="badge bg-soft-primary text-primary">Agent / B2B</span>
                                                @else
                                                    <span class="badge bg-soft-info text-info">Client / B2C</span>
                                                @endif
                                                @if($sale->contact) • {{ $sale->contact }} @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark fs-12">{{ $sale->reservation_title }}</span>
                                        </td>
                                        <td>
                                            @foreach($sale->items as $item)
                                                <div class="fs-11 mb-1">
                                                    <i class="mdi mdi-hotel text-primary"></i>
                                                    <strong>{{ $item->hotel_name }}</strong>:
                                                    @if($item->allotment_basis === 'bed' || strtoupper($item->room_type) === 'BED')
                                                        <span class="badge bg-soft-warning text-warning border">BED: {{ $item->quantity }} Beds</span>
                                                    @else
                                                        <span class="badge bg-soft-success text-success border">{{ $item->room_type }}: {{ $item->quantity }} Rooms</span>
                                                    @endif
                                                    <span class="text-muted">({{ $item->meal_plan ?: 'RO' }}, {{ $item->no_of_nights }} Nts)</span>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="text-end fw-bold text-primary">
                                            {{ $sale->currency }} {{ number_format($sale->total_amount, 2) }}
                                        </td>
                                        <td class="text-end text-success fw-semibold">
                                            {{ number_format($sale->till_received, 2) }}
                                        </td>
                                        <td class="text-end fw-bold {{ $sale->balance_remaining > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($sale->balance_remaining, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success text-uppercase fs-11">
                                                {{ $sale->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('hotel-sales.show', $sale->id) }}" class="btn btn-primary btn-sm" title="View & Print Voucher">
                                                    <i class="mdi mdi-printer"></i>
                                                </a>
                                                <a href="{{ route('hotel-sales.edit', $sale->id) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form action="{{ route('hotel-sales.delete', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this reservation invoice?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-information-outline fs-24 d-block mb-1"></i>
                                            No hotel reservation invoices found.
                                            <div class="mt-2">
                                                <a href="{{ route('hotel-sales.create') }}" class="btn btn-primary btn-sm">
                                                    <i class="mdi mdi-plus-circle me-1"></i> Create First Hotel Sale Invoice
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sales->hasPages())
                    <div class="card-footer bg-white border-top py-2">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
