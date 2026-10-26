@php
    $showCol = function($key) use ($visibleColumns) {
        if ($visibleColumns === null) {
            return in_array($key, ['sr', 'hajj-id', 'hb', 'passport', 'name', 'room-typ', 'room-no', 'gender', 'picture']);
        }
        return in_array($key, $visibleColumns);
    };
    $allKeys = ['sr', 'hajj-id', 'hb', 'passport', 'passport-doi', 'passport-exp', 'cnic', 'name', 'given-name', 'surname', 'father-name', 'dob', 'phone', 'booking-no', 'client', 'room-typ', 'room-no', 'gender', 'picture'];
    $colCount = 0;
    foreach ($allKeys as $k) {
        if ($showCol($k)) $colCount++;
    }
    if ($colCount === 0) $colCount = 9;

    // Use Landscape if more than 7 columns selected, or if passed from controller
    $isLandscape = (isset($paperOrientation) && $paperOrientation === 'landscape') || ($colCount > 7);
    $pageOrientation = $isLandscape ? 'landscape' : 'portrait';

    // Auto-scale fonts, padding & photo sizes based on number of columns
    if ($colCount >= 15) {
        $tableFontSize = '6.5px';
        $thFontSize = '7px';
        $cellPadding = '2px 1.5px';
        $photoW = 24;
        $photoH = 28;
        $pageMargin = '5mm 3mm 5mm 3mm';
    } elseif ($colCount >= 12) {
        $tableFontSize = '7px';
        $thFontSize = '7.5px';
        $cellPadding = '2.5px 2px';
        $photoW = 28;
        $photoH = 32;
        $pageMargin = '6mm 4mm 6mm 4mm';
    } elseif ($colCount >= 9) {
        $tableFontSize = '8px';
        $thFontSize = '8.5px';
        $cellPadding = '3px 2px';
        $photoW = 32;
        $photoH = 36;
        $pageMargin = '7mm 5mm 7mm 5mm';
    } else {
        $tableFontSize = '9px';
        $thFontSize = '9.5px';
        $cellPadding = '4px 3px';
        $photoW = 36;
        $photoH = 42;
        $pageMargin = '8mm 6mm 8mm 6mm';
    }

    $colWeights = [
        'sr'            => 3,
        'hajj-id'       => 6,
        'hb'            => 5,
        'passport'      => 7.5,
        'passport-doi'  => 5.5,
        'passport-exp'  => 5.5,
        'cnic'          => 7.5,
        'name'          => 12,
        'given-name'    => 7,
        'surname'       => 6,
        'father-name'   => 8,
        'dob'           => 6,
        'phone'         => 7,
        'booking-no'    => 5.5,
        'client'        => 7,
        'room-typ'      => 8,
        'room-no'       => 6,
        'gender'        => 5,
        'picture'       => 4.5,
    ];

    $totalWeight = 0;
    foreach ($allKeys as $k) {
        if ($showCol($k)) {
            $totalWeight += $colWeights[$k] ?? 6;
        }
    }
    if ($totalWeight === 0) $totalWeight = 100;

    $getColWidth = function($k) use ($colWeights, $totalWeight) {
        $w = $colWeights[$k] ?? 6;
        return round(($w / $totalWeight) * 100, 2) . '%';
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Rooming List & Manifest Report</title>
    <style>
        @page {
            margin: {{ $pageMargin }};
            size: A4 {{ $pageOrientation }};
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: {{ $tableFontSize }};
            color: #111;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header-box {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 2px solid #000;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 2px 0;
            color: #000;
        }
        .header-subtitle {
            font-size: 10.5px;
            font-weight: bold;
            color: #444;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .header-meta {
            font-size: 8.5px;
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
            padding: 3px 6px;
            font-size: {{ $tableFontSize }};
            text-align: center;
        }
        .kpi-table td strong {
            font-size: {{ $thFontSize }};
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
            table-layout: fixed;
            page-break-inside: auto;
        }
        .manifest-table th {
            background-color: #f1f5f9;
            color: #000;
            font-size: {{ $thFontSize }};
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: {{ $cellPadding }};
            border: 1px solid #000;
            text-transform: uppercase;
            word-wrap: break-word;
            overflow: hidden;
        }
        .manifest-table td {
            padding: {{ $cellPadding }};
            vertical-align: middle;
            font-size: {{ $tableFontSize }};
            border: 0.5px solid #000;
            word-wrap: break-word;
            overflow: hidden;
        }
        .room-divider-top td {
            border-top: 1.5px solid #000 !important;
        }
        .room-sub-row td {
            border-top: 0.5px solid #888 !important;
        }
        .room-typ-cell {
            font-weight: bold;
            font-size: {{ $colCount >= 11 ? '7.5px' : '9px' }};
            text-align: center;
            text-transform: uppercase;
            border-left: 1.5px solid #000 !important;
            border-right: 1px solid #000 !important;
            background-color: #fff;
        }
        .room-no-cell {
            font-weight: 900;
            font-size: {{ $colCount >= 11 ? '10px' : '12px' }};
            color: #c00000;
            text-align: center;
            border-left: 1px solid #000 !important;
            border-right: 1.5px solid #000 !important;
            background-color: #fff;
            letter-spacing: 0.5px;
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
        .haji-photo {
            width: {{ $photoW }}px;
            height: {{ $photoH }}px;
            object-fit: cover;
            border-radius: 2px;
            border: 0.5px solid #666;
            display: block;
            margin: 0 auto;
        }
        .photo-placeholder {
            width: {{ $photoW }}px;
            height: {{ $photoH }}px;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            display: block;
            margin: 0 auto;
            line-height: {{ $photoH }}px;
            text-align: center;
            color: #999;
            font-size: 7.5px;
        }
        tr {
            page-break-inside: avoid;
        }
        .page-num:after {
            content: counter(page);
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            font-size: 7.5px;
            color: #777;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 2px;
        }
    </style>
</head>
<body>

    <div class="header-box">
        <div class="header-title">Pirwani Hajj Group</div>
        <div class="header-subtitle">Official Rooming List & Manifest Report</div>
        <div class="header-meta">
            Generated on: <strong>{{ date('d M Y, h:i A') }}</strong>
            @if(!empty($hotelFilter)) | Hotel: <strong>{{ is_array($hotelFilter) ? implode(', ', $hotelFilter) : $hotelFilter }}</strong> @endif
            @if(!empty($locationFilter) && $locationFilter !== 'all') | Location: <strong>{{ strtoupper($locationFilter) }}</strong> @endif
            @if(!empty($roomTypeFilter) && $roomTypeFilter !== 'all') | Room Type: <strong>{{ strtoupper($roomTypeFilter) }}</strong> @endif
            @if(!empty($packageFilter)) | Package: <strong>{{ is_array($packageFilter) ? count($packageFilter) . ' selected' : $packageFilter }}</strong> @endif
            @if(!empty($fromDate) || !empty($toDate)) | Dates: <strong>{{ $fromDate ?: 'Start' }} to {{ $toDate ?: 'End' }}</strong> @endif
        </div>
    </div>

    {{-- KPI Summary Bar --}}
    <table class="kpi-table">
        <tr>
            <td>Total Allocated Rooms: <strong>{{ number_format($totalRoomsInUse ?? ($stats['total_rooms'] ?? 0)) }}</strong></td>
            <td>Total Pilgrims (Pax): <strong>{{ number_format($totalBedsOccupied ?? ($stats['allocated_beds'] ?? 0)) }}</strong></td>
            <td>Total Bed Capacity: <strong>{{ number_format($totalBedsCapacity ?? ($stats['total_beds'] ?? 0)) }}</strong></td>
            <td>Free / Available Beds: <strong>{{ number_format($totalBedsAvailable ?? ($stats['available_beds'] ?? 0)) }}</strong></td>
        </tr>
    </table>

    <table class="manifest-table">
        <thead>
            <tr>
                @if($showCol('sr')) <th style="width: {{ $getColWidth('sr') }};">SR</th> @endif
                @if($showCol('hajj-id')) <th style="width: {{ $getColWidth('hajj-id') }};">HAJJ ID</th> @endif
                @if($showCol('hb')) <th style="width: {{ $getColWidth('hb') }};">HB</th> @endif
                @if($showCol('passport')) <th style="width: {{ $getColWidth('passport') }};">PASSPORT</th> @endif
                @if($showCol('passport-doi')) <th style="width: {{ $getColWidth('passport-doi') }};">DOI</th> @endif
                @if($showCol('passport-exp')) <th style="width: {{ $getColWidth('passport-exp') }};">EXPIRY</th> @endif
                @if($showCol('cnic')) <th style="width: {{ $getColWidth('cnic') }};">CNIC</th> @endif
                @if($showCol('name')) <th style="width: {{ $getColWidth('name') }}; text-align: left; padding-left: 3px;">FULL NAME</th> @endif
                @if($showCol('given-name')) <th style="width: {{ $getColWidth('given-name') }}; text-align: left; padding-left: 3px;">GIVEN NAME</th> @endif
                @if($showCol('surname')) <th style="width: {{ $getColWidth('surname') }}; text-align: left; padding-left: 3px;">SURNAME</th> @endif
                @if($showCol('father-name')) <th style="width: {{ $getColWidth('father-name') }}; text-align: left; padding-left: 3px;">FATHER NAME</th> @endif
                @if($showCol('dob')) <th style="width: {{ $getColWidth('dob') }};">DOB</th> @endif
                @if($showCol('phone')) <th style="width: {{ $getColWidth('phone') }};">PHONE</th> @endif
                @if($showCol('booking-no')) <th style="width: {{ $getColWidth('booking-no') }};">BOOKING #</th> @endif
                @if($showCol('client')) <th style="width: {{ $getColWidth('client') }};">CLIENT</th> @endif
                @if($showCol('room-typ')) <th style="width: {{ $getColWidth('room-typ') }};">ROOM TYP</th> @endif
                @if($showCol('room-no')) <th style="width: {{ $getColWidth('room-no') }};">ROOM NO</th> @endif
                @if($showCol('gender')) <th style="width: {{ $getColWidth('gender') }};">Gender</th> @endif
                @if($showCol('picture')) <th style="width: {{ $getColWidth('picture') }};">Picture</th> @endif
            </tr>
        </thead>
        <tbody>
            @php $globalSr = 1; @endphp
            @forelse(($groupedRooms ?? $rooms ?? []) as $room)
                @php
                    $occupants = $room['occupants'];
                    $totalOccupants = count($occupants);
                    
                    $rawNum = trim($room['room_number'] ?? '');
                    $isUnassigned = ($rawNum === 'PENDING' || $rawNum === 'UNASSIGNED' || empty($rawNum) || empty($room['is_assigned']));
                    $roomDisplayNo = $isUnassigned ? '—' : (str_starts_with(strtoupper($rawNum), 'R') ? strtoupper($rawNum) : ('R' . $rawNum));
                    
                    $isAllocated = !empty($room['is_assigned']);
                    $rawRoomType = trim($room['room_type'] ?? '');
                    if ($isAllocated && !empty($rawRoomType)) {
                        $roomTypeDisplay = strtoupper($rawRoomType);
                        if (in_array(strtolower($roomTypeDisplay), ['sharing', '6', 'six-bed', '6-bed'])) {
                            $roomTypeDisplay = 'SIX';
                        }
                    } else {
                        $roomTypeDisplay = '—';
                    }

                    $loc = strtolower(trim($room['location'] ?? 'makkah'));
                    $locLabel = match($loc) {
                        'makkah'  => 'MAKKAH',
                        'azizia'  => 'AZIZIA',
                        'mina'    => 'MINA',
                        'arafat'  => 'ARAFAT',
                        'madinah' => 'MADINAH',
                        'unassigned' => 'UNASSIGNED',
                        default   => strtoupper($loc),
                    };
                @endphp

                @if($totalOccupants === 0)
                    <tr class="room-divider-top">
                        @if($showCol('sr')) <td style="text-align: center; color: #777;">{{ $globalSr++ }}</td> @endif
                        @if($showCol('hajj-id')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('hb')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('passport')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('passport-doi')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('passport-exp')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('cnic')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('name')) <td style="color: #777; font-style: italic;">(Empty Room - {{ $room['total_capacity'] }} Beds Available)</td> @endif
                        @if($showCol('given-name')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('surname')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('father-name')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('dob')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('phone')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('booking-no')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('client')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('room-typ'))
                            <td class="room-typ-cell">
                                <div style="font-size: 7.5px; color: #555;">{{ $locLabel }}</div>
                                <div>{{ $roomTypeDisplay }}</div>
                            </td>
                        @endif
                        @if($showCol('room-no')) <td class="room-no-cell" style="{{ $isUnassigned ? 'color: #555; font-weight: normal;' : '' }}">{{ $roomDisplayNo }}</td> @endif
                        @if($showCol('gender')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('picture')) <td style="text-align: center; color: #777;">—</td> @endif
                    </tr>
                @else
                    @foreach($occupants as $oIdx => $occ)
                        @php
                            $photoSrc = null;
                            if (!empty($occ['photo'])) {
                                $cleanPath = ltrim($occ['photo'], '/\\');
                                $fullPath = public_path($cleanPath);
                                if (file_exists($fullPath)) {
                                    $photoSrc = $fullPath;
                                }
                            }
                            $isFemale = strtoupper($occ['gender'] ?? 'MALE') === 'FEMALE';
                        @endphp
                        <tr class="{{ $oIdx === 0 ? 'room-divider-top' : 'room-sub-row' }}">
                            @if($showCol('sr')) <td style="text-align: center; font-weight: bold;">{{ $globalSr++ }}</td> @endif
                            @if($showCol('hajj-id')) <td style="text-align: center; font-weight: bold; text-transform: uppercase;">{{ !empty($occ['hajj_id']) ? $occ['hajj_id'] : '—' }}</td> @endif
                            @if($showCol('hb')) <td style="text-align: center; font-weight: bold; text-transform: uppercase;">{{ !empty($occ['hb_number']) ? $occ['hb_number'] : '—' }}</td> @endif
                            @if($showCol('passport'))
                                <td style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                    <div>{{ $occ['passport'] }}</div>
                                    @if(!$showCol('passport-doi') && !empty($occ['date_of_issue']))
                                        <div style="font-size: 6px; color: #555; font-weight: normal;">DOI: {{ date('d/m/Y', strtotime($occ['date_of_issue'])) }}</div>
                                    @endif
                                    @if(!$showCol('passport-exp') && !empty($occ['passport_expiry_date']))
                                        <div style="font-size: 6px; color: #b91c1c; font-weight: normal;">EXP: {{ date('d/m/Y', strtotime($occ['passport_expiry_date'])) }}</div>
                                    @endif
                                </td>
                            @endif
                            @if($showCol('passport-doi')) <td style="text-align: center;">{{ !empty($occ['date_of_issue']) ? date('d/m/Y', strtotime($occ['date_of_issue'])) : '—' }}</td> @endif
                            @if($showCol('passport-exp')) <td style="text-align: center;">{{ !empty($occ['passport_expiry_date']) ? date('d/m/Y', strtotime($occ['passport_expiry_date'])) : '—' }}</td> @endif
                            @if($showCol('cnic')) <td style="text-align: center;">{{ $occ['cnic'] ?? '—' }}</td> @endif
                            @if($showCol('name'))
                                <td style="text-align: left; padding-left: 3px; font-weight: bold; text-transform: uppercase; color: #000;">
                                    <div>{{ $occ['name'] }}</div>
                                </td>
                            @endif
                            @if($showCol('given-name')) <td style="text-align: left; padding-left: 3px;">{{ $occ['given_name'] ?? '—' }}</td> @endif
                            @if($showCol('surname')) <td style="text-align: left; padding-left: 3px;">{{ $occ['surname'] ?? '—' }}</td> @endif
                            @if($showCol('father-name')) <td style="text-align: left; padding-left: 3px;">{{ $occ['father_name'] ?? '—' }}</td> @endif
                            @if($showCol('dob')) <td style="text-align: center;">{{ !empty($occ['dob']) ? date('d/m/Y', strtotime($occ['dob'])) : '—' }}</td> @endif
                            @if($showCol('phone')) <td style="text-align: center;">{{ $occ['phone'] ?? '—' }}</td> @endif
                            @if($showCol('booking-no')) <td style="text-align: center;">{{ $occ['booking_id'] ?? '—' }}</td> @endif
                            @if($showCol('client')) <td style="text-align: left; padding-left: 3px;">{{ $occ['client_name'] ?? '—' }}</td> @endif
                            @if($showCol('room-typ'))
                                <td class="room-typ-cell" style="text-align: center;">
                                    <div style="font-weight: bold;">{{ $roomTypeDisplay }}</div>
                                </td>
                            @endif
                            @if($showCol('room-no'))
                                <td class="room-no-cell" style="{{ $isUnassigned ? 'color: #555; font-weight: normal;' : '' }}">
                                    <div>{{ $roomDisplayNo }}</div>
                                    @if(!$isUnassigned && $oIdx === 0 && $room['total_capacity'] > 0)
                                        <div style="font-size: 6px; color: #666; font-weight: normal;">{{ $room['occupied_beds'] }}/{{ $room['total_capacity'] }}</div>
                                    @endif
                                </td>
                            @endif
                            @if($showCol('gender'))
                                <td class="{{ $isFemale ? 'gender-female' : 'gender-male' }}" style="text-transform: uppercase;">
                                    {{ strtoupper($occ['gender']) }}
                                </td>
                            @endif
                            @if($showCol('picture'))
                                <td style="text-align: center; padding: 1px;">
                                    @if($photoSrc)
                                        <img src="{{ $photoSrc }}" class="haji-photo" alt="">
                                    @else
                                        <div class="photo-placeholder">—</div>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="{{ $colCount }}" style="text-align: center; padding: 25px; color: #777;">
                        No room allocation records found matching the criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
