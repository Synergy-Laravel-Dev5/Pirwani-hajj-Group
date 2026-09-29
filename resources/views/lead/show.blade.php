@extends('layout.master')
@section('title', 'Lead Details')
@section('content')
    @php
        $statusColors = [
            'pending' => 'warning',
            'done' => 'success',
            'need further follow up' => 'info',
        ];
    @endphp
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                    <h4 class="fs-18 fw-semibold">Lead Details</h4>
                </div>
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="card mb-4">
                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-4">
                                <h6 class="text-muted">Contact Person</h6>
                                <p class="fw-semibold fs-15">{{ $lead->contact_person ?? '-' }}</p>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted">Phone</h6>
                                <p class="fw-semibold fs-15">{{ $lead->phone ?? '-' }}</p>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted">Email</h6>
                                <p class="fw-semibold fs-15">{{ $lead->email ?? '-' }}</p>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted">Company</h6>
                                <p class="fw-semibold fs-15">{{ $lead->company->company_name ?? '-' }}</p>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted">Package</h6>
                                <p class="fw-semibold fs-15">{{ $lead->package->package_number ?? '-' }}</p>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted">Number Of Person</h6>
                                <p class="fw-semibold fs-15">{{ $lead->number_of_pax ?? '-' }}</p>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted">Lead Source</h6>
                                <p class="fw-semibold fs-15">{{ $lead->source ? ucfirst($lead->source) : '-' }}</p>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted">Medium Of Contact</h6>
                                <p class="fw-semibold fs-15">
                                    {{ $lead->medium_of_contact ? ucfirst($lead->medium_of_contact) : '-' }}</p>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted">Assigned User</h6>
                                <p class="fw-semibold fs-15">{{ $lead->user->name ?? '-' }}</p>
                            </div>

                            <div class="col-12">
                                <h6 class="text-muted">Description</h6>
                                <div class="border rounded p-3">
                                    {!! nl2br(e($lead->description ?? '-')) !!}
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer text-end">
                        <a href="{{ route('lead.edit', $lead->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('lead.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>

                {{-- Follow-ups Card --}}
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0" style="color: #005f73">Follow-ups</h5>
                            <button data-bs-toggle="modal" data-bs-target="#followUpModal-{{ $lead->id }}"
                                class="btn btn-success btn-sm">Create Follow-up</button>
                        </div>

                        {{-- Create Follow-up Modal --}}
                        <div class="modal fade" id="followUpModal-{{ $lead->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('followUp.store', $lead->id) }}">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Create Follow-up for
                                                {{ $lead->contact_person ?? 'Lead' }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Subject</label>
                                                <input type="text" name="subject" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Follow-up Date</label>
                                                <input type="text" name="create_date"
                                                    class="form-control flatpickr-create-date" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Reason</label>
                                                <input type="text" name="reason" class="form-control"
                                                    placeholder="Enter reason">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Create Follow-up</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Follow-ups Accordion --}}
                        <div class="accordion" id="followUpAccordion">
                            @forelse ($lead->followUps as $followUp)
                                <div class="accordion-item mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $followUp->id }}">
                                            {{ $followUp->subject }}
                                            ({{ $followUp->create_date ? \Carbon\Carbon::parse($followUp->create_date)->format('d M Y, h:i A') : '-' }})
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $followUp->id }}" class="accordion-collapse collapse"
                                        data-bs-parent="#followUpAccordion">
                                        <div class="accordion-body">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Subject</th>
                                                    <td>{{ $followUp->subject }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Reason</th>
                                                    <td>{{ $followUp->reason ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Status</th>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $statusColors[$followUp->status] ?? 'secondary' }}">
                                                            {{ ucfirst($followUp->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Remarks</th>
                                                    <td>{{ $followUp->remarks ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Take Date</th>
                                                    <td>{{ $followUp->taken_at ? \Carbon\Carbon::parse($followUp->taken_at)->format('d M Y, h:i A') : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Due Date</th>
                                                    <td>{{ $followUp->due_date ? \Carbon\Carbon::parse($followUp->due_date)->format('d M Y, h:i A') : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Action</th>
                                                    <td>
                                                        @if ($followUp->status != 'done')
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#takeModal{{ $followUp->id }}">Take</button>
                                                        @else
                                                            <span class="badge bg-primary text-white">Taken</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- Take Follow-up Modal --}}
                                <div class="modal fade" id="takeModal{{ $followUp->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('followUp.take', $followUp->id) }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Take Follow-up</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Remarks</label>
                                                        <textarea name="remarks" class="form-control" rows="3"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Select Status</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="" selected disabled>Select Status</option>
                                                            @foreach (\App\Models\FollowUp::getStatuses() as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Take Date</label>
                                                        <input type="text" name="taken_at"
                                                            class="form-control flatpickr-taken-at"
                                                            value="{{ \Carbon\Carbon::now('Asia/Karachi')->format('d M Y, h:i A') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Due Date</label>
                                                        <input type="text" name="due_date"
                                                            class="form-control flatpickr-due-date">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted">No follow-ups available.</p>
                            @endforelse
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr(".flatpickr-create-date", {
            enableTime: true,
            dateFormat: "d M Y, h:i K",
            defaultDate: "{{ \Carbon\Carbon::now('Asia/Karachi')->format('d M Y, h:i K') }}",
            time_24hr: false
        });
        document.querySelectorAll('.flatpickr-taken-at').forEach(function(el) {
            flatpickr(el, {
                enableTime: true,
                dateFormat: "d M Y, h:i K",
                time_24hr: false
            });
        });
        document.querySelectorAll('.flatpickr-due-date').forEach(function(el) {
            flatpickr(el, {
                enableTime: true,
                dateFormat: "d M Y, h:i K",
                time_24hr: false
            });
        });
    </script>
@endsection
