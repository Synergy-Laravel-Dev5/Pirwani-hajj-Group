<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Voucher - {{ $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)) }}</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        :root {
            --gold: #bfa15f;
            --gold-dark: #8c7031;
            --gold-light: #f7edd4;
            --navy: #0f172a;
            --navy-light: #1e293b;
            --border-color: #334155;
            --emerald: #059669;
        }

        body {
            background: #e2e8f0;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 11.5px;
            color: #0f172a;
            margin: 0;
            padding: 24px 0;
            line-height: 1.45;
        }

        .voucher-wrapper {
            background: #ffffff;
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
            padding: 28px 34px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            position: relative;
        }

        /* ── HEADER ── */
        .voucher-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 14px;
            margin-bottom: 14px;
        }

        .voucher-logo-box {
            width: 105px;
            flex-shrink: 0;
        }

        .voucher-logo-box img {
            width: 95px;
            height: auto;
            max-height: 85px;
            object-fit: contain;
            display: block;
        }

        .voucher-company-info {
            flex-grow: 1;
            padding-left: 18px;
        }

        .company-title {
            font-family: 'Cinzel', serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .company-sub-addr {
            font-size: 10.5px;
            color: #334155;
            line-height: 1.35;
            margin-bottom: 2px;
        }

        .company-contacts {
            font-size: 10.5px;
            color: #334155;
            line-height: 1.35;
        }

        .company-licence-badge {
            display: inline-block;
            background: var(--navy);
            color: #fff;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.3px;
            margin-top: 4px;
            border: 1px solid var(--gold);
        }

        /* ── TITLE BADGE ── */
        .voucher-title-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(90deg, var(--navy) 0%, #1e293b 100%);
            color: #ffffff;
            padding: 7px 16px;
            border-radius: 4px;
            margin-bottom: 14px;
            border-left: 4px solid var(--gold);
        }

        .voucher-title-text {
            font-family: 'Cinzel', serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #f8fafc;
        }

        .voucher-ref-badge {
            font-size: 12px;
            font-weight: 700;
            color: #fef08a;
            background: rgba(255, 255, 255, 0.12);
            padding: 3px 10px;
            border-radius: 3px;
            border: 1px dashed rgba(254, 240, 138, 0.4);
            letter-spacing: 0.5px;
        }

        /* ── 2-COLUMN METADATA BOX ── */
        .meta-box-table {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 11px;
            background: #f8fafc;
            border-radius: 4px;
            overflow: hidden;
        }

        .meta-box-table td {
            padding: 6px 12px;
            vertical-align: top;
        }

        .meta-box-table .col-left {
            width: 48%;
            border-right: 1px solid #cbd5e1;
        }

        .meta-box-table .col-right {
            width: 52%;
        }

        .meta-line {
            display: flex;
            margin-bottom: 3px;
            line-height: 1.4;
        }

        .meta-lbl {
            width: 95px;
            font-weight: 700;
            color: #475569;
            flex-shrink: 0;
            font-size: 10.5px;
            text-transform: uppercase;
        }

        .meta-sep {
            width: 10px;
            text-align: center;
            font-weight: 700;
            color: #64748b;
            flex-shrink: 0;
        }

        .meta-val {
            flex-grow: 1;
            font-weight: 600;
            color: #0f172a;
        }

        /* ── SIDE-BY-SIDE TABLES (FLIGHT & PAX) ── */
        .dual-tables-wrap {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }

        .table-half-left {
            width: 55%;
        }

        .table-half-right {
            width: 45%;
        }

        .v-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            font-size: 10px;
            background: #fff;
        }

        .v-table th,
        .v-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            text-align: left;
            vertical-align: middle;
        }

        .v-table thead.section-head th {
            background: var(--navy);
            color: #fff;
            text-align: center;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            padding: 5px;
            letter-spacing: 0.5px;
            border-color: var(--navy);
        }

        .v-table thead.col-head th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: center;
            font-size: 10px;
            white-space: nowrap;
            padding: 4px;
        }

        .v-table td.center {
            text-align: center;
        }

        .v-table td.fw-bold {
            font-weight: 700;
        }

        /* ── FULL WIDTH SERVICE DETAILS TABLE ── */
        .service-details-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            font-size: 10.5px;
            margin-bottom: 14px;
            background: #fff;
        }

        .service-details-table th,
        .service-details-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: top;
        }

        .service-details-table thead.section-head th {
            background: var(--navy);
            color: #ffffff;
            text-align: center;
            font-weight: 700;
            font-size: 11.5px;
            text-transform: uppercase;
            padding: 6px;
            letter-spacing: 0.8px;
            border-color: var(--navy);
        }

        .service-details-table thead.col-head th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: center;
            font-size: 10.5px;
            padding: 5px;
        }

        .hotel-desc-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 11px;
            margin-bottom: 1px;
        }

        .hotel-desc-sub {
            font-size: 10px;
            color: #475569;
            line-height: 1.35;
        }

        .badge-confirm {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            font-weight: 700;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 3px;
            border: 1px solid #86efac;
            text-transform: uppercase;
        }

        .badge-service-type {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 700;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 3px;
            border: 1px solid #bae6fd;
            text-transform: uppercase;
        }

        /* ── SIGNATURES ── */
        .signatures-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 26px;
            margin-bottom: 18px;
            padding: 0 10px;
            font-size: 11px;
        }

        .sign-col {
            text-align: center;
            width: 26%;
        }

        .sign-line {
            border-bottom: 1px solid #64748b;
            margin-bottom: 5px;
            height: 1px;
            width: 100%;
        }

        .sign-label {
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.3px;
        }

        .sign-company-right {
            text-align: right;
            font-weight: 700;
            font-size: 11px;
            width: 40%;
            color: var(--navy);
        }

        /* ── FOOTER CONTACT & POLICY ── */
        .footer-contacts-section {
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            margin-top: 8px;
            font-size: 10px;
            line-height: 1.45;
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .footer-contacts-section .contact-item {
            font-weight: 700;
            text-transform: uppercase;
            color: var(--navy);
            margin-bottom: 2px;
        }

        .footer-contacts-section .note-line {
            font-weight: 600;
            color: #b91c1c;
            margin-top: 2px;
        }

        .dev-watermark {
            text-align: right;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 10px;
        }

        /* ── PRINT RULES ── */
        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }

            .voucher-wrapper {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid py-2">

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 mb-3 no-print mx-auto" style="max-width: 860px;">
            <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Booking
            </a>
            <a href="{{ route('report.check-in') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calendar-check me-1"></i> Check-In Report
            </a>
            <button onclick="window.print()" class="btn btn-dark btn-sm">
                <i class="bi bi-printer me-1"></i> Print Voucher
            </button>
            <button onclick="downloadVoucherPDF()" class="btn btn-primary btn-sm" id="downloadBtn">
                <i class="bi bi-download me-1"></i> Download PDF
            </button>
        </div>

        @php
            $company = $booking->company ?? \App\Models\Company::first();
            $companyName = $company->company_name ?? 'PIRWANI HAJJ GROUP (PVT) LTD.';
            $licenceNo = $company->company_code ?? '2990';
            
            $companyAddress = 'Office # 106, First Floor, Balad Trade Center, Near Bahar-e-Shariat Masjid Bahadurabad, Karachi.';
            if ($company && $company->addresses && $company->addresses->count() > 0) {
                $companyAddress = $company->addresses->first()->address ?? $companyAddress;
            }

            $companyPhone = 'Phone : 021-34133006, 021-34133007 | Fax : 021-34133008';
            if ($company && $company->contactNumbers && $company->contactNumbers->count() > 0) {
                $phones = $company->contactNumbers->pluck('contact_number')->filter()->implode(', ');
                if (!empty($phones)) {
                    $companyPhone = 'Phone : ' . $phones;
                }
            }

            // Reference No
            $ourRefNo = $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT));
            
            // Package Title
            $pkgTitle = $booking->package_name ?: ($booking->package->package_title ?? ($booking->package->name ?? ($booking->package_type ? strtoupper($booking->package_type . ' ' . date('Y')) : 'HAJJ / UMRAH 2026')));
            $pkgCode = $booking->package->package_code ?? ($booking->package_code ?? null);

            // Care Of
            $careOfName = $booking->care_of ?: ($booking->booking_for === 'company' ? ($booking->company->name ?? 'COUNTER CLIENT') : ($booking->client->name ?? 'COUNTER CLIENT'));

            // Passengers breakdown & Main Pax
            $persons = $booking->persons;
            $mainPerson = $persons->first();
            $mainPersonName = $mainPerson ? $mainPerson->full_name : ($booking->client->name ?? 'GUEST');

            $adultCount = 0;
            $childCount = 0;
            $infantCount = 0;

            foreach ($persons as $p) {
                $dob = $p->dob ? \Carbon\Carbon::parse($p->dob) : null;
                if ($dob) {
                    $age = $dob->age;
                    if ($age < 2) {
                        $infantCount++;
                    } elseif ($age < 12) {
                        $childCount++;
                    } else {
                        $adultCount++;
                    }
                } else {
                    $adultCount++;
                }
            }
            if ($adultCount === 0 && $childCount === 0 && $infantCount === 0) {
                $adultCount = $booking->no_of_pax ?: 1;
            }

            $paxSummaryStr = strtoupper($mainPersonName) . ' X ' . str_pad($adultCount, 2, '0', STR_PAD_LEFT);
            if ($childCount > 0) {
                $paxSummaryStr .= ' + ' . str_pad($childCount, 2, '0', STR_PAD_LEFT) . ' CHD';
            }
            if ($infantCount > 0) {
                $paxSummaryStr .= ' + ' . str_pad($infantCount, 2, '0', STR_PAD_LEFT) . ' INF';
            }

            // Departure & Arrival Details
            $depFlight = $booking->departure_flight ?: 'SV701';
            $depSector = ($booking->package->departure_sector ?? 'KHI') . ' - ' . ($booking->package->arrival_sector ?? 'JED');
            $depDateStr = $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d/m/Y') : ($booking->package->departure_date ? \Carbon\Carbon::parse($booking->package->departure_date)->format('d/m/Y') : '—');
            $depEtd = $booking->departure_time ? \Carbon\Carbon::parse($booking->departure_time)->format('H:i') : '10:20';
            $depEta = '12:45';
            $depPnr = $booking->departure_pnr ?: 'CONFIRMED';

            $arrFlight = $booking->arrival_flight ?: 'SV1456';
            $arrSector = ($booking->package->arrival_sector ?? 'MED') . ' - ' . ($booking->package->departure_sector ?? 'KHI');
            $arrDateStr = $booking->arrival_date ? \Carbon\Carbon::parse($booking->arrival_date)->format('d/m/Y') : ($booking->package->arrival_date ? \Carbon\Carbon::parse($booking->package->arrival_date)->format('d/m/Y') : '—');
            $arrEtd = $booking->arrival_time ? \Carbon\Carbon::parse($booking->arrival_time)->format('H:i') : '00:05';
            $arrEta = '01:35';
            $arrPnr = $booking->arrival_pnr ?: $depPnr;
        @endphp

        <div id="voucherBox" class="voucher-wrapper">

            {{-- 1. HEADER --}}
            <div class="voucher-header">
                <div class="voucher-logo-box">
                    <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Pirwani Hajj Group">
                </div>
                <div class="voucher-company-info text-start">
                    <div class="company-title">{{ $companyName }}</div>
                    <div class="company-sub-addr">{{ $companyAddress }}</div>
                    <div class="company-contacts">{{ $companyPhone }}</div>
                    <div class="company-licence-badge">Govt. Licence No. {{ $licenceNo }} | Munazzam # 2990</div>
                </div>
            </div>

            {{-- 2. TITLE BANNER --}}
            <div class="voucher-title-banner">
                <div class="voucher-title-text">
                    <i class="bi bi-bookmark-check-fill text-warning me-1"></i> Official Service & Hotel Voucher
                </div>
                <div class="voucher-ref-badge">
                    VOUCHER REF: {{ $ourRefNo }}
                </div>
            </div>

            {{-- 3. TWO-COLUMN METADATA BOX --}}
            <table class="meta-box-table">
                <tr>
                    <td class="col-left">
                        <div class="meta-line">
                            <span class="meta-lbl">Sponsor / Agency</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ $companyName }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Govt. Licence</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">Licence # {{ $licenceNo }} (MTQ)</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">City / Base</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">Karachi, Pakistan</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Booking Ref</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ $ourRefNo }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Package Code</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ $pkgCode ?: 'STANDARD' }}</span>
                        </div>
                    </td>
                    <td class="col-right">
                        <div class="meta-line">
                            <span class="meta-lbl">Passenger(s)</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val" style="color:var(--navy); font-size:11.5px;">{{ $paxSummaryStr }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Package Title</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ strtoupper($pkgTitle) }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Care Of / Agent</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ strtoupper($careOfName) }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Booking Date</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ $booking->created_at ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') : date('d/m/Y') }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Voucher Issued</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ now()->format('d/m/Y') }}</span>
                        </div>
                    </td>
                </tr>
            </table>

            {{-- 4. SIDE-BY-SIDE TABLES (FLIGHT & PAX) --}}
            <div class="dual-tables-wrap">

                {{-- Left Table: Arrival / Departure Details --}}
                <div class="table-half-left">
                    <table class="v-table">
                        <thead class="section-head">
                            <tr>
                                <th colspan="7">Flight Schedule</th>
                            </tr>
                        </thead>
                        <thead class="col-head">
                            <tr>
                                <th>Sector</th>
                                <th>Flight</th>
                                <th>Route</th>
                                <th>Date</th>
                                <th>ETD</th>
                                <th>ETA</th>
                                <th>PNR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-primary">Dep :</td>
                                <td class="center fw-bold">{{ $depFlight }}</td>
                                <td class="center">{{ $depSector }}</td>
                                <td class="center">{{ $depDateStr }}</td>
                                <td class="center">{{ $depEtd }}</td>
                                <td class="center">{{ $depEta }}</td>
                                <td class="center fw-bold">{{ $depPnr }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-success">Ret :</td>
                                <td class="center fw-bold">{{ $arrFlight }}</td>
                                <td class="center">{{ $arrSector }}</td>
                                <td class="center">{{ $arrDateStr }}</td>
                                <td class="center">{{ $arrEtd }}</td>
                                <td class="center">{{ $arrEta }}</td>
                                <td class="center fw-bold">{{ $arrPnr }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Right Table: Pax. Info with Photos / Avatars --}}
                <div class="table-half-right">
                    <table class="v-table">
                        <thead class="section-head">
                            <tr>
                                <th colspan="4">Pilgrim / Passenger Information</th>
                            </tr>
                        </thead>
                        <thead class="col-head">
                            <tr>
                                <th style="width: 24px;">#</th>
                                <th style="width: 32px;">Pic</th>
                                <th>Name & Passport No.</th>
                                <th style="width: 45px;">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($persons as $idx => $p)
                                @php
                                    $pType = 'Adult';
                                    if ($p->dob) {
                                        $age = \Carbon\Carbon::parse($p->dob)->age;
                                        if ($age < 2) $pType = 'Infant';
                                        elseif ($age < 12) $pType = 'Child';
                                    }
                                @endphp
                                <tr>
                                    <td class="center fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td class="center" style="padding: 2px;">
                                        @if (!empty($p->photo))
                                            <img src="{{ asset($p->photo) }}" alt="Photo" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1px solid var(--gold);">
                                        @else
                                            <div style="width: 22px; height: 22px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #475569;">
                                                {{ strtoupper(substr($p->full_name, 0, 1) ?: 'P') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong style="color:var(--navy);">{{ strtoupper($p->full_name) }}</strong>
                                        <div style="font-size:9px; color:#475569;">
                                            <i class="bi bi-passport me-1"></i>{{ $p->passport_number ?: 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="center fw-bold" style="font-size: 9.5px;">{{ $pType }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="center">1</td>
                                    <td class="center">
                                        <div style="width: 22px; height: 22px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700;">G</div>
                                    </td>
                                    <td>
                                        <strong style="color:var(--navy);">{{ strtoupper($bookingPartyName ?? 'GUEST') }}</strong>
                                        <div style="font-size:9px; color:#475569;">{{ $booking->passport_number ?: 'N/A' }}</div>
                                    </td>
                                    <td class="center fw-bold">Adult</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- 5. SERVICE DETAILS FULL WIDTH TABLE --}}
            <table class="service-details-table">
                <thead class="section-head">
                    <tr>
                        <th colspan="5">Accommodation & Service Details</th>
                    </tr>
                </thead>
                <thead class="col-head">
                    <tr>
                        <th style="width: 90px;">Service</th>
                        <th style="width: 110px;">Date / Check-In</th>
                        <th>Hotel / Stay / Transfer Description</th>
                        <th style="width: 90px;">Ref #</th>
                        <th style="width: 90px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Hotel Rows --}}
                    @forelse ($booking->hotels as $hotel)
                        @php
                            $hIn = $hotel->check_in ? \Carbon\Carbon::parse($hotel->check_in) : null;
                            $hOut = $hotel->check_out ? \Carbon\Carbon::parse($hotel->check_out) : null;
                            $hNights = $hotel->no_of_nights ?: ($hIn && $hOut ? $hIn->diffInDays($hOut) : 7);
                            $hRoomType = $hotel->room_type ? ucfirst($hotel->room_type) : 'Quad';
                            $hRooms = $hotel->no_of_rooms ?: 1;
                            $hLoc = $hotel->location ? strtoupper($hotel->location) : 'MAKKAH';
                            $hRef = 'HTL-' . str_pad($hotel->id, 4, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <td class="text-center">
                                <span class="badge-service-type"><i class="bi bi-building me-1"></i>Hotel</span>
                            </td>
                            <td class="text-center fw-bold">
                                <div>{{ $hIn ? $hIn->format('d/m/Y') : '—' }}</div>
                                <small class="text-muted fw-normal">In: 04:00 PM</small>
                            </td>
                            <td>
                                <div class="hotel-desc-title">
                                    {{ strtoupper($hotel->hotel_name) }} — <span class="text-primary">{{ $hLoc }}</span>
                                </div>
                                <div class="hotel-desc-sub">
                                    <strong>{{ $hRooms }} Room(s)</strong> ({{ $hRoomType }} Sharing)
                                    @if(!empty($hotel->room_number))
                                        &bull; <strong>Room #:</strong> <span class="badge bg-light text-dark border">{{ $hotel->room_number }}</span>
                                    @endif
                                    @if(!empty($hotel->gender) && $hotel->gender !== 'Any')
                                        &bull; <strong>Room Gender:</strong> <span class="badge bg-light text-primary border">{{ ucfirst($hotel->gender) }}</span>
                                    @endif
                                </div>
                                <div class="hotel-desc-sub text-dark">
                                    <i class="bi bi-moon-stars me-1 text-warning"></i><strong>{{ $hNights }} Nights</strong>: From <strong>{{ $hIn ? $hIn->format('d-M-Y') : '—' }}</strong> To <strong>{{ $hOut ? $hOut->format('d-M-Y') : '—' }}</strong> (Out: 12:00 PM)
                                </div>
                            </td>
                            <td class="text-center fw-bold text-muted">{{ $hRef }}</td>
                            <td class="text-center"><span class="badge-confirm"><i class="bi bi-check-circle me-1"></i>Confirmed</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center"><span class="badge-service-type">Hotel</span></td>
                            <td class="text-center fw-bold">03/11/2026</td>
                            <td>
                                <div class="hotel-desc-title">EMAAR AL ANDALOUS — <span class="text-primary">MAKKAH</span></div>
                                <div class="hotel-desc-sub">Room 2 Triple Bed</div>
                                <div class="hotel-desc-sub">For 7 Nights From 03-Nov-2026 To 10-Nov-2026</div>
                            </td>
                            <td class="text-center fw-bold text-muted">HTL-001</td>
                            <td class="text-center"><span class="badge-confirm">Confirmed</span></td>
                        </tr>
                    @endforelse

                    {{-- Transportation / Ziyarat / Transfer Rows --}}
                    @if ($booking->transports && $booking->transports->count() > 0)
                        @foreach ($booking->transports as $t)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size:10px; font-weight:700;">
                                        <i class="bi bi-bus-front me-1"></i>Transfer
                                    </span>
                                </td>
                                <td class="text-center">{{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d/m/Y') : 'Schedule' }}</td>
                                <td>
                                    <div class="fw-bold text-dark">For {{ strtoupper($t->route) }}</div>
                                    <div class="text-muted" style="font-size:9.5px;">Vehicle Type: {{ $t->vehicle ?: 'Private AC Transport / Bus' }}</div>
                                    @if ($t->notes)
                                        <div class="text-muted" style="font-size:9px;">{{ $t->notes }}</div>
                                    @endif
                                </td>
                                <td class="text-center text-muted">—</td>
                                <td class="text-center"><span class="badge-confirm">Confirmed</span></td>
                            </tr>
                        @endforeach
                    @elseif ($booking->package && $booking->package->transports && $booking->package->transports->count() > 0)
                        @foreach ($booking->package->transports as $t)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size:10px; font-weight:700;">
                                        <i class="bi bi-bus-front me-1"></i>Transfer
                                    </span>
                                </td>
                                <td class="text-center">{{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d/m/Y') : 'Schedule' }}</td>
                                <td>
                                    <div class="fw-bold text-dark">For {{ strtoupper($t->route) }}</div>
                                    <div class="text-muted" style="font-size:9.5px;">Vehicle Type: {{ $t->vehicle ?: 'Complete Luxury Transport' }}</div>
                                </td>
                                <td class="text-center text-muted">—</td>
                                <td class="text-center"><span class="badge-confirm">Confirmed</span></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size:10px; font-weight:700;">
                                    <i class="bi bi-bus-front me-1"></i>Transfer
                                </span>
                            </td>
                            <td class="text-center">Complete</td>
                            <td>
                                <div class="fw-bold text-dark">JEDDAH - MAKKAH - MADINAH - AIRPORT TRANSFERS & ZIYARAT</div>
                                <div class="text-muted" style="font-size:9.5px;">Complete ground transport as per package itinerary</div>
                            </td>
                            <td class="text-center text-muted">—</td>
                            <td class="text-center"><span class="badge-confirm">Confirmed</span></td>
                        </tr>
                    @endif
                </tbody>
            </table>

            {{-- 6. SIGNATURES --}}
            <div class="signatures-row">
                <div class="sign-col">
                    <div class="sign-line"></div>
                    <div class="sign-label">Prepared by</div>
                </div>
                <div class="sign-col">
                    <div class="sign-line"></div>
                    <div class="sign-label">Checked by</div>
                </div>
                <div class="sign-company-right">
                    <div class="sign-line"></div>
                    <div>For <strong>{{ strtoupper($companyName) }}</strong></div>
                    <small class="text-muted" style="font-size:9.5px;">Authorized Signature & Stamp</small>
                </div>
            </div>

            {{-- 7. FOOTER CONTACT & HOTEL POLICY --}}
            <div class="footer-contacts-section">
                <div class="d-flex flex-wrap justify-content-between">
                    <div>
                        <div class="contact-item"><i class="bi bi-geo-alt-fill text-danger me-1"></i>MAKKAH CONTACT: ATIF (+966 55 875 2823)</div>
                        <div class="contact-item"><i class="bi bi-geo-alt-fill text-success me-1"></i>MADINAH CONTACT: SHEHZAD (+966 53 983 3798)</div>
                    </div>
                    <div class="text-end">
                        <div class="note-line"><i class="bi bi-clock me-1"></i>CHECK-IN TIME: <strong>04:00 PM</strong></div>
                        <div class="note-line"><i class="bi bi-clock-history me-1"></i>CHECK-OUT TIME: <strong>12:00 PM</strong></div>
                    </div>
                </div>
            </div>

            <div class="dev-watermark">
                Issued electronically by Pirwani Hajj Group Management System • Valid without physical seal when verified online.
            </div>

        </div>
    </div>

    {{-- PDF Generation Script --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadVoucherPDF() {
            const element = document.getElementById('voucherBox');
            const btn = document.getElementById('downloadBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Generating PDF...';
            btn.disabled = true;

            const opt = {
                margin: [4, 4, 4, 4],
                filename: 'Service-Voucher-{{ $ourRefNo }}.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2.5,
                    useCORS: true,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                },
                pagebreak: {
                    mode: ['avoid-all', 'css', 'legacy']
                }
            };

            html2pdf().set(opt).from(element).save().then(function() {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(function(err) {
                console.error(err);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</body>

</html>
