<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hajj Contract - {{ $application->given_name }} {{ $application->surname }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0e1726;
            --dark-grey: #3e3e3e;
            --border-color: #777777;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #d4d4d4;
            color: #111;
            padding: 15px 10px;
        }

        .action-bar {
            max-width: 950px;
            margin: 0 auto 12px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contract-card {
            max-width: 950px;
            margin: 0 auto;
            background: #fff;
            padding: 20px 25px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            box-sizing: border-box;
        }

        .main-header-banner {
            background: var(--dark-grey);
            color: #fff;
            text-align: center;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 2px;
        }

        .main-header-banner h1 {
            font-weight: 900;
            font-size: 1.4rem;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .section-header-banner {
            background: #666;
            color: #fff;
            font-weight: 800;
            font-size: 0.90rem;
            text-align: center;
            padding: 4px;
            margin-top: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-badge {
            border: 1px solid #777;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.72rem;
            font-weight: 700;
            display: inline-block;
            margin-right: 6px;
            margin-bottom: 4px;
            background: #fdfdfd;
        }

        .policy-text {
            font-size: 0.68rem;
            line-height: 1.35;
            color: #333;
        }

        .docs-list {
            font-size: 0.70rem;
            font-weight: 600;
            color: #222;
            line-height: 1.4;
        }

        .app-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
            margin-top: 4px;
        }

        .app-table td, .app-table th {
            border: 1px solid var(--border-color);
            padding: 4px 8px;
            vertical-align: middle;
        }

        .app-table .lbl {
            font-weight: 700;
            font-size: 0.68rem;
            text-transform: uppercase;
            color: #333;
        }

        .val-text {
            font-weight: 700;
            font-size: 0.78rem;
            color: #000;
        }

        .photo-box {
            width: 110px;
            height: 130px;
            border: 1px solid #777;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            overflow: hidden;
            background: #f8f8f8;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .action-bar {
                display: none !important;
            }
            .contract-card {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="action-bar d-print-none">
        <a href="{{ route('hajj-application.index') }}" class="btn btn-dark btn-sm">⬅ Back to Applications</a>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('hajj-application.updateStatus', $application->id) }}" method="POST" class="d-flex align-items-center gap-1">
                @csrf
                @method('PATCH')
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">🖨 Print / Export PDF</button>
        </div>
    </div>

    <div class="contract-card" id="printableContract">

        <!-- Main Header -->
        <div class="main-header-banner">
            <h1>CONTRACT HAJJ {{ $application->year ?? '2027 / 1448 AH' }}</h1>
        </div>

        <!-- Top Payment Plan & Package Name -->
        <div class="row g-2 mb-2">
            <div class="col-md-5 col-5">
                <div class="fw-bold mb-1" style="font-size: 0.8rem;">PAYMENT PLAN</div>
                <div>
                    <span class="plan-badge">50% at the time of booking</span>
                    <span class="plan-badge">25% by 15 September 2026</span>
                    <span class="plan-badge">25% by 15 December 2026</span>
                </div>
            </div>
            <div class="col-md-7 col-7">
                <div class="d-flex align-items-center mb-1">
                    <span class="fw-bold me-2" style="font-size: 0.8rem; white-space: nowrap;">PACKAGE NAME:</span>
                    <span class="val-text border px-2 py-1 bg-light rounded flex-fill">
                        {{ $application->package_name ?? ($application->package->package_title ?? 'N/A') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Refund & Substitution Policy -->
        <div class="policy-text p-2 bg-light border rounded">
            <b>Refund &amp; Substitution Policy:</b><br>
            • If a Haji needs to cancel due to any emergency before 15th December 2026, a full refund will be issued after deducting US $350 per person as service charges.<br>
            • After 15th December 2026, and before Hajj visa issuance, the case will be treated as a substitution. The Haji may:<br>
            <div class="ms-3">
                - Nominate another person in their place, or<br>
                - Allow us to offer the same package to another waiting applicant. (depending on demand)<br>
            </div>
            In either case, US $350 per person will be deducted as service charges.<br>
            <small class="text-muted"><b>Important Note:</b> All policies / Packages / Bookings are subject to the final guidelines issued by the Saudi Government and the Government of Pakistan. Services are subject to payment plan and company will have the right to cancel / substitute in case payment plan not followed.</small>
        </div>

        <!-- Required Documents Section -->
        <div class="section-header-banner">
            REQUIRED DOCUMENTS
        </div>
        <div class="row docs-list px-2 py-1">
            <div class="col-md-6 col-6">
                1) Passenger passport copy (Upto 30 November 2027)<br>
                2) 2 photographs 4x3cm (white background)<br>
                3) Passenger ID Card copy (Nadra)
            </div>
            <div class="col-md-6 col-6">
                4) Next of Kin ID Card, contact number &amp; Relation<br>
                5) Passenger blood group<br>
                6) Children B-FORM required (Nadra)
            </div>
        </div>

        <!-- Hajj Application Grid Table -->
        <div class="section-header-banner">
            HAJJ APPLICATION
        </div>

        <table class="app-table">
            <tbody>
                <tr>
                    <td rowspan="5" style="width: 130px; text-align: center; background: #fafafa;">
                        <div class="photo-box">
                            @if ($application->photo && file_exists(public_path($application->photo)))
                                <img src="{{ asset($application->photo) }}" alt="Applicant Photo">
                            @else
                                <span class="text-muted" style="font-size: 0.7rem;">Passport Photo (White BG)</span>
                            @endif
                        </div>
                    </td>
                    <td colspan="2" class="p-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="lbl me-3">GENDER:</span>
                            <div class="val-text">
                                <span class="badge {{ strtolower($application->gender) == 'female' ? 'bg-info' : 'bg-primary' }} text-uppercase px-3 py-1">
                                    {{ $application->gender ?? 'MALE' }}
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%;">
                        <div class="lbl">SUR NAME:</div>
                        <div class="val-text">{{ $application->surname ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="lbl">GIVEN NAME:</div>
                        <div class="val-text">{{ $application->given_name }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="lbl">CNIC / NIC No:</div>
                        <div class="val-text">{{ $application->cnic_no }}</div>
                    </td>
                    <td>
                        <div class="lbl">DATE OF BIRTH:</div>
                        <div class="val-text">{{ $application->dob ? $application->dob->format('d M Y') : '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="lbl">PASSPORT NO:</div>
                        <div class="val-text">{{ $application->passport_no ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="lbl">PASSPORT EXPIRY:</div>
                        <div class="val-text">{{ $application->passport_expiry ? $application->passport_expiry->format('d M Y') : '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="lbl">FATHER'S / HUSBAND'S NAME:</div>
                        <div class="val-text">{{ $application->father_or_husband_name ?? '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="lbl">PRESENT POSTAL ADDRESS:</div>
                        <div class="val-text">{{ $application->postal_address ?? '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="lbl">TEHSIL CODE:</div>
                        <div class="val-text">{{ $application->tehsil_code ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="lbl">MOBILE NO:</div>
                        <div class="val-text">{{ $application->mobile_no }}</div>
                    </td>
                    <td>
                        <div class="lbl">TELEPHONE NO:</div>
                        <div class="val-text">{{ $application->telephone_no ?? '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="lbl">ARE YOU MARRIED?</div>
                        <div class="val-text">{{ $application->is_married ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="lbl">FIQAH:</div>
                        <div class="val-text">{{ $application->fiqah ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="lbl">BLOOD GROUP:</div>
                        <div class="val-text">{{ $application->blood_group ?? '-' }}</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Questions Table -->
        <table class="app-table mt-1">
            <tbody>
                <tr>
                    <td style="width: 70%;" class="lbl">PERFORMED HAJJ IN LAST 5 YEARS?</td>
                    <td class="text-center val-text">{{ $application->performed_hajj_last_5_years ?? 'NO' }}</td>
                </tr>
                <tr>
                    <td class="lbl">WANT TO PERFORM HAJJ-E-BADAL?</td>
                    <td class="text-center val-text">{{ $application->hajj_e_badal ?? 'NO' }}</td>
                </tr>
                <tr>
                    <td class="lbl">PERFORM HAJJ AS A GROUP WORKER?</td>
                    <td class="text-center val-text">{{ $application->group_worker ?? 'NO' }}</td>
                </tr>
                <tr>
                    <td class="lbl">ARE YOU MEHRAM OF A LADY?</td>
                    <td class="text-center val-text">{{ $application->is_mehram_of_lady ?? 'NO' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Emergency Nominee & Mehram Section -->
        <div class="row g-1 mt-1">
            <div class="col-md-6 col-6">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th colspan="2" class="lbl text-center bg-light">NOMINEE IN CASE OF DEATH (MUST BE ADULT / RELATIVE) EMERGENCY CONTACT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 35%;" class="lbl">NAME</td>
                            <td class="val-text">{{ $application->nominee_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">CONTACT NO</td>
                            <td class="val-text">{{ $application->nominee_contact ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">CNIC NO</td>
                            <td class="val-text">{{ $application->nominee_cnic ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">RELATIONSHIP</td>
                            <td class="val-text">{{ $application->nominee_relation ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6 col-6">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th colspan="2" class="lbl text-center bg-light">MEHRAM'S DETAILS (IF APPLICABLE)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 35%;" class="lbl">NAME</td>
                            <td class="val-text">{{ $application->mehram_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">RELATIONSHIP</td>
                            <td class="val-text">{{ $application->mehram_relation ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom Footer with Sign -->
        <div class="d-flex justify-content-end align-items-center mt-3 pt-2 border-top">
            <div class="fw-bold" style="font-size: 0.78rem;">
                Applicant Sign: ________________________________
            </div>
        </div>

    </div>

</body>

</html>
