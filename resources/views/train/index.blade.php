@extends('layout.master')
@section('title', 'Trains')
@section('content')

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between">
                    <h4 class="fs-18 fw-semibold m-0">Trains</h4>
                    <div class="d-flex gap-2">
                        @can('train_create')
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#createTrainModal">
                                + Add Train
                            </button>
                        @endcan
                        @can('train_trash_view')
                            <a href="{{ route('train.trash') }}" class="btn btn-danger btn-sm">
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

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
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
                                        <th>Train Name</th>
                                        <th>Train Code</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($trains as $train)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $train->train_name }}</strong></td>
                                            <td>{{ $train->train_code ?? '—' }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $train->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($train->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @can('train_edit')
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-success edit-train-btn"
                                                            data-id="{{ $train->id }}" data-name="{{ $train->train_name }}"
                                                            data-code="{{ $train->train_code }}"
                                                            data-status="{{ $train->status }}" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                    @endcan
                                                    @can('train_trash')
                                                        <form action="{{ route('train.delete', $train->id) }}" method="POST"
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

    <div class="modal fade" id="createTrainModal" tabindex="-1" aria-labelledby="createTrainModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTrainModalLabel">Add New Train</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('train.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Train Name <span class="text-danger">*</span></label>
                            <input type="text" name="train_name" class="form-control" placeholder="Enter train name"
                                required value="{{ old('train_name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Train Code</label>
                            <input type="text" name="train_code" class="form-control" placeholder="Enter train code"
                                value="{{ old('train_code') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Train</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTrainModal" tabindex="-1" aria-labelledby="editTrainModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTrainModalLabel">Edit Train</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editTrainForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Train Name <span class="text-danger">*</span></label>
                            <input type="text" name="train_name" id="edit_train_name" class="form-control"
                                placeholder="Enter train name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Train Code</label>
                            <input type="text" name="train_code" id="edit_train_code" class="form-control"
                                placeholder="Enter train code">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Train</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const editButtons = document.querySelectorAll('.edit-train-btn');
            const editModal = new bootstrap.Modal(document.getElementById('editTrainModal'));
            const editForm = document.getElementById('editTrainForm');
            const editNameInput = document.getElementById('edit_train_name');
            const editCodeInput = document.getElementById('edit_train_code');
            const editStatusSelect = document.getElementById('edit_status');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const code = this.getAttribute('data-code');
                    const status = this.getAttribute('data-status');

                    editForm.action = `/admin/train/update/${id}`;

                    editNameInput.value = name;
                    editCodeInput.value = code;
                    editStatusSelect.value = status;

                    editModal.show();
                });
            });
        });
    </script>

@endsection
