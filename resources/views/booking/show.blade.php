<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking — {{ $booking->client->name ?? 'N/A' }} | Maxims Group</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Outfit:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.2.96/css/materialdesignicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --gold: #c9a84c;
            --gold-lt: #e8c96d;
            --gold-dk: #7a5c18;
            --green: #1a9960;
            --red: #c83a3a;
            --text: #1c1508;
            --muted: #7a6e58;
            --border: #ddd0aa;
            --bg: #f2e8d0;
            --card: #fffdf5;
            --head-bg: #faf3dc;
            --strip: #fffef8;
        }

        /* ══════════════════════════════════════
           PRINT
        ══════════════════════════════════════ */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .costing-section.costing-hidden {
                display: none !important;
            }

            @page {
                margin: 8mm 10mm;
            }
        }

        /* ══════════════════════════════════════
           BODY — Warm patterned background
        ══════════════════════════════════════ */
        body {
            background-color: #f2e8d0;
            background-image:
                /* Islamic geometric star pattern */
                radial-gradient(circle at 25px 25px, rgba(201, 168, 76, 0.10) 2px, transparent 2px),
                radial-gradient(circle at 75px 75px, rgba(201, 168, 76, 0.10) 2px, transparent 2px),
                radial-gradient(circle at 75px 25px, rgba(201, 168, 76, 0.06) 1.5px, transparent 1.5px),
                radial-gradient(circle at 25px 75px, rgba(201, 168, 76, 0.06) 1.5px, transparent 1.5px),
                /* diagonal crosshatch */
                repeating-linear-gradient(45deg, rgba(201, 168, 76, 0.04) 0, rgba(201, 168, 76, 0.04) 1px, transparent 1px, transparent 50px),
                repeating-linear-gradient(-45deg, rgba(201, 168, 76, 0.04) 0, rgba(201, 168, 76, 0.04) 1px, transparent 1px, transparent 50px);
            background-size: 100px 100px, 100px 100px, 100px 100px, 100px 100px, 100px 100px, 100px 100px;
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════
           HERO
        ══════════════════════════════════════ */
        .hero {
            position: relative;
            min-height: 230px;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            background: linear-gradient(145deg, #0c0902 0%, #1a1205 45%, #231808 75%, #160f03 100%);
        }

        .hero-tex {
            position: absolute;
            inset: 0;
            background-image:
                repeating-linear-gradient(45deg, rgba(201, 168, 76, 0.04) 0, rgba(201, 168, 76, 0.04) 1px, transparent 1px, transparent 28px),
                repeating-linear-gradient(-45deg, rgba(201, 168, 76, 0.04) 0, rgba(201, 168, 76, 0.04) 1px, transparent 1px, transparent 28px);
        }

        .hero-glow {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 80% at 10% 70%, rgba(201, 168, 76, 0.14) 0%, transparent 60%),
                radial-gradient(ellipse 35% 45% at 85% 25%, rgba(201, 168, 76, 0.07) 0%, transparent 55%);
        }

        .hero-kaaba {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.18;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 20px 32px 22px;
            width: 100%;
        }

        .hero-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* ── LOGO ── */
        .hero-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-img-box {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(201, 168, 76, 0.25);
            border-radius: 10px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .logo-img-box img {
            height: 48px;
            width: auto;
            max-width: 140px;
            object-fit: contain;
            display: block;
            filter: brightness(1.15) drop-shadow(0 2px 8px rgba(201, 168, 76, 0.4));
        }

        .logo-text-wrap {
            display: flex;
            flex-direction: column;
        }

        .logo-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--gold-lt);
            line-height: 1.1;
            letter-spacing: .3px;
        }

        .logo-sub {
            font-size: 9px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(201, 168, 76, 0.48);
            margin-top: 2px;
        }

        /* ── BUTTONS ── */
        .hero-btns {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            border: none;
            white-space: nowrap;
            letter-spacing: .2px;
            transition: all .18s;
        }

        .btn-pdf {
            background: linear-gradient(135deg, #d4a830 0%, #a07010 100%);
            color: #18100000;
            color: #1a0d00;
            box-shadow: 0 4px 16px rgba(201, 168, 76, 0.38);
        }

        .btn-pdf:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .btn-toggle {
            background: rgba(255, 255, 255, 0.07);
            color: rgba(255, 255, 255, 0.70);
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .btn-toggle:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        /* ── HERO META ── */
        .hero-meta {
            display: flex;
            align-items: flex-end;
            gap: 14px;
        }

        .hero-avatar {
            width: 56px;
            height: 56px;
            border-radius: 13px;
            background: linear-gradient(135deg, var(--gold), #6a4408);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 6px 20px rgba(201, 168, 76, 0.30);
        }

        .hero-info h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 27px;
            font-weight: 700;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 3px;
        }

        .hero-company {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.38);
            margin-bottom: 9px;
        }

        .badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .bk-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 11px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        /* ══════════════════════════════════════
           STATS STRIP
        ══════════════════════════════════════ */
        .stats-strip {
            background: var(--strip);
            border-bottom: 1.5px solid var(--border);
            display: flex;
            overflow-x: auto;
        }

        .stat-item {
            padding: 14px 22px;
            border-right: 1px solid var(--border);
            flex: 1;
            min-width: 110px;
            text-align: center;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-label {
            font-size: 9px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 3px;
        }

        .stat-value {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 700;
        }

        .stat-sub {
            font-size: 9px;
            color: var(--muted);
            margin-top: 1px;
        }

        /* ══════════════════════════════════════
           PROGRESS
        ══════════════════════════════════════ */
        .progress-wrap {
            padding: 11px 32px;
            background: var(--strip);
            border-bottom: 1.5px solid var(--border);
        }

        .progress-labels {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .progress-track {
            height: 6px;
            background: #e0d4b0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--gold), var(--green));
        }

        /* ══════════════════════════════════════
           MAIN BODY
        ══════════════════════════════════════ */
        .main-body {
            padding: 24px 32px 60px;
        }

        .section-label {
            font-size: 9.5px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1.8px;
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, var(--border), transparent);
        }

        .info-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 2px 16px rgba(100, 70, 10, 0.07), 0 1px 4px rgba(100, 70, 10, 0.04);
        }

        .info-card:last-child {
            margin-bottom: 0;
        }

        .row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .row-2col .info-card {
            margin-bottom: 0;
        }

        .card-head {
            padding: 11px 18px;
            background: var(--head-bg);
            border-bottom: 1px solid var(--border);
            font-size: 10.5px;
            font-weight: 700;
            color: var(--gold-dk);
            text-transform: uppercase;
            letter-spacing: 1.3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-head .mdi {
            color: var(--gold);
            font-size: 15px;
        }

        .count-pill {
            margin-left: auto;
            background: rgba(201, 168, 76, 0.13);
            color: var(--gold-dk);
            border: 1px solid rgba(201, 168, 76, 0.30);
            border-radius: 12px;
            padding: 1px 10px;
            font-size: 10px;
            font-weight: 600;
        }

        /* ══════════════════════════════════════
           INFO TABLE — label | value (proper 2-col)
        ══════════════════════════════════════ */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table tr {
            border-bottom: 1px solid #f0e5c8;
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        .info-table tr:hover {
            background: #fdf7e8;
        }

        .info-table td {
            padding: 10px 18px;
            font-size: 13px;
            vertical-align: middle;
        }

        /* LABEL column */
        .info-table td:first-child {
            width: 38%;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            border-right: 1px solid #f0e5c8;
            background: rgba(250, 243, 220, 0.5);
        }

        /* VALUE column */
        .info-table td:last-child {
            color: var(--text);
            font-weight: 400;
            padding-left: 20px;
        }

        /* ══════════════════════════════════════
           DATA TABLE — multi-column
        ══════════════════════════════════════ */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .data-table thead tr {
            border-bottom: 2px solid var(--border);
        }

        .data-table thead th {
            padding: 10px 14px;
            font-size: 10px;
            font-weight: 700;
            color: var(--gold-dk);
            text-transform: uppercase;
            letter-spacing: .6px;
            background: var(--head-bg);
            white-space: nowrap;
            text-align: left;
        }

        .data-table thead th:first-child {
            text-align: center;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #f0e5c8;
        }

        .data-table tbody tr:last-child {
            border-bottom: none;
        }

        .data-table tbody tr:hover td {
            background: #fdf7e8;
        }

        .data-table tbody td {
            padding: 10px 14px;
            vertical-align: middle;
            color: var(--text);
        }

        .data-table tbody td:first-child {
            text-align: center;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* ══════════════════════════════════════
           FLIGHT ROUTE
        ══════════════════════════════════════ */
        .flight-route {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            background: linear-gradient(90deg, #fdf6e3, #faf0cc, #fdf6e3);
            border-bottom: 1px solid var(--border);
        }

        .flight-city .city-code {
            font-family: 'Cormorant Garamond', serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: 1px;
        }

        .flight-city .city-date {
            font-size: 10.5px;
            color: var(--muted);
            margin-top: 2px;
        }

        .flight-arrow {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .flight-line {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .flight-city.right {
            text-align: right;
        }

        /* ══════════════════════════════════════
           COSTING
        ══════════════════════════════════════ */
        .cost-card {
            background: linear-gradient(135deg, #fefaf0 0%, #f8f0d8 100%);
            border-color: rgba(201, 168, 76, 0.32) !important;
        }

        .cost-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cost-table tr {
            border-bottom: 1px solid #ece0b8;
        }

        .cost-table tr:last-child {
            border-bottom: none;
        }

        .cost-table td {
            padding: 11px 18px;
            font-size: 13px;
            color: var(--text);
        }

        /* label col */
        .cost-table td:first-child {
            font-weight: 500;
            width: 50%;
        }

        /* detail col */
        .cost-table td:nth-child(2) {
            text-align: right;
            color: var(--muted);
            font-size: 11px;
        }

        /* amount col */
        .cost-table td:last-child {
            text-align: right;
            font-weight: 600;
            width: 25%;
        }

        .cost-total td {
            background: rgba(201, 168, 76, 0.10) !important;
            border-top: 1px solid rgba(201, 168, 76, 0.30) !important;
            border-bottom: 1px solid rgba(201, 168, 76, 0.30) !important;
            font-weight: 700 !important;
        }

        /* Costing toggle */
        .costing-section.costing-hidden .cost-table {
            display: none;
        }

        .cost-hidden-msg {
            display: none;
            padding: 32px 18px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }

        .costing-section.costing-hidden .cost-hidden-msg {
            display: block;
        }

        /* ══════════════════════════════════════
           TRANSPORT/VISA ROW
        ══════════════════════════════════════ */
        .row-tv {
            display: grid;
            gap: 16px;
            margin-bottom: 16px;
        }

        .row-tv.both {
            grid-template-columns: 1fr 1fr;
        }

        .row-tv .info-card {
            margin-bottom: 0;
        }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 860px) {

            .row-2col,
            .row-tv.both {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 580px) {

            .hero-content,
            .progress-wrap,
            .main-body {
                padding-left: 14px;
                padding-right: 14px;
            }

            .stat-item {
                padding: 12px 10px;
            }
        }

        /* PDF loading overlay */
        #pdf-loading {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 8, 3, 0.75);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 14px;
        }

        #pdf-loading.show {
            display: flex;
        }

        .pdf-spinner {
            width: 44px;
            height: 44px;
            border: 3px solid rgba(201, 168, 76, 0.2);
            border-top: 3px solid var(--gold);
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .pdf-loading-text {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: rgba(201, 168, 76, 0.8);
            letter-spacing: .5px;
        }
    </style>
</head>

<body>

    {{-- PDF Loading Overlay --}}
    <div id="pdf-loading">
        <div class="pdf-spinner"></div>
        <div class="pdf-loading-text">Preparing PDF…</div>
    </div>

    {{-- ══════════════ HERO ══════════════ --}}
    <div class="hero" id="page-top">
        <div class="hero-tex"></div>
        <div class="hero-glow"></div>

        <div class="hero-kaaba">
            <svg viewBox="0 0 320 450" xmlns="http://www.w3.org/2000/svg" fill="none" width="300">
                <ellipse cx="160" cy="432" rx="140" ry="14" fill="rgba(201,168,76,0.2)" />
                <rect x="14" y="168" width="28" height="264" fill="rgba(201,168,76,0.52)" rx="3" />
                <rect x="4" y="148" width="48" height="28" fill="rgba(201,168,76,0.42)" rx="3" />
                <ellipse cx="28" cy="143" rx="20" ry="13" fill="rgba(201,168,76,0.48)" />
                <line x1="28" y1="130" x2="28" y2="92" stroke="rgba(201,168,76,0.72)"
                    stroke-width="2.5" />
                <polygon points="28,76 35,92 21,92" fill="rgba(201,168,76,0.88)" />
                <circle cx="28" cy="70" r="5" fill="rgba(201,168,76,0.6)" />
                <rect x="19" y="188" width="13" height="20" fill="rgba(201,168,76,0.22)" rx="6" />
                <rect x="19" y="224" width="13" height="20" fill="rgba(201,168,76,0.22)" rx="6" />
                <rect x="19" y="260" width="13" height="20" fill="rgba(201,168,76,0.22)" rx="6" />
                <rect x="278" y="168" width="28" height="264" fill="rgba(201,168,76,0.52)" rx="3" />
                <rect x="268" y="148" width="48" height="28" fill="rgba(201,168,76,0.42)" rx="3" />
                <ellipse cx="292" cy="143" rx="20" ry="13" fill="rgba(201,168,76,0.48)" />
                <line x1="292" y1="130" x2="292" y2="92" stroke="rgba(201,168,76,0.72)"
                    stroke-width="2.5" />
                <polygon points="292,76 299,92 285,92" fill="rgba(201,168,76,0.88)" />
                <circle cx="292" cy="70" r="5" fill="rgba(201,168,76,0.6)" />
                <rect x="288" y="188" width="13" height="20" fill="rgba(201,168,76,0.22)" rx="6" />
                <rect x="288" y="224" width="13" height="20" fill="rgba(201,168,76,0.22)" rx="6" />
                <rect x="288" y="260" width="13" height="20" fill="rgba(201,168,76,0.22)" rx="6" />
                <rect x="88" y="200" width="144" height="222" fill="rgba(201,168,76,0.09)"
                    stroke="rgba(201,168,76,0.82)" stroke-width="2.5" rx="3" />
                <rect x="88" y="200" width="15" height="222" fill="rgba(201,168,76,0.14)" rx="2" />
                <rect x="217" y="200" width="15" height="222" fill="rgba(201,168,76,0.14)" rx="2" />
                <rect x="88" y="244" width="144" height="38" fill="rgba(201,168,76,0.26)"
                    stroke="rgba(201,168,76,0.55)" stroke-width="1.5" />
                <line x1="102" y1="252" x2="218" y2="252" stroke="rgba(201,168,76,0.62)"
                    stroke-width="1" />
                <line x1="102" y1="258" x2="218" y2="258" stroke="rgba(201,168,76,0.42)"
                    stroke-width="0.8" />
                <line x1="102" y1="264" x2="218" y2="264" stroke="rgba(201,168,76,0.35)"
                    stroke-width="0.8" />
                <line x1="102" y1="270" x2="218" y2="270" stroke="rgba(201,168,76,0.42)"
                    stroke-width="0.8" />
                <rect x="132" y="322" width="56" height="98" fill="rgba(201,168,76,0.16)"
                    stroke="rgba(201,168,76,0.62)" stroke-width="2" rx="3" />
                <rect x="139" y="329" width="20" height="40" fill="rgba(201,168,76,0.12)" rx="2" />
                <rect x="161" y="329" width="20" height="40" fill="rgba(201,168,76,0.12)" rx="2" />
                <circle cx="160" cy="378" r="3.5" fill="rgba(201,168,76,0.5)" />
                <path d="M128,188 Q160,155 192,188" stroke="rgba(201,168,76,0.72)" stroke-width="2" fill="none" />
                <line x1="160" y1="155" x2="160" y2="118" stroke="rgba(201,168,76,0.75)"
                    stroke-width="2.5" />
                <path d="M153,111 Q160,98 167,111 Q160,104 153,111Z" fill="rgba(201,168,76,0.88)" />
                <circle cx="60" cy="55" r="1.8" fill="rgba(201,168,76,0.48)" />
                <circle cx="255" cy="32" r="1.8" fill="rgba(201,168,76,0.48)" />
                <circle cx="118" cy="24" r="2.2" fill="rgba(201,168,76,0.38)" />
                <circle cx="210" cy="60" r="1.5" fill="rgba(201,168,76,0.32)" />
            </svg>
        </div>

        <div class="hero-content">
            <div class="hero-topbar">

                {{-- ── LOGO ── --}}
                <div class="hero-logo-wrap">
                    <div class="logo-img-box">
                        <img src="{{ asset('assets/images/logo1.png') }}" alt="Maxims Group"
                            onerror="this.parentElement.style.display='none'">
                    </div>
                    <div class="logo-text-wrap">
                        <div class="logo-name">Maxims Group Umrah</div>
                        <div class="logo-sub">Booking Detail</div>
                    </div>
                </div>

                <div class="hero-btns no-print">
                    <button class="btn btn-pdf" onclick="downloadPDF()">
                        <i class="mdi mdi-file-download-outline"></i> Download PDF
                    </button>
                    <button class="btn btn-toggle" id="toggleCostBtn" onclick="toggleCosting()">
                        <i class="mdi mdi-eye-off-outline" id="toggleCostIcon"></i>
                        <span id="toggleCostText">Hide Costing</span>
                    </button>
                </div>
            </div>

            <div class="hero-meta">
                <div class="hero-avatar">
                    {{ strtoupper(substr($booking->client->name ?? 'U', 0, 1)) }}
                </div>
                <div class="hero-info">
                    <h1>{{ $booking->client->name ?? 'N/A' }}</h1>
                    @if ($booking->client->company_name ?? false)
                        <div class="hero-company">{{ $booking->client->company_name }}</div>
                    @endif
                    <div class="badge-row">
                        @php
                            $pkgC = [
                                'umrah' => ['rgba(46,204,138,0.18)', 'rgba(46,204,138,0.4)', '#12845e'],
                                'hajj' => ['rgba(224,155,60,0.18)', 'rgba(224,155,60,0.4)', '#8a5c10'],
                                'other' => ['rgba(255,255,255,0.1)', 'rgba(255,255,255,0.2)', 'rgba(255,255,255,0.62)'],
                            ][$booking->package_type] ?? [
                                'rgba(255,255,255,0.1)',
                                'rgba(255,255,255,0.2)',
                                'rgba(255,255,255,0.62)',
                            ];
                            $stC = [
                                'pending' => ['rgba(224,155,60,0.18)', 'rgba(224,155,60,0.4)', '#8a5c10'],
                                'confirmed' => ['rgba(46,204,138,0.18)', 'rgba(46,204,138,0.4)', '#12845e'],
                                'cancelled' => ['rgba(224,92,92,0.18)', 'rgba(224,92,92,0.4)', '#a02828'],
                            ][$booking->status] ?? [
                                'rgba(255,255,255,0.1)',
                                'rgba(255,255,255,0.2)',
                                'rgba(255,255,255,0.62)',
                            ];
                        @endphp
                        <span class="bk-badge"
                            style="background:{{ $pkgC[0] }};border:1px solid {{ $pkgC[1] }};color:{{ $pkgC[2] }}">
                            <i class="mdi mdi-kaaba"></i>
                            {{ ucfirst($booking->package_type) }}@if ($booking->package_year)
                                — {{ $booking->package_year }}
                            @endif
                        </span>
                        <span class="bk-badge"
                            style="background:{{ $stC[0] }};border:1px solid {{ $stC[1] }};color:{{ $stC[2] }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                        @if ($booking->package_name)
                            <span class="bk-badge"
                                style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.13);color:rgba(255,255,255,0.52)">
                                {{ $booking->package_name }}
                            </span>
                        @endif
                        <span class="bk-badge"
                            style="background:rgba(201,168,76,0.14);border:1px solid rgba(201,168,76,0.32);color:#c9a84c">
                            <i class="mdi mdi-account-group"></i> {{ $booking->no_of_pax }} Pax
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════ STATS ══════════════ --}}
    @php
        $percent = $booking->total_amount > 0 ? min(100, ($booking->total_received / $booking->total_amount) * 100) : 0;
    @endphp

    <div class="stats-strip">
        <div class="stat-item">
            <div class="stat-label">Total Amount</div>
            <div class="stat-value" style="color:var(--gold)">{{ number_format($booking->total_amount, 0) }}</div>
            <div class="stat-sub">PKR</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Received</div>
            <div class="stat-value" style="color:var(--green)">{{ number_format($booking->total_received, 0) }}</div>
            <div class="stat-sub">PKR</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Balance</div>
            <div class="stat-value" style="color:{{ $booking->balance > 0 ? 'var(--red)' : 'var(--green)' }}">
                {{ number_format($booking->balance, 0) }}</div>
            <div class="stat-sub">PKR</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Pkg Cost</div>
            <div class="stat-value" style="color:var(--gold)">{{ number_format($booking->package_cost, 0) }}</div>
            <div class="stat-sub">per person</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Pax</div>
            <div class="stat-value" style="color:var(--text)">{{ $booking->no_of_pax }}</div>
            <div class="stat-sub">persons</div>
        </div>
    </div>

    <div class="progress-wrap">
        <div class="progress-labels">
            <span>Payment Progress</span>
            <span style="color:var(--gold-dk);font-weight:600">{{ number_format($percent, 0) }}% Paid</span>
        </div>
        <div class="progress-track">
            <div class="progress-fill" style="width:{{ $percent }}%"></div>
        </div>
    </div>

    {{-- ══════════════ MAIN BODY ══════════════ --}}
    <div class="main-body" id="pdf-content">

        <div class="section-label">Traveller Information</div>

        <div class="row-2col">
            {{-- Booking Details --}}
            <div class="info-card">
                <div class="card-head"><i class="mdi mdi-card-account-details"></i> Booking Details</div>
                <table class="info-table">
                    <tr>
                        <td>Passport #</td>
                        <td>{{ $booking->passport_number ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>CNIC</td>
                        <td>{{ $booking->cnic ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Care Of</td>
                        <td>{{ $booking->care_of ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>{{ $booking->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Emergency</td>
                        <td>{{ $booking->emergency_phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Voucher #</td>
                        <td><strong style="color:var(--gold-dk)">{{ $booking->voucher_number ?? '—' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Card #</td>
                        <td>{{ $booking->card_number ?? '—' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Flight Details --}}
            <div class="info-card">
                <div class="card-head"><i class="mdi mdi-airplane"></i> Flight Details</div>
                @if ($booking->departure_date || $booking->arrival_date)
                    <div class="flight-route">
                        <div class="flight-city">
                            <div class="city-code">{{ $booking->departure_flight ?? 'DEP' }}</div>
                            <div class="city-date">
                                {{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d M Y') : '—' }}
                                @if ($booking->departure_time)
                                    · {{ $booking->departure_time }}
                                @endif
                            </div>
                        </div>
                        <div class="flight-arrow">
                            <i class="mdi mdi-airplane-takeoff" style="color:var(--gold);font-size:18px"></i>
                            <div class="flight-line"></div>
                            <div style="font-size:10px;color:var(--muted)">{{ $booking->airline ?? '' }}</div>
                        </div>
                        <div class="flight-city right">
                            <div class="city-code">{{ $booking->arrival_flight ?? 'ARR' }}</div>
                            <div class="city-date">
                                {{ $booking->arrival_date ? \Carbon\Carbon::parse($booking->arrival_date)->format('d M Y') : '—' }}
                                @if ($booking->arrival_time)
                                    · {{ $booking->arrival_time }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                <table class="info-table">
                    <tr>
                        <td>Dep. Airline</td>
                        <td>{{ $booking->departure_airline ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Dep. Flight #</td>
                        <td>{{ $booking->departure_flight ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Dep. PNR</td>
                        <td><strong style="color:var(--gold-dk)">{{ $booking->departure_pnr ?? '—' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Departure</td>
                        <td>{{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d M Y') : '—' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Arr. Airline</td>
                        <td>{{ $booking->arrival_airline ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Arr. Flight #</td>
                        <td>{{ $booking->arrival_flight ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Arr. PNR</td>
                        <td><strong style="color:var(--gold-dk)">{{ $booking->arrival_pnr ?? '—' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Return</td>
                        <td>{{ $booking->arrival_date ? \Carbon\Carbon::parse($booking->arrival_date)->format('d M Y') : '—' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Travelling Persons --}}
        @if ($booking->persons->count())
            <div class="info-card">
                <div class="card-head">
                    <i class="mdi mdi-account-group"></i> Travelling Persons
                    <span class="count-pill">{{ $booking->persons->count() }}</span>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:48px">#</th>
                            <th>Full Name</th>
                            <th>Passport #</th>
                            <th>CNIC</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($booking->persons as $person)
                            <tr>
                                <td><span class="pill"
                                        style="background:rgba(201,168,76,0.12);color:var(--gold-dk);border:1px solid rgba(201,168,76,0.28)">{{ $loop->iteration }}</span>
                                </td>
                                <td><strong style="color:var(--text)">{{ $person->full_name }}</strong></td>
                                <td>{{ $person->passport_number ?? '—' }}</td>
                                <td>{{ $person->cnic ?? '—' }}</td>
                                <td>{{ $person->phone ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="section-label">Accommodation & Services</div>

        {{-- Accommodation --}}
        @if ($booking->hotels->count())
            <div class="info-card">
                <div class="card-head">
                    <i class="mdi mdi-hotel"></i> Accommodation
                    <span class="count-pill">{{ $booking->hotels->count() }}</span>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Hotel Name</th>
                            <th style="text-align:center">Nights</th>
                            <th>Room Type</th>
                            <th style="text-align:center">Rooms</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($booking->hotels as $hotel)
                            @php
                                $lc = [
                                    'makkah' => ['rgba(201,168,76,0.12)', 'rgba(201,168,76,0.32)', '#7a5c18'],
                                    'madinah' => ['rgba(46,204,138,0.12)', 'rgba(46,204,138,0.32)', '#12845e'],
                                    'other' => ['rgba(0,0,0,0.03)', 'rgba(0,0,0,0.08)', 'var(--muted)'],
                                ][$hotel->location] ?? ['rgba(0,0,0,0.03)', 'rgba(0,0,0,0.08)', 'var(--muted)'];
                            @endphp
                            <tr>
                                <td><span class="pill"
                                        style="background:{{ $lc[0] }};border:1px solid {{ $lc[1] }};color:{{ $lc[2] }}">{{ ucfirst($hotel->location) }}</span>
                                </td>
                                <td><strong style="color:var(--text)">{{ $hotel->hotel_name }}</strong></td>
                                <td style="text-align:center">{{ $hotel->no_of_nights }}</td>
                                <td>{{ ucfirst($hotel->room_type) }}</td>
                                <td style="text-align:center">{{ $hotel->no_of_rooms }}</td>
                                <td>{{ $hotel->check_in ? \Carbon\Carbon::parse($hotel->check_in)->format('d M Y') : '—' }}
                                </td>
                                <td>{{ $hotel->check_out ? \Carbon\Carbon::parse($hotel->check_out)->format('d M Y') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Transport + Visa --}}
        @if ($booking->transports->count() || $booking->visas->count())
            <div class="row-tv {{ $booking->transports->count() && $booking->visas->count() ? 'both' : '' }}">

                @if ($booking->transports->count())
                    <div class="info-card">
                        <div class="card-head"><i class="mdi mdi-bus"></i> Transportation <span
                                class="count-pill">{{ $booking->transports->count() }}</span></div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Type</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->transports as $t)
                                    <tr>
                                        <td>{{ $t->route }}</td>
                                        <td><span class="pill"
                                                style="background:rgba(120,90,20,0.07);color:var(--muted);border:1px solid var(--border)">{{ ucfirst(str_replace('_', ' ', $t->transport_type)) }}</span>
                                        </td>
                                        <td>{{ $t->notes ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ($booking->visas->count())
                    <div class="info-card">
                        <div class="card-head"><i class="mdi mdi-passport"></i> Visa Details <span
                                class="count-pill">{{ $booking->visas->count() }}</span></div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Passport #</th>
                                    <th>DOB</th>
                                    <th>Send To</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->visas as $visa)
                                    @php
                                        $vc = [
                                            'pending' => ['rgba(224,155,60,0.12)', 'rgba(224,155,60,0.32)', '#8a5c10'],
                                            'submitted' => [
                                                'rgba(201,168,76,0.12)',
                                                'rgba(201,168,76,0.32)',
                                                '#7a5c18',
                                            ],
                                            'approved' => ['rgba(46,204,138,0.12)', 'rgba(46,204,138,0.32)', '#12845e'],
                                            'rejected' => ['rgba(224,92,92,0.12)', 'rgba(224,92,92,0.32)', '#a02828'],
                                        ][$visa->status] ?? ['rgba(0,0,0,0.03)', 'rgba(0,0,0,0.08)', 'var(--muted)'];
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong style="color:var(--text)">{{ $visa->given_name ?? '—' }}</strong>
                                        </td>
                                        <td>{{ $visa->passport_number ?? '—' }}</td>
                                        <td>{{ $visa->date_of_birth ? \Carbon\Carbon::parse($visa->date_of_birth)->format('d M Y') : '—' }}
                                        </td>
                                        <td>{{ ucfirst($visa->send_to ?? '—') }}</td>
                                        <td><span class="pill"
                                                style="background:{{ $vc[0] }};border:1px solid {{ $vc[1] }};color:{{ $vc[2] }}">{{ ucfirst($visa->status) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        @endif

        <div class="section-label">Financial Summary</div>

        {{-- Costing --}}
        <div class="info-card cost-card costing-section" id="costingSection">
            <div class="card-head" style="background:rgba(201,168,76,0.10);border-color:rgba(201,168,76,0.28)">
                <i class="mdi mdi-cash-multiple"></i> Costing Summary
            </div>
            <table class="cost-table">
                <tr>
                    <td>Package Cost</td>
                    <td>PKR {{ number_format($booking->package_cost, 0) }} × {{ $booking->no_of_pax }} pax</td>
                    <td>PKR {{ number_format($booking->package_cost * $booking->no_of_pax, 0) }}</td>
                </tr>
                <tr>
                    <td>Visa Charges</td>
                    <td></td>
                    <td>PKR {{ number_format($booking->visa_charges, 0) }}</td>
                </tr>
                <tr>
                    <td>Flight Charges</td>
                    <td></td>
                    <td>PKR {{ number_format($booking->flight_charges, 0) }}</td>
                </tr>
                <tr>
                    <td>Other Charges</td>
                    <td></td>
                    <td>PKR {{ number_format($booking->other_charges, 0) }}</td>
                </tr>
                <tr class="cost-total">
                    <td style="color:var(--gold-dk)"><strong>Total Amount</strong></td>
                    <td></td>
                    <td style="color:var(--gold-dk)"><strong>PKR
                            {{ number_format($booking->total_amount, 0) }}</strong></td>
                </tr>
                <tr>
                    <td style="color:var(--green);font-weight:600">Total Received</td>
                    <td></td>
                    <td style="color:var(--green);font-weight:600">PKR
                        {{ number_format($booking->total_received, 0) }}</td>
                </tr>
                <tr class="cost-total">
                    <td style="color:{{ $booking->balance > 0 ? 'var(--red)' : 'var(--green)' }};font-weight:700">
                        Balance Due</td>
                    <td></td>
                    <td
                        style="color:{{ $booking->balance > 0 ? 'var(--red)' : 'var(--green)' }};font-size:15px;font-weight:700">
                        PKR {{ number_format($booking->balance, 0) }}
                    </td>
                </tr>
            </table>
            <div class="cost-hidden-msg">
                <i class="mdi mdi-eye-off-outline"
                    style="font-size:32px;color:var(--border);display:block;margin-bottom:8px"></i>
                Costing is hidden — will not appear in PDF
            </div>
        </div>

    </div>{{-- /main-body --}}

    <script>
        /* ── Costing Toggle ── */
        var costingVisible = true;

        function toggleCosting() {
            var s = document.getElementById('costingSection');
            var i = document.getElementById('toggleCostIcon');
            var t = document.getElementById('toggleCostText');
            if (costingVisible) {
                s.classList.add('costing-hidden');
                i.className = 'mdi mdi-eye-outline';
                t.textContent = 'Show Costing';
            } else {
                s.classList.remove('costing-hidden');
                i.className = 'mdi mdi-eye-off-outline';
                t.textContent = 'Hide Costing';
            }
            costingVisible = !costingVisible;
        }

        /* ── PDF Download (html2pdf) ── */
        function downloadPDF() {
            // var overlay = document.getElementById('pdf-loading');
            // overlay.classList.add('show');

            /* Hide buttons during capture */
            var btns = document.querySelectorAll('.no-print');
            btns.forEach(function(b) {
                b.style.display = 'none';
            });

            var element = document.getElementById('page-top').parentElement;

            var clientName = '{{ addslashes($booking->client->name ?? 'booking') }}';
            var filename = 'Gulf-Umrah-' + clientName.replace(/\s+/g, '-') + '.pdf';

            var opt = {
                margin: [6, 6, 6, 6],
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 0.97
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#f2e8d0',
                    logging: false
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                },
                pagebreak: {
                    mode: ['avoid-all', 'css', 'legacy']
                }
            };

            html2pdf().set(opt).from(element).save().then(function() {
                btns.forEach(function(b) {
                    b.style.display = '';
                });
                overlay.classList.remove('show');
            }).catch(function() {
                btns.forEach(function(b) {
                    b.style.display = '';
                });
                overlay.classList.remove('show');
            });
        }
    </script>

</body>

</html>
