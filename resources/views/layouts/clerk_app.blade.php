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
        /* Clerk Sidebar Link Styles */
        .clerk-sidebar-link {
            color: #222;
            font-weight: 500;
            font-size: 1.08rem;
            text-decoration: none;
            transition: color 0.18s;
            border-radius: 6px;
            padding: 8px 12px;
            position: relative;
        }
        .clerk-sidebar-link.active {
            color: #4CAF50;
            font-weight: 600;
            border-left: 4px solid transparent;
            background: none;
        }
        .clerk-sidebar-link:hover {
            color: #4CAF50;
            background: #F0F8F0;
        }
        .clerk-sidebar-link.active::after {
            content: '';
            display: block;
            width: 110px;
            height: 2.5px;
            background: #4CAF50;
            position: absolute;
            left: 7px;
            bottom: 6px;
            border-radius: 2px;
        }
        .clerk-sidebar-link:not(.active)::after {
            display: none;
        }
        .clerk-navbar-spacer { height: 28px; width: 100%; }
        
        /* Clerk Sidebar Container */
        .clerk-sidebar-container {
            background: #F6FBF4;
            min-width: 220px;
            max-width: 260px;
            height: calc(100vh - 108px);
            position: fixed;
            top: 108px;
            left: 0;
            z-index: 999;
            padding: 20px 0;
            overflow-y: auto;
        }
        
        .clerk-sidebar-profile {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 20px;
        }
        
        .clerk-sidebar-profile-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #4CAF50;
        }
        
        .clerk-sidebar-profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .clerk-sidebar-profile-icon i {
            font-size: 3rem;
            color: #888;
        }
        
        .clerk-sidebar-profile-label {
            font-size: 1rem;
            color: #222;
            font-weight: 500;
        }
        
        .clerk-sidebar-nav {
            padding: 0 15px;
        }
        
        .clerk-sidebar-nav .nav-item {
            margin-bottom: 5px;
        }
        
        .clerk-main-content {
            margin-left: 260px;
            padding: 20px 4vw;
        }
        
        @media (max-width: 768px) {
            .clerk-sidebar-container {
                display: none;
            }
            .clerk-main-content {
                margin-left: 0;
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
                <a href="{{ route('clerk.inventory.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.inventory.*')) active @endif">
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
        
        @if(!(request()->routeIs('clerk.product_catalog.*') || request()->routeIs('clerk.customize.*') || request()->routeIs('clerk.orders.*') || request()->routeIs('clerk.invoices.*') || request()->routeIs('clerk.inventory.*')))
        <!-- Clerk Sidebar -->
        <div class="clerk-sidebar-container">
            <div class="clerk-sidebar-profile">
                <div class="clerk-sidebar-profile-icon">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture">
                    @else
                        <i class="bi bi-person-circle"></i>
                    @endif
                </div>
                <div class="clerk-sidebar-profile-label">{{ auth()->user()->name ?? 'Clerk' }}</div>
            </div>
            
            <nav class="clerk-sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('clerk.dashboard') }}" class="clerk-sidebar-link @if(request()->routeIs('clerk.dashboard')) active @endif">
                            <i class="bi bi-house me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('clerk.invoices.index') }}" class="clerk-sidebar-link @if(request()->routeIs('clerk.invoices.*')) active @endif">
                            <i class="bi bi-file-earmark-text me-2"></i>Invoices
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('clerk.profile.edit') }}" class="clerk-sidebar-link @if(request()->routeIs('clerk.profile.*')) active @endif">
                            <i class="bi bi-person me-2"></i>Edit Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('clerk.notifications.index') }}" class="clerk-sidebar-link @if(request()->routeIs('clerk.notifications.*')) active @endif">
                            <i class="bi bi-bell me-2"></i>Notifications
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
        
        <!-- Main Content -->
        <div class="@if(!(request()->routeIs('clerk.product_catalog.*') || request()->routeIs('clerk.customize.*') || request()->routeIs('clerk.orders.*') || request()->routeIs('clerk.invoices.*') || request()->routeIs('clerk.inventory.*')))clerk-main-content @else container-fluid py-4 @endif" style="@if(!(request()->routeIs('clerk.product_catalog.*') || request()->routeIs('clerk.customize.*') || request()->routeIs('clerk.orders.*') || request()->routeIs('clerk.invoices.*') || request()->routeIs('clerk.inventory.*')))@else padding-left: 4.0vw; padding-right: 4.0vw; @endif">
            @yield('content')
        </div>
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