<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from zoyothemes.com/silva/html/ by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 14 Apr 2026 05:08:02 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>

    <meta charset="utf-8" />
    <title>Dashboard | Pirwani Hajj Group</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pirwani Hajj Group Management System" />
    <meta name="author" content="Pirwani Hajj Group" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/PIRWANI PNG FILE.png') }}">

    <!-- App css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom Styles for Sidebar & Branding -->
    <style>
        /* Sidebar Logo Box */
        .logo-box {
            height: 100px !important;
            width: 260px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 8px 15px !important;
            background: #FFFFFF !important;
            border-bottom: 1px solid #E2E8F0 !important;
            border-right: 1px solid #E2E8F0 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 1001 !important;
            transition: all 0.2s ease-out !important;
        }
        .logo-box .logo {
            line-height: normal !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
        }
        .logo-box .logo span.logo-lg {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
        }
        .logo-box .logo span.logo-lg img,
        .sidebar-logo-full {
            height: 84px !important;
            max-height: 84px !important;
            width: auto !important;
            max-width: 220px !important;
            object-fit: contain !important;
            filter: drop-shadow(0 2px 6px rgba(201, 168, 76, 0.25)) !important;
            display: block !important;
            margin: 0 auto !important;
        }
        .logo-box .logo span.logo-sm img,
        .sidebar-logo-small {
            height: 40px !important;
            max-height: 40px !important;
            width: 40px !important;
            object-fit: contain !important;
        }
        .app-sidebar-menu {
            padding-top: 100px !important;
            background: #FFFFFF !important;
            transition: all 0.2s ease-out !important;
        }

        /* ── CRITICAL: Hide logo-box and collapse cleanly when sidebar is closed/hidden ── */
        body[data-sidebar="hidden"] .logo-box,
        .left-side-menu.condensed .logo-box,
        body[data-sidebar="hidden"] .sidebar-logo-full,
        body[data-sidebar="hidden"] .sidebar-logo-small {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
            pointer-events: none !important;
            transform: translateX(-100%) !important;
        }
        body[data-sidebar="hidden"] .app-sidebar-menu {
            width: 0 !important;
            overflow: hidden !important;
            visibility: hidden !important;
        }
        body[data-sidebar="hidden"] .topbar-custom {
            left: 0 !important;
        }
        body[data-sidebar="hidden"] .content-page {
            margin-left: 0 !important;
        }

        /* ── Header Topbar: Deep Midnight Navy & Luxury Gold (Logo Themed) ── */
        .topbar-custom {
            background: linear-gradient(135deg, #071527 0%, #0E233E 55%, #08162A 100%) !important;
            border-bottom: 2px solid #C9A84C !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25), 0 1px 0 rgba(201, 168, 76, 0.2) !important;
            height: 70px !important;
            padding: 0 20px !important;
            transition: all 0.2s ease-out !important;
        }

        /* Hamburger Sidebar Toggle Button */
        .topbar-custom .button-toggle-menu {
            background: rgba(201, 168, 76, 0.12) !important;
            border: 1px solid rgba(201, 168, 76, 0.35) !important;
            border-radius: 8px !important;
            color: #F3DC9B !important;
            width: 40px !important;
            height: 40px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }
        .topbar-custom .button-toggle-menu:hover {
            background: linear-gradient(135deg, #D4AF37, #B88E28) !important;
            border-color: #D4AF37 !important;
            color: #071527 !important;
            transform: none !important;
        }
        .topbar-custom .button-toggle-menu i,
        .topbar-custom .button-toggle-menu svg {
            width: 22px !important;
            height: 22px !important;
            stroke-width: 2.2 !important;
        }

        /* Topbar Search */
        .topbar-custom .topbar-search input {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(201, 168, 76, 0.35) !important;
            color: #FFFFFF !important;
            border-radius: 24px !important;
            padding: 7px 16px 7px 38px !important;
            font-size: 13px !important;
            height: 38px !important;
            transition: all 0.2s ease !important;
        }
        .topbar-custom .topbar-search input::placeholder {
            color: rgba(243, 220, 155, 0.6) !important;
        }
        .topbar-custom .topbar-search input:focus {
            background: rgba(255, 255, 255, 0.14) !important;
            border-color: #D4AF37 !important;
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.25) !important;
            color: #FFFFFF !important;
        }
        .topbar-custom .topbar-search i {
            color: #E5C368 !important;
        }

        /* Topbar User Profile Pill */
        .topbar-custom .nav-user {
            background: rgba(201, 168, 76, 0.12) !important;
            border: 1px solid rgba(201, 168, 76, 0.35) !important;
            padding: 4px 12px 4px 6px !important;
            border-radius: 30px !important;
            display: inline-flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
        }
        .topbar-custom .nav-user:hover {
            background: rgba(201, 168, 76, 0.22) !important;
            border-color: #D4AF37 !important;
        }
        .topbar-custom .nav-user .user-avatar-top,
        .topbar-custom .nav-user img {
            width: 32px !important;
            height: 32px !important;
            object-fit: cover !important;
            border: 1.5px solid #C9A84C !important;
            box-shadow: 0 0 8px rgba(201, 168, 76, 0.3) !important;
        }
        .topbar-custom .nav-user .pro-user-name {
            color: #FFFFFF !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
        }
        .topbar-custom .nav-user i {
            color: #F3DC9B !important;
            font-size: 14px !important;
            margin-left: 3px !important;
        }

        /* Profile Dropdown & Logout Menu Item */
        .profile-dropdown {
            background: #0B1E36 !important;
            border-radius: 12px !important;
            border: 1px solid #C9A84C !important;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.45) !important;
            padding: 8px !important;
            min-width: 210px !important;
        }
        .profile-dropdown .noti-title {
            background: rgba(201, 168, 76, 0.1) !important;
            border-radius: 8px !important;
        }
        .profile-dropdown .noti-title small {
            color: #C9A84C !important;
        }
        .profile-dropdown .noti-title h6 {
            color: #FFFFFF !important;
        }
        .profile-dropdown .dropdown-divider {
            border-color: rgba(201, 168, 76, 0.2) !important;
        }
        .profile-dropdown .dropdown-item {
            border-radius: 8px !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #E2E8F0 !important;
            padding: 8px 12px !important;
            transition: all 0.18s ease !important;
        }
        .profile-dropdown .dropdown-item:hover {
            background: rgba(201, 168, 76, 0.2) !important;
            color: #F3DC9B !important;
        }
        .profile-dropdown .dropdown-item i.mdi-lock-reset {
            color: #E5C368 !important;
        }
        .profile-dropdown .logout-menu-btn {
            color: #FF6B6B !important;
            font-weight: 600 !important;
            background: rgba(255, 107, 107, 0.08) !important;
            border: none !important;
            width: 100% !important;
            text-align: left !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            transition: all 0.18s ease !important;
        }
        .profile-dropdown .logout-menu-btn:hover {
            background: #E11D48 !important;
            color: #FFFFFF !important;
        }
        .profile-dropdown .logout-menu-btn:hover i {
            color: #FFFFFF !important;
        }

        /* ══════════════════════════════════════════════════════════════
           GLOBAL LUXURY BUTTON THEME (Matching Header Navy & Gold)
        ══════════════════════════════════════════════════════════════ */
        /* Primary / Create / Update / Submit / Next Buttons */
        .btn-primary,
        .btn-primary:active,
        .btn-primary:focus,
        .btn-next,
        .btn-save-booking {
            background: linear-gradient(135deg, #071527 0%, #0E233E 55%, #15325B 100%) !important;
            color: #F3DC9B !important;
            border: 1px solid #C9A84C !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(7, 21, 39, 0.25) !important;
            transition: all 0.2s ease !important;
        }
        .btn-primary:hover,
        .btn-next:hover,
        .btn-save-booking:hover {
            background: linear-gradient(135deg, #D4AF37 0%, #B88E28 100%) !important;
            color: #071527 !important;
            border-color: #D4AF37 !important;
            box-shadow: 0 4px 14px rgba(201, 168, 76, 0.45) !important;
            transform: translateY(-1px) !important;
        }

        /* Secondary / Back / Cancel Buttons */
        .btn-secondary,
        .btn-outline-secondary {
            background: #F8FAFC !important;
            border: 1px solid #CBD5E1 !important;
            color: #334155 !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }
        .btn-secondary:hover,
        .btn-outline-secondary:hover {
            background: #0E233E !important;
            color: #F3DC9B !important;
            border-color: #C9A84C !important;
            transform: translateY(-1px) !important;
        }

        /* Warning / Gold Action Buttons */
        .btn-warning,
        .btn-outline-warning {
            background: linear-gradient(135deg, #D4AF37 0%, #B88E28 100%) !important;
            color: #071527 !important;
            border: 1px solid #C9A84C !important;
            font-weight: 700 !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(201, 168, 76, 0.3) !important;
            transition: all 0.2s ease !important;
        }
        .btn-warning:hover,
        .btn-outline-warning:hover {
            background: #0E233E !important;
            color: #F3DC9B !important;
            border-color: #C9A84C !important;
            box-shadow: 0 4px 14px rgba(7, 21, 39, 0.4) !important;
            transform: translateY(-1px) !important;
        }

        /* Success / Excel / Download Buttons */
        .btn-success,
        .btn-outline-success {
            background: linear-gradient(135deg, #064E3B 0%, #047857 100%) !important;
            color: #ECFDF5 !important;
            border: 1px solid #10B981 !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(4, 120, 87, 0.25) !important;
            transition: all 0.2s ease !important;
        }
        .btn-success:hover,
        .btn-outline-success:hover {
            background: #059669 !important;
            color: #FFFFFF !important;
            border-color: #059669 !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4) !important;
            transform: translateY(-1px) !important;
        }

        /* Info / View Buttons */
        .btn-info,
        .btn-outline-info {
            background: linear-gradient(135deg, #0C4A6E 0%, #0284C7 100%) !important;
            color: #F0F9FF !important;
            border: 1px solid #38BDF8 !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }
        .btn-info:hover,
        .btn-outline-info:hover {
            background: #0284C7 !important;
            color: #FFFFFF !important;
            border-color: #0284C7 !important;
            transform: translateY(-1px) !important;
        }

        /* Danger / Delete / Trash Buttons */
        .btn-danger,
        .btn-outline-danger {
            background: linear-gradient(135deg, #881337 0%, #BE123C 100%) !important;
            color: #FFF1F2 !important;
            border: 1px solid #F43F5E !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(190, 18, 60, 0.2) !important;
            transition: all 0.2s ease !important;
        }
        .btn-danger:hover,
        .btn-outline-danger:hover {
            background: #E11D48 !important;
            color: #FFFFFF !important;
            border-color: #E11D48 !important;
            box-shadow: 0 4px 14px rgba(225, 29, 72, 0.4) !important;
            transform: translateY(-1px) !important;
        }

        /* Outlined Buttons on Dark / Table Context */
        .btn-outline-primary {
            background: rgba(201, 168, 76, 0.08) !important;
            color: #0E233E !important;
            border: 1.5px solid #0E233E !important;
            font-weight: 600 !important;
        }
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #071527 0%, #0E233E 100%) !important;
            color: #F3DC9B !important;
            border-color: #C9A84C !important;
        }

        /* ── GLOBAL INPUT FOCUS & TAB NAVIGATION HIGHLIGHT (Active Blue Accent) ── */
        .form-control:focus,
        .form-select:focus,
        input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="reset"]):focus,
        textarea:focus {
            border-color: #0d6efd !important;
            background-color: #f0f7ff !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25), 0 0 8px rgba(13, 110, 253, 0.3) !important;
            outline: 0 !important;
            transition: all 0.15s ease-in-out !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection,
        .select2-container--default.select2-container--focus .select2-selection,
        .select2-container--default.select2-container--open .select2-selection {
            border-color: #0d6efd !important;
            background-color: #f0f7ff !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25), 0 0 8px rgba(13, 110, 253, 0.3) !important;
            outline: 0 !important;
        }

        .select2-container {
            width: 100% !important;
        }

        div:focus-within > label.form-label,
        .col-md-2:focus-within > label.form-label,
        .col-md-3:focus-within > label.form-label,
        .col-md-4:focus-within > label.form-label,
        .col-md-6:focus-within > label.form-label,
        .col-md-12:focus-within > label.form-label,
        .mb-2:focus-within > label.form-label,
        .mb-3:focus-within > label.form-label {
            color: #0d6efd !important;
            font-weight: 700 !important;
            transition: color 0.15s ease-in-out !important;
        }
    </style>
</head>

<!-- body start -->

<body data-menu-color="light" data-sidebar="default">
    <div id="app-layout">


        @include('layout.header')

        @yield('content')

        @include('layout.footer')

        <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
        <script src="{{ asset('assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
        <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


        <!-- Apexcharts JS -->
        <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

        <!-- for basic area chart -->
        <script src="{{ asset('assets/apexcharts.com/samples/assets/stock-prices.js') }}"></script>

        <!-- Widgets Init Js -->
        <script src="{{ asset('assets/js/pages/crm-dashboard.init.js') }}"></script>

        <!-- App js-->
        <script src="{{ asset('assets/js/app.js') }}"></script>
        <!-- Datatable Demo App Js -->
        <script src="{{ asset('assets/js/pages/datatable.init.js') }}"></script>

        <!-- App js-->

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof feather !== "undefined") {
                    feather.replace();
                }
            });

            // Auto-focus search input when Select2 dropdown opens
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    const searchInput = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, 10);
            });

            // Ensure Select2 selection dispatches native change event for vanilla event listeners
            $(document).on('select2:select select2:unselect select2:clear', 'select', function() {
                this.dispatchEvent(new Event('change', { bubbles: true }));
            });
        </script>
</body>


</html>
