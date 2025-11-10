<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - J'J FLOWERSHOP</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts for script font -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/loginstyle.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Font Awesome CDN for Google icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        html, body { 
            margin: 0; 
            padding: 0; 
        }
        body { 
            background: #f6fbf2; 
            font-family: 'Montserrat', Arial, sans-serif; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
        }
        .logo { width: 50px; height: 50px; margin-right: 12px; }
        .navbar { background: #8ACB88; min-height: 70px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .navbar-brand, .navbar-brand span { color: #fff !important; }
        .navbar-nav .nav-link { color: #f8fff5 !important; font-size: 1.13rem; font-weight: 400; letter-spacing: 1px; }
        .navbar-nav .nav-link:hover { color: #fff !important; }
        .navbar-icons i { color: #f8fff5; font-size: 1.35rem; margin-left: 22px; cursor: pointer; opacity: 0.85; transition: color 0.2s, opacity 0.2s; }
        .navbar-icons i:hover { color: #fff; opacity: 1; }
        .page-content { 
            flex: 1 0 auto; 
            display: flex; 
            flex-direction: column; 
        }
        .register-main-wrapper { 
            margin-top: 20px; 
            display: flex; 
            justify-content: center; 
            align-items: flex-start; 
            flex: 1; 
            padding-bottom: 20px; 
        }
        .register-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 32px 28px; min-width: 340px; max-width: 400px; }
        .mobile-footer { 
            flex-shrink: 0; 
            background: #8ACB88 !important; 
            color: #fff !important; 
            width: 100%; 
            margin-top: auto; 
        }
        @media (max-width: 900px) {
            .register-main-wrapper { flex-direction: column; align-items: center; }
            .register-card { max-width: 100%; min-width: unset; margin-bottom: 18px; }
        }
    </style>
</head>
<body>
<div class="page-content">
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="/images/logo.png" alt="Logo" class="logo">
            J'J FLOWERSHOP <span>Est. 2023</span>
        </a>
        <div class="navbar-icons ms-auto">
            <a href="{{ route('customer.login') }}" title="View Cart (Login Required)"><i class="bi bi-cart3"></i></a>
            <div class="dropdown d-inline-block">
                <a href="#" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Login / Profile" style="padding:0; border:none; background:none;">
                    <i class="bi bi-person-circle"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="{{ route('customer.login') }}">Customer Login</a></li>
                    <li><a class="dropdown-item" href="{{ route('staff.login') }}">Staff Login</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
@if(session('error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(str_contains(session('error'), 'already have an account'))
            Swal.fire({
                icon: 'warning',
                title: 'Account Already Exists!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#4CAF50',
                confirmButtonText: 'Go to Login',
                timer: 3000,
                timerProgressBar: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('customer.login') }}";
                }
            });
        @else
            Swal.fire({
                icon: 'info',
                title: 'Registration Required!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#4CAF50',
                confirmButtonText: 'Register Now',
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    </script>
@endif
@if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#4CAF50',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
    </script>
@endif
<div class="register-main-wrapper">
    <div class="register-card">
        <h2 class="text-center text-success mb-2" style="font-weight: 700;">JJ Flowershop</h2>
        <p class="text-center text-muted mb-2">Register to create an account</p>
        <div class="alert alert-info text-center mb-3" style="font-size: 0.9rem;">
            <strong>Create your account:</strong><br>
            Fill out the form below to get started
        </div>
        <form method="POST" action="{{ url('/register') }}">
            @csrf
            <div class="mb-2">
                <label for="first_name" class="form-label visually-hidden">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('first_name') }}" required autofocus style="text-transform: capitalize;">
                @error('first_name')
                    <span class="text-danger" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-2">
                <label for="last_name" class="form-label visually-hidden">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('last_name') }}" required style="text-transform: capitalize;">
                @error('last_name')
                    <span class="text-danger" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-2">
                <label for="email" class="form-label visually-hidden">Email</label>
                <input type="email" name="email" id="email" class="form-control form-control-sm" placeholder="Email (Gmail)" value="{{ old('email') }}">
                @error('email')
                    <span class="text-danger" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-2">
                <label for="password" class="form-label visually-hidden">Password</label>
                <input type="password" name="password" id="password" class="form-control form-control-sm" placeholder="Password" required>
                @error('password')
                    <span class="text-danger" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-2">
                <label for="password_confirmation" class="form-label visually-hidden">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-sm" placeholder="Confirm Password" required>
            </div>
            <div class="d-grid mb-2">
                <button type="submit" class="btn btn-success btn-sm">Sign Up</button>
            </div>
            <div class="text-center">
                <p class="mb-0" style="font-size: 0.95rem;">Already have an account? <a href="{{ route('login') }}" class="text-decoration-none text-success">Login here</a></p>
            </div>
        </form>
    </div>
</div>
</div>
<div class="mobile-footer p-3">
    <div class="row">
        <div class="col-12 text-center">
            <div class="footer-icons" style="font-size:1.3rem;">
            <a href="https://www.facebook.com/profile.php?id=100089623153779" target="_blank" style="color:#1877F3;"><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/jjflowershop_" target="_blank" style="color:#E1306C;"><i class="bi bi-instagram"></i></a>
            <a href="https://wa.me/639674184857" target="_blank" style="color:#25D366;"><i class="bi bi-whatsapp"></i></a>
            <a href="https://mail.google.com/mail/?view=cm&to=jjflowershopph@gmail.com" target="_blank" style="color:#EA4335;"><i class="fab fa-google fa-lg"></i></a>
            <a href="https://www.google.com/maps?q=Bang-bang+Cordova,+Cebu+Valeriano+Inoc+Street,+Arles+Building+(B-4)" target="_blank" style="color:#dc3545;" title="View Location"><i class="bi bi-geo-alt-fill"></i></a>
            <a href="tel:09674184857" style="color:#198754;" title="Call 09674184857"><i class="bi bi-telephone-fill"></i></a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// JavaScript for form validation and social login
window.addEventListener('DOMContentLoaded', function() {
    // Prevent leading spaces and ensure proper case
    function preventLeadingSpaces(input) {
        input.addEventListener('input', function(e) {
            if (e.target.value.startsWith(' ')) {
                e.target.value = e.target.value.trim();
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === ' ' && e.target.selectionStart === 0) {
                e.preventDefault();
            }
        });
    }
    
    // Apply to all text inputs
    document.querySelectorAll('input[type="text"]').forEach(preventLeadingSpaces);
    
    console.log('Registration form loaded successfully');
});
</script>
<style>
.footer-icons > a { margin-right: 18px !important; }
.footer-icons > a:last-child { margin-right: 0 !important; }
</style>
</body>
</html>
