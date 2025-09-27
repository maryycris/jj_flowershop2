<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'JJ Flowershop')); ?></title>

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

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div id="app">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <!-- New Top Navigation Bar for Customer (standardized for all customer pages) -->
        <nav class="customer-top-navbar" style="background: #8ACB88; color: #fff; padding: 0 6.0vw; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw);">
            <div class="container-fluid px-4 pt-2 pb-1">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-1">
                    <div class="d-flex align-items-center justify-content-center gap-5" style="padding: 0 4.2vw;">
                        <div class="d-flex align-items-center justify-content-center gap-5 pb-1" style="margin-left: 175px;">
                            <a href="<?php echo e(route('customer.dashboard')); ?>" class="nav-link text-white d-flex align-items-center gap-2 <?php if(request()->routeIs('customer.dashboard')): ?> active <?php endif; ?>" style="font-size: 0.9rem;"><i class="bi bi-house-door"></i> Home</a>
                            
                            <a href="<?php echo e(route('customer.products.bouquet-customize')); ?>" class="nav-link text-white d-flex align-items-center gap-2 <?php if(request()->routeIs('customer.products.bouquet-customize')): ?> active <?php endif; ?>" style="font-size: 0.9rem;"><i class="bi bi-brush"></i> Customize</a>
                            <a href="<?php echo e(route('customer.events.book')); ?>" class="nav-link text-white d-flex align-items-center gap-2 <?php if(request()->routeIs('customer.events.book')): ?> active <?php endif; ?>" style="font-size: 0.9rem;"><i class="bi bi-calendar-event"></i> Book Event</a>
                            <a href="<?php echo e(route('customer.notifications.index')); ?>" class="nav-link text-white d-flex align-items-center gap-2 position-relative <?php if(request()->routeIs('customer.notifications.index')): ?> active <?php endif; ?>" style="font-size: 0.9rem;">
                                <i class="bi bi-bell"></i> Notifications
                                <?php if(isset($unreadCount) && $unreadCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;"><?php echo e($unreadCount); ?></span>
                                <?php endif; ?>
                            </a>
                            
                            <div class="dropdown">
                                <button class="nav-link text-white d-flex align-items-center gap-1 btn btn-link p-0" type="button" id="customerUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.9rem; background: none; border: none;">
                                    <i class="bi bi-person-circle" style="font-size: 0.9rem;"></i> <?php echo e(Auth::user()->name ?? "customer's name"); ?>

                                </button>
                                <ul class="dropdown-menu dropdown-menu-end smooth-dropdown" aria-labelledby="customerUserDropdown">
                                    <li><a class="dropdown-item" href="<?php echo e(route('customer.account.index')); ?>"><i class="bi bi-person"></i> MY ACCOUNT</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> LOGOUT</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <div class="d-flex align-items-center">
                        <img src="/images/logo.png" alt="JJ Flower Shop" style="height: 48px; background: transparent;" class="me-2">
                        <div class="fw-bold" style="font-size: 1.3rem; line-height: 1;">J ' J FLOWER<br><span style="font-size: 0.9rem; font-weight: 400;">SHOP <span class="fs-6">Est. 2023</span></span></div>
                    </div>
                    <form class="flex-grow-1 mx-4" style="max-width: 500px;">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search" aria-label="Search">
                            <button class="btn btn-light" type="submit"><i class="bi bi-funnel"></i></button>
                        </div>
                    </form>
                    <div class="d-flex align-items-center gap-4">
                        <a href="<?php echo e(route('customer.favorites')); ?>" class="icon-btn text-white position-relative" title="Add to Favorites" style="font-size: 1.5rem;"><i class="bi bi-heart"></i></a>
                        <a href="<?php echo e(route('customer.cart.index')); ?>" class="icon-btn text-white position-relative"><i class="bi bi-cart" style="font-size: 1.5rem;"></i></a>
                        <button class="icon-btn text-white position-relative" id="navbarChatBtn" title="Chat Support" style="background: none; border: none; font-size: 1.5rem; padding: 0 0.5rem 0 0;"><i class="bi bi-chat-dots"></i></button>
                    </div>
                </div>
            </div>
        </nav>
        <main class="py-4 flex-grow-1">
            <div class="container-fluid">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
        <footer class="footer mt-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                        <div class="small">© <?php echo e(date('Y')); ?> J' J Flower Shop · All rights reserved</div>
                    </div>
                    <div class="col-12 col-md-6 text-md-end">
                        <a href="<?php echo e(route('faq')); ?>" class="me-0"><i class="bi bi-question-circle me-1"></i>FAQ</a>
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

    <?php echo $__env->yieldPushContent('scripts'); ?>

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
</html> <?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/layouts/customer_app.blade.php ENDPATH**/ ?>