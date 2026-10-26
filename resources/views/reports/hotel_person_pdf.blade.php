<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Hotel & Location Person Occupancy Report</title>
    <style>
        @page {
            margin: 8mm 6mm 8mm 6mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
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
            font-size: 9.5px;
            color: #000;
        }
        
        /* Manifest Table */
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
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 5px 3px;
            border: 1.5px solid #000;
            text-transform: uppercase;
        }
        .manifest-table td {
            padding: 4px 3px;
            vertical-align: middle;
            font-size: 8px;
            border: 1px solid #000;
        }
        .gender-male {
            color: #1e3a8a;
            font-weight: bold;
            text-align: center;
        }
        .gender-female {
            color: #9d174d;
            font-weight: bold;
            text-align: center;
        }
        .hotel-divider-top td {
            border-top: 2px solid #000 !important;
        }
        .hotel-sub-row td {
            border-top: 0.5px solid #aaa !important;
        }
        tr {
            page-break-inside: avoid;
        }
        .page-num:after {
            content: counter(page);
        }
    </style>
</head>
<body>

    {{-- Official Report Header --}}
    <div class="header-box">
        <div class="header-title">PIRWANI HAJJ GROUP</div>
        <div class="header-subtitle">Official Hotel & Location Person Occupancy Report</div>
        <div class="header-meta">
            <strong>Generated:</strong> {{ now()->format('d M Y, h:i A') }}
            @php $hPdfLabel = is_array($hotelFilter ?? null) ? (empty($hotelFilter) ? 'ALL HOTELS' : implode(', ', $hotelFilter)) : ($hotelFilter ?: 'ALL HOTELS'); @endphp
            &bull; <strong>Hotel:</strong> {{ strtoupper($hPdfLabel) }}
            &bull; <strong>Location:</strong> {{ strtoupper($locationFilter ?: 'ALL') }}
            @if(!empty($aziziaPhase) && $locationFilter === 'azizia')
                &bull; <strong>Phase:</strong> {{ strtoupper($aziziaPhase) === 'FIRST' ? '1ST CHECK-IN (PRE-HAJJ)' : (strtoupper($aziziaPhase) === 'SECOND' ? '2ND CHECK-IN (POST-HAJJ)' : 'BOTH CHECK-INS') }}
            @endif
            &bull; <strong>Date Range:</strong> {{ $fromDate ? date('d/m/Y', strtotime($fromDate)) : 'ALL' }} to {{ $toDate ? date('d/m/Y', strtotime($toDate)) : 'ALL' }}
            &bull; Page <span class="page-num"></span>
        </div>
    </div>

    {{-- KPI Summary Ribbon --}}
    <table class="kpi-table">
        <tr>
            <td>TOTAL PILGRIMS: <strong>{{ number_format($totalUniquePersons ?? $totalPersons) }} Persons</strong></td>
            <td>HOTEL STAYS: <strong>{{ number_format($totalStays) }}</strong> ({{ $totalRooms }} Rooms)</td>
            <td>MAKKAH: <strong>{{ number_format($locationBreakdown['makkah']['persons'] ?? 0) }} Pax</strong></td>
            <td>AZIZIA: <strong>{{ number_format($locationBreakdown['azizia']['persons'] ?? 0) }} Pax</strong> <span style="font-size: 8px;">(1st: {{ $aziziaStats['first'] ?? 182 }} | 2nd: {{ $aziziaStats['second'] ?? 177 }})</span></td>
            <td>MADINAH: <strong>{{ number_format($locationBreakdown['madinah']['persons'] ?? 0) }} Pax</strong></td>
            <td>MINA: <strong>{{ number_format($locationBreakdown['mina']['persons'] ?? 0) }} Pax</strong></td>
        </tr>
    </table>

    {{-- Manifest Table --}}
    @php
        $showCol = function($key) use ($visibleColumns) {
            if ($visibleColumns === null) {
                return in_array($key, ['sr', 'hajj-id', 'hb', 'pilgrim', 'gender', 'passport', 'hotel', 'location', 'room', 'room-no', 'checkin', 'checkout', 'nights', 'maktab', 'package', 'voucher', 'status']);
            }
            return in_array($key, $visibleColumns);
        };
        $allKeys = ['sr', 'picture', 'hajj-id', 'hb', 'pilgrim', 'given-name', 'surname', 'father-name', 'gender', 'passport', 'passport-doi', 'passport-exp', 'hotel', 'location', 'room', 'room-no', 'checkin', 'checkout', 'nights', 'maktab', 'package', 'voucher', 'client', 'status'];
        $colCount = 0;
        foreach ($allKeys as $k) {
            if ($showCol($k)) $colCount++;
        }
        if ($colCount === 0) $colCount = 17;
    @endphp
    <table class="manifest-table">
        <thead>
            <tr>
                @if($showCol('sr')) <th style="width: 25px;">SR</th> @endif
                @if($showCol('hajj-id')) <th style="width: 55px;">HAJJ ID</th> @endif
                @if($showCol('hb')) <th style="width: 50px;">HB #</th> @endif
                @if($showCol('pilgrim')) <th style="width: 140px; text-align: left; padding-left: 5px;">PILGRIM FULL NAME</th> @endif
                @if($showCol('gender')) <th style="width: 45px;">GENDER</th> @endif
                @if($showCol('passport')) <th style="width: 65px;">PASSPORT</th> @endif
                @if($showCol('hotel')) <th style="width: 110px; text-align: left; padding-left: 5px;">HOTEL NAME</th> @endif
                @if($showCol('location')) <th style="width: 65px;">LOCATION</th> @endif
                @if($showCol('room')) <th style="width: 65px;">ROOM TYPE</th> @endif
                @if($showCol('room-no')) <th style="width: 45px;">ROOM NO</th> @endif
                @if($showCol('checkin')) <th style="width: 55px;">CHECK-IN</th> @endif
                @if($showCol('checkout')) <th style="width: 55px;">CHECK-OUT</th> @endif
                @if($showCol('nights')) <th style="width: 35px;">NIGHTS</th> @endif
                @if($showCol('maktab')) <th style="width: 60px;">MAKTAB</th> @endif
                @if($showCol('package')) <th style="width: 90px; text-align: left; padding-left: 5px;">PACKAGE</th> @endif
                @if($showCol('voucher')) <th style="width: 60px;">VOUCHER #</th> @endif
                @if($showCol('status')) <th style="width: 50px;">STATUS</th> @endif
            </tr>
        </thead>
        <tbody>
            @php $globalSr = 1; @endphp
            @forelse($hotelStays as $stay)
                @php
                    $booking = $stay->booking;
                    $persons = $booking ? $booking->persons : collect([]);
                    $totalPax = $persons->count();
                    $rowspan = max(1, $totalPax);

                    $checkIn = $stay->check_in ? \Carbon\Carbon::parse($stay->check_in) : null;
                    $checkOut = $stay->check_out ? \Carbon\Carbon::parse($stay->check_out) : null;
                    $nights = ($checkIn && $checkOut) ? $checkIn->diffInDays($checkOut) : '—';

                    $statusText = 'Scheduled';
                    if ($checkIn) {
                        if ($checkIn->isToday()) $statusText = 'Check-in Today';
                        elseif ($checkIn->isTomorrow()) $statusText = 'Tomorrow';
                        elseif ($checkOut && $checkOut->isPast()) $statusText = 'Checked Out';
                        elseif ($checkIn->isPast() && ($checkOut ? $checkOut->isFuture() || $checkOut->isToday() : true)) $statusText = 'In-House';
                        elseif ($checkIn->isFuture()) $statusText = 'Upcoming';
                    }

                    $campVal = $booking?->camp ?: ($booking?->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : '—');
                @endphp

                @if($totalPax === 0)
                    <tr class="hotel-divider-top">
                        @if($showCol('sr')) <td style="text-align: center;">{{ $globalSr++ }}</td> @endif
                        @if($showCol('hajj-id')) <td style="text-align: center;">—</td> @endif
                        @if($showCol('hb')) <td style="text-align: center;">—</td> @endif
                        @if($showCol('pilgrim')) <td style="text-align: left; padding-left: 5px; font-weight: bold;">{{ $booking?->client?->name ?? 'Direct Pilgrim' }}</td> @endif
                        @if($showCol('gender')) <td style="text-align: center;">—</td> @endif
                        @if($showCol('passport')) <td style="text-align: center; font-weight: bold;">{{ $booking?->passport_number ?: '—' }}</td> @endif
                        @if($showCol('hotel')) <td style="text-align: left; padding-left: 5px; font-weight: bold;">{{ strtoupper($stay->clean_hotel_name ?? ($stay->hotel_name ?: 'Hotel')) }}@if(!empty($stay->azizia_phase_name))<br><span style="font-size: 8px; color: #b45309; font-weight: bold;">[{{ strtoupper($stay->azizia_phase_name) }}]</span>@endif</td> @endif
                        @if($showCol('location')) <td style="text-align: center; text-transform: uppercase;">{{ $stay->clean_location ?? ($stay->location ?: '—') }}</td> @endif
                        @if($showCol('room')) <td style="text-align: center;">{{ ucfirst($stay->room_type ?: 'Standard') }}</td> @endif
                        @if($showCol('room-no')) <td style="text-align: center; font-weight: bold;">{{ $stay->room_number ?? '—' }}</td> @endif
                        @if($showCol('checkin')) <td style="text-align: center;">{{ $checkIn ? $checkIn->format('d/m/Y') : '—' }}</td> @endif
                        @if($showCol('checkout')) <td style="text-align: center;">{{ $checkOut ? $checkOut->format('d/m/Y') : '—' }}</td> @endif
                        @if($showCol('nights')) <td style="text-align: center;">{{ $nights }}</td> @endif
                        @if($showCol('maktab')) <td style="text-align: center;">{{ $campVal }}</td> @endif
                        @if($showCol('package')) <td style="text-align: left; padding-left: 5px;">{{ $booking?->package?->package_title ?? ($booking?->package?->name ?? '—') }}</td> @endif
                        @if($showCol('voucher')) <td style="text-align: center; font-weight: bold;">{{ $booking?->voucher_number ?? ('UB' . ($booking?->id ?? '')) }}</td> @endif
                        @if($showCol('status')) <td style="text-align: center;">{{ $statusText }}</td> @endif
                    </tr>
                @else
                    @foreach($persons as $oIdx => $person)
                        <tr class="{{ $oIdx === 0 ? 'hotel-divider-top' : 'hotel-sub-row' }}">
                            @if($showCol('sr')) <td style="text-align: center; font-weight: bold;">{{ $globalSr++ }}</td> @endif
                            @if($showCol('hajj-id')) <td style="text-align: center; font-weight: bold;">{{ !empty($person->hajj_id) ? $person->hajj_id : '—' }}</td> @endif
                            @if($showCol('hb')) <td style="text-align: center; font-weight: bold;">{{ !empty($person->hb_number) ? $person->hb_number : '—' }}</td> @endif
                            @if($showCol('pilgrim'))
                                <td style="text-align: left; padding-left: 5px; font-weight: bold; text-transform: uppercase;">
                                    {{ $person->full_name }}
                                </td>
                            @endif
                            @if($showCol('gender'))
                                <td class="{{ strtoupper($person->gender ?? '') === 'FEMALE' ? 'gender-female' : 'gender-male' }}">
                                    {{ strtoupper($person->gender ?? '—') }}
                                </td>
                            @endif
                            @if($showCol('passport')) <td style="text-align: center; font-weight: bold;">{{ $person->passport_number ?: '—' }}</td> @endif

                            @if($oIdx === 0)
                                @if($showCol('hotel'))
                                    <td rowspan="{{ $rowspan }}" style="text-align: left; padding-left: 5px; font-weight: bold;">
                                        {{ strtoupper($stay->clean_hotel_name ?? ($stay->hotel_name ?: 'Hotel')) }}
                                        @if(!empty($stay->azizia_phase_name))
                                            <br><span style="font-size: 8px; color: #b45309; font-weight: bold;">[{{ strtoupper($stay->azizia_phase_name) }}]</span>
                                        @endif
                                    </td>
                                @endif
                                @if($showCol('location'))
                                    <td rowspan="{{ $rowspan }}" style="text-align: center; text-transform: uppercase;">
                                        {{ $stay->clean_location ?? ($stay->location ?: '—') }}
                                    </td>
                                @endif
                                @if($showCol('room')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ ucfirst($stay->room_type ?: 'Standard') }}</td> @endif
                                @if($showCol('room-no')) <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $stay->room_number ?? '—' }}</td> @endif
                                @if($showCol('checkin')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $checkIn ? $checkIn->format('d/m/Y') : '—' }}</td> @endif
                                @if($showCol('checkout')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $checkOut ? $checkOut->format('d/m/Y') : '—' }}</td> @endif
                                @if($showCol('nights')) <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $nights }}</td> @endif
                                @if($showCol('maktab')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $campVal }}</td> @endif
                                @if($showCol('package')) <td rowspan="{{ $rowspan }}" style="text-align: left; padding-left: 5px;">{{ $booking?->package?->package_title ?? ($booking?->package?->name ?? '—') }}</td> @endif
                                @if($showCol('voucher')) <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $booking?->voucher_number ?? ('UB' . ($booking?->id ?? '')) }}</td> @endif
                                @if($showCol('status')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $statusText }}</td> @endif
                            @endif
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="{{ $colCount }}" style="text-align: center; padding: 20px; color: #777;">No Hotel Person Records Found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
