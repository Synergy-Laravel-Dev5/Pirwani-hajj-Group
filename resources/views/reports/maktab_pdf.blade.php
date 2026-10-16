<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Maktab Report & Manifest</title>
    <style>
        @page {
            margin: 8mm 6mm 8mm 6mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #111;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header-box {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #000;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0 0 2px 0;
            color: #000;
        }
        .header-subtitle {
            font-size: 10px;
            font-weight: bold;
            color: #444;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }
        .header-meta {
            font-size: 8px;
            color: #555;
        }
        .kpi-table {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .kpi-table td {
            background-color: #f8fafc;
            border: 1px solid #ccc;
            padding: 4px 6px;
            font-size: 8px;
            text-align: center;
        }
        .kpi-table td strong {
            font-size: 10px;
            color: #000;
        }
        
        /* Official Manifest Table */
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }
        .manifest-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }
        .manifest-table th {
            background-color: #f1f5f9;
            color: #000;
            font-size: 8.5px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 5px 3px;
            border: 1.5px solid #000;
            text-transform: uppercase;
        }
        .manifest-table td {
            padding: 4px 3px;
            vertical-align: top;
            font-size: 8.5px;
            border: 1px solid #000;
        }
        .haji-photo {
            width: 32px;
            height: 36px;
            object-fit: cover;
            border-radius: 2px;
            border: 1px solid #777;
            display: inline-block;
            vertical-align: middle;
            margin-right: 4px;
        }
        .gender-male {
            color: #1e3a8a;
            font-weight: bold;
        }
        .gender-female {
            color: #9d174d;
            font-weight: bold;
        }
        .maktab-badge-a {
            background-color: #fef9c3;
            color: #854d0e;
            font-weight: bold;
            padding: 2px 4px;
            border: 1px solid #fde047;
            border-radius: 2px;
            display: inline-block;
            font-size: 7.5px;
        }
        .maktab-badge-c {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: bold;
            padding: 2px 4px;
            border: 1px solid #6ee7b7;
            border-radius: 2px;
            display: inline-block;
            font-size: 7.5px;
        }
        .center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Header Box -->
    <div class="header-box">
        <div class="header-title">PIRWANI HAJJ GROUP (PVT) LTD.</div>
        <div class="header-subtitle">Official Maktab Report & Manifest • Hajj 1448 / 2027</div>
        <div class="header-meta">
            Generated: <strong>{{ date('d M Y, h:i A') }}</strong>
            @if(!empty($maktabFilter) && $maktabFilter !== 'all')
                &nbsp;|&nbsp; Maktab: <strong>{{ strtoupper(str_replace('_', ' ', $maktabFilter)) }}</strong>
            @endif
            @if(!empty($locationFilter) && $locationFilter !== 'all')
                &nbsp;|&nbsp; Location: <strong>{{ strtoupper($locationFilter) }}</strong>
            @endif
            @if(!empty($genderFilter) && $genderFilter !== 'all')
                &nbsp;|&nbsp; Gender: <strong>{{ strtoupper($genderFilter) }}</strong>
            @endif
        </div>
    </div>

    <!-- KPI Summary Table -->
    <table class="kpi-table">
        <tr>
            <td>{{ (empty($locationFilter) || $locationFilter === 'all') ? 'Total Bookings' : 'Total Stays' }}<br><strong>{{ number_format($totalStays) }}</strong></td>
            <td>Total Rooms<br><strong>{{ number_format($totalRooms) }}</strong></td>
            <td>Total Pilgrims (Pax)<br><strong>{{ number_format($totalPilgrims) }}</strong></td>
            <td>👨 Male Pilgrims<br><strong>{{ number_format($totalMalePilgrims) }}</strong></td>
            <td>👩 Female Pilgrims<br><strong>{{ number_format($totalFemalePilgrims) }}</strong></td>
            <td>Active In-House<br><strong>{{ number_format($activeInHouse) }}</strong></td>
        </tr>
    </table>

    <!-- Manifest Table (Without Check-In & Check-Out) -->
    <table class="manifest-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style="width: 250px; text-align: left; padding-left: 6px;">PILGRIM / HAJJI DETAILS</th>
                <th style="width: 170px; text-align: left; padding-left: 6px;">HOTEL & LOCATION</th>
                <th style="width: 110px; text-align: center;">ROOM DETAILS</th>
                <th style="width: 170px; text-align: left; padding-left: 6px;">PACKAGE & VOUCHER</th>
                <th style="width: 110px; text-align: center;">MAKTAB / CAMP</th>
                <th style="width: 80px; text-align: center;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sr = 1;
                $isAllLoc = empty($locationFilter) || $locationFilter === 'all';
            @endphp
            @forelse($hotelStays as $idx => $stay)
                @php
                    $booking = $stay->booking;
                    $rawPersons = $booking ? $booking->persons : collect([]);
                    if (!empty($genderFilter) && $genderFilter !== 'all') {
                        $gLower = strtolower(trim($genderFilter));
                        $persons = $rawPersons->filter(fn($p) => strtolower(trim($p->gender ?? '')) === $gLower)->values();
                    } else {
                        $persons = $rawPersons;
                    }
                    if ($persons->isEmpty()) continue;

                    $mainPerson = $persons->first();
                    $otherPersons = $persons->slice(1);
                    $campName = $booking ? ($booking->camp ?: ($booking->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : 'Standard Camp')) : '—';
                    $isMaktabA = str_contains(strtolower($campName), 'a') || str_contains(strtolower($campName), 'zone 1') || str_contains(strtolower($campName), 'zone 2');

                    $voucherNo = $booking ? ($booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT))) : '—';
                    $pkgTitle = $booking && $booking->package ? ($booking->package->package_title ?: $booking->package->name) : '—';

                    $allStays = $stay->all_stays ?? collect([$stay]);

                    $checkIn = $stay->check_in ? \Carbon\Carbon::parse($stay->check_in) : null;
                    $checkOut = $stay->check_out ? \Carbon\Carbon::parse($stay->check_out) : null;
                    $statusText = 'Scheduled';
                    if ($checkIn) {
                        if ($checkIn->isToday()) $statusText = 'Checking In';
                        elseif ($checkOut && $checkOut->isPast()) $statusText = 'Checked Out';
                        elseif ($checkIn->isPast() && ($checkOut ? $checkOut->isFuture() || $checkOut->isToday() : true)) $statusText = 'In-House';
                        elseif ($checkIn->isFuture()) $statusText = 'Upcoming';
                    }
                @endphp
                <tr>
                    <td class="center fw-bold">{{ $sr++ }}</td>
                    
                    {{-- Pilgrim Details --}}
                    <td style="padding-left: 6px;">
                        <div>
                            @php
                                $photoPath = !empty($mainPerson?->photo) ? trim($mainPerson->photo) : null;
                                $photoSrc = null;
                                if ($photoPath) {
                                    $clean = ltrim($photoPath, '/\\');
                                    $full = public_path($clean);
                                    if (file_exists($full)) {
                                        $photoSrc = $full;
                                    }
                                }
                            @endphp
                            @if($photoSrc)
                                <img src="{{ $photoSrc }}" class="haji-photo" alt="">
                            @endif
                            <strong style="font-size: 9.5px; text-transform: uppercase;">
                                {{ $mainPerson->full_name ?? ($mainPerson->given_name . ' ' . $mainPerson->surname) }}
                            </strong>
                            @if(!empty($mainPerson->given_name) || !empty($mainPerson->surname))
                                <div style="font-size: 7.5px; color: #444;">{{ trim(($mainPerson->given_name ?? '') . ' ' . ($mainPerson->surname ?? '')) }}</div>
                            @endif
                            <span class="{{ strtolower($mainPerson->gender ?? '') === 'female' ? 'gender-female' : 'gender-male' }}" style="font-size: 8px;">
                                ({{ ucfirst($mainPerson->gender ?? 'Male') }})
                            </span>
                            @if($mainPerson->passport_number)
                                <span style="font-size: 8px; color: #444;"> | Pass: {{ $mainPerson->passport_number }}</span>
                                @php
                                    $mDoi = $mainPerson->date_of_issue ?: $mainPerson->passport_issue_date;
                                @endphp
                                @if(!empty($mDoi))
                                    <span style="font-size: 7.5px; color: #555;"> (DOI: {{ date('d/m/Y', strtotime($mDoi)) }})</span>
                                @endif
                                @if(!empty($mainPerson->passport_expiry_date))
                                    <span style="font-size: 7.5px; color: #b91c1c;"> (EXP: {{ date('d/m/Y', strtotime($mainPerson->passport_expiry_date)) }})</span>
                                @endif
                            @endif
                        </div>
                        @if($otherPersons->count() > 0)
                            <div style="margin-top: 3px; padding-top: 3px; border-top: 0.5px dashed #ccc; font-size: 8px; color: #444;">
                                <strong>Accompanying ({{ $otherPersons->count() }}):</strong>
                                @foreach($otherPersons as $oP)
                                    <div>
                                        • {{ $oP->full_name }} 
                                        @if(!empty($oP->given_name) || !empty($oP->surname))
                                            <span style="font-size: 7.5px; color: #555;">({{ trim(($oP->given_name ?? '') . ' ' . ($oP->surname ?? '')) }})</span>
                                        @endif
                                        <span class="{{ strtolower($oP->gender ?? '') === 'female' ? 'gender-female' : 'gender-male' }}">
                                             ({{ ucfirst($oP->gender ?? 'Male') }})
                                        </span>
                                        @if($oP->passport_number)
                                            - {{ $oP->passport_number }}
                                            @php
                                                $oDoi = $oP->date_of_issue ?: $oP->passport_issue_date;
                                            @endphp
                                            @if(!empty($oDoi))
                                                <span style="font-size: 7px; color: #555;">(DOI: {{ date('d/m/Y', strtotime($oDoi)) }})</span>
                                            @endif
                                            @if(!empty($oP->passport_expiry_date))
                                                <span style="font-size: 7px; color: #b91c1c;">(EXP: {{ date('d/m/Y', strtotime($oP->passport_expiry_date)) }})</span>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>

                    {{-- Hotel & Location --}}
                    <td style="padding-left: 6px;">
                        @if($isAllLoc)
                            @foreach($allStays as $st)
                                <div style="margin-bottom: 2px;">
                                    <strong>{{ strtoupper($st->location ?: 'LOC') }}:</strong>
                                    {{ strtoupper($st->hotel_name ?: 'Hotel') }}
                                </div>
                            @endforeach
                        @else
                            <strong style="text-transform: uppercase;">{{ $stay->hotel_name ?: 'Hotel Not Specified' }}</strong>
                            <div style="font-size: 8px; color: #555;">Location: {{ strtoupper($stay->location ?: 'Location') }}</div>
                        @endif
                    </td>

                    {{-- Room Details --}}
                    <td class="center">
                        @if($isAllLoc)
                            @foreach($allStays as $st)
                                <div>{{ ucfirst($st->room_type ?: 'Standard') }} ({{ $st->no_of_rooms ?: 1 }} R)</div>
                            @endforeach
                        @else
                            <strong>{{ ucfirst($stay->room_type ?: 'Standard') }}</strong>
                            <div style="font-size: 7.5px; color: #555;">{{ $stay->no_of_rooms ?: 1 }} Room(s)</div>
                        @endif
                    </td>

                    {{-- Package & Voucher --}}
                    <td style="padding-left: 6px;">
                        <div class="fw-bold">{{ $pkgTitle }}</div>
                        <div style="font-size: 7.5px; color: #64748b;">Voucher: {{ $voucherNo }}</div>
                        @if(!empty($booking->client->name))
                            <div style="font-size: 7.5px; color: #555;">Client: {{ $booking->client->name }}</div>
                        @endif
                    </td>

                    {{-- Maktab / Camp --}}
                    <td class="center">
                        <span class="{{ $isMaktabA ? 'maktab-badge-a' : 'maktab-badge-c' }}">
                            {{ $campName }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="center">
                        <span style="font-size: 8px; font-weight: bold; color: #2563eb;">{{ $statusText }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center" style="padding: 20px; font-style: italic; color: #888;">
                        No records match the selected Maktab or Location criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
