<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JJ Flowershop') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

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

        .nav-link {
            color: rgba(255, 255, 255, 0.75) !important;
            font-weight: 600;
            margin-right: 15px;
            transition: color 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white !important;
        }

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
            <div class="container-fluid px-4 pt-2 pb-1">
                <div class="d-flex align-items-center justify-content-center border-bottom pb-1" style="gap: 3rem;">
                    <!-- Home link -->
                    <a href="{{ route('customer.dashboard') }}" class="nav-link text-white d-flex align-items-center gap-2 @if(request()->routeIs('customer.dashboard')) active @endif" style="font-size: 0.9rem;"><i class="bi bi-house-door"></i> Home</a>
                    
                    <!-- Customize link -->
                    <a href="{{ route('customer.products.bouquet-customize') }}" class="nav-link text-white d-flex align-items-center gap-2 @if(request()->routeIs('customer.products.bouquet-customize')) active @endif" style="font-size: 0.9rem;"><i class="bi bi-brush"></i> Customize</a>
                    
                    <!-- Notifications link -->
                    <a href="{{ route('customer.notifications.index') }}" class="nav-link text-white d-flex align-items-center gap-2 position-relative @if(request()->routeIs('customer.notifications.index')) active @endif" style="font-size: 0.9rem;">
                        <i class="bi bi-bell"></i> Notifications
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    
                    <!-- Customer name dropdown with profile picture -->
                    <div class="dropdown">
                        <button class="nav-link text-white d-flex align-items-center gap-2 btn btn-link p-0" type="button" id="customerUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.9rem; background: none; border: none;">
                            @php
                                $profileSrc = null;
                                if (Auth::check()) {
                                    $pp = Auth::user()->profile_picture ?? null;
                                    if ($pp) {
                                        $profileSrc = asset('storage/' . ltrim($pp, '/'));
                                    }
                                }
                                if (!$profileSrc) {
                                    $profileSrc = asset('images/default-avatar.png');
                                }
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
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <div class="d-flex align-items-center">
                        <img src="/images/logo.png" alt="JJ Flower Shop" style="height: 48px; background: transparent;" class="me-2">
                        <div class="fw-bold" style="font-size: 1.3rem; line-height: 1;">J ' J FLOWER<br><span style="font-size: 0.9rem; font-weight: 400;">SHOP <span class="fs-6">Est. 2023</span></span></div>
                    </div>
                    @if(request()->routeIs('customer.dashboard') || request()->routeIs('customer.products.*'))
                    <div class="flex-grow-1 mx-4" style="max-width: 500px; position: relative;">
                        <div class="input-group">
                            <input id="globalSearchInput" type="text" class="form-control" placeholder="Search products..." aria-label="Search">
                            <button id="globalFilterBtn" class="btn btn-light" type="button" title="Filter"><i class="bi bi-funnel"></i></button>
                        </div>
                        <!-- Filter Panel -->
                        <div id="globalFilterPanel" class="card p-3" style="display:none; position:absolute; top: 44px; left:0; right:0; z-index:1060; box-shadow:0 8px 20px rgba(0,0,0,.1);">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label mb-1">Category</label>
                                    <select id="globalFilterCategory" class="form-select form-select-sm">
                                        <option value="all">All</option>
                                        <option value="bouquets">Bouquets</option>
                                        <option value="packages">Packages</option>
                                        <option value="gifts">Gifts</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label mb-1">Min Price</label>
                                    <input id="globalFilterMin" type="number" min="0" class="form-control form-control-sm" placeholder="0">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label mb-1">Max Price</label>
                                    <input id="globalFilterMax" type="number" min="0" class="form-control form-control-sm" placeholder="9999">
                                </div>
                                <div class="col-12 col-md-2 d-grid">
                                    <button id="globalFilterApply" class="btn btn-success btn-sm">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="d-flex align-items-center gap-4">
                        <a href="{{ route('customer.favorites') }}" class="icon-btn text-white position-relative" title="Add to Favorites" style="font-size: 1.5rem;"><i class="bi bi-heart"></i></a>
                        <a href="{{ route('customer.cart.index') }}" class="icon-btn text-white position-relative"><i class="bi bi-cart" style="font-size: 1.5rem;"></i></a>
                        <button class="icon-btn text-white position-relative" id="navbarChatBtn" title="Chat Support" style="background: none; border: none; font-size: 1.5rem; padding: 0 0.5rem 0 0;"><i class="bi bi-chat-dots"></i></button>
                    </div>
                </div>
            </div>
        </nav>
        <main class="py-4 flex-grow-1">
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
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
                    </div>
                </div>
            </div>
        </footer>
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

            // Simple client-side search & filter for product grids on customer pages
            const searchInput = document.getElementById('globalSearchInput');
            const filterBtn = document.getElementById('globalFilterBtn');
            const filterPanel = document.getElementById('globalFilterPanel');
            const filterApply = document.getElementById('globalFilterApply');
            const filterCategory = document.getElementById('globalFilterCategory');
            const filterMin = document.getElementById('globalFilterMin');
            const filterMax = document.getElementById('globalFilterMax');

            function getProductCards() {
                // Works on dashboard and product index grids
                return Array.from(document.querySelectorAll('.product-grid .card'));
            }

            function normalize(text) { return (text || '').toString().toLowerCase(); }

            function getCardData(card) {
                const titleEl = card.querySelector('.card-title, h6');
                const priceEl = card.querySelector('.product-price, .card-text');
                const title = titleEl ? titleEl.textContent.trim() : '';
                // extract numeric price
                const priceText = priceEl ? priceEl.textContent.replace(/[^0-9.]/g, '') : '';
                const price = parseFloat(priceText || '0') || 0;
                return { title, price };
            }

            function applyFilters() {
                const q = normalize(searchInput ? searchInput.value : '');
                const cat = filterCategory ? filterCategory.value : 'all';
                const min = filterMin && filterMin.value ? parseFloat(filterMin.value) : null;
                const max = filterMax && filterMax.value ? parseFloat(filterMax.value) : null;

                const cards = getProductCards();
                cards.forEach(card => {
                    const { title, price } = getCardData(card);

                    // category check based on current page URL
                    let inCategory = true;
                    if (cat && cat !== 'all') {
                        const urlCat = new URL(window.location.href, window.location.origin).searchParams.get('category') || 'all';
                        inCategory = (urlCat.toLowerCase() === cat.toLowerCase());
                    }

                    let match = true;
                    if (q && !normalize(title).includes(q)) match = false;
                    if (min !== null && price < min) match = false;
                    if (max !== null && price > max) match = false;
                    if (!inCategory) match = false;

                    card.closest('.col-6, .col-md-4, .col-lg-3').style.display = match ? '' : 'none';
                });
            }

            if (filterBtn && filterPanel) {
                filterBtn.addEventListener('click', function() {
                    filterPanel.style.display = (filterPanel.style.display === 'none' || !filterPanel.style.display) ? 'block' : 'none';
                });
                document.addEventListener('click', function(e){
                    if (filterPanel.style.display === 'block') {
                        const within = filterPanel.contains(e.target) || filterBtn.contains(e.target);
                        if (!within) filterPanel.style.display = 'none';
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', function(){
                    // small debounce
                    clearTimeout(searchInput.__t);
                    searchInput.__t = setTimeout(applyFilters, 180);
                });
            }
            if (filterApply) {
                filterApply.addEventListener('click', function(){
                    applyFilters();
                    if (filterPanel) filterPanel.style.display = 'none';
                });
            }
        });
        });
    </script>
</body>
</html> 