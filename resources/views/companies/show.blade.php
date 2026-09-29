@extends('layout.master')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .cs-page {
            background: #f4f6f9;
        }

        .cs-wrap {
            max-width: 100%;
            margin: 0 auto;
        }

        .cs-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
            margin-bottom: 22px;
            overflow: hidden;
        }

        .cs-card-head {
            padding: 14px 20px;
            border-bottom: 1px solid #f1f3f6;
            font-weight: 600;
            font-size: 14.5px;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #fafbfc;
        }

        .cs-card-head .cs-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cs-card-head .cs-ico {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #eef2ff;
            color: #4f46e5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .cs-card-body {
            padding: 20px;
        }

        /* Header card */
        .cs-profile-body {
            padding: 28px;
            text-align: center;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #fff;
        }

        .cs-logo-box {
            width: 110px;
            height: 110px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .08);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 0 auto 14px;
            border: 1px dashed rgba(255, 255, 255, .25);
        }

        .cs-logo-box img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .cs-profile-name {
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 4px;
        }

        .cs-profile-meta {
            color: #cbd5e1;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .cs-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .cs-badge.active {
            background: #dcfce7;
            color: #15803d;
        }

        .cs-badge.inactive {
            background: #f1f5f9;
            color: #64748b;
        }

        .cs-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 18px;
        }

        .cs-btn {
            border-radius: 8px;
            font-weight: 500;
            font-size: 13px;
            padding: 8px 18px;
            border: 1px solid rgba(255, 255, 255, .25);
            color: #fff;
            background: rgba(255, 255, 255, .08);
            transition: .2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .cs-btn:hover {
            background: rgba(255, 255, 255, .18);
            color: #fff;
        }

        .cs-btn.warning {
            background: #f59e0b;
            border-color: #f59e0b;
        }

        .cs-btn.warning:hover {
            background: #d97706;
        }

        /* Rows of label/value */
        .cs-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 11px 0;
            border-bottom: 1px solid #f1f3f6;
        }

        .cs-row:last-child {
            border-bottom: none;
        }

        .cs-row label {
            font-size: 12.5px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
            flex: 0 0 auto;
        }

        .cs-row .val {
            font-size: 14px;
            color: #1e293b;
            font-weight: 500;
            text-align: right;
            word-break: break-word;
        }

        .cs-row .val.empty {
            color: #cbd5e1;
            font-weight: 400;
        }

        .cs-row a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }

        .cs-row a:hover {
            text-decoration: underline;
        }

        .cs-row.block {
            flex-direction: column;
            align-items: flex-start;
        }

        .cs-row.block .val {
            text-align: left;
            width: 100%;
        }

        .cs-director-photo {
            text-align: center;
            margin-bottom: 16px;
        }

        .cs-avatar {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #eef2ff;
        }

        .cs-avatar-empty {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            background: #f1f5f9;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 22px;
            border: 3px solid #eef2ff;
        }

        .cs-thumb-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .cs-thumb-box {
            text-align: center;
        }

        .cs-thumb-box label {
            display: block;
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .4px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .cs-thumb {
            max-height: 90px;
            max-width: 150px;
            border-radius: 8px;
            border: 1px solid #eef1f5;
            padding: 6px;
            background: #fafbfc;
        }

        .cs-thumb-empty {
            height: 90px;
            width: 150px;
            border-radius: 8px;
            border: 1px dashed #dbe1ea;
            background: #fafbfc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            font-size: 12px;
        }

        .cs-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .cs-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 700;
            padding: 12px 16px;
            border-bottom: 2px solid #eef1f5;
            white-space: nowrap;
        }

        .cs-table tbody td {
            padding: 13px 16px;
            font-size: 13.5px;
            color: #334155;
            border-bottom: 1px solid #f1f3f6;
        }

        .cs-table tbody tr:last-child td {
            border-bottom: none;
        }

        .cs-table tbody tr:hover td {
            background: #fafbff;
        }

        .cs-pill {
            display: inline-block;
            padding: 3px 11px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .cs-pill.yes {
            background: #dcfce7;
            color: #15803d;
        }

        .cs-pill.no {
            background: #f1f5f9;
            color: #94a3b8;
        }

        .cs-pill.expired {
            background: #fee2e2;
            color: #dc2626;
            margin-left: 6px;
        }

        .cs-empty-state {
            padding: 32px;
            text-align: center;
            color: #94a3b8;
            font-size: 13.5px;
        }

        .cs-empty-state i {
            font-size: 22px;
            display: block;
            margin-bottom: 8px;
            color: #cbd5e1;
        }
    </style>

    <div class="content-page cs-page">
        <div class="content">
            <div class="container-fluid">
               <div class="cs-wrap" style="margin-top: 20px; width: 100%;">


                    {{-- Company Logo / Name / Code / Status --}}
                    <div class="cs-card">
                        <div class="cs-profile-body">
                            <div class="cs-logo-box">
                                @if ($company->company_logo)
                                    <img src="{{ asset('storage/' . $company->company_logo) }}" alt="Logo">
                                @else
                                    <span style="font-size:12px;color:#cbd5e1;">No Logo</span>
                                @endif
                            </div>
                            <div class="cs-profile-name">{{ $company->company_name ?? 'N/A' }}</div>
                            <div class="cs-profile-meta">Company Code: {{ $company->company_code ?? '-' }}</div>
                            <span
                                class="cs-badge {{ $company->current_company_status === 'Active' ? 'active' : 'inactive' }}">
                                {{ $company->current_company_status ?? 'N/A' }}
                            </span>
                            <div class="cs-actions">
                                <a href="{{ route('company.edit', $company->id) }}" class="cs-btn warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('company.index') }}" class="cs-btn">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Company Details (A-Z: name, code, currency, established, quota, website, statuses) --}}
                    <div class="cs-card">
                        <div class="cs-card-head"><span class="cs-title"><span class="cs-ico"><i
                                        class="fa fa-building"></i></span> Company Details</span></div>
                        <div class="cs-card-body">
                            <div class="cs-row"><label>Company Name</label>
                                <div class="val">{{ $company->company_name ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Company Code</label>
                                <div class="val">{{ $company->company_code ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Currency Type</label>
                                <div class="val {{ $company->currency_type ? '' : 'empty' }}">
                                    {{ is_object($company->currency_type) ? $company->currency_type->value ?? $company->currency_type->name : $company->currency_type ?? 'Not set' }}
                                </div>
                            </div>
                            <div class="cs-row"><label>Established On</label>
                                <div class="val">{{ optional($company->established_on)->format('d-M-Y') ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Quota</label>
                                <div class="val">{{ $company->quota ?? '-' }}</div>
                            </div>
                            <div class="cs-row">
                                <label>Website</label>
                                <div class="val">
                                    @if ($company->website)
                                        <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="cs-row"><label>Status on Establishment</label>
                                <div class="val">{{ $company->company_status_on_establishment ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Current Company Status</label>
                                <div class="val">{{ $company->current_company_status ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Company Images (Logo, Stamp, Letter Head Header/Footer, Signature) --}}
                    <div class="cs-card">
                        <div class="cs-card-head"><span class="cs-title"><span class="cs-ico"><i
                                        class="fa fa-images"></i></span> Company Images</span></div>
                        <div class="cs-card-body">
                            <div class="cs-thumb-row">
                                <div class="cs-thumb-box">
                                    <label>Company Logo</label>
                                    @if ($company->company_logo)
                                        <img src="{{ asset('storage/' . $company->company_logo) }}" class="cs-thumb">
                                    @else
                                        <div class="cs-thumb-empty">No image</div>
                                    @endif
                                </div>
                                <div class="cs-thumb-box">
                                    <label>Company Stamp</label>
                                    @if ($company->company_stamp)
                                        <img src="{{ asset('storage/' . $company->company_stamp) }}" class="cs-thumb">
                                    @else
                                        <div class="cs-thumb-empty">No image</div>
                                    @endif
                                </div>
                                <div class="cs-thumb-box">
                                    <label>Company Signature</label>
                                    @if ($company->company_signature)
                                        <img src="{{ asset('storage/' . $company->company_signature) }}" class="cs-thumb">
                                    @else
                                        <div class="cs-thumb-empty">No image</div>
                                    @endif
                                </div>
                                <div class="cs-thumb-box">
                                    <label>Letter Head Header</label>
                                    @if ($company->letter_head_header)
                                        <img src="{{ asset('storage/' . $company->letter_head_header) }}" class="cs-thumb">
                                    @else
                                        <div class="cs-thumb-empty">No image</div>
                                    @endif
                                </div>
                                <div class="cs-thumb-box">
                                    <label>Letter Head Footer</label>
                                    @if ($company->letter_head_footer)
                                        <img src="{{ asset('storage/' . $company->letter_head_footer) }}" class="cs-thumb">
                                    @else
                                        <div class="cs-thumb-empty">No image</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Company Registrations (Mohra, Munazzam, Cluster, DTS, IATA, NTN) --}}
                    <div class="cs-card">
                        <div class="cs-card-head"><span class="cs-title"><span class="cs-ico"><i
                                        class="fa fa-id-card"></i></span> Company Registrations</span></div>
                        <div class="cs-card-body">
                            <div class="cs-row"><label>Mohra Enrollment No</label>
                                <div class="val">{{ $company->mohra_enrollment_no ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Munazzam No</label>
                                <div class="val">{{ $company->munazzam_no ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Cluster Enrollment No</label>
                                <div class="val">{{ $company->cluster_enrollment_no ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>DTS No</label>
                                <div class="val">{{ $company->dts_no ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>DTS Expiry</label>
                                <div class="val">{{ optional($company->dts_expiry)->format('d-M-Y') ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>IATA No</label>
                                <div class="val">{{ $company->iata_no ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>IATA Expiry</label>
                                <div class="val">{{ optional($company->iata_expiry)->format('d-M-Y') ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>NTN</label>
                                <div class="val">{{ $company->ntn ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Director Details (Name, CNIC, CNIC Expiry, Photo, Detail) --}}
                    <div class="cs-card">
                        <div class="cs-card-head"><span class="cs-title"><span class="cs-ico"><i
                                        class="fa fa-user"></i></span> Director Details</span></div>
                        <div class="cs-card-body">
                            <div class="cs-director-photo">
                                @if ($company->director_photo)
                                    <img src="{{ asset('storage/' . $company->director_photo) }}" class="cs-avatar">
                                @else
                                    <div class="cs-avatar-empty"><i class="fa fa-user"></i></div>
                                @endif
                            </div>
                            <div class="cs-row"><label>Director Name</label>
                                <div class="val">{{ $company->director_name ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Director CNIC</label>
                                <div class="val">{{ $company->director_cnic ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Director CNIC Expiry</label>
                                <div class="val">{{ optional($company->director_cnic_expiry)->format('d-M-Y') ?? '-' }}
                                </div>
                            </div>
                            <div class="cs-row block"><label>Director Detail</label>
                                <div class="val">{{ $company->director_detail ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Bank Details --}}
                    <div class="cs-card">
                        <div class="cs-card-head"><span class="cs-title"><span class="cs-ico"><i
                                        class="fa fa-university"></i></span> Bank Details</span></div>
                        <div class="cs-card-body">
                            <div class="cs-row"><label>Bank Name</label>
                                <div class="val">{{ $company->bank_name ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Account Title</label>
                                <div class="val">{{ $company->account_title ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Branch</label>
                                <div class="val">{{ $company->branch ?? '-' }}</div>
                            </div>
                            <div class="cs-row"><label>Account Number</label>
                                <div class="val">{{ $company->account_number ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Addresses --}}
                    <div class="cs-card">
                        <div class="cs-card-head"><span class="cs-title"><span class="cs-ico"><i
                                        class="fa fa-map-marker-alt"></i></span> Addresses</span></div>
                        <div class="p-0">
                            @if ($company->addresses && $company->addresses->count())
                                <div class="table-responsive">
                                    <table class="cs-table">
                                        <thead>
                                            <tr>
                                                <th>Address Of</th>
                                                <th>Address</th>
                                                <th>Country</th>
                                                <th>City</th>
                                                <th>PO Box</th>
                                                <th>Zip Code</th>
                                                <th>Preferred</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($company->addresses as $addr)
                                                <tr>
                                                    <td>{{ $addr->address_of ?: '-' }}</td>
                                                    <td>{{ $addr->address ?: '-' }}</td>
                                                    <td>{{ $addr->country ?: '-' }}</td>
                                                    <td>{{ $addr->city ?: '-' }}</td>
                                                    <td>{{ $addr->po_box ?: '-' }}</td>
                                                    <td>{{ $addr->zip_code ?: '-' }}</td>
                                                    <td>
                                                        @if ($addr->is_preferred)
                                                        <span class="cs-pill yes">Yes</span>@else<span
                                                                class="cs-pill no">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="cs-empty-state"><i class="fa fa-map-marker-alt"></i>No addresses added.</div>
                            @endif
                        </div>
                    </div>

                    {{-- Contact Numbers --}}
                    <div class="cs-card">
                        <div class="cs-card-head"><span class="cs-title"><span class="cs-ico"><i
                                        class="fa fa-phone"></i></span> Contact Numbers</span></div>
                        <div class="p-0">
                            @if ($company->contactNumbers && $company->contactNumbers->count())
                                <div class="table-responsive">
                                    <table class="cs-table">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Contact</th>
                                                <th>Preferred</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($company->contactNumbers as $con)
                                                <tr>
                                                    <td>{{ $con->contact_type ?: '-' }}</td>
                                                    <td>{{ $con->contact ?: '-' }}</td>
                                                    <td>
                                                        @if ($con->is_preferred)
                                                        <span class="cs-pill yes">Yes</span>@else<span
                                                                class="cs-pill no">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="cs-empty-state"><i class="fa fa-phone"></i>No contact numbers added.</div>
                            @endif
                        </div>
                    </div>

                    {{-- Emails --}}
                    <div class="cs-card">
                        <div class="cs-card-head"><span class="cs-title"><span class="cs-ico"><i
                                        class="fa fa-envelope"></i></span> Emails</span></div>
                        <div class="p-0">
                            @if ($company->emails && $company->emails->count())
                                <div class="table-responsive">
                                    <table class="cs-table">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Email</th>
                                                <th>Preferred</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($company->emails as $em)
                                                <tr>
                                                    <td>{{ $em->email_type ?: '-' }}</td>
                                                    <td>{{ $em->email ?: '-' }}</td>
                                                    <td>
                                                        @if ($em->is_preferred)
                                                        <span class="cs-pill yes">Yes</span>@else<span
                                                                class="cs-pill no">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="cs-empty-state"><i class="fa fa-envelope"></i>No emails added.</div>
                            @endif
                        </div>
                    </div>

                    {{-- Licenses --}}
                    <div class="cs-card">
                        <div class="cs-card-head"><span class="cs-title"><span class="cs-ico"><i
                                        class="fa fa-certificate"></i></span> Licenses</span></div>
                        <div class="p-0">
                            @if ($company->licenses && $company->licenses->count())
                                <div class="table-responsive">
                                    <table class="cs-table">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>License No</th>
                                                <th>Place of Issue</th>
                                                <th>Date of Issue</th>
                                                <th>Valid Upto</th>
                                                <th>Preferred</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($company->licenses as $lic)
                                                @php $expired = $lic->valid_upto && \Carbon\Carbon::parse($lic->valid_upto)->isPast(); @endphp
                                                <tr>
                                                    <td>{{ $lic->license_type ?: '-' }}</td>
                                                    <td>{{ $lic->license_no ?: '-' }}</td>
                                                    <td>{{ $lic->place_of_issue ?: '-' }}</td>
                                                    <td>{{ $lic->date_of_issue ? \Carbon\Carbon::parse($lic->date_of_issue)->format('d-M-Y') : '-' }}
                                                    </td>
                                                    <td>
                                                        {{ $lic->valid_upto ? \Carbon\Carbon::parse($lic->valid_upto)->format('d-M-Y') : '-' }}
                                                        @if ($expired)
                                                            <span class="cs-pill expired">Expired</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($lic->is_preferred)
                                                        <span class="cs-pill yes">Yes</span>@else<span
                                                                class="cs-pill no">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="cs-empty-state"><i class="fa fa-certificate"></i>No licenses added.</div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
