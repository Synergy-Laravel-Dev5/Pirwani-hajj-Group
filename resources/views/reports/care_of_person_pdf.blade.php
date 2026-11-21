<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Care of Persons Report & Booking Manifest</title>
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
            margin-bottom: 6px;
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
            color: #333;
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
        .careof-title-bar {
            background-color: #071527;
            color: #F3DC9B;
            font-size: 10px;
            font-weight: bold;
            padding: 5px 8px;
            margin-top: 10px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .booking-info-bar {
            background-color: #e2e8f0;
            color: #0f172a;
            font-size: 8.5px;
            font-weight: bold;
            padding: 3px 6px;
            border: 1px solid #cbd5e1;
            border-bottom: none;
            margin-top: 6px;
        }
        .manifest-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            page-break-inside: auto;
            table-layout: fixed;
        }
        .manifest-table th {
            background-color: #f1f5f9;
            color: #000;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 4px 2px;
            border: 1px solid #000;
            text-transform: uppercase;
            overflow: hidden;
        }
        .manifest-table td {
            padding: 3px 2px;
            vertical-align: middle;
            font-size: 8px;
            border: 0.75px solid #000;
            word-wrap: break-word;
            word-break: break-word;
            overflow: hidden;
        }
        .haji-photo {
            width: 26px;
            height: 30px;
            object-fit: cover;
            border-radius: 2px;
            border: 1px solid #777;
            display: block;
            margin: 0 auto;
        }
        .gender-male {
            color: #1e3a8a;
            font-weight: bold;
        }
        .gender-female {
            color: #9d174d;
            font-weight: bold;
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

    <div class="header-box">
        <div class="header-title">PIRWANI HAJJ GROUP (PVT) LTD.</div>
        <div class="header-subtitle">Official Care of Persons Report & Booking Manifest</div>
        <div class="header-meta">
            Generated: <strong>{{ date('d M Y, h:i A') }}</strong>
            @if(!empty($selectedCareOf) && $selectedCareOf !== 'all')
                &nbsp;|&nbsp; Care Of: <strong>{{ strtoupper($selectedCareOf) }}</strong>
            @endif
            @if(!empty($gender) && $gender !== 'all')
                &nbsp;|&nbsp; Gender: <strong>{{ strtoupper($gender) }}</strong>
            @endif
        </div>
    </div>

    <table class="kpi-table">
        <tr>
            <td>Care Of Groups<br><strong>{{ number_format($totalCareOfGroups) }}</strong></td>
            <td>Total Bookings<br><strong>{{ number_format($totalBookings) }}</strong></td>
            <td>Total Pilgrims<br><strong>{{ number_format($totalPersons) }}</strong></td>
            <td>Male / Female<br><strong>{{ $maleCount }} M / {{ $femaleCount }} F</strong></td>
            <td>Total Received<br><strong>PKR {{ number_format($totalReceived) }}</strong></td>
            <td>Total Balance<br><strong>PKR {{ number_format($totalBalance) }}</strong></td>
        </tr>
    </table>

    @php
        $showCol = function($key) use ($visibleColumns) {
            if ($visibleColumns === null) {
                return in_array($key, ['sr', 'picture', 'hajj-id', 'hb', 'name', 'gender', 'passport', 'cnic', 'phone', 'booking-ref', 'package', 'room', 'hotel', 'camp']);
            }
            return in_array($key, $visibleColumns);
        };

        // Proportional column weights so columns dynamically scale to 100% with no empty space gap
        $colWeights = [
            'sr'          => 4,
            'picture'     => 6,
            'hajj-id'     => 9,
            'hb'          => 9,
            'name'        => 24,
            'gender'      => 8,
            'passport'    => 12,
            'doi'         => 9,
            'expiry'      => 9,
            'cnic'        => 12,
            'phone'       => 11,
            'booking-ref' => 13,
            'package'     => 13,
            'room'        => 9,
            'hotel'       => 11,
            'camp'        => 9,
        ];

        $totalActiveWeight = 0;
        $activeColCount = 0;
        foreach ($colWeights as $colK => $w) {
            if ($showCol($colK)) {
                $totalActiveWeight += $w;
                $activeColCount++;
            }
        }

        $getColWidth = function($key) use ($colWeights, $totalActiveWeight) {
            if ($totalActiveWeight <= 0 || !isset($colWeights[$key])) return 'auto';
            return round(($colWeights[$key] / $totalActiveWeight) * 100, 1) . '%';
        };
    @endphp

    @foreach($groupedByCareOf as $careOfName => $bookings)
        @php
            $allGroupPersons = $bookings->flatMap(function($b) {
                return $b->persons->map(function($p) use ($b) {
                    $p->parent_booking = $b;
                    return $p;
                });
            });
            $groupPax = $allGroupPersons->count();
            $groupAmt = $bookings->sum('total_amount');
            $groupRecv = $bookings->sum('total_received');
            $groupBal = $bookings->sum('balance');
        @endphp

        <div class="careof-title-bar">
            CARE OF: {{ strtoupper($careOfName) }} 
            &nbsp;|&nbsp; Bookings: {{ $bookings->count() }} 
            &nbsp;|&nbsp; Pilgrims: {{ $groupPax }}
            &nbsp;|&nbsp; Amount: PKR {{ number_format($groupAmt) }}
            &nbsp;|&nbsp; Recv: PKR {{ number_format($groupRecv) }}
            &nbsp;|&nbsp; Bal: PKR {{ number_format($groupBal) }}
        </div>

        <table class="manifest-table">
            <thead>
                <tr>
                    @if($showCol('sr')) <th style="width: {{ $getColWidth('sr') }};">#</th> @endif
                    @if($showCol('picture')) <th style="width: {{ $getColWidth('picture') }};">PHOTO</th> @endif
                    @if($showCol('hajj-id')) <th style="width: {{ $getColWidth('hajj-id') }};">HAJJ ID</th> @endif
                    @if($showCol('hb')) <th style="width: {{ $getColWidth('hb') }};">HB #</th> @endif
                    @if($showCol('name')) <th style="width: {{ $getColWidth('name') }}; text-align: left; padding-left: 4px;">PILGRIM NAME</th> @endif
                    @if($showCol('gender')) <th style="width: {{ $getColWidth('gender') }};">GENDER</th> @endif
                    @if($showCol('passport')) <th style="width: {{ $getColWidth('passport') }};">PASSPORT</th> @endif
                    @if($showCol('doi')) <th style="width: {{ $getColWidth('doi') }};">DOI</th> @endif
                    @if($showCol('expiry')) <th style="width: {{ $getColWidth('expiry') }};">EXPIRY</th> @endif
                    @if($showCol('cnic')) <th style="width: {{ $getColWidth('cnic') }};">CNIC</th> @endif
                    @if($showCol('phone')) <th style="width: {{ $getColWidth('phone') }};">PHONE</th> @endif
                    @if($showCol('booking-ref')) <th style="width: {{ $getColWidth('booking-ref') }};">BOOKING REF</th> @endif
                    @if($showCol('package')) <th style="width: {{ $getColWidth('package') }};">PACKAGE</th> @endif
                    @if($showCol('room')) <th style="width: {{ $getColWidth('room') }};">ROOM</th> @endif
                    @if($showCol('hotel')) <th style="width: {{ $getColWidth('hotel') }};">HOTEL</th> @endif
                    @if($showCol('camp')) <th style="width: {{ $getColWidth('camp') }};">CAMP</th> @endif
                </tr>
            </thead>
            <tbody>
                @forelse($allGroupPersons as $person)
                    @php
                        $booking = $person->parent_booking;
                        $gender = strtoupper($person->gender ?? 'MALE');
                        $genderClass = ($gender === 'FEMALE') ? 'gender-female' : 'gender-male';
                        $hb = \App\Models\BookingPerson::formatHbNumber($person->hb_number);
                        $doi = $person->date_of_issue ?: ($person->passport_issue_date ?: '—');
                        $roomInfo = ($person->room_number ? 'Room ' . $person->room_number : '') . ($person->room_type ? ' (' . $person->room_type . ')' : '');

                        $voucherNo = $booking ? ($booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT))) : '—';
                        $pkg = $booking ? ($booking->package->package_title ?? $booking->package->name ?? $booking->package_name ?? ucfirst($booking->package_type ?? 'N/A')) : '—';
                    @endphp
                    <tr>
                        @if($showCol('sr')) <td class="center">{{ $loop->iteration }}</td> @endif
                        @if($showCol('picture'))
                            <td class="center">
                                @php
                                    $photoSrc = $person->thumb_photo ?? null;
                                    if (!$photoSrc && !empty($person->photo)) {
                                        $clean = ltrim($person->photo, '/\\');
                                        if (file_exists(public_path($clean))) {
                                            $photoSrc = public_path($clean);
                                        } elseif (file_exists($person->photo)) {
                                            $photoSrc = $person->photo;
                                        }
                                    }
                                @endphp
                                @if($photoSrc && file_exists($photoSrc))
                                    <img src="{{ $photoSrc }}" class="haji-photo" alt="P">
                                @else
                                    —
                                @endif
                            </td>
                        @endif
                        @if($showCol('hajj-id')) <td class="center fw-bold">{{ $person->hajj_id ?? '—' }}</td> @endif
                        @if($showCol('hb')) <td class="center">{{ $hb ?: '—' }}</td> @endif
                        @if($showCol('name'))
                            <td>
                                <strong>{{ $person->full_name ?: ($person->given_name . ' ' . $person->surname) }}</strong>
                                @if($person->father_name)
                                    @php
                                        $pGenderLower = strtolower($person->gender ?? '');
                                        $relationLabel = ($pGenderLower === 'female') ? 'D/O, W/O' : 'S/O';
                                    @endphp
                                    <br><span style="font-size:7px; color:#555;">{{ $relationLabel }}: {{ $person->father_name }}</span>
                                @endif
                            </td>
                        @endif
                        @if($showCol('gender')) <td class="center {{ $genderClass }}">{{ $gender }}</td> @endif
                        @if($showCol('passport')) <td class="center fw-bold">{{ $person->passport_number ?? '—' }}</td> @endif
                        @if($showCol('doi')) <td class="center">{{ $doi }}</td> @endif
                        @if($showCol('expiry')) <td class="center">{{ $person->passport_expiry_date ?? '—' }}</td> @endif
                        @if($showCol('cnic')) <td class="center">{{ $person->cnic ?? '—' }}</td> @endif
                        @if($showCol('phone')) <td class="center">{{ $person->phone ?: ($booking->phone ?? '—') }}</td> @endif
                        @if($showCol('booking-ref')) <td class="center fw-bold">{{ $voucherNo }}</td> @endif
                        @if($showCol('package')) <td class="center">{{ $pkg }}</td> @endif
                        @if($showCol('room')) <td class="center">{{ $roomInfo ?: '—' }}</td> @endif
                        @if($showCol('hotel')) <td class="center">{{ $person->hotel_name ?? '—' }}</td> @endif
                        @if($showCol('camp')) <td class="center">{{ $booking->camp ?? '—' }}</td> @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ max(1, $activeColCount) }}" class="center" style="padding: 5px;">No pilgrims found for this Care of person.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

</body>
</html>
