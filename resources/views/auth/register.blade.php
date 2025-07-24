<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - J & J Flower Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts for script font -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/loginstyle.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f6fbf2; font-family: 'Montserrat', Arial, sans-serif; }
        .logo { width: 50px; height: 50px; margin-right: 12px; }
        .navbar { background: #8ACB88; min-height: 70px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .navbar-brand, .navbar-brand span { color: #fff !important; }
        .navbar-nav .nav-link { color: #f8fff5 !important; font-size: 1.13rem; font-weight: 400; letter-spacing: 1px; }
        .navbar-nav .nav-link:hover { color: #fff !important; }
        .navbar-icons i { color: #f8fff5; font-size: 1.35rem; margin-left: 22px; cursor: pointer; opacity: 0.85; transition: color 0.2s, opacity 0.2s; }
        .navbar-icons i:hover { color: #fff; opacity: 1; }
        .register-main-wrapper { margin-top: 20px; display: flex; justify-content: center; align-items: flex-start; min-height: 60vh; }
        .register-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 32px 28px; min-width: 340px; max-width: 400px; }
        @media (max-width: 900px) {
            .register-main-wrapper { flex-direction: column; align-items: center; }
            .register-card { max-width: 100%; min-width: unset; margin-bottom: 18px; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="/images/logo.png" alt="Logo" class="logo">
            J & J FLOWER SHOP <span>Est. 2023</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
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
    </div>
</nav>
<div class="register-main-wrapper">
    <div class="register-card">
        <h2 class="text-center text-success mb-2" style="font-weight: 700;">JJ Flowershop</h2>
        <p class="text-center text-muted mb-2">Register to create an account</p>
        <form method="POST" action="{{ url('/register') }}">
            @csrf
            <div class="mb-2">
                <label for="first_name" class="form-label visually-hidden">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('first_name') }}" required autofocus oninput="this.value = this.value.replace(/^\s+/, '')">
                @error('first_name')
                    <span class="text-danger" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-2">
                <label for="last_name" class="form-label visually-hidden">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('last_name') }}" required oninput="this.value = this.value.replace(/^\s+/, '')">
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
                <label for="contact_number" class="form-label visually-hidden">Phone Number</label>
                <input type="text" name="contact_number" id="contact_number" class="form-control form-control-sm"
                       placeholder="Phone Number" value="{{ old('contact_number') }}"
                       maxlength="11" pattern="\d{11}" title="Phone number must be exactly 11 digits">
                @error('contact_number')
                    <span class="text-danger" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div id="verification-channel-group" class="mb-2" style="display:none;">
                <label class="form-label">Where do you want to receive your verification code?</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="verification_channel" id="channel_email" value="email" {{ old('verification_channel') == 'email' ? 'checked' : '' }}>
                    <label class="form-check-label" for="channel_email">Gmail</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="verification_channel" id="channel_phone" value="phone" {{ old('verification_channel') == 'phone' ? 'checked' : '' }}>
                    <label class="form-check-label" for="channel_phone">Phone Number</label>
                </div>
                @if($errors->has('verification_channel'))
                    <span class="text-danger" role="alert">{{ $errors->first('verification_channel') }}</span>
                @endif
            </div>
            <div id="at-least-one-error" class="text-danger mb-2" style="display:none; font-size:0.95rem;"></div>
            @if($errors->has('at_least_one'))
                <div class="text-danger mb-2" style="font-size:0.95rem;">{{ $errors->first('at_least_one') }}</div>
            @endif
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
<div class="mobile-footer mt-4 p-3">
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
window.addEventListener('DOMContentLoaded', function() {
    var emailField = document.getElementById('email');
    var phoneField = document.getElementById('contact_number');
    var group = document.getElementById('verification-channel-group');
    var channelEmail = document.getElementById('channel_email');
    var channelPhone = document.getElementById('channel_phone');
    function toggleVerificationChannel() {
        var email = emailField ? emailField.value.trim() : '';
        var phone = phoneField ? phoneField.value.trim() : '';
        if(group) {
            if(email && phone) {
                group.style.display = '';
            } else {
                group.style.display = 'none';
                if(channelEmail) channelEmail.checked = false;
                if(channelPhone) channelPhone.checked = false;
            }
        }
    }
    if(emailField) emailField.addEventListener('input', toggleVerificationChannel);
    if(phoneField) phoneField.addEventListener('input', toggleVerificationChannel);
    toggleVerificationChannel();
    var email = emailField ? emailField.value.trim() : '';
    var phone = phoneField ? phoneField.value.trim() : '';
    if(group && email && phone) {
        group.style.display = '';
    }
});
</script>
<style>
.mobile-footer { background: #8ACB88 !important; color: #fff !important; }
.footer-icons > a { margin-right: 18px !important; }
.footer-icons > a:last-child { margin-right: 0 !important; }
</style>
</body>
</html>
