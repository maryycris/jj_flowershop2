<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JJ Flower Shop Clerk</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/clerk-styles.css') }}">
    @stack('styles')
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
                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications icon (moved beside profile) -->
                    <a href="{{ route('clerk.notifications.index') }}" class="text-white text-decoration-none" title="Notifications">
                        <i class="bi bi-bell" style="font-size: 1.1rem;"></i>
                    </a>
                    <!-- Chat icon (moved beside profile) -->
                    <a href="#" class="text-white text-decoration-none" title="Chat">
                        <i class="bi bi-chat" style="font-size: 1.1rem;"></i>
                    </a>
                    <div class="clerk-user dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle" id="clerkProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                        @if(auth()->user()->profile_picture)
                            @php
                                $pp = auth()->user()->profile_picture;
                                $profileSrc = filter_var($pp, FILTER_VALIDATE_URL) ? $pp : asset('storage/' . $pp);
                            @endphp
                            <img src="{{ $profileSrc }}" alt="Profile Picture" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid white;" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
                        @else
                            <i class="bi bi-person-circle text-white"></i>
                        @endif
                        {{ Auth::user()->name ?? 'CLERK' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="clerkProfileDropdown">
                        <li><a class="dropdown-item" href="{{ route('clerk.dashboard') }}">Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('clerk.profile.edit') }}">Edit Profile</a></li>
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
            <hr class="clerk-navbar-divider">
            <div class="clerk-navbar-links">
                <a href="{{ route('clerk.product_catalog.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.product_catalog.*')) active @endif">
                    <i class="bi bi-grid"></i>
                    <span>Product Catalog</span>
                </a>
                <a href="{{ route('clerk.inventory.manage') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.inventory.*')) active @endif">
                    <i class="bi bi-box"></i>
                    <span>Inventory</span>
                </a>
                <a href="{{ route('clerk.invoices.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.invoices.*')) active @endif">
                    <i class="bi bi-receipt"></i>
                    <span>Invoices</span>
                </a>
                <a href="{{ route('clerk.orders.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.orders.*')) active @endif">
                    <i class="bi bi-cart"></i>
                    <span>Sales Orders</span>
                </a>
                <a href="{{ route('clerk.loyalty.index') }}" class="clerk-navbar-link @if(request()->routeIs('clerk.loyalty.*')) active @endif">
                    <i class="bi bi-gift"></i>
                    <span>Loyalty Cards</span>
                </a>
                <!-- Chat link removed - icon moved beside profile -->
            </div>
        </nav>
        
        <!-- Main Content (sidebar removed) -->
        <div class="container-fluid py-4" style="padding-left: 4.0vw; padding-right: 4.0vw;">
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
    <!-- Auto Capitalization Script -->
    <script src="{{ asset('js/auto-capitalization.js') }}"></script>
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