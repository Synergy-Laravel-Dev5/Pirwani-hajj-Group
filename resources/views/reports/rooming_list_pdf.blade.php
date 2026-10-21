<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Rooming List & Manifest Report</title>
    <style>
        @page {
            margin: 12mm 8mm 12mm 8mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #111;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header-box {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 3px 0;
            color: #000;
        }
        .header-subtitle {
            font-size: 11px;
            font-weight: bold;
            color: #444;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header-meta {
            font-size: 9px;
            color: #555;
        }
        .kpi-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .kpi-table td {
            background-color: #f8fafc;
            border: 1px solid #ccc;
            padding: 4px 8px;
            font-size: 9px;
            text-align: center;
        }
        .kpi-table td strong {
            font-size: 11px;
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
            font-size: 9.5px;
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
            font-size: 9px;
            border: 1px solid #000;
        }
        .room-divider-top td {
            border-top: 2px solid #000 !important;
        }
        .room-sub-row td {
            border-top: 0.5px solid #888 !important;
        }
        .room-typ-cell {
            font-weight: bold;
            font-size: 9.5px;
            text-align: center;
            text-transform: uppercase;
            border-left: 1.5px solid #000 !important;
            border-right: 1px solid #000 !important;
            background-color: #fff;
        }
        .room-no-cell {
            font-weight: 900;
            font-size: 13px;
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
            width: 38px;
            height: 44px;
            object-fit: cover;
            border-radius: 2px;
            border: 0.5px solid #666;
            display: block;
            margin: 0 auto;
        }
        .photo-placeholder {
            width: 36px;
            height: 42px;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            display: block;
            margin: 0 auto;
            line-height: 42px;
            text-align: center;
            color: #999;
            font-size: 8px;
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
            font-size: 8px;
            color: #777;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 3px;
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

    {{-- Official Manifest Table matching User PDF --}}
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
    @endphp
    <table class="manifest-table">
        <thead>
            <tr>
                @if($showCol('sr')) <th style="width: 25px;">SR</th> @endif
                @if($showCol('hajj-id')) <th style="width: 65px;">HAJJ ID</th> @endif
                @if($showCol('hb')) <th style="width: 55px;">HB</th> @endif
                @if($showCol('passport')) <th style="width: 75px;">PASSPORT</th> @endif
                @if($showCol('passport-doi')) <th style="width: 55px;">DOI</th> @endif
                @if($showCol('passport-exp')) <th style="width: 55px;">EXPIRY</th> @endif
                @if($showCol('cnic')) <th style="width: 70px;">CNIC</th> @endif
                @if($showCol('name')) <th style="text-align: left; padding-left: 5px;">FULL NAME</th> @endif
                @if($showCol('given-name')) <th style="text-align: left; padding-left: 5px; width: 65px;">GIVEN NAME</th> @endif
                @if($showCol('surname')) <th style="text-align: left; padding-left: 5px; width: 65px;">SURNAME</th> @endif
                @if($showCol('father-name')) <th style="text-align: left; padding-left: 5px; width: 75px;">FATHER NAME</th> @endif
                @if($showCol('dob')) <th style="width: 55px;">DOB</th> @endif
                @if($showCol('phone')) <th style="width: 65px;">PHONE</th> @endif
                @if($showCol('booking-no')) <th style="width: 55px;">BOOKING #</th> @endif
                @if($showCol('client')) <th style="width: 70px;">CLIENT</th> @endif
                @if($showCol('room-typ')) <th style="width: 65px;">ROOM TYP</th> @endif
                @if($showCol('room-no')) <th style="width: 65px;">ROOM NO</th> @endif
                @if($showCol('gender')) <th style="width: 50px;">Gender</th> @endif
                @if($showCol('picture')) <th style="width: 45px;">Haji Picture</th> @endif
            </tr>
        </thead>
        <tbody>
            @php $globalSr = 1; @endphp
            @forelse(($groupedRooms ?? $rooms ?? []) as $room)
                @php
                    $occupants = $room['occupants'];
                    $totalOccupants = count($occupants);
                    
                    $rawNum = trim($room['room_number']);
                    $isUnassigned = ($rawNum === 'PENDING' || $rawNum === 'UNASSIGNED' || empty($rawNum));
                    $roomDisplayNo = $isUnassigned ? 'PENDING' : (str_starts_with(strtoupper($rawNum), 'R') ? strtoupper($rawNum) : ('R' . $rawNum));
                    
                    $roomTypeDisplay = strtoupper($room['room_type']);
                    if ($isUnassigned) {
                        $roomTypeDisplay = 'PENDING ALLOCATION';
                    } elseif (in_array(strtolower($roomTypeDisplay), ['sharing', '6', 'six-bed', '6-bed'])) {
                        $roomTypeDisplay = 'SIX';
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
                        @if($showCol('room-no')) <td class="room-no-cell">{{ $roomDisplayNo }}</td> @endif
                        @if($showCol('gender')) <td style="text-align: center; color: #777;">—</td> @endif
                        @if($showCol('picture')) <td style="text-align: center; color: #777;">—</td> @endif
                    </tr>
                @else
                    @foreach($occupants as $oIdx => $occ)
                        @php
                            $photoBase64 = null;
                            if (!empty($occ['photo']) && file_exists(public_path($occ['photo']))) {
                                $ext = pathinfo(public_path($occ['photo']), PATHINFO_EXTENSION);
                                $imgData = @file_get_contents(public_path($occ['photo']));
                                if ($imgData) {
                                    $photoBase64 = 'data:image/' . ($ext ?: 'jpeg') . ';base64,' . base64_encode($imgData);
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
                                    {{ $occ['passport'] }}
                                    @if(!empty($occ['date_of_issue']))
                                        <div style="font-size: 6.5px; color: #555; font-weight: normal; margin-top: 1px;">DOI: {{ date('d/m/Y', strtotime($occ['date_of_issue'])) }}</div>
                                    @endif
                                    @if(!empty($occ['passport_expiry_date']))
                                        <div style="font-size: 6.5px; color: #b91c1c; font-weight: normal; margin-top: 1px;">EXP: {{ date('d/m/Y', strtotime($occ['passport_expiry_date'])) }}</div>
                                    @endif
                                </td>
                            @endif
                            @if($showCol('passport-doi')) <td style="text-align: center;">{{ !empty($occ['date_of_issue']) ? date('d/m/Y', strtotime($occ['date_of_issue'])) : '—' }}</td> @endif
                            @if($showCol('passport-exp')) <td style="text-align: center;">{{ !empty($occ['passport_expiry_date']) ? date('d/m/Y', strtotime($occ['passport_expiry_date'])) : '—' }}</td> @endif
                            @if($showCol('cnic')) <td style="text-align: center;">{{ $occ['cnic'] ?? '—' }}</td> @endif
                            @if($showCol('name'))
                                <td style="text-align: left; padding-left: 5px; font-weight: bold; text-transform: uppercase; color: #000;">
                                    <div>{{ $occ['name'] }}</div>
                                </td>
                            @endif
                            @if($showCol('given-name')) <td style="text-align: left; padding-left: 5px;">{{ $occ['given_name'] ?? '—' }}</td> @endif
                            @if($showCol('surname')) <td style="text-align: left; padding-left: 5px;">{{ $occ['surname'] ?? '—' }}</td> @endif
                            @if($showCol('father-name')) <td style="text-align: left; padding-left: 5px;">{{ $occ['father_name'] ?? '—' }}</td> @endif
                            @if($showCol('dob')) <td style="text-align: center;">{{ !empty($occ['dob']) ? date('d/m/Y', strtotime($occ['dob'])) : '—' }}</td> @endif
                            @if($showCol('phone')) <td style="text-align: center;">{{ $occ['phone'] ?? '—' }}</td> @endif
                            @if($showCol('booking-no')) <td style="text-align: center;">{{ $occ['booking_id'] ?? '—' }}</td> @endif
                            @if($showCol('client')) <td style="text-align: left; padding-left: 5px;">{{ $occ['client_name'] ?? '—' }}</td> @endif
                            @if($showCol('room-typ'))
                                <td class="room-typ-cell">
                                    <div style="font-size: 7.5px; color: #444; font-weight: bold;">{{ $locLabel }}</div>
                                    <div style="font-size: 9.5px; font-weight: bold;">{{ $roomTypeDisplay }}</div>
                                    @if($oIdx === 0 && $room['total_capacity'] > 0)
                                        <div style="font-size: 7.5px; color: #666;">({{ $room['total_capacity'] }} Beds)</div>
                                    @endif
                                </td>
                            @endif
                            @if($showCol('room-no'))
                                <td class="room-no-cell">
                                    <div>{{ $roomDisplayNo }}</div>
                                    @if($oIdx === 0 && $room['total_capacity'] > 0)
                                        <div style="font-size: 7.5px; color: #666; font-weight: normal;">{{ $room['occupied_beds'] }}/{{ $room['total_capacity'] }}</div>
                                    @endif
                                </td>
                            @endif
                            @if($showCol('gender'))
                                <td class="{{ $isFemale ? 'gender-female' : 'gender-male' }}" style="font-size: 8.5px; text-transform: uppercase;">
                                    {{ strtoupper($occ['gender']) }}
                                </td>
                            @endif
                            @if($showCol('picture'))
                                <td style="text-align: center; padding: 2px;">
                                    @if($photoBase64)
                                        <img src="{{ $photoBase64 }}" class="haji-photo" alt="Photo">
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
