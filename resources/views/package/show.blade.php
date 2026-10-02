<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $package->package_title ?? ($package->name ?? 'Hajj Package') }} - Pirwani Hajj Group</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Montserrat:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">

    <!-- High-Resolution PDF Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        :root {
            --gold-primary: #D1A843;
            --gold-dark: #A27616;
            --gold-light: #F9E7B3;
            --gold-badge: #ECC678;
            --header-teal: #083747;
            --card-border: #C8A344;
            --dark-text: #111111;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #232a35;
            color: var(--dark-text);
            margin: 0;
            padding: 15px 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ── Top Action Bar ── */
        .action-bar {
            max-width: 800px;
            margin: 0 auto 10px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 4px;
        }
        .btn-action-back {
            background: #0E233E;
            color: #F3DC9B;
            border: 1px solid #C9A84C;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }
        .btn-action-pdf {
            background: linear-gradient(135deg, #D4AF37, #B88E28);
            color: #071527;
            border: none;
            font-weight: 700;
            padding: 7px 18px;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(201, 168, 76, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }
        .btn-action-print {
            background: #FFFFFF;
            color: #071527;
            border: 1px solid #C9A84C;
            font-weight: 700;
            padding: 7px 14px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }

        /* ── Exact Brochure Container ── */
        .brochure-page {
            width: 794px;
            max-width: 794px;
            height: auto;
            margin: 0 auto;
            background: #FAF5EB radial-gradient(circle at 50% 6%, #FFFFFF 0%, #FBF6EC 40%, #F5EADB 100%);
            padding: 10px 14px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.45);
            border: 1.5px solid #C59A27;
            overflow: hidden;
        }

        /* ── Background Layer 1: Radiant Beams & Arch Halo from top ── */
        .brochure-page::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 380px;
            background-image:
                radial-gradient(ellipse at 50% -10px, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.75) 25%, rgba(236, 198, 120, 0.28) 55%, transparent 80%),
                repeating-conic-gradient(from 0deg at 50% -15px, rgba(209, 168, 67, 0.05) 0deg 7deg, transparent 7deg 14deg);
            pointer-events: none;
            z-index: 1;
        }

        /* ── Background Layer 2: Holy Watermark Silhouette (Kaaba & Minarets) ── */
        .brochure-page::after {
            content: "";
            position: absolute;
            top: 48%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 520px;
            height: 520px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 500 500'%3E%3Cg fill='%23C89926' opacity='0.038'%3E%3Crect x='190' y='180' width='120' height='130' rx='4'/%3E%3Cpolygon points='190,210 310,210 310,218 190,218' fill='%23D4AF37' opacity='0.8'/%3E%3Cpath d='M250 80 L260 140 L240 140 Z'/%3E%3Crect x='246' y='140' width='8' height='40'/%3E%3Cpath d='M130 110 L136 170 L124 170 Z'/%3E%3Crect x='127' y='170' width='6' height='140'/%3E%3Cpath d='M370 110 L376 170 L364 170 Z'/%3E%3Crect x='367' y='170' width='6' height='140'/%3E%3Ccircle cx='250' cy='250' r='210' fill='none' stroke='%23C89926' stroke-width='2' stroke-dasharray='6,6'/%3E%3C/g%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
            z-index: 1;
        }

        .brochure-border-frame {
            position: relative;
            background: transparent;
            z-index: 2;
        }

        /* ── 1. Header Grid ── */
        .top-header-grid {
            display: grid;
            grid-template-columns: 185px 1fr 185px;
            align-items: flex-start;
            gap: 4px;
            margin-bottom: 5px;
        }
        .header-left-col {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .stay-type-pill {
            background: #0E2938;
            color: #FFFFFF;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.5px;
            padding: 2.5px 8px;
            display: inline-block;
            text-transform: uppercase;
        }
        .stay-days-large {
            margin: 2px 0 1px 0;
        }
        .stay-num-val {
            font-size: 26px;
            font-weight: 900;
            color: #000;
            line-height: 1;
            letter-spacing: -0.5px;
        }
        .stay-days-text {
            font-size: 19px;
            font-weight: 900;
            color: #000;
            line-height: 1;
            letter-spacing: 0.5px;
        }
        .header-route-label {
            font-size: 10.5px;
            font-weight: 800;
            color: #000;
            display: flex;
            align-items: center;
            gap: 3px;
            margin-top: 3px;
        }
        .header-route-pill {
            background: #ECC678;
            color: #000;
            font-weight: 800;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 9px;
            margin: 2px 0;
            display: inline-block;
            white-space: nowrap;
        }
        .header-route-city {
            font-weight: 800;
            color: #111;
            font-size: 10px;
        }

        /* Center Header */
        .header-center-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .brand-top-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .brand-crest-box {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .brand-crest-img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }
        .brand-gl-number {
            font-size: 7.5px;
            font-weight: 800;
            color: #000;
            margin-top: 1px;
            white-space: nowrap;
        }
        .brand-name-box {
            text-align: left;
        }
        .brand-name-pirwani {
            font-size: 32px;
            font-weight: 900;
            color: #C89926;
            line-height: 0.88;
        }
        .brand-name-sub {
            font-size: 9px;
            font-weight: 800;
            color: #000;
            margin-top: 2px;
        }
        .hajj-main-title {
            font-family: 'Cinzel', 'Playfair Display', serif;
            font-size: 58px;
            font-weight: 900;
            letter-spacing: 4px;
            color: #0F3746;
            line-height: 0.82;
            margin: 2px 0;
        }
        .package-banner-cartouche {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #092C3A;
            color: #FFFFFF;
            padding: 2.5px 20px;
            border-radius: 4px;
            border: 1.5px solid #E5C368;
            margin: 1px 0;
        }
        .cartouche-text {
            font-family: 'Cinzel', serif;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 4px;
            color: #FFFFFF;
        }
        .cartouche-flourish {
            color: #E8C874;
            display: inline-flex;
            align-items: center;
            width: 22px;
            height: 12px;
        }
        .hajj-year-text {
            font-size: 13.5px;
            font-weight: 900;
            color: #000;
            letter-spacing: 1px;
            margin-top: 3px;
        }

        /* Right Header */
        .header-right-col {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            text-align: right;
        }
        .company-title-text {
            font-size: 9px;
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .company-logo-img {
            height: 32px;
            max-width: 130px;
            object-fit: contain;
            margin-bottom: 2px;
        }

        /* ── 2. Pricing Matrix Row ── */
        .pricing-matrix-container {
            border: 1.5px solid #C59A27;
            background: #FFFDF8;
            margin-bottom: 6px;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .matrix-half {
            display: flex;
            align-items: stretch;
        }
        .matrix-half:first-child {
            border-right: 1.5px solid #C59A27;
        }
        .matrix-gold-block {
            background: #ECC678;
            color: #000;
            width: 66px;
            min-width: 66px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3px 2px;
            text-align: center;
            border-right: 1.2px solid #C59A27;
        }
        .matrix-letter {
            font-size: 27px;
            font-weight: 900;
            line-height: 1;
        }
        .matrix-zone-text {
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.1;
            margin-top: 1px;
        }
        .matrix-rates-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1.1fr 1fr 1fr;
            align-items: center;
            padding: 3px 1px;
            background: #FFFDF8;
        }
        .matrix-rate-col {
            text-align: center;
            padding: 0 1px;
        }
        .matrix-rate-col:not(:last-child) {
            border-right: 1px solid #E2D3B8;
        }
        .matrix-col-title {
            font-size: 8.5px;
            font-weight: 900;
            color: #000;
            margin-bottom: 1px;
            white-space: nowrap;
        }
        .matrix-pkr-val {
            font-size: 13.5px;
            font-weight: 900;
            color: #000;
            line-height: 1.05;
        }
        .matrix-usd-val {
            font-size: 9px;
            font-weight: 800;
            color: #8C6615;
            line-height: 1.1;
        }

        /* ── 3. Main Middle Section ── */
        .main-middle-grid {
            display: grid;
            grid-template-columns: 1fr 265px;
            gap: 7px;
            margin-bottom: 5px;
        }

        /* Left Stack */
        .stay-cards-stack {
            display: flex;
            flex-direction: column;
            gap: 4.5px;
        }
        .stay-card {
            border: 1.3px solid #C59A27;
            border-radius: 5px;
            overflow: hidden;
            background: #FFFDF8;
        }
        .stay-card-head {
            background: var(--header-teal);
            color: #FFFFFF;
            font-size: 9.5px;
            font-weight: 900;
            letter-spacing: 0.5px;
            padding: 2.5px 7px;
            display: flex;
            align-items: center;
            gap: 5px;
            text-transform: uppercase;
        }
        .stay-card-head i {
            color: #FFFFFF;
            font-size: 11px;
        }
        .stay-card-body {
            display: grid;
            grid-template-columns: 1.15fr 1.15fr 1.3fr;
            padding: 2px 2px;
            background: #FFFDF8;
            text-align: center;
            align-items: center;
        }
        .stay-col-item {
            padding: 0 2px;
        }
        .stay-col-item:not(:last-child) {
            border-right: 1px solid #E2D3B8;
        }
        .stay-col-label {
            font-size: 9px;
            font-weight: 900;
            color: #000;
            padding-bottom: 1px;
        }
        .stay-col-val {
            font-size: 9px;
            font-weight: 800;
            color: #111;
            line-height: 1.15;
        }
        .stay-col-sub {
            font-size: 8px;
            font-weight: 700;
            color: #333;
            line-height: 1.1;
        }

        /* Right Stack */
        .right-cards-stack {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .gold-bordered-card {
            border: 1.3px solid #C59A27;
            border-radius: 6px;
            background: #FFFDF8;
            padding: 4px 6px;
        }
        .gold-pill-header {
            background: #ECC678;
            color: #000;
            font-size: 9.5px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-align: center;
            padding: 2px 6px;
            border-radius: 12px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .azizia-row-item {
            display: grid;
            grid-template-columns: 95px 1fr;
            align-items: center;
            margin-bottom: 3.5px;
        }
        .azizia-pill-tag {
            background: #ECC678;
            color: #000;
            font-size: 8px;
            font-weight: 900;
            padding: 1.5px 6px;
            border-radius: 10px;
            text-align: center;
            display: inline-block;
        }
        .azizia-rate-col {
            text-align: right;
            line-height: 1.1;
        }
        .azizia-pkr-num {
            font-size: 12px;
            font-weight: 900;
            color: #000;
        }
        .azizia-usd-num {
            font-size: 8.5px;
            font-weight: 800;
            color: #8C6615;
            display: block;
        }

        .doc-required-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .doc-required-list li {
            position: relative;
            padding-left: 9px;
            margin-bottom: 2px;
            font-size: 8px;
            font-weight: 700;
            line-height: 1.25;
            color: #111;
        }
        .doc-required-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #000;
            font-weight: 900;
            font-size: 9px;
            top: -1px;
        }

        .important-note-text {
            font-size: 8.2px;
            font-weight: 600;
            line-height: 1.3;
            color: #111;
            margin: 0;
        }

        /* ── 4. Inclusions & Instructions ── */
        .inclusions-instructions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px;
            margin-bottom: 5px;
        }
        .info-card-box {
            border: 1.3px solid #C59A27;
            border-radius: 6px;
            background: #FFFDF8;
            padding: 4px 6px;
        }
        .info-card-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .info-card-list li {
            position: relative;
            padding-left: 11px;
            margin-bottom: 2px;
            font-size: 8px;
            font-weight: 700;
            line-height: 1.25;
            color: #111;
        }
        .info-card-list li::before {
            content: "✦";
            position: absolute;
            left: 0;
            color: #8C6615;
            font-size: 7px;
            top: 0px;
        }

        /* ── 5. Note Card ── */
        .note-card-container {
            border: 1.3px solid #C59A27;
            border-radius: 6px;
            background: #FFFDF8;
            padding: 7px 8px 4px 8px;
            position: relative;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .note-badge-pill {
            position: absolute;
            top: -7px;
            left: 50%;
            transform: translateX(-50%);
            background: #ECC678;
            color: #000;
            font-weight: 900;
            font-size: 9px;
            padding: 0px 14px;
            border-radius: 10px;
            border: 1px solid #A27616;
        }
        .note-bullets-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 12px;
            font-size: 7.8px;
            font-weight: 700;
            line-height: 1.22;
            color: #222;
        }
        .note-bullets-grid div {
            position: relative;
            padding-left: 8px;
        }
        .note-bullets-grid div::before {
            content: "•";
            position: absolute;
            left: 0;
            font-weight: 900;
            color: #000;
            font-size: 9px;
            top: -1px;
        }

        /* ── 6. Official Footer ── */
        .brochure-footer {
            border-top: 1.2px solid #C59A27;
            padding-top: 4px;
            text-align: center;
            font-size: 8.5px;
            color: #222;
            margin-top: auto;
        }
        .footer-address {
            font-weight: 800;
            font-size: 9px;
            color: #000;
            margin-bottom: 1px;
        }
        .footer-contacts {
            font-weight: 800;
            color: #000;
            margin-bottom: 2px;
            font-size: 9px;
        }
        .footer-social-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            font-size: 7.8px;
            font-weight: 700;
            color: #333;
        }
        .footer-social-row span {
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .footer-social-row i {
            color: #000;
            font-size: 8.5px;
        }

        @media print {
            body {
                background: #FFFFFF !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .action-bar {
                display: none !important;
            }
            .brochure-page {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>

<body>

    <div class="action-bar no-print">
        <a href="{{ route('package.index') }}" class="btn-action-back">
            <i class="mdi mdi-arrow-left"></i> Back to Packages
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('package.edit', $package->id) }}" class="btn btn-warning btn-sm fw-bold px-3 d-flex align-items-center gap-1" style="background:#E8C874;border:none;color:#000;">
                <i class="mdi mdi-pencil"></i> Edit
            </a>
            <button onclick="window.print()" class="btn-action-print">
                <i class="mdi mdi-printer"></i> Print
            </button>
            <button onclick="generatePDF()" class="btn-action-pdf" id="btn-download-pdf">
                <i class="mdi mdi-file-pdf-box fs-16"></i> Download Official PDF
            </button>
        </div>
    </div>

    <div class="brochure-page" id="brochure-container">
        <div class="brochure-border-frame">

            {{-- 1. TOP HEADER GRID --}}
            @php
                $depFlight = $package->transportFlights ? $package->transportFlights->first() : null;
                $arrFlight = ($package->transportFlights && $package->transportFlights->count() > 1) ? $package->transportFlights->last() : $depFlight;

                $stayDur = $package->stay_duration ?: ($package->days ? $package->days . ' DAYS' : ($package->duration_days ? $package->duration_days . ' DAYS' : '21 - 22 DAYS'));
                $parts = explode(' ', trim($stayDur));
                $numPart = $stayDur;
                if (count($parts) > 1 && strtoupper(end($parts)) === 'DAYS') {
                    array_pop($parts);
                    $numPart = implode(' ', $parts);
                }

                $depDateStr = $package->departure_date_str ?: ($depFlight && $depFlight->departure_date ? \Carbon\Carbon::parse($depFlight->departure_date)->format('d M / d F') : ($package->departure_date ? \Carbon\Carbon::parse($package->departure_date)->format('d M / d F') : ''));
                $depSectorStr = $package->departure_sector ?: ($depFlight && $depFlight->route ? $depFlight->route : 'Karachi to Jeddah');

                $arrDateStr = $package->arrival_date_str ?: ($arrFlight && $arrFlight->arrival_date ? \Carbon\Carbon::parse($arrFlight->arrival_date)->format('d M / d F') : ($arrFlight && $arrFlight->departure_date ? \Carbon\Carbon::parse($arrFlight->departure_date)->format('d M / d F') : ($package->arrival_date ? \Carbon\Carbon::parse($package->arrival_date)->format('d M / d F') : '')));
                $arrSectorStr = $package->arrival_sector ?: ($arrFlight && $arrFlight->route ? $arrFlight->route : 'Jeddah/Madinah to Karachi');

                $cQuadPkr = $package->maktab_c_quad_pkr > 0 ? (float)$package->maktab_c_quad_pkr : ((float)($package->adult_pkr ?? 0));
                $cQuadUsd = $package->maktab_c_quad_usd > 0 ? (float)$package->maktab_c_quad_usd : ((float)($package->adult_usd ?? 0));
                $cTripPkr = (float)($package->maktab_c_triple_pkr ?? 0);
                $cTripUsd = (float)($package->maktab_c_triple_usd ?? 0);
                $cDoubPkr = (float)($package->maktab_c_double_pkr ?? 0);
                $cDoubUsd = (float)($package->maktab_c_double_usd ?? 0);

                $aQuadPkr = (float)($package->maktab_a_quad_pkr ?? 0);
                $aQuadUsd = (float)($package->maktab_a_quad_usd ?? 0);
                $aTripPkr = (float)($package->maktab_a_triple_pkr ?? 0);
                $aTripUsd = (float)($package->maktab_a_triple_usd ?? 0);
                $aDoubPkr = (float)($package->maktab_a_double_pkr ?? 0);
                $aDoubUsd = (float)($package->maktab_a_double_usd ?? 0);

                $azQuadPkr = (float)($package->azizia_quad_pkr ?? 0);
                $azQuadUsd = (float)($package->azizia_quad_usd ?? 0);
                $azTripPkr = (float)($package->azizia_triple_pkr ?? 0);
                $azTripUsd = (float)($package->azizia_triple_usd ?? 0);
                $azDoubPkr = (float)($package->azizia_double_pkr ?? 0);
                $azDoubUsd = (float)($package->azizia_double_usd ?? 0);
            @endphp

            <div class="top-header-grid">
                
                {{-- Left Column --}}
                <div class="header-left-col">
                    <span class="stay-type-pill">{{ $package->stay_type ?: ($package->category ?: 'SHORT STAY') }}</span>
                    <div class="stay-days-large">
                        <div class="stay-num-val">{{ $numPart }}</div>
                        <div class="stay-days-text">DAYS</div>
                    </div>
                    @if(!empty($depDateStr))
                        <div class="header-route-label">
                            Departure Date: <i class="mdi mdi-airplane-takeoff"></i>
                        </div>
                        <div class="header-route-pill">
                            {{ $depDateStr }}
                        </div>
                    @endif
                    <div class="header-route-city">
                        {{ $depSectorStr }}
                    </div>
                </div>

                {{-- Center Column --}}
                <div class="header-center-col">
                    <div class="brand-top-row">
                        <div class="brand-crest-box">
                            <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Pirwani Crest" class="brand-crest-img">
                            <div class="brand-gl-number">{{ $package->company->gl_no ?? 'G.L. # 2990' }}</div>
                        </div>
                        <div class="brand-name-box">
                            <div class="brand-name-pirwani">Pirwani</div>
                            <div class="brand-name-sub">HAJJ GROUP (Pvt.) Ltd.</div>
                        </div>
                    </div>
                    <div class="hajj-main-title">HAJJ</div>
                    <div class="package-banner-cartouche">
                        <svg class="cartouche-flourish" viewBox="0 0 32 14" fill="#E8C874">
                            <path d="M2,7 C6,2 11,1 16,7 C11,13 6,12 2,7 Z M16,7 C21,2 26,1 30,7 C26,12 21,13 16,7 Z M16,4 L18,7 L16,10 L14,7 Z"/>
                            <circle cx="2" cy="7" r="1.5" fill="#E8C874"/>
                            <circle cx="30" cy="7" r="1.5" fill="#E8C874"/>
                        </svg>
                        <span class="cartouche-text">PACKAGE</span>
                        <svg class="cartouche-flourish" viewBox="0 0 32 14" fill="#E8C874" style="transform: scaleX(-1);">
                            <path d="M2,7 C6,2 11,1 16,7 C21,2 26,1 30,7 C26,12 21,13 16,7 Z M16,4 L18,7 L16,10 L14,7 Z"/>
                            <circle cx="2" cy="7" r="1.5" fill="#E8C874"/>
                            <circle cx="30" cy="7" r="1.5" fill="#E8C874"/>
                        </svg>
                    </div>
                    <div class="hajj-year-text">{{ $package->hijri_year ?: '1448' }} - {{ $package->gregorian_year ?: ($package->year ?: date('Y')) }}</div>
                </div>

                {{-- Right Column --}}
                <div class="header-right-col">
                    @if($package->company && !empty($package->company->company_logo))
                        <img src="{{ asset($package->company->company_logo) }}" alt="{{ $package->company->company_name }}" class="company-logo-img">
                        <div class="company-title-text">{{ $package->company->company_name }}</div>
                    @elseif($package->company && !empty($package->company->company_name))
                        <div class="company-title-text" style="font-size: 13px; font-weight: 900; color: #C28D1B; margin-bottom: 4px;">{{ $package->company->company_name }}</div>
                    @else
                        <div style="font-size: 18px; font-weight: 900; line-height: 1; color: #000;">AS</div>
                        <div class="company-title-text">AS SALAM MUNAZZAM (Pvt.) Ltd.</div>
                    @endif

                    @if(!empty($arrDateStr))
                        <div class="header-route-label">
                            Arrival Date: <i class="mdi mdi-airplane-landing"></i>
                        </div>
                        <div class="header-route-pill">
                            {{ $arrDateStr }}
                        </div>
                    @endif
                    <div class="header-route-city">
                        {{ $arrSectorStr }}
                    </div>
                </div>

            </div>

            {{-- 2. FULL-WIDTH PRICING MATRIX --}}
            <div class="pricing-matrix-container">
                {{-- Maktab C Section --}}
                <div class="matrix-half">
                    <div class="matrix-gold-block">
                        <div class="matrix-letter">{{ $package->camp_category ?: ($package->maktab ?: 'C') }}</div>
                        <div class="matrix-zone-text">MAKTAB<br>{{ $package->camp_zone ?: ($package->zone ?: 'ZONE 5') }}</div>
                    </div>
                    <div class="matrix-rates-grid">
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">QUAD / SHARING</div>
                            <div class="matrix-pkr-val">{{ $cQuadPkr > 0 ? number_format($cQuadPkr, 0) . '.' : '0.00' }}</div>
                            <div class="matrix-usd-val">{{ $cQuadUsd > 0 ? '$ ' . number_format($cQuadUsd, 0) . '.' : '$ 0.00' }}</div>
                        </div>
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">TRIPLE</div>
                            <div class="matrix-pkr-val">{{ $cTripPkr > 0 ? number_format($cTripPkr, 0) . '.' : '0.00' }}</div>
                            <div class="matrix-usd-val">{{ $cTripUsd > 0 ? '$ ' . number_format($cTripUsd, 0) . '.' : '$ 0.00' }}</div>
                        </div>
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">DOUBLE</div>
                            <div class="matrix-pkr-val">{{ $cDoubPkr > 0 ? number_format($cDoubPkr, 0) . '.' : '0.00' }}</div>
                            <div class="matrix-usd-val">{{ $cDoubUsd > 0 ? '$ ' . number_format($cDoubUsd, 0) . '.' : '$ 0.00' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Maktab A Section --}}
                <div class="matrix-half">
                    <div class="matrix-gold-block">
                        <div class="matrix-letter">A</div>
                        <div class="matrix-zone-text">MAKTAB<br>ZONE 1 OR 2</div>
                    </div>
                    <div class="matrix-rates-grid">
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">QUAD / SHARING</div>
                            <div class="matrix-pkr-val">{{ $aQuadPkr > 0 ? number_format($aQuadPkr, 0) . '.' : '0.00' }}</div>
                            <div class="matrix-usd-val">{{ $aQuadUsd > 0 ? '$ ' . number_format($aQuadUsd, 0) . '.' : '$ 0.00' }}</div>
                        </div>
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">TRIPLE</div>
                            <div class="matrix-pkr-val">{{ $aTripPkr > 0 ? number_format($aTripPkr, 0) . '.' : '0.00' }}</div>
                            <div class="matrix-usd-val">{{ $aTripUsd > 0 ? '$ ' . number_format($aTripUsd, 0) . '.' : '$ 0.00' }}</div>
                        </div>
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">DOUBLE</div>
                            <div class="matrix-pkr-val">{{ $aDoubPkr > 0 ? number_format($aDoubPkr, 0) . '.' : '0.00' }}</div>
                            <div class="matrix-usd-val">{{ $aDoubUsd > 0 ? '$ ' . number_format($aDoubUsd, 0) . '.' : '$ 0.00' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. MAIN MIDDLE SECTION --}}
            <div class="main-middle-grid">

                {{-- Left: Stay Cards --}}
                <div class="stay-cards-stack">
                    @php $accList = $package->accommodations; @endphp

                    @if($accList && $accList->count() > 0)
                        @foreach($accList as $acc)
                            <div class="stay-card">
                                <div class="stay-card-head">
                                    @if(stripos($acc->place ?? '', 'makkah') !== false)
                                        <i class="mdi mdi-kaaba"></i>
                                    @elseif(stripos($acc->place ?? '', 'madinah') !== false || stripos($acc->place ?? '', 'medina') !== false)
                                        <i class="mdi mdi-mosque"></i>
                                    @elseif(stripos($acc->place ?? '', 'hajj') !== false || stripos($acc->place ?? '', 'mina') !== false || stripos($acc->place ?? '', 'arafat') !== false)
                                        <i class="mdi mdi-tent"></i>
                                    @else
                                        <i class="mdi mdi-office-building"></i>
                                    @endif
                                    @php
                                        $hotelDisplay = $acc->package_a_hotel ?: ($acc->hotel ?: ($acc->package_b_hotel ?: ''));
                                        $hasHotel = !empty($hotelDisplay) && strcasecmp(trim($hotelDisplay), trim($acc->place ?? '')) !== 0;
                                    @endphp
                                    <span>
                                        @if(!empty($acc->place))
                                            {{ $acc->place }}@if($hasHotel) - {{ $hotelDisplay }}@endif
                                        @elseif(!empty($hotelDisplay))
                                            {{ $hotelDisplay }}
                                        @else
                                            ACCOMMODATION
                                        @endif
                                    </span>
                                </div>
                                <div class="stay-card-body">
                                    <div class="stay-col-item">
                                        <div class="stay-col-label">Arrival</div>
                                        <div class="stay-col-val">{{ $acc->check_in ? \Carbon\Carbon::parse($acc->check_in)->format('d M Y') : '—' }}</div>
                                        <div class="stay-col-sub">{{ $acc->note ?? '' }}</div>
                                    </div>
                                    <div class="stay-col-item">
                                        <div class="stay-col-label">Departure</div>
                                        <div class="stay-col-val">{{ $acc->check_out ? \Carbon\Carbon::parse($acc->check_out)->format('d M Y') : '—' }}</div>
                                        <div class="stay-col-sub">{{ $acc->sharing ?? '' }}</div>
                                    </div>
                                    <div class="stay-col-item">
                                        <div class="stay-col-label">Meal Plan</div>
                                        <div class="stay-col-val">{{ $acc->food_package ?? ($acc->package_a_food_package ?? 'FULL BOARD') }}</div>
                                        <div class="stay-col-sub">{{ $acc->sharing_type ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="stay-card p-3 text-center text-muted">
                            <i class="mdi mdi-information-outline fs-24 mb-1 text-primary"></i>
                            <p class="m-0" style="font-size:11px;font-weight:700;">No accommodation rows added for this package yet.</p>
                        </div>
                    @endif
                </div>

                {{-- Right: 3 Cards Stack --}}
                <div class="right-cards-stack">
                    
                    {{-- 1. AZIZIA SEPARATE ROOM --}}
                    <div class="gold-bordered-card">
                        <div class="gold-pill-header">AZIZIA SEPARATE ROOM</div>
                        
                        <div class="azizia-row-item">
                            <div><span class="azizia-pill-tag">QUAD / SHARING</span></div>
                            <div class="azizia-rate-col">
                                <span class="azizia-pkr-num">{{ $azQuadPkr > 0 ? number_format($azQuadPkr, 0) . '.' : '0.00' }}</span>
                                <span class="azizia-usd-num">Per Person $ {{ $azQuadUsd > 0 ? number_format($azQuadUsd, 0) . '.' : '0.00' }}</span>
                            </div>
                        </div>

                        <div class="azizia-row-item">
                            <div><span class="azizia-pill-tag">TRIPLE</span></div>
                            <div class="azizia-rate-col">
                                <span class="azizia-pkr-num">{{ $azTripPkr > 0 ? number_format($azTripPkr, 0) . '.' : '0.00' }}</span>
                                <span class="azizia-usd-num">Per Person $ {{ $azTripUsd > 0 ? number_format($azTripUsd, 0) . '.' : '0.00' }}</span>
                            </div>
                        </div>

                        <div class="azizia-row-item mb-0">
                            <div><span class="azizia-pill-tag">DOUBLE</span></div>
                            <div class="azizia-rate-col">
                                <span class="azizia-pkr-num">{{ $azDoubPkr > 0 ? number_format($azDoubPkr, 0) . '.' : '0.00' }}</span>
                                <span class="azizia-usd-num">Per Person $ {{ $azDoubUsd > 0 ? number_format($azDoubUsd, 0) . '.' : '0.00' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. DOCUMENT REQUIRED --}}
                    <div class="gold-bordered-card">
                        <div class="gold-pill-header">DOCUMENT REQUIRED</div>
                        @php
                            $docList = [];
                            if (!empty($package->documents_required)) {
                                $docList = array_filter(array_map('trim', explode("\n", $package->documents_required)));
                            }
                        @endphp

                        <ul class="doc-required-list">
                            @if(!empty($docList))
                                @foreach($docList as $dItem)
                                    @php $cleanDoc = ltrim($dItem, "•-*\t✦ "); @endphp
                                    @if(!empty($cleanDoc)) <li>{{ $cleanDoc }}</li> @endif
                                @endforeach
                            @else
                                <li>Passport First Page (Valid for 6+ months)</li>
                                <li>Photograph (White Background)</li>
                                <li>ID Card Copy / NICOP (Nadra)</li>
                                <li>Next of Kin ID Card & Contact Number</li>
                                <li>Passenger Blood Group</li>
                            @endif
                        </ul>
                    </div>

                    {{-- 3. IMPORTANT NOTE --}}
                    <div class="gold-bordered-card">
                        <div class="gold-pill-header">IMPORTANT NOTE</div>
                        <p class="important-note-text">
                            {{ $package->qurbani_note ?: 'Qurbani is not included in the package. Qurbani charges will be added separately as per the applicable charges in the Nusuk Masar system.' }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- 4. INCLUSIONS & INSTRUCTIONS --}}
            <div class="inclusions-instructions-grid">

                <div class="info-card-box">
                    <div class="gold-pill-header">PACKAGE INCLUDED</div>
                    @php
                        $inclusions = [];
                        if (!empty($package->package_included_points) && is_array($package->package_included_points)) {
                            $inclusions = $package->package_included_points;
                        } elseif ($package->terms && !empty($package->terms->content)) {
                            $inclusions = array_filter(array_map('trim', explode("\n", $package->terms->content)));
                        }
                    @endphp

                    <ul class="info-card-list">
                        @if(!empty($inclusions))
                            @foreach($inclusions as $inc)
                                @php $cleanInc = ltrim($inc, "•-*\t✦ "); @endphp
                                @if(!empty($cleanInc)) <li>{{ $cleanInc }}</li> @endif
                            @endforeach
                        @else
                            <li>Meet & Assist Upon Arrival At Airport.</li>
                            <li>Complete Accommodation In Makkah, Azizia, Madinah.</li>
                            <li>Complete Transfer By Air-Conditioned Private Buses.</li>
                            <li>Air fare Include Indirect Flight (Any Airline).</li>
                            <li>Hajj Training Programme Conducted by Renowned Religious Scholars.</li>
                            <li>Madinah Ziyarat By Bus.</li>
                            <li>Gift items will be provided to all Hujjaj.</li>
                        @endif
                    </ul>
                </div>

                <div class="info-card-box">
                    <div class="gold-pill-header">INSTRUCTIONS</div>
                    @php
                        $instructions = [];
                        if (!empty($package->instructions_points) && is_array($package->instructions_points)) {
                            $instructions = $package->instructions_points;
                        } elseif (!empty($package->instructions_points) && is_string($package->instructions_points)) {
                            $instructions = array_filter(array_map('trim', explode("\n", $package->instructions_points)));
                        }
                    @endphp

                    <ul class="info-card-list">
                        @if(!empty($instructions))
                            @foreach($instructions as $inst)
                                @php $cleanInst = ltrim($inst, "•-*\t✦ "); @endphp
                                @if(!empty($cleanInst)) <li>{{ $cleanInst }}</li> @endif
                            @endforeach
                        @else
                            <li>Haram & Kabah View Not Committed.</li>
                            <li>Separate Room Makkah & Madinah Hotel Is Applicable As Per Above Package.</li>
                            <li>Azizia Sharing 5/6 Persons Per Room.</li>
                            <li>Transport Will Not Be Provided For Tawaf-e-Ziyarah.</li>
                            <li>Company Will Not Be Responsible For Any Inconvenience, Loss, or Damage Incurred During Travel.</li>
                            <li>Ziyarat in Package Only for Madinah.</li>
                        @endif
                    </ul>
                </div>

            </div>

            {{-- 5. NOTE CARD --}}
            <div class="note-card-container">
                <span class="note-badge-pill">Note</span>
                <div class="note-bullets-grid">
                    @php
                        $notesList = [];
                        $rawNote = $package->notes ?: ($package->important_note ?: null);
                        if ($rawNote) {
                            $notesList = array_filter(array_map('trim', explode("\n", $rawNote)));
                        }
                    @endphp

                    @if(!empty($notesList))
                        @foreach($notesList as $nItem)
                            @php $cleanNote = ltrim($nItem, "•-*\t "); @endphp
                            @if(!empty($cleanNote)) <div>{{ $cleanNote }}</div> @endif
                        @endforeach
                    @else
                        <div>The above package calculation is based on standard currency conversion rates. Any major change will be adjusted accordingly.</div>
                        <div>During Travel Between Airports and Cities, meals are provided as per airline/transport policy.</div>
                        <div>From 8th to 12th Zilhajj (during Hajj days), meals will be provided at Maktab camps in Mina/Arafat.</div>
                        <div>
                            Payments can only be made into official bank accounts.<br>
                            <strong>No Cash Payments are Accepted.</strong>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 6. OFFICIAL FOOTER --}}
            @php
                $company = $package->company;
                $compAddress = $company && $company->addresses && $company->addresses->count() ? $company->addresses->first()->address : ($company->address ?? 'Office no. 106, Balad Trade Centre, B.M.C.H.S Bahadurabad, Karachi. Pakistan');
                $compPhones = $company && $company->contactNumbers && $company->contactNumbers->count() ? $company->contactNumbers->pluck('contact_number')->join(' | ') : ($company->phone ?? '021-34133006 | 021-34133007 | 0336-2374638');
                $compEmail = $company && $company->emails && $company->emails->count() ? $company->emails->first()->email : ($company->email ?? 'pirwanitravel@gmail.com');
                $compWeb = $company && $company->website ? $company->website : 'pirwanitravels.com';
            @endphp
            <div class="brochure-footer">
                <div class="footer-address">
                    {{ $compAddress }}
                </div>
                <div class="footer-contacts">
                    {{ $compPhones }}
                </div>
                <div class="footer-social-row">
                    <span><i class="mdi mdi-facebook"></i> PIRWANIHAJJGROUP</span>
                    <span><i class="mdi mdi-web"></i> {{ $compWeb }}</span>
                    <span><i class="mdi mdi-email"></i> {{ $compEmail }}</span>
                    <span><i class="mdi mdi-instagram"></i> Pirwanitourism</span>
                </div>
            </div>

        </div>
    </div>

    {{-- Script --}}
    <script>
        async function generatePDF() {
            const btn = document.getElementById('btn-download-pdf');
            const originalContent = btn ? btn.innerHTML : '';
            if (btn) {
                btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Generating PDF...';
                btn.disabled = true;
            }

            const element = document.getElementById('brochure-container');

            try {
                const canvas = await html2canvas(element, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    scrollY: 0,
                    scrollX: 0,
                    backgroundColor: '#FAF5EB',
                    logging: false
                });

                const imgData = canvas.toDataURL('image/jpeg', 1.0);
                const { jsPDF } = window.jspdf;
                const pdfWidth = 210;
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [pdfWidth, pdfHeight]
                });

                pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight, undefined, 'FAST');
                const fileName = 'Pirwani-Hajj-Package-{{ $package->id ?? "brochure" }}-{{ $package->gregorian_year ?? "2027" }}.pdf';
                pdf.save(fileName);
            } catch (err) {
                console.error('Direct PDF error, fallback to html2pdf:', err);
                const opt = {
                    margin: 0,
                    filename: 'Pirwani-Hajj-Package-{{ $package->id ?? "brochure" }}.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2.5, useCORS: true, scrollY: 0 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };
                html2pdf().set(opt).from(element).save().catch(() => {
                    window.print();
                });
            } finally {
                if (btn) {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }
            }
        }
    </script>

</body>
</html>