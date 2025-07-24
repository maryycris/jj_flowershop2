<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'JJ Flowershop')); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #e83e8c;
            --primary-dark: #d63384;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        main {
            flex: 1;
            padding: 2rem 0;
        }

        footer {
            background-color: var(--light-bg);
            padding: 2rem 0;
            margin-top: auto;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .social-links a {
            color: var(--secondary-color);
            margin-left: 1rem;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            color: var(--primary-color);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .dropdown-item {
            padding: 0.7rem 1.5rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .social-links {
                margin-top: 1rem;
            }
            
            .social-links a {
                margin: 0 0.5rem;
            }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-flower"></i> JJ Flowershop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::user()->role === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('admin.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('admin.products.index')); ?>">
                                    <i class="fas fa-box"></i> Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('admin.orders.index')); ?>">
                                    <i class="fas fa-shopping-cart"></i> Orders
                                </a>
                            </li>
                        <?php elseif(Auth::user()->role === 'clerk'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('clerk.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('clerk.orders.index')); ?>">
                                    <i class="fas fa-shopping-cart"></i> Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('clerk.deliveries.index')); ?>">
                                    <i class="fas fa-truck"></i> Deliveries
                                </a>
                            </li>
                        <?php elseif(Auth::user()->role === 'customer'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('customer.dashboard')); ?>">
                                    <i class="fas fa-home"></i> Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('customer.orders.index')); ?>">
                                    <i class="fas fa-shopping-bag"></i> My Orders
                                </a>
                            </li>
                        <?php elseif(Auth::user()->role === 'driver'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('driver.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('driver.deliveries')); ?>">
                                    <i class="fas fa-truck"></i> My Deliveries
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i><span class="visually-hidden">Account Menu</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><h6 class="dropdown-header"><?php echo e(Auth::user()->name); ?></h6></li>
                                <?php if(Auth::user()->role === 'customer'): ?>
                                    <li><a class="dropdown-item" href="<?php echo e(route('customer.account.index')); ?>"><i class="fas fa-user-circle me-2"></i> My Account</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button class="dropdown-item" type="submit">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if(auth()->guard()->guest()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('login')); ?>">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('register')); ?>">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="copyright">
                    &copy; <?php echo e(date('Y')); ?> JJ Flowershop. All rights reserved.
                </div>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script>
        $(document).ready(function() {
            // Auto-dismiss alerts after 5 seconds
            $('.alert-dismissible').fadeTo(5000, 500).slideUp(500, function(){
                $(this).remove();
            });

            // Add shadow to navbar on scroll
            $(window).scroll(function() {
                if ($(window).scrollTop() > 50) {
                    $('.navbar').addClass('shadow');
                } else {
                    $('.navbar').removeClass('shadow');
                }
            });
        });
    </script>
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
</html> <?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/layouts/app.blade.php ENDPATH**/ ?>