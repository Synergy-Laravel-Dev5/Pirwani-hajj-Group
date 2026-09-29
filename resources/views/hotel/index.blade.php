@extends('layout.master')
@section('title', 'Hotels')
@section('content')

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between">
                    <h4 class="fs-18 fw-semibold m-0">Hotels</h4>
                    <div class="d-flex gap-2">
                        @can('hotel_create')
                            <a href="{{ route('hotel.create') }}" class="btn btn-primary btn-sm">
                                + Add Hotel
                            </a>
                        @endcan
                        @can('hotel_trash_view')
                            <a href="{{ route('hotel.trash') }}" class="btn btn-danger btn-sm">
                                🗑 Trash <span class="badge bg-white text-danger ms-1">{{ $trashCount }}</span>
                            </a>
                        @endcan
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Logo</th>
                                        <th>Hotel Number</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Place</th>
                                        <th>Acc. Type</th>
                                        <th>Acc. Category</th>
                                        <th>Contact</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($hotels as $hotel)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($hotel->logo)
                                                    <img src="{{ asset($hotel->logo) }}" alt="Logo" class="rounded"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $hotel->hotel_number ?? '—' }}</td>
                                            <td>{{ $hotel->code ?? '—' }}</td>
                                            <td><strong>{{ $hotel->name }}</strong><br><small
                                                    class="text-muted">{{ $hotel->address }}</small></td>
                                            <td>{{ $hotel->place ?? '—' }}</td>
                                            <td>{{ $hotel->accommodation_type ?? '—' }}</td>
                                            <td>{{ $hotel->accommodation_category ?? '—' }}</td>
                                            <td>{{ $hotel->contact ?? '—' }}</td>
                                            <td>{{ $hotel->email ?? '—' }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $hotel->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($hotel->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @can('hotel_edit')
                                                        <a href="{{ route('hotel.edit', $hotel->id) }}"
                                                            class="btn btn-sm btn-outline-success" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                    @endcan
                                                    @can('hotel_trash')
                                                        <form action="{{ route('hotel.delete', $hotel->id) }}" method="POST"
                                                            onsubmit="return confirm('Move to trash?')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
