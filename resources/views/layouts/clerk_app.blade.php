<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JJ Flower Shop - Clerk</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    @stack('styles')
    <style>
        body { font-family: 'Poppins', sans-serif; min-height: 100vh; background: #f8f9fa; padding-top: 120px; }
        .clerk-navbar-bg {
            background: #5E8458;
            color: #fff;
            min-height: 108px;
            border-bottom: none;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            padding-bottom: 10px;
            position: fixed;
            top: 0;
            z-index: 1000;
        }
        .clerk-navbar-content { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0 6.0vw; height: 56px; padding-top: 6px; }
        .clerk-logo-title { display: flex; align-items: center; gap: 1.2rem; }
        .clerk-logo-img { height: 56px; width: 56px; background: transparent; margin-left: -8px; }
        .clerk-shop-title { font-size: 1.25rem; font-weight: bold; color: #fff; line-height: 1.1; }
        .clerk-shop-title span { font-size: 0.9rem; font-weight: 400; }
        .clerk-user { display: flex; align-items: center; gap: 0.6rem; font-size: 1.1rem; color: #fff; }
        .clerk-user i { font-size: 2rem; }
        .clerk-navbar-divider {border: none; border-top: 2px solid #fff; opacity: 0.7; margin: 0; width: 88%; margin-left: 5.4%; margin-top: 6px; }
        .clerk-navbar-links {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 3rem;
            padding-top: 6px;
            padding-bottom: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
            width: 100%;
            z-index: 11;
            white-space: nowrap;
        }
        .clerk-navbar-link {
            color: #fff !important;
            font-weight: 500;
            font-size: 0.92rem;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: color 0.18s;
            padding: 0 6px;
            text-align: left;
            line-height: 1.1;
        }
        .clerk-navbar-link span {
            color: #fff !important;
            font-size: 0.92rem;
            display: inline-block;
            margin-top: 0;
        }
        .clerk-navbar-link i {
            font-size: 1rem;
            margin-bottom: 0;
            color: #fff !important;
        }
        .clerk-navbar-link.active, .clerk-navbar-link:hover {
            color: #fff !important;
            border-bottom: 2px solid #fff;
        }
        .clerk-navbar-link.active span, .clerk-navbar-link:hover span {
            color: #fff !important;
        }
        @media (max-width: 900px) {
            .clerk-navbar-content, .clerk-navbar-divider { padding-left: 2vw; padding-right: 2vw; }
            .clerk-navbar-links { gap: 1.2rem; }
        }
        /* Hide Bootstrap dropdown caret for clerk profile */
        #clerkProfileDropdown::after {
            display: none !important;
        }
        /* --- Updated Sidebar Styles --- */
        .sidebar-container {
            min-width: 240px;
            max-width: 260px;
            background: #f8f9f4;
            display: flex;
            flex-direction: column;
        }
        .sidebar-profile {
            text-align: center;
            margin-bottom: 2rem;
        }
        .sidebar-profile-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-profile-icon img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .sidebar-profile-icon i {
            font-size: 3.5rem;
            color: #888;
        }
        .sidebar-profile-label {
            font-size: 1.1rem;
            color: #222;
            font-weight: 500;
            margin: 0;
        }
        .sidebar-link {
            display: block;
            text-align: left;
            color: #222;
            text-decoration: none;
            padding: 12px 0 12px 32px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-radius: 0;
            margin-bottom: 0;
            position: relative;
        }
        .sidebar-link.active {
            color: #4CAF50;
            font-weight: 600;
            border-left: 4px solid transparent;
            background: none;
        }
        .sidebar-link:hover {
            color: #4CAF50;
            background: #F0F8F0;
        }
        .sidebar-link.active::after {
            content: '';
            display: block;
            width: 150px;
            height: 2.5px;
            background: #4CAF50;
            position: absolute;
            bottom: 0;
            left: 32px;
            border-radius: 2px;
        }
        .sidebar-link:not(.active)::after {
            display: none;
        }
        .sidebar-container .nav-item {
            width: 100%;
        }
        /* Remove default list styles */
        .sidebar-container ul.nav {
            list-style: none;
        }
        /* Responsive: sidebar stays fixed width */
        @media (max-width: 900px) {
            .sidebar-container { min-width: 180px; max-width: 200px; }
            .sidebar-link { font-size: 1rem; padding-left: 18px; }
        }
        /* Remove Bootstrap's default .active background */
        .sidebar-link.active, .sidebar-link:active {
            background: none !important;
        }
        .clerk-navbar-spacer { height: 28px; width: 100%; }
        
        
        
        #wrapper {
            min-height: calc(100vh - 56px);
            display: flex;
        }
        
        #page-content-wrapper {
            padding: 20px 4vw;
            background: #f8f9fa;
            min-height: calc(100vh - 56px);
        }
        
        @media (max-width: 768px) {
            .sidebar-container {
                display: none;
            }
            #page-content-wrapper {
                padding: 20px 2vw;
            }
        }
    </style>
</head>
<body class="antialiased">
    <div id="app">
        <!-- Clerk Navbar (matches the desired UI) -->
        <nav class="clerk-navbar-bg">
            <div class="clerk-navbar-content">
                <div class="clerk-logo-title">
                    <img src="/images/logo.png" alt="JJ Flower Shop" class="clerk-logo-img">
                    <div class="clerk-shop-title">J ' J FLOWER<br><span>SHOP <span class="fs-6">Est. 2023</span></span></div>
                </div>
                <div class="clerk-user dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle" id="clerkProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid white;">
                        @else
                            <i class="bi bi-person-circle text-white"></i>
                        @endif
                        {{ Auth::user()->name ?? 'CLERK' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="clerkProfileDropdown">
                        <li><a class="dropdown-item" href="{{ route('clerk.profile.edit') }}">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Log out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="clerk-navbar-divider">
            <div class="clerk-navbar-links">
                <a href="{{ route('clerk.product_catalog.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.product_catalog.*')) active @endif">
                    <i class="bi bi-grid"></i>
                    <span>Product catalog</span>
                </a>
                <a href="{{ route('clerk.customize.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.customize.*')) active @endif">
                    <i class="bi bi-brush"></i>
                    <span>Customize</span>
                </a>
                <a href="{{ route('clerk.clerk.inventory.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.clerk.inventory.*')) active @endif">
                    <i class="bi bi-box"></i>
                    <span>Inventory</span>
                </a>
                <a href="{{ route('clerk.orders.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.orders.*')) active @endif">
                    <i class="bi bi-cart"></i>
                    <span>Sales Orders</span>
                </a>
                <a href="{{ route('clerk.loyalty.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.loyalty.*')) active @endif">
                    <i class="bi bi-gift"></i>
                    <span>Loyalty Cards</span>
                </a>
                <a href="#" class="clerk-navbar-link">
                    <i class="bi bi-chat"></i>
                    <span>Chat</span>
                </a>
            </div>
        </nav>
        
        @if(!(request()->routeIs('clerk.product_catalog.*') || request()->routeIs('clerk.customize.*') || request()->routeIs('clerk.orders.*') || request()->routeIs('clerk.clerk.inventory.*') || request()->routeIs('clerk.inventory.*')))
        <div class="d-flex" id="wrapper">
            <!-- Clerk Sidebar -->
            <div class="sidebar-container sidebar-clean d-flex flex-column align-items-center" style="background: #F6FBF4; min-width: 220px; max-width: 260px; height: 100vh; padding-top: 48px;">
                <div class="sidebar-profile text-center mb-4">
                    <div class="sidebar-profile-icon mx-auto mb-2">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #4CAF50;">
                        @else
                            <i class="bi bi-person-circle" style="font-size: 3.5rem; color: #888;"></i>
                        @endif
                    </div>
                    <div class="sidebar-profile-label" style="font-size: 1.1rem; color: #222; font-weight: 500;">{{ auth()->user()->name ?? 'Clerk' }}</div>
                </div>
                <nav class="w-100">
                    <ul class="nav flex-column align-items-center align-items-md-start w-100">
                        <li class="nav-item w-100 mb-1">
                            <a href="{{ route('clerk.dashboard') }}" class="sidebar-link @if(request()->routeIs('clerk.dashboard')) active @endif">Dashboard</a>
                        </li>
                        <li class="nav-item w-100 mb-1">
                            <a href="{{ route('clerk.invoices.index') }}" class="sidebar-link @if(request()->routeIs('clerk.invoices.*')) active @endif">Invoices</a>
                        </li>
                        <li class="nav-item w-100 mb-1">
                            <a href="{{ route('clerk.profile.edit') }}" class="sidebar-link @if(request()->routeIs('clerk.profile.*')) active @endif">Edit Profile</a>
                        </li>
                        <li class="nav-item w-100 mb-1">
                            <a href="{{ route('clerk.notifications.index') }}" class="sidebar-link @if(request()->routeIs('clerk.notifications.*')) active @endif">Notifications</a>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div id="page-content-wrapper" class="flex-grow-1">
                @yield('content')
            </div>
        </div>
        @else
        <!-- Main Content -->
        <div class="container-fluid py-4" style="padding-left: 4.0vw; padding-right: 4.0vw;">
            @yield('content')
        </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global SweetAlert function with OK button and auto-dismiss
        function showSweetAlertWithCheckbox(title, message, icon = 'success', timer = 3000) {
            return Swal.fire({
                title: title,
                text: message,
                icon: icon,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#4CAF50',
                timer: timer,
                timerProgressBar: true,
                allowOutsideClick: true,
                didOpen: () => {
                    // Auto-dismiss after specified time
                    setTimeout(() => {
                        Swal.close();
                    }, timer);
                }
            });
        }

        // Show success message on page load if exists
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showSweetAlertWithCheckbox('Success!', '{{ session('success') }}', 'success', 3000);
            @endif
        });
    </script>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                    if (alert.classList.contains('show')) {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    }
                });
            }, 2000);
        });
    </script>
</body>
</html> 