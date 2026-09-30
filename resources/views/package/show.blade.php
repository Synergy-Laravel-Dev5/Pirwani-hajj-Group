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
            --gold-primary: #C59A27;
            --gold-dark: #9E7412;
            --gold-light: #E7C968;
            --gold-badge: #E8C874;
            --gold-gradient: linear-gradient(180deg, #EAD083 0%, #C99E32 100%);
            --header-wine: #3E0C1F;
            --banner-dark: #220812;
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

        /* ── Top Action Bar (Screen Only) ── */
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
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }
        .btn-action-back:hover {
            background: #193860;
            color: #FFF;
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
            transition: all 0.2s ease;
        }
        .btn-action-pdf:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(201, 168, 76, 0.6);
            color: #000;
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
            transition: all 0.2s ease;
        }
        .btn-action-print:hover {
            background: #FDF9EE;
            color: #000;
        }

        /* ══════════════════════════════════════════════
           BROCHURE PAGE CONTAINER (Exact Replica Layout)
        ══════════════════════════════════════════════ */
        .brochure-page {
            width: 794px;
            max-width: 794px;
            height: auto;
            min-height: auto;
            max-height: none;
            margin: 0 auto;
            background: #FAF6EE radial-gradient(circle at 50% 12%, #FFFFFF 0%, #FAF6EE 50%, #F5EDE1 100%);
            padding: 8px 10px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.45);
            border: 1.5px solid #C59A27;
            overflow: hidden;
        }

        /* Top Background Mosque Arch Texture & Radiant Golden Beams */
        .brochure-page::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 240px;
            background-image: 
                radial-gradient(ellipse at 50% 0%, rgba(255, 255, 255, 0.95) 0%, rgba(232, 200, 116, 0.38) 40%, rgba(250, 246, 238, 0.15) 75%, transparent 100%),
                repeating-conic-gradient(from 0deg at 50% -10px, rgba(201, 168, 76, 0.05) 0deg 8deg, transparent 8deg 16deg);
            pointer-events: none;
            z-index: 1;
        }

        /* Center Holy Watermark Logo & Texture */
        .brochure-page::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 440px;
            height: 440px;
            background-image: url('{{ asset("assets/images/PIRWANI PNG FILE.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.04;
            pointer-events: none;
            z-index: 1;
        }

        .brochure-border-frame {
            position: relative;
            background: transparent;
            box-sizing: border-box;
            z-index: 2;
        }

        /* ══════════════════════════════════════════════
           1. TOP HEADER GRID (3 Columns)
        ══════════════════════════════════════════════ */
        .top-header-grid {
            display: grid;
            grid-template-columns: 195px 1fr 195px;
            align-items: flex-start;
            gap: 4px;
            margin-bottom: 4px;
        }

        /* ── Header Left ── */
        .header-left-col {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .stay-type-pill {
            background: #1F0812;
            color: #FFFFFF;
            border: 1.5px solid #C59A27;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 900;
            letter-spacing: 1px;
            padding: 2px 10px;
            display: inline-block;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .stay-days-large {
            margin-bottom: 2px;
        }
        .stay-num-val {
            font-size: 27px;
            font-weight: 900;
            color: #000000;
            line-height: 1;
            letter-spacing: -0.5px;
        }
        .stay-days-text {
            font-size: 17px;
            font-weight: 900;
            color: #000000;
            line-height: 1;
            letter-spacing: 0.5px;
        }
        .header-route-label {
            font-size: 10px;
            font-weight: 800;
            color: #000000;
            display: flex;
            align-items: center;
            gap: 3px;
            margin-top: 2px;
        }
        .header-route-pill {
            background: var(--gold-badge);
            color: #000000;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9.5px;
            margin: 2px 0;
            display: inline-block;
            white-space: nowrap;
        }
        .header-route-city {
            font-weight: 800;
            color: #222222;
            font-size: 9.5px;
        }

        /* ── Header Center (Logo & Hajj Title) ── */
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
            gap: 8px;
            margin-bottom: 0px;
        }
        .brand-crest-box {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .brand-crest-img {
            height: 42px;
            width: auto;
            max-width: 48px;
            object-fit: contain;
        }
        .brand-gl-number {
            font-family: 'Montserrat', sans-serif;
            font-size: 8px;
            font-weight: 800;
            color: #000000;
            letter-spacing: 0.5px;
            margin-top: 1px;
            white-space: nowrap;
        }
        .brand-name-box {
            text-align: left;
        }
        .brand-name-pirwani {
            font-family: 'Montserrat', sans-serif;
            font-size: 34px;
            font-weight: 900;
            color: #A87E1B;
            line-height: 0.88;
            letter-spacing: -0.5px;
        }
        .brand-name-sub {
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            font-weight: 800;
            color: #000000;
            letter-spacing: 0.5px;
            line-height: 1;
            margin-top: 2px;
        }
        .hajj-main-title {
            font-family: 'Playfair Display', 'Bodoni Moda', 'Cinzel', serif;
            font-size: 62px;
            font-weight: 900;
            letter-spacing: 4.5px;
            color: #1A140F;
            line-height: 0.82;
            margin: 0px 0 2px 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }
        .package-banner-cartouche {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: linear-gradient(180deg, #1C1810 0%, #090805 100%);
            color: #FFFFFF;
            padding: 3px 22px;
            border-radius: 6px;
            border: 1.8px solid #E5C368;
            box-shadow: 0 0 0 1px #8C6615, 0 3px 8px rgba(0, 0, 0, 0.35);
            position: relative;
            margin: 1px 0 2px 0;
        }
        .cartouche-text {
            font-family: 'Cinzel', serif;
            font-size: 13.5px;
            font-weight: 800;
            letter-spacing: 5px;
            color: #FFFFFF;
            text-transform: uppercase;
        }
        .cartouche-flourish {
            color: #E8C874;
            display: inline-flex;
            align-items: center;
            width: 24px;
            height: 14px;
        }
        .hajj-year-text {
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 900;
            color: #000000;
            letter-spacing: 1.5px;
            margin-top: 2px;
        }

        /* ── Header Right ── */
        .header-right-col {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            text-align: right;
        }
        .company-title-text {
            font-size: 9.5px;
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .company-logo-img {
            height: 32px;
            max-width: 140px;
            object-fit: contain;
            margin-bottom: 2px;
        }

        /* ══════════════════════════════════════════════
           2. FULL-WIDTH PRICING MATRIX ROW
        ══════════════════════════════════════════════ */
        .pricing-matrix-container {
            border: 1.8px solid #C59A27;
            background: #FFFDF9;
            margin-bottom: 5px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            box-shadow: 0 1px 4px rgba(197, 154, 39, 0.12);
        }
        .matrix-half {
            display: flex;
            align-items: stretch;
        }
        .matrix-half:first-child {
            border-right: 1.8px solid #C59A27;
        }
        .matrix-gold-block {
            background: var(--gold-badge);
            color: #000000;
            width: 68px;
            min-width: 68px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4px 2px;
            text-align: center;
            border-right: 1.2px solid #C59A27;
        }
        .matrix-letter {
            font-size: 28px;
            font-weight: 900;
            line-height: 1;
            font-family: 'Montserrat', sans-serif;
        }
        .matrix-zone-text {
            font-size: 7.8px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.05;
            margin-top: 1px;
            letter-spacing: -0.2px;
        }
        .matrix-rates-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1.15fr 1fr 1fr;
            align-items: center;
            padding: 4px 2px;
            background: #FFFDF9;
        }
        .matrix-rate-col {
            text-align: center;
            padding: 0 2px;
        }
        .matrix-rate-col:not(:last-child) {
            border-right: 1px solid #EADBBA;
        }
        .matrix-col-title {
            font-size: 8.5px;
            font-weight: 900;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 1px;
            white-space: nowrap;
        }
        .matrix-pkr-val {
            font-size: 13.5px;
            font-weight: 900;
            color: #000000;
            line-height: 1.05;
            letter-spacing: -0.3px;
        }
        .matrix-usd-val {
            font-size: 9.8px;
            font-weight: 800;
            color: #9E7412;
            line-height: 1.1;
        }

        /* ══════════════════════════════════════════════
           3. MAIN MIDDLE SECTION (2-Column Grid Layout)
           Left: 5 Stay Cards (~63%)
           Right: 3 Stacked Cards (~37%)
        ══════════════════════════════════════════════ */
        .main-middle-grid {
            display: grid;
            grid-template-columns: 1fr 275px;
            gap: 6px;
            margin-bottom: 4px;
        }

        /* Left: Stay Cards Stack */
        .stay-cards-stack {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .stay-card {
            border: 1.2px solid #C59A27;
            border-radius: 4px;
            overflow: hidden;
            background: #FFFFFF;
        }
        .stay-card-head {
            background: var(--header-wine);
            color: #FFFFFF;
            font-size: 9.5px;
            font-weight: 900;
            letter-spacing: 0.5px;
            padding: 2.5px 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            text-transform: uppercase;
        }
        .stay-card-head i {
            color: #E5C368;
            font-size: 11px;
        }
        .stay-card-body {
            display: grid;
            grid-template-columns: 1.15fr 1.15fr 1.35fr;
            padding: 2.5px 3px;
            background: #FFFDF9;
            text-align: center;
            align-items: center;
        }
        .stay-col-item {
            padding: 0 3px;
        }
        .stay-col-item:not(:last-child) {
            border-right: 1px solid #EADBBA;
        }
        .stay-col-label {
            font-size: 9px;
            font-weight: 900;
            color: #000000;
            text-transform: uppercase;
            border-bottom: 1px solid #EADBBA;
            padding-bottom: 1px;
            margin-bottom: 1px;
        }
        .stay-col-val {
            font-size: 9.5px;
            font-weight: 800;
            color: #111111;
            line-height: 1.15;
        }
        .stay-col-sub {
            font-size: 8.5px;
            font-weight: 600;
            color: #444444;
            line-height: 1.1;
        }

        /* Right: 3 Stacked Cards */
        .right-cards-stack {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .gold-bordered-card {
            border: 1.4px solid #C59A27;
            border-radius: 8px;
            background: #FFFFFF;
            padding: 4px 6px;
            box-shadow: 0 1px 4px rgba(197, 154, 39, 0.1);
        }
        .gold-pill-header {
            background: var(--gold-badge);
            color: #000000;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.6px;
            text-align: center;
            padding: 2px 8px;
            border-radius: 10px;
            text-transform: uppercase;
            margin-bottom: 3.5px;
        }

        /* Card 1: Azizia Separate Room */
        .azizia-row-item {
            display: grid;
            grid-template-columns: 95px 1fr;
            align-items: center;
            margin-bottom: 3px;
            padding: 0 2px;
        }
        .azizia-pill-tag {
            background: var(--gold-badge);
            color: #000000;
            font-size: 8.2px;
            font-weight: 900;
            padding: 1.5px 4px;
            border-radius: 6px;
            text-align: center;
            text-transform: uppercase;
            display: inline-block;
        }
        .azizia-rate-col {
            text-align: right;
            line-height: 1.1;
        }
        .azizia-pkr-num {
            font-size: 11.8px;
            font-weight: 900;
            color: #000000;
        }
        .azizia-usd-num {
            font-size: 8.8px;
            font-weight: 800;
            color: #9E7412;
            display: block;
        }

        /* Card 2: Document Required */
        .doc-required-list {
            list-style: none;
            padding: 0 2px;
            margin: 0;
        }
        .doc-required-list li {
            position: relative;
            padding-left: 9px;
            margin-bottom: 2px;
            font-size: 8.5px;
            font-weight: 700;
            line-height: 1.22;
            color: #111111;
        }
        .doc-required-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #000000;
            font-weight: 900;
            font-size: 9.5px;
            top: -1px;
        }

        /* Card 3: Important Note */
        .important-note-text {
            font-size: 8.5px;
            font-weight: 600;
            line-height: 1.3;
            color: #111111;
            padding: 0 2px;
            margin: 0;
        }

        /* ══════════════════════════════════════════════
           4. INCLUSIONS & INSTRUCTIONS (2 Equal Columns)
        ══════════════════════════════════════════════ */
        .inclusions-instructions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-bottom: 4px;
        }
        .info-card-box {
            border: 1.4px solid #C59A27;
            border-radius: 8px;
            background: #FFFFFF;
            padding: 5px 7px;
            box-shadow: 0 1px 4px rgba(197, 154, 39, 0.08);
        }
        .info-card-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .info-card-list li {
            position: relative;
            padding-left: 11px;
            margin-bottom: 2.5px;
            font-size: 8.5px;
            font-weight: 600;
            line-height: 1.25;
            color: #111111;
        }
        .info-card-list li::before {
            content: "✦";
            position: absolute;
            left: 0;
            color: #000000;
            font-size: 7.2px;
            top: 0px;
        }

        /* ══════════════════════════════════════════════
           5. NOTE & DISCLAIMER BOX (Directly Underneath)
        ══════════════════════════════════════════════ */
        .note-card-container {
            border: 1.2px solid #E5C975;
            border-radius: 8px;
            background: #FFFDF4;
            padding: 7px 8px 4px 8px;
            position: relative;
            margin-top: 4px;
            margin-bottom: 4px;
            box-shadow: 0 1px 3px rgba(197, 154, 39, 0.08);
        }
        .note-badge-pill {
            position: absolute;
            top: -7px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gold-badge);
            color: #000000;
            font-weight: 900;
            font-size: 8.8px;
            padding: 0px 12px;
            border-radius: 8px;
            text-transform: capitalize;
            border: 1px solid #9E7412;
        }
        .note-bullets-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 10px;
            font-size: 8px;
            font-weight: 600;
            line-height: 1.22;
            color: #222222;
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
            color: #000000;
            font-size: 9.5px;
            top: -1px;
        }

        /* ══════════════════════════════════════════════
           6. OFFICIAL FOOTER (Exact Match to Image)
        ══════════════════════════════════════════════ */
        .brochure-footer {
            border-top: 1.2px solid #C59A27;
            padding-top: 4px;
            text-align: center;
            font-size: 8.5px;
            color: #222222;
            margin-top: auto;
        }
        .footer-address {
            font-weight: 800;
            font-size: 9px;
            color: #000000;
            margin-bottom: 1px;
        }
        .footer-contacts {
            font-weight: 800;
            color: #000000;
            margin-bottom: 2px;
            font-size: 8.8px;
        }
        .footer-social-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            font-size: 8px;
            font-weight: 700;
            color: #333333;
        }
        .footer-social-row span {
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }
        .footer-social-row i {
            color: #000000;
            font-size: 9px;
        }

        /* ── Print Media Optimization ── */
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
                height: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>

<body>

    {{-- Top Action Bar --}}
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

    {{-- ══════════════════════════════════════════════
         MAIN BROCHURE CANVAS (Exact Replica Layout)
    ══════════════════════════════════════════════ --}}
    <div class="brochure-page" id="brochure-container">
        <div class="brochure-border-frame">

            {{-- ── 1. Top Header Grid (3 Columns) ── --}}
            @php
                $stayDur = $package->stay_duration ?: ($package->days ? $package->days . ' DAYS' : '16 - 17 DAYS');
                $parts = explode(' ', trim($stayDur));
                $daysWord = 'DAYS';
                $numPart = $stayDur;
                if (count($parts) > 1 && strtoupper(end($parts)) === 'DAYS') {
                    array_pop($parts);
                    $numPart = implode(' ', $parts);
                }
            @endphp

            <div class="top-header-grid">
                
                {{-- Left: STAY TYPE (Arrow 1) & Departure Date --}}
                <div class="header-left-col">
                    <span class="stay-type-pill">{{ $package->stay_type ?: 'SHORT STAY' }}</span>
                    <div class="stay-days-large">
                        <div class="stay-num-val">{{ $numPart }}</div>
                        <div class="stay-days-text">DAYS</div>
                    </div>
                    <div class="header-route-label">
                        Departure Date: <i class="mdi mdi-airplane-takeoff"></i>
                    </div>
                    <div class="header-route-pill">
                        {{ $package->departure_date_str ?: '07 May / 01 Zil Hajj' }}
                    </div>
                    <div class="header-route-city">
                        {{ $package->departure_sector ?: 'Karachi to Jeddah' }}
                    </div>
                </div>

                {{-- Center: Brand Crest Logo, HAJJ & PACKAGE Banner --}}
                <div class="header-center-col">
                    <div class="brand-top-row">
                        <div class="brand-crest-box">
                            <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Pirwani Crest" class="brand-crest-img">
                            <div class="brand-gl-number">G.L. # 2990</div>
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
                            <path d="M2,7 C6,2 11,1 16,7 C11,13 6,12 2,7 Z M16,7 C21,2 26,1 30,7 C26,12 21,13 16,7 Z M16,4 L18,7 L16,10 L14,7 Z"/>
                            <circle cx="2" cy="7" r="1.5" fill="#E8C874"/>
                            <circle cx="30" cy="7" r="1.5" fill="#E8C874"/>
                        </svg>
                    </div>
                    <div class="hajj-year-text">{{ $package->hijri_year ?: '1448' }} - {{ $package->gregorian_year ?: ($package->year ?: '2027') }}</div>
                </div>

                {{-- Right: Company Logo/Name & Arrival Date --}}
                <div class="header-right-col">
                    @if($package->company && !empty($package->company->company_logo))
                        <img src="{{ asset($package->company->company_logo) }}" alt="{{ $package->company->company_name }}" class="company-logo-img">
                        <div class="company-title-text">{{ $package->company->company_name }}</div>
                    @elseif($package->company && !empty($package->company->company_name))
                        <div class="company-title-text" style="font-size: 13px; font-weight: 900; color: #C28D1B; margin-bottom: 4px;">{{ $package->company->company_name }}</div>
                    @endif

                    <div class="header-route-label">
                        Arrival Date: <i class="mdi mdi-airplane-landing"></i>
                    </div>
                    <div class="header-route-pill">
                        {{ $package->arrival_date_str ?: '22 - 23 May / 16 - 17 Zil Hajj' }}
                    </div>
                    <div class="header-route-city">
                        {{ $package->arrival_sector ?: 'Jeddah/Madinah to Karachi' }}
                    </div>
                </div>

            </div>

            {{-- ── 2. Full-Width Pricing Matrix (Maktab C & Maktab A Side-by-Side) ── --}}
            @php
                // Maktab C rates
                $cQuadPkr = $package->maktab_c_quad_pkr > 0 ? $package->maktab_c_quad_pkr : ($package->adult_pkr > 0 ? $package->adult_pkr : 1845000);
                $cQuadUsd = $package->maktab_c_quad_usd > 0 ? $package->maktab_c_quad_usd : ($package->adult_usd > 0 ? $package->adult_usd : 6709);
                $cTripPkr = $package->maktab_c_triple_pkr > 0 ? $package->maktab_c_triple_pkr : 1900000;
                $cTripUsd = $package->maktab_c_triple_usd > 0 ? $package->maktab_c_triple_usd : 6909;
                $cDoubPkr = $package->maktab_c_double_pkr > 0 ? $package->maktab_c_double_pkr : 1975000;
                $cDoubUsd = $package->maktab_c_double_usd > 0 ? $package->maktab_c_double_usd : 7090;

                // Maktab A rates
                $aQuadPkr = $package->maktab_a_quad_pkr > 0 ? $package->maktab_a_quad_pkr : 2445000;
                $aQuadUsd = $package->maktab_a_quad_usd > 0 ? $package->maktab_a_quad_usd : 8890;
                $aTripPkr = $package->maktab_a_triple_pkr > 0 ? $package->maktab_a_triple_pkr : 2500000;
                $aTripUsd = $package->maktab_a_triple_usd > 0 ? $package->maktab_a_triple_usd : 9090;
                $aDoubPkr = $package->maktab_a_double_pkr > 0 ? $package->maktab_a_double_pkr : 2575000;
                $aDoubUsd = $package->maktab_a_double_usd > 0 ? $package->maktab_a_double_usd : 9363;
            @endphp

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
                            <div class="matrix-pkr-val">{{ number_format($cQuadPkr, 0) }}.</div>
                            <div class="matrix-usd-val">$ {{ number_format($cQuadUsd, 0) }}.</div>
                        </div>
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">TRIPLE</div>
                            <div class="matrix-pkr-val">{{ number_format($cTripPkr, 0) }}.</div>
                            <div class="matrix-usd-val">$ {{ number_format($cTripUsd, 0) }}.</div>
                        </div>
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">DOUBLE</div>
                            <div class="matrix-pkr-val">{{ number_format($cDoubPkr, 0) }}.</div>
                            <div class="matrix-usd-val">$ {{ number_format($cDoubUsd, 0) }}.</div>
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
                            <div class="matrix-pkr-val">{{ number_format($aQuadPkr, 0) }}.</div>
                            <div class="matrix-usd-val">$ {{ number_format($aQuadUsd, 0) }}.</div>
                        </div>
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">TRIPLE</div>
                            <div class="matrix-pkr-val">{{ number_format($aTripPkr, 0) }}.</div>
                            <div class="matrix-usd-val">$ {{ number_format($aTripUsd, 0) }}.</div>
                        </div>
                        <div class="matrix-rate-col">
                            <div class="matrix-col-title">DOUBLE</div>
                            <div class="matrix-pkr-val">{{ number_format($aDoubPkr, 0) }}.</div>
                            <div class="matrix-usd-val">$ {{ number_format($aDoubUsd, 0) }}.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 3. Middle Section (2 Columns: Left ~63%, Right ~37%) ── --}}
            <div class="main-middle-grid">

                {{-- Left Column: 5 Stay Cards Stack --}}
                <div class="stay-cards-stack">
                    @php
                        $accList = $package->accommodations;
                    @endphp

                    @if($accList && $accList->count() > 0)
                        @foreach($accList as $acc)
                            <div class="stay-card">
                                <div class="stay-card-head">
                                    @if(stripos($acc->place ?? '', 'makkah') !== false)
                                        <i class="mdi mdi-kaaba"></i>
                                    @elseif(stripos($acc->place ?? '', 'madinah') !== false || stripos($acc->place ?? '', 'medina') !== false)
                                        <i class="mdi mdi-mosque"></i>
                                    @elseif(stripos($acc->place ?? '', 'hajj') !== false || stripos($acc->place ?? '', 'mina') !== false)
                                        <i class="mdi mdi-tent"></i>
                                    @else
                                        <i class="mdi mdi-office-building"></i>
                                    @endif
                                    @php
                                        $cleanPlace = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($acc->place ?? ''));
                                        $cleanHotel = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($acc->package_a_hotel ?? ''));
                                        $showHotel = !empty($acc->package_a_hotel) && !empty($cleanHotel) && (empty($cleanPlace) || (strpos($cleanPlace, $cleanHotel) === false && strpos($cleanHotel, $cleanPlace) === false));
                                    @endphp
                                    <span>
                                        @if(!empty($acc->place))
                                            {{ $acc->place }}@if($showHotel) - {{ $acc->package_a_hotel }}@endif
                                        @elseif(!empty($acc->package_a_hotel))
                                            {{ $acc->package_a_hotel }}
                                        @else
                                            ACCOMMODATION
                                        @endif
                                    </span>
                                </div>
                                <div class="stay-card-body">
                                    <div class="stay-col-item">
                                        <div class="stay-col-label">Arrival</div>
                                        <div class="stay-col-val">{{ $acc->check_in ? \Carbon\Carbon::parse($acc->check_in)->format('d - M Y') : '07 May 2027' }}</div>
                                        <div class="stay-col-sub">{{ $acc->note ?? '01 Zil Hajj 1448' }}</div>
                                    </div>
                                    <div class="stay-col-item">
                                        <div class="stay-col-label">Departure</div>
                                        <div class="stay-col-val">{{ $acc->check_out ? \Carbon\Carbon::parse($acc->check_out)->format('d - M Y') : '10 May 2027' }}</div>
                                        <div class="stay-col-sub">{{ $acc->sharing ?? '04 Zil Hajj 1448' }}</div>
                                    </div>
                                    <div class="stay-col-item">
                                        <div class="stay-col-label">Meal Plan</div>
                                        <div class="stay-col-val">{{ $acc->food_package ?? ($acc->package_a_food_package ?? 'FULL BOARD') }}</div>
                                        <div class="stay-col-sub">{{ $acc->sharing_type ?? 'Breakfast, Lunch, Dinner Asian Meal' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Segment 1: Makkah Hotel --}}
                        <div class="stay-card">
                            <div class="stay-card-head">
                                <i class="mdi mdi-kaaba"></i>
                                <span>MAKKAH - {{ $package->makkah_type ?: 'SWISSOTEL / SWISS AL MAQAM' }}</span>
                            </div>
                            <div class="stay-card-body">
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Arrival</div>
                                    <div class="stay-col-val">07 May 2027</div>
                                    <div class="stay-col-sub">01 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Departure</div>
                                    <div class="stay-col-val">10 May 2027</div>
                                    <div class="stay-col-sub">04 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Meal Plan</div>
                                    <div class="stay-col-val">HALF BOARD</div>
                                    <div class="stay-col-sub">Breakfast & Dinner Asian Meal</div>
                                </div>
                            </div>
                        </div>

                        {{-- Segment 2: Azizia Building --}}
                        <div class="stay-card">
                            <div class="stay-card-head">
                                <i class="mdi mdi-office-building"></i>
                                <span>AZIZIA - BUILDING</span>
                            </div>
                            <div class="stay-card-body">
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Arrival</div>
                                    <div class="stay-col-val">10 May 2027</div>
                                    <div class="stay-col-sub">04 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Departure</div>
                                    <div class="stay-col-val">13 May 2027</div>
                                    <div class="stay-col-sub">07 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Meal Plan</div>
                                    <div class="stay-col-val">FULL BOARD</div>
                                    <div class="stay-col-sub">Breakfast ,Lunch, Dinner Asian Meal</div>
                                </div>
                            </div>
                        </div>

                        {{-- Segment 3: Hajj Days --}}
                        <div class="stay-card">
                            <div class="stay-card-head">
                                <i class="mdi mdi-tent"></i>
                                <span>HAJJ - DAYS</span>
                            </div>
                            <div class="stay-card-body">
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Arrival</div>
                                    <div class="stay-col-val">14 May 2027</div>
                                    <div class="stay-col-sub">08 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Departure</div>
                                    <div class="stay-col-val">18 May 2027</div>
                                    <div class="stay-col-sub">12 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Meal Plan</div>
                                    <div class="stay-col-val">FULL BOARD</div>
                                    <div class="stay-col-sub">Provided By Maktab</div>
                                </div>
                            </div>
                        </div>

                        {{-- Segment 4: Azizia Building --}}
                        <div class="stay-card">
                            <div class="stay-card-head">
                                <i class="mdi mdi-office-building"></i>
                                <span>AZIZIA - BUILDING</span>
                            </div>
                            <div class="stay-card-body">
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Arrival</div>
                                    <div class="stay-col-val">18 May 2027</div>
                                    <div class="stay-col-sub">12 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Departure</div>
                                    <div class="stay-col-val">20 May 2027</div>
                                    <div class="stay-col-sub">14 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Meal Plan</div>
                                    <div class="stay-col-val">FULL BOARD</div>
                                    <div class="stay-col-sub">Breakfast ,Lunch, Dinner Asian Meal</div>
                                </div>
                            </div>
                        </div>

                        {{-- Segment 5: Madinah Hotel --}}
                        <div class="stay-card">
                            <div class="stay-card-head">
                                <i class="mdi mdi-mosque"></i>
                                <span>MADINAH - {{ $package->medinah_type ?: 'NUSK AL HIJRA' }}</span>
                            </div>
                            <div class="stay-card-body">
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Arrival</div>
                                    <div class="stay-col-val">20 May 2027</div>
                                    <div class="stay-col-sub">14 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Departure</div>
                                    <div class="stay-col-val">22 - 23 May 2027</div>
                                    <div class="stay-col-sub">16 - 17 Zil Hajj 1448</div>
                                </div>
                                <div class="stay-col-item">
                                    <div class="stay-col-label">Meal Plan</div>
                                    <div class="stay-col-val">FULL BOARD</div>
                                    <div class="stay-col-sub">Breakfast ,Lunch, Dinner Asian Meal</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Column: 3 Stacked Cards (Azizia Separate Room, Document Required, Important Note) --}}
                <div class="right-cards-stack">
                    
                    {{-- 1. AZIZIA SEPARATE ROOM CARD --}}
                    <div class="gold-bordered-card">
                        <div class="gold-pill-header">AZIZIA SEPARATE ROOM</div>
                        
                        <div class="azizia-row-item">
                            <div><span class="azizia-pill-tag">QUAD / SHARING</span></div>
                            <div class="azizia-rate-col">
                                <span class="azizia-pkr-num">{{ number_format($package->azizia_quad_pkr ?? 50000, 0) }}.</span>
                                <span class="azizia-usd-num">Per Person $ {{ number_format($package->azizia_quad_usd ?? 181, 0) }}.</span>
                            </div>
                        </div>

                        <div class="azizia-row-item">
                            <div><span class="azizia-pill-tag">TRIPLE</span></div>
                            <div class="azizia-rate-col">
                                <span class="azizia-pkr-num">{{ number_format($package->azizia_triple_pkr ?? 100000, 0) }}.</span>
                                <span class="azizia-usd-num">Per Person $ {{ number_format($package->azizia_triple_usd ?? 363, 0) }}.</span>
                            </div>
                        </div>

                        <div class="azizia-row-item mb-0">
                            <div><span class="azizia-pill-tag">DOUBLE</span></div>
                            <div class="azizia-rate-col">
                                <span class="azizia-pkr-num">{{ number_format($package->azizia_double_pkr ?? 200000, 0) }}.</span>
                                <span class="azizia-usd-num">Per Person $ {{ number_format($package->azizia_double_usd ?? 727, 0) }}.</span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. DOCUMENT REQUIRED CARD --}}
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
                                    @php
                                        $cleanDoc = ltrim($dItem, "•-*\t✦ ");
                                    @endphp
                                    @if(!empty($cleanDoc))
                                        <li>{{ $cleanDoc }}</li>
                                    @endif
                                @endforeach
                            @else
                                <li>Passport First Page (16 NOV, 2027)</li>
                                <li>Photograph ( White Background )</li>
                                <li>ID Card Copy / NICOP (Nadra)</li>
                                <li>Next of Kin ID Card & Contact Number</li>
                                <li>Passenger Blood Group</li>
                            @endif
                        </ul>
                    </div>

                    {{-- 3. IMPORTANT NOTE (QURBANI) CARD --}}
                    <div class="gold-bordered-card">
                        <div class="gold-pill-header">IMPORTANT NOTE</div>
                        <p class="important-note-text">
                            {{ $package->qurbani_note ?: 'Qurbani is not included in the package. Qurbani charges will be added separately as per the applicable charges in the Nusuk Masar system.' }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- ── 4. INCLUSIONS & INSTRUCTIONS (2 Equal Columns) ── --}}
            <div class="inclusions-instructions-grid">

                {{-- Left: Package Included --}}
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
                                @php
                                    $cleanInc = ltrim($inc, "•-*\t✦ ");
                                @endphp
                                @if(!empty($cleanInc))
                                    <li>{{ $cleanInc }}</li>
                                @endif
                            @endforeach
                        @else
                            <li>Meet & Assist Upon Arrival At Airport.</li>
                            <li>Complete Accommodation In Makkah, Azizia, Madinah.</li>
                            <li>Complete Transfer By Air-Conditioned Privete Buses.</li>
                            <li>Air fare Include Indirect Flight. (Any Airline).</li>
                            <li>Hajj Training Programme Conducted by Renowned Religious Scholars.</li>
                            <li>Makkah Madinah Ziyarat By Bus.</li>
                            <li>Gift items will be provided only to Hujjaj travelling from Pakistan.</li>
                        @endif
                    </ul>
                </div>

                {{-- Right: Instructions --}}
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
                                @php
                                    $cleanInst = ltrim($inst, "•-*\t✦ ");
                                @endphp
                                @if(!empty($cleanInst))
                                    <li>{{ $cleanInst }}</li>
                                @endif
                            @endforeach
                        @else
                            <li>Haram & Kabah View Not Committed.</li>
                            <li>Saperate Room Makkah & Madinah Hotel Is Applicable Of Above Package, Separate Double Room Master Beds are Not Committed in any Hotel In Makkah, Madinah, or Azizia Building.</li>
                            <li>Azizia Sharing 5/6 Persons Per Room.</li>
                            <li>Transport Will Not Be Provided TAWAF-EZIYARAH.</li>
                            <li>Company Will Not Be Responsible For Any Inconvenience, Loss, or Damage(s) Incurred During Travel.</li>
                            <li>Ziyarat Not Include Package .</li>
                        @endif
                    </ul>
                </div>

            </div>

            {{-- ── 5. Note Box (Directly Under Inclusions & Instructions) ── --}}
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
                            @php
                                $cleanNote = ltrim($nItem, "•-*\t ");
                            @endphp
                            @if(!empty($cleanNote))
                                <div>{{ $cleanNote }}</div>
                            @endif
                        @endforeach
                    @else
                        <div>The above package calculation is based on a USD Rate of @275. If the USD Rate increases, the difference in rate will be applied.</div>
                        <div>During Travel Between (jed-mak-med-jed Airport / med Airport) Meals are not Provided by the Hajj Company."</div>
                        <div>From 8th to 12th Zilhajj (during Hajj days), meals will not be available at Azizia Building. Meals will be available at Maktab "C" & "A" during this period.</div>
                        <div>
                            Payments can only be made into a bank account.<br>
                            <strong>No Cash Payments are Accepted.</strong>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── 6. Official Footer ── --}}
            <div class="brochure-footer">
                <div class="footer-address">
                    Office no. 106, Balad Trade Centre,B.M.C.H.S Bahadurabad, Karachi.Pakistan,
                </div>
                <div class="footer-contacts">
                    021-34133006 | 021-34133007 | 0336-2374638
                </div>
                <div class="footer-social-row">
                    <span><i class="mdi mdi-facebook"></i> PIRWANIHAJJGROUP</span>
                    <span><i class="mdi mdi-web"></i> pirwanitravels.com</span>
                    <span><i class="mdi mdi-email"></i> pirwanitravel@gmail.com</span>
                    <span><i class="mdi mdi-instagram"></i> Pirwanitourism</span>
                </div>
            </div>

        </div>
    </div>

    {{-- High-Resolution Perfect PDF Generator Script --}}
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
                // High Resolution capture
                const canvas = await html2canvas(element, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    scrollY: 0,
                    scrollX: 0,
                    backgroundColor: '#FAF6EE',
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
                console.error('Direct PDF error, falling back to html2pdf:', err);
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
