@extends('layout.master')
@section('title', 'Supplier Profile - ' . $supplier->name)

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Title & Top Actions -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-primary">
                        <i class="mdi mdi-office-building me-1"></i> {{ $supplier->name }}
                        <span class="badge bg-soft-info text-info fs-12 ms-2">{{ $supplier->supplier_code }}</span>
                        @if($supplier->status === 'active')
                            <span class="badge bg-success fs-12 ms-1">Active</span>
                        @else
                            <span class="badge bg-warning fs-12 ms-1">Inactive</span>
                        @endif
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        {{ $supplier->service_type }} &bull; {{ $supplier->city }}, {{ $supplier->country }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('room-inventory.create') }}?supplier_id={{ $supplier->id }}" class="btn btn-outline-success btn-sm">
                        <i class="mdi mdi-plus-box me-1"></i> + Add Allotment
                    </a>
                    <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-pencil me-1"></i> Edit Supplier
                    </a>
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <!-- KPI Row -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <span class="fs-12 text-muted fw-semibold text-uppercase">Stock Allotment Batches</span>
                            <h3 class="fs-20 fw-bold mb-0 mt-1 text-primary">{{ number_format($totalAllotmentLines) }} Lines</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <span class="fs-12 text-muted fw-semibold text-uppercase">Total Purchased Rooms</span>
                            <h3 class="fs-20 fw-bold mb-0 mt-1 text-info">{{ number_format($totalStockRooms) }} Rooms</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <span class="fs-12 text-muted fw-semibold text-uppercase">Total Purchased Beds</span>
                            <h3 class="fs-20 fw-bold mb-0 mt-1 text-success">{{ number_format($totalStockBeds) }} Beds</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <span class="fs-12 text-muted fw-semibold text-uppercase">Hotel Invoices / Sales</span>
                            <h3 class="fs-20 fw-bold mb-0 mt-1 text-dark">{{ number_format($totalSalesCount) }} (SAR {{ number_format($totalSalesAmount, 2) }})</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <!-- Left Column: Details -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="fs-14 fw-semibold text-primary mb-0">
                                <i class="mdi mdi-card-account-details-outline me-1"></i> Contact & Address
                            </h5>
                        </div>
                        <div class="card-body p-3 fs-13">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted fw-semibold" style="width: 120px;">Contact Person:</td>
                                    <td class="fw-bold">{{ $supplier->contact_person ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Office Phone:</td>
                                    <td>{{ $supplier->phone ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Mobile / WA:</td>
                                    <td>{{ $supplier->mobile ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Email:</td>
                                    <td>{{ $supplier->email ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">City:</td>
                                    <td>{{ $supplier->city ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Country:</td>
                                    <td>{{ $supplier->country ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Address:</td>
                                    <td>{{ $supplier->address ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="fs-14 fw-semibold text-primary mb-0">
                                <i class="mdi mdi-bank-outline me-1"></i> Banking & Tax Information
                            </h5>
                        </div>
                        <div class="card-body p-3 fs-13">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted fw-semibold" style="width: 120px;">VAT / Tax #:</td>
                                    <td><code>{{ $supplier->tax_number ?: '-' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">CR Number:</td>
                                    <td><code>{{ $supplier->commercial_reg_no ?: '-' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Currency:</td>
                                    <td>{{ $supplier->currency }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Opening Bal:</td>
                                    <td class="fw-bold">{{ $supplier->currency }} {{ number_format($supplier->opening_balance, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Bank Name:</td>
                                    <td>{{ $supplier->bank_name ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Account Title:</td>
                                    <td>{{ $supplier->bank_account_title ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Account #:</td>
                                    <td>{{ $supplier->bank_account_number ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">IBAN:</td>
                                    <td><code>{{ $supplier->bank_iban ?: '-' }}</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($supplier->notes)
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white py-2 border-bottom">
                                <h6 class="fs-13 fw-semibold text-primary mb-0">Contract & Supplier Notes</h6>
                            </div>
                            <div class="card-body p-3 fs-13 text-muted">
                                {!! nl2br(e($supplier->notes)) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Activity / Allotments -->
                <div class="col-md-8">
                    <!-- Hotel Stock Allotments Purchased from this Supplier -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fs-14 fw-semibold text-primary mb-0">
                                <i class="mdi mdi-warehouse me-1"></i> Purchased Hotel Stock Allotments
                            </h5>
                            <a href="{{ route('room-inventory.create') }}?supplier_id={{ $supplier->id }}" class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-plus"></i> Add Allotment
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0 fs-13">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Hotel</th>
                                            <th>Room Type / Basis</th>
                                            <th>Check-In (Hijri)</th>
                                            <th>Check-Out (Hijri)</th>
                                            <th class="text-center">Rooms</th>
                                            <th class="text-center">Beds</th>
                                            <th class="text-end">Cost Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($supplier->inventories as $inv)
                                            <tr>
                                                <td class="fw-bold">{{ $inv->hotel->name ?? 'Hotel #' . $inv->hotel_id }}</td>
                                                <td>
                                                    <span class="badge bg-soft-info text-info">{{ $inv->room_type }}</span>
                                                    @if($inv->allotment_basis === 'bed')
                                                        <span class="badge bg-soft-warning text-warning">Bed Basis</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $inv->check_in ? $inv->check_in->format('d M Y') : '-' }}
                                                    @if($inv->hijri_check_in)
                                                        <div class="fs-11 text-muted">{{ $inv->hijri_check_in }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $inv->check_out ? $inv->check_out->format('d M Y') : '-' }}
                                                    @if($inv->hijri_check_out)
                                                        <div class="fs-11 text-muted">{{ $inv->hijri_check_out }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $inv->total_rooms }}</td>
                                                <td class="text-center fw-bold">{{ $inv->total_beds }}</td>
                                                <td class="text-end">SAR {{ number_format($inv->cost_rate, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    No stock inventory recorded for this supplier yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Hotel Sales / Invoices -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fs-14 fw-semibold text-primary mb-0">
                                <i class="mdi mdi-receipt me-1"></i> Hotel Sale Invoices / Reservations
                            </h5>
                            <a href="{{ route('hotel-sales.create') }}" class="btn btn-outline-info btn-sm">
                                <i class="mdi mdi-plus"></i> Create Invoice
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0 fs-13">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Title</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-end">Received</th>
                                            <th class="text-end">Balance</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($supplier->hotelSales as $sale)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('hotel-sales.show', $sale->id) }}" class="fw-bold text-primary">
                                                        {{ $sale->invoice_number }}
                                                    </a>
                                                </td>
                                                <td>{{ $sale->invoice_date ? $sale->invoice_date->format('d M Y') : '-' }}</td>
                                                <td>{{ Str::limit($sale->reservation_title, 25) }}</td>
                                                <td class="text-end fw-bold">SAR {{ number_format($sale->total_amount, 2) }}</td>
                                                <td class="text-end text-success">SAR {{ number_format($sale->till_received, 2) }}</td>
                                                <td class="text-end text-danger">SAR {{ number_format($sale->balance_remaining, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-soft-success text-success">{{ ucfirst($sale->status) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    No reservation invoices for this supplier yet.
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

        </div>
    </div>
</div>
@endsection
