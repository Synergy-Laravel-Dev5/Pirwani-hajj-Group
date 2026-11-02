<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Portal | Pirwani Hajj Group</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/PIRWANI PNG FILE.png') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --gold: #C9A84C;
            --gold-light: #F3DC9B;
            --gold-bright: #D4AF37;
            --gold-dim: #996515;
            --navy-dark: #071527;
            --navy-mid: #0E233E;
            --navy-card: #102B4E;
            --border-gold: rgba(201, 168, 76, 0.35);
        }

        html,
        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F8F9FA;
            color: #1e293b;
        }

        .shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            background: linear-gradient(180deg, #FFFFFF 0%, #F5F7FA 100%);
        }

        /* Ambient luxury subtle background based on logo gold */
        .shell::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 65% 55% at 20% 20%, rgba(201, 168, 76, .10) 0%, transparent 65%),
                radial-gradient(ellipse 55% 55% at 80% 80%, rgba(14, 35, 62, .06) 0%, transparent 60%);
        }

        /* Topbar Header matching master layout */
        .topbar-header {
            position: relative;
            z-index: 10;
            background: linear-gradient(135deg, #071527 0%, #0E233E 55%, #08162A 100%);
            border-bottom: 2px solid #C9A84C;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25), 0 1px 0 rgba(201, 168, 76, 0.2);
            padding: 12px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        .logo-img {
            height: 48px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(201, 168, 76, 0.3));
        }

        .logo-text-block {
            display: flex;
            flex-direction: column;
        }

        .logo-brand-badge {
            font-size: 10px;
            font-weight: 700;
            color: #F3DC9B;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .logo-system-title {
            font-size: 14px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: 0.3px;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(201, 168, 76, 0.14);
            border: 1px solid rgba(201, 168, 76, 0.35);
            padding: 6px 14px;
            border-radius: 50px;
            color: #F3DC9B;
            font-size: 12px;
            font-weight: 600;
        }

        .user-chip .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #E5C368;
            box-shadow: 0 0 6px rgba(229, 195, 104, 0.8);
        }

        .date-chip {
            font-size: 11.5px;
            font-weight: 600;
            color: #CBD5E1;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 6px 13px;
            border-radius: 50px;
        }

        .mobile-ops-btn {
            background: rgba(201, 168, 76, 0.14);
            color: #F3DC9B;
            border: 1px solid rgba(201, 168, 76, 0.38);
            font-size: 11.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .mobile-ops-btn:hover {
            background: rgba(201, 168, 76, 0.25);
            color: #FFFFFF;
            border-color: #D4AF37;
        }

        .logout-btn {
            font-size: 12px;
            font-weight: 600;
            color: #FCA5A5;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 6px 14px;
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #FEE2E2;
            border-color: #EF4444;
        }

        /* Main Workspace Container */
        .portal-main {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px 60px;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        /* Headline Header */
        .headline {
            text-align: center;
            margin-bottom: 34px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--gold-dim);
            background: rgba(201, 168, 76, 0.12);
            border: 1px solid rgba(201, 168, 76, 0.3);
            padding: 6px 18px;
            border-radius: 50px;
            margin-bottom: 14px;
        }

        .h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 4vw, 42px);
            font-weight: 800;
            color: #0F2544;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .h1 em {
            font-style: italic;
            background: linear-gradient(120deg, #C9A84C 0%, #996515 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tagline {
            font-size: 15px;
            color: #64748B;
            font-weight: 500;
            max-width: 580px;
            margin: 0 auto;
            line-height: 1.55;
        }

        /* Cards Grid */
        .cards-row {
            display: flex;
            gap: 32px;
            justify-content: center;
            flex-wrap: wrap;
            width: 100%;
            max-width: 860px;
        }

        .portal-card {
            flex: 1;
            min-width: 300px;
            max-width: 400px;
            background: #FFFFFF;
            border-radius: 22px;
            border: 2px solid rgba(201, 168, 76, 0.3);
            box-shadow: 0 10px 30px rgba(15, 37, 68, 0.06);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
        }

        .portal-card:hover {
            transform: translateY(-8px);
            border-color: #C9A84C;
            box-shadow: 0 24px 50px rgba(201, 168, 76, 0.20);
        }

        /* Band Header on Card (Both themed around Logo Midnight Navy & Luxury Gold) */
        .card-band {
            padding: 26px 24px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .card-band::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255, 255, 255, 0.04) 10px, rgba(255, 255, 255, 0.04) 11px);
        }

        /* Main Dashboard Band */
        .portal-card.card-main .card-band {
            background: linear-gradient(135deg, #071527 0%, #0E233E 55%, #183863 100%);
            border-bottom: 3px solid #C9A84C;
        }

        /* Hotel Sale Band (Deep Royal Navy & Warm Amber Gold) */
        .portal-card.card-hotel .card-band {
            background: linear-gradient(135deg, #0B1E36 0%, #163259 55%, #23487A 100%);
            border-bottom: 3px solid #E5C368;
        }

        .band-icon-wrap {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            position: relative;
            z-index: 1;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
        }

        .card-main .band-icon-wrap {
            background: linear-gradient(135deg, #D4AF37, #996515);
            color: #FFFFFF;
            border: 1px solid rgba(243, 220, 155, 0.5);
        }

        .card-hotel .band-icon-wrap {
            background: linear-gradient(135deg, #E5C368, #B88E28);
            color: #FFFFFF;
            border: 1px solid rgba(251, 240, 204, 0.5);
        }

        .band-badge {
            position: relative;
            z-index: 1;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .card-main .band-badge {
            background: rgba(201, 168, 76, 0.2);
            color: #F3DC9B;
            border: 1px solid rgba(201, 168, 76, 0.45);
        }

        .card-hotel .band-badge {
            background: rgba(229, 195, 104, 0.2);
            color: #FBF0CC;
            border: 1px solid rgba(229, 195, 104, 0.45);
        }

        /* Card Content Area - Clean, Heading & Button Only */
        .card-body-content {
            padding: 32px 24px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 22px;
            flex: 1;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            color: #0F172A;
            margin: 0;
            letter-spacing: 0.3px;
        }

        /* Action CTA Button */
        .card-cta-btn {
            width: 100%;
            padding: 13px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(7, 21, 39, 0.12);
            border: none;
            cursor: pointer;
        }

        .card-main .card-cta-btn {
            background: linear-gradient(135deg, #071527 0%, #17365D 100%);
            color: #F3DC9B;
            border: 1.5px solid #C9A84C;
        }

        .portal-card.card-main:hover .card-cta-btn {
            background: linear-gradient(135deg, #D4AF37 0%, #B88E28 100%);
            color: #071527;
            border-color: #D4AF37;
            box-shadow: 0 6px 22px rgba(201, 168, 76, 0.45);
        }

        .card-hotel .card-cta-btn {
            background: linear-gradient(135deg, #0B1E36 0%, #1A3E6D 100%);
            color: #FBF0CC;
            border: 1.5px solid #E5C368;
        }

        .portal-card.card-hotel:hover .card-cta-btn {
            background: linear-gradient(135deg, #E5C368 0%, #C9A84C 100%);
            color: #071527;
            border-color: #E5C368;
            box-shadow: 0 6px 22px rgba(201, 168, 76, 0.45);
        }

        .arrow-bubble {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            font-size: 12px;
            transition: transform 0.2s;
        }

        .portal-card:hover .arrow-bubble {
            transform: translateX(3px);
        }

        /* Footer info */
        .portal-footer {
            text-align: center;
            margin-top: 36px;
            font-size: 12.5px;
            color: #94A3B8;
        }

        @media (max-width: 768px) {
            .topbar-header {
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .logo-text-block {
                display: none;
            }

            .date-chip,
            .mobile-ops-btn {
                display: none;
            }

            .portal-main {
                padding: 24px 16px 40px;
            }

            .cards-row {
                flex-direction: column;
                align-items: center;
            }

            .portal-card {
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="shell">

        {{-- Topbar Header (Header at the top matching Pirwani Logo Navy & Gold) --}}
        <header class="topbar-header">
            <a href="{{ route('welcome') }}" class="logo-area">
                <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}?v={{ time() }}"
                    alt="Pirwani Hajj Group" class="logo-img">
                <div class="logo-text-block">
                    <span class="logo-brand-badge">🕋 Pirwani Hajj Group</span>
                    <span class="logo-system-title">Management System</span>
                </div>
            </a>

            <div class="top-right">
                <div class="user-chip">
                    <span class="dot"></span>
                    <span>{{ Auth::user()->name ?? 'Admin User' }}</span>
                </div>

                <span class="date-chip">📅 {{ now()->format('d M Y') }}</span>

                <a href="{{ route('mobile-ops.dashboard') }}" class="mobile-ops-btn">
                    <span>📱</span>
                    <span>Mobile App Portal</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button class="logout-btn" type="submit" title="Sign Out">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        {{-- Main Area with 2 Cards --}}
        <main class="portal-main">

            <div class="headline">
                <div class="eyebrow">❖ Pirwani Management Portal ❖</div>
                <h1 class="h1">Welcome, <em>{{ Auth::user()->name ?? 'User' }}</em></h1>
                <p class="tagline">Select your workspace module below to access management tools, reports and operational records.</p>
            </div>

            {{-- 2 Cards Row --}}
            <div class="cards-row">

                {{-- CARD 1: Main Dashboard --}}
                <a href="{{ route('module.switch', 'main') }}" class="portal-card card-main">
                    <div class="card-band">
                        <div class="band-icon-wrap">
                            <span>📊</span>
                        </div>
                        <span class="band-badge">Core ERP & Operations</span>
                    </div>

                    <div class="card-body-content">
                        <div class="card-title">Main Dashboard</div>
                        <div class="card-cta-btn">
                            <span>Enter Main Dashboard</span>
                            <span class="arrow-bubble">→</span>
                        </div>
                    </div>
                </a>

                {{-- CARD 2: Hotel Sale --}}
                <a href="{{ route('module.switch', 'hotel_sale') }}" class="portal-card card-hotel">
                    <div class="card-band">
                        <div class="band-icon-wrap">
                            <span>🏨</span>
                        </div>
                        <span class="band-badge">Hotel Inventory & Invoicing</span>
                    </div>

                    <div class="card-body-content">
                        <div class="card-title">Hotel Sale</div>
                        <div class="card-cta-btn">
                            <span>Enter Hotel Sale</span>
                            <span class="arrow-bubble">→</span>
                        </div>
                    </div>
                </a>

            </div>

            <div class="portal-footer">
                &copy; {{ date('Y') }} Pirwani Hajj Group. All rights reserved.
            </div>

        </main>

    </div>
</body>

</html>
