@extends('layout.master')

@section('title', 'Companies')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between">
                    <h4 class="fs-18 fw-semibold m-0">Companies</h4>
                    <div class="d-flex gap-2">
                        @can('company_create')
                            <a href="{{ route('company.create') }}" class="btn btn-primary btn-sm">
                                + Add Company
                            </a>
                        @endcan
                        @can('company_trash_view')
                            <a href="{{ route('company.trash') }}" class="btn btn-danger btn-sm">
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

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Company Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($companies as $company)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $company->name }}</strong></td>
                                            <td>
                                                @if ($company->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @can('company_edit')
                                                        <a href="{{ route('company.edit', $company->id) }}"
                                                            class="btn btn-sm btn-outline-success" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                    @endcan
                                                    @can('company_trash')
                                                        <form action="{{ route('company.delete', $company->id) }}"
                                                            method="POST" onsubmit="return confirm('Move to trash?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                No companies found.
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
@endsection
