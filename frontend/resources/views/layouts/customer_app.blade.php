<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>JJ Flowershop Customer</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-green: #385E42;
            --light-green: #F0F2ED;
            --accent-green: #A0C49D;
            --text-dark: #333;
            --text-light: #666;
            --border-light: #ddd;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 80px;
        }

        .navbar {
            background-color: var(--primary-green);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .navbar-scrolled {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            height: 30px;
            margin-right: 10px;
        }

        /* Brand font */
        .brand-inclusive { font-family: 'Poppins', sans-serif; font-weight: 400; }

        .nav-link {
            color: rgba(255, 255, 255, 0.75) !important;
            font-weight: 400; /* non-bold */
            margin-right: 15px;
            transition: color 0.3s ease;
        }
        .nav-link i { font-size: 1.15rem; }

        .nav-link:hover,
        .nav-link.active {
            color: white !important;
        }
        .nav-link.active i { color: #fff !important; }
        .icon-btn.active i { color: #fff !important; }

        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .btn-primary:hover {
            background-color: #2a4a34;
            border-color: #2a4a34;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
        }

        .card-title {
            color: var(--primary-green);
            font-weight: 600;
        }

        .card-text {
            color: var(--text-light);
        }

        .alert {
            border-radius: 0.5rem;
            margin-top: 1rem;
        }

        /* Brand sits flush; no hover */
        .customer-brand { }

        /* Footer Styling */
        .footer {
            background-color: #8ACB88;
            color: white;
            padding: 15px 0;
            margin-top: auto; /* Push footer to the bottom */
        }

        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        .social-icons a {
            font-size: 24px;
            margin-right: 15px;
            color: white;
        }

        /* Custom CSS variables for the Admin UI, extendable */
        .primary-bg-dark {
            background-color: var(--primary-green) !important;
        }
        .bg-light-green {
            background-color: var(--light-green) !important;
        }
        
        /* Customer specific styles */
        .bottom-navigation {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 10px 0;
        }
        .bottom-navigation .nav-link {
            color: var(--text-light) !important;
            font-weight: 400;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 5px 0;
        }
        .bottom-navigation .nav-link.active {
            color: var(--primary-green) !important;
        }
        .bottom-navigation .nav-link i {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        .smooth-dropdown {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .smooth-dropdown.show {
            opacity: 1;
            transform: translateY(0);
        }
        .smooth-dropdown {
            opacity: 0;
            transform: translateY(10px);
        }
        .customer-top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
        }
        
        /* Custom Success Alert Global Styling */
        .custom-success-alert-global {
            background: #e8f5e8;
            border: 1px solid #7bb47b;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 0;
            max-width: 500px;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1060;
            box-shadow: 0 4px 12px rgba(123, 180, 123, 0.25);
            animation: slideInDown 0.3s ease-out;
        }
        
        .custom-success-alert-global .alert-icon {
            background: #7bb47b;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }
        
        .custom-success-alert-global .alert-message {
            color: #2d5a2d;
            font-weight: 500;
            flex: 1;
            font-size: 14px;
        }
        
        .custom-success-alert-global .alert-close {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: background-color 0.2s;
            flex-shrink: 0;
        }
        
        .custom-success-alert-global .alert-close:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #333;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-20px);
                opacity: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        @if(session('success'))
            <div class="custom-success-alert-global" role="alert" id="successAlert">
                <div class="alert-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="alert-message">{{ session('success') }}</div>
                <button type="button" class="alert-close" onclick="dismissAlert()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- New Top Navigation Bar for Customer (standardized for all customer pages) -->
        <nav class="customer-top-navbar" style="background: #8ACB88; color: #fff; padding: 0 6.0vw; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw);">
            <div class="container-fluid px-4" style="padding-top: 4px; padding-bottom: 6px;">
                <div class="d-flex justify-content-between" style="align-items: flex-start;">
                    <!-- Brand - full navbar height -->
                    <a href="{{ route('customer.dashboard') }}" class="d-flex align-items-center customer-brand" style="gap: .6rem; padding-top: 1px; text-decoration:none; color:inherit;">
                        <img src="/images/logo.png" alt="JJ Flower Shop" style="height: 64px; background: transparent;" class="me-1">
                        <div class="brand-inclusive" style="font-size: 1.8rem; line-height: 1; letter-spacing: .5px;">
                            J ' J FLOWER
                            <br>
                            <span style="font-size: 1.8rem; font-weight: 400;">SHOP <span class="fs-6">Est. 2023</span></span>
                        </div>
                    </a>

                    <!-- Center block: links (top) + icons (bottom) -->
                    <div class="d-flex flex-column flex-grow-1" style="max-width: 1000px; margin: 0 20px;">
                        <!-- Desktop Nav Links (Hidden on Mobile) -->
                        <div class="d-flex align-items-center justify-content-center customer-nav-links d-none d-lg-flex" style="gap: 2.2rem; padding-top: 0;">
                        <a href="{{ route('customer.dashboard') }}" class="nav-link text-white d-flex align-items-center gap-2 @if(request()->routeIs('customer.dashboard')) active @endif" style="font-size: 0.95rem;"><i class="bi @if(request()->routeIs('customer.dashboard')) bi-house-fill @else bi-house-door @endif"></i> Home</a>
                        <a href="{{ route('customer.products.bouquet-customize') }}" class="nav-link text-white d-flex align-items-center gap-2 @if(request()->routeIs('customer.products.bouquet-customize')) active @endif" style="font-size: 0.95rem;"><i class="bi @if(request()->routeIs('customer.products.bouquet-customize')) bi-brush-fill @else bi-brush @endif"></i> Customize</a>
                        <a href="{{ route('customer.notifications.index') }}" class="nav-link text-white d-flex align-items-center gap-2 position-relative @if(request()->routeIs('customer.notifications.index')) active @endif" style="font-size: 0.95rem;">
                            <i class="bi @if(request()->routeIs('customer.notifications.index')) bi-bell-fill @else bi-bell @endif"></i> Notifications
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <div class="dropdown">
                                <button class="nav-link text-white d-flex align-items-center gap-2 btn btn-link p-0" type="button" id="customerUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.95rem; background: none; border: none;">
                                    @php
                                        $profileSrc = null;
                                        if (Auth::check()) {
                                            $pp = Auth::user()->profile_picture ?? null;
                                            if ($pp) { 
                                                // Check if it's a full URL (from social login) or a local path
                                                if (filter_var($pp, FILTER_VALIDATE_URL)) {
                                                    $profileSrc = $pp;
                                                } else {
                                                    $profileSrc = asset('storage/' . ltrim($pp, '/'));
                                                }
                                            }
                                        }
                                        if (!$profileSrc) { $profileSrc = asset('images/default-avatar.png'); }
                                    @endphp
                                    <img src="{{ $profileSrc }}" alt="Profile" style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,.6); background:#fff;" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';"> {{ Auth::user()->name ?? "customer's name" }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end smooth-dropdown" aria-labelledby="customerUserDropdown">
                                    <li><a class="dropdown-item" href="{{ route('customer.account.index') }}"><i class="bi bi-person"></i> MY ACCOUNT</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> LOGOUT</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end gap-4 customer-right-icons">
                            <a href="{{ route('customer.favorites') }}" class="icon-btn text-white position-relative @if(request()->routeIs('customer.favorites')) active @endif" title="Favorites" style="font-size: 1.35rem;"><i class="bi @if(request()->routeIs('customer.favorites')) bi-heart-fill @else bi-heart @endif"></i></a>
                            <a href="{{ route('customer.cart.index') }}" class="icon-btn text-white position-relative @if(request()->routeIs('customer.cart.index')) active @endif" title="Cart"><i class="bi @if(request()->routeIs('customer.cart.index')) bi-cart-fill @else bi-cart @endif" style="font-size: 1.35rem;"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <main class="py-2 flex-grow-1">
            <div class="container-fluid">
                @auth
                @yield('content')
                @endauth
            </div>
        </main>

        <!-- Global Mobile Bottom Navigation (<=480px) -->
        <div class="mobile-bottom-nav d-lg-none">
            <a href="{{ route('customer.dashboard') }}" class="nav-item @if(request()->routeIs('customer.dashboard')) active @endif">
                <i class="bi @if(request()->routeIs('customer.dashboard')) bi-house-fill @else bi-house-door @endif"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('customer.products.bouquet-customize') }}" class="nav-item @if(request()->routeIs('customer.products.bouquet-customize')) active @endif">
                <i class="bi @if(request()->routeIs('customer.products.bouquet-customize')) bi-brush-fill @else bi-brush @endif"></i>
                <span>Customize</span>
            </a>
            <a href="{{ route('customer.notifications.index') }}" class="nav-item @if(request()->routeIs('customer.notifications.index')) active @endif">
                <i class="bi @if(request()->routeIs('customer.notifications.index')) bi-bell-fill @else bi-bell @endif"></i>
                <span>Notifications</span>
            </a>
            <div class="nav-item profile-dropdown-wrapper @if(request()->routeIs('customer.account.index') || request()->routeIs('customer.address_book.*') || request()->routeIs('customer.orders.*') || request()->routeIs('customer.trackOrders.*')) active @endif" style="position: relative;">
                <a href="#" class="profile-trigger" onclick="event.preventDefault(); toggleProfileMenu(event);" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: inherit;">
                    <i class="bi bi-person"></i>
                    <span>My Profile</span>
                </a>
                <div class="profile-dropdown-menu" id="profileDropdown" style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 10px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 180px; z-index: 1200; display: none; padding: 8px 0;">
                    <a href="{{ route('customer.account.index') }}" class="dropdown-item" style="padding: 10px 16px; display: block; color: #333; text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid #eee;">
                        <i class="bi bi-person me-2"></i>Profile
                    </a>
                    <a href="{{ route('customer.address_book.index') }}" class="dropdown-item" style="padding: 10px 16px; display: block; color: #333; text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid #eee;">
                        <i class="bi bi-book me-2"></i>Address Book
                    </a>
                    <a href="{{ route('customer.orders.index') }}" class="dropdown-item" style="padding: 10px 16px; display: block; color: #333; text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid #eee;">
                        <i class="bi bi-bag me-2"></i>My Purchase
                    </a>
                    <a href="{{ route('customer.trackOrders.page') }}" class="dropdown-item" style="padding: 10px 16px; display: block; color: #333; text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid #eee;">
                        <i class="bi bi-truck me-2"></i>Track Order
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item" style="width: 100%; padding: 10px 16px; display: block; color: #dc3545; text-decoration: none; font-size: 0.9rem; border: none; background: none; text-align: left; cursor: pointer;">
                            <i class="bi bi-box-arrow-right me-2"></i>Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Alert Component -->
        @include('components.alert')
        
        @php
            $hideFooterOnRoutes = [
                'customer.account.index',
                'customer.address_book.index',
                'customer.account.change_password',
                'customer.orders.index',
                'customer.orders.show',
                'customer.trackOrders.page',
                'customer.checkout.*',
                'customer.cart.index',
                'customer.store-credit.history',
            ];
        @endphp
        @unless (request()->routeIs($hideFooterOnRoutes))
        <footer class="footer mt-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                        <div class="small">© {{ date('Y') }} J' J Flower Shop · All rights reserved</div>
                    </div>
                    <div class="col-12 col-md-6 text-md-end">
                        <a href="{{ route('faq') }}" class="me-0"><i class="bi bi-question-circle me-1"></i>FAQ</a>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 text-center">
                        <div class="footer-icons" style="font-size:1.1rem;">
                            <a href="https://www.facebook.com/profile.php?id=100089623153779" target="_blank" style="color:#1877F3;"><i class="bi bi-facebook"></i></a>
                            <a href="https://www.instagram.com/jjflowershop_" target="_blank" style="color:#E1306C;"><i class="bi bi-instagram"></i></a>
                            <a href="https://wa.me/639674184857" target="_blank" style="color:#25D366;"><i class="bi bi-whatsapp"></i></a>
                            <a href="https://mail.google.com/mail/?view=cm&to=jjflowershopph@gmail.com" target="_blank" style="color:#EA4335;"><i class="fab fa-google fa-lg"></i></a>
                            <a href="https://www.google.com/maps?q=Bang-bang+Cordova,+Cebu+Valeriano+Inoc+Street,+Arles+Building+(B-4)" target="_blank" style="color:#dc3545;" title="View Location"><i class="bi bi-geo-alt-fill"></i></a>
                            <a href="tel:09674184857" style="color:#198754;" title="Call 09674184857"><i class="bi bi-telephone-fill"></i></a>
                        </div>
                        <style>
                        .footer-icons > a { margin-right: 15px; }
                        .footer-icons > a:last-child { margin-right: 0; }
                        </style>
    <style>
        /* Divider line only beneath nav links */
        .customer-nav-links { position: relative; }
        .customer-nav-links::after {
            content: '';
            position: absolute;
            left: 0; right: 0; bottom: -6px;
            height: 2px; border-radius: 2px;
            background: rgba(255,255,255,0.6);
        }
        /* Place right icons visually below the divider line */
        .customer-right-icons { 
            padding-top: 14px; 
            align-self: flex-end; 
            display: flex;
            align-items: center;
            padding-right: 10px; /* slight nudge to the right */
        }
        .customer-right-icons a,
        .customer-right-icons button { 
            margin-top: 2px; 
            display: flex;
            align-items: center;
            justify-content: center;
            width: 29px;
            height: 29px;
        }
        
        /* Mobile/Compact Navbar (≤650px) */
        @media (max-width: 650px) {
            /* Reserve space for bottom nav */
            body { padding-bottom: 76px; }

            /* Global Mobile Bottom Nav */
            .mobile-bottom-nav {
                position: fixed;
                bottom: 0; left: 0; right: 0;
                background: rgb(138, 203, 136, 1); /* match top navbar */
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                align-items: stretch;
                padding: 0;
                z-index: 1000;
                box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
                height: 56px;
            }
            .mobile-bottom-nav .nav-item {
                flex: 1 1 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: #fff;
                text-decoration: none;
                gap: 2px;
            }
            .mobile-bottom-nav .nav-item.active { background: rgba(255,255,255,0.18); }
            .mobile-bottom-nav .nav-item i,
            .mobile-bottom-nav .nav-item .bi { font-size: 20px; }
            .mobile-bottom-nav .nav-item span { font-size: 11px; }
            
            /* Profile Dropdown Menu Styles */
            .mobile-bottom-nav .profile-dropdown-wrapper {
                flex: 1 1 0;
                position: relative;
            }
            .mobile-bottom-nav .profile-trigger {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                color: #fff !important;
                text-decoration: none !important;
                cursor: pointer;
                gap: 2px;
            }
            .mobile-bottom-nav .profile-dropdown-menu {
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                margin-bottom: 10px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                min-width: 180px;
                z-index: 1200;
                display: none;
                padding: 8px 0;
                overflow: hidden;
            }
            .mobile-bottom-nav .profile-dropdown-menu.show {
                display: block !important;
            }
            .mobile-bottom-nav .profile-dropdown-menu .dropdown-item {
                padding: 10px 16px;
                display: flex;
                align-items: center;
                gap: 8px;
                justify-content: flex-start;
                color: #333;
                text-decoration: none;
                font-size: 0.9rem;
                border-bottom: 1px solid #eee;
                transition: background 0.2s;
            }
            .mobile-bottom-nav .profile-dropdown-menu .dropdown-item i { color: #6c757d; }
            .mobile-bottom-nav .profile-dropdown-menu .dropdown-item:hover {
                background: #f8f9fa;
            }
            .mobile-bottom-nav .profile-dropdown-menu .dropdown-item:last-child {
                border-bottom: none;
            }
            .mobile-bottom-nav .profile-dropdown-menu button.dropdown-item {
                width: 100%;
                border: none;
                background: none;
                text-align: left;
                cursor: pointer;
                color: #dc3545;
            }
            .mobile-bottom-nav .profile-dropdown-menu button.dropdown-item i { color: #dc3545; }
            .mobile-bottom-nav .profile-dropdown-menu button.dropdown-item:hover {
                background: #f8f9fa;
            }
            
            .customer-top-navbar {
                padding: 0 15px !important; /* compact side padding on mobile */
            }

            /* Make the top navbar compact and consistent across all customer pages */
            .customer-top-navbar > .container-fluid {
                padding-top: 4px !important;
                padding-bottom: 6px !important;
            }
            .customer-brand { gap: .4rem !important; padding-top: 0 !important; }
            .customer-brand img { height: 40px !important; }
            .customer-brand .brand-inclusive { font-size: 1.2rem !important; line-height: 1.1 !important; }
            .customer-right-icons { padding-top: 10px !important; gap: 15px !important; }
            .customer-right-icons a,
            .customer-right-icons button { width: 28px !important; height: 28px !important; font-size: 1.1rem !important; }
            
            .customer-brand {
                gap: 0.4rem !important;
            }
            
            .customer-brand img {
                height: 40px !important;
            }
            
            .brand-inclusive {
                font-size: 1.2rem !important;
            }
            
            .customer-right-icons {
                padding-top: 12px !important;
                padding-right: 12px !important; /* nudge right on mobile */
                gap: 15px !important;
            }
            
            .customer-right-icons a,
            .customer-right-icons button {
                font-size: 1.1rem !important;
                margin-top: 0 !important;
                width: 28px !important;
                height: 28px !important;
            }
        }
        
        @media (max-width: 992px) {
            .customer-right-icons { padding-top: 10px; }
        }
    </style>
                    </div>
                </div>
            </div>
        </footer>
        @endunless
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    <script>
        // Toggle Profile Dropdown Menu
        function toggleProfileMenu(event) {
            event.stopPropagation();
            const trigger = event.currentTarget;
            const wrapper = trigger.closest('.profile-dropdown-wrapper');
            if (!wrapper) return;
            
            const menu = wrapper.querySelector('.profile-dropdown-menu');
            if (!menu) return;
            
            const isVisible = menu.classList.contains('show');
            
            // Close all other dropdowns
            document.querySelectorAll('.profile-dropdown-menu').forEach(m => {
                m.classList.remove('show');
            });
            
            // Toggle this menu
            if (!isVisible) {
                menu.classList.add('show');
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.profile-dropdown-wrapper')) {
                document.querySelectorAll('.profile-dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    </script>
    
    <script>
        // Check authentication status on page load and back button
        function checkAuthStatus() {
            fetch('{{ route("customer.dashboard") }}', {
                method: 'HEAD',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (response.status === 401 || response.status === 403) {
                    window.location.href = '{{ route("login") }}';
                }
            })
            .catch(error => {
                console.log('Auth check failed:', error);
            });
        }

        // Check auth on page load
        checkAuthStatus();

        // Check auth when page becomes visible (back button, tab switch)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                checkAuthStatus();
            }
        });

        // Check auth on focus (when user returns to tab)
        window.addEventListener('focus', checkAuthStatus);

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
        
        // Auto-dismiss success alert
        function dismissAlert() {
            const alert = document.getElementById('successAlert');
            if (alert) {
                alert.style.animation = 'slideOutUp 0.3s ease-in';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        }
        
        // Auto-dismiss after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('successAlert');
            if (alert) {
                setTimeout(() => {
                    dismissAlert();
                }, 3000);
            }
        });
    </script>
    
    <!-- Auto Capitalization Script -->
    <script src="{{ asset('js/auto-capitalization.js') }}"></script>
    @yield('scripts')
</body>
</html> 