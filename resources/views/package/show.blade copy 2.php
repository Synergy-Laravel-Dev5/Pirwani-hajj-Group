<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $package->name ?? 'Executive Platinum' }} - {{ $package->days ?? '14' }} Days Package</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Poppins:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- html2pdf.js Library -->
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
            margin: 0 auto;
            background: #fff;
            padding: 15px 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, .15);
            box-sizing: border-box;
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
            padding: 8px 15px;
            flex: 2.2;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .header-left h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 900;
            font-size: 1.6rem;
            margin: 0;
            line-height: 1.1;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header-left .subtitle {
            color: #ffffff;
            font-weight: 700;
            font-size: .75rem;
            letter-spacing: .5px;
            margin-top: 2px;
            text-transform: uppercase;
        }

        .header-right {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold) 50%, #b9812a);
            color: var(--navy);
            flex: 1.4;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 4px 10px;
            position: relative;
        }

        .pkg-code-box {
            text-align: center;
            font-weight: 800;
        }

        .pkg-code-box .lbl {
            font-size: .58rem;
            letter-spacing: 0.5px;
            color: #131738;
        }

        .pkg-code-box .code {
            font-size: 1rem;
            background: var(--navy);
            color: #fff;
            padding: 1px 7px;
            border-radius: 2px;
            font-weight: 800;
        }

        .header-right .days {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--navy);
            line-height: 1;
        }

        .tags-container {
            position: absolute;
            bottom: 4px;
            right: 10px;
            display: flex;
            gap: 4px;
        }

        .tag-badge {
            background: var(--navy);
            color: #fff;
            font-size: .58rem;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 2px;
        }

        /* Custom Image Matching Table Styling */
        .pkg-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            font-size: .70rem;
        }

        .pkg-table th,
        .pkg-table td {
            border: 1px solid var(--border-color);
            text-align: center;
            vertical-align: middle;
            padding: 4px 5px;
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
        .pkg-table tbody tr.mashair-row td.mashair-cell {
            background: var(--peach-bg) !important;
            font-weight: 700;
            color: #1a1a1a;
        }

        .stars {
            color: #ffb400;
            font-size: .78rem;
            margin-left: 4px;
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
            width: 20%;
            letter-spacing: .5px;
        }

        .room-table .room-label {
            width: 30%;
            text-align: center;
            font-weight: 600;
            background: #fdfdfd;
            color: #333;
        }

        .room-table .room-price-a,
        .room-table .room-price-b {
            width: 25%;
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
            height: 100%;
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
            margin-top: 8px;
            font-weight: 700;
            font-size: .7rem;
            text-align: right;
            padding-right: 15px;
        }
    </style>
</head>

<body>

    <!-- Top Action Bar -->
    <div class="action-bar">
        <a href="{{ route('package.index') }}" class="btn-back">⬅ Back to List</a>
        <button class="btn-download" onclick="downloadPackagePDF()">📥 Download PDF</button>
    </div>

    <div class="sheet" id="packageSheet">
        <!-- Header Banner -->
        <div class="header-banner">
            <div class="header-left">
                <h1>{{ $package->package_title ?? $package->name ?? 'EXECUTIVE PLATINUM' }}</h1>
                {{-- <div class="subtitle">{{ $package->travel_route ?? 'N/a'}}</div> --}}
            </div>
            <div class="header-right">
                <div class="pkg-code-box">
                    <div class="lbl">PKG CODE</div>
                    <div class="code">{{ $package->code ?? 'UB 002' }}</div>
                </div>
                <div class="days">{{ $package->days ?? '14' }} Days Package</div>
                <div class="tags-container">
                    <span
                        class="tag-badge">{{ strtoupper(str_replace('_', ' ', $package->medina_arrival ?? 'NON SHIFTING')) }}</span>
                    <span class="tag-badge">{{ strtoupper($package->hajj_duration ?? 'NON AZIZIYA') }}</span>
                </div>
            </div>
        </div>

        @php
            // Helpers to safely fetch data properties
            $pkgAccVal = function ($acc, $key) {
                if (is_array($acc)) {
                    return $acc[$key] ?? null;
                }
                return $acc->$key ?? null;
            };

            $pkgNested = function ($acc, $group, $key) use ($pkgAccVal) {
                $flatKey = "{$group}_{$key}";
                if (is_array($acc) && array_key_exists($flatKey, $acc)) {
                    return $acc[$flatKey];
                }
                if (is_object($acc) && (isset($acc->$flatKey) || property_exists($acc, $flatKey))) {
                    return $acc->$flatKey;
                }

                $g = $pkgAccVal($acc, $group);
                if (!$g) {
                    return null;
                }
                return is_array($g) ? $g[$key] ?? null : $g->$key ?? null;
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
            $islamicMonthLengths = [
                1 => 30,
                2 => 29,
                3 => 30,
                4 => 29,
                5 => 30,
                6 => 29,
                7 => 30,
                8 => 29,
                9 => 30,
                10 => 29,
                11 => 30,
                12 => 30,
            ];

            $addHijriDays = function ($startDay, $startMonth, $daysToAdd) use ($islamicMonthLengths) {
                $day = (int) $startDay;
                $month = (int) $startMonth;
                for ($i = 0; $i < $daysToAdd; $i++) {
                    $day++;
                    if ($day > ($islamicMonthLengths[$month] ?? 30)) {
                        $day = 1;
                        $month = $month == 12 ? 1 : $month + 1;
                    }
                }
                return [$day, $month];
            };

            $makkahHotelA = '-';
            $makkahHotelB = '-';
            $makkahStarsA = 0;
            $makkahStarsB = 0;

            if (!empty($package->accommodations)) {
                foreach ($package->accommodations as $acc) {
                    $place = strtolower($pkgAccVal($acc, 'place') ?? '');
                    if (str_contains($place, 'makkah')) {
                        $makkahHotelA = $pkgNested($acc, 'package_a', 'hotel') ?: $makkahHotelA;
                        $makkahHotelB = $pkgNested($acc, 'package_b', 'hotel') ?: $makkahHotelB;
                        $makkahStarsA = (int) ($pkgNested($acc, 'package_a', 'saudi_star_rating') ?: $makkahStarsA);
                        $makkahStarsB = (int) ($pkgNested($acc, 'package_b', 'saudi_star_rating') ?: $makkahStarsB);
                    }
                }
            }

            $itineraryList = [];
            $dayCounter = 1;
            $hijriStartDay = $package->hijri_start_day ?? 4;
            $hijriStartMonth = $package->hijri_start_month ?? 12;

            if (!empty($package->accommodations) && count($package->accommodations) > 0) {
                foreach ($package->accommodations as $acc) {
                    $checkInRaw = $pkgAccVal($acc, 'check_in');
                    $checkOutRaw = $pkgAccVal($acc, 'check_out');
                    $checkIn = $checkInRaw ? \Carbon\Carbon::parse($checkInRaw) : null;
                    $checkOut = $checkOutRaw ? \Carbon\Carbon::parse($checkOutRaw) : null;
                    $sameForBoth = (bool) $pkgAccVal($acc, 'same_for_both');

                    $starsA = (int) ($pkgNested($acc, 'package_a', 'saudi_star_rating') ?: 0);
                    $starsB = (int) ($pkgNested($acc, 'package_b', 'saudi_star_rating') ?: 0);

                    if ($checkIn && $checkOut) {
                        $period = \Carbon\CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
                        foreach ($period as $date) {
                            $hijriDate = '-';
                            $isMashair = false;
                            $hDayVal = 0;
                            if ($hijriStartDay && $hijriStartMonth) {
                                [$hDay, $hMonth] = $addHijriDays($hijriStartDay, $hijriStartMonth, $dayCounter - 1);
                                $hijriDate = sprintf('%02d %s', $hDay, $islamicMonths[$hMonth] ?? '');
                                if ($hMonth == 12 && $hDay >= 8 && $hDay <= 11) {
                                    $isMashair = true;
                                    $hDayVal = $hDay;
                                }
                            }

                            if ($isMashair) {
                                $servicesText =
                                    $hDayVal == 9
                                        ? 'Arafat Air Conditioned Marquee (Exclusive Services)'
                                        : 'Zone 1 near to Jamarat A Category (Exclusive Services)';

                                $itineraryList[] = [
                                    'day' => sprintf('%02d', $dayCounter++),
                                    'date' => $date->format('d M'),
                                    'hijri' => $hijriDate,
                                    'city' => $hDayVal == 8 ? 'To Mina' : 'Mina',
                                    'is_mashair' => true,
                                    'same_for_both' => false,
                                    'hotel_a' => $servicesText . ' / ' . $makkahHotelA,
                                    'stars_a' => '',
                                    'hotel_b' => $servicesText . ' / ' . $makkahHotelB,
                                    'stars_b' => '',
                                    // 'food_a' => $pkgNested($acc, 'package_a', 'food_package') ?: $pkgAccVal($acc, 'food_package'),
                                    // // 'food_b' => $pkgNested($acc, 'package_b', 'food_package') ?: $pkgAccVal($acc, 'food_package'),
                                    'azizia_date' => $pkgAccVal($acc, 'azizia_date') ? \Carbon\Carbon::parse($pkgAccVal($acc, 'azizia_date'))->format('d M') : null,
                                ];
                            } else {
                                $itineraryList[] = [
                                    'day' => sprintf('%02d', $dayCounter++),
                                    'date' => $date->format('d M'),
                                    'hijri' => $hijriDate,
                                    'city' => $pkgAccVal($acc, 'place') ?? 'Makkah',
                                    'is_mashair' => false,
                                    'same_for_both' => $sameForBoth,
                                    'hotel_a' => $pkgNested($acc, 'package_a', 'hotel') ?? '-',
                                    'stars_a' => str_repeat('★', $starsA),
                                    'hotel_b' => $pkgNested($acc, 'package_b', 'hotel') ?? '-',
                                    'stars_b' => str_repeat('★', $starsB),
                                    // // 'food_a' => $pkgNested($acc, 'package_a', 'food_package') ?: $pkgAccVal($acc, 'food_package'),
                                    // // 'food_b' => $pkgNested($acc, 'package_b', 'food_package') ?: $pkgAccVal($acc, 'food_package'),
                                    'azizia_date' => $pkgAccVal($acc, 'azizia_date') ? \Carbon\Carbon::parse($pkgAccVal($acc, 'azizia_date'))->format('d M') : null,
                                ];
                            }
                        }
                    }
                }
            }

            $prevCity = null;
            foreach ($itineraryList as &$item) {
                $city = trim($item['city']);
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
                        <th rowspan="2" class="th-left" style="width: 7%;">DAY</th>
                        <th rowspan="2" class="th-left" style="width: 10%;">DATE<br>(AD)</th>
                        <th rowspan="2" class="th-left" style="width: 12%;">DATE<br>(Hijri)</th>
                        <th rowspan="2" class="th-left" style="width: 13%;">CITY</th>
                        <th class="th-pkg-a" style="width: 29%;">PACKAGE (A)</th>
                        <th class="th-pkg-b" style="width: 29%;">PACKAGE (B)</th>
                    </tr>
                    <tr class="sub-header">
                        <th colspan="2">ACCOMMODATION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itineraryList as $item)
                        <tr class="{{ !empty($item['is_mashair']) ? 'mashair-row' : '' }}">
                            <td><b>{{ $item['day'] }}</b></td>
                            <td>{{ $item['date'] }}</td>
                            <td>{{ $item['hijri'] }}</td>
                            <td>{{ $item['city'] }}</td>
                            @if ($item['same_for_both'])
                                <td colspan="2"
                                    class="fw-bold text-center">
                                    {{ $item['hotel_a'] }}
                                    @if (!empty($item['stars_a']))
                                        <span class="stars">{{ $item['stars_a'] }}</span>
                                    @endif
                                    @if (!empty($item['food_a']))
                                        <div style="font-size: 0.72rem; color: #555; font-weight: 500; margin-top: 1px;">({{ $item['food_a'] }})</div>
                                    @endif
                                    {{-- @if (!empty($item['azizia_date']))
                                        <div style="font-size: 0.72rem; color: #d9534f; font-weight: 600; margin-top: 1px;">Azizia: {{ $item['azizia_date'] }}</div>
                                    @endif --}}
                                </td>
                            @else
                                <td class="text-center">
                                    {{ $item['hotel_a'] }}
                                    @if (!empty($item['stars_a']))
                                        <span class="stars">{{ $item['stars_a'] }}</span>
                                    @endif
                                    @if (!empty($item['food_a']))
                                        <div style="font-size: 0.72rem; color: #555; font-weight: 500; margin-top: 1px;">({{ $item['food_a'] }})</div>
                                    @endif
                                    {{-- @if (!empty($item['azizia_date']))
                                        <div style="font-size: 0.72rem; color: #d9534f; font-weight: 600; margin-top: 1px;">Azizia: {{ $item['azizia_date'] }}</div>
                                    @endif --}}
                                </td>
                                <td class="text-center">
                                    {{ $item['hotel_b'] }}
                                    @if (!empty($item['stars_b']))
                                        <span class="stars">{{ $item['stars_b'] }}</span>
                                    @endif
                                    @if (!empty($item['food_b']))
                                        <div style="font-size: 0.72rem; color: #555; font-weight: 500; margin-top: 1px;">({{ $item['food_b'] }})</div>
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

            $quadA = !empty($makkahA['quad']) ? 'SAR ' . number_format((float) $makkahA['quad']) . '/-' : 'NA';
            $tripleA = !empty($makkahA['triple'])
                ? 'SAR ' . number_format((float) $makkahA['triple']) . '/-'
                : 'SAR 94,600/-';
            $doubleA = !empty($makkahA['double'])
                ? 'SAR ' . number_format((float) $makkahA['double']) . '/-'
                : 'SAR 118,500/-';

            $quadB = !empty($makkahB['quad'])
                ? 'SAR ' . number_format((float) $makkahB['quad']) . '/-'
                : 'SAR 63,500/-';
            $tripleB = !empty($makkahB['triple'])
                ? 'SAR ' . number_format((float) $makkahB['triple']) . '/-'
                : 'SAR 70,200/-';
            $doubleB = !empty($makkahB['double'])
                ? 'SAR ' . number_format((float) $makkahB['double']) . '/-'
                : 'SAR 85,500/-';
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
        <div class="row">
            <div class="col-lg-5 col-5 mb-2">
                <div class="icon-box">
                    <div class="row">
                        <div class="col-6 icon-item">
                            <div class="text-center mb-1">
                                <span class="zone-badge">ZONE {{ $package->category_zone ?? '1' }}<small>MAKTAB
                                        {{ $package->maktab ?? 'A-CATEGORY' }}</small></span>
                            </div>
                            <div class="label">BEST LOCATION IN MINA</div>
                            <div class="desc">AVG 16 PEOPLE TO A TENT<br>SOFA CUM BED SIZE 50-55 CM EACH</div>
                        </div>
                        <div class="col-6 icon-item">
                            <div class="icon-circle">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.55 3.89 3.56 4.16L6.5 22h3l-.06-8.84C11.45 12.89 13 11.12 13 9V2h-2v7zm9-7h-3c-1.1 0-2 .9-2 2v8h2v10h3V2z"/></svg>
                            </div>
                            <div class="label">FULL BOARD BUFFET</div>
                            <div class="desc">MEAL IN MINA &amp; ARAFAT</div>
                        </div>
                        <div class="col-6 icon-item">
                            <div class="icon-circle">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/></svg>
                            </div>
                            <div class="label">MAKKAH AND MEDINAH HOTELS</div>
                            <div class="desc">HALF BOARD BASIS</div>
                        </div>
                        <div class="col-6 icon-item">
                            <div class="icon-circle">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20 13V4.83C20 3.27 18.73 2 17.17 2c-.75 0-1.47.3-2 0l-2.83 2.83C11.8 5.37 11.29 6 10.5 6H7c-1.1 0-2 .9-2 2v5H2v2c0 3.31 2.69 6 6 6h8c3.31 0 6-2.69 6-6v-2h-2zm-6-7.17l1.41-1.41c.2-.2.45-.3.76-.3.64 0 1.17.53 1.17 1.17V13h-3.34l.05-.05V5.83zM7 8h3v5H7V8zm13 7c0 2.21-1.79-4-4-4H8c-2.21 0-4-1.79-4-4v-1h16v1z"/></svg>
                            </div>
                            <div class="label">PRIVATE BATHROOM</div>
                            <div class="desc">IN MINA &amp; ARAFAT FOR UB GROUP</div>
                        </div>
                        <div class="col-6 icon-item">
                            <div class="icon-circle">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zm0 2c3.51 0 4.96.48 5.5 1H6.5c.54-.52 1.99-1 5.5-1zm6 11.5c0 .83-.67 1.5-1.5 1.5h-9c-.83 0-1.5-.67-1.5-1.5V11h12v4.5zm0-6H6V7h12v2.5z"/><circle cx="8.5" cy="13.5" r="1.5"/><circle cx="15.5" cy="13.5" r="1.5"/></svg>
                            </div>
                            <div class="label">BULLET TRAIN</div>
                            <div class="desc">MAK-MED OR MED-MAK</div>
                        </div>
                        <div class="col-6 icon-item">
                            <div class="icon-circle">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M4 16c0 1.1.9 2 2 2h1v2c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-2h8v2c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-2h1c1.1 0 2-.9 2-2V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 0c-.83 0-1.5-.67-1.5-1.5S6.67 13 7.5 13s1.5.67 1.5 1.5S8.33 16 7.5 16zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V6h12v4z"/></svg>
                            </div>
                            <div class="label">PRIVATE LUXURY BUSSES</div>
                            <div class="desc">MODEL 2025 FOR MASHAER DAYS WITH BATHROOM</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-7 mb-2">
                <div class="notes-title">NOTES:</div>
                <ul class="notes-list">
                    <li>• <b>Reference Pages:</b> Package Service: Pg 24-Platinum Mina Hajj Services: Pg 10-12 | Payment
                        Plan: Pg 41 - Terms &amp; Conditions: Pg 43</li>
                    <li>• <b>Upgrade your Hajj from Platinum to Deluxe Hajj</b> with Supplement SAR 16,000 (Per Person)
                        (Private Toilet, 08 People to a tent).</li>
                    <li>• <b>Upgrade your Hajj from Platinum to Diamond Hajj</b> with Supplement SAR 6,000 (Per Person)
                        (12 People to a tent).</li>
                    <li>• Family tent in Mina with private toilet available on request basis.</li>
                    <li>• <b>Aziziya Accommodation:</b> Family Rooms available in our Aziziya Building duration of 05
                        days of Hajj with Supplement SAR 20,000.</li>
                </ul>
                <div class="taxi-strip">FAMILY CAR/TAXI SERVICES AVAILABLE SAR
                    {{ $package->jeddah_taxi_fare ?? '600' }} PER PERSON FROM JEDDAH AIRPORT TO MAKKAH HOTEL &amp; V.V
                </div>
                <div class="taxi-strip">FAMILY CAR/TAXI SERVICES AVAILABLE SAR
                    {{ $package->madinah_taxi_fare ?? '150' }} PER PERSON FROM MEDINAH AIRPORT TO MEDINAH HOTEL &amp;
                    V.V</div>

                <div class="sign-box">
                    Applicant Sign: ____________________
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadPackagePDF() {
            const element = document.getElementById('packageSheet');
            const options = {
                margin: [4, 4, 4, 4],
                filename: '{{ $package->code ?? 'Package' }}_{{ $package->days ?? '14' }}_Days_Package.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            html2pdf().set(options).from(element).save();
        }
    </script>
</body>

</html>
