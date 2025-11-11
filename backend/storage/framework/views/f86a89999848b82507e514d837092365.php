<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - J'J FLOWERSHOP</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.png')); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('images/logo.png')); ?>">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts for script font -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/loginstyle.css')); ?>">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Font Awesome CDN for Google icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #f6fbf2; font-family: 'Montserrat', Arial, sans-serif; }
        .logo { width: 50px; height: 50px; margin-right: 12px; }
        .navbar { background: #8ACB88; min-height: 70px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .navbar-brand, .navbar-brand span { color: #fff !important; }
        .navbar-nav .nav-link { color: #f8fff5 !important; font-size: 1.13rem; font-weight: 400; letter-spacing: 1px; }
        .navbar-nav .nav-link:hover { color: #fff !important; }
        .navbar-icons i { color: #f8fff5; font-size: 1.35rem; margin-left: 22px; cursor: pointer; opacity: 0.85; transition: color 0.2s, opacity 0.2s; }
        .navbar-icons i:hover { color: #fff; opacity: 1; }
        .login-main-wrapper { margin-top: 20px; display: flex; justify-content: center; align-items: flex-start; min-height: 60vh; }
        .login-left, .login-right { background: #fff; border-radius: 8px; padding: 32px 28px; }
        .login-left { min-width: 340px; max-width: 400px; }
        .login-right { min-width: 300px; max-width: 350px; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .login-divider { width: 2px;  margin: 0 18px; }
        @media (max-width: 900px) {
            .login-main-wrapper { flex-direction: column; align-items: center; }
            .login-divider { display: none; }
            .login-left, .login-right { max-width: 100%; min-width: unset; margin-bottom: 18px; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="/images/logo.png" alt="Logo" class="logo">
            J'J FLOWERSHOP <span>Est. 2023</span>
        </a>
        <div class="navbar-icons ms-auto">
            <a href="<?php echo e(route('customer.login')); ?>" title="View Cart (Login Required)"><i class="bi bi-cart3"></i></a>
            <div class="dropdown d-inline-block">
                <a href="#" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Login / Profile" style="padding:0; border:none; background:none;">
                    <i class="bi bi-person-circle"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="<?php echo e(route('customer.login')); ?>">Customer Login</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(route('staff.login')); ?>">Staff Login</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<?php if(session('success')): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "<?php echo e(session('success')); ?>",
            confirmButtonColor: '#4CAF50',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
    </script>
<?php endif; ?>
<?php if($errors->any()): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if($errors->has('login_field')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed!',
                text: "<?php echo e($errors->first('login_field')); ?>",
                confirmButtonColor: '#4CAF50',
                confirmButtonText: 'Try Again',
                timer: 3000,
                timerProgressBar: true
            });
        <?php elseif($errors->has('password')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Incorrect Password!',
                text: "<?php echo e($errors->first('password')); ?>",
                confirmButtonColor: '#4CAF50',
                confirmButtonText: 'Try Again',
                timer: 3000,
                timerProgressBar: true
            });
        <?php endif; ?>
    </script>
<?php endif; ?>
<div class="login-main-wrapper">
    <div class="login-left">
        <h4 class="login-title"><span class="login-icon">&#8594;</span> Customer Login</h4>
        <form method="POST" action="<?php echo e(route('customer.login')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label for="login_field" class="form-label">E-Mail Address or Phone Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="login_field" name="login_field" required placeholder="E-Mail Address or Phone Number">
            </div>
            <div class="form-group password-group">
                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                <div class="password-input-wrapper">
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Password">
                    <button type="button" class="show-password-btn" onclick="togglePassword()">&#128065;</button>
                </div>
            </div>
            <button type="submit" class="btn-login">Login</button>
            <div class="forgot-password-link">
                <a href="<?php echo e(route('password.request')); ?>">Forgot password?</a>
            </div>
            <a href="<?php echo e(url('auth/facebook')); ?>" class="btn-facebook"><i class="bi bi-facebook"></i> Facebook</a>
            <a href="<?php echo e(url('auth/google')); ?>" class="btn-google"><i class="bi bi-google"></i> Google</a>
        </form>
    </div>
    <div class="login-divider"></div>
    <div class="login-right">
        <br><br><br>
        <div class="new-customer-title">New Customer</div>
        <div class="new-customer-desc">Register now to enjoy a seamless shopping experience and bring fresh blooms to your doorstep</div>
        <a href="<?php echo e(route('register')); ?>" class="btn-continue">Continue</a>
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
function togglePassword() {
    var pwd = document.getElementById('password');
    if (pwd.type === 'password') {
        pwd.type = 'text';
    } else {
        pwd.type = 'password';
    }
}
</script>
<style>
.mobile-footer { background: #8ACB88 !important; color: #fff !important; }
.footer-icons > a { margin-right: 18px !important; }
.footer-icons > a:last-child { margin-right: 0 !important; }
</style>
</body>
</html> <?php /**PATH C:\xampp\htdocs\JJ_FLOWERSHOP CAPSTONE\backend\../frontend/resources/views/auth/customer_login.blade.php ENDPATH**/ ?>