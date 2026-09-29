<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $package->package_title ?? ($package->name ?? 'Executive Platinum') }} - {{ $package->days ?? '14' }} Days
        Package</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Poppins:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- html2pdf.js, html2canvas & jsPDF Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        :root {
            --navy: #0e1726;
            --gold: #d9a441;
            --gold-dark: #c8922e;
            --peach-bg: #f8dbb7;
            --grey-row: #eaeaea;
            --border-color: #7f7f7f;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #d4d4d4;
            color: #131738;
            padding-top: 10px;
        }

        /* Top Action Bar */
        .action-bar {
            max-width: 1000px;
            margin: 0 auto 10px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 5px;
        }

        .btn-back {
            background-color: var(--navy);
            color: #fff;
            font-weight: 600;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #050714;
            color: #fff;
        }

        .btn-download {
            background-color: var(--gold);
            color: var(--navy);
            font-weight: 700;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-download:hover {
            background-color: var(--gold-dark);
            color: #fff;
        }

        /* Sheet Document Container */
        .sheet {
            max-width: 1000px;
            width: 1000px;
            min-height: 1414px;
            margin: 0 auto 30px auto;
            background: #fff;
            padding: 10px 14px 20px 14px;
            box-shadow: 0 0 15px rgba(0, 0, 0, .15);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sheet-content {
            flex: 1 0 auto;
        }

        /* Top Header Banner */
        .header-banner {
            display: flex;
            align-items: stretch;
            margin-bottom: 8px;
        }

        .header-left {
            background: var(--navy);
            color: var(--gold);
            padding: 10px 20px;
            flex: 2.2;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .header-left h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 900;
            font-size: 1.8rem;
            margin: 0;
            line-height: 1.1;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header-left .subtitle {
            color: #ffffff;
            font-weight: 700;
            font-size: .8rem;
            letter-spacing: .5px;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .header-right {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold) 50%, #b9812a);
            color: var(--navy);
            flex: 1.8;
            display: flex;
            align-items: center;
            padding: 8px 15px;
            box-sizing: border-box;
        }

        .pkg-code-box {
            text-align: center;
            font-weight: 900;
            line-height: 1.1;
            padding-right: 15px;
        }

        .pkg-code-box .lbl {
            font-size: 0.6rem;
            letter-spacing: 0.5px;
            color: var(--navy);
            text-transform: uppercase;
            font-weight: 800;
        }

        .pkg-code-box .code {
            font-size: 0.95rem;
            color: var(--navy);
            font-weight: 900;
        }

        .vertical-divider {
            width: 1px;
            background-color: rgba(14, 23, 38, 0.3);
            height: 70%;
            margin: 0 15px;
        }

        .pkg-details-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .pkg-details-box .days {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--navy);
            line-height: 1;
            margin-bottom: 2px;
            text-align: center;
            white-space: nowrap;
        }

        .pkg-details-box .city-indicator {
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: var(--navy);
            text-transform: uppercase;
            margin-bottom: 3px;
            border-bottom: 1.5px solid var(--navy);
            padding-bottom: 1px;
            width: 90%;
            text-align: center;
        }

        .tags-container-inline {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 3px;
        }

        .tags-container-inline .tag-badge {
            background: transparent;
            color: var(--navy);
            font-size: 0.52rem;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 2px;
            border: 1px solid var(--navy);
            text-transform: uppercase;
            line-height: 1;
        }

        /* Custom Image Matching Table Styling */
        .pkg-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            font-size: .65rem;
        }

        .pkg-table th,
        .pkg-table td {
            border: 1px solid var(--border-color);
            text-align: center;
            vertical-align: middle;
            padding: 1.5px 2px;
        }

        /* Top Left Header Columns */
        .pkg-table thead th.th-left {
            background: var(--navy) !important;
            color: #ffffff !important;
            font-weight: 800;
            font-size: .8rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            border-right: 3px solid #fff;
        }

        .pkg-table thead th.th-left:last-of-type {
            border-right: 1px solid var(--border-color);
        }

        /* Package Headers (A & B) */
        .pkg-table thead th.th-pkg-a,
        .pkg-table thead th.th-pkg-b {
            background: var(--navy) !important;
            color: #ffffff !important;
            font-weight: 800;
            font-size: .85rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            padding: 4px 0;
        }

        .pkg-table thead th.th-pkg-a {
            border-right: 3px solid #fff;
        }

        /* Sub-Header Accommodation Row */
        .pkg-table thead tr.sub-header th {
            background: var(--peach-bg) !important;
            color: #2b2b2b !important;
            font-weight: 900;
            font-size: .85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 3px 0;
        }

        /* Table Body Row Colors */
        .pkg-table tbody tr:nth-child(odd) {
            background: var(--grey-row);
        }

        .pkg-table tbody tr:nth-child(even) {
            background: #ffffff;
        }

        /* Mina Highlight Styling */
        .pkg-table tbody td.mashair-cell {
            background: var(--peach-bg) !important;
        }

        .stars {
            color: #ffb400;
            font-size: .68rem;
            margin-left: 2px;
        }

        /* Middle Note Strip */
        .note-strip {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 6px 0;
        }

        .ticket-note {
            font-weight: 800;
            font-size: 0.78rem;
            color: #131738;
            white-space: nowrap;
        }

        .note-box {
            background: var(--navy);
            color: #fff;
            font-size: .65rem;
            padding: 4px 8px;
            border-radius: 2px;
            flex: 1;
            text-align: center;
            font-weight: 600;
        }

        .note-box b {
            background: #fff;
            color: var(--navy);
            padding: 0 4px;
            margin-right: 4px;
            border-radius: 2px;
        }

        /* ROOM TYPE PRICING TABLE */
        .room-table-container {
            border: 1px solid var(--border-color);
            margin-bottom: 4px;
        }

        .room-table {
            border-collapse: collapse;
            width: 100%;
            font-size: .73rem;
            margin-bottom: 0;
        }

        .room-table td {
            border: 1px solid var(--border-color);
            padding: 4px 8px;
            vertical-align: middle;
        }

        .room-table .room-type-sidebar {
            background: var(--navy);
            color: #ffffff;
            font-weight: 800;
            font-size: .85rem;
            text-align: center;
            width: 17%;
            letter-spacing: .5px;
        }

        .room-table .room-label {
            width: 17%;
            text-align: center;
            font-weight: 600;
            background: #fdfdfd;
            color: #333;
        }

        .room-table .room-price-a,
        .room-table .room-price-b {
            width: 33%;
            text-align: center;
            font-weight: 700;
            background: var(--grey-row);
            color: #000;
        }

        .price-disclaimer {
            text-align: right;
            font-size: .65rem;
            font-weight: 700;
            margin: 2px 0 6px 0;
            color: #222;
            font-style: italic;
        }

        /* Bottom Details & Notes Section */
        .icon-box {
            border: 2px solid var(--gold);
            border-radius: 6px;
            padding: 6px;
            height: auto !important;
            align-self: flex-start;
        }

        .icon-item {
            text-align: center;
            margin-bottom: 6px;
        }

        .icon-circle {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--navy);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2px;
            font-size: .8rem;
        }

        .icon-item .label {
            font-weight: 800;
            font-size: .62rem;
            line-height: 1.1;
        }

        .icon-item .desc {
            font-size: .52rem;
            color: #333;
            line-height: 1.1;
        }

        .zone-badge {
            background: var(--navy);
            color: var(--gold);
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            font-size: .65rem;
        }

        .zone-badge small {
            display: block;
            color: #fff;
            font-size: .48rem;
            font-weight: 600;
        }

        .notes-title {
            font-weight: 900;
            font-size: .82rem;
            margin-bottom: 2px;
        }

        .notes-list {
            font-size: .62rem;
            line-height: 1.25;
            padding-left: 15px;
        }

        .notes-list li {
            margin-bottom: 3px;
        }

        .notes-content {
            font-size: .56rem;
            line-height: 1.15;
        }

        .notes-content p {
            margin-bottom: 2px;
        }

        .taxi-strip {
            background: var(--navy);
            color: #fff;
            font-size: .58rem;
            text-align: center;
            padding: 3px;
            font-weight: 600;
            margin-bottom: 3px;
            border-radius: 2px;
        }

        .sign-box {
            font-weight: 700;
            font-size: .8rem;
            text-align: left;
            margin-top: auto;
            padding-top: 15px;
            padding-bottom: 5px;
            width: 100%;
        }

        /* Services Sheet 2 Styling */
        .services-header-banner {
            background: #3e3e3e;
            color: #ffffff;
            padding: 9px 15px;
            margin-bottom: 15px;
            text-align: center;
            border-radius: 2px;
        }

        .services-header-banner h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #ffffff;
        }

        .services-sheet-body {
            font-size: 0.72rem;
            color: #1a1a1a;
            line-height: 1.45;
        }

        .services-sheet-body ul {
            padding-left: 18px;
            margin-bottom: 10px;
        }

        .services-sheet-body li {
            margin-bottom: 3.5px;
        }

        .services-sheet-body p {
            margin-bottom: 8px;
        }

        .services-sheet-body ol {
            padding-left: 18px;
            margin-bottom: 10px;
        }

        .services-sheet-body ol li {
            margin-bottom: 3.5px;
        }

        .services-sheet-body strong {
            color: #000;
        }
    </style>
</head>

<body>

    <!-- Top Action Bar -->
    <div class="action-bar">
        <a href="{{ route('package.index') }}" class="btn-back">⬅ Back to List</a>
        <button class="btn-download" onclick="downloadPackagePDF()">📥 Download PDF</button>
    </div>

    <div class="sheet mb-4" id="packageSheet1">
        @php
            $pkgAccVal = function ($acc, $key) {
                if (is_array($acc)) {
                    return $acc[$key] ?? null;
                }
                return $acc->$key ?? null;
            };

            $hasAzizia = false;
            if (!empty($package->accommodations)) {
                foreach ($package->accommodations as $acc) {
                    $placeVal = $pkgAccVal($acc, 'place') ?? '';
                    if (str_contains(strtolower($placeVal), 'aziz')) {
                        $hasAzizia = true;
                        break;
                    }
                }
            }
            $shiftingText = $hasAzizia ? 'SHIFTING' : 'NON SHIFTING';
            $aziziaText = $hasAzizia ? 'WITH AZIZIYA' : 'NON AZIZIYA';
            $firstCity = ($package->medina_arrival ?? 'before_hajj') == 'before_hajj' ? 'MEDINAH' : 'MAKKAH';
            $firstCityFirst = $firstCity . ' FIRST';
        @endphp

        <div class="sheet-content">
            <!-- Header Banner -->
            <div class="header-banner">
            <div class="header-left">
                <h1>{{ $package->package_title ?? 'EXECUTIVE PLATINUM' }}</h1>
                <div class="subtitle"> {{ $package->name ?? '-' }} </div>
            </div>
            <div class="header-right">
                <div class="pkg-code-box">
                    <div class="lbl">PKG CODE</div>
                    <div class="code">{{ $package->code }}</div>
                </div>
                <div class="vertical-divider"></div>
                <div class="pkg-details-box">
                    @php
                        $displayName = (string) ($package->days ?? '14');
                        if (
                            !str_contains(strtolower($displayName), 'days') &&
                            !str_contains(strtolower($displayName), 'day')
                        ) {
                            $displayName .= ' Days Package';
                        }
                    @endphp
                    <div class="days">{{ $displayName }}</div>
                    <div class="city-indicator">— {{ $firstCity }} —</div>
                    <div class="tags-container-inline">
                        <span class="tag-badge">{{ $shiftingText }}</span>
                        <span class="tag-badge">{{ $aziziaText }}</span>
                    </div>
                </div>
            </div>
        </div>

        @php

            $pkgNested = function ($acc, $group, $key) use ($pkgAccVal) {
                $flatKey = "{$group}_{$key}";
                if (is_array($acc)) {
                    if (isset($acc[$flatKey]) && $acc[$flatKey] !== null) {
                        return $acc[$flatKey];
                    }
                    $g = $acc[$group] ?? null;
                    return is_array($g) ? ($g[$key] ?? null) : null;
                }
                if (is_object($acc)) {
                    if (isset($acc->$flatKey) && $acc->$flatKey !== null && $acc->$flatKey !== '') {
                        return $acc->$flatKey;
                    }
                    $g = $pkgAccVal($acc, $group);
                    if ($g) {
                        return is_array($g) ? ($g[$key] ?? null) : ($g->$key ?? null);
                    }
                }
                return null;
            };

            $islamicMonths = [
                1 => 'Muharram',
                2 => 'Safar',
                3 => 'Rabi-ul-Awwal',
                4 => 'Rabi-ul-Thani',
                5 => 'Jumada-al-Awwal',
                6 => 'Jumada-al-Thani',
                7 => 'Rajab',
                8 => 'Shaban',
                9 => 'Ramadan',
                10 => 'Shawwal',
                11 => 'Zil Qadah',
                12 => 'Zil Hajj',
            ];
            $getHijriDate = function ($date) use ($islamicMonths) {
                if (!$date) {
                    return ['day' => 0, 'month' => 0, 'formatted' => '-'];
                }
                if (class_exists('\IntlDateFormatter')) {
                    try {
                        $fmtD = new \IntlDateFormatter(
                            'en_US@calendar=islamic-umalqura',
                            \IntlDateFormatter::FULL,
                            \IntlDateFormatter::NONE,
                            'UTC',
                            \IntlDateFormatter::TRADITIONAL,
                            'd',
                        );
                        $fmtM = new \IntlDateFormatter(
                            'en_US@calendar=islamic-umalqura',
                            \IntlDateFormatter::FULL,
                            \IntlDateFormatter::NONE,
                            'UTC',
                            \IntlDateFormatter::TRADITIONAL,
                            'M',
                        );
                        $targetDate = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
                        $d = (int) $fmtD->format($targetDate);
                        $m = (int) $fmtM->format($targetDate);
                        $mName = $islamicMonths[$m] ?? 'Zil Hajj';
                        return [
                            'day' => $d,
                            'month' => $m,
                            'formatted' => sprintf('%02d %s', $d, $mName),
                        ];
                    } catch (\Throwable $e) {
                    }
                }
                return ['day' => 0, 'month' => 0, 'formatted' => '-'];
            };

            $itineraryList = [];
            $dayCounter = 1;

            if (!empty($package->accommodations) && count($package->accommodations) > 0) {
                foreach ($package->accommodations as $acc) {
                    $checkInRaw = $pkgAccVal($acc, 'check_in');
                    $checkOutRaw = $pkgAccVal($acc, 'check_out');
                    $checkIn = $checkInRaw ? \Carbon\Carbon::parse($checkInRaw) : null;
                    $checkOut = $checkOutRaw ? \Carbon\Carbon::parse($checkOutRaw) : null;
                    $hotelA = $pkgNested($acc, 'package_a', 'hotel') ?? '-';
                    $hotelB = $pkgNested($acc, 'package_b', 'hotel') ?? '-';
                    $foodA = $pkgNested($acc, 'package_a', 'food_package');
                    $foodB = $pkgNested($acc, 'package_b', 'food_package');

                    $sameForBoth = (bool) $pkgAccVal($acc, 'same_for_both');
                    if (trim($hotelA) === trim($hotelB) || empty($hotelB) || trim($hotelB) === '-') {
                        $sameForBoth = true;
                    }

                    $starsA = (int) ($pkgNested($acc, 'package_a', 'saudi_star_rating') ?: 0);
                    $starsB = (int) ($pkgNested($acc, 'package_b', 'saudi_star_rating') ?: 0);

                    if ($checkIn && $checkOut) {
                        $period = \Carbon\CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
                        foreach ($period as $date) {
                            $hijri = $getHijriDate($date);
                            $hDay = $hijri['day'];
                            $hMonth = $hijri['month'];
                            $isMashair = ($hMonth == 12 && $hDay >= 8 && $hDay <= 11);

                            if ($isMashair) {
                                $servicesText =
                                    $hDay == 9
                                        ? 'Arafat Air Conditioned Marquee (Exclusive Services)'
                                        : 'Zone 1 near to Jamarat A Category (Exclusive Services)';

                                $hotelAFull = $servicesText . ' / ' . $hotelA;
                                $hotelBFull = $sameForBoth ? $hotelAFull : ($servicesText . ' / ' . $hotelB);

                                $itineraryList[] = [
                                    'day' => sprintf('%02d', $dayCounter++),
                                    'date' => $date->format('d M'),
                                    'hijri' => $hijri['formatted'],
                                    'city' => $hDay == 8 ? 'To Mina' : 'Mina',
                                    'is_mashair' => true,
                                    'same_for_both' => $sameForBoth,
                                    'hotel_a' => $hotelAFull,
                                    'stars_a' => '',
                                    'hotel_b' => $hotelBFull,
                                    'stars_b' => '',
                                    'food_a' => null,
                                    'food_b' => null,
                                    'azizia_date' => $pkgAccVal($acc, 'azizia_date')
                                        ? \Carbon\Carbon::parse($pkgAccVal($acc, 'azizia_date'))->format('d M')
                                        : null,
                                ];
                            } else {
                                $placeVal = $pkgAccVal($acc, 'place') ?? 'Makkah';
                                $itineraryList[] = [
                                    'day' => sprintf('%02d', $dayCounter++),
                                    'date' => $date->format('d M'),
                                    'hijri' => $hijri['formatted'],
                                    'city' => $placeVal,
                                    'is_mashair' => false,
                                    'same_for_both' => $sameForBoth,
                                    'hotel_a' => $hotelA,
                                    'stars_a' => str_repeat('★', $starsA),
                                    'hotel_b' => $hotelB,
                                    'stars_b' => str_repeat('★', $starsB),
                                    'food_a' => $foodA,
                                    'food_b' => $foodB,
                                    'azizia_date' => $pkgAccVal($acc, 'azizia_date')
                                        ? \Carbon\Carbon::parse($pkgAccVal($acc, 'azizia_date'))->format('d M')
                                        : null,
                                ];
                            }
                        }
                    }
                }
            }

            $finalCheckOutDate = null;
            if (!empty($package->accommodations) && count($package->accommodations) > 0) {
                $lastAcc = $package->accommodations->last();
                $checkOutRaw = $pkgAccVal($lastAcc, 'check_out');
                if ($checkOutRaw) {
                    $finalCheckOutDate = \Carbon\Carbon::parse($checkOutRaw);
                }
            }

            if ($finalCheckOutDate) {
                $hijri = $getHijriDate($finalCheckOutDate);

                $itineraryList[] = [
                    'day' => sprintf('%02d', $dayCounter++),
                    'date' => $finalCheckOutDate->format('d M'),
                    'hijri' => $hijri['formatted'],
                    'city' => '',
                    'is_mashair' => true,
                    'same_for_both' => true,
                    'hotel_a' => 'DEPARTURE TO AIRPORT',
                    'stars_a' => '',
                    'hotel_b' => 'DEPARTURE TO AIRPORT',
                    'stars_b' => '',
                    'food_a' => null,
                    'food_b' => null,
                    'azizia_date' => null,
                ];
            }

            $prevCity = null;
            foreach ($itineraryList as &$item) {
                $city = trim($item['city']);
                if (empty($city)) {
                    continue;
                }
                $cleanedCity = preg_replace('/^to\s+/i', '', $city);
                if (strtolower($cleanedCity) !== strtolower($prevCity)) {
                    $item['city'] = 'To ' . $cleanedCity;
                } else {
                    $item['city'] = $cleanedCity;
                }
                $prevCity = $cleanedCity;
            }
            unset($item);
        @endphp

        <!-- Main Accommodation Table (Image Style Matching) -->
        <div class="table-responsive">
            <table class="pkg-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="th-left" style="width: 5%;">DAY</th>
                        <th rowspan="2" class="th-left" style="width: 9%;">DATE<br>(AD)</th>
                        <th rowspan="2" class="th-left" style="width: 10%;">DATE<br>(Hijri)</th>
                        <th rowspan="2" class="th-left" style="width: 10%;">CITY</th>
                        <th class="th-pkg-a" style="width: 33%;">PACKAGE (A)</th>
                        <th class="th-pkg-b" style="width: 33%;">PACKAGE (B)</th>
                    </tr>
                    <tr class="sub-header">
                        <th colspan="2">ACCOMMODATION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itineraryList as $item)
                        <tr>
                            <td><b>{{ $item['day'] }}</b></td>
                            <td>{{ $item['date'] }}</td>
                            <td>{{ $item['hijri'] }}</td>
                            <td>{{ $item['city'] }}</td>
                            @if ($item['same_for_both'])
                                <td colspan="2"
                                    class="fw-bold text-center {{ !empty($item['is_mashair']) ? 'mashair-cell' : '' }}">
                                    {{ $item['hotel_a'] }}
                                    @if (!empty($item['stars_a']))
                                        <span class="stars">{{ $item['stars_a'] }}</span>
                                    @endif
                                    {{-- @if (!empty($item['azizia_date']))
                                        <div style="font-size: 0.72rem; color: #d9534f; font-weight: 600; margin-top: 1px;">Azizia: {{ $item['azizia_date'] }}</div>
                                    @endif --}}
                                </td>
                            @else
                                <td class="text-center {{ !empty($item['is_mashair']) ? 'mashair-cell' : '' }}">
                                    {{ $item['hotel_a'] }}
                                    @if (!empty($item['stars_a']))
                                        <span class="stars">{{ $item['stars_a'] }}</span>
                                    @endif
                                    {{-- @if (!empty($item['azizia_date']))
                                        <div style="font-size: 0.72rem; color: #d9534f; font-weight: 600; margin-top: 1px;">Azizia: {{ $item['azizia_date'] }}</div>
                                    @endif --}}
                                </td>
                                <td class="text-center {{ !empty($item['is_mashair']) ? 'mashair-cell' : '' }}">
                                    {{ $item['hotel_b'] }}
                                    @if (!empty($item['stars_b']))
                                        <span class="stars">{{ $item['stars_b'] }}</span>
                                    @endif
                                    {{-- @if (!empty($item['azizia_date']))
                                        <div style="font-size: 0.72rem; color: #d9534f; font-weight: 600; margin-top: 1px;">Azizia: {{ $item['azizia_date'] }}</div>
                                    @endif --}}
                                </td>
                            @endif
                        </tr>
                    @empty
                        @for ($i = 1; $i <= ($package->days ?? 14); $i++)
                            <tr>
                                <td><b>{{ sprintf('%02d', $i) }}</b></td>
                                <td>-</td>
                                <td>-</td>
                                <td>Makkah / Medinah</td>
                                <td class="text-center">Hotel Information Pending</td>
                                <td class="text-center">Hotel Information Pending</td>
                            </tr>
                        @endfor
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Note Strip -->
        <div class="note-strip">
            <div class="ticket-note">{{ $package->ticket_note ?? 'Ticket & Qurbani not Included' }}</div>
            <div class="note-box">
                <b>NOTE:</b>
                {{ $package->retention_note ?? 'MAKKAH HOTEL ROOMS WILL BE RETAINED FROM 08 ZIL HAJJ TO 12 ZIL HAJJ.' }}
            </div>
        </div>

        @php
            $makkahA = is_string($package->makkah_a ?? null)
                ? json_decode($package->makkah_a, true)
                : $package->makkah_a ?? [];
            $makkahB = is_string($package->makkah_b ?? null)
                ? json_decode($package->makkah_b, true)
                : $package->makkah_b ?? [];
            $madinahA = is_string($package->madinah_a ?? null)
                ? json_decode($package->madinah_a, true)
                : $package->madinah_a ?? [];
            $madinahB = is_string($package->madinah_b ?? null)
                ? json_decode($package->madinah_b, true)
                : $package->madinah_b ?? [];

            $quadAVal = !empty($makkahA['quad'])
                ? $makkahA['quad']
                : (!empty($madinahA['quad'])
                    ? $madinahA['quad']
                    : null);
            $tripleAVal = !empty($makkahA['triple'])
                ? $makkahA['triple']
                : (!empty($madinahA['triple'])
                    ? $madinahA['triple']
                    : null);
            $doubleAVal = !empty($makkahA['double'])
                ? $makkahA['double']
                : (!empty($madinahA['double'])
                    ? $madinahA['double']
                    : null);

            $quadBVal = !empty($makkahB['quad'])
                ? $makkahB['quad']
                : (!empty($madinahB['quad'])
                    ? $madinahB['quad']
                    : null);
            $tripleBVal = !empty($makkahB['triple'])
                ? $makkahB['triple']
                : (!empty($madinahB['triple'])
                    ? $madinahB['triple']
                    : null);
            $doubleBVal = !empty($makkahB['double'])
                ? $makkahB['double']
                : (!empty($madinahB['double'])
                    ? $madinahB['double']
                    : null);

            $quadA = !empty($quadAVal) ? 'SAR ' . number_format((float) $quadAVal) . '/-' : 'NA';
            $tripleA = !empty($tripleAVal) ? 'SAR ' . number_format((float) $tripleAVal) . '/-' : 'NA';
            $doubleA = !empty($doubleAVal) ? 'SAR ' . number_format((float) $doubleAVal) . '/-' : 'NA';

            $quadB = !empty($quadBVal) ? 'SAR ' . number_format((float) $quadBVal) . '/-' : 'NA';
            $tripleB = !empty($tripleBVal) ? 'SAR ' . number_format((float) $tripleBVal) . '/-' : 'NA';
            $doubleB = !empty($doubleBVal) ? 'SAR ' . number_format((float) $doubleBVal) . '/-' : 'NA';
        @endphp

        <!-- ROOM TYPE PRICING TABLE -->
        <div class="room-table-container">
            <table class="room-table">
                <tbody>
                    <tr>
                        <td rowspan="3" class="room-type-sidebar">ROOM TYPE</td>
                        <td class="room-label">QUAD Per Person</td>
                        <td class="room-price-a">{{ $quadA }}</td>
                        <td class="room-price-b">{{ $quadB }}</td>
                    </tr>
                    <tr>
                        <td class="room-label">TRIPLE Per Person</td>
                        <td class="room-price-a">{{ $tripleA }}</td>
                        <td class="room-price-b">{{ $tripleB }}</td>
                    </tr>
                    <tr>
                        <td class="room-label">DOUBLE Per Person</td>
                        <td class="room-price-a">{{ $doubleA }}</td>
                        <td class="room-price-b">{{ $doubleB }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="price-disclaimer">{!! $package->price_disclaimer ?? '"Book Early, Prices and Packages Subject to Change."' !!}</div>

        <!-- Bottom Icons and Terms/Notes -->
        @php
            $feat = $package->feature_icons ?? [];

            $item1Zone = $feat['item_1']['badge_zone'] ?? ('ZONE ' . ($package->category_zone ?? '1'));
            $item1Maktab = $feat['item_1']['badge_maktab'] ?? ('MAKTAB ' . ($package->maktab ?? 'A-CATEGORY'));
            $item1Title = $feat['item_1']['title'] ?? 'BEST LOCATION IN MINA';
            $item1Desc = $feat['item_1']['desc'] ?? "AVG16 PEOPLE TO A TENT\n<small>SOFACUM BED SIZE 50-55 CM EACH \n(TENT MAY BE COMBINED) \nAS PER SAUDI TALIMAAT</small>";

            $item2Icon = $feat['item_2']['icon'] ?? 'assets/images/package_images/1.png';
            $item2Text = $feat['item_2']['text'] ?? "MAKKAH AND \n MEDINAH HOTELS \n HALF BOARD BASIS";

            $item3Icon = $feat['item_3']['icon'] ?? 'assets/images/package_images/2.png';
            $item3Text = $feat['item_3']['text'] ?? "FULL BOARD BUFFET \n MEAL IN MINA & ARAFAT \n <small>For Group Maktab A Category \nHujjaj</small>";

            $item4Icon = $feat['item_4']['icon'] ?? 'assets/images/package_images/2.png';
            $item4Text = $feat['item_4']['text'] ?? "AZIZIYA ACCOMMODATION \nQUAD SHARING, FULL \nBOARD BUFFET";

            $item5Icon = $feat['item_5']['icon'] ?? 'assets/images/package_images/3.png';
            $item5Text = $feat['item_5']['text'] ?? "PRIVATE BATHROOM \n IN MINA & ARAFAT FOR \n UB GROUP";

            $item6Icon = $feat['item_6']['icon'] ?? 'assets/images/package_images/4.png';
            $item6Text = $feat['item_6']['text'] ?? "<small>BULLET TRAIN MAK-MED OR MED-MAK</small>\n PRIVATE LUXURY BUSSES \n MODEL 2025 FOR MASHAER \n DAYS WITH BATHROOM";
        @endphp

        <div class="row align-items-start">
            <div class="col-lg-5 col-5 mb-2 align-self-start">
                <div class="icon-box">
                    <div class="row">
                        <!-- Block 1: Zone & Maktab -->
                        <div class="col-6 icon-item">
                            <div class="text-center mb-1">
                                <span class="zone-badge">{{ $item1Zone }}@if(!empty($item1Maktab))<small>{{ $item1Maktab }}</small>@endif</span>
                            </div>
                            @if(!empty($item1Title))
                                <div class="desc fw-bold">{!! nl2br($item1Title) !!}</div>
                            @endif
                            <div class="desc">{!! nl2br($item1Desc) !!}</div>
                        </div>

                        <!-- Block 2: Hotels & Meals -->
                        <div class="col-6 icon-item">
                            <div>
                                <img src="{{ asset($item2Icon) }}" alt="Icon"
                                    width="26" height="26" style="object-fit: contain;">
                            </div>
                            <div class="desc">{!! nl2br($item2Text) !!}</div>
                        </div>

                        <!-- Block 3: Mina & Arafat Meal -->
                        <div class="col-6 icon-item">
                            <div>
                                <img src="{{ asset($item3Icon) }}" alt="Icon"
                                    width="26" height="26" style="object-fit: contain;">
                            </div>
                            <div class="desc">{!! nl2br($item3Text) !!}</div>
                        </div>

                        <!-- Block 4: Aziziya Accommodation -->
                        <div class="col-6 icon-item">
                            <div>
                                <img src="{{ asset($item4Icon) }}" alt="Icon"
                                    width="26" height="26" style="object-fit: contain;">
                            </div>
                            <div class="desc">{!! nl2br($item4Text) !!}</div>
                        </div>

                        <!-- Block 5: Private Bathroom -->
                        <div class="col-6 icon-item">
                            <div>
                                <img src="{{ asset($item5Icon) }}" alt="Icon"
                                    width="26" height="26" style="object-fit: contain;">
                            </div>
                            <div class="desc">{!! nl2br($item5Text) !!}</div>
                        </div>

                        <!-- Block 6: Transport & Luxury Busses -->
                        <div class="col-6 icon-item">
                            <div>
                                <img src="{{ asset($item6Icon) }}" alt="Icon"
                                    width="26" height="26" style="object-fit: contain;">
                            </div>
                            <div class="desc">{!! nl2br($item6Text) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-7 mb-2">
                {{-- <div class="notes-title">NOTES:</div> --}}
                <div class="notes-content">
                    {!! $package->notes !!}
                </div>
                {{-- <div class="taxi-strip">FAMILY CAR/TAXI SERVICES AVAILABLE SAR
                    {{ $package->jeddah_taxi_fare ?? '600' }} PER PERSON FROM JEDDAH AIRPORT TO MAKKAH HOTEL &amp; V.V
                </div>
                <div class="taxi-strip">FAMILY CAR/TAXI SERVICES AVAILABLE SAR
                    {{ $package->madinah_taxi_fare ?? '150' }} PER PERSON FROM MEDINAH AIRPORT TO MEDINAH HOTEL &amp;
                    V.V</div> --}}

            </div>
        </div>
        </div>

        <div class="sign-box">
            Applicant Sign: ____________________
        </div>
    </div>

    <!-- Page 2 Sheet: Platinum Packages Services (Matching Image 1) -->
    @php
        $defaultServicesContent = '<ul>
    <li>Meet & assist at the airport Jeddah/Medinah Hajj Terminal. (Sub to Approval Handling).</li>
    <li>Group arrival transfer by Bus from Airport to hotel is provided by NAQABA / SAUDI MOULLEM.</li>
    <li>Average 04 person sharing Accommodation in Aziziya with air condition A class building with proper beds (Pillow, Bed sheet, Blanket).</li>
    <li>Fullboard meal (Breakfast, Lunch & dinner) with hot & Coldrink to serve in Aziziya building except Hajj Days.</li>
    <li>Aziziya Services please reference to Page # 25 & 25A.</li>
    <li>Accommodation in Makkah hotels with Breakfast & Dinner (by Saudi Star Standard).</li>
    <li>Accommodation in Medinah hotels with Breakfast & Dinner (by Saudi Star Standard).</li>
    <li>During peak days from the 1st to the 14th of Zill Hajj, the check-in time at hotels in Makkah and Medinah is after Isha prayers, in accordance with hotel policies and due to the large number of check-ins and check-outs. The check-out time is 12 PM</li>
    <li>Fullboard meal to be serve in Mina from 08 Zil hajj to 12 Zil hajj.</li>
    <li>5 days Platinum Arrangment between 08 Zil hajj to 12 Zil hajj with retaining room in Aziziya.</li>
    <li>Private Special Luxury Busses with Bathroom (Mina - Arafat - Muzdalfa - Mina).</li>
    <li>Transfer Makkah to Medinah or Medinah to Makkah by Bus/Train.</li>
    <li>Best location MAKTAB (A) in mina very near to Jamarat, with Sofa Cum Bed (size 50 to 55cm) Private Toilet, for Group MAKTAB (A) Category Hujjaj. (Indian and western) (Services by Saudi Company)</li>
    <li>(Mashaer Hajj Services) Pillow, Bed sheet, blanket, Air conditioned tent, buffet meal & Hot & Coldrink. Avg 16 people to a tent (Tent may be combined). (Services by Saudi Company)</li>
    <li>Tent in Arafat with meals and Hot & Coldrink. Floor Mat & snack box in Muzdalfa. (Services by Saudi Company)</li>
    <li>Mic and Speaker are installed to the religious speeches for guidance.</li>
</ul>

<p><strong>Airline Ticket not included in this package<br>
(Approx PKR 335,000/- FROM KARACHI & PKR 345,000/- FROM NORTH PAKISTAN.)</strong></p>

<p>Different fares for Hajis coming from international destination.</p>
<ul>
    <li>Saudi Airline, Emirates, Oman, PIA, Qatar, SereneAir, FlyNas, Turkish Airlines, Fly Dubai etc Inclusive PSF.</li>
    <li>International ticket may be upgraded to Business class by paying suppliment. (Subject to Availability)</li>
    <li>Ziyarat in Medinah with guidance.</li>
    <li>Hajj training program and guidance in Pakistan / Saudia.</li>
    <li>Religious guide book etc.</li>
    <li>Assitance in doing Qurbani Approx Charges SAR 720/-</li>
    <li>Assitance in Tawaf - e - Ziyara.</li>
</ul>

<p><strong>IMPORTANT NOTES:</strong></p>
<ol>
    <li>No of days of stay in Makkah can be reduced but prices remain the same.</li>
    <li>Shuttle will be provided two times a day for drop to Haram till 07 Zil hajj, (Shuttle services subject to Saudi Laws and traffic).</li>
    <li>Abraaj Tower Means Swiss Maqam, Hajar tower, Swissotel, Safwa orchid, Al marwa etc & Project of Jabal e Omar, means Hayat Regency, Address Hotel, Jumeirah Hotel, Hilton Convention, Double Tree, Marriot Hotel etc.</li>
    <li>Kaba view Supplement SAR 3800/- per person.</li>
    <li>Rates & Hotels subject to change (Currency Difference) prices are subject to change. Even after booking /Saudi Talimaat changes.</li>
</ol>';
    @endphp

    <div class="sheet mb-4" id="packageSheet2">
        <div class="sheet-content">
            <div class="services-header-banner">
                <h2>{{ $package->services_title ?? 'PLATINUM PACKAGES SERVICES (WITH AZIZIYA)' }}</h2>
            </div>

            <div class="services-sheet-body">
                {!! $package->services_content ?? $defaultServicesContent !!}
            </div>
        </div>

        <div class="sign-box">
            Applicant Sign: ____________________
        </div>
    </div>

    <script>
        function downloadPackagePDF() {
            const sheet1 = document.getElementById('packageSheet1');
            const sheet2 = document.getElementById('packageSheet2');
            if (!sheet1) return;

            const filename = '{{ $package->code ?? 'Package' }}_{{ $package->days ?? '14' }}_Days_Package.pdf';
            const jsPDFClass = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : (window.jsPDF || null);

            if (typeof html2canvas !== 'undefined' && jsPDFClass) {
                // Render Sheet 1 (Page 1)
                html2canvas(sheet1, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    scrollY: 0
                }).then(function(canvas1) {
                    const imgData1 = canvas1.toDataURL('image/jpeg', 0.98);
                    const pdf = new jsPDFClass('p', 'mm', 'a4');
                    const pdfWidth = 210;
                    const pdfHeight = 297;
                    const margin = 2; // 2mm margin
                    const printableWidth = pdfWidth - (margin * 2);
                    const printableHeight = pdfHeight - (margin * 2);

                    let renderWidth1 = printableWidth;
                    let renderHeight1 = (canvas1.height * renderWidth1) / canvas1.width;
                    let posX1 = margin;
                    let posY1 = margin;

                    if (renderHeight1 > printableHeight) {
                        const ratio = printableHeight / renderHeight1;
                        renderHeight1 = printableHeight;
                        renderWidth1 = renderWidth1 * ratio;
                        posX1 = margin + ((printableWidth - renderWidth1) / 2);
                    }

                    pdf.addImage(imgData1, 'JPEG', posX1, posY1, renderWidth1, renderHeight1);

                    // Render Sheet 2 (Page 2) if it exists
                    if (sheet2) {
                        html2canvas(sheet2, {
                            scale: 2,
                            useCORS: true,
                            logging: false,
                            scrollY: 0
                        }).then(function(canvas2) {
                            pdf.addPage('a4', 'portrait');
                            const imgData2 = canvas2.toDataURL('image/jpeg', 0.98);

                            let renderWidth2 = printableWidth;
                            let renderHeight2 = (canvas2.height * renderWidth2) / canvas2.width;
                            let posX2 = margin;
                            let posY2 = margin;

                            if (renderHeight2 > printableHeight) {
                                const ratio = printableHeight / renderHeight2;
                                renderHeight2 = printableHeight;
                                renderWidth2 = renderWidth2 * ratio;
                                posX2 = margin + ((printableWidth - renderWidth2) / 2);
                            }

                            pdf.addImage(imgData2, 'JPEG', posX2, posY2, renderWidth2, renderHeight2);
                            pdf.save(filename);
                        }).catch(function(err) {
                            console.error('Error rendering sheet 2:', err);
                            pdf.save(filename);
                        });
                    } else {
                        pdf.save(filename);
                    }
                }).catch(function(err) {
                    console.error('Error generating PDF:', err);
                });
            } else {
                html2pdf().set({
                    margin: [2, 2, 2, 2],
                    filename: filename,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                }).from(sheet1).save();
            }
        }

        @if (!empty($auto_download))
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    downloadPackagePDF();
                }, 1000);
            });
        @endif
    </script>
</body>

</html>
