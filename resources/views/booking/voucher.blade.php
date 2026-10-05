<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Voucher - {{ $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)) }}</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background: #eef1f5;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 20px 0;
        }

        .voucher-wrapper {
            background: #fff;
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
            padding: 22px 28px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            border: 1px solid #d5d5d5;
        }

        /* ── HEADER ── */
        .voucher-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .voucher-logo-box {
            width: 90px;
            flex-shrink: 0;
        }

        .voucher-logo-box img {
            width: 85px;
            height: auto;
            max-height: 85px;
            object-fit: contain;
            display: block;
        }

        .voucher-company-info {
            flex-grow: 1;
            padding-left: 15px;
        }

        .company-title {
            font-size: 21px;
            font-weight: 800;
            color: #000;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .company-sub-addr {
            font-size: 10px;
            color: #111;
            line-height: 1.35;
            margin-bottom: 2px;
        }

        .company-contacts {
            font-size: 10px;
            color: #111;
            line-height: 1.35;
        }

        .company-licence {
            font-size: 11px;
            font-weight: 700;
            color: #000;
            margin-top: 3px;
        }

        /* ── TITLE BADGE ── */
        .service-voucher-title-wrap {
            text-align: center;
            margin: 10px 0 12px;
        }

        .service-voucher-badge {
            display: inline-block;
            border: 2px solid #000;
            padding: 4px 28px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: capitalize;
            background: #fff;
        }

        /* ── 2-COLUMN METADATA BOX ── */
        .meta-box-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10.5px;
        }

        .meta-box-table td {
            padding: 3px 8px;
            vertical-align: top;
            border: none;
        }

        .meta-box-table .col-left {
            width: 48%;
            border-right: 1px solid #000;
        }

        .meta-box-table .col-right {
            width: 52%;
        }

        .meta-line {
            display: flex;
            margin-bottom: 2px;
            line-height: 1.35;
        }

        .meta-lbl {
            width: 90px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .meta-sep {
            width: 12px;
            text-align: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .meta-val {
            flex-grow: 1;
            font-weight: 600;
            color: #000;
        }

        /* ── SIDE-BY-SIDE TABLES (FLIGHT & PAX) ── */
        .dual-tables-wrap {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .table-half-left {
            width: 56%;
        }

        .table-half-right {
            width: 44%;
        }

        .v-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            font-size: 9.5px;
        }

        .v-table th,
        .v-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: left;
            vertical-align: middle;
        }

        .v-table thead.section-head th {
            background: #e2e2e2;
            text-align: center;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            padding: 4px;
            letter-spacing: 0.3px;
        }

        .v-table thead.col-head th {
            background: #f0f0f0;
            font-weight: 700;
            text-align: center;
            font-size: 9.5px;
            white-space: nowrap;
            padding: 3px;
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
            border: 1px solid #000;
            font-size: 10px;
            margin-bottom: 14px;
        }

        .service-details-table th,
        .service-details-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        .service-details-table thead.section-head th {
            background: #e2e2e2;
            text-align: center;
            font-weight: 700;
            font-size: 11.5px;
            text-transform: uppercase;
            padding: 4px;
            letter-spacing: 0.5px;
        }

        .service-details-table thead.col-head th {
            background: #f0f0f0;
            font-weight: 700;
            text-align: center;
            font-size: 10px;
            padding: 4px;
        }

        .hotel-desc-title {
            font-weight: 700;
            color: #000;
            margin-bottom: 1px;
        }

        .hotel-desc-sub {
            font-size: 9.5px;
            color: #222;
            line-height: 1.3;
        }

        /* ── SIGNATURES ── */
        .signatures-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 32px;
            margin-bottom: 22px;
            padding: 0 10px;
            font-size: 11px;
        }

        .sign-col {
            text-align: center;
            width: 25%;
        }

        .sign-line {
            border-bottom: 1px solid #000;
            margin-bottom: 4px;
            height: 1px;
            width: 100%;
        }

        .sign-label {
            font-weight: 700;
            color: #000;
        }

        .sign-company-right {
            text-align: right;
            font-weight: 700;
            font-size: 11.5px;
            width: 40%;
        }

        /* ── FOOTER CONTACT & POLICY ── */
        .footer-contacts-section {
            border-top: 1px solid #000;
            padding-top: 8px;
            margin-top: 10px;
            font-size: 10px;
            line-height: 1.45;
        }

        .footer-contacts-section .contact-item {
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 1px;
        }

        .footer-contacts-section .note-line {
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .dev-watermark {
            text-align: right;
            font-size: 8.5px;
            color: #555;
            margin-top: 12px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }

            .voucher-wrapper {
                box-shadow: none;
                border: none;
                padding: 10px 14px;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 6mm 8mm;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid py-3">

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 mb-3 no-print max-w-820 mx-auto" style="max-width: 820px;">
            <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Booking
            </a>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-printer me-1"></i> Print
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

            $companyPhone = 'Phone : 34133006,07, 32790124,161 Fax : 922134133008';
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
                $paxSummaryStr .= '+' . str_pad($childCount, 2, '0', STR_PAD_LEFT);
            }
            if ($infantCount > 0) {
                $paxSummaryStr .= '+' . str_pad($infantCount, 2, '0', STR_PAD_LEFT) . ' INF';
            }

            // Departure & Arrival Details
            $depFlight = $booking->departure_flight ?: 'SV701';
            $depSector = ($booking->package->departure_sector ?? 'KHI') . ' ' . ($booking->package->arrival_sector ?? 'JED');
            $depDateStr = $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d/m/Y') : ($booking->package->departure_date ? \Carbon\Carbon::parse($booking->package->departure_date)->format('d/m/Y') : '03/11/2026');
            $depEtd = $booking->departure_time ? \Carbon\Carbon::parse($booking->departure_time)->format('H:i') : '10:20';
            $depEta = '12:45';
            $depPnr = $booking->departure_pnr ?: 'ASDFA';

            $arrFlight = $booking->arrival_flight ?: 'SV1456';
            $arrSector = ($booking->package->arrival_sector ?? 'MED') . ' ' . ($booking->package->departure_sector ?? 'RUH');
            $arrDateStr = $booking->arrival_date ? \Carbon\Carbon::parse($booking->arrival_date)->format('d/m/Y') : ($booking->package->arrival_date ? \Carbon\Carbon::parse($booking->package->arrival_date)->format('d/m/Y') : '17/11/2026');
            $arrEtd = $booking->arrival_time ? \Carbon\Carbon::parse($booking->arrival_time)->format('H:i') : '00:05';
            $arrEta = '01:35';
            $arrPnr = $booking->arrival_pnr ?: $depPnr;
        @endphp

        <div id="voucherBox" class="voucher-wrapper">

            {{-- 1. HEADER --}}
            <div class="voucher-header">
                <div class="voucher-logo-box">
                    <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Pirwani Hajj Group Logo">
                </div>
                <div class="voucher-company-info text-start">
                    <div class="company-title">{{ $companyName }}</div>
                    <div class="company-sub-addr">{{ $companyAddress }}</div>
                    <div class="company-contacts">{{ $companyPhone }}</div>
                    <div class="company-licence">Govt. Licence No. {{ $licenceNo }}</div>
                </div>
            </div>

            {{-- 2. TITLE --}}
            <div class="service-voucher-title-wrap">
                <span class="service-voucher-badge">Service Voucher</span>
            </div>

            {{-- 3. TWO-COLUMN METADATA BOX --}}
            <table class="meta-box-table">
                <tr>
                    <td class="col-left">
                        <div class="meta-line">
                            <span class="meta-lbl">Sponsor</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ $companyName }} (MTQ)</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Address</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val"></span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">City</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">Karachi</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Tel</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val"></span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Fax</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val"></span>
                        </div>
                    </td>
                    <td class="col-right">
                        <div class="meta-line">
                            <span class="meta-lbl">Our Ref. No.</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ $ourRefNo }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Your Ref. No.</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val"></span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Booking Date</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ $booking->created_at ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') : date('d/m/Y') }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Issue Date</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Pax. Name</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ $paxSummaryStr }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Package Title</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ strtoupper($pkgTitle) }}</span>
                        </div>
                        <div class="meta-line">
                            <span class="meta-lbl">Care Of</span>
                            <span class="meta-sep">:</span>
                            <span class="meta-val">{{ strtoupper($careOfName) }}</span>
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
                                <th colspan="7">Arrival / Departure Details</th>
                            </tr>
                        </thead>
                        <thead class="col-head">
                            <tr>
                                <th>Action</th>
                                <th>Flight</th>
                                <th>Sector</th>
                                <th>Date</th>
                                <th>ETD</th>
                                <th>ETA</th>
                                <th>PNR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Departure :</td>
                                <td class="center">{{ $depFlight }}</td>
                                <td class="center">{{ $depSector }}</td>
                                <td class="center">{{ $depDateStr }}</td>
                                <td class="center">{{ $depEtd }}</td>
                                <td class="center">{{ $depEta }}</td>
                                <td class="center">{{ $depPnr }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Arrival :</td>
                                <td class="center">{{ $arrFlight }}</td>
                                <td class="center">{{ $arrSector }}</td>
                                <td class="center">{{ $arrDateStr }}</td>
                                <td class="center">{{ $arrEtd }}</td>
                                <td class="center">{{ $arrEta }}</td>
                                <td class="center">{{ $arrPnr }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Right Table: Pax. Info --}}
                <div class="table-half-right">
                    <table class="v-table">
                        <thead class="section-head">
                            <tr>
                                <th colspan="4">Pax. Info</th>
                            </tr>
                        </thead>
                        <thead class="col-head">
                            <tr>
                                <th style="width: 20px;">#</th>
                                <th>Passport No.</th>
                                <th>Name</th>
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
                                    <td class="center">{{ $idx + 1 }}</td>
                                    <td class="center fw-bold">{{ $p->passport_number ?: '—' }}</td>
                                    <td>{{ strtoupper($p->full_name) }}</td>
                                    <td class="center">{{ $pType }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="center">1</td>
                                    <td class="center fw-bold">{{ $booking->passport_number ?: '—' }}</td>
                                    <td>{{ strtoupper($bookingPartyName ?? 'GUEST') }}</td>
                                    <td class="center">Adult</td>
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
                        <th colspan="5">SERVICE DETAILS</th>
                    </tr>
                </thead>
                <thead class="col-head">
                    <tr>
                        <th style="width: 80px;">Type</th>
                        <th style="width: 100px;">Date & Time</th>
                        <th>Description</th>
                        <th style="width: 90px;">Ref. No.</th>
                        <th style="width: 80px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Transportation / Ziyarat / Transfer Rows --}}
                    @if ($booking->transports && $booking->transports->count() > 0)
                        @foreach ($booking->transports as $t)
                            <tr>
                                <td class="text-center fw-bold">Transfer</td>
                                <td class="text-center">{{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d/m/Y') : '' }}</td>
                                <td>
                                    For {{ strtoupper($t->route) }} by {{ $t->vehicle ?: '1 H1' }}
                                    @if ($t->notes)
                                        <div class="text-muted" style="font-size:9px;">{{ $t->notes }}</div>
                                    @endif
                                </td>
                                <td class="text-center">—</td>
                                <td class="text-center fw-bold">Confirm</td>
                            </tr>
                        @endforeach
                    @elseif ($booking->package && $booking->package->transports && $booking->package->transports->count() > 0)
                        @foreach ($booking->package->transports as $t)
                            <tr>
                                <td class="text-center fw-bold">Transfer</td>
                                <td class="text-center">{{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d/m/Y') : '' }}</td>
                                <td>For {{ strtoupper($t->route) }} by {{ $t->vehicle ?: '1 H1' }}</td>
                                <td class="text-center">—</td>
                                <td class="text-center fw-bold">Confirm</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center fw-bold">Transfer</td>
                            <td class="text-center"></td>
                            <td>For MAKKAH-MADINA ZIARAT by 1 H1</td>
                            <td class="text-center">—</td>
                            <td class="text-center fw-bold">Confirm</td>
                        </tr>
                        <tr>
                            <td class="text-center fw-bold">Transfer</td>
                            <td class="text-center"></td>
                            <td>For JED-MAK-MED HTL-MED APT by 1 H1</td>
                            <td class="text-center">—</td>
                            <td class="text-center fw-bold">Confirm</td>
                        </tr>
                    @endif

                    {{-- Hotel Rows --}}
                    @forelse ($booking->hotels as $hotel)
                        @php
                            $hIn = $hotel->check_in ? \Carbon\Carbon::parse($hotel->check_in) : null;
                            $hOut = $hotel->check_out ? \Carbon\Carbon::parse($hotel->check_out) : null;
                            $hNights = $hotel->no_of_nights ?: ($hIn && $hOut ? $hIn->diffInDays($hOut) : 7);
                            $hRoomType = $hotel->room_type ? ucfirst($hotel->room_type) : 'Triple';
                            $hRooms = $hotel->no_of_rooms ?: 1;
                            $hLoc = $hotel->location ? strtoupper($hotel->location) : 'MAKKAH';
                            $hRef = '22' . str_pad($hotel->id, 3, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <td class="text-center fw-bold">Hotel</td>
                            <td class="text-center">{{ $hIn ? $hIn->format('d/m/Y') : '—' }}</td>
                            <td>
                                <div class="hotel-desc-title">{{ strtoupper($hotel->hotel_name) }} In {{ $hLoc }}</div>
                                <div class="hotel-desc-sub">Room {{ $hRooms }} {{ $hRoomType }} Bed</div>
                                <div class="hotel-desc-sub">
                                    For {{ $hNights }} Nights From {{ $hIn ? $hIn->format('d-M-y') : '—' }} To {{ $hOut ? $hOut->format('d-M-y') : '—' }}
                                </div>
                            </td>
                            <td class="text-center fw-bold">{{ $hRef }}</td>
                            <td class="text-center fw-bold">Confirm</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center fw-bold">Hotel</td>
                            <td class="text-center">03/11/2026</td>
                            <td>
                                <div class="hotel-desc-title">EMAAR AL ANDALOUS (EAD 23) In MAKKAH</div>
                                <div class="hotel-desc-sub">Room 2 Triple Bed</div>
                                <div class="hotel-desc-sub">For 7 Nights From 03-Nov-26 To 10-Nov-26</div>
                            </td>
                            <td class="text-center fw-bold">22212</td>
                            <td class="text-center fw-bold">Confirm</td>
                        </tr>
                        <tr>
                            <td class="text-center fw-bold">Hotel</td>
                            <td class="text-center">10/11/2026</td>
                            <td>
                                <div class="hotel-desc-title">SAJA AL MADIANH (UNM 23) In MADINAH</div>
                                <div class="hotel-desc-sub">Room 2 Triple Bed</div>
                                <div class="hotel-desc-sub">For 7 Nights From 10-Nov-26 To 17-Nov-26</div>
                            </td>
                            <td class="text-center fw-bold">845</td>
                            <td class="text-center fw-bold">Confirm</td>
                        </tr>
                    @endforelse
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
                    For {{ strtoupper($companyName) }}
                </div>
            </div>

            {{-- 7. FOOTER CONTACT & HOTEL POLICY --}}
            <div class="footer-contacts-section">
                <div class="contact-item">MAKKAH CONTACT ATIF 00966558752823</div>
                <div class="contact-item">MADINAH CONTACT SHEHZAD 00966539833798</div>
                <div class="note-line">NOTE : PASSENGER CHECK IN TIME IN HOTEL : 04:00 PM</div>
                <div class="note-line" style="margin-left: 42px;">CHECK OUT TIME IN HOTEL : 12:00 PM</div>
            </div>

            <div class="dev-watermark">
                Software developed by http://pirwanitravels.com.pk
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
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Generating...';
            btn.disabled = true;

            const opt = {
                margin: [5, 5, 5, 5],
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
