<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JJ Flowershop') }}</title>

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
            padding-top: 110px;
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
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    <div class="d-flex align-items-center customer-brand" style="gap: .6rem; padding-top: 1px;">
                        <img src="/images/logo.png" alt="JJ Flower Shop" style="height: 64px; background: transparent;" class="me-1">
                        <div class="brand-inclusive" style="font-size: 1.8rem; line-height: 1; letter-spacing: .5px;">
                            J ' J FLOWER
                            <br>
                            <span style="font-size: 1.8rem; font-weight: 400;">SHOP <span class="fs-6">Est. 2023</span></span>
                        </div>
                    </div>

                    <!-- Center block: links (top) + icons (bottom) -->
                    <div class="d-flex flex-column flex-grow-1" style="max-width: 1000px; margin: 0 20px;">
                        <div class="d-flex align-items-center justify-content-center customer-nav-links" style="gap: 2.2rem; padding-top: 0;">
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
                                            if ($pp) { $profileSrc = asset('storage/' . ltrim($pp, '/')); }
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
                            <button class="icon-btn text-white position-relative @if(request()->routeIs('customer.chat.*')) active @endif" id="navbarChatBtn" title="Chat Support" style="background: none; border: none; font-size: 1.35rem; padding: 0 .5rem 0 0;"><i class="bi bi-chat-dots"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <main class="py-4 flex-grow-1">
            <div class="container-fluid">
                @auth
                @yield('content')
                @endauth
            </div>
        </main>
        @php
            $hideFooterOnRoutes = [
                'customer.account.index',
                'customer.address_book.index',
                'customer.account.change_password',
                'customer.orders.index',
                'customer.trackOrders.page',
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
        .customer-right-icons { padding-top: 8px; align-self: flex-end; }
        .customer-right-icons a,
        .customer-right-icons button { margin-top: 2px; }
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
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                    if (alert.classList.contains('show')) {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    }
                });
            }, 2000);

            // Product search and filter functionality for customer dashboard
            const searchInput = document.getElementById('productSearchInput');
            const filterBtn = document.getElementById('productFilterBtn');
            const filterPanel = document.getElementById('productFilterPanel');
            const filterMin = document.getElementById('productFilterMin');
            const filterMax = document.getElementById('productFilterMax');
            const filterApply = document.getElementById('productFilterApply');
            const filterClear = document.getElementById('productFilterClear');

            function performSearch() {
                const searchTerm = searchInput ? searchInput.value : '';
                // preserve current category from URL (default to 'all')
                const currentUrl = new URL(window.location.href);
                const category = (currentUrl.searchParams.get('category') || 'all');
                const minPrice = filterMin && filterMin.value ? filterMin.value : '';
                const maxPrice = filterMax && filterMax.value ? filterMax.value : '';

                // Build URL with search parameters
                const url = new URL(window.location.href);
                url.searchParams.set('search', searchTerm);
                url.searchParams.set('category', category);
                if (minPrice) url.searchParams.set('min_price', minPrice);
                if (maxPrice) url.searchParams.set('max_price', maxPrice);

                // Redirect to the same page with search parameters
                window.location.href = url.toString();
            }

            function clearFilters() {
                if (searchInput) searchInput.value = '';
                if (filterMin) filterMin.value = '';
                if (filterMax) filterMax.value = '';
                
                // Redirect to clean URL
                const url = new URL(window.location.href);
                url.searchParams.delete('search');
                url.searchParams.delete('min_price');
                url.searchParams.delete('max_price');
                window.location.href = url.toString();
            }

            // Event listeners
            if (filterBtn && filterPanel) {
                filterBtn.addEventListener('click', function() {
                    filterPanel.style.display = (filterPanel.style.display === 'none' || !filterPanel.style.display) ? 'block' : 'none';
                });
                
                // Close filter panel when clicking outside
                document.addEventListener('click', function(e){
                    if (filterPanel.style.display === 'block') {
                        const within = filterPanel.contains(e.target) || filterBtn.contains(e.target);
                        if (!within) filterPanel.style.display = 'none';
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });
                // Optional: live typing debounce search
                clearTimeout(searchInput.__t);
                searchInput.addEventListener('input', function(){
                    clearTimeout(searchInput.__t);
                    searchInput.__t = setTimeout(performSearch, 400);
                });
            }

            if (filterApply) {
                filterApply.addEventListener('click', function(){
                    performSearch();
                    if (filterPanel) filterPanel.style.display = 'none';
                });
            }

            if (filterClear) {
                filterClear.addEventListener('click', clearFilters);
            }
        });
    </script>
</body>
</html> 