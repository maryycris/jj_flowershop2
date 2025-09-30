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
    }
    .order-list-row:last-child {
        border-bottom: none;
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
        background: none;
        border: none;
        text-align: left;
        padding: 8px 16px;
        color: #222;
        cursor: pointer;
    }
    .review-dropdown-menu button.active {
        background: #cfe3d8;
        font-weight: bold;
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
        <div class="col-md-9 col-lg-7">
            <div class="py-4 px-3">
                <div class="mb-3 d-flex align-items-center">
                    <ul class="nav nav-tabs order-tabs" id="orderTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-all" data-status="all" type="button" role="tab">All</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-to-pay" data-status="to_pay" type="button" role="tab">To Pay</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-to-ship" data-status="to_ship" type="button" role="tab">To Ship</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-to-receive" data-status="to_receive" type="button" role="tab">To Receive</button>
                        </li>
                        <li class="nav-item position-relative" role="presentation">
                            <div class="d-inline-block" id="toReviewTabWrapper">
                                <button class="nav-link" id="tab-to-review" data-status="to_review" type="button" role="tab">
                                    <span id="toReviewTabLabel">To Review ▼</span>
                                </button>
                                <div class="review-dropdown-menu" id="reviewDropdownMenu" style="display:none; position:absolute; top:100%; left:0; min-width:150px;">
                                    <button type="button" class="dropdown-item active" data-review-type="to_be_review">To be Review</button>
                                    <button type="button" class="dropdown-item" data-review-type="reviewed">Reviewed</button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="order-search-bar mb-2">
                    <input type="text" id="orderSearchInput" class="form-control border-0 bg-transparent" placeholder="Search orders by product name...">
                </div>
                <div id="reviewSectionHeader" class="d-flex align-items-center mb-0" style="display:none;">
                    <div id="reviewSectionTitle" style="font-size:1.3rem;color:#7bb47b; margin-right: 12px;">To be Review</div>
                </div>
                <?php if($orders->isEmpty()): ?>
                    <div class="alert alert-info" role="alert">
                        You haven't placed any orders yet.
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
                                    <div class="row order-list-row order-row" 
                                        data-status="<?php echo e(\App\Services\OrderStatusService::getCustomerDisplayStatus($order->status)); ?>"
                                        data-product="<?php echo e(strtolower($product->name)); ?>"
                                        data-review="<?php echo e(isset($product->pivot->reviewed) && $product->pivot->reviewed ? 'reviewed' : 'to_be_review'); ?>">
                                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                                            <img src="<?php echo e($product->image_url ?? asset('images/placeholder.png')); ?>" class="order-product-img" alt="Product">
                                        </div>
                                        <div class="col-md-3 d-flex flex-column justify-content-center">
                                            <div class="fw-bold"><?php echo e($product->name); ?></div>
                                            <div class="text-muted small">Order #<?php echo e($order->id); ?></div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">₱<?php echo e(number_format($product->price, 2)); ?></div>
                                        <div class="col-md-2 d-flex align-items-center">x<?php echo e($product->pivot->quantity); ?></div>
                                        <div class="col-md-2 d-flex align-items-center"><?php echo e($order->created_at->format('M d, Y')); ?></div>
                                        <div class="col-md-2 d-flex align-items-center justify-content-end">
                                            <?php if($order->status === 'pending'): ?>
                                                <span class="btn btn-sm btn-warning order-status-btn" style="font-weight:bold;">Pending Approval</span>
                                            <?php else: ?>
                                                <span class="btn btn-sm btn-outline-success order-status-btn"><?php echo e(\App\Services\OrderStatusService::getStatusLabel(\App\Services\OrderStatusService::getCustomerDisplayStatus($order->status))); ?></span>
                                            <?php endif; ?>
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
                            <?php
                            // Sample demo data for reviewable and reviewed products
                            $toBeReview = [
                                ['name'=>'Sample Product 1','order'=>'#100','image'=>asset('images/placeholder.png')],
                                ['name'=>'Sample Product 2','order'=>'#101','image'=>asset('images/placeholder.png')],
                            ];
                            $reviewed = [
                                ['name'=>'Reviewed Product 1','order'=>'#90','image'=>asset('images/placeholder.png'),'rating'=>4,'comment'=>'Great product!'],
                                ['name'=>'Reviewed Product 2','order'=>'#91','image'=>asset('images/placeholder.png'),'rating'=>5,'comment'=>'Excellent!'],
                            ];
                            ?>
                            <div id="toBeReviewSection">
                                <?php $__currentLoopData = $toBeReview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row order-list-row align-items-center">
                                    <div class="col-md-6 d-flex align-items-center">
                                        <img src="<?php echo e($item['image']); ?>" class="order-product-img me-3" alt="Product">
                                        <div>
                                            <div class="fw-bold"><?php echo e($item['name']); ?></div>
                                            <div class="text-muted small">Order <?php echo e($item['order']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <form>
                                            <div class="star-rating mb-2">
                                                <input type="radio" id="star5-<?php echo e($loop->index); ?>" name="rating-<?php echo e($loop->index); ?>" value="5"><label for="star5-<?php echo e($loop->index); ?>">★</label>
                                                <input type="radio" id="star4-<?php echo e($loop->index); ?>" name="rating-<?php echo e($loop->index); ?>" value="4"><label for="star4-<?php echo e($loop->index); ?>">★</label>
                                                <input type="radio" id="star3-<?php echo e($loop->index); ?>" name="rating-<?php echo e($loop->index); ?>" value="3"><label for="star3-<?php echo e($loop->index); ?>">★</label>
                                                <input type="radio" id="star2-<?php echo e($loop->index); ?>" name="rating-<?php echo e($loop->index); ?>" value="2"><label for="star2-<?php echo e($loop->index); ?>">★</label>
                                                <input type="radio" id="star1-<?php echo e($loop->index); ?>" name="rating-<?php echo e($loop->index); ?>" value="1"><label for="star1-<?php echo e($loop->index); ?>">★</label>
                                            </div>
                                            <textarea class="review-comment-box" placeholder="Your comment of the product and the service of the shop"></textarea>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div id="reviewedSection" style="display:none;">
                                <?php $__currentLoopData = $reviewed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row order-list-row align-items-center">
                                    <div class="col-md-6 d-flex align-items-center">
                                        <img src="<?php echo e($item['image']); ?>" class="order-product-img me-3" alt="Product">
                                        <div>
                                            <div class="fw-bold"><?php echo e($item['name']); ?></div>
                                            <div class="text-muted small">Order <?php echo e($item['order']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="star-rating readonly mb-2">
                                            <?php for($i=5;$i>=1;$i--): ?>
                                                <label><?php echo $i <= $item['rating'] ? '★' : '☆'; ?></label>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="review-comment-box"><?php echo e($item['comment']); ?></div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<script>
    // Tab filtering
    document.querySelectorAll('.order-tabs .nav-link').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            document.querySelectorAll('.order-tabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            let status = this.getAttribute('data-status');
            // Show/hide review section header
            if(status === 'to_review') {
                document.getElementById('reviewSectionHeader').style.display = 'flex';
                document.getElementById('orderList').style.display = 'none';
                document.getElementById('reviewList').style.display = '';
                document.getElementById('toReviewTabWrapper').classList.add('show-dropdown');
            } else {
                document.getElementById('reviewSectionHeader').style.display = 'none';
                document.getElementById('orderList').style.display = '';
                document.getElementById('reviewList').style.display = 'none';
                document.getElementById('reviewDropdownMenu').style.display = 'none';
                document.getElementById('toReviewTabWrapper').classList.remove('show-dropdown');
            }
            document.getElementById('reviewSectionTitle').textContent = document.getElementById('toReviewTabLabel').textContent.replace('▼','').trim();
            // Filter order rows by status
            document.querySelectorAll('#orderList .order-row').forEach(function(row) {
                if (status === 'all' || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    // Review dropdown logic for To Review tab
    const toReviewTab = document.getElementById('tab-to-review');
    const reviewDropdownMenu = document.getElementById('reviewDropdownMenu');
    const toReviewTabLabel = document.getElementById('toReviewTabLabel');
    toReviewTab && toReviewTab.addEventListener('click', function(e) {
        if(document.getElementById('toReviewTabWrapper').classList.contains('show-dropdown')) {
            e.preventDefault();
            reviewDropdownMenu.style.display = reviewDropdownMenu.style.display === 'block' ? 'none' : 'block';
        }
    });
    document.addEventListener('click', function(e) {
        if(!e.target.closest('#toReviewTabWrapper')) {
            reviewDropdownMenu.style.display = 'none';
        }
    });
    document.querySelectorAll('#reviewDropdownMenu button').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            document.querySelectorAll('#reviewDropdownMenu button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            toReviewTabLabel.textContent = this.textContent + ' ▼';
            document.getElementById('reviewSectionTitle').textContent = this.textContent;
            reviewDropdownMenu.style.display = 'none';
            if(this.getAttribute('data-review-type') === 'to_be_review') {
                document.getElementById('toBeReviewSection').style.display = '';
                document.getElementById('reviewedSection').style.display = 'none';
            } else {
                document.getElementById('toBeReviewSection').style.display = 'none';
                document.getElementById('reviewedSection').style.display = '';
            }
        });
    });
    // Default: show To be Review section
    if(document.getElementById('toBeReviewSection')) {
        document.getElementById('toBeReviewSection').style.display = '';
        document.getElementById('reviewedSection').style.display = 'none';
    }
    // Search filtering
    document.getElementById('orderSearchInput').addEventListener('input', function() {
        let val = this.value.toLowerCase();
        document.querySelectorAll('#orderList .order-row').forEach(function(row) {
            let product = row.getAttribute('data-product');
            if (product.includes(val)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        // Also filter review sections if visible
        ['toBeReviewSection','reviewedSection'].forEach(function(sectionId){
            let section = document.getElementById(sectionId);
            if(section && section.style.display !== 'none') {
                section.querySelectorAll('.fw-bold').forEach(function(el){
                    let row = el.closest('.order-list-row');
                    if(el.textContent.toLowerCase().includes(val)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/orders/index.blade.php ENDPATH**/ ?>