<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Booking Person Report & Pilgrim Manifest</title>
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
            vertical-align: middle;
            font-size: 8.5px;
            border: 1px solid #000;
        }
        .haji-photo {
            width: 30px;
            height: 35px;
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
        <div class="header-subtitle">Official Booking Person Report & Pilgrim Manifest • Hajj 1448 / 2027</div>
        <div class="header-meta">
            Generated: <strong>{{ date('d M Y, h:i A') }}</strong>
            @if(!empty($maktab) && $maktab !== 'all')
                &nbsp;|&nbsp; Maktab Filter: <strong>{{ strtoupper(str_replace('_', ' ', $maktab)) }}</strong>
            @endif
            @if(!empty($gender) && $gender !== 'all')
                &nbsp;|&nbsp; Gender: <strong>{{ strtoupper($gender) }}</strong>
            @endif
        </div>
    </div>

    <table class="kpi-table">
        <tr>
            <td>Total Pilgrims<br><strong>{{ number_format($totalPersons) }}</strong></td>
            <td>👨 Male Pilgrims<br><strong>{{ number_format($maleCount) }}</strong></td>
            <td>👩 Female Pilgrims<br><strong>{{ number_format($femaleCount) }}</strong></td>
            <td>Passports Recorded<br><strong>{{ number_format($passportCount) }}</strong></td>
            <td>CNICs Identified<br><strong>{{ number_format($cnicCount) }}</strong></td>
            <td>Photos Uploaded<br><strong>{{ number_format($photoCount) }}</strong></td>
        </tr>
    </table>

    @php
        $showCol = function($key) use ($visibleColumns) {
            if (!isset($visibleColumns) || empty($visibleColumns)) {
                return in_array($key, ['sr', 'picture', 'hajj-id', 'hb', 'name', 'gender', 'passport', 'cnic', 'phone', 'voucher', 'package', 'maktab']);
            }
            return in_array($key, $visibleColumns);
        };
        $allKeys = ['sr', 'picture', 'hajj-id', 'hb', 'name', 'gender', 'passport', 'cnic', 'phone', 'voucher', 'package', 'maktab', 'client'];
        $colCount = 0;
        foreach ($allKeys as $k) {
            if ($showCol($k)) $colCount++;
        }
        if ($colCount === 0) $colCount = 12;
    @endphp
    <table class="manifest-table">
        <thead>
            <tr>
                @if($showCol('sr')) <th style="width: 25px;">#</th> @endif
                @if($showCol('picture')) <th style="width: 35px;">PHOTO</th> @endif
                @if($showCol('hajj-id')) <th style="width: 70px;">HAJJ ID</th> @endif
                @if($showCol('hb')) <th style="width: 60px;">HB #</th> @endif
                @if($showCol('name')) <th style="width: 170px; text-align: left; padding-left: 6px;">FULL NAME</th> @endif
                @if($showCol('gender')) <th style="width: 60px;">GENDER</th> @endif
                @if($showCol('passport')) <th style="width: 80px;">PASSPORT</th> @endif
                @if($showCol('cnic')) <th style="width: 95px;">CNIC</th> @endif
                @if($showCol('phone')) <th style="width: 85px;">PHONE</th> @endif
                @if($showCol('voucher')) <th style="width: 85px;">VOUCHER</th> @endif
                @if($showCol('package')) <th style="width: 140px; text-align: left; padding-left: 6px;">PACKAGE</th> @endif
                @if($showCol('maktab')) <th style="width: 95px;">MAKTAB / CAMP</th> @endif
            </tr>
        </thead>
        <tbody>
            @forelse($persons as $idx => $p)
                @php
                    $booking = $p->booking;
                    $campName = $booking ? ($booking->camp ?: ($booking->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : 'Standard Camp')) : '—';
                    $pkgTitle = $booking && $booking->package ? ($booking->package->package_title ?: $booking->package->name) : '—';
                    $voucherNo = $booking ? ($booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT))) : '—';
                    $pGender = strtoupper($p->gender ?? 'MALE');
                    $isFemale = ($pGender === 'FEMALE');

                    $photoSrc = null;
                    if (!empty($p->photo)) {
                        $clean = ltrim($p->photo, '/\\');
                        $full = public_path($clean);
                        if (file_exists($full)) {
                            $photoSrc = $full;
                        }
                    }
                @endphp
                <tr>
                    @if($showCol('sr')) <td class="center fw-bold">{{ $idx + 1 }}</td> @endif
                    @if($showCol('picture'))
                        <td class="center">
                            @if($photoSrc)
                                <img src="{{ $photoSrc }}" class="haji-photo" alt="">
                            @else
                                —
                            @endif
                        </td>
                    @endif
                    @if($showCol('hajj-id')) <td class="center fw-bold">{{ $p->hajj_id ?: '—' }}</td> @endif
                    @if($showCol('hb')) <td class="center">{{ $p->hb_number ?: '—' }}</td> @endif
                    @if($showCol('name'))
                        <td style="padding-left: 6px;">
                            <strong>{{ $p->full_name }}</strong>
                            @if(!empty($p->given_name) || !empty($p->surname))
                                <br><span style="font-size: 7.5px; color: #444;">{{ trim(($p->given_name ?? '') . ' ' . ($p->surname ?? '')) }}</span>
                            @endif
                            @if(!empty($p->father_name))
                                <br><span style="font-size: 7.5px; color: #555;">S/O, D/O, W/O: {{ $p->father_name }}</span>
                            @endif
                        </td>
                    @endif
                    @if($showCol('gender'))
                        <td class="center {{ $isFemale ? 'gender-female' : 'gender-male' }}">
                            {{ $pGender }}
                        </td>
                    @endif
                    @if($showCol('passport'))
                        <td class="center fw-bold">
                            {{ $p->passport_number ?: '—' }}
                            @php
                                $rawDoi = $p->date_of_issue ?: $p->passport_issue_date;
                            @endphp
                            @if(!empty($rawDoi))
                                <br><span style="font-size: 7px; color: #555; font-weight: normal;">DOI: {{ date('d/m/Y', strtotime($rawDoi)) }}</span>
                            @endif
                            @if(!empty($p->passport_expiry_date))
                                <br><span style="font-size: 7px; color: #b91c1c; font-weight: normal;">EXP: {{ date('d/m/Y', strtotime($p->passport_expiry_date)) }}</span>
                            @endif
                        </td>
                    @endif
                    @if($showCol('cnic')) <td class="center">{{ $p->cnic ?: '—' }}</td> @endif
                    @if($showCol('phone')) <td class="center">{{ $p->phone ?: '—' }}</td> @endif
                    @if($showCol('voucher')) <td class="center">{{ $voucherNo }}</td> @endif
                    @if($showCol('package')) <td style="padding-left: 6px;">{{ $pkgTitle }}</td> @endif
                    @if($showCol('maktab')) <td class="center fw-bold">{{ $campName }}</td> @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $colCount }}" class="center" style="padding: 20px;">No Pilgrims Found matching the criteria</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
