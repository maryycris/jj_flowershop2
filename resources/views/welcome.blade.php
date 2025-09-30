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
            margin: 0;
            padding: 0;
        }
        html {
            scroll-behavior: smooth;
            margin: 0;
            padding: 0;
            height: 100%;
        }
        
        /* Fade-in Animation Styles */
        .fade-in-section {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .fade-in-section.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .fade-in-section.fade-in-delay-1 {
            transition-delay: 0.2s;
        }
        
        .fade-in-section.fade-in-delay-2 {
            transition-delay: 0.4s;
        }
        
        .fade-in-section.fade-in-delay-3 {
            transition-delay: 0.6s;
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
        /* Hero with carousel background */
        .hero-row { position: relative; min-height: 580px; height: 80vh; overflow: hidden; }
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
        .image-resize { height: 580px; width: 1300px; }
        .hero-equal { height: 580px; }
        .content-stretch { height: 100%; display: flex; }
        .hero-carousel { position: absolute; inset: 0; z-index: 0; }
        .hero-carousel .carousel-item img { width: 100%; height: 100%; object-fit: cover; }
        .hero-content { position: relative; z-index: 1; }
        /* Section background images */
        #about, #products, #reviews {
            background-repeat: no-repeat;
            background-position: center top;
            background-size: contain;
            padding-top: 18px;
            padding-bottom: 24px;
        }
        #about { background-image: none; position: relative; overflow: hidden; }
        #about .about-bg { position: absolute; top: -120px; bottom: -120px; left: 0; right: 0; background-image: url('/images/aboutusbg.jpg'); background-size: cover; background-position: center; filter: blur(5px) opacity(0.75); transform: scale(1.06); pointer-events: none; }
        #about.scroll-offset { scroll-margin-top: 76px; }
        #products.scroll-offset { scroll-margin-top: 76px; }
        #about .about-content { position: relative; z-index: 1; }
        #products { background-image: none; position: relative; overflow: hidden; }
        #products .products-bg { position: absolute; top: -60px; left: 0; right: 0; bottom: -60px; background-image: url('/images/productsbg.jpg'); background-size: cover; background-position: center top; z-index: 0; pointer-events: none; filter: blur(2.5px) opacity(0.5); transform: scale(1.06); }
        #products .products-content { position: relative; z-index: 1; margin-top: 10px; }
        #products { padding-top: 5px; padding-bottom: 60px; }
        #reviews { background-image: none; position: relative; overflow: hidden; }
        #reviews .reviews-bg { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-image: url('/images/reviewbg.jpg'); background-size: cover; background-position: center center; background-repeat: no-repeat; z-index: 0; filter: blur(2px) opacity(0.9); }
        #reviews.scroll-offset { scroll-margin-top: 76px; }
        #reviews .reviews-content { position: relative; z-index: 2; }
        /* Only on this page: bring footer to the front */
        #reviews + footer { position: relative; z-index: 1; margin-top: 0; }
        footer { position: static; z-index: 1; margin-top: 0; }
        /* Ensure social media icons are at the very bottom */
        .footer-icons { margin-top: 20px; }
        /* Ensure footer is visible and in front */
        footer { 
            background: #8ACB88 !important; 
            position: fixed; 
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 10; 
            margin: 0 !important; 
            padding: 0 !important;
        }
        /* Remove any extra space below footer */
        body { 
            margin: 0 !important; 
            padding: 0 !important; 
            min-height: 100vh;
        }
        html { 
            margin: 0 !important; 
            padding: 0 !important; 
            height: 100%;
        }
        /* Ensure no space after footer */
        * {
            box-sizing: border-box;
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
            margin-top: 120px;
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
</nav>
<div class="container-fluid main-section px-0">
    <?php
        $carouselImages = [];
        try {
            $paths = glob(public_path('images/lpcarousel/*.{jpg,jpeg,png,gif,webp}'), GLOB_BRACE) ?: [];
            foreach ($paths as $p) {
                $rel = str_replace(public_path(), '', $p);
                $carouselImages[] = asset(ltrim($rel, '/\\'));
            }
        } catch (\Throwable $e) {
            $carouselImages = [];
        }
    ?>
    <div class="row w-100 align-items-center hero-row g-0 fade-in-section" id="home">
        <div id="lpCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <?php if(count($carouselImages) === 0): ?>
                    <div class="carousel-item active">
                        <img src="/images/landingpagebm.png" alt="Placeholder">
                    </div>
                <?php else: ?>
                    <?php foreach($carouselImages as $idx => $img): ?>
                        <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                            <img src="<?= $img ?>" alt="Slide <?= $idx+1 ?>">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php if(count($carouselImages) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#lpCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#lpCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            <?php endif; ?>
        </div>
        <div class="col-lg-6"></div>
        <div class="col-lg-6 d-flex justify-content-center hero-content">
            <div class="content-box" style="min-width: 370px; max-width: 470px; margin-left: 0; margin-top: 0;">
                <div class="d-flex flex-column justify-content-center w-100">
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
    </div>
</div>
<!-- About Us Section -->
<div id="about" class="container-fluid mt-0 scroll-offset fade-in-section">
    <div class="about-bg"></div>
    <div class="container about-content">
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
                <!-- Left: About content stacked -->
                <div class="col-lg-6 mb-3">
                    <div class="mb-3" style="background:#ADF2AB; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:22px 22px 22px 22px;">
                        <h5 style="color:#1a8c5a; font-weight:600;">Why choose us?</h5>
                        <ul style="padding-left:18px; color:#1a8c5a; font-size:1.08rem;">
                            <li>Fresh flowers daily</li>
                            <li>Same-day delivery available</li>
                            <li>Personalized gift options</li>
                            <li>Friendly customer support</li>
                        </ul>
                    </div>
                    <div style="background:transparent; border-radius:0; box-shadow:none; padding:16px 0 0 0; color:#000; font-size:0.98rem;">
                        J & J Flower Shop was founded by partners Clair Joy Dang and Joshua Vinhard Ng-Aso in 2023.<br><br>
                        Located in Pardo, Cebu, the shop offers fresh and artificial flowers, gift and event packages, and more.<br><br>
                        We proudly serve customers all over Cebu City, adding a touch of floral magic to every special moment.
                    </div>
                </div>

                <!-- Right: Best Seller Carousel -->
                <div class="col-lg-6 mb-3">
                    <?php
                        $bestSellerSlides = [];
                        try {
                            $paths = glob(public_path('images/landingpage_bestseller_caraousel_static/*.{jpg,jpeg,png,gif,webp}'), GLOB_BRACE) ?: [];
                            foreach ($paths as $p) {
                                $rel = str_replace(public_path(), '', $p);
                                $src = asset(ltrim($rel, '/\\'));
                                $filename = pathinfo($p, PATHINFO_FILENAME);
                                $readable = ucwords(trim(preg_replace('/[-_]+/', ' ', $filename)));
                                $price = null;
                                if (preg_match('/(\d{2,6})/', $filename, $m)) { $price = (int)$m[1]; }
                                $bestSellerSlides[] = [ 'src' => $src, 'name' => $readable, 'price' => $price ];
                            }
                        } catch (\Throwable $e) {
                            $bestSellerSlides = [];
                        }
                    ?>
                    <div class="text-center mb-2">
                        <span style="color:#1a8c5a; font-size:1.2rem; font-weight:500;">Best Seller Flowers</span>
                    </div>
                    <div id="bestSellerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500" style="max-width: 420px; margin: 0 auto;">
                        <div class="carousel-inner" style="border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                            <?php if(count($bestSellerSlides) === 0): ?>
                                <div class="carousel-item active">
                                    <div class="product-box" style="width:100%; height:340px; display:flex; align-items:center; justify-content:center; color:#1a8c5a;">No images found</div>
                                    <div class="text-center mt-2" style="color:#1a8c5a; font-size:1.05rem;">P1000</div>
                                </div>
                            <?php else: ?>
                                <?php foreach($bestSellerSlides as $idx => $slide): ?>
                                    <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                        <img src="<?= $slide['src'] ?>" alt="<?= $slide['name'] ?>" style="width:100%; height:340px; object-fit:cover;">
                                        <div class="text-center mt-2" style="color:#1a8c5a; font-size:1.05rem;">
                                            <?= $slide['name'] ?>
                                            <span style="color:#888;">
                                                <?= $slide['price'] ? 'P' . number_format($slide['price']) : 'P1000' ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php if(count($bestSellerSlides) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#bestSellerCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#bestSellerCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<!-- Check Our Products Section -->
<div id="products" class="container-fluid mt-1 scroll-offset fade-in-section">
    <div class="products-bg"></div>
    <div class="container products-content">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="rounded-1" style="background:#8ACB88; padding: 8px 0 8px 0; text-align:center;">
                <span style="font-size:2rem; font-weight:400; color:#e6f5e6; letter-spacing:2px;">Check Our <span style="color:#1a8c5a; font-weight:600;">Products</span></span>
            </div>
        </div>
    </div>
    <br>
    <?php
        function jj_pick_images(string $dir, int $limit = 6): array {
            $out = [];
            try {
                $paths = glob(public_path($dir.'/*.{jpg,jpeg,png,webp,gif}'), GLOB_BRACE) ?: [];
                foreach (array_slice($paths, 0, $limit) as $p) {
                    $rel = str_replace(public_path(), '', $p);
                    $out[] = asset(ltrim($rel, '/\\'));
                }
            } catch (\Throwable $e) {}
            return $out;
        }
        $catBouquets = jj_pick_images('images/landingpage_bouquet', 6);
        $catPackages = jj_pick_images('images/landingpage_package', 6);
        $catGifts    = jj_pick_images('images/landingpage_gift', 6);
    ?>
    <style>
        .prod-card{width:140px; border-radius:10px; overflow:hidden; background:#fff; box-shadow:0 6px 18px rgba(0,0,0,0.08); transition:transform .2s, box-shadow .2s}
        .prod-card:hover{transform:translateY(-4px); box-shadow:0 12px 28px rgba(0,0,0,0.12)}
        .prod-card img{width:100%; height:120px; object-fit:cover}
        .prod-card .cap{padding:6px 8px; color:#1a8c5a; font-size:.85rem; text-align:center}
        @media (max-width: 992px){ .prod-card{width:128px} .prod-card img{height:110px} }
    </style>
    <div class="row mt-4 justify-content-center">
        <div class="col-11 col-xl-10 mx-auto">
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <div style="color:#1a8c5a; font-size:1.13rem; font-weight:500; margin-bottom:10px;">Bouquets</div>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <?php foreach(array_slice($catBouquets,0,3) as $img): ?>
                            <div class="prod-card"><img src="<?= $img ?>" alt="Bouquet"><div class="cap">Bouquet</div></div>
                        <?php endforeach; ?>
                    </div>
                    <a href="{{ route('customer.login') }}" class="btn btn-sm btn-outline-success mt-2">View All</a>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div style="color:#1a8c5a; font-size:1.13rem; font-weight:500; margin-bottom:10px;">Packages</div>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <?php foreach(array_slice($catPackages,0,3) as $img): ?>
                            <div class="prod-card"><img src="<?= $img ?>" alt="Package"><div class="cap">Package</div></div>
                        <?php endforeach; ?>
                    </div>
                    <a href="{{ route('customer.login') }}" class="btn btn-sm btn-outline-success mt-2">View All</a>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div style="color:#1a8c5a; font-size:1.13rem; font-weight:500; margin-bottom:10px;">Gifts</div>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <?php foreach(array_slice($catGifts,0,3) as $img): ?>
                            <div class="prod-card"><img src="<?= $img ?>" alt="Gift"><div class="cap">Gift</div></div>
                        <?php endforeach; ?>
                    </div>
                    <a href="{{ route('customer.login') }}" class="btn btn-sm btn-outline-success mt-2">View All</a>
                </div>
            </div>
        </div>
    </div>
</div></div>
<!-- Shop's Review Section -->
<div id="reviews" class="container-fluid mt-0 mb-5 scroll-offset fade-in-section" style="min-height: 95vh;">
    <div class="reviews-bg"></div>
    <div class="container reviews-content">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="rounded-1" style="background:#8ACB88; padding: 8px 0 8px 0; text-align:center;">
                <span style="font-size:2rem; font-weight:400; color:#e6f5e6; letter-spacing:2px;">Shop's <span style="color:#1a8c5a; font-weight:600;">Review</span></span>
            </div>
        </div>
    </div>
    <style>
        .review-card{background:#fff; border-radius:12px; box-shadow:0 10px 28px rgba(0,0,0,0.10); padding:22px 25px; min-height:240px; width:320px; transition: transform 7.25s ease, filter 1.25s ease, opacity 2.25s ease}
        .review-set{display:flex; justify-content:center; gap:28px; align-items:center; min-height: 60vh; padding: 40px 0;}
        .review-card.role-center{filter:none; opacity:1; order:2; transform: translateY(20px)}
        .review-card.role-left{filter: blur(2px); opacity:0.65; order:1;}
        .review-card.role-right{filter: blur(2px); opacity:0.65; order:3;}
        .review-name{font-weight:600; color:#333; margin-top:12px; font-size:1.0rem}
        .review-stars{color:#1a8c5a; font-size:1.3rem; margin-bottom:12px}
        .review-text{color:#444; font-size:1.0rem; line-height:1.4}
        #reviewsCarousel{position:relative}
        #reviewsCarousel .carousel-control-prev, #reviewsCarousel .carousel-control-next{width:auto; top:50%; transform:translateY(-50%); bottom:auto; opacity:1}
        #reviewsCarousel .carousel-control-prev{left:18%; right:auto}
        #reviewsCarousel .carousel-control-next{right:18%; left:auto}
        #reviewsCarousel .ctrl{background:#fff; border:1px solid #dfe8df; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#1a8c5a; box-shadow:0 4px 12px rgba(0,0,0,0.08)}
    </style>
    <div class="row mt-4 justify-content-center" style="min-height: 60vh; display: flex; align-items: center;">
        <div class="col-12 d-flex justify-content-center">
            <div id="reviewsCarousel" class="carousel slide" data-bs-ride="false" style="max-width:1200px; position:relative;">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="review-set">
                            <div class="review-card role-left">
                                <div class="review-stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i><i class="bi bi-star"></i></div>
                                <div class="review-text">Nice bouquet and reasonable price. The bouquet was fresh and fragrant.</div>
                                <div class="review-name">KAYE L.</div>
                            </div>
                            <div class="review-card role-center">
                                <div class="review-stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i></div>
                                <div class="review-text">This site offers beautiful flowers. I love this shop. I will surely buy more in future.</div>
                                <div class="review-name">CATHERINE LOPEZ</div>
                            </div>
                            <div class="review-card role-right">
                                <div class="review-stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                                <div class="review-text">Amazing flowers! I told my friends about it. Thank you for the fast service.</div>
                                <div class="review-name">JOHANNA ESCOBAR</div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" onclick="jjCycleCenter(-1, event)">
                    <span class="ctrl" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" onclick="jjCycleCenter(1, event)">
                    <span class="ctrl" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>
    </div>
<script>
  // Keep role-based slide effect
  function jjApplyCenterIndex(){
    var c = document.getElementById('reviewsCarousel');
    if(!c) return;
    var idx = parseInt(c.dataset.centerIdx || '1', 10);
    var wrap = c.querySelector('.review-set');
    if(!wrap) return; // critical as requested
    var cards = wrap.querySelectorAll('.review-card');
    if(cards.length !== 3) return;
    cards.forEach(function(el){ el.classList.remove('role-left','role-center','role-right'); });
    var left = (idx + 3 - 1) % 3, right = (idx + 1) % 3;
    cards[left].classList.add('role-left');
    cards[idx].classList.add('role-center');
    cards[right].classList.add('role-right');
    c.dataset.centerIdx = String(idx);
  }
  function jjCycleCenter(dir, ev){
    if(ev) ev.preventDefault();
    var c = document.getElementById('reviewsCarousel');
    if(!c) return;
    var idx = parseInt(c.dataset.centerIdx || '1', 10);
    idx = (idx + dir + 3) % 3;
    c.dataset.centerIdx = String(idx);
    jjApplyCenterIndex();
  }
  document.addEventListener('DOMContentLoaded', function(){
    var c = document.getElementById('reviewsCarousel');
    if(c && !c.dataset.centerIdx) c.dataset.centerIdx = '1';
    jjApplyCenterIndex();
  });
</script>
<!-- Footer -->
<footer style="background:#8ACB88; color:#fff; margin:0; padding:0;">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <div class="footer-icons" style="font-size:1.3rem; padding: 0px 0 12px 0;">
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

<!-- Fade-in Animation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to check if element is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Function to check if element is partially visible
    function isPartiallyInViewport(element) {
        const rect = element.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        const windowWidth = window.innerWidth || document.documentElement.clientWidth;
        
        return (
            rect.top < windowHeight &&
            rect.bottom > 0 &&
            rect.left < windowWidth &&
            rect.right > 0
        );
    }
    
    // Function to handle fade-in animations
    function handleFadeIn() {
        const fadeElements = document.querySelectorAll('.fade-in-section');
        
        fadeElements.forEach((element, index) => {
            if (isPartiallyInViewport(element) && !element.classList.contains('visible')) {
                // Add delay classes for staggered animation
                if (index > 0) {
                    element.classList.add(`fade-in-delay-${(index % 3) + 1}`);
                }
                element.classList.add('visible');
            }
        });
    }
    
    // Initial check for elements already in viewport
    handleFadeIn();
    
    // Add scroll event listener
    window.addEventListener('scroll', handleFadeIn);
    
    // Add resize event listener to handle window resize
    window.addEventListener('resize', handleFadeIn);
});
</script>
</body>
</html> 