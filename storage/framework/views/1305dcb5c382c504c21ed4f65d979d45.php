

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="background: #f4faf4; min-height: 100vh;">
    <!-- Promoted Products Carousel -->
    <div class="mx-auto mb-4" style="max-width: 900px;">
        <div class="bg-white rounded-4 p-3 position-relative" style="box-shadow: none;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <button class="btn btn-link text-success p-0" data-bs-target="#promotedCarousel" data-bs-slide="prev"><i class="bi bi-chevron-left" style="font-size: 2rem;"></i></button>
                <h5 class="mb-0 fw-bold text-center flex-grow-1" style="font-size: 1.2rem; color: #385E42;">Promoted Products</h5>
                <button class="btn btn-link text-success p-0" data-bs-target="#promotedCarousel" data-bs-slide="next"><i class="bi bi-chevron-right" style="font-size: 2rem;"></i></button>
            </div>
            <div id="promotedCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php $__currentLoopData = $promotedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="carousel-item <?php if($i === 0): ?> active <?php endif; ?> text-center">
                        <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>" style="height: 180px; object-fit: cover; border-radius: 12px;">
                        <div class="mt-2 fw-bold" style="font-size: 1.08rem;"><?php echo e($product->name); ?></div>
                        <div class="text-success" style="font-size: 1.05rem;">₱<?php echo e(number_format($product->price, 2)); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="mx-auto mb-3" style="max-width: 900px;">
        <ul class="nav nav-tabs border-0 justify-content-center category-tabs" id="productTabs" role="tablist" style="background: #fff; border-radius: 12px 12px 0 0; box-shadow: none;">
            <?php
                $categories = ['all' => 'All', 'bouquets' => 'Bouquets', 'packages' => 'Packages', 'gifts' => 'Gifts'];
                $currentCategory = $categories[request('category', 'all')] ?? 'All';
            ?>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link category-tab-link <?php if(request('category', 'all') === $key): ?> active <?php endif; ?>" href="?category=<?php echo e($key); ?>"><?php echo e($label); ?></a>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <!-- Product Filtering Section -->
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <h4 class="mb-3 text-center" style="color: #385E42; font-weight: 600;">Filter Products</h4>
                    
                    <div class="row">
                        <!-- Price Range Filter -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Price Range</label>
                            <select class="form-select" id="priceFilter">
                                <option value="">All Prices</option>
                                <option value="0-500">Under ₱500</option>
                                <option value="500-1000">₱500 - ₱1,000</option>
                                <option value="1000-2000">₱1,000 - ₱2,000</option>
                                <option value="2000+">Above ₱2,000</option>
                            </select>
                        </div>
                        
                        <!-- Popularity Filter -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Sort By</label>
                            <select class="form-select" id="popularityFilter">
                                <option value="">Default</option>
                                <option value="popular">Most Popular</option>
                                <option value="newest">Newest</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                            </select>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                <option value="bouquets">Bouquets</option>
                                <option value="packages">Packages</option>
                                <option value="gifts">Gifts</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button class="btn btn-success" id="applyFilters">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        <button class="btn btn-outline-secondary ms-2" id="clearFilters">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="mx-auto" style="max-width: 900px;">
        <div class="bg-white rounded-4 p-4" style="box-shadow: none;">
            <div class="mb-3 fw-bold fs-5" style="color: #385E42;"><?php echo e($currentCategory); ?></div>
            <div class="row g-3 product-grid">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="#" class="text-decoration-none text-dark" onclick='openProductModal({
                        id: <?php echo e($product->id); ?>,
                        name: "<?php echo e(addslashes($product->name)); ?>",
                        price: "<?php echo e($product->price); ?>",
                        image: "<?php echo e(asset('storage/' . $product->image)); ?>",
                        description: "<?php echo e(addslashes($product->description ?? '')); ?>"
                    }); return false;'>
                        <div class="card product-card h-100" style="border: 1px solid #e0e0e0; border-radius: 10px; box-shadow: none; transition: transform 0.2s;">
                            <img src="<?php echo e(asset('storage/' . $product->image)); ?>" class="card-img-top product-image" alt="<?php echo e($product->name); ?>" style="height: 150px; object-fit: cover; border-radius: 8px 8px 0 0;">
                            <div class="card-body text-center p-2">
                                <h6 class="card-title mb-1" style="font-size: 1.05rem; font-weight: 500; color: #222;"><?php echo e($product->name); ?></h6>
                                <p class="card-text product-price" style="color: #7bb47b; font-weight: 600; font-size: 1.02rem;">₱<?php echo e(number_format($product->price, 2)); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <p class="text-center">No products found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php echo $__env->make('customer.products.modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    body { background: #f4faf4; }
    .category-tabs .nav-link {
        border: none !important;
        color: #385E42 !important;
        font-weight: 500;
        background: #fff !important;
        margin: 0 1.5rem;
        font-size: 1.12rem;
        border-radius: 0;
        padding: 10px 0 14px 0;
        position: relative;
        transition: color 0.2s;
    }
    .category-tabs .nav-link.active {
        color: #7bb47b !important;
        font-weight: 600;
    }
    .category-tabs .nav-link.active::after {
        content: '';
        display: block;
        margin: 0 auto;
        width: 60%;
        height: 4px;
        border-radius: 2px;
        background: #7bb47b;
        margin-top: 6px;
    }
    .category-tabs {
        border-bottom: none !important;
    }
    .product-card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: none;
    }
    .product-card:hover {
        transform: translateY(-5px) scale(1.03);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }
    .product-image {
        height: 150px;
        object-fit: cover;
    }
    .product-price {
        color: #7bb47b;
        font-weight: 600;
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/dashboard.blade.php ENDPATH**/ ?>