<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIRWANI HAJJ GROUP — Portal Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pirwani Hajj Group (Pvt.) Ltd. Management System Login" />
    <meta name="author" content="Pirwani Hajj Group" />

    <!-- App Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/PIRWANI PNG FILE.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.2.96/css/materialdesignicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --gold-primary: #C9A84C;
            --gold-light: #F3DC9B;
            --gold-dark: #96711E;
            --gold-glow: rgba(201, 168, 76, 0.35);
            --navy-deep: #081426;
            --navy-surface: #0F233D;
            --navy-card: #152E4D;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --border-soft: #E2E8F0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            background: #F4F7FB;
            color: var(--text-main);
            overflow-x: hidden;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            width: 100vw;
        }

        /* ════ LEFT AUTH FORM SECTION ════ */
        .auth-left {
            width: 46%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 36px 52px;
            background: #FFFFFF;
            position: relative;
            z-index: 2;
            box-shadow: 12px 0 45px rgba(8, 20, 38, 0.06);
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--gold-dark), var(--gold-primary), var(--gold-light), var(--gold-primary));
        }

        /* Centered Brand Header */
        .brand-header-center {
            text-align: center;
            padding-top: 10px;
        }

        .brand-logo-img {
            height: 120px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 6px 16px rgba(201, 168, 76, 0.28));
            transition: transform 0.3s ease;
        }

        .brand-logo-img:hover {
            transform: scale(1.03);
        }

        /* Main Form Container */
        .form-box {
            max-width: 420px;
            width: 100%;
            margin: 20px auto;
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(201, 168, 76, 0.12);
            color: var(--gold-dark);
            border: 1px solid rgba(201, 168, 76, 0.28);
            padding: 5px 14px;
            border-radius: 30px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .welcome-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 800;
            color: var(--navy-deep);
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .welcome-desc {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.5;
        }

        /* Custom Input Groups */
        .form-group-custom {
            margin-bottom: 18px;
            position: relative;
        }

        .form-label-custom {
            font-size: 13px;
            font-weight: 600;
            color: var(--navy-surface);
            margin-bottom: 7px;
            display: block;
        }

        .input-icon-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-wrap .input-icon-left {
            position: absolute;
            left: 15px;
            color: #94A3B8;
            font-size: 18px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .input-custom {
            width: 100%;
            height: 48px;
            padding: 10px 16px 10px 45px;
            font-size: 14px;
            font-weight: 500;
            color: var(--navy-deep);
            background: #F8FAFC;
            border: 1.5px solid var(--border-soft);
            border-radius: 12px;
            transition: all 0.25s ease;
            outline: none;
        }

        .input-custom:focus {
            background: #FFFFFF;
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 4px var(--gold-glow);
        }

        .input-custom:focus + .input-icon-left,
        .input-custom:focus ~ .input-icon-left {
            color: var(--gold-dark);
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            font-size: 18px;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .toggle-password-btn:hover {
            color: var(--navy-deep);
        }

        /* Button */
        .btn-submit-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #081426 0%, #0F233D 60%, #1A395F 100%);
            color: #FFFFFF;
            border: 1px solid rgba(201, 168, 76, 0.35);
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(8, 20, 38, 0.28);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            margin-top: 10px;
        }

        .btn-submit-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(201, 168, 76, 0.35), transparent);
            transition: left 0.6s ease;
        }

        .btn-submit-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(8, 20, 38, 0.38);
            background: linear-gradient(135deg, #050E1B 0%, #081426 100%);
            border-color: var(--gold-primary);
            color: #FFFFFF;
        }

        .btn-submit-login:hover::before {
            left: 100%;
        }

        .btn-submit-login:active {
            transform: translateY(0);
        }

        /* Footer Info */
        .auth-footer {
            font-size: 12px;
            color: var(--text-muted);
            text-align: center;
            padding-top: 16px;
            border-top: 1px solid #F1F5F9;
        }

        /* ════ RIGHT HERO / VISUAL SECTION ════ */
        .auth-right {
            width: 54%;
            min-height: 100vh;
            position: relative;
            background: url('/assets/images/logo/omer-f-arslan-W0FhhtnMd8k-unsplash.jpg') center center no-repeat;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 56px;
            overflow: hidden;
        }

        /* High-class Dark Gold/Navy Overlay */
        .auth-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(8, 20, 38, 0.94) 0%, rgba(15, 35, 61, 0.88) 50%, rgba(150, 113, 30, 0.78) 100%);
            z-index: 1;
        }

        /* Ambient Radial Glow */
        .auth-right::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 80% 20%, rgba(201, 168, 76, 0.28) 0%, transparent 50%),
                              radial-gradient(circle at 20% 80%, rgba(26, 107, 74, 0.22) 0%, transparent 45%);
            z-index: 1;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: #FFFFFF;
        }

        .arabic-badge {
            font-family: 'Amiri', serif;
            font-size: 25px;
            color: var(--gold-light);
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.6);
            margin-bottom: 22px;
            display: inline-block;
            letter-spacing: 1px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 7px 22px;
            border-radius: 40px;
            border: 1px solid rgba(243, 220, 155, 0.3);
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 3.4vw, 44px);
            font-weight: 800;
            line-height: 1.2;
            color: #FFFFFF;
            margin-bottom: 18px;
            text-shadow: 0 4px 18px rgba(0, 0, 0, 0.4);
        }

        .hero-title em {
            font-style: italic;
            background: linear-gradient(120deg, #FFFFFF 20%, var(--gold-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-desc {
            font-size: 15px;
            line-height: 1.65;
            color: #E2E8F0;
            max-width: 480px;
            margin-bottom: 32px;
            font-weight: 300;
        }

        /* Feature Pillars Grid */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: auto;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 18px 16px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(201, 168, 76, 0.45);
            transform: translateY(-3px);
        }

        .feature-icon {
            font-size: 24px;
            margin-bottom: 8px;
            display: inline-block;
        }

        .feature-card h6 {
            color: #FFFFFF;
            font-size: 13.5px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .feature-card p {
            color: #CBD5E1;
            font-size: 11.5px;
            margin: 0;
            line-height: 1.4;
        }

        /* Alert Styling */
        .alert-custom-danger {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .alert-custom-success {
            background: #ECFDF5;
            border: 1px solid #6EE7B7;
            color: #065F46;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        /* Responsive Adjustments */
        @media (max-width: 1024px) {
            .auth-wrapper {
                flex-direction: column;
            }
            .auth-left {
                width: 100%;
                min-height: auto;
                padding: 36px 24px;
            }
            .auth-right {
                width: 100%;
                min-height: 380px;
                padding: 40px 24px;
            }
            .feature-grid {
                grid-template-columns: 1fr;
            }
            .brand-logo-img {
                height: 100px;
            }
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">

        <!-- ════ LEFT FORM CONTAINER ════ -->
        <div class="auth-left">

            <!-- Brand Centered Header with 120px Logo -->
            <div class="brand-header-center">
                <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Pirwani Hajj Group (Pvt.) Ltd." class="brand-logo-img">
            </div>

            <!-- Form Box -->
            <div class="form-box">

                <div class="text-center mb-3">
                    <div class="welcome-badge">
                        <i class="ri-shield-check-line"></i> Official Management Portal
                    </div>
                    <h1 class="welcome-title">Welcome Back</h1>
                    <p class="welcome-desc">Sign in to manage Hajj bookings, flights, and operations</p>
                </div>

                <!-- Status & Alerts -->
                @if (session('success'))
                    <div class="alert-custom-success d-flex align-items-center gap-2">
                        <i class="ri-checkbox-circle-fill fs-16"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-custom-danger">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-error-warning-fill fs-16"></i>
                            <strong>Authentication Failed</strong>
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <!-- Email Field -->
                    <div class="form-group-custom">
                        <label class="form-label-custom" for="email">Email Address</label>
                        <div class="input-icon-wrap">
                            <input type="email" id="email" name="email"
                                class="input-custom @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required autofocus
                                placeholder="name@pirwanihajj.com">
                            <i class="ri-mail-line input-icon-left"></i>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group-custom">
                        <label class="form-label-custom" for="password">Password</label>
                        <div class="input-icon-wrap">
                            <input type="password" id="password" name="password"
                                class="input-custom @error('password') is-invalid @enderror"
                                required placeholder="••••••••••••">
                            <i class="ri-lock-line input-icon-left"></i>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility">
                                <i id="togglePasswordIcon" class="ri-eye-line"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="d-flex align-items-center gap-2" style="font-size: 13px; color: var(--text-muted); cursor: pointer;">
                            <input type="checkbox" name="remember" style="accent-color: var(--gold-primary); width: 16px; height: 16px; border-radius: 4px;">
                            Remember me on this device
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit-login">
                        <span>Sign In to Hajj Portal</span>
                        <i class="ri-arrow-right-line fs-18"></i>
                    </button>
                </form>

            </div>

            <!-- Footer -->
            <div class="auth-footer">
                &copy; {{ date('Y') }} Pirwani Hajj Group (Pvt.) Ltd. &bull; All Rights Reserved.
            </div>

        </div>

        <!-- ════ RIGHT HERO PANEL ════ -->
        <div class="auth-right">

            <div class="hero-content">
                <div class="arabic-badge">
                    لَبَّيْكَ اللَّهُمَّ لَبَّيْكَ &bull; بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                </div>

                <h2 class="hero-title">
                    Serving the Blessed Pilgrims with <em>Excellence & Trust</em>
                </h2>

                <p class="hero-desc">
                    Comprehensive portal for organizing Hajj operations, pilgrim contracts, flight logistics, and real-time financial reporting.
                </p>
            </div>

            <!-- Feature Cards Bottom Grid -->
            <div class="feature-grid hero-content">
                <div class="feature-card">
                    <div class="feature-icon">🕋</div>
                    <h6>Hajj Operations</h6>
                    <p>Complete pilgrim application & package management.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">✈️</div>
                    <h6>Travel & Logistics</h6>
                    <p>Real-time flight bookings, hotels & transportation.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📑</div>
                    <h6>Vouchers & Accounts</h6>
                    <p>Automated invoices, multi-currency ledger & reports.</p>
                </div>
            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'ri-eye-off-line';
            } else {
                passwordInput.type = 'password';
                icon.className = 'ri-eye-line';
            }
        }
    </script>

</body>

</html>
