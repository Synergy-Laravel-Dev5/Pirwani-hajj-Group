    <!-- Topbar Start -->
    <div class="topbar-custom">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                    <li>
                        <button class="button-toggle-menu nav-link" aria-label="Toggle Sidebar">
                            <i data-feather="menu" class="noti-icon"></i>
                        </button>
                    </li>
                    <li class="d-none d-lg-block ms-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" style="background: rgba(201, 168, 76, 0.18); color: #F3DC9B; border: 1px solid rgba(201, 168, 76, 0.45); font-size: 11px; font-weight: 700; padding: 5px 12px; border-radius: 20px; letter-spacing: 0.5px;">
                                🕋 PIRWANI HAJJ GROUP
                            </span>
                            <h5 class="mb-0 text-white fw-bold" style="font-size: 15px; letter-spacing: 0.3px;">
                                {{ Auth::user()->name ?? 'Admin User' }}
                            </h5>
                        </div>
                    </li>
                </ul>

                <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center gap-2">

                    <li class="d-none d-lg-block">
                        <div class="position-relative topbar-search">
                            <input type="text" class="form-control"
                                placeholder="Search here...">
                            <i class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                        </div>
                    </li>

                    <li class="dropdown notification-list topbar-dropdown">
                        <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="{{ asset('assets/images/users/user-5.jpg') }}" alt="user-image"
                                class="rounded-circle user-avatar-top">
                            <span class="pro-user-name ms-1">
                                {{ Auth::user()->name ?? 'Admin User' }}
                                <i class="mdi mdi-chevron-down"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown shadow-lg">
                            <div class="dropdown-header noti-title py-2 px-3">
                                <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 0.5px; font-size: 10.5px;">Signed in as</small>
                                <h6 class="text-overflow m-0 fw-bold text-dark" style="font-size: 13.5px;">{{ Auth::user()->name ?? 'Admin' }}</h6>
                            </div>

                            <div class="dropdown-divider my-1"></div>

                            <a href="{{ route('change.password') }}" class="dropdown-item notify-item d-flex align-items-center gap-2 py-2">
                                <i class="mdi mdi-lock-reset fs-16 text-primary"></i>
                                <span>Change Password</span>
                            </a>

                            <div class="dropdown-divider my-1"></div>

                            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="dropdown-item notify-item logout-menu-btn d-flex align-items-center gap-2 py-2">
                                    <i class="mdi mdi-logout fs-16 text-danger"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="app-sidebar-menu">
        <div class="h-100" data-simplebar>
            <div id="sidebar-menu">
                <div class="logo-box text-center">
                    <a class="logo logo-dark" href="{{ route('dashboard') }}">
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}?v={{ time() }}" alt="Pirwani Hajj Group"
                                class="sidebar-logo-full"
                                style="height: 84px; width: auto; max-width: 220px; object-fit: contain;">
                        </span>
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}?v={{ time() }}" alt="Pirwani Hajj Group"
                                class="sidebar-logo-small"
                                style="height: 40px; width: auto; object-fit: contain;">
                        </span>
                    </a>
                </div>
                <ul id="side-menu">
                    <li class="menu-title">Menu</li>
                    <li>
                        <a href="{{ route('dashboard') }}">
                            <i data-feather="home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#sidebarUserManagement" data-bs-toggle="collapse">
                            <i data-feather="users"></i>
                            <span>User Management</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse" id="sidebarUserManagement">
                            <ul class="nav-second-level">

                                @can('role_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('role.index') }}">
                                            Role
                                        </a>
                                    </li>
                                @endcan

                                @can('user_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('user.index') }}">
                                            User
                                        </a>
                                    </li>
                                @endcan
                                @can('user_activity_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('user_activity.index') }}">
                                            User Activity
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#sidebarLeadManagement" data-bs-toggle="collapse">
                            <i data-feather="user-plus"></i>
                            <span>Lead Management</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarLeadManagement">
                            <ul class="nav-second-level">
                                {{-- @can('lead_view') --}}
                                <li>
                                    <a class="tp-link" href="{{ route('lead.index') }}">
                                        All Leads
                                    </a>
                                </li>
                                {{-- @endcan --}}
                                {{-- @can('lead_create')
                                    <li>
                                        <a class="tp-link" href="{{ route('lead.create') }}">
                                            Add Lead
                                        </a>
                                    </li>
                                @endcan --}}
                            </ul>
                        </div>
                    </li>

                    @can('client_view')
                        <li>
                            <a href="{{ route('client.index') }}">
                                <i data-feather="user-plus"></i>
                                <span>Clients</span>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <a href="{{ route('company.index') }}">
                            <i data-feather="briefcase"></i>
                            <span>Companies</span>
                        </a>
                    </li>
                    <li>
                        <a href="#sidebarPackageManagement" data-bs-toggle="collapse">
                            <i data-feather="package"></i>
                            <span>Package Management</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarPackageManagement">
                            <ul class="nav-second-level">
                                <li>
                                    <a class="tp-link" href="{{ route('package.index') }}">
                                        Packages
                                    </a>
                                </li>
                                @can('hotel_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('hotel.index') }}">
                                            Hotels
                                        </a>
                                    </li>
                                @endcan
                                @can('airline_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('airline.index') }}">
                                            Airlines
                                        </a>
                                    </li>
                                @endcan
                                <li>
                                    <a class="tp-link" href="{{ route('flight.index') }}">
                                        Flights
                                    </a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ route('route.index') }}">
                                        Routes
                                    </a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ route('travel-route.index') }}">
                                        Travel Routes
                                    </a>
                                </li>
                                @can('train_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('train.index') }}">
                                            Trains
                                        </a>
                                    </li>
                                @endcan
                                @can('vehicle_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('vehicle.index') }}">
                                            Vehicles
                                        </a>
                                    </li>
                                @endcan
                                <li>
                                    <a class="tp-link" href="{{ route('training-session.index') }}">
                                        Training Sessions
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @can('booking_view')
                        <li>
                            <a href="{{ route('booking.index') }}">
                                <i data-feather="calendar"></i>
                                <span>Booking</span>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <a href="{{ route('haji-group.index') }}">
                            <i data-feather="users"></i>
                            <span>Haji Pilgrim Groups</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('room-inventory.index') }}">
                            <i data-feather="home"></i>
                            <span>Rooming List & Stock</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('arrival-group.index') }}">
                            <i data-feather="arrow-down-left"></i>
                            <span>Arrival Groups</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('departure-group.index') }}">
                            <i data-feather="arrow-up-right"></i>
                            <span>Departure Groups</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hajj-application.index') }}">
                            <i data-feather="file-text"></i>
                            <span>Hajj Applications</span>
                        </a>
                    </li>
                    @can('expense_view')
                        <li>
                            <a href="{{ route('expense.index') }}">
                                <i data-feather="dollar-sign"></i>
                                <span>Add Expense</span>
                            </a>
                        </li>
                    @endcan
                    @can('expense_transaction_view')
                        <li>
                            <a href="{{ route('expense.transaction.index') }}">
                                <i data-feather="credit-card"></i>
                                <span>Expense Transactions</span>
                            </a>
                        </li>
                    @endcan

                    @can('client_Transactions_view')
                        <li>
                            <a href="{{ route('transaction.index') }}">
                                <i data-feather="list"></i>
                                <span>Transactions</span>
                            </a>
                        </li>
                    @endcan
                    @can('Ledger_Filter_view')
                        <li>
                            <a href="{{ route('transaction.ledger.filter') }}">
                                <i data-feather="filter"></i>
                                <span>Ledger Filter</span>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <a href="{{ route('expense.transaction.report.filter') }}">
                            <i data-feather="filter"></i>
                            <span>Expense Filter</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('transaction.company-ledger.filter') }}">
                            <i data-feather="filter"></i>
                            <span>Company Filter</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
