<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'JJ Flowershop') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Poppins', sans-serif; min-height: 100vh; background: #f8f9fa; padding-top: 120px; }
        .navbar-admin { background-color: #5E8458; height: 70px; border-bottom: none; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); position: fixed; top: 0; z-index: 1000; }
        .navbar-admin-content { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0 6.0vw; height: 70px; padding-top: 10px; }
        .navbar-admin .shop-title { font-size: 1.3rem; line-height: 1; font-weight: bold; color: #fff; }
        .navbar-admin .shop-title span { font-size: 0.9rem; font-weight: 400; }
        .navbar-admin .admin-user { font-size: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .navbar-admin .admin-user i { font-size: 2rem; }
        .navbar-admin-logo { height: 70px; width: 70px; background: transparent; }
        .navbar-admin-hr { border: none; border-top: 2px solid #fff; opacity: 0.7; margin: 0; width: 88%; margin-left: 6%; margin-top: 10px;}
        .navbar-admin-links-row { width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); background: #5E8458; position: fixed; top: 70px; z-index: 999; }
        .navbar-admin-links { display: flex; justify-content: center; align-items: center; gap: 10rem; padding: 0.5rem 0 0.7rem 0; padding-top: 20px;}
        .navbar-admin-links .nav-link { color: #fff !important; font-weight: 500; font-size: 1.08rem; display: flex; align-items: center; gap: 0.5rem; padding: 0; border-radius: 0; border-bottom: 2px solid transparent; transition: border 0.2s; }
        .navbar-admin-links .nav-link.active, .navbar-admin-links .nav-link:hover { color: #fff !important; border-bottom: 2px solid #fff; }
        .navbar-admin .bi { font-size: 1.2rem; }
        /* --- Updated Sidebar Styles --- */
        #wrapper { min-height: calc(100vh - 56px); display: flex; }
        .sidebar-container {
            min-width: 240px;
            max-width: 260px;
            background: #f8f9f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 40px;
            height: 100vh;
        }
        .sidebar-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 36px;
        }
        .sidebar-profile-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin-bottom: 8px;
            border: none;
            overflow: hidden;
        }
        .sidebar-profile-label {
            font-weight: 500;
            color: #222;
            font-size: 1.1rem;
            margin-bottom: 0;
        }
        .sidebar-link {
            display: block;
            text-align: left;
            color: #222;
            text-decoration: none;
            padding: 12px 0 12px 32px;
            font-size: 1.08rem;
            margin-bottom: 2px;
            font-family: 'Poppins', Arial, sans-serif;
            border-left: 4px solid transparent;
            transition: color 0.2s, border-color 0.2s;
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
            left: 32px;
            bottom: 6px;
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
        .card { border-radius: 0.75rem; }
    </style>
    @stack('styles')
    <style>
    /* Hide Bootstrap dropdown caret for admin profile */
    #adminProfileDropdown::after {
        display: none !important;
    }
    /* Hide Bootstrap dropdown caret for Sales Orders */
    #salesOrdersDropdown::after {
        display: none !important;
    }
    
    /* Enhanced Sales Orders Dropdown Styles */
    .navbar-admin-links .dropdown-menu {
        margin-top: 8px;
        min-width: 200px;
    }
    
    .navbar-admin-links .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #5E8458 !important;
    }
    
    .navbar-admin-links .dropdown-item:active {
        background-color: #5E8458;
        color: white !important;
    }
    
    .navbar-admin-links .dropdown-toggle:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }
    </style>
</head>
<body>
<!-- Admin Top Navbar (always at the top) -->
<div class="navbar-admin">
    <div class="navbar-admin-content">
        <div class="d-flex align-items-center gap-3">
            <img src="/images/logo.png" alt="JJ Flower Shop Logo" class="navbar-admin-logo">
            <div class="shop-title">
                J ' J FLOWERSHOP <span class="fs-6">Est. 2023</span>
            </div>
        </div>
        <div class="admin-user dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-white text-decoration-none" id="adminProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid white;">
                @else
                    <i class="bi bi-person-circle text-white"></i>
                @endif
                <span class="fw-semibold">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminProfileDropdown">
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}">My Profile</a></li>
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
    <hr class="navbar-admin-hr">
</div>
<div class="navbar-admin-links-row">
    <div class="navbar-admin-links">
        <a href="{{ route('admin.products.index') }}" class="nav-link @if(request()->routeIs('admin.products.*')) active @endif"><i class="bi bi-grid"></i> Product catalog</a>
        <a href="{{ route('admin.inventory.index') }}" class="nav-link @if(request()->routeIs('admin.inventory.*')) active @endif"><i class="bi bi-box"></i> Inventory</a>
        <div class="nav-link dropdown d-inline-block @if(request()->routeIs('admin.orders.*') || request()->routeIs('admin.walkInOrders.*')) active @endif" style="padding: 0;">
            <a href="#" class="dropdown-toggle text-decoration-none text-white" id="salesOrdersDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.5rem 1rem; display: inline-block;">
                <i class="bi bi-cart"></i> Sales Orders <i class="bi bi-chevron-down ms-1"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="salesOrdersDropdown" style="background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <li><a class="dropdown-item" href="{{ route('admin.orders.index', ['type' => 'online']) }}" style="padding: 0.75rem 1rem; color: #333; transition: background-color 0.2s;">
                    <i class="bi bi-globe me-2"></i> Online Orders
                </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.orders.index', ['type' => 'walkin']) }}" style="padding: 0.75rem 1rem; color: #333; transition: background-color 0.2s;">
                    <i class="bi bi-shop me-2"></i> Walk-in Orders
                </a></li>
            </ul>
        </div>
        <a href="{{ route('admin.chatbox') }}" class="nav-link @if(request()->routeIs('admin.chatbox')) active @endif"><i class="bi bi-chat"></i> Chat</a>
    </div>
</div>
@if(!(request()->routeIs('admin.orders.*') || request()->routeIs('admin.walkInOrders.*') || request()->routeIs('admin.products.*') || request()->routeIs('admin.inventory.index') || request()->routeIs('admin.chatbox')))
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="sidebar-container sidebar-clean d-flex flex-column align-items-center py-4" style="background: #F6FBF4; min-width: 220px; max-width: 260px; height: 100vh; ">
        <div class="sidebar-profile text-center mb-4">
            <div class="sidebar-profile-icon mx-auto mb-2">
                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #4CAF50;">
                @else
                    <i class="bi bi-person-circle" style="font-size: 3.5rem; color: #888;"></i>
                @endif
            </div>
            <div class="sidebar-profile-label" style="font-size: 1.1rem; color: #222; font-weight: 500;">{{ auth()->user()->name }}</div>
        </div>
        <nav class="w-100">
            <ul class="nav flex-column align-items-center align-items-md-start w-100">
                <li class="nav-item w-100 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link @if(request()->routeIs('admin.dashboard')) active @endif">Dashboard</a>
                </li>
                <li class="nav-item w-100 mb-1">
                    <a href="{{ route('admin.analytics') }}" class="sidebar-link @if(request()->routeIs('admin.analytics')) active @endif">Analytics</a>
                </li>
                <li class="nav-item w-100 mb-1">
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link @if(request()->routeIs('admin.users.*')) active @endif">Manage Accounts</a>
                </li>
                <li class="nav-item w-100 mb-1">
                    <a href="{{ route('admin.reports.sales') }}" class="sidebar-link @if(request()->routeIs('admin.reports.sales')) active @endif">Sales Report</a>
                </li>
                <li class="nav-item w-100 mb-1">
                    <a href="{{ route('admin.notifications.index') }}" class="sidebar-link @if(request()->routeIs('admin.notifications.index')) active @endif">Notifications</a>
                </li>
            </ul>
        </nav>
    </div>
    <div id="page-content-wrapper" class="flex-grow-1">
        <div class="container-fluid py-4" style="padding-left: 4.0vw; padding-right: 4.0vw;">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @yield('admin_content')
        </div>
    </div>
</div>
@else
<div class="container-fluid py-4" style="padding-left: 4.0vw; padding-right: 4.0vw;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @yield('admin_content')
</div>
@endif
<div class="container-fluid py-4" style="padding-left: 4.0vw; padding-right: 4.0vw;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @yield('content')
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
@stack('scripts')
</body>
</html> 