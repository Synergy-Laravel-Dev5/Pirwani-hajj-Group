<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>MAXIMS GROUP - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{ asset('assets/images/logo/logo.png') }}">

    <!-- CSS -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* RIGHT SIDE BACKGROUND */
        .account-page-bg {
            height: 100vh;
            background: url('/assets/images/logo/omer-f-arslan-W0FhhtnMd8k-unsplash.jpg') center center no-repeat;
            background-size: cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* OVERLAY */
        .account-page-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
        }

        /* TEXT */
        .overlay-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
        }

        .overlay-content h2 {
            font-size: 34px;
            font-weight: 700;
        }

        .overlay-content p {
            font-size: 16px;
        }

        /* LEFT CARD */
        .card {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .form-control {
            border-radius: 8px;
            height: 45px;
        }

        .btn-primary {
            height: 45px;
            border-radius: 8px;
            font-weight: 600;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .account-page-bg {
                height: 250px;
            }
        }
    </style>
</head>

<body class="bg-primary-subtle">

    <div class="account-page">
        <div class="container-fluid p-0">
            <div class="row align-items-center g-0">

                <!-- LEFT SIDE -->
                <div class="col-xl-4">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="">
                                <div class="card-body">

                                    <div class="p-4">

                                        <!-- LOGO -->
                                        <div class="mb-4 text-center">
                                            <img src="{{ asset('assets/images/logo/logo.png') }}" height="80">
                                        </div>

                                        <!-- TITLE -->
                                        <div class="text-center mb-3">
                                            <h3>Welcome back</h3>
                                            <p>MAXIMS GROUP HAJJ & UMRAH</p>
                                        </div>

                                        <!-- ERROR MESSAGES -->
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        <!-- FORM -->
                                        <form method="POST" action="{{ route('login.post') }}">
                                            @csrf

                                            <div class="mb-3">
                                                <label>Email</label>
                                                <input type="email" name="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    value="{{ old('email') }}" required placeholder="Enter your email">
                                                @error('email')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label>Password</label>
                                                <input type="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    required placeholder="Enter your password">
                                                @error('password')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="d-flex justify-content-between mb-3">
                                                <div>
                                                    {{-- <input type="checkbox" name="remember"> Remember me --}}
                                                </div>
                                            </div>

                                            <button class="btn w-100" style="background:#211D66; color:#fff;">
    Log In
</button>
                                        </form>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE -->
                <div class="col-xl-8">
                    <div class="account-page-bg">
                        <div class="overlay-content">
                            <h2>Welcome to MAXIMS GROUP HAJJ & UMRAH</h2>
                            <p>Manage Hajj & Umrah Bookings </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

</body>

</html>
