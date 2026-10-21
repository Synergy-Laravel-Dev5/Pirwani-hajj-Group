<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Booking Flight Report & Schedule</title>
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
        .booking-divider-top td {
            border-top: 2px solid #000 !important;
        }
        .booking-sub-row td {
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
        <div class="header-subtitle">Official Booking Flight Report & Schedule Manifest</div>
        <div class="header-meta">
            <strong>Generated:</strong> {{ now()->format('d M Y, h:i A') }}
            &bull; <strong>Airline:</strong> {{ strtoupper($airline ?: 'ALL') }}
            &bull; <strong>Status / Type:</strong> {{ strtoupper($flightType ?: 'ALL') }}
            &bull; <strong>Date Range:</strong> {{ $fromDate ? date('d/m/Y', strtotime($fromDate)) : 'ALL' }} to {{ $toDate ? date('d/m/Y', strtotime($toDate)) : 'ALL' }}
            &bull; Page <span class="page-num"></span>
        </div>
    </div>

    {{-- KPI Summary Ribbon --}}
    <table class="kpi-table">
        <tr>
            <td>FLIGHT GROUPS: <strong>{{ number_format($totalBookings) }}</strong></td>
            <td>PASSENGERS: <strong>{{ number_format($totalPassengers) }}</strong></td>
            <td>DEPARTURES: <strong>{{ number_format($departuresScheduled) }}</strong></td>
            <td>RETURN FLIGHTS: <strong>{{ number_format($returnsScheduled) }}</strong></td>
            <td>PNRS ISSUED: <strong>{{ number_format($pnrsIssued) }}</strong></td>
            <td>FLIGHT CHARGES: <strong>PKR {{ number_format($totalFlightCharges) }}</strong></td>
        </tr>
    </table>

    {{-- Flight Manifest Table --}}
    @php
        $showCol = function($key) use ($visibleColumns) {
            if (!isset($visibleColumns) || empty($visibleColumns)) {
                return in_array($key, ['sr', 'hajj-id', 'hb', 'pilgrim', 'gender', 'passport', 'voucher', 'dep-airline', 'dep-flight', 'dep-sector', 'dep-date', 'dep-pnr', 'ret-airline', 'ret-flight', 'ret-sector', 'ret-date', 'ret-pnr', 'duration', 'package', 'charges', 'status']);
            }
            return in_array($key, $visibleColumns);
        };
        $allKeys = ['sr', 'picture', 'hajj-id', 'hb', 'pilgrim', 'given-name', 'surname', 'father-name', 'gender', 'passport', 'passport-doi', 'passport-exp', 'voucher', 'dep-airline', 'dep-flight', 'dep-sector', 'dep-date', 'dep-pnr', 'ret-airline', 'ret-flight', 'ret-sector', 'ret-date', 'ret-pnr', 'duration', 'maktab', 'client', 'package', 'charges', 'status'];
        $colCount = 0;
        foreach ($allKeys as $k) {
            if ($showCol($k)) $colCount++;
        }
        if ($colCount === 0) $colCount = 21;
    @endphp
    <table class="manifest-table">
        <thead>
            <tr>
                @if($showCol('sr')) <th style="width: 20px;">SR</th> @endif
                @if($showCol('hajj-id')) <th style="width: 48px;">HAJJ ID</th> @endif
                @if($showCol('hb')) <th style="width: 44px;">HB #</th> @endif
                @if($showCol('pilgrim')) <th style="width: 120px; text-align: left; padding-left: 4px;">PASSENGER FULL NAME</th> @endif
                @if($showCol('gender')) <th style="width: 38px;">GENDER</th> @endif
                @if($showCol('passport')) <th style="width: 58px;">PASSPORT</th> @endif
                @if($showCol('voucher')) <th style="width: 55px;">VOUCHER #</th> @endif
                @if($showCol('dep-airline')) <th style="width: 65px;">DEP AIRLINE</th> @endif
                @if($showCol('dep-flight')) <th style="width: 50px;">DEP FLIGHT</th> @endif
                @if($showCol('dep-sector')) <th style="width: 60px;">DEP SECTOR</th> @endif
                @if($showCol('dep-date')) <th style="width: 52px;">DEP DATE</th> @endif
                @if($showCol('dep-pnr')) <th style="width: 50px;">DEP PNR</th> @endif
                @if($showCol('ret-airline')) <th style="width: 65px;">RET AIRLINE</th> @endif
                @if($showCol('ret-flight')) <th style="width: 50px;">RET FLIGHT</th> @endif
                @if($showCol('ret-sector')) <th style="width: 60px;">RET SECTOR</th> @endif
                @if($showCol('ret-date')) <th style="width: 52px;">RET DATE</th> @endif
                @if($showCol('ret-pnr')) <th style="width: 50px;">RET PNR</th> @endif
                @if($showCol('duration')) <th style="width: 35px;">DAYS</th> @endif
                @if($showCol('package')) <th style="width: 75px; text-align: left; padding-left: 4px;">PACKAGE</th> @endif
                @if($showCol('charges')) <th style="width: 55px;">CHARGES</th> @endif
                @if($showCol('status')) <th style="width: 50px;">STATUS</th> @endif
            </tr>
        </thead>
        <tbody>
            @php $globalSr = 1; @endphp
            @forelse($bookings as $b)
                @php
                    $persons = $b->persons;
                    $totalPax = $persons->count();
                    $rowspan = max(1, $totalPax);

                    $depDate = $b->departure_date ? \Carbon\Carbon::parse($b->departure_date) : null;
                    $arrDate = $b->arrival_date ? \Carbon\Carbon::parse($b->arrival_date) : null;
                    $durationDays = ($depDate && $arrDate) ? $depDate->diffInDays($arrDate) : null;

                    $statusText = 'Pending';
                    if ($depDate) {
                        if ($depDate->isToday()) $statusText = 'Departing Today';
                        elseif ($depDate->isTomorrow()) $statusText = 'Tomorrow';
                        elseif ($arrDate && $arrDate->isPast()) $statusText = 'Returned';
                        elseif ($depDate->isPast() && ($arrDate ? $arrDate->isFuture() || $arrDate->isToday() : true)) $statusText = 'Travelling';
                        elseif ($depDate->isFuture()) $statusText = 'Upcoming';
                    }

                    $depSector = trim(($b->departure_from ?? '') . ($b->departure_to ? ' → ' . $b->departure_to : ''));
                    $retSector = trim(($b->arrival_from ?? '') . ($b->arrival_to ? ' → ' . $b->arrival_to : ''));
                    $pkgTitle = $b->package?->package_title ?? ($b->package?->name ?? '—');
                @endphp

                @if($totalPax === 0)
                    {{-- Row when no booking persons are registered yet --}}
                    <tr class="booking-divider-top">
                        @if($showCol('sr')) <td style="text-align: center;">{{ $globalSr++ }}</td> @endif
                        @if($showCol('hajj-id')) <td style="text-align: center;">—</td> @endif
                        @if($showCol('hb')) <td style="text-align: center;">—</td> @endif
                        @if($showCol('pilgrim'))
                            <td style="text-align: left; padding-left: 4px; font-weight: bold;">
                                {{ $b->client?->name ?? ($b->company?->company_name ?? 'Direct Passenger') }}
                            </td>
                        @endif
                        @if($showCol('gender')) <td style="text-align: center;">—</td> @endif
                        @if($showCol('passport')) <td style="text-align: center; font-weight: bold;">{{ $b->passport_number ?: '—' }}</td> @endif
                        @if($showCol('voucher')) <td style="text-align: center; font-weight: bold;">{{ $b->voucher_number ?? ('UB' . str_pad($b->id, 6, '0', STR_PAD_LEFT)) }}</td> @endif
                        
                        {{-- Departure --}}
                        @if($showCol('dep-airline')) <td style="text-align: center;">{{ $b->departure_airline ?: '—' }}</td> @endif
                        @if($showCol('dep-flight')) <td style="text-align: center; font-weight: bold;">{{ $b->departure_flight ?: '—' }}</td> @endif
                        @if($showCol('dep-sector')) <td style="text-align: center;">{{ $depSector ?: '—' }}</td> @endif
                        @if($showCol('dep-date')) <td style="text-align: center;">{{ $depDate ? $depDate->format('d/m/Y') : '—' }}</td> @endif
                        @if($showCol('dep-pnr')) <td style="text-align: center; font-weight: bold;">{{ $b->departure_pnr ?: '—' }}</td> @endif

                        {{-- Return --}}
                        @if($showCol('ret-airline')) <td style="text-align: center;">{{ $b->arrival_airline ?: '—' }}</td> @endif
                        @if($showCol('ret-flight')) <td style="text-align: center; font-weight: bold;">{{ $b->arrival_flight ?: '—' }}</td> @endif
                        @if($showCol('ret-sector')) <td style="text-align: center;">{{ $retSector ?: '—' }}</td> @endif
                        @if($showCol('ret-date')) <td style="text-align: center;">{{ $arrDate ? $arrDate->format('d/m/Y') : '—' }}</td> @endif
                        @if($showCol('ret-pnr')) <td style="text-align: center; font-weight: bold;">{{ $b->arrival_pnr ?: '—' }}</td> @endif

                        {{-- Duration, Package, Charges, Status --}}
                        @if($showCol('duration')) <td style="text-align: center;">{{ $durationDays !== null ? $durationDays . ' D' : '—' }}</td> @endif
                        @if($showCol('package')) <td style="text-align: left; padding-left: 4px;">{{ $pkgTitle }}</td> @endif
                        @if($showCol('charges')) <td style="text-align: center;">{{ $b->flight_charges > 0 ? number_format($b->flight_charges) : 'Included' }}</td> @endif
                        @if($showCol('status')) <td style="text-align: center;">{{ $statusText }}</td> @endif
                    </tr>
                @else
                    @foreach($persons as $oIdx => $person)
                        <tr class="{{ $oIdx === 0 ? 'booking-divider-top' : 'booking-sub-row' }}">
                            @if($showCol('sr')) <td style="text-align: center; font-weight: bold;">{{ $globalSr++ }}</td> @endif
                            @if($showCol('hajj-id')) <td style="text-align: center; font-weight: bold;">{{ !empty($person->hajj_id) ? $person->hajj_id : '—' }}</td> @endif
                            @if($showCol('hb')) <td style="text-align: center; font-weight: bold;">{{ !empty($person->hb_number) ? $person->hb_number : '—' }}</td> @endif
                            @if($showCol('pilgrim'))
                                <td style="text-align: left; padding-left: 4px; font-weight: bold; text-transform: uppercase;">
                                    {{ $person->full_name }}
                                    @if(!empty($person->is_family_head))
                                        <span style="font-size: 7px; color: #166534; font-weight: bold;">[Head]</span>
                                    @endif
                                </td>
                            @endif
                            @if($showCol('gender'))
                                <td class="{{ strtoupper($person->gender ?? '') === 'FEMALE' ? 'gender-female' : 'gender-male' }}">
                                    {{ strtoupper($person->gender ?? '—') }}
                                </td>
                            @endif
                            @if($showCol('passport')) <td style="text-align: center; font-weight: bold;">{{ $person->passport_number ?: '—' }}</td> @endif

                            @if($oIdx === 0)
                                @if($showCol('voucher'))
                                    <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">
                                        {{ $b->voucher_number ?? ('UB' . str_pad($b->id, 6, '0', STR_PAD_LEFT)) }}
                                    </td>
                                @endif

                                {{-- Departure --}}
                                @if($showCol('dep-airline')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $b->departure_airline ?: '—' }}</td> @endif
                                @if($showCol('dep-flight')) <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $b->departure_flight ?: '—' }}</td> @endif
                                @if($showCol('dep-sector')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $depSector ?: '—' }}</td> @endif
                                @if($showCol('dep-date')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $depDate ? $depDate->format('d/m/Y') : '—' }}</td> @endif
                                @if($showCol('dep-pnr')) <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $b->departure_pnr ?: '—' }}</td> @endif

                                {{-- Return --}}
                                @if($showCol('ret-airline')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $b->arrival_airline ?: '—' }}</td> @endif
                                @if($showCol('ret-flight')) <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $b->arrival_flight ?: '—' }}</td> @endif
                                @if($showCol('ret-sector')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $retSector ?: '—' }}</td> @endif
                                @if($showCol('ret-date')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $arrDate ? $arrDate->format('d/m/Y') : '—' }}</td> @endif
                                @if($showCol('ret-pnr')) <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold;">{{ $b->arrival_pnr ?: '—' }}</td> @endif

                                {{-- Duration, Package, Charges, Status --}}
                                @if($showCol('duration')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $durationDays !== null ? $durationDays . ' D' : '—' }}</td> @endif
                                @if($showCol('package')) <td rowspan="{{ $rowspan }}" style="text-align: left; padding-left: 4px;">{{ $pkgTitle }}</td> @endif
                                @if($showCol('charges')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $b->flight_charges > 0 ? number_format($b->flight_charges) : 'Included' }}</td> @endif
                                @if($showCol('status')) <td rowspan="{{ $rowspan }}" style="text-align: center;">{{ $statusText }}</td> @endif
                            @endif
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="{{ $colCount }}" style="text-align: center; padding: 20px; color: #777;">No Booking Flight Records Found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
