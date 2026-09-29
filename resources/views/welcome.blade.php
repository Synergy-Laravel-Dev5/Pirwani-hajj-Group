<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAXIMS GROUP WELCOME</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@400;500;600&display=swap"
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
            --gold-light: #F0D080;
            --gold-dim: #96711E;
            --emerald: #1A6B4A;
            --emerald-light: #3DAF78;
            --navy: #0F2544;
            --cream: #FAF6EE;
            --border: #E6D9BC;
            --muted: #857860;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
            font-family: 'DM Sans', sans-serif;
            background: white;
        }

        /* ── Full-screen grid ── */
        .shell {
            height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr;
            position: relative;
            overflow: hidden;
        }

        /* Ambient blobs */
        .shell::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 55% 55% at 15% 20%, rgba(201, 168, 76, .13) 0%, transparent 65%),
                radial-gradient(ellipse 45% 50% at 85% 75%, rgba(26, 107, 74, .09) 0%, transparent 60%);
        }

        /* Subtle diagonal lines */
        .shell::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                repeating-linear-gradient(45deg, transparent, transparent 36px, rgba(201, 168, 76, .022) 36px, rgba(201, 168, 76, .022) 37px),
                repeating-linear-gradient(-45deg, transparent, transparent 36px, rgba(201, 168, 76, .022) 36px, rgba(201, 168, 76, .022) 37px);
        }

        /* ── Topbar ── */
        .topbar {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 36px;
            border-bottom: 1px solid var(--border);
            background: white;
            backdrop-filter: blur(14px);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .logo-mark {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            box-shadow: 0 3px 10px rgba(201, 168, 76, .32);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .logo-name {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.15;
        }

        .logo-sub {
            font-size: 9.5px;
            color: var(--muted);
            font-weight: 500;
            letter-spacing: .6px;
            text-transform: uppercase;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-chip {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--gold-dim);
            background: rgba(201, 168, 76, .10);
            border: 1px solid rgba(201, 168, 76, .22);
            padding: 5px 13px;
            border-radius: 50px;
        }

        .logout-btn {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted);
            background: #fff;
            border: 1px solid var(--border);
            padding: 5px 13px;
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color .18s, border-color .18s;
        }

        .logout-btn:hover {
            color: var(--navy);
            border-color: #bbb;
        }

        /* ── Main area — perfectly centered ── */
        .main {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 28px;
            padding: 24px 20px;
        }

        /* ── Header text block ── */
        .headline {
            text-align: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.3px;
            text-transform: uppercase;
            color: var(--gold-dim);
            background: rgba(201, 168, 76, .10);
            border: 1px solid rgba(201, 168, 76, .25);
            padding: 5px 14px;
            border-radius: 50px;
            margin-bottom: 14px;
            animation: up .5s ease both;
        }

        .h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 800;
            color: var(--navy);
            line-height: 1.15;
            margin-bottom: 10px;
            animation: up .5s .08s ease both;
        }

        .h1 em {
            font-style: italic;
            background: linear-gradient(120deg, var(--gold) 0%, var(--gold-dim) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tagline {
            font-size: 13.5px;
            color: var(--muted);
            line-height: 1.55;
            animation: up .5s .16s ease both;
        }

        /* ── Cards row ── */
        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            animation: up .55s .24s ease both;
        }

        .card {
            width: 270px;
            background: #fff;
            border-radius: 20px;
            border: 1.5px solid var(--border);
            box-shadow: 0 6px 28px rgba(15, 37, 68, .07);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            text-decoration: none;
            transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 52px rgba(15, 37, 68, .12);
        }

        .card.hajj:hover {
            border-color: rgba(201, 168, 76, .6);
        }

        .card.umrah:hover {
            border-color: rgba(26, 107, 74, .5);
        }

        /* Colour band */
        .band {
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .card.hajj .band {
            background: linear-gradient(135deg, #EFE0A8, #FAF2DC);
        }

        .card.umrah .band {
            background: linear-gradient(135deg, #A8D4BE, #DFF2E8);
        }

        .band::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(-45deg, transparent, transparent 7px, rgba(255, 255, 255, .35) 7px, rgba(255, 255, 255, .35) 8px);
        }

        .band-icon {
            font-size: 46px;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 3px 6px rgba(0, 0, 0, .1));
        }

        /* Card inner */
        .card-inner {
            padding: 18px 20px 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .tag {
            align-self: flex-start;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 50px;
            margin-bottom: 9px;
        }

        .card.hajj .tag {
            background: #FAF0D0;
            color: #956A10;
        }

        .card.umrah .tag {
            background: #D2EDDF;
            color: #1A6B4A;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 21px;
            font-weight: 700;
            margin-bottom: 7px;
            text-align: center;
        }

        .card.hajj .card-title {
            color: #704808;
        }

        .card.umrah .card-title {
            color: var(--emerald);
        }

        .card-desc {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.55;
            margin-bottom: 16px;
            flex: 1;
        }

        /* Mini stats */
        .stats {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .stat {
            flex: 1;
            border-radius: 10px;
            padding: 8px 10px;
            text-align: center;
        }

        .card.hajj .stat {
            background: #FBF5E6;
            border: 1px solid #E8D499;
        }

        .card.umrah .stat {
            background: #E8F5EE;
            border: 1px solid #92CCA8;
        }

        .sv {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 2px;
        }

        .card.hajj .sv {
            color: #956A10;
        }

        .card.umrah .sv {
            color: var(--emerald);
        }

        .sl {
            font-size: 9.5px;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        /* CTA */
        .cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 11px 16px;
            border-radius: 11px;
            font-size: 12.5px;
            font-weight: 700;
            color: #fff;
            letter-spacing: .15px;
            transition: filter .2s, transform .2s;
        }

        .card.hajj .cta {
            background: linear-gradient(135deg, #C9A84C, #DFC060);
            box-shadow: 0 4px 14px rgba(201, 168, 76, .33);
        }

        .card.umrah .cta {
            background: linear-gradient(135deg, #1A6B4A, #2E9A68);
            box-shadow: 0 4px 14px rgba(26, 107, 74, .28);
        }

        .cta:hover {
            filter: brightness(1.06);
            transform: scale(1.01);
            color: #fff;
        }

        .cta-arrow {
            width: 17px;
            height: 17px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .22);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        /* ── Animations ── */
        @keyframes up {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Responsive ── */
        @media (max-height: 700px) {
            .band {
                height: 70px;
            }

            .band-icon {
                font-size: 36px;
            }

            .card-inner {
                padding: 14px 16px 16px;
            }

            .main {
                gap: 18px;
            }

            .h1 {
                font-size: 26px;
            }
        }

        @media (max-width: 600px) {
            .shell {
                overflow: auto;
            }

            html,
            body {
                overflow: auto;
                height: auto;
            }

            .main {
                padding: 28px 16px 32px;
            }

            .card {
                width: 100%;
                max-width: 320px;
            }

            .topbar {
                padding: 12px 18px;
            }

            .date-chip {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="shell">

        {{-- Topbar --}}
        <header class="topbar">
            <div class="logo">
                <div class="logo-mark">🌙</div>
                <div>
                    <div class="logo-name">Maxims Group</div>
                    <div class="logo-sub">Management Portal</div>
                </div>
            </div>
            <div class="top-right">
                <span class="date-chip">📅 {{ now()->format('d M Y') }}</span>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button class="logout-btn" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M16 13v-2H7V8l-5 4l5 4v-3zm1-11H9a2 2 0 0 0-2 2v4h2V4h8v16H9v-4H7v4a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Main --}}
        <main class="main">

            {{-- Headline --}}
            <div class="headline">
                <div class="eyebrow">✦ Management Dashboard ✦</div>
                <h1 class="h1">Welcome to <em>Maxims Group</em></h1>
                <p class="tagline">Select a package to view bookings, revenue & monthly reports.</p>
            </div>

            {{-- Cards --}}
            <div class="cards">

                {{-- Hajj --}}
                <a href="{{ route('dashboard', ['package' => 'hajj', 'year' => now()->year]) }}" class="card hajj">
                    <div class="band">
                        <div class="band-icon">🕋</div>
                    </div>
                    <div class="card-inner">
                        <div class="card-title">Hajj</div>
                        <p class="card-desc">Bookings, clients, revenue & monthly performance at a glance.</p>
                        <div class="stats">
                            <div class="stat">
                                <div class="sv">{{ number_format($hajjCount) }}</div>
                                <div class="sl">Bookings</div>
                            </div>
                            <div class="stat">
                                <div class="sv">
                                    {{ $hajjRevenue >= 1000 ? number_format($hajjRevenue / 1000, 0) . 'K' : number_format($hajjRevenue) }}
                                </div>
                                <div class="sl">Revenue</div>
                            </div>
                        </div>
                        <span class="cta">View Hajj Dashboard <span class="cta-arrow">→</span></span>
                    </div>
                </a>

                {{-- Umrah --}}
                <a href="{{ route('dashboard', ['package' => 'umrah', 'year' => now()->year]) }}" class="card umrah">
                    <div class="band">
                        <div class="band-icon">🕌</div>
                    </div>
                    <div class="card-inner">
                        <div class="card-title">Umrah</div>
                        <p class="card-desc">Bookings, clients, revenue & monthly performance at a glance.</p>
                        <div class="stats">
                            <div class="stat">
                                <div class="sv">{{ number_format($umrahCount) }}</div>
                                <div class="sl">Bookings</div>
                            </div>
                            <div class="stat">
                                <div class="sv">
                                    {{ $umrahRevenue >= 1000 ? number_format($umrahRevenue / 1000, 0) . 'K' : number_format($umrahRevenue) }}
                                </div>
                                <div class="sl">Revenue</div>
                            </div>
                        </div>
                        <span class="cta">View Umrah Dashboard <span class="cta-arrow">→</span></span>
                    </div>
                </a>

            </div>
        </main>

    </div>
</body>

</html>
