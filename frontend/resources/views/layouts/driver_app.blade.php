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
        .nav-link, .navbar-brand { color: #fff !important; }
        .nav-link.active { font-weight: bold; }
    </style>
</head>
<body>
<nav class="navbar driver-navbar px-4">
    <a class="navbar-brand" href="#">
        <img src="/images/logo.png" alt="JJ Flower Shop" height="40" class="me-2">
        JJ FLOWER SHOP <span class="fs-6">Driver</span>
    </a>
    <ul class="nav">
        <li class="nav-item"><a class="nav-link" href="{{ route('driver.dashboard') }}">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('driver.deliveries') }}">Deliveries</a></li>
    </ul>
    <div class="ms-auto">
        <div class="dropdown">
            <button class="btn btn-link text-white" type="button" id="driverUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person" style="font-size: 1.7rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="driverUserDropdown">
                <li class="dropdown-item text-muted">{{ Auth::user()->name ?? 'Driver' }}</li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid py-4">
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Auto Capitalization Script -->
<script src="{{ asset('js/auto-capitalization.js') }}"></script>
@stack('scripts')
@yield('scripts')
</body>
</html> 