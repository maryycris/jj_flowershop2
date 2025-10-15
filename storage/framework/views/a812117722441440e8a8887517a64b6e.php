<?php $__env->startSection('content'); ?>
<style>
    .sidebar {
        background: #f4f9f4;
        border-radius: 10px;
        padding: 30px 20px;
        min-height: 500px;
    }
    .sidebar .profile-img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        background: #e0e0e0;
        display: block;
        margin: 0 auto 10px auto;
    }
    .sidebar .active-link {
        background: #cfe3d8;
        border-radius: 4px;
        font-weight: bold;
    }
    .order-tabs .nav-link {
        color: #222 !important;
        background: #f4f9f4;
    }
    .order-tabs .nav-link.active {
        border-bottom: 3px solid #7bb47b;
        color: #222 !important;
        font-weight: bold;
        background: #f4f9f4;
    }
    .order-list-row {
        border-bottom: 1px solid #e0e0e0;
        padding: 18px 0;
        align-items: center;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    .order-list-row:last-child {
        border-bottom: none;
    }
    .order-list-row:hover {
        background-color: #f8f9fa !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        border-radius: 8px;
        border: 1px solid #e3f2fd;
    }
    .order-list-row.clickable {
        position: relative;
    }
    .order-list-row.clickable::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: transparent;
        z-index: 1;
    }
    .order-product-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        background: #e0e0e0;
    }
    .order-status-btn {
        min-width: auto;
        white-space: nowrap;
        font-size: 0.8rem;
        padding: 4px 8px;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .order-search-bar {
        background: #f4f9f4;
        border-radius: 6px;
        padding: 8px 12px;
        margin-bottom: 10px;
        border: 1px solid #e0e0e0;
    }
    .review-dropdown {
        position: relative;
        display: inline-block;
    }
    .review-dropdown-toggle {
        background: #f4f9f4;
        border: none;
        color: #222;
        font-weight: bold;
        padding: 6px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 4px;
    }
    .review-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background: #fff;
        min-width: 120px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        z-index: 10;
        border-radius: 4px;
        border: 1px solid #e0e0e0;
    }
    .review-dropdown-menu.show {
        display: block;
    }
    .review-dropdown-menu button {
        width: 100%;
        padding: 6px 12px;
        border: none;
        background: none;
        text-align: left;
        font-size: 12px;
        color: #333;
        cursor: pointer;
    }
    .review-dropdown-menu button:hover {
        background: #f8f9fa;
    }
    .review-dropdown-menu button.active {
        background: #e3f2fd;
        color: #1976d2;
    }
    .star-rating {
        direction: rtl;
        display: inline-flex;
        font-size: 1.2rem;
    }
    .star-rating input[type="radio"] {
        display: none;
    }
    .star-rating label {
        color: #bbb;
        cursor: pointer;
        margin: 0 1px;
    }
    .star-rating input[type="radio"]:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #f5b301;
    }
    .star-rating.readonly label {
        cursor: default;
    }
    .star-rating.readonly label {
        color: #f5b301;
    }
    .star-rating.readonly label.filled {
        color: #f5b301;
    }
    .star-rating.readonly label:not(.filled) {
        color: #ddd;
    }
    
    /* Modal Star Rating Styles */
    .star-rating-modal {
        direction: rtl;
        display: inline-flex;
        font-size: 2rem;
        gap: 5px;
    }
    .star-rating-modal input[type="radio"] {
        display: none;
    }
    .star-label {
        color: #ddd;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 2.5rem;
    }
    .star-label:hover,
    .star-label:hover ~ .star-label,
    .star-rating-modal input[type="radio"]:checked ~ .star-label {
        color: #ffc107;
        transform: scale(1.1);
    }
    
    /* Review item hover effects */
    .review-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .review-comment-box {
        width: 100%;
        min-height: 50px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 6px 10px;
        margin-top: 6px;
        font-size: 0.95rem;
        background: #fafafa;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-3">
            <?php echo $__env->make('customer.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-8 main-content-with-sidebar" style="margin-left: 25%; max-width: calc(75% - 30px);">
            <div class="py-4 px-3">
                <div class="mb-3 d-flex align-items-center">
                    <ul class="nav nav-tabs order-tabs" id="orderTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo e(request('status', 'all') === 'all' ? 'active' : ''); ?>" id="tab-all" data-status="all" type="button" role="tab">All</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo e(request('status') === 'to_pay' ? 'active' : ''); ?>" id="tab-to-pay" data-status="to_pay" type="button" role="tab">To Pay</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo e(request('status') === 'to_ship' ? 'active' : ''); ?>" id="tab-to-ship" data-status="to_ship" type="button" role="tab">To Ship</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo e(request('status') === 'to_receive' ? 'active' : ''); ?>" id="tab-to-receive" data-status="to_receive" type="button" role="tab">To Receive</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo e(request('status') === 'receive' ? 'active' : ''); ?>" id="tab-receive" data-status="receive" type="button" role="tab">RECEIVED</button>
                        </li>
                        <li class="nav-item position-relative" role="presentation">
                            <div class="d-inline-block" id="toReviewTabWrapper">
                                <button class="nav-link <?php echo e(request('status') === 'to_review' ? 'active' : ''); ?>" id="tab-to-review" data-status="to_review" type="button" role="tab">
                                    <span id="toReviewTabLabel">To Review</span>
                                </button>
                                <div class="review-dropdown-menu" id="reviewDropdownMenu" style="display:none; position:absolute; top:100%; left:0; min-width:180px;">
                                    <button type="button" class="dropdown-item active" data-review-type="to_be_review">To be Review Product</button>
                                    <button type="button" class="dropdown-item" data-review-type="reviewed">Reviewed Products</button>
                                    <button type="button" class="dropdown-item" data-review-type="shop_review">Review the Shop</button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <form method="GET" id="orderFilterForm">
                    <div class="order-search-bar mb-2">
                        <input type="text" id="orderSearchInput" name="search" class="form-control border-0 bg-transparent" 
                               placeholder="Search orders by product name..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <input type="hidden" name="status" id="statusFilter" value="<?php echo e(request('status', 'all')); ?>">
                </form>
                <div id="reviewSectionHeader" class="d-flex align-items-center mb-0" style="display:none;">
                    <div id="reviewSectionTitle" style="font-size:1.3rem;color:#7bb47b; margin-right: 12px;">To be Review Product</div>
                </div>
                <?php if($orders->isEmpty()): ?>
                    <div class="alert alert-info" role="alert">
                        <?php if(request('status') && request('status') !== 'all'): ?>
                            <?php switch(request('status')):
                                case ('to_pay'): ?>
                                    No orders pending payment.
                                    <?php break; ?>
                                <?php case ('to_ship'): ?>
                                    No orders ready to ship.
                                    <?php break; ?>
                                <?php case ('to_receive'): ?>
                                    No orders currently on delivery.
                                    <?php break; ?>
                                <?php case ('receive'): ?>
                                    No orders received yet.
                                    <?php break; ?>
                                <?php case ('to_review'): ?>
                                    No orders ready for review.
                                    <?php break; ?>
                                <?php default: ?>
                                    No orders found for this status.
                            <?php endswitch; ?>
                        <?php else: ?>
                            You haven't placed any orders yet.
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded shadow-sm p-3">
                        <div class="row fw-bold text-muted mb-2" style="border-bottom:1px solid #e0e0e0;">
                            <div class="col-md-1"></div>
                            <div class="col-md-3">Product Info</div>
                            <div class="col-md-2">Price</div>
                            <div class="col-md-2">Quantity</div>
                            <div class="col-md-2">Date Received</div>
                            <div class="col-md-2 text-end">Status</div>
                        </div>
                        <div id="orderList">
                            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row order-list-row order-row clickable" 
                                        data-status="<?php echo e(\App\Services\OrderStatusService::getCustomerDisplayStatus($order->order_status ?? $order->status)); ?>"
                                        data-product="<?php echo e(strtolower($product->name)); ?>"
                                        data-review="<?php echo e(isset($product->pivot->reviewed) && $product->pivot->reviewed ? 'reviewed' : 'to_be_review'); ?>"
                                        style="cursor: pointer; transition: all 0.3s ease;"
                                        onclick="window.location.href='<?php echo e(route('customer.orders.show', $order->id)); ?>'">
                                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                                            <?php if($product->image_url): ?>
                                                <img src="<?php echo e($product->image_url); ?>" class="order-product-img" alt="<?php echo e($product->name); ?>">
                                            <?php elseif($product->image): ?>
                                                <img src="<?php echo e(asset('storage/' . $product->image)); ?>" class="order-product-img" alt="<?php echo e($product->name); ?>">
                                            <?php else: ?>
                                                <div class="order-product-img d-flex align-items-center justify-content-center bg-light text-muted">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-3 d-flex flex-column justify-content-center">
                                            <div class="fw-bold"><?php echo e($product->name); ?></div>
                                            <div class="text-muted small">Order #<?php echo e($order->id); ?></div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">₱<?php echo e(number_format($product->price, 2)); ?></div>
                                        <div class="col-md-2 d-flex align-items-center">x<?php echo e($product->pivot->quantity); ?></div>
                                        <div class="col-md-2 d-flex align-items-center"><?php echo e($order->created_at->format('M d, Y')); ?></div>
                                        <div class="col-md-2 d-flex align-items-center justify-content-end">
                                            <?php
                                                $orderStatus = $order->order_status ?? $order->status;
                                                $statusLabel = \App\Services\OrderStatusService::getStatusLabel($orderStatus);
                                            ?>
                                            
                                            <div class="d-flex flex-column align-items-end">
                                                <?php if($orderStatus === 'pending'): ?>
                                                    <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;"><?php echo e($statusLabel); ?></span>
                                                <?php elseif($orderStatus === 'approved'): ?>
                                                    <span class="btn btn-sm btn-info order-status-btn" style="font-weight:bold;"><?php echo e($statusLabel); ?></span>
                                                <?php elseif($orderStatus === 'on_delivery'): ?>
                                                    <span class="btn btn-sm btn-primary order-status-btn" style="font-weight:bold;"><?php echo e($statusLabel); ?></span>
                                                <?php elseif($orderStatus === 'delivered'): ?>
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;"><?php echo e($statusLabel); ?></span>
                                                <?php elseif($orderStatus === 'completed'): ?>
                                                    <span class="btn btn-sm btn-success order-status-btn" style="font-weight:bold;"><?php echo e($statusLabel); ?></span>
                                                <?php elseif($orderStatus === 'cancelled'): ?>
                                                    <span class="btn btn-sm btn-danger order-status-btn" style="font-weight:bold;"><?php echo e($statusLabel); ?></span>
                                                <?php else: ?>
                                                    <span class="btn btn-sm btn-secondary order-status-btn" style="font-weight:bold;"><?php echo e($statusLabel); ?></span>
                                                <?php endif; ?>
                                                <small class="text-muted mt-1" style="font-size: 0.7rem;">
                                                    <i class="fas fa-eye me-1"></i>Click to view details
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <!-- Review Section (hidden by default, shown for To Review tab) -->
                    <div id="reviewList" style="display:none;">
                        <div class="bg-white rounded shadow-sm p-3">
                            <div class="row fw-bold text-muted mb-2 mt-2" style="border-bottom:1px solid #e0e0e0;">
                                <div class="col-md-6">Products</div>
                                <div class="col-md-6">Review</div>
                            </div>
                            
                            <!-- To be Review Section -->
                            <div id="toBeReviewSection">
                                <?php
                                // Get completed orders that haven't been reviewed yet
                                $completedOrders = $orders->filter(function($order) {
                                    $orderStatus = $order->order_status ?? $order->status;
                                    return $orderStatus === 'completed';
                                });
                                $toBeReviewedProducts = [];
                                foreach($completedOrders as $order) {
                                    foreach($order->products as $product) {
                                        if(!isset($product->pivot->reviewed) || !$product->pivot->reviewed) {
                                            $toBeReviewedProducts[] = [
                                                'product' => $product,
                                                'order' => $order,
                                                'pivot' => $product->pivot
                                            ];
                                        }
                                    }
                                }
                                ?>
                                
                                <?php if(count($toBeReviewedProducts) > 0): ?>
                                    <?php $__currentLoopData = $toBeReviewedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row order-list-row align-items-center mb-3 review-item" 
                                         data-order-id="<?php echo e($item['order']->id); ?>" 
                                         data-product-id="<?php echo e($item['product']->id); ?>"
                                         style="cursor: pointer; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; transition: all 0.3s ease;"
                                         onmouseover="this.style.backgroundColor='#f8f9fa'" 
                                         onmouseout="this.style.backgroundColor='white'"
                                         onclick="openReviewModal(<?php echo e($item['order']->id); ?>, <?php echo e($item['product']->id); ?>, <?php echo e(json_encode($item['product']->name)); ?>, <?php echo e(json_encode($item['product']->image_url ?? asset('storage/' . $item['product']->image))); ?>)">
                                        <div class="col-md-6 d-flex align-items-center">
                                            <?php if($item['product']->image_url): ?>
                                                <img src="<?php echo e($item['product']->image_url); ?>" class="order-product-img me-3" alt="<?php echo e($item['product']->name); ?>">
                                            <?php elseif($item['product']->image): ?>
                                                <img src="<?php echo e(asset('storage/' . $item['product']->image)); ?>" class="order-product-img me-3" alt="<?php echo e($item['product']->name); ?>">
                                            <?php else: ?>
                                                <div class="order-product-img me-3 d-flex align-items-center justify-content-center bg-light text-muted">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold"><?php echo e($item['product']->name); ?></div>
                                                <div class="text-muted small">Order #<?php echo e($item['order']->id); ?></div>
                                                <div class="text-primary small mt-1">
                                                    <i class="fas fa-star"></i> Click to rate this product
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <button class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-star me-1"></i> Rate Product
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-star" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No products to review yet</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Reviewed Section -->
                            <div id="reviewedSection" style="display:none;">
                                <?php
                                // Get completed orders that have been reviewed
                                $reviewedProducts = [];
                                foreach($completedOrders as $order) {
                                    foreach($order->products as $product) {
                                        if(isset($product->pivot->reviewed) && $product->pivot->reviewed) {
                                            $reviewedProducts[] = [
                                                'product' => $product,
                                                'order' => $order,
                                                'pivot' => $product->pivot
                                            ];
                                        }
                                    }
                                }
                                ?>
                                
                                <?php if(count($reviewedProducts) > 0): ?>
                                    <?php $__currentLoopData = $reviewedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row order-list-row align-items-center mb-3">
                                        <div class="col-md-6 d-flex align-items-center">
                                            <?php if($item['product']->image_url): ?>
                                                <img src="<?php echo e($item['product']->image_url); ?>" class="order-product-img me-3" alt="<?php echo e($item['product']->name); ?>">
                                            <?php elseif($item['product']->image): ?>
                                                <img src="<?php echo e(asset('storage/' . $item['product']->image)); ?>" class="order-product-img me-3" alt="<?php echo e($item['product']->name); ?>">
                                            <?php else: ?>
                                                <div class="order-product-img me-3 d-flex align-items-center justify-content-center bg-light text-muted">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold"><?php echo e($item['product']->name); ?></div>
                                                <div class="text-muted small">Order #<?php echo e($item['order']->id); ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="star-rating readonly mb-2">
                                                <?php for($i=5;$i>=1;$i--): ?>
                                                    <label class="<?php echo e($i <= ($item['pivot']->rating ?? 0) ? 'filled' : ''); ?>">★</label>
                                                <?php endfor; ?>
                                            </div>
                                            <div class="review-comment-display p-2 bg-light rounded">
                                                <?php echo e($item['pivot']->review_comment ?? 'No comment provided'); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No reviewed products yet</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Shop Review Section -->
                            <div id="shopReviewSection" style="display:none;">
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-store text-success" style="font-size: 3rem;"></i>
                                    </div>
                                    <h4 class="text-success mb-3">Review Our Shop</h4>
                                    <p class="text-muted mb-4">Share your overall experience with J'J Flower Shop</p>
                                    
                                    <!-- Shop Review Form -->
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Overall Rating</label>
                                                        <div class="star-rating" id="shopRating">
                                                            <label for="star5">★</label>
                                                            <input type="radio" id="star5" name="shop_rating" value="5">
                                                            <label for="star4">★</label>
                                                            <input type="radio" id="star4" name="shop_rating" value="4">
                                                            <label for="star3">★</label>
                                                            <input type="radio" id="star3" name="shop_rating" value="3">
                                                            <label for="star2">★</label>
                                                            <input type="radio" id="star2" name="shop_rating" value="2">
                                                            <label for="star1">★</label>
                                                            <input type="radio" id="star1" name="shop_rating" value="1">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="shopReviewComment" class="form-label fw-bold">Your Review</label>
                                                        <textarea class="form-control" id="shopReviewComment" rows="4" 
                                                                placeholder="Tell us about your experience with our shop..."></textarea>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">What did you like most?</label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="quality" value="quality">
                                                                    <label class="form-check-label" for="quality">Product Quality</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="delivery" value="delivery">
                                                                    <label class="form-check-label" for="delivery">Fast Delivery</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="packaging" value="packaging">
                                                                    <label class="form-check-label" for="packaging">Beautiful Packaging</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="service" value="service">
                                                                    <label class="form-check-label" for="service">Customer Service</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="pricing" value="pricing">
                                                                    <label class="form-check-label" for="pricing">Fair Pricing</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="variety" value="variety">
                                                                    <label class="form-check-label" for="variety">Product Variety</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="text-center">
                                                        <button type="button" class="btn btn-success btn-lg px-5" id="submitShopReview">
                                                            <i class="fas fa-paper-plane me-2"></i>Submit Shop Review
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($orders->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?>
</div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">
                    <i class="fas fa-star text-warning me-2"></i>Rate Your Product
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div id="modalProductImage" class="text-center">
                            <!-- Product image will be loaded here -->
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 id="modalProductName" class="mb-3"></h6>
                        <p class="text-muted small mb-4">Order #<span id="modalOrderId"></span></p>
                        
                        <form id="reviewModalForm">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" id="modalOrderIdInput" name="order_id">
                            <input type="hidden" id="modalProductIdInput" name="product_id">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Rate this product:</label>
                                <div class="star-rating-modal">
                                    <input type="radio" id="modal-star5" name="rating" value="5">
                                    <label for="modal-star5" class="star-label">★</label>
                                    <input type="radio" id="modal-star4" name="rating" value="4">
                                    <label for="modal-star4" class="star-label">★</label>
                                    <input type="radio" id="modal-star3" name="rating" value="3">
                                    <label for="modal-star3" class="star-label">★</label>
                                    <input type="radio" id="modal-star2" name="rating" value="2">
                                    <label for="modal-star2" class="star-label">★</label>
                                    <input type="radio" id="modal-star1" name="rating" value="1">
                                    <label for="modal-star1" class="star-label">★</label>
                                </div>
                                <div id="ratingText" class="mt-2 text-muted"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="modalComment" class="form-label fw-bold">Your Review:</label>
                                <textarea class="form-control" id="modalComment" name="comment" rows="4" 
                                          placeholder="Share your experience with this product and our service..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitReviewBtn" onclick="submitReview()">
                    <i class="fas fa-paper-plane me-1"></i> Submit Review
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    // Review Modal Functions
    function openReviewModal(orderId, productId, productName, productImage) {
        // Set modal content
        document.getElementById('modalOrderId').textContent = orderId;
        document.getElementById('modalOrderIdInput').value = orderId;
        document.getElementById('modalProductIdInput').value = productId;
        document.getElementById('modalProductName').textContent = productName;
        
        // Set product image
        const imageContainer = document.getElementById('modalProductImage');
        if (productImage && productImage !== '') {
            imageContainer.innerHTML = `<img src="${productImage}" class="img-fluid rounded" style="max-height: 200px;" alt="${productName}">`;
        } else {
            imageContainer.innerHTML = `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;"><i class="fas fa-image text-muted" style="font-size: 3rem;"></i></div>`;
        }
        
        // Reset form
        document.getElementById('reviewModalForm').reset();
        document.getElementById('ratingText').textContent = '';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        modal.show();
    }
    
    function submitReview() {
        const form = document.getElementById('reviewModalForm');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitReviewBtn');
        
        // Validate rating
        const rating = formData.get('rating');
        if (!rating) {
            alert('Please select a rating before submitting.');
            return;
        }
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
        submitBtn.disabled = true;
        
        // Submit review
        fetch('<?php echo e(route("customer.orders.submitReview")); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Review submitted successfully!');
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                modal.hide();
                // Reload page to update the sections
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to submit review'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the review.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Review';
            submitBtn.disabled = false;
        });
    }
    
    // Star rating text updates
    document.addEventListener('DOMContentLoaded', function() {
        const starInputs = document.querySelectorAll('.star-rating-modal input[type="radio"]');
        const ratingText = document.getElementById('ratingText');
        
        starInputs.forEach(input => {
            input.addEventListener('change', function() {
                const rating = this.value;
                const texts = {
                    '1': 'Poor',
                    '2': 'Fair', 
                    '3': 'Good',
                    '4': 'Very Good',
                    '5': 'Excellent'
                };
                ratingText.textContent = texts[rating] || '';
            });
        });
    });
    
    // Tab filtering with server-side requests
    document.querySelectorAll('.order-tabs .nav-link').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            let status = this.getAttribute('data-status');
            
            // Handle To Review tab specially - don't submit form, show review sections
            if (status === 'to_review') {
                e.preventDefault();
                e.stopPropagation();
                // Show review list and hide order list
                const orderList = document.getElementById('orderList');
                const reviewList = document.getElementById('reviewList');
                if (orderList) orderList.style.display = 'none';
                if (reviewList) reviewList.style.display = 'block';
                return; // Don't submit form for review tab
            } else {
                // For other tabs, allow normal form submission
                // Show order list and hide review list for other tabs
                const orderList = document.getElementById('orderList');
                const reviewList = document.getElementById('reviewList');
                if (orderList) orderList.style.display = 'block';
                if (reviewList) reviewList.style.display = 'none';
                
                // Update the hidden status filter input
                const statusFilter = document.getElementById('statusFilter');
                if (statusFilter) statusFilter.value = status;
                
                // Submit the form to reload with new filter
                const orderFilterForm = document.getElementById('orderFilterForm');
                if (orderFilterForm) orderFilterForm.submit();
            }
        });
    });
    // Review dropdown logic for To Review tab
    const toReviewTab = document.getElementById('tab-to-review');
    const reviewDropdownMenu = document.getElementById('reviewDropdownMenu');
    const toReviewTabLabel = document.getElementById('toReviewTabLabel');
    
    // Handle To Review tab click to show/hide dropdown
    if (toReviewTab && reviewDropdownMenu) {
        toReviewTab.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle dropdown visibility
            const isVisible = reviewDropdownMenu.style.display === 'block';
            reviewDropdownMenu.style.display = isVisible ? 'none' : 'block';
            
            // If showing dropdown, also show the review list
            if (!isVisible) {
                const orderList = document.getElementById('orderList');
                const reviewList = document.getElementById('reviewList');
                if (orderList) orderList.style.display = 'none';
                if (reviewList) reviewList.style.display = 'block';
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (reviewDropdownMenu && !e.target.closest('#toReviewTabWrapper')) {
            reviewDropdownMenu.style.display = 'none';
        }
    });
    
    // Handle dropdown option clicks
    if (reviewDropdownMenu) {
        document.querySelectorAll('#reviewDropdownMenu button').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Remove active class from all dropdown buttons
                document.querySelectorAll('#reviewDropdownMenu button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Update tab label (without arrow)
                if (toReviewTabLabel) {
                    toReviewTabLabel.textContent = this.textContent;
                }
                
                // Hide dropdown
                reviewDropdownMenu.style.display = 'none';
                
                // Show/hide appropriate sections
                const toBeReviewSection = document.getElementById('toBeReviewSection');
                const reviewedSection = document.getElementById('reviewedSection');
                const shopReviewSection = document.getElementById('shopReviewSection');
                
                if(this.getAttribute('data-review-type') === 'to_be_review') {
                    if (toBeReviewSection) toBeReviewSection.style.display = '';
                    if (reviewedSection) reviewedSection.style.display = 'none';
                    if (shopReviewSection) shopReviewSection.style.display = 'none';
                } else if(this.getAttribute('data-review-type') === 'reviewed') {
                    if (toBeReviewSection) toBeReviewSection.style.display = 'none';
                    if (reviewedSection) reviewedSection.style.display = '';
                    if (shopReviewSection) shopReviewSection.style.display = 'none';
                } else if(this.getAttribute('data-review-type') === 'shop_review') {
                    if (toBeReviewSection) toBeReviewSection.style.display = 'none';
                    if (reviewedSection) reviewedSection.style.display = 'none';
                    if (shopReviewSection) shopReviewSection.style.display = '';
                }
            });
        });
    }
    
    // Default: show To be Review section when To Review tab is active
    const toBeReviewSection = document.getElementById('toBeReviewSection');
    const reviewedSection = document.getElementById('reviewedSection');
    if(toBeReviewSection) {
        toBeReviewSection.style.display = '';
    }
    if(reviewedSection) {
        reviewedSection.style.display = 'none';
    }
    // Search filtering with debouncing
    let searchTimeout;
    const searchInput = document.getElementById('orderSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                document.getElementById('orderFilterForm').submit();
            }, 500); // Wait 500ms after user stops typing
        });
    }

    // Debug: Log current status on page load
    console.log('Current status filter:', document.getElementById('statusFilter').value);
    console.log('Current search term:', document.getElementById('orderSearchInput').value);

    // Review form submission
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('review-form')) {
            e.preventDefault();
            
            const form = e.target;
            const orderId = form.getAttribute('data-order-id');
            const productId = form.getAttribute('data-product-id');
            const formData = new FormData(form);
            
            // Add order and product IDs to form data
            formData.append('order_id', orderId);
            formData.append('product_id', productId);
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            submitBtn.disabled = true;
            
            fetch('<?php echo e(route("customer.orders.submitReview")); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Review submitted successfully!');
                    // Reload the page to show updated reviews
                    window.location.reload();
                } else {
                    alert('Failed to submit review: ' + (data.message || 'Unknown error'));
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error submitting review:', error);
                alert('Error submitting review. Please try again.');
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
    });
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/orders/index.blade.php ENDPATH**/ ?>