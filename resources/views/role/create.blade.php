@extends('layout.master')

@section('title', 'Create Role')
@section('header-title', 'Create Role')

@section('content')

    <div class="content-page">
        <div class="content">
            <div class="container">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- HEADER -->
                            <div class="card-header">
                                <h5 class="mb-0">Create Role</h5>
                            </div>

                            <div class="card-body">

                                <form action="{{ route('role.store') }}" method="POST">
                                    @csrf

                                    <div class="row">

                                        <!-- ROLE NAME -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Role Name <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="name" class="form-control"
                                                    placeholder="Enter role name">
                                            </div>
                                        </div>

                                        <!-- SELECT ALL -->
                                        <div class="col-lg-6 d-flex align-items-end">
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="checkAll">
                                                <label class="form-check-label" for="checkAll">
                                                    Select All Permissions
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                                    <hr>

                                    <!-- PERMISSIONS -->
                                    <div class="row">

                                        @php
                                            $groupedPermissions = $permissions->groupBy('group_name');
                                        @endphp

                                        @foreach ($groupedPermissions as $module => $perms)
                                            @php
                                                $groupSlug = \Illuminate\Support\Str::slug($module ?: 'other');
                                            @endphp
                                            <div class="col-12 mt-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <h5 class="text-primary text-uppercase mb-0">
                                                    {{ ucfirst(str_replace('_', ' ', $module)) }}
                                                </h5>
                                                <div class="form-check mb-0">
                                                    <input type="checkbox"
                                                        class="form-check-input group-check-all"
                                                        id="group-check-{{ $groupSlug }}"
                                                        data-group="{{ $groupSlug }}">
                                                    <label class="form-check-label text-muted fs-12 fw-semibold" for="group-check-{{ $groupSlug }}">
                                                        Select Group
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-12 mb-2">
                                                <div class="row">

                                                    @foreach ($perms as $permission)
                                                        <div class="col-lg-4 col-md-6 mb-2">

                                                             <div class="form-check">
                                                                <input type="checkbox"
                                                                    class="form-check-input permission-checkbox group-{{ $groupSlug }}"
                                                                    name="permissions[]"
                                                                    value="{{ $permission->id }}"
                                                                    data-group="{{ $groupSlug }}">

                                                                <label class="form-check-label">
                                                                    {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                                                </label>
                                                            </div>

                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>

                                            <hr>
                                        @endforeach

                                    </div>

                                    <!-- SUBMIT -->
                                    <div class="row">
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-primary px-4">
                                                Submit
                                            </button>
                                        </div>
                                    </div>

                                </form>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- CHECK ALL SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAll');
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            const groupCheckboxes = document.querySelectorAll('.group-check-all');

            function updateAllState() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const someChecked = Array.from(checkboxes).some(cb => cb.checked);
                if (checkAll) {
                    checkAll.checked = allChecked;
                    checkAll.indeterminate = !allChecked && someChecked;
                }

                groupCheckboxes.forEach(grp => {
                    const slug = grp.getAttribute('data-group');
                    const grpItems = document.querySelectorAll('.group-' + slug);
                    const grpAllChecked = Array.from(grpItems).every(cb => cb.checked);
                    const grpSomeChecked = Array.from(grpItems).some(cb => cb.checked);
                    grp.checked = grpAllChecked;
                    grp.indeterminate = !grpAllChecked && grpSomeChecked;
                });
            }

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = checkAll.checked);
                    groupCheckboxes.forEach(grp => grp.checked = checkAll.checked);
                });
            }

            groupCheckboxes.forEach(grp => {
                grp.addEventListener('change', function() {
                    const slug = this.getAttribute('data-group');
                    const grpItems = document.querySelectorAll('.group-' + slug);
                    grpItems.forEach(cb => cb.checked = grp.checked);
                    updateAllState();
                });
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateAllState);
            });

            updateAllState();
        });
    </script>

@endsection
