<?php $__env->startSection('content'); ?>
<div class="container-fluid py-2" style="background: #f4faf4; min-height: 100vh;">

    <!-- Promoted Products Carousel -->
    <div class="mx-auto mb-3" style="max-width: 1000px;">
        <div class="bg-white rounded-3 p-3 position-relative shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <button class="btn btn-link text-success p-0" data-bs-target="#promotedCarousel" data-bs-slide="prev"><i class="bi bi-chevron-left" style="font-size: 1.5rem;"></i></button>
                <h5 class="mb-0 fw-bold text-center flex-grow-1" style="font-size: 1.1rem; color: #385E42;">Promoted Products</h5>
                <button class="btn btn-link text-success p-0" data-bs-target="#promotedCarousel" data-bs-slide="next"><i class="bi bi-chevron-right" style="font-size: 1.5rem;"></i></button>
            </div>
            <div id="promotedCarousel" class="carousel slide mt-1" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php $__currentLoopData = $promotedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="carousel-item <?php if($i === 0): ?> active <?php endif; ?> text-center">
                        <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>" style="height: 150px; object-fit: cover; border-radius: 8px;">
                        <div class="mt-1 fw-bold" style="font-size: 1rem;"><?php echo e($product->name); ?></div>
                        <div class="text-success" style="font-size: 0.95rem;">₱<?php echo e(number_format($product->price, 2)); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                </div>
                </div>
    </div>

    <!-- Category Tabs -->
    <div class="mx-auto" style="max-width: 1000px;">
        <ul class="nav nav-tabs border-0 justify-content-center category-tabs mb-2" id="productTabs" role="tablist" style="background: transparent; border-radius: 8px 8px 0 0; box-shadow: none;">
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

        <!-- Products Section -->
        <div class="mb-1 fw-bold fs-5" style="color: #385E42; padding-left: 15px;"><?php echo e($currentCategory); ?></div>
        <div class="row g-2 product-grid" style="padding-left: 15px; padding-right: 15px;">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="#" class="text-decoration-none text-dark" onclick='openProductModal({
                        id: <?php echo e($product->id); ?>,
                        name: "<?php echo e(addslashes($product->name)); ?>",
                        price: "<?php echo e($product->price); ?>",
                        image: "<?php echo e(asset('storage/' . $product->image)); ?>",
                        description: "<?php echo e(addslashes($product->description ?? '')); ?>"
                    }); return false;'>
                        <div class="card product-card h-100" style="border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s; background: transparent;">
                            <img src="<?php echo e(asset('storage/' . $product->image)); ?>" class="card-img-top product-image" alt="<?php echo e($product->name); ?>" style="height: 240px; object-fit: cover; border-radius: 8px 8px 0 0;">
                            <div class="card-body text-center" style="background: transparent; padding: 20px 15px 15px 15px;">
                                <h6 class="card-title mb-2" style="font-size: 0.8rem; font-weight: 600; color: #2c3e50; line-height: 1.2;"><?php echo e($product->name); ?></h6>
                                <p class="card-text product-price mb-0" style="color: #27ae60; font-weight: 700; font-size: 0.85rem;">₱<?php echo e(number_format($product->price, 2)); ?></p>
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
    <?php echo $__env->make('customer.products.modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    body { background: #f4faf4; }
    .category-tabs .nav-link {
        border: none !important;
        color: #7f8c8d !important;
        font-weight: 500;
        background: transparent !important;
        margin: 0 1rem;
        font-size: 1rem;
        border-radius: 0;
        padding: 10px 16px;
        position: relative;
        transition: all 0.3s ease;
    }
    .category-tabs .nav-link.active {
        color: #27ae60 !important;
        font-weight: 700;
    }
    .category-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 3px;
        background: #27ae60;
        border-radius: 2px;
    }
    .category-tabs .nav-link:hover {
        color: #27ae60 !important;
        background: #f8f9fa !important;
    }
    .category-tabs {
        border-bottom: none !important;
        padding: 0 1rem;
    }
    .product-card {
        border: none;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        background: transparent;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .product-image {
        height: 240px;
        width: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
        background-color: transparent;
        padding: 0;
        margin: 0;
        border-radius: 8px 8px 0 0;
        transition: transform 0.3s ease;
    }
    .product-card:hover .product-image {
        transform: scale(1.05);
    }
    .product-price {
        color: #27ae60;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .card-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.8rem;
        line-height: 1.2;
    }
    .card-body {
        padding: 20px 15px 15px 15px !important;
    }
    .product-grid {
        background: transparent;
        border-radius: 0 0 8px 8px;
        padding: 0;
        box-shadow: none;
        min-height: 300px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dashboard initialization
    console.log('Dashboard loaded');
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/dashboard.blade.php ENDPATH**/ ?>