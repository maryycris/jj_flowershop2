<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JJ Flower Shop Driver</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f6faf6; }
        .driver-navbar { background: #4b6e4b; color: #fff; }
        .menu-btn { background: none; border: none; color: #234c23; font-size: 2rem; }
        .driver-menu { background: #eaf3ea; position: fixed; top: 0; left: 0; height: 100vh; width: 80vw; max-width: 320px; z-index: 1050; transform: translateX(-100%); transition: transform 0.3s; }
        .driver-menu.show { transform: translateX(0); }
        .driver-menu .nav-link { color: #234c23; font-size: 1.1rem; }
        .driver-menu .nav-link.active, .driver-menu .nav-link:hover { color: #fff; background: #4b6e4b; border-radius: 5px; }
        .driver-menu .logout-btn { color: #c00; }
        .driver-navbar .logout-btn { color: #fff; }
        .driver-navbar .logout-btn:hover { color: #c00; }
    </style>
</head>
<body>
    <nav class="d-flex align-items-center driver-navbar px-3 py-2">
        <button class="menu-btn me-2" id="openMenuBtn"><i class="bi bi-list"></i></button>
        <img src="/images/logo.png" alt="JJ Flower Shop" height="36" class="me-2">
        <span class="fw-bold">JJ FLOWER SHOP</span>
        <form method="POST" action="{{ route('logout') }}" class="ms-auto">
            @csrf
            <button type="submit" class="btn btn-link logout-btn"><i class="bi bi-box-arrow-right" style="font-size: 1.5rem;"></i></button>
        </form>
    </nav>
    <div class="driver-menu shadow" id="driverMenu">
        <div class="d-flex align-items-center justify-content-between px-3 py-3">
            <span class="fw-bold">Menu</span>
            <button class="btn btn-link" id="closeMenuBtn"><i class="bi bi-x-lg"></i></button>
        </div>
        <ul class="nav flex-column px-3">
            <li class="nav-item mb-2"><a href="{{ route('driver.dashboard') }}" class="nav-link">Dashboard</a></li>
            <li class="nav-item mb-2"><a href="{{ route('driver.orders.index') }}" class="nav-link">Orders</a></li>
            <li class="nav-item mb-2"><a href="{{ route('driver.history.index') }}" class="nav-link">Delivery History</a></li>
            <li class="nav-item mb-2"><a href="{{ route('driver.profile') }}" class="nav-link">My Profile</a></li>
            <li class="nav-item mt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link logout-btn">Logout</button>
                </form>
            </li>
        </ul>
    </div>
    <div id="menuOverlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.2); z-index:1040;"></div>
    <main class="container py-3">
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const menu = document.getElementById('driverMenu');
        const overlay = document.getElementById('menuOverlay');
        document.getElementById('openMenuBtn').onclick = function() {
            menu.classList.add('show');
            overlay.style.display = 'block';
        };
        document.getElementById('closeMenuBtn').onclick = function() {
            menu.classList.remove('show');
            overlay.style.display = 'none';
        };
        overlay.onclick = function() {
            menu.classList.remove('show');
            overlay.style.display = 'none';
        };
    </script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Auto Capitalization Script -->
    <script src="{{ asset('js/auto-capitalization.js') }}"></script>
</body>
</html> 