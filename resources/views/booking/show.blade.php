<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking — {{ $booking->client->name ?? 'N/A' }} | Pirwani Hajj Group</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Cinzel:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.2.96/css/materialdesignicons.min.css">
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
            --navy-dark: #070e19;
            --navy: #0f1e36;
            --navy-light: #1e3a5f;
            --gold: #c9a84c;
            --gold-light: #fef08a;
            --gold-dark: #8c6819;
            --green: #10b981;
            --green-dark: #059669;
            --red: #ef4444;
            --red-dark: #dc2626;
            --bg-page: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            line-height: 1.5;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════
           PRINT RULES
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

            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .main-body {
                padding: 10px 15px !important;
                max-width: 100% !important;
            }

            @page {
                size: A4 portrait;
                margin: 8mm 8mm;
            }
        }

        /* ══════════════════════════════════════
           TOP NAVIGATION & ACTION BAR
        ══════════════════════════════════════ */
        .top-action-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        .bar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .bar-logo-img {
            height: 44px;
            width: auto;
            object-fit: contain;
        }

        .bar-brand-text h2 {
            font-family: 'Cinzel', serif;
            font-size: 16px;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .bar-brand-text p {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .bar-btn-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8.5px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.18s ease;
            white-space: nowrap;
        }

        .btn-gold-luxury {
            background: linear-gradient(135deg, #c9a84c 0%, #a67c1e 100%);
            color: #091322;
            border: 1px solid #fef08a;
            box-shadow: 0 3px 12px rgba(201, 168, 76, 0.35);
        }

        .btn-gold-luxury:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            color: #091322;
        }

        .btn-navy-outline {
            background: #ffffff;
            color: var(--navy);
            border-color: #cbd5e1;
        }

        .btn-navy-outline:hover {
            background: #f1f5f9;
            border-color: var(--navy);
            color: var(--navy);
        }

        .btn-secondary-outline {
            background: #ffffff;
            color: var(--text-muted);
            border-color: #e2e8f0;
        }

        .btn-secondary-outline:hover {
            background: #f8fafc;
            color: var(--text-main);
            border-color: #cbd5e1;
        }

        /* ══════════════════════════════════════
           HERO BANNER
        ══════════════════════════════════════ */
        .hero-banner {
            background: linear-gradient(135deg, #070e19 0%, #0f1e36 60%, #172b4d 100%);
            color: #ffffff;
            padding: 28px 32px 30px;
            position: relative;
            overflow: hidden;
            border-bottom: 3.5px solid var(--gold);
        }

        .hero-banner::after {
            content: "";
            position: absolute;
            right: -30px;
            top: -40px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(201, 168, 76, 0.18) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
            gap: 20px;
        }

        .hero-client {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .client-avatar {
            width: 66px;
            height: 66px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--gold), #7a5c18);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cinzel', serif;
            font-size: 30px;
            font-weight: 800;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, 0.25);
        }

        .client-info h1 {
            font-size: 24px;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 4px;
            letter-spacing: -0.2px;
        }

        .client-subtitle {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        /* ══════════════════════════════════════
           QUICK KPI STATS STRIP
        ══════════════════════════════════════ */
        .stats-grid {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .stat-card {
            padding: 16px 22px;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-card:last-child {
            border-right: none;
        }

        .stat-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }

        .stat-num {
            font-size: 21px;
            font-weight: 800;
            line-height: 1.15;
        }

        .stat-sub {
            font-size: 10.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Progress Bar */
        .progress-container {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 10px 32px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .progress-track {
            height: 7px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--gold) 0%, var(--green) 100%);
            transition: width 0.4s ease;
        }

        /* ══════════════════════════════════════
           MAIN CONTENT BODY
        ══════════════════════════════════════ */
        .main-body {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px 32px 60px;
        }

        .section-header-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin: 22px 0 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        @media (max-width: 900px) {
            .grid-2col {
                grid-template-columns: 1fr;
            }
        }

        /* ══════════════════════════════════════
           CARD STYLES
        ══════════════════════════════════════ */
        .content-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            margin-bottom: 18px;
        }

        .card-top {
            padding: 12px 20px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            font-size: 11.5px;
            font-weight: 800;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-top .title-flex {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .count-pill {
            background: #e2e8f0;
            color: var(--navy);
            font-size: 10.5px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 12px;
        }

        /* Key-Value Tables */
        .table-kv {
            width: 100%;
            border-collapse: collapse;
        }

        .table-kv tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .table-kv tr:last-child {
            border-bottom: none;
        }

        .table-kv td {
            padding: 9.5px 20px;
            font-size: 12.5px;
        }

        .table-kv td:first-child {
            width: 38%;
            color: var(--text-muted);
            font-weight: 600;
        }

        .table-kv td:last-child {
            color: var(--text-main);
            font-weight: 700;
        }

        /* Visual Flight Route */
        .flight-route-box {
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .flight-point {
            display: flex;
            flex-direction: column;
        }

        .flight-point.text-end {
            text-align: right;
        }

        .flight-code {
            font-size: 18px;
            font-weight: 900;
            color: var(--navy);
            line-height: 1.1;
        }

        .flight-datetime {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 2px;
        }

        .flight-connector {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            padding: 0 16px;
        }

        .connector-line {
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #cbd5e1, var(--gold), #cbd5e1);
            position: relative;
            margin: 6px 0;
        }

        /* ══════════════════════════════════════
           HOTEL STAYS CARDS GRID
        ══════════════════════════════════════ */
        .hotel-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }

        .hotel-stay-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border-top: 4px solid var(--navy);
        }

        .hotel-stay-card.card-makkah { border-top-color: #d97706; }
        .hotel-stay-card.card-azizia { border-top-color: #0284c7; }
        .hotel-stay-card.card-madinah { border-top-color: #16a34a; }

        .hotel-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .hotel-loc-pill {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2.5px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .pill-makkah { background: #fef3c7; color: #92400e; }
        .pill-azizia { background: #e0f2fe; color: #0284c7; }
        .pill-madinah { background: #dcfce7; color: #166534; }

        .hotel-card-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 6px;
            line-height: 1.25;
        }

        .hotel-specs-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px 12px;
            margin: 12px 0 14px;
            background: #f8fafc;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
        }

        .hotel-spec-item {
            display: flex;
            flex-direction: column;
        }

        .hotel-spec-label {
            font-size: 9.5px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 1px;
        }

        .hotel-spec-val {
            font-size: 12px;
            font-weight: 800;
            color: var(--navy);
        }

        .btn-maps-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #ffffff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-maps-link:hover {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
        }

        /* ══════════════════════════════════════
           DATA TABLES
        ══════════════════════════════════════ */
        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }

        .table-modern thead th {
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 11px 14px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 12px 14px;
            font-size: 12.5px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-modern tbody tr:hover {
            background: #fafbfc;
        }

        /* Photo Avatars */
        .person-photo-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--gold);
            display: block;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .person-initial-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e2e8f0;
            color: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            border: 1.5px solid #cbd5e1;
        }

        .table-action-btns {
            display: flex;
            gap: 5px;
            align-items: center;
            justify-content: center;
        }

        .btn-table-icon {
            padding: 5px 9px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid transparent;
            transition: all 0.15s;
        }

        .btn-table-badge {
            background: linear-gradient(135deg, #091322 0%, #172b4d 100%);
            color: #fde047;
            border-color: #d4af37;
        }

        .btn-table-badge:hover {
            filter: brightness(1.2);
            color: #ffffff;
        }

        .btn-table-epass {
            background: #e0f2fe;
            color: #0369a1;
            border-color: #bae6fd;
        }

        .btn-table-epass:hover {
            background: #bae6fd;
            color: #0c4a6e;
        }

        .btn-table-del {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            cursor: pointer;
        }

        .btn-table-del:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ══════════════════════════════════════
           COSTING TABLE
        ══════════════════════════════════════ */
        .costing-table {
            width: 100%;
            border-collapse: collapse;
        }

        .costing-table tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .costing-table td {
            padding: 10.5px 20px;
            font-size: 12.5px;
        }

        .costing-table td:nth-child(2) {
            color: var(--text-muted);
            font-size: 11.5px;
        }

        .costing-table td:last-child {
            text-align: right;
            font-weight: 700;
        }

        .costing-table tr.total-row {
            background: #f8fafc;
            border-top: 2px solid var(--border-color);
            font-size: 14px;
            font-weight: 800;
        }

        .costing-table tr.total-row td {
            padding: 13px 20px;
            color: var(--navy);
        }

        .cost-hidden-msg {
            display: none;
            padding: 30px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
        }

        .costing-section.costing-hidden .costing-table {
            display: none;
        }

        .costing-section.costing-hidden .cost-hidden-msg {
            display: block;
        }
    </style>
</head>

<body>

    <!-- ══════════════ TOP ACTION BAR ══════════════ -->
    <header class="top-action-bar no-print">
        <div class="bar-brand">
            <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Pirwani Hajj Group" class="bar-logo-img" onerror="this.src='{{ asset('assets/images/logo.png') }}'">
            <div class="bar-brand-text">
                <h2>Pirwani Hajj Group</h2>
                <p>Booking Management & Manifest Portal</p>
            </div>
        </div>

        <div class="bar-btn-group">
            <!-- 1. Bulk Badges Sheet (VIP Executive Feature) -->
            <a href="{{ route('pilgrim.bulk-badges', $booking->id) }}" target="_blank" class="action-btn btn-gold-luxury" title="Open printable A4 sheet of lanyard badges">
                <i class="mdi mdi-badge-account-horizontal"></i> Hajji ID Card
            </a>

            <!-- 2. Hotel Voucher -->
            <a href="{{ route('booking.voucher', $booking->id) }}" target="_blank" class="action-btn btn-navy-outline">
                <i class="mdi mdi-ticket-confirmation-outline"></i> Hotel Voucher
            </a>

            <!-- 3. Agreement -->
            <a href="{{ URL::signedRoute('booking.agreement', ['booking' => $booking->id]) }}" target="_blank" class="action-btn btn-navy-outline">
                <i class="mdi mdi-file-sign"></i> Agreement
            </a>

            <!-- 4. Edit Booking -->
            <a href="{{ route('booking.edit', $booking->id) }}" class="action-btn btn-navy-outline">
                <i class="mdi mdi-pencil-outline"></i> Edit Booking
            </a>

            <!-- 5. Toggle Costing -->
            <button class="action-btn btn-secondary-outline" id="toggleCostBtn" onclick="toggleCosting()">
                <i class="mdi mdi-eye-off-outline" id="toggleCostIcon"></i>
                <span id="toggleCostText">Hide Costing</span>
            </button>

            <!-- 6. Download PDF -->
            <button class="action-btn btn-secondary-outline" onclick="downloadPDF()">
                <i class="mdi mdi-file-download-outline"></i> PDF Export
            </button>

            <!-- 7. Back to Index -->
            <a href="{{ route('booking.index') }}" class="action-btn btn-secondary-outline">
                <i class="mdi mdi-arrow-left"></i> Back to Bookings
            </a>
        </div>
    </header>

    <!-- ══════════════ HERO BANNER ══════════════ -->
    <div class="hero-banner" id="page-top">
        <div class="hero-flex">
            <div class="hero-client">
                <div class="client-avatar">
                    {{ strtoupper(substr($booking->client->name ?? 'P', 0, 1)) }}
                </div>
                <div class="client-info">
                    <h1>{{ $booking->client->name ?? 'Guest Client' }}</h1>
                    <div class="client-subtitle">
                        @if ($booking->client->company_name ?? false)
                            <span><i class="mdi mdi-office-building me-1"></i>{{ $booking->client->company_name }}</span>
                            <span>·</span>
                        @endif
                        <span><i class="mdi mdi-barcode me-1"></i>Voucher #{{ $booking->voucher_number ?? $booking->id }}</span>
                        @if ($booking->phone)
                            <span>·</span>
                            <span><i class="mdi mdi-phone me-1"></i>{{ $booking->phone }}</span>
                        @endif
                        @if ($booking->care_of)
                            <span>·</span>
                            <span><i class="mdi mdi-account-arrow-right me-1"></i>C/O: {{ $booking->care_of }}</span>
                        @endif
                    </div>

                    <div class="badge-row">
                        <!-- Package Type -->
                        @php
                            $isHajj = strtolower($booking->package_type ?? 'hajj') === 'hajj';
                        @endphp
                        <span class="pill-badge" style="background: rgba(201, 168, 76, 0.22); color: #fde047; border: 1px solid rgba(201, 168, 76, 0.45);">
                            <i class="mdi mdi-kaaba"></i> {{ strtoupper($booking->package_type ?? 'HAJJ') }} {{ $booking->package_year ?? date('Y') }}
                        </span>

                        <!-- Booking Status -->
                        @php
                            $stClass = match(strtolower($booking->status ?? 'pending')) {
                                'confirmed' => ['bg' => 'rgba(16, 185, 129, 0.22)', 'color' => '#6ee7b7', 'border' => 'rgba(16, 185, 129, 0.45)'],
                                'cancelled' => ['bg' => 'rgba(239, 68, 68, 0.22)', 'color' => '#fca5a5', 'border' => 'rgba(239, 68, 68, 0.45)'],
                                default => ['bg' => 'rgba(245, 158, 11, 0.22)', 'color' => '#fcd34d', 'border' => 'rgba(245, 158, 11, 0.45)'],
                            };
                        @endphp
                        <span class="pill-badge" style="background: {{ $stClass['bg'] }}; color: {{ $stClass['color'] }}; border: 1px solid {{ $stClass['border'] }};">
                            <i class="mdi mdi-check-circle-outline"></i> {{ ucfirst($booking->status ?? 'Pending') }}
                        </span>

                        <!-- Camp / Maktab -->
                        @if ($booking->camp)
                            <span class="pill-badge" style="background: rgba(255, 255, 255, 0.12); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);">
                                <i class="mdi mdi-tent"></i> {{ $booking->camp }}
                            </span>
                        @endif

                        <!-- Qurbani -->
                        @if ($booking->qurbani_charges > 0 || ($booking->qurbani_option && $booking->qurbani_option !== 'not_included'))
                            <span class="pill-badge" style="background: rgba(16, 185, 129, 0.22); color: #a7f3d0; border: 1px solid rgba(16, 185, 129, 0.35);">
                                <i class="mdi mdi-sheep"></i> Qurbani: {{ ucfirst(str_replace('_', ' ', $booking->qurbani_option)) }} @if($booking->qurbani_qty) ({{ $booking->qurbani_qty }}) @endif
                            </span>
                        @endif

                        <!-- Package Name / Code -->
                        @if ($booking->package && ($booking->package->code || $booking->package->package_number))
                            <span class="pill-badge" style="background: rgba(255, 255, 255, 0.08); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.15);">
                                Code: {{ $booking->package->code ?? $booking->package->package_number }}
                            </span>
                        @endif

                        <!-- Pax Count -->
                        <span class="pill-badge" style="background: rgba(201, 168, 76, 0.25); color: #fef08a; border: 1px solid rgba(201, 168, 76, 0.5);">
                            <i class="mdi mdi-account-group"></i> {{ $booking->persons->count() ?: ($booking->no_of_pax ?? 1) }} Pax
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════ QUICK KPI STATS STRIP ══════════════ -->
    @php
        $paxTotal = $booking->persons->count() ?: ($booking->no_of_pax ?? 1);
        $percentPaid = $booking->total_amount > 0 ? min(100, ($booking->total_received / $booking->total_amount) * 100) : 0;
    @endphp
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-title">Total Invoice Amount</span>
            <span class="stat-num" style="color: var(--navy);">PKR {{ number_format($booking->total_amount, 0) }}</span>
            <span class="stat-sub">Official Package Total</span>
        </div>
        <div class="stat-card">
            <span class="stat-title">Total Received</span>
            <span class="stat-num" style="color: var(--green);">PKR {{ number_format($booking->total_received, 0) }}</span>
            <span class="stat-sub">{{ number_format($percentPaid, 1) }}% Cleared</span>
        </div>
        <div class="stat-card">
            <span class="stat-title">Outstanding Balance</span>
            <span class="stat-num" style="color: {{ $booking->balance > 0 ? 'var(--red)' : 'var(--green)' }};">
                PKR {{ number_format($booking->balance, 0) }}
            </span>
            <span class="stat-sub">{{ $booking->balance > 0 ? 'Pending Collection' : 'Zero Balance — Fully Settled' }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-title">Per Pax Package Cost</span>
            <span class="stat-num" style="color: var(--gold-dark);">PKR {{ number_format($booking->package_cost, 0) }}</span>
            <span class="stat-sub">per person rate</span>
        </div>
        <div class="stat-card">
            <span class="stat-title">Registered Pilgrims</span>
            <span class="stat-num" style="color: var(--navy);">{{ $paxTotal }} Pax</span>
            <span class="stat-sub">Booking Manifest</span>
        </div>
    </div>

    <div class="progress-container">
        <div class="progress-header">
            <span>Payment Clearance Progress</span>
            <span style="color: var(--navy);">{{ number_format($percentPaid, 0) }}% Paid</span>
        </div>
        <div class="progress-track">
            <div class="progress-fill" style="width: {{ $percentPaid }}%;"></div>
        </div>
    </div>

    <!-- ══════════════ MAIN CONTENT ══════════════ -->
    <main class="main-body" id="pdf-content">

        <!-- ── SECTION 1: TRAVELLING PERSONS & BADGES ── -->
        @if ($booking->persons->count())
            <div class="section-header-title">
                <i class="mdi mdi-badge-account-horizontal-outline"></i> Registered Travelling Pilgrims ({{ $booking->persons->count() }})
            </div>

            <div class="content-card">
                <div class="card-top">
                    <div class="title-flex">
                        <i class="mdi mdi-badge-account-outline text-primary"></i> Pilgrim Identity Manifest & ID Badges
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('pilgrim.bulk-badges', $booking->id) }}" target="_blank" class="btn-table-icon btn-table-badge" title="Print all badges on A4 sheet">
                            <i class="mdi mdi-printer"></i> Hajji ID Card (All)
                        </a>
                        <span class="count-pill">{{ $booking->persons->count() }} Pilgrims</span>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th style="width: 35px;">#</th>
                                <th style="width: 45px;">Photo</th>
                                <th>Pilgrim Name</th>
                                <th>Hajj ID & HB #</th>
                                <th>Father / Husband</th>
                                <th>Passport #</th>
                                <th>Issue / Expiry</th>
                                <th>CNIC / Phone</th>
                                <th>Ticket</th>
                                <th>Passport Doc</th>
                                <th style="text-align: center; width: 160px;">ID Card Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking->persons as $person)
                                @php
                                    $hajjId = \App\Models\BookingPerson::formatHajjId($person->hajj_id) ?? ('PW27' . str_pad($person->id, 3, '0', STR_PAD_LEFT));
                                    $hbNumber = \App\Models\BookingPerson::formatHbNumber($person->hb_number) ?? 'HB0001';
                                    
                                    $pilgrimPhoto = null;
                                    if (!empty($person->photo) && file_exists(public_path($person->photo))) {
                                        $pilgrimPhoto = asset($person->photo);
                                    } elseif (!empty($person->passport_image) && file_exists(public_path($person->passport_image))) {
                                        $pilgrimPhoto = asset($person->passport_image);
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <span style="font-weight: 800; color: var(--navy); font-size: 11px;">{{ $loop->iteration }}</span>
                                    </td>
                                    <td>
                                        @if ($pilgrimPhoto)
                                            <a href="{{ $pilgrimPhoto }}" target="_blank">
                                                <img src="{{ $pilgrimPhoto }}" alt="{{ $person->full_name }}" class="person-photo-circle">
                                            </a>
                                        @else
                                            <div class="person-initial-circle">
                                                {{ strtoupper(substr($person->full_name, 0, 1) ?: 'P') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 800; color: var(--navy); font-size: 13px;">{{ $person->full_name }}</div>
                                        @if($person->gender)
                                            <span style="font-size: 10.5px; color: var(--text-muted);">
                                                <i class="mdi {{ strtolower($person->gender) === 'female' ? 'mdi-gender-female text-danger' : 'mdi-gender-male text-primary' }}"></i>
                                                {{ ucfirst($person->gender) }}
                                                @if($person->blood_group) · <strong style="color: #dc2626;">{{ $person->blood_group }}</strong>@endif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span style="background: #0f1e36; color: #fde047; border: 1px solid #d4af37; font-size: 10px; font-weight: 800; padding: 1.5px 6px; border-radius: 4px; display: inline-block;">
                                                {{ $hajjId }}
                                            </span>
                                            <span style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; font-size: 10px; font-weight: 800; padding: 1.5px 6px; border-radius: 4px; display: inline-block;">
                                                {{ $hbNumber }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600;">{{ $person->father_name ?: '—' }}</span>
                                    </td>
                                    <td>
                                        <strong style="color: var(--gold-dark); font-size: 13px;">{{ $person->passport_number ?: '—' }}</strong>
                                    </td>
                                    <td>
                                        <div style="font-size: 11.5px;">
                                            <div>Iss: {{ ($person->date_of_issue ?? $person->passport_issue_date) ? \Carbon\Carbon::parse($person->date_of_issue ?? $person->passport_issue_date)->format('d M Y') : '—' }}</div>
                                            <div style="color: var(--text-muted);">Exp: {{ $person->passport_expiry_date ? \Carbon\Carbon::parse($person->passport_expiry_date)->format('d M Y') : '—' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 11.5px;">
                                            <div>{{ $person->cnic ?: '—' }}</div>
                                            <div style="color: var(--text-muted);">{{ $person->phone ?: '—' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if(($person->ticket_status ?? 'on_ticket') === 'without_ticket')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 10px; font-weight: 800;">Without Ticket</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 10px; font-weight: 800;">On Ticket</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($person->passport_image) && file_exists(public_path($person->passport_image)))
                                            <a href="{{ asset($person->passport_image) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2" style="font-size: 11px;" title="View uploaded passport document">
                                                <i class="mdi mdi-file-document-outline me-1"></i>View
                                            </a>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-action-btns">
                                            <!-- Print Single Hajji ID Card -->
                                            <a href="{{ route('pilgrim.badge', $person->id) }}" target="_blank" class="btn-table-icon btn-table-badge" title="Print Hajji ID Card">
                                                <i class="mdi mdi-badge-account"></i> ID Card
                                            </a>

                                            <!-- Live E-Pass -->
                                            <a href="{{ route('pilgrim.epass', $person->id) }}" target="_blank" class="btn-table-icon btn-table-epass" title="Open Mobile Live Pass">
                                                <i class="mdi mdi-cellphone-link"></i> Pass
                                            </a>

                                            <!-- Delete Person Option if > 1 -->
                                            @if ($booking->persons->count() > 1)
                                                <form method="POST" action="{{ route('booking.person.delete', [$booking->id, $person->id]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to remove pilgrim {{ addslashes($person->full_name) }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-table-icon btn-table-del" title="Delete pilgrim">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- ── SECTION 2: HOTEL ACCOMMODATIONS ── -->
        @if ($booking->hotels->count())
            <div class="section-header-title">
                <i class="mdi mdi-office-building-marker-outline"></i> Accommodation & Hotel Manifest ({{ $booking->hotels->count() }})
            </div>

            <div class="hotel-cards-grid">
                @foreach ($booking->hotels as $hotel)
                    @php
                        $loc = strtolower($hotel->location ?? '');
                        $hName = strtolower($hotel->hotel_name ?? '');
                        $isMakkah = str_contains($loc, 'makkah') || str_contains($hName, 'makkah');
                        $isAzizia = str_contains($loc, 'azizia') || str_contains($loc, 'aziziya') || str_contains($hName, 'azizia') || str_contains($hName, 'aziziya');
                        $isMadinah = str_contains($loc, 'madinah') || str_contains($loc, 'medina') || str_contains($hName, 'madinah') || str_contains($hName, 'medina');

                        $cardClass = $isMakkah ? 'card-makkah' : ($isAzizia ? 'card-azizia' : ($isMadinah ? 'card-madinah' : ''));
                        $pillClass = $isMakkah ? 'pill-makkah' : ($isAzizia ? 'pill-azizia' : ($isMadinah ? 'pill-madinah' : 'pill-makkah'));
                        $locIcon = $isMakkah ? 'mdi-kaaba' : ($isAzizia ? 'mdi-home-city' : ($isMadinah ? 'mdi-mosque' : 'mdi-hotel'));
                        $locTitle = $isMakkah ? 'Makkah Mukarramah' : ($isAzizia ? 'Azizia Building' : ($isMadinah ? 'Madinah Munawwarah' : ucfirst($hotel->location ?: 'Hotel')));

                        $mapQuery = urlencode(($hotel->hotel_name ?? '') . ' ' . $locTitle . ' Saudi Arabia');
                    @endphp
                    <div class="hotel-stay-card {{ $cardClass }}">
                        <div>
                            <div class="hotel-card-header">
                                <span class="hotel-loc-pill {{ $pillClass }}">
                                    <i class="mdi {{ $locIcon }}"></i> {{ $locTitle }}
                                </span>
                                <span style="background: #0f1e36; color: #fde047; font-weight: 800; padding: 2px 8px; border-radius: 6px; font-size: 11px;">
                                    Room: {{ $hotel->room_number ?: '—' }}
                                </span>
                            </div>

                            <h3 class="hotel-card-title">{{ $hotel->hotel_name }}</h3>

                            <div class="hotel-specs-grid">
                                <div class="hotel-spec-item">
                                    <span class="hotel-spec-label">Nights & Type</span>
                                    <span class="hotel-spec-val">{{ $hotel->no_of_nights ?? 1 }} Nights · {{ ucfirst($hotel->room_type ?? 'Sharing') }}</span>
                                </div>
                                <div class="hotel-spec-item">
                                    <span class="hotel-spec-label">Room Gender</span>
                                    <span class="hotel-spec-val">{{ $hotel->gender ? ucfirst($hotel->gender) : 'Any / Mixed' }}</span>
                                </div>
                                <div class="hotel-spec-item">
                                    <span class="hotel-spec-label">Check-In</span>
                                    <span class="hotel-spec-val">{{ $hotel->check_in ? \Carbon\Carbon::parse($hotel->check_in)->format('d M Y') : '—' }}</span>
                                </div>
                                <div class="hotel-spec-item">
                                    <span class="hotel-spec-label">Check-Out</span>
                                    <span class="hotel-spec-val">{{ $hotel->check_out ? \Carbon\Carbon::parse($hotel->check_out)->format('d M Y') : '—' }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}" target="_blank" class="btn-maps-link">
                            <i class="mdi mdi-map-marker-radius"></i> Open in Google Maps
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- ── SECTION 3: FLIGHTS & BOOKING PROFILE ── -->
        <div class="section-header-title">
            <i class="mdi mdi-airplane-takeoff"></i> Flight Schedule & Booking Details
        </div>

        <div class="grid-2col">
            <!-- Card 1: Booking Details -->
            <div class="content-card">
                <div class="card-top">
                    <div class="title-flex">
                        <i class="mdi mdi-card-account-details-outline text-primary"></i> Booking Overview
                    </div>
                    <span class="count-pill">ID #{{ $booking->id }}</span>
                </div>
                <table class="table-kv">
                    <tr>
                        <td>Voucher Number</td>
                        <td><strong style="color: var(--navy);"><i class="mdi mdi-barcode me-1"></i>{{ $booking->voucher_number ?? '—' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Passport Number</td>
                        <td><strong style="color: var(--gold-dark);">{{ $booking->passport_number ?? '—' }}</strong></td>
                    </tr>
                    <tr>
                        <td>CNIC Number</td>
                        <td>{{ $booking->cnic ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Care Of</td>
                        <td>{{ $booking->care_of ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Contact Mobile</td>
                        <td>{{ $booking->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Emergency Contact</td>
                        <td><span style="color: #b91c1c; font-weight: 800;">{{ $booking->emergency_phone ?? '—' }}</span></td>
                    </tr>
                    @if ($booking->camp)
                    <tr>
                        <td>Camp / Maktab</td>
                        <td><strong style="color: var(--gold-dark);">{{ $booking->camp }}</strong></td>
                    </tr>
                    @endif
                    <tr>
                        <td>Qurbani Details</td>
                        <td>
                            {{ ucfirst(str_replace('_', ' ', $booking->qurbani_option ?? 'Not Included')) }}
                            @if($booking->qurbani_qty) ({{ $booking->qurbani_qty }} Pax) @endif
                            @if($booking->qurbani_charges > 0) · <span class="text-success">PKR {{ number_format($booking->qurbani_charges, 0) }}</span>@endif
                        </td>
                    </tr>
                    @if (!empty($booking->remarks))
                    <tr>
                        <td>Remarks</td>
                        <td><span>{{ $booking->remarks }}</span></td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Card 2: Flight Schedule -->
            <div class="content-card">
                <div class="card-top">
                    <div class="title-flex">
                        <i class="mdi mdi-airplane-clock text-primary"></i> Flight Itinerary
                    </div>
                    <span class="count-pill">{{ ($booking->departure_date || $booking->arrival_date) ? 'Scheduled' : 'Pending' }}</span>
                </div>

                @if ($booking->departure_date || $booking->arrival_date)
                    <div class="flight-route-box">
                        <div class="flight-point">
                            <span class="flight-code">{{ $booking->departure_flight ?? 'DEP' }}</span>
                            <span class="flight-datetime">
                                {{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d M Y') : '—' }}
                                {{ $booking->departure_time ? '· ' . $booking->departure_time : '' }}
                            </span>
                        </div>
                        <div class="flight-connector">
                            <i class="mdi mdi-airplane-takeoff" style="color: var(--gold); font-size: 18px;"></i>
                            <div class="connector-line"></div>
                            <span style="font-size: 10px; color: var(--text-muted); font-weight: 700;">{{ $booking->departure_airline ?? ($booking->arrival_airline ?? 'Flight') }}</span>
                        </div>
                        <div class="flight-point text-end">
                            <span class="flight-code">{{ $booking->arrival_flight ?? 'ARR' }}</span>
                            <span class="flight-datetime">
                                {{ $booking->arrival_date ? \Carbon\Carbon::parse($booking->arrival_date)->format('d M Y') : '—' }}
                                {{ $booking->arrival_time ? '· ' . $booking->arrival_time : '' }}
                            </span>
                        </div>
                    </div>
                @endif

                <table class="table-kv">
                    <tr>
                        <td>Departure Airline</td>
                        <td>{{ $booking->departure_airline ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Dep. Flight # / PNR</td>
                        <td>
                            <strong>{{ $booking->departure_flight ?? '—' }}</strong>
                            @if($booking->departure_pnr)
                                <span class="badge bg-light text-primary border ms-1">{{ $booking->departure_pnr }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Departure Date</td>
                        <td>{{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('d M Y') : '—' }} {{ $booking->departure_time ? '@ ' . $booking->departure_time : '' }}</td>
                    </tr>
                    <tr>
                        <td>Return Airline</td>
                        <td>{{ $booking->arrival_airline ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Return Flight # / PNR</td>
                        <td>
                            <strong>{{ $booking->arrival_flight ?? '—' }}</strong>
                            @if($booking->arrival_pnr)
                                <span class="badge bg-light text-info border ms-1">{{ $booking->arrival_pnr }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Return Date</td>
                        <td>{{ $booking->arrival_date ? \Carbon\Carbon::parse($booking->arrival_date)->format('d M Y') : '—' }} {{ $booking->arrival_time ? '@ ' . $booking->arrival_time : '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ── SECTION 4: GROUND TRANSFERS & VISAS ── -->
        @if ($booking->transports->count() || $booking->visas->count())
            <div class="section-header-title">
                <i class="mdi mdi-bus-clock"></i> Ground Logistics & Visas
            </div>

            <div class="grid-2col">
                <!-- Transport Routes -->
                @if ($booking->transports->count())
                    <div class="content-card">
                        <div class="card-top">
                            <div class="title-flex">
                                <i class="mdi mdi-bus text-primary"></i> Transport Transfers
                            </div>
                            <span class="count-pill">{{ $booking->transports->count() }}</span>
                        </div>
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Vehicle</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->transports as $tr)
                                    <tr>
                                        <td><strong style="color: var(--navy);">{{ $tr->route }}</strong></td>
                                        <td>
                                            <span class="pill-badge" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                                                {{ ucfirst(str_replace('_', ' ', $tr->transport_type ?? 'bus')) }}
                                            </span>
                                        </td>
                                        <td><span class="text-muted">{{ $tr->notes ?: '—' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Visas -->
                @if ($booking->visas->count())
                    <div class="content-card">
                        <div class="card-top">
                            <div class="title-flex">
                                <i class="mdi mdi-passport text-primary"></i> Visa Processing
                            </div>
                            <span class="count-pill">{{ $booking->visas->count() }}</span>
                        </div>
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Passport #</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->visas as $visa)
                                    @php
                                        $vSt = match(strtolower($visa->status ?? 'pending')) {
                                            'approved' => 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;',
                                            'submitted' => 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;',
                                            'rejected' => 'background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;',
                                            default => 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong style="color: var(--navy);">{{ $visa->given_name ?: '—' }}</strong></td>
                                        <td>{{ $visa->passport_number ?: '—' }}</td>
                                        <td>
                                            <span class="pill-badge" style="{{ $vSt }}">
                                                {{ ucfirst($visa->status ?? 'pending') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        <!-- ── SECTION 5: FINANCIAL & COSTING SUMMARY ── -->
        <div class="section-header-title">
            <i class="mdi mdi-cash-multiple"></i> Financial Ledger & Costing Breakdown
        </div>

        <div class="content-card costing-section" id="costingSection">
            <div class="card-top">
                <div class="title-flex">
                    <i class="mdi mdi-calculator-variant-outline text-primary"></i> Costing Breakdown
                </div>
                <span class="count-pill">Official Account Ledger</span>
            </div>

            <table class="costing-table">
                <tr>
                    <td>Package Cost</td>
                    <td>PKR {{ number_format($booking->package_cost, 0) }} × {{ $booking->no_of_pax }} Pax</td>
                    <td>PKR {{ number_format($booking->package_cost * $booking->no_of_pax, 0) }}</td>
                </tr>
                @if ($booking->visa_charges > 0)
                <tr>
                    <td>Visa Charges</td>
                    <td>Consular & Service Fees</td>
                    <td>PKR {{ number_format($booking->visa_charges, 0) }}</td>
                </tr>
                @endif
                @if ($booking->flight_charges > 0)
                <tr>
                    <td>Flight Charges</td>
                    <td>Air Ticket Fares</td>
                    <td>PKR {{ number_format($booking->flight_charges, 0) }}</td>
                </tr>
                @endif
                @if ($booking->qurbani_charges > 0)
                <tr>
                    <td>Qurbani Charges</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $booking->qurbani_option ?? 'Qurbani')) }} @if($booking->qurbani_qty) ({{ $booking->qurbani_qty }} heads) @endif</td>
                    <td>PKR {{ number_format($booking->qurbani_charges, 0) }}</td>
                </tr>
                @endif
                @if ($booking->other_charges > 0)
                <tr>
                    <td>Other Charges</td>
                    <td>Additional Services</td>
                    <td>PKR {{ number_format($booking->other_charges, 0) }}</td>
                </tr>
                @endif
                @if (($booking->discount ?? 0) > 0)
                <tr style="color: var(--red);">
                    <td><strong><i class="mdi mdi-tag-minus me-1"></i>Discount Applied</strong></td>
                    <td>Concession / Special Rebate</td>
                    <td><strong>- PKR {{ number_format($booking->discount, 0) }}</strong></td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total Amount</td>
                    <td>Net Booking Amount</td>
                    <td style="color: var(--navy); font-size: 15px;">PKR {{ number_format($booking->total_amount, 0) }}</td>
                </tr>
                <tr>
                    <td style="color: var(--green); font-weight: 700;">Total Received</td>
                    <td style="color: var(--green);">Payments Credited</td>
                    <td style="color: var(--green); font-size: 14px;">PKR {{ number_format($booking->total_received, 0) }}</td>
                </tr>
                <tr class="total-row" style="background: {{ $booking->balance > 0 ? '#fef2f2' : '#f0fdf4' }};">
                    <td style="color: {{ $booking->balance > 0 ? 'var(--red-dark)' : 'var(--green-dark)' }};">Balance Due</td>
                    <td style="color: {{ $booking->balance > 0 ? 'var(--red-dark)' : 'var(--green-dark)' }};">
                        {{ $booking->balance > 0 ? 'Outstanding Dues' : 'Zero Balance — Fully Settled' }}
                    </td>
                    <td style="color: {{ $booking->balance > 0 ? 'var(--red-dark)' : 'var(--green-dark)' }}; font-size: 16px; font-weight: 900;">
                        PKR {{ number_format($booking->balance, 0) }}
                    </td>
                </tr>
            </table>

            <div class="cost-hidden-msg">
                <i class="mdi mdi-eye-off-outline" style="font-size: 32px; color: #94a3b8; display: block; margin-bottom: 8px;"></i>
                Financial costing summary is hidden for client presentation.
            </div>
        </div>

    </main>

    <!-- ══════════════ SCRIPTS ══════════════ -->
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
            var btns = document.querySelectorAll('.no-print');
            btns.forEach(function(b) {
                b.style.display = 'none';
            });

            var element = document.getElementById('page-top').parentElement;
            var clientName = '{{ addslashes($booking->client->name ?? "Booking") }}';
            var filename = 'Pirwani-Hajj-' + clientName.replace(/\s+/g, '-') + '.pdf';

            var opt = {
                margin: [6, 6, 6, 6],
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#f8fafc',
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
            }).catch(function() {
                btns.forEach(function(b) {
                    b.style.display = '';
                });
            });
        }
    </script>

</body>

</html>
