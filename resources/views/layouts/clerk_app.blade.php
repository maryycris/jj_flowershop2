<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JJ Flower Shop - Clerk</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    @stack('styles')
    <style>
        body { font-family: 'Poppins', sans-serif; min-height: 100vh; background: #f8f9fa;padding: 0 4.0vw; }
        .clerk-navbar-bg {
            background: #5E8458;
            color: #fff;
            min-height: 155px;
            border-bottom: none;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            padding-bottom: 18px;
            position: relative;
            z-index: 10;
        }
        .clerk-navbar-content { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0 6.0vw; height: 70px; padding-top: 10px; }
        .clerk-logo-title { display: flex; align-items: center; gap: 1.2rem; }
        .clerk-logo-img { height: 70px; width: 70px; background: transparent; margin-left: -8px; }
        .clerk-shop-title { font-size: 1.3rem; font-weight: bold; color: #fff; line-height: 1.1; }
        .clerk-shop-title span { font-size: 0.9rem; font-weight: 400; }
        .clerk-user { display: flex; align-items: center; gap: 0.6rem; font-size: 1.1rem; color: #fff; }
        .clerk-user i { font-size: 2rem; }
        .clerk-navbar-divider {border: none; border-top: 2px solid #fff; opacity: 0.7; margin: 0; width: 88%; margin-left: 5.4%; margin-top: 10px; }
        .clerk-navbar-links {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 2.2rem;
            padding-bottom: 0.5rem;
            margin-top: 9px;
            flex-wrap: wrap;
            width: 100%;
            z-index: 11;
        }
        .clerk-navbar-link {
            color: #fff !important;
            font-weight: 500;
            font-size: 0.92rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.1rem;
            text-decoration: none;
            transition: color 0.18s;
            min-width: 70px;
            max-width: 90px;
            padding: 0 2px;
            text-align: center;
            white-space: normal;
            line-height: 1.1;
        }
        .clerk-navbar-link span {
            color: #fff !important;
            font-size: 0.92rem;
            text-align: center;
            display: block;
            margin-top: 2px;
            word-break: break-word;
        }
        .clerk-navbar-link i {
            font-size: 1.2rem;
            margin-bottom: 2px;
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
        .clerk-navbar-spacer {
            height: 60px;
            width: 100%;
        }
    </style>
</head>
<body class="antialiased">
    <div id="app">
        <!-- Clerk Navbar -->
        <nav class="customer-top-navbar" style="background: #8ACB88; color: #fff; padding: 0 6.0vw; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw);">
            <div class="container-fluid px-4 pt-2 pb-1">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-1">
                    <div class="d-flex align-items-center gap-5" style="padding: 0 4.0vw;">
                        <img src="/images/logo.png" alt="JJ Flower Shop" style="height: 48px; background: transparent;" class="me-2">
                        <div class="fw-bold" style="font-size: 1.3rem; line-height: 1;">J ' J FLOWER<br><span style="font-size: 0.9rem; font-weight: 400;">SHOP <span class="fs-6">Est. 2023</span></span></div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <a href="{{ route('clerk.product_catalog.index') }}" class="nav-link text-white d-flex align-items-center gap-1 @if(request()->routeIs('clerk.product_catalog.*')) active @endif"><i class="bi bi-grid"></i> Product catalog</a>
                        <a href="{{ route('clerk.inventory.index') }}" class="nav-link text-white d-flex align-items-center gap-1 @if(request()->routeIs('clerk.inventory.index')) active @endif"><i class="bi bi-clipboard"></i> Inventory</a>
                        <a href="{{ route('clerk.orders.index') }}" class="nav-link text-white d-flex align-items-center gap-1 @if(request()->routeIs('clerk.orders.*')) active @endif"><i class="bi bi-cart"></i> Sales Orders</a>
                        <a href="{{ route('clerk.notifications.index') }}" class="nav-link text-white d-flex align-items-center gap-1 @if(request()->routeIs('clerk.notifications.index')) active @endif"><i class="bi bi-bell"></i> Notifications</a>
                        <a href="#" class="nav-link text-white d-flex align-items-center gap-1 disabled" tabindex="-1" aria-disabled="true"><i class="bi bi-chat"></i> Chat</a>
                    </div>
                    <div class="clerk-user dropdown">
                        <a href="#" class="d-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle" id="clerkProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid white;">
                            @else
                                <i class="bi bi-person-circle text-white"></i>
                            @endif
                            {{ Auth::user()->name ?? 'CLERK' }}
                            <span class="badge bg-light text-dark ms-2">{{ strtoupper(Auth::user()->role) }}</span>
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
            </div>
        </nav>
        <div class="clerk-navbar-spacer"></div>
        <div class="container-fluid py-4">
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global SweetAlert function with checkbox and auto-dismiss
        function showSweetAlertWithCheckbox(title, message, icon = 'success', timer = 5000) {
            return Swal.fire({
                title: title,
                html: `
                    <div class="text-start">
                        <p>${message}</p>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="dontShowAgain">
                            <label class="form-check-label" for="dontShowAgain">
                                Don't show this message again
                            </label>
                        </div>
                    </div>
                `,
                icon: icon,
                showConfirmButton: false,
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
                showSweetAlertWithCheckbox('Success!', '{{ session('success') }}', 'success', 5000);
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