<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J & J Flower Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts for script font -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f6fbf2;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        html {
            scroll-behavior: smooth;
        }
        .scroll-offset {
            scroll-margin-top: 90px;
        }
        .product-box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .product-box:hover {
            box-shadow: 0 8px 24px rgba(60,60,60,0.18);
            transform: scale(1.04);
            cursor: pointer;
        }
        .navbar {
            background: #8ACB88;
            min-height: 70px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .navbar-brand {
            font-weight: 400;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            letter-spacing: 1px;
            color: #fff;
        }
        .navbar-brand .logo {
            width: 50px;
            height: 50px;
            margin-right: 12px;
        }
        .navbar-brand span {
            font-size: 0.95rem;
            font-weight: 300;
            margin-left: 8px;
            color: #fff;
        }
        .navbar-nav {
            display: flex !important;
            flex-direction: row !important;
            gap: 48px !important;
        }
        .navbar-nav .nav-link {
            color: #f8fff5 !important;
            font-size: 1.13rem;
            font-weight: 400;
            letter-spacing: 1px;
            transition: color 0.2s;
        }
        .navbar-nav .nav-link:hover {
            color: #fff !important;
        }
        .navbar-icons {
            display: flex;
            align-items: center;
        }
        .navbar-icons i {
            color: #f8fff5;
            font-size: 1.35rem;
            margin-left: 22px;
            cursor: pointer;
            opacity: 0.85;
            transition: color 0.2s, opacity 0.2s;
        }
        .navbar-icons i:hover {
            color: #fff;
            opacity: 1;
        }
        .main-section {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 0 0 0;
        }
        .flower-img {
            max-width: 2000px;
            width: 100%;
            height: auto;
            border-radius: 0;
            margin-left: -118px;
            margin-top: -5px;
            margin-bottom: 30px;
            box-shadow: none;
            filter: drop-shadow(0 16px 48px rgba(37, 37, 37, 0.18));
            transform: scaleX(-1);
        }
        .image-resize {
            height: 580px;
            width: 1300px;
        }
        .content-box {
            background: rgba(115, 174, 113, 0.85);
            color: #fff;
            border-radius: 1px;
            padding: 48px 38px 38px 38px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            position: relative;
            min-width: 370px;
            max-width: 470px;
            text-align: center;
            margin-left: -100px;
            margin-top: -20px;
        }
        .content-box h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 2.7rem;
            margin-bottom: 0.2rem;
            font-weight: 400;
        }
        .content-box h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 2.2rem;
            margin-bottom: 1rem;
            font-weight: 400;
        }
        .content-box .subtitle {
            font-style: italic;
            font-size: 1.18rem;
            margin-bottom: 1.2rem;
            color: #f8fff5;
        }
        .content-box hr {
            border-top: 1px solid #e0e0e0;
            margin: 1.2rem 0;
        }
        .content-box p {
            font-size: 1.01rem;
            margin-bottom: 1.5rem;
            color: #f8fff5;
        }
        .btn-shop {
            background: #f3f3f3;
            color: #4a7c59;
            border: none;
            border-radius: 6px;
            padding: 9px 26px;
            font-size: 1.08rem;
            font-weight: 400;
            transition: background 0.2s, color 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .btn-shop:hover {
            background: #e0e0e0;
            color: #3a5c39;
        }
        @media (max-width: 991px) {
            .main-section {
                flex-direction: column;
                padding: 20px 0;
            }
            .content-box {
                margin-top: 30px;
                min-width: unset;
            }
            .flower-img {
                margin-left: 0;
            }
        }
        @media (max-width: 600px) {
            .content-box {
                padding: 28px 10px 24px 10px;
            }
            .flower-img {
                max-width: 100%;
            }
        }
    </style>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Font Awesome CDN for Google icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="/images/logo.png" alt="Logo" class="logo">
            J & J FLOWER SHOP <span>Est. 2023</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#products">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="#reviews">Review</a></li>
            </ul>
            <div class="navbar-icons ms-3">
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
<div class="container main-section">
    <div class="row w-100 align-items-center">
        <div class="col-lg-6 text-center text-lg-start">
            <img src="/images/landingpagebm.png" alt="Flower Illustration" class="flower-img image-resize">
        </div>
        <div class="col-lg-6 d-flex justify-content-center">
            <div class="content-box w-100">
                <h1>Send Love, Send</h1>
                <h2>Blooms,</h2>
                <div class="subtitle">Beautifully arranged flowers,<br>delivered fresh — just when you need them.</div>
                <hr>
                <p>J & J Flower Shop offers handcrafted bouquets and floral gifts perfect for any occasion. We deliver love across Cebu City and nearby provinces fresh, fast, and always with care.</p>
                <a href="{{ route('customer.login') }}" class="btn btn-shop">Shop Now</a>
            </div>
        </div>
    </div>
</div>
<!-- About Us Section -->
<div id="about" class="container mt-5 scroll-offset">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="rounded-1" style="background:#8ACB88; padding: 8px 0 8px 0; text-align:center;">
                <span style="font-size:2rem; font-weight:400; color:#fff; letter-spacing:2px;">About <span style="color:#e6f5e6; font-weight:300;">Us</span></span>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mt-4">
        <div class="col-11 col-xl-10 mx-auto">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div style="background:#ADF2AB; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:22px 22px 22px 22px;">
                        <h5 style="color:#1a8c5a; font-weight:600;">Why choose us?</h5>
                        <ul style="padding-left:18px; color:#1a8c5a; font-size:1.08rem;">
                            <li>Fresh flowers daily</li>
                            <li>Same-day delivery available</li>
                            <li>Personalized gift options</li>
                            <li>Friendly customer support</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-8 mb-3">
                    <div style="background:#fff; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:22px 28px 22px 28px; color:#444; font-size:1.12rem;">
                        J & J Flower Shop was founded by partners Clair Joy Dang and Joshua Vinhard Ng-Aso in 2023.<br><br>
                        Located in Pardo, Cebu, the shop offers fresh and artificial flowers, gift and event packages, and more.<br><br>
                        We proudly serve customers all over Cebu City, adding a touch of floral magic to every special moment.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Best Seller Flower -->
    <div class="row mt-4 justify-content-center">
        <div class="col-12 text-center">
            <span style="color:#1a8c5a; font-size:1.2rem; font-weight:500;">Best Seller Flower</span>
        </div>
    </div>
    <div class="row mt-3 justify-content-center">
        <div class="col-11 col-xl-10 mx-auto">
            <div class="row justify-content-center">
                <div class="col-md-3 col-4 d-flex flex-column align-items-center mb-3">
                    <div class="product-box" style="width:190px; height:240px;"></div>
                    <div class="mt-2" style="color:#1a8c5a; font-size:1.08rem;">Name <span style="color:#888;">P1000</span></div>
                </div>
                <div class="col-md-3 col-4 d-flex flex-column align-items-center mb-3">
                    <div class="product-box" style="width:190px; height:240px;"></div>
                    <div class="mt-2" style="color:#1a8c5a; font-size:1.08rem;">Name <span style="color:#888;">P1000</span></div>
                </div>
                <div class="col-md-3 col-4 d-flex flex-column align-items-center mb-3">
                    <div class="product-box" style="width:190px; height:240px;"></div>
                    <div class="mt-2" style="color:#1a8c5a; font-size:1.08rem;">Name <span style="color:#888;">P1000</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Check Our Products Section -->
<div id="products" class="container mt-5 scroll-offset">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="rounded-1" style="background:#8ACB88; padding: 8px 0 8px 0; text-align:center;">
                <span style="font-size:2rem; font-weight:400; color:#e6f5e6; letter-spacing:2px;">Check Our <span style="color:#1a8c5a; font-weight:600;">Products</span></span>
            </div>
        </div>
    </div>
    <br>
    <div class="row mt-4 justify-content-center">
        <div class="col-11 col-xl-10 mx-auto">
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <div style="color:#1a8c5a; font-size:1.13rem; font-weight:500; margin-bottom:10px;">Boquets</div>
                    <br>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <div class="product-box" style="width:170px; height:170px;"></div>
                        <div class="product-box" style="width:170px; height:170px;"></div>
                        <div class="product-box" style="width:170px; height:170px;"></div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div style="color:#1a8c5a; font-size:1.13rem; font-weight:500; margin-bottom:10px;">Packages</div>
                    <br>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <div class="product-box" style="width:170px; height:170px; display:flex; align-items:center; justify-content:center;">Package 1</div>
                        <div class="product-box" style="width:170px; height:170px; display:flex; align-items:center; justify-content:center;">Package 2</div>
                        <div class="product-box" style="width:170px; height:170px; display:flex; align-items:center; justify-content:center;">Package 3</div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div style="color:#1a8c5a; font-size:1.13rem; font-weight:500; margin-bottom:10px;">Gifts</div>
                    <br>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <div class="product-box" style="width:170px; height:170px;"></div>
                        <div class="product-box" style="width:170px; height:170px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Shop's Review Section -->
<div id="reviews" class="container mt-5 mb-5 scroll-offset">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="rounded-1" style="background:#8ACB88; padding: 8px 0 8px 0; text-align:center;">
                <span style="font-size:2rem; font-weight:400; color:#e6f5e6; letter-spacing:2px;">Shop's <span style="color:#1a8c5a; font-weight:600;">Review</span></span>
            </div>
        </div>
    </div>
    <div class="row mt-4 justify-content-center">
        <div class="col-11 col-xl-10 mx-auto">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div style="background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:22px 28px; min-height:200px;">
                        <div style="color:#1a8c5a; font-size:1.3rem;">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span style="font-size:1.1rem; color:#444; font-weight:400; margin-left:10px;">Delightful</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div style="background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:22px 28px; min-height:200px;">
                        <div style="color:#1a8c5a; font-size:1.3rem;">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div style="color:#444; font-size:1.05rem; margin-top:8px;">Lorem</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div style="background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:22px 28px; min-height:200px;"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <div style="background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:22px 28px; min-height:200px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer -->
<footer style="background:#8ACB88; color:#fff; padding:24px 0 12px 0;">
    <div class="container">
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
                <style>
                .footer-icons > a { margin-right: 18px; }
                .footer-icons > a:last-child { margin-right: 0; }
                </style>
            </div>
        </div>
    </div>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 