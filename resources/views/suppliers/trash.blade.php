@extends('layout.master')
@section('title', 'Trashed Suppliers')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Title & Breadcrumb -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                <div>
                    <h4 class="fs-18 fw-semibold mb-1 text-danger">
                        <i class="mdi mdi-delete-outline me-1"></i> Trashed Suppliers
                    </h4>
                    <p class="text-muted fs-13 mb-0">
                        View deleted suppliers, restore them back to active list or permanently delete them.
                    </p>
                </div>
                <div>
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to Active Suppliers
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

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fs-15 fw-semibold text-danger mb-0">
                        <i class="mdi mdi-trash-can-outline me-1"></i> Trashed Suppliers List
                    </h5>
                    <span class="fs-12 text-muted">{{ $trashCount }} items in trash</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Supplier Name</th>
                                    <th>Service Type</th>
                                    <th>City</th>
                                    <th>Contact</th>
                                    <th>Deleted At</th>
                                    <th class="text-end" style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trashedSuppliers as $sup)
                                    <tr>
                                        <td><code>{{ $sup->supplier_code ?: 'SUP-' . $sup->id }}</code></td>
                                        <td class="fw-bold">{{ $sup->name }}</td>
                                        <td>{{ $sup->service_type }}</td>
                                        <td>{{ $sup->city }}</td>
                                        <td>{{ $sup->phone ?: $sup->mobile }}</td>
                                        <td class="text-muted">{{ $sup->deleted_at->format('d M Y, h:i A') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('supplier.restore', $sup->id) }}" class="btn btn-outline-success btn-sm">
                                                <i class="mdi mdi-restore me-1"></i> Restore
                                            </a>
                                            <form action="{{ route('supplier.force-delete', $sup->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Permanently delete supplier \'{{ $sup->name }}\'? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="mdi mdi-delete-forever"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-delete-empty fs-36 text-muted mb-2 d-block"></i>
                                            Trash is empty.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($trashedSuppliers->hasPages())
                    <div class="card-footer bg-white border-top py-2">
                        {{ $trashedSuppliers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
