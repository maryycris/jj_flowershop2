<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JJ Flower Shop</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('styles')
    <style>
        body { background: #f6faf6; }
        .mobile-topbar { background: #8ACB88; color: #FFF; padding: 0.5rem 1rem; position: sticky; top: 0; z-index: 1030; }
        .mobile-logo { height: 36px; }
        .mobile-header-label { background: #eaf3ea; color: #234c23; font-weight: 600; padding: 0.5rem 1rem; border-bottom: 1px solid #c3e6c3; }
        .mobile-footer { background: #eaf3ea; color: #234c23; font-size: 0.95rem; }
        .icon-btn { background: none; border: none; color: #234c23; font-size: 1.5rem; }
    </style>
</head>
<body>
    <div class="mobile-topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <img src="/images/logo.png" alt="JJ Flower Shop" class="mobile-logo me-2">
            <span class="fw-bold">JJ FLOWER SHOP <span class="fs-6">Est. 2023</span></span>
        </div>
        <div>
            <a href="{{ route('customer.cart.index') }}" class="icon-btn me-2"><i class="bi bi-cart"></i></a>
            <a href="{{ route('login') }}" class="icon-btn"><i class="bi bi-person"></i></a>
        </div>
    </div>
    @if(isset($headerLabel))
        <div class="mobile-header-label">{{ $headerLabel }}</div>
    @endif
    <main class="container py-3">
        @yield('content')
    </main>
    @yield('footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Auto Capitalization Script -->
    <script src="{{ asset('js/auto-capitalization.js') }}"></script>
</body>
</html> 