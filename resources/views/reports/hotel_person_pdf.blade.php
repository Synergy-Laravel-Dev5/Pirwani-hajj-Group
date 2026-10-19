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
            &bull; <strong>Hotel:</strong> {{ strtoupper($hotelFilter ?: 'ALL HOTELS') }}
            &bull; <strong>Location:</strong> {{ strtoupper($locationFilter ?: 'ALL') }}
            &bull; <strong>Date Range:</strong> {{ $fromDate ? date('d/m/Y', strtotime($fromDate)) : 'ALL' }} to {{ $toDate ? date('d/m/Y', strtotime($toDate)) : 'ALL' }}
            &bull; Page <span class="page-num"></span>
        </div>
    </div>

    {{-- KPI Summary Ribbon --}}
    <table class="kpi-table">
        <tr>
            <td>TOTAL PILGRIMS: <strong>{{ number_format($totalPersons) }} Persons</strong></td>
            <td>HOTEL STAYS: <strong>{{ number_format($totalStays) }}</strong> ({{ $totalRooms }} Rooms)</td>
            <td>MAKKAH: <strong>{{ number_format($locationBreakdown['makkah']['persons'] ?? 0) }} Pax</strong></td>
            <td>AZIZIA: <strong>{{ number_format($locationBreakdown['azizia']['persons'] ?? 0) }} Pax</strong></td>
            <td>MADINAH: <strong>{{ number_format($locationBreakdown['madinah']['persons'] ?? 0) }} Pax</strong></td>
            <td>MINA: <strong>{{ number_format($locationBreakdown['mina']['persons'] ?? 0) }} Pax</strong></td>
        </tr>
    </table>

    {{-- Manifest Table --}}
    <table class="manifest-table">
        <thead>
            <tr>
                <th style="width: 25px;">SR</th>
                <th style="width: 55px;">HAJJ ID</th>
                <th style="width: 50px;">HB #</th>
                <th style="width: 140px; text-align: left; padding-left: 5px;">PILGRIM FULL NAME</th>
                <th style="width: 45px;">GENDER</th>
                <th style="width: 65px;">PASSPORT</th>
                <th style="width: 110px; text-align: left; padding-left: 5px;">HOTEL NAME</th>
                <th style="width: 65px;">LOCATION</th>
                <th style="width: 65px;">ROOM TYPE</th>
                <th style="width: 45px;">ROOM NO</th>
                <th style="width: 55px;">CHECK-IN</th>
                <th style="width: 55px;">CHECK-OUT</th>
                <th style="width: 35px;">NIGHTS</th>
                <th style="width: 60px;">MAKTAB</th>
                <th style="width: 90px; text-align: left; padding-left: 5px;">PACKAGE</th>
                <th style="width: 60px;">VOUCHER #</th>
                <th style="width: 50px;">STATUS</th>
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
                        <td style="text-align: center;">{{ $globalSr++ }}</td>
                        <td style="text-align: center;">—</td>
                        <td style="text-align: center;">—</td>
                        <td style="text-align: left; padding-left: 5px; font-weight: bold;">{{ $booking?->client?->name ?? 'Direct Pilgrim' }}</td>
                        <td style="text-align: center;">—</td>
                        <td style="text-align: center; font-weight: bold;">{{ $booking?->passport_number ?: '—' }}</td>
                        <td style="text-align: left; padding-left: 5px; font-weight: bold;">{{ strtoupper($stay->hotel_name ?: 'Hotel') }}</td>
                        <td style="text-align: center; text-transform: uppercase;">{{ $stay->location ?: '—' }}</td>
                        <td style="text-align: center;">{{ ucfirst($stay->room_type ?: 'Standard') }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $stay->room_number ?? '—' }}</td>
                        <td style="text-align: center;">{{ $checkIn ? $checkIn->format('d/m/Y') : '—' }}</td>
                        <td style="text-align: center;">{{ $checkOut ? $checkOut->format('d/m/Y') : '—' }}</td>
                        <td style="text-align: center;">{{ $nights }}</td>
                        <td style="text-align: center;">{{ $campVal }}</td>
                        <td style="text-align: left; padding-left: 5px;">{{ $booking?->package?->package_title ?? ($booking?->package?->name ?? '—') }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $booking?->voucher_number ?? ('UB' . ($booking?->id ?? '')) }}</td>
                        <td style="text-align: center;">{{ $statusText }}</td>
                    </tr>
                @else
                    @foreach($persons as $oIdx => $person)
                        <tr class="{{ $oIdx === 0 ? 'hotel-divider-top' : 'hotel-sub-row' }}">
                            <td style="text-align: center; font-weight: bold;">{{ $globalSr++ }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ !empty($person->hajj_id) ? $person->hajj_id : '—' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ !empty($person->hb_number) ? $person->hb_number : '—' }}</td>
                            <td style="text-align: left; padding-left: 5px; font-weight: bold; text-transform: uppercase;">
                                {{ $person->full_name }}
                            </td>
                            <td class="{{ strtoupper($person->gender ?? '') === 'FEMALE' ? 'gender-female' : 'gender-male' }}">
                                {{ strtoupper($person->gender ?? '—') }}
                            </td>
                            <td style="text-align: center; font-weight: bold;">{{ $person->passport_number ?: '—' }}</td>

                            @if($oIdx === 0)
                                <td rowspan="{{ $rowspan }}" style="text-align: left; padding-left: 5px; font-weight: bold;">
                                    {{ strtoupper($stay->hotel_name ?: 'Hotel') }}
                                </td>
                                <td rowspan="{{ $rowspan }}" style="text-align: center; text-transform: uppercase;">
                                    {{ $stay->location ?: '—' }}
                                </td>
                                <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ ucfirst($stay->room_type ?: 'Standard') }}</td>
                                <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $stay->room_number ?? '—' }}</td>
                                <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $checkIn ? $checkIn->format('d/m/Y') : '—' }}</td>
                                <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $checkOut ? $checkOut->format('d/m/Y') : '—' }}</td>
                                <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $nights }}</td>
                                <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $campVal }}</td>
                                <td rowspan="{{ $rowspan }}" style="text-align: left; padding-left: 5px;">{{ $booking?->package?->package_title ?? ($booking?->package?->name ?? '—') }}</td>
                                <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $booking?->voucher_number ?? ('UB' . ($booking?->id ?? '')) }}</td>
                                <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $statusText }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="17" style="text-align: center; padding: 20px; color: #777;">No Hotel Person Records Found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
