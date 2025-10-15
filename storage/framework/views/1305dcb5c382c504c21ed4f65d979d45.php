<?php $__env->startSection('content'); ?>
<div class="container-fluid py-2" style="background: #f4faf4; min-height: 100vh;">

    <!-- Promoted Products Carousel -->
    <div class="mx-auto mb-3" style="max-width: 1000px;">
        <div class="bg-white rounded-3 p-2 position-relative shadow-sm">
            <div id="promotedCarousel" class="carousel slide" data-bs-ride="carousel">
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="prev" style="left: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-left" style="font-size: 1.5rem;"></i></button>
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="next" style="right: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-right" style="font-size: 1.5rem;"></i></button>
                <div class="carousel-inner">
                    <?php
                        $banners = \App\Models\PromotedBanner::active()->take(5)->get();
                    ?>
                    <?php if($banners->count()): ?>
                        <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="carousel-item <?php if($i === 0): ?> active <?php endif; ?> text-center">
                            <a href="<?php echo e($b->link_url ?? '#'); ?>" <?php if($b->link_url): ?> target="_self" <?php endif; ?>>
                                <img src="<?php echo e(asset('storage/' . $b->image)); ?>" alt="<?php echo e($b->title ?? 'Banner'); ?>" style="height: 180px; object-fit: cover; border-radius: 6px; width:100%;">
                            </a>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php $__currentLoopData = $promotedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isPromotedOutOfStock = isset($promotedProductAvailability[$product->id]) && !$promotedProductAvailability[$product->id]['can_fulfill'];
                        ?>
                        <div class="carousel-item <?php if($i === 0): ?> active <?php endif; ?> text-center" style="position: relative;">
                            <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>" style="height: 180px; object-fit: cover; border-radius: 6px; width:100%; <?php if($isPromotedOutOfStock): ?> filter: grayscale(50%); <?php endif; ?>">
                            <?php if($isPromotedOutOfStock): ?>
                                <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                    <span class="badge bg-danger" style="font-size: 0.6rem;">OUT OF STOCK</span>
                                </div>
                            <?php endif; ?>
                            <div class="mt-1 fw-bold" style="font-size: 1rem;"><?php echo e($product->name); ?></div>
                            <div class="text-success" style="font-size: 0.95rem;">₱<?php echo e(number_format($product->price, 2)); ?></div>
                            <?php if($isPromotedOutOfStock): ?>
                                <small class="text-muted" style="font-size: 0.7rem;">Insufficient materials</small>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mx-auto mb-3" style="max-width: 1000px;">
        <div class="p-0">
            <div class="row g-2 align-items-end">
                <div class="col-12">
                    <div class="input-group">
                        <input id="productSearchInput" type="text" class="form-control" placeholder="Search products..." aria-label="Search" value="<?php echo e(request('search', '')); ?>">
                        <button id="productFilterBtn" class="btn btn-outline-success" type="button" title="Filter"><i class="bi bi-funnel"></i></button>
                    </div>
                </div>
            </div>
            <!-- Advanced Filter Panel -->
            <div id="productFilterPanel" class="card p-3 mt-2" style="display:none;">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1">Min Price</label>
                        <input id="productFilterMin" type="number" min="0" class="form-control form-control-sm" placeholder="0" value="<?php echo e(request('min_price', '')); ?>">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1">Max Price</label>
                        <input id="productFilterMax" type="number" min="0" class="form-control form-control-sm" placeholder="9999" value="<?php echo e(request('max_price', '')); ?>">
                    </div>
                    <div class="col-12 col-md-6 d-flex gap-2">
                        <button id="productFilterApply" class="btn btn-success btn-sm">Apply Filters</button>
                        <button id="productFilterClear" class="btn btn-outline-secondary btn-sm">Clear</button>
                    </div>
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
        
        <!-- Horizontal Line Separator -->
        <hr class="my-2" style="border: 2px solid #1f3b2a; border-radius: 1px; margin-left: 15px; margin-right: 15px;">
        <div class="row g-2 product-grid" style="padding-left: 15px; padding-right: 15px;">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $isOutOfStock = isset($productAvailability[$product->id]) && !$productAvailability[$product->id]['can_fulfill'];
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <?php if($isOutOfStock): ?>
                        <div class="text-decoration-none text-dark" style="opacity: 0.6;">
                            <div class="card product-card h-100" style="border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: transparent; position: relative;">
                                <img src="<?php echo e(asset('storage/' . $product->image)); ?>" class="card-img-top product-image" alt="<?php echo e($product->name); ?>" style="height: 240px; object-fit: cover; border-radius: 8px 8px 0 0; filter: grayscale(50%);">
                                <!-- OUT OF STOCK Overlay -->
                                <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                    <span class="badge bg-danger" style="font-size: 0.7rem;">OUT OF STOCK</span>
                                </div>
                                <div class="card-body text-center" style="background: transparent; padding: 20px 15px 15px 15px;">
                                    <h6 class="card-title mb-2" style="font-size: 0.8rem; font-weight: 600; color: #2c3e50; line-height: 1.2;"><?php echo e($product->name); ?></h6>
                                    <p class="card-text product-price mb-0" style="color: #27ae60; font-weight: 700; font-size: 0.85rem;">₱<?php echo e(number_format($product->price, 2)); ?></p>
                                    <small class="text-muted" style="font-size: 0.7rem;">Insufficient materials</small>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="#" class="text-decoration-none text-dark" onclick='console.log("Clicking product:", <?php echo e($product->id); ?>); openProductModal({
                            id: <?php echo e($product->id); ?>,
                            name: <?php echo json_encode($product->name); ?>,
                            price: "<?php echo e($product->price); ?>",
                            image: "<?php echo e(asset('storage/' . $product->image)); ?>",
                            description: <?php echo json_encode($product->description ?? ''); ?>

                        }); return false;'>
                            <div class="card product-card h-100" style="border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s; background: transparent;">
                                <img src="<?php echo e(asset('storage/' . $product->image)); ?>" class="card-img-top product-image" alt="<?php echo e($product->name); ?>" style="height: 240px; object-fit: cover; border-radius: 8px 8px 0 0;">
                                <div class="card-body text-center" style="background: transparent; padding: 20px 15px 15px 15px;">
                                    <h6 class="card-title mb-2" style="font-size: 0.8rem; font-weight: 600; color: #2c3e50; line-height: 1.2;"><?php echo e($product->name); ?></h6>
                                    <p class="card-text product-price mb-0" style="color: #27ae60; font-weight: 700; font-size: 0.85rem;">₱<?php echo e(number_format($product->price, 2)); ?></p>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
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
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
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
    
    /* Search bar styling (no white container) */
    #productSearchInput {
        border: 2px solid #e9ecef;
        border-radius: 8px 0 0 8px;
        transition: border-color 0.3s ease;
        background: #fff;
    }
    
    #productSearchInput:focus {
        border-color: #27ae60;
        box-shadow: 0 0 0 0.2rem rgba(39, 174, 96, 0.25);
    }
    
    #productFilterBtn {
        border: 2px solid #27ae60;
        border-left: none;
        border-radius: 0 8px 8px 0;
        transition: all 0.3s ease;
    }
    
    #productFilterBtn:hover {
        background-color: #27ae60;
        color: white;
    }
    
    #productFilterPanel {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: #f8f9fa;
    }
    
    #productFilterMin, #productFilterMax {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: border-color 0.3s ease;
    }
    
    #productFilterMin:focus, #productFilterMax:focus {
        border-color: #27ae60;
        box-shadow: 0 0 0 0.2rem rgba(39, 174, 96, 0.25);
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