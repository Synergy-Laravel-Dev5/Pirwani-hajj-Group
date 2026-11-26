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
                    @can('room_inventory_view')
                    <a href="{{ route('room-inventory.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="mdi mdi-warehouse me-1"></i> Room & Bed Stock
                    </a>
                    @endcan
                    @can('hotel_sale_create')
                    <a href="{{ route('hotel-sales.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus-circle me-1"></i> + Create Hotel Sale Invoice
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show fs-13" role="alert">
                    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(isset($errors) && $errors->any())
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
                                    <option value="">-- All (Suppliers, Agents & Clients) --</option>
                                    <option value="supplier" {{ request('sale_for') == 'supplier' ? 'selected' : '' }}>Supplier / Vendor</option>
                                    <option value="company" {{ request('sale_for') == 'company' ? 'selected' : '' }}>Agent / Company (B2B)</option>
                                    <option value="client" {{ request('sale_for') == 'client' ? 'selected' : '' }}>Direct Client (B2C)</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-semibold mb-1 text-muted">Supplier / Agent</label>
                                <select name="supplier_id" class="form-select form-select-sm">
                                    <option value="">-- All Suppliers / Agents --</option>
                                    <optgroup label="Suppliers / Vendors">
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                                                {{ $sup->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    @if(count($companies) > 0)
                                        <optgroup label="Companies">
                                            @foreach($companies as $comp)
                                                <option value="{{ $comp->id }}" {{ request('company_id') == $comp->id ? 'selected' : '' }}>
                                                    {{ $comp->company_name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
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
            <style>
                .hotel-sales-table th,
                .hotel-sales-table td {
                    white-space: nowrap;
                    vertical-align: middle;
                    padding: 0.6rem 0.45rem;
                }
                .hotel-sales-table thead th {
                    font-size: 11px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.3px;
                }
            </style>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fs-15 fw-semibold text-primary mb-0">
                        <i class="mdi mdi-format-list-bulleted me-1"></i> Hotel Reservation Invoices List
                    </h5>
                    <span class="fs-12 text-muted">Showing {{ $sales->total() }} confirmed reservations</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 fs-13 hotel-sales-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Customer / Agent</th>
                                    <th>Title</th>
                                    <th>Hotels & Allotment</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Received</th>
                                    <th class="text-end">Balance</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    @php
                                        $itemsCount = $sale->items->count();
                                        $uniqueHotels = $sale->items->pluck('hotel_name')->filter()->unique()->values();
                                        $hotelCount = $uniqueHotels->count();
                                        $firstHotel = $uniqueHotels->first() ?? '—';
                                        $totalRooms = $sale->items->filter(fn($i) => $i->allotment_basis !== 'bed' && strtoupper((string)$i->room_type) !== 'BED')->sum('quantity');
                                        $totalBeds  = $sale->items->filter(fn($i) => $i->allotment_basis === 'bed' || strtoupper((string)$i->room_type) === 'BED')->sum('quantity');

                                        $summaryParts = [];
                                        if ($totalRooms > 0) {
                                            $summaryParts[] = $totalRooms . ' ' . \Illuminate\Support\Str::plural('Room', $totalRooms);
                                        }
                                        if ($totalBeds > 0) {
                                            $summaryParts[] = $totalBeds . ' ' . \Illuminate\Support\Str::plural('Bed', $totalBeds);
                                        }
                                        $summaryText = count($summaryParts) > 0
                                            ? implode(', ', $summaryParts)
                                            : ($itemsCount . ' ' . \Illuminate\Support\Str::plural('Item', $itemsCount));
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('hotel-sales.show', $sale->id) }}" class="fw-bold text-primary text-decoration-none">
                                                {{ $sale->invoice_number }}
                                            </a>
                                        </td>
                                        <td class="text-muted">
                                            {{ $sale->invoice_date ? $sale->invoice_date->format('d-M-Y') : '—' }}
                                        </td>
                                        <td>
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <span class="fw-bold text-dark d-inline-block text-truncate" style="max-width: 125px;"
                                                      title="{{ $sale->customer_name }}{{ $sale->contact ? ' • ' . $sale->contact : '' }}">
                                                    {{ $sale->customer_name }}
                                                </span>
                                                @if($sale->supplier || $sale->sale_for === 'supplier')
                                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle fs-11">Supplier</span>
                                                @elseif($sale->sale_for === 'company')
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle fs-11">B2B</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">Client</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark fs-12 d-inline-block text-truncate align-middle" style="max-width: 145px;"
                                                  title="{{ $sale->reservation_title }}">
                                                {{ $sale->reservation_title ?: '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($itemsCount > 0)
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <i class="mdi mdi-hotel text-primary"></i>
                                                    <span class="fw-semibold text-dark d-inline-block text-truncate" style="max-width: 115px;"
                                                          title="{{ $uniqueHotels->implode(', ') }}">
                                                        {{ $firstHotel }}
                                                    </span>
                                                    @if($hotelCount > 1)
                                                        <span class="badge bg-secondary-subtle text-secondary fs-11"
                                                              title="{{ $uniqueHotels->implode(', ') }}">+{{ $hotelCount - 1 }}</span>
                                                    @endif
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary py-0 px-2 fs-11 fw-semibold"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#saleItemsModal{{ $sale->id }}"
                                                            title="View all {{ $itemsCount }} allotment line(s)">
                                                        {{ $summaryText }} <i class="mdi mdi-eye-outline ms-1"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
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
                                            <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                                @can('hotel_sale_show')
                                                <a href="{{ route('hotel-sales.show', $sale->id) }}" class="btn btn-primary btn-sm px-2 py-1" title="View & Print Voucher">
                                                    <i class="mdi mdi-printer"></i>
                                                </a>
                                                <a href="{{ route('hotel-sales.download-pdf', $sale->id) }}" class="btn btn-danger btn-sm px-2 py-1" title="Download PDF">
                                                    <i class="mdi mdi-file-pdf-box"></i>
                                                </a>
                                                @endcan
                                                @can('hotel_sale_edit')
                                                <a href="{{ route('hotel-sales.edit', $sale->id) }}" class="btn btn-outline-secondary btn-sm px-2 py-1" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                @endcan
                                                @can('hotel_sale_trash')
                                                <form action="{{ route('hotel-sales.delete', $sale->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Delete this reservation invoice?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-1" title="Delete">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </form>
                                                @endcan
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

            <!-- Allotment Details Modals -->
            @foreach($sales as $sale)
                @if($sale->items->count() > 0)
                    <div class="modal fade" id="saleItemsModal{{ $sale->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-primary text-white py-2 px-3">
                                    <div>
                                        <h5 class="modal-title fs-15 fw-bold text-white mb-0">
                                            <i class="mdi mdi-hotel me-1"></i> Reserved Hotels & Allotment — {{ $sale->invoice_number }}
                                        </h5>
                                        <small class="text-white-50">
                                            {{ $sale->customer_name }}@if($sale->reservation_title) • {{ $sale->reservation_title }}@endif
                                        </small>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle mb-0 fs-12">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Hotel</th>
                                                    <th>Type & Qty</th>
                                                    <th>Meal Plan</th>
                                                    <th>Check In</th>
                                                    <th>Check Out</th>
                                                    <th class="text-center">Nights</th>
                                                    <th class="text-end">Rate</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sale->items as $idx => $item)
                                                    <tr>
                                                        <td class="text-muted">{{ $idx + 1 }}</td>
                                                        <td class="fw-semibold text-dark">{{ $item->hotel_name }}</td>
                                                        <td>
                                                            @if($item->allotment_basis === 'bed' || strtoupper((string)$item->room_type) === 'BED')
                                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle">BED: {{ $item->quantity }} Beds</span>
                                                            @elseif($item->allotment_basis === 'per_night')
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle">{{ $item->room_type }}: {{ $item->quantity }} (Per Night)</span>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $item->room_type }}: {{ $item->quantity }} Rooms</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->meal_plan ?: 'RO' }}</td>
                                                        <td>{{ $item->check_in ? \Carbon\Carbon::parse($item->check_in)->format('d-M-Y') : '—' }}</td>
                                                        <td>{{ $item->check_out ? \Carbon\Carbon::parse($item->check_out)->format('d-M-Y') : '—' }}</td>
                                                        <td class="text-center">{{ $item->no_of_nights }}</td>
                                                        <td class="text-end">{{ number_format($item->rate_per_unit, 2) }}</td>
                                                        <td class="text-end fw-bold text-primary">{{ number_format($item->amount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                                    <span class="fw-bold text-dark fs-13">
                                        Total: <span class="text-primary">{{ $sale->currency }} {{ number_format($sale->total_amount, 2) }}</span>
                                    </span>
                                    <div class="d-flex gap-2">
                                        @can('hotel_sale_show')
                                            <a href="{{ route('hotel-sales.show', $sale->id) }}" class="btn btn-primary btn-sm">
                                                <i class="mdi mdi-printer me-1"></i> View & Print Voucher
                                            </a>
                                        @endcan
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</div>
@endsection
