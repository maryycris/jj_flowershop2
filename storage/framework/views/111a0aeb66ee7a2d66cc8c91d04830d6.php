<?php $__env->startPush('styles'); ?>
<style>
/* Composition dropdown styles */
.composition-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    display: none;
    min-width: 100%;
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.composition-dropdown.show {
    display: block !important;
}
.composition-search {
    position: relative;
}
.composition-product-id {
    margin-top: 5px;
}
.dropdown-item {
    padding: 8px 12px;
    cursor: pointer;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="background: #f6faf6; min-height: 100vh;">
    <!-- Promoted Products Carousel -->
    <div class="mx-auto mb-4" style="max-width: 900px;">
        <div class="bg-white rounded-4 shadow-sm p-3 position-relative">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <button class="btn btn-link text-success p-0" data-bs-target="#promotedCarousel" data-bs-slide="prev"><i class="bi bi-chevron-left" style="font-size: 2rem;"></i></button>
                <h5 class="mb-0 fw-bold text-center flex-grow-1">Promoted Products</h5>
                <button class="btn btn-link text-success p-0" data-bs-target="#promotedCarousel" data-bs-slide="next"><i class="bi bi-chevron-right" style="font-size: 2rem;"></i></button>
            </div>
            <div id="promotedCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php $__currentLoopData = $promotedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="carousel-item <?php if($i === 0): ?> active <?php endif; ?> text-center">
                        <?php if($product->image): ?>
                            <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>" style="height: 180px; object-fit: cover; border-radius: 12px;">
                        <?php endif; ?>
                        <div class="mt-2 fw-bold"><?php echo e($product->name); ?></div>
                        <div class="text-success">₱<?php echo e(number_format($product->price, 2)); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mx-auto mb-3" style="max-width: 900px;">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('clerk.product_catalog.index')); ?>" class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">All Categories</option>
                            <option value="Fresh Flowers" <?php echo e(request('category') == 'Fresh Flowers' ? 'selected' : ''); ?>>Fresh Flowers</option>
                            <option value="Dried Flowers" <?php echo e(request('category') == 'Dried Flowers' ? 'selected' : ''); ?>>Dried Flowers</option>
                            <option value="Artificial Flowers" <?php echo e(request('category') == 'Artificial Flowers' ? 'selected' : ''); ?>>Artificial Flowers</option>
                            <option value="Floral Supplies" <?php echo e(request('category') == 'Floral Supplies' ? 'selected' : ''); ?>>Floral Supplies</option>
                            <option value="Packaging Materials" <?php echo e(request('category') == 'Packaging Materials' ? 'selected' : ''); ?>>Packaging Materials</option>
                            <option value="Materials, Tools, and Equipment" <?php echo e(request('category') == 'Materials, Tools, and Equipment' ? 'selected' : ''); ?>>Materials, Tools, and Equipment</option>
                            <option value="Office Supplies" <?php echo e(request('category') == 'Office Supplies' ? 'selected' : ''); ?>>Office Supplies</option>
                            <option value="Other Offers" <?php echo e(request('category') == 'Other Offers' ? 'selected' : ''); ?>>Other Offers</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="price_min" class="form-label">Min Price</label>
                        <input type="number" name="price_min" id="price_min" class="form-control" value="<?php echo e(request('price_min')); ?>" placeholder="0.00" step="0.01">
                    </div>
                    <div class="col-md-3">
                        <label for="price_max" class="form-label">Max Price</label>
                        <input type="number" name="price_max" id="price_max" class="form-control" value="<?php echo e(request('price_max')); ?>" placeholder="9999.99" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label for="sort" class="form-label">Sort By</label>
                        <select name="sort" id="sort" class="form-select">
                            <option value="name_asc" <?php echo e(request('sort') == 'name_asc' ? 'selected' : ''); ?>>Name A-Z</option>
                            <option value="name_desc" <?php echo e(request('sort') == 'name_desc' ? 'selected' : ''); ?>>Name Z-A</option>
                            <option value="price_asc" <?php echo e(request('sort') == 'price_asc' ? 'selected' : ''); ?>>Price Low-High</option>
                            <option value="price_desc" <?php echo e(request('sort') == 'price_desc' ? 'selected' : ''); ?>>Price High-Low</option>
                            <option value="newest" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>Newest First</option>
                            <option value="oldest" <?php echo e(request('sort') == 'oldest' ? 'selected' : ''); ?>>Oldest First</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Apply Filters
                        </button>
                        <a href="<?php echo e(route('clerk.product_catalog.index')); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                        <span class="ms-3 text-muted">
                            Showing <?php echo e($products->count()); ?> product(s)
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Product Grid Card -->
    <div class="mx-auto" style="max-width: 900px;">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="mb-3 fw-bold fs-5">
                <?php if(request('category')): ?>
                    <?php echo e(request('category')); ?> Products
                <?php else: ?>
                    All Products
                <?php endif; ?>
            </div>
            <div class="row g-3 product-grid">
                <!-- Add New Product Card -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card add-new-product-card h-100 d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus fa-3x text-muted"></i>
                    </div>
                </div>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <img src="<?php echo e($product->image ? asset('storage/' . $product->image) : '/images/logo.png'); ?>" class="card-img-top product-image" alt="<?php echo e($product->name); ?>">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1"><?php echo e($product->name); ?></h6>
                            <p class="card-text product-price">₱<?php echo e(number_format($product->price, 2)); ?></p>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button class="btn btn-light btn-icon edit-product-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editProductModal" data-product='<?php echo e(json_encode($product)); ?>'><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-light btn-icon manage-images-btn" title="Images" data-bs-toggle="modal" data-bs-target="#manageImagesModal" data-product='<?php echo e(json_encode($product)); ?>'><i class="bi bi-images"></i></button>
                                <form action="/clerk/product_catalog/<?php echo e($product->id); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product and its images?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <input type="hidden" name="id" value="<?php echo e($product->id); ?>">
                                    <button type="submit" class="btn btn-light btn-icon text-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <p class="text-center">No products found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('clerk.product_catalog.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3 text-center">
                            <label for="product_image" class="btn btn-outline-secondary">
                                <i class="fas fa-upload me-2"></i>Upload Image
                                <input type="file" id="product_image" name="image" style="display:none;" accept="image/*" required>
                            </label>
                            <img id="image_preview" src="" alt="Image Preview" class="img-thumbnail mt-2" style="display:none; max-width: 150px; max-height: 150px;">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_code" class="form-label">Product Code</label>
                                    <input type="text" class="form-control" id="product_code" name="code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="name" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_category" class="form-label">Category</label>
                                    <select class="form-select" id="product_category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Fresh Flowers">Fresh Flowers</option>
                                        <option value="Dried Flowers">Dried Flowers</option>
                                        <option value="Artificial Flowers">Artificial Flowers</option>
                                        <option value="Floral Supplies">Floral Supplies</option>
                                        <option value="Packaging Materials">Packaging Materials</option>
                                        <option value="Materials, Tools, and Equipment">Materials, Tools, and Equipment</option>
                                        <option value="Office Supplies">Office Supplies</option>
                                        <option value="Other Offers">Other Offers</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_stock" class="form-label">Initial Stock</label>
                                    <input type="number" class="form-control" id="product_stock" name="stock" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_price" class="form-label">Selling Price</label>
                                    <input type="number" class="form-control" id="product_price" name="price" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_cost_price" class="form-label">Cost Price</label>
                                    <input type="number" class="form-control" id="product_cost_price" name="cost_price" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_reorder_min" class="form-label">Reorder Min</label>
                                    <input type="number" class="form-control" id="product_reorder_min" name="reorder_min" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_reorder_max" class="form-label">Reorder Max</label>
                                    <input type="number" class="form-control" id="product_reorder_max" name="reorder_max" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="product_description" class="form-label">Description</label>
                            <textarea class="form-control" id="product_description" name="description" rows="3" placeholder="Enter product description..."></textarea>
                        </div>
                        
                        <!-- Product Composition Section -->
                        <div class="mb-3">
                            <label class="form-label">Product Composition (Materials Needed)</label>
                            <div id="composition-container">
                                <div class="composition-row row mb-2">
                    <div class="col-md-3">
                        <select class="form-select composition-select" name="compositions[0][component_id]" id="composition-select-0" onchange="updateCompositionName(0)">
                            <option value="">Select Material...</option>
                            <option value="1" data-name="Red roses" data-stock="50">Red roses (Stock: 50)</option>
                            <option value="2" data-name="White roses" data-stock="30">White roses (Stock: 30)</option>
                            <option value="3" data-name="Pink roses" data-stock="25">Pink roses (Stock: 25)</option>
                            <option value="4" data-name="Sunflower" data-stock="15">Sunflower (Stock: 15)</option>
                            <option value="5" data-name="Carnation" data-stock="20">Carnation (Stock: 20)</option>
                            <option value="6" data-name="Tulips" data-stock="18">Tulips (Stock: 18)</option>
                            <option value="7" data-name="Aster" data-stock="12">Aster (Stock: 12)</option>
                            <option value="8" data-name="Gypsophila" data-stock="8">Gypsophila (Stock: 8)</option>
                            <option value="9" data-name="Eucalyptus" data-stock="22">Eucalyptus (Stock: 22)</option>
                            <option value="10" data-name="Lily" data-stock="14">Lily (Stock: 14)</option>
                        </select>
                        <input type="hidden" class="composition-component-name" name="compositions[0][component_name]">
                    </div>
                                    <div class="col-md-2">
                                        <div class="composition-product-id" style="display: none;">
                                            <small class="text-muted">ID: <span class="product-id-display">-</span></small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control composition-quantity" name="compositions[0][quantity]" placeholder="Qty" step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select composition-unit" name="compositions[0][unit]" required>
                                            <option value="">Unit</option>
                                            <option value="Pieces">Pieces</option>
                                            <option value="Meters">Meters</option>
                                            <option value="Grams">Grams</option>
                                            <option value="Kilograms">Kilograms</option>
                                            <option value="Liters">Liters</option>
                                            <option value="Units">Units</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-composition" style="display: none;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-composition">
                                <i class="bi bi-plus"></i> Add Categories
                            </button>
                            <button type="button" class="btn btn-info btn-sm ms-2" id="test-dropdown">
                                <i class="bi bi-bug"></i> Test Dropdown
                            </button>
                            <button type="button" class="btn btn-warning btn-sm ms-1" id="debug-info">
                                <i class="bi bi-info"></i> Debug Info
                            </button>
                            <button type="button" class="btn btn-danger btn-sm ms-1" onclick="testDropdown()">
                                <i class="bi bi-play"></i> Simple Test
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editProductForm" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body">
                        <div class="mb-3 text-center">
                            <label for="edit_product_image" class="btn btn-outline-secondary">
                                <i class="fas fa-upload me-2"></i>Upload Image
                                <input type="file" id="edit_product_image" name="image" style="display:none;" accept="image/*">
                            </label>
                            <img id="edit_current_image" src="" alt="Current Image" class="img-thumbnail mt-2" style="display:none; max-width: 150px; max-height: 150px;">
                        </div>
                        <div class="mb-3">
                            <label for="edit_product_name" class="form-label">Product name</label>
                            <input type="text" class="form-control" id="edit_product_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_product_price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="edit_product_price" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_product_category" class="form-label">Category</label>
                            <select class="form-select" id="edit_product_category" name="category" required>
                                <option value="Fresh Flowers">Fresh Flowers</option>
                                <option value="Dried Flowers">Dried Flowers</option>
                                <option value="Artificial Flowers">Artificial Flowers</option>
                                <option value="Floral Supplies">Floral Supplies</option>
                                <option value="Packaging Materials">Packaging Materials</option>
                                <option value="Materials, Tools, and Equipment">Materials, Tools, and Equipment</option>
                                <option value="Office Supplies">Office Supplies</option>
                                <option value="Other Offers">Other Offers</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manage Images Modal -->
    <div class="modal fade" id="manageImagesModal" tabindex="-1" aria-labelledby="manageImagesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="manageImagesModalLabel">Manage Product Image</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="manageImagesForm" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="delete_image" id="delete_image_flag" value="0">
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <label for="manage_product_image" class="btn btn-outline-secondary">
                                <i class="fas fa-upload me-2"></i>Upload New Image
                                <input type="file" id="manage_product_image" name="image" style="display:none;" accept="image/*">
                            </label>
                        </div>
                        <div class="text-center">
                            <img id="current_image_preview" src="" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 200px; display: none;">
                        </div>
                        <div class="text-center mt-2">
                            <img id="new_image_preview" src="" alt="New Image Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px; display: none;">
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger" id="deleteImageBtn">Delete Image</button>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 

<?php $__env->startPush('styles'); ?>
<style>
    body { background: #f6faf6; }
    .product-card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.2s ease-in-out;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .product-image {
        height: 150px;
        width: 100%;
        display: block;
        /* Reverted: show whole image and keep centered */
        object-fit: contain;
        object-position: center;
        background-color: #ffffff;
        padding: 0;
        margin: 0;
        border-radius: 10px 10px 0 0;
    }
    .product-card .card-img-top {
        margin: 0; /* ensure bootstrap doesn't add margins */
    }
    /* reverted overlay styles */
    .product-price {
        color: #8ACB88;
        font-weight: 600;
    }
    .btn-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: none;
    }
    .btn-icon i { font-size: 1rem; }
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #8ACB88 !important;
        color: #385E42 !important;
        background: #fff !important;
    }
    .nav-tabs .nav-link {
        border: none !important;
        color: #8ACB88 !important;
        font-weight: 500;
        background: #fff !important;
        margin: 0 1.5rem;
        font-size: 1.1rem;
    }
    .nav-tabs {
        border-bottom: none !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Image preview for Add Product Modal
        const productImageInput = document.getElementById('product_image');
        const imagePreview = document.getElementById('image_preview');

        if (productImageInput) {
            productImageInput.addEventListener('change', function(event) {
                if (event.target.files && event.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(event.target.files[0]);
                } else {
                    imagePreview.src = '';
                    imagePreview.style.display = 'none';
                }
            });
        }

        // Edit Product Modal population
        var editProductModal = document.getElementById('editProductModal');
        editProductModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var product = JSON.parse(button.getAttribute('data-product'));

            var form = editProductModal.querySelector('#editProductForm');
            form.action = '/clerk/product_catalog/' + product.id;

            editProductModal.querySelector('#edit_product_name').value = product.name;
            editProductModal.querySelector('#edit_product_price').value = product.price;
            editProductModal.querySelector('#edit_product_category').value = product.category;

            // show current image if any
            var currentImg = document.getElementById('edit_current_image');
            if (product.image) {
                currentImg.src = '<?php echo e(asset('storage')); ?>' + '/' + product.image;
                currentImg.style.display = 'block';
            } else {
                currentImg.src = '';
                currentImg.style.display = 'none';
            }
        });

        // Manage Images Modal population and functionality
        var manageImagesModal = document.getElementById('manageImagesModal');
        manageImagesModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var product = JSON.parse(button.getAttribute('data-product'));
            var currentImagePreview = document.getElementById('current_image_preview');
            var newImagePreview = document.getElementById('new_image_preview');

            if (product.image) {
                currentImagePreview.src = '<?php echo e(asset('storage/')); ?>' + '/' + product.image;
                currentImagePreview.style.display = 'block';
                newImagePreview.style.display = 'none';
            } else {
                currentImagePreview.src = '';
                currentImagePreview.style.display = 'none';
                newImagePreview.style.display = 'none';
            }

            var form = manageImagesModal.querySelector('#manageImagesForm');
            form.action = '/clerk/product_catalog/' + product.id; // Set update form action

            // Set delete image button action
            var deleteImageBtn = document.getElementById('deleteImageBtn');
            deleteImageBtn.onclick = function() {
                if (confirm('Are you sure you want to delete the current product image?')) {
                    document.getElementById('delete_image_flag').value = '1';
                    form.submit();
                }
            };

            // Image preview for new upload
            var manageProductImageInput = document.getElementById('manage_product_image');
            var newImagePreview = document.getElementById('new_image_preview');
            
            if (manageProductImageInput) {
                manageProductImageInput.addEventListener('change', function(event) {
                    if (event.target.files && event.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            newImagePreview.src = e.target.result;
                            newImagePreview.style.display = 'block';
                        };
                        reader.readAsDataURL(event.target.files[0]);
                    } else {
                        newImagePreview.src = '';
                        newImagePreview.style.display = 'none';
                    }
                });
            }
        });

        // Product Composition Management
        let compositionIndex = 1;
        let allInventoryItems = [];
        
        // Load inventory items on page load
        <?php
            $inventoryItems = \App\Services\InventoryService::getAvailableInventoryItems();
        ?>
        allInventoryItems = <?php echo json_encode($inventoryItems, 15, 512) ?>;
        
        console.log('=== INVENTORY ITEMS LOADED ===');
        console.log('Total items:', allInventoryItems.length);
        console.log('Items:', allInventoryItems);
        
        // Test if Fresh Flowers are available
        const freshFlowers = allInventoryItems.filter(item => item.category === 'Fresh Flowers');
        console.log('Fresh Flowers available:', freshFlowers);
        
        // Simple dropdown show/hide function
        function showDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.style.display = 'block';
                console.log('Showing dropdown:', dropdownId);
            }
        }
        
        function hideDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.style.display = 'none';
                console.log('Hiding dropdown:', dropdownId);
            }
        }
        
        // Filter inventory items by category
        function getFilteredInventoryItems(category) {
            if (!category) return allInventoryItems;
            return allInventoryItems.filter(item => item.category === category);
        }
        
        // Create dropdown options
        function createDropdownOptions(items, dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (!dropdown) return;
            
            dropdown.innerHTML = '';
            if (items.length === 0) {
                dropdown.innerHTML = '<div class="p-2 text-muted">No materials found</div>';
                return;
            }
            
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'p-2 border-bottom dropdown-item';
                div.style.cursor = 'pointer';
                div.innerHTML = `${item.name} (Stock: ${item.stock})`;
                div.setAttribute('data-id', item.id);
                div.setAttribute('data-name', item.name);
                dropdown.appendChild(div);
            });
        }
        
        // Setup search functionality for a composition row
        function setupCompositionSearch(row, index) {
            const searchInput = row.querySelector('.composition-search');
            const dropdownId = `dropdown-${index}`;
            const componentIdInput = row.querySelector('.composition-component-id');
            const componentNameInput = row.querySelector('.composition-component-name');
            const productIdDisplay = row.querySelector('.product-id-display');
            const productIdContainer = row.querySelector('.composition-product-id');
            
            console.log('Setting up search for row:', index);
            
                // Show dropdown on focus - SIMPLIFIED VERSION
                searchInput.addEventListener('focus', function() {
                    console.log('=== SEARCH INPUT FOCUSED ===');
                    
                    const dropdown = document.getElementById(dropdownId);
                    if (dropdown) {
                        // Create simple test dropdown
                        dropdown.innerHTML = `
                            <div style="padding: 8px; cursor: pointer; background: #e3f2fd; border-bottom: 1px solid #ccc;">Red roses (Stock: 50)</div>
                            <div style="padding: 8px; cursor: pointer; background: #e8f5e8; border-bottom: 1px solid #ccc;">White roses (Stock: 30)</div>
                            <div style="padding: 8px; cursor: pointer; background: #fff3e0; border-bottom: 1px solid #ccc;">Pink roses (Stock: 25)</div>
                            <div style="padding: 8px; cursor: pointer; background: #fce4ec;">Sunflower (Stock: 15)</div>
                        `;
                        dropdown.style.display = 'block';
                        dropdown.style.position = 'absolute';
                        dropdown.style.zIndex = '9999';
                        dropdown.style.border = '2px solid green';
                        dropdown.style.backgroundColor = 'white';
                        dropdown.style.boxShadow = '0 4px 8px rgba(0,0,0,0.3)';
                        dropdown.style.maxHeight = '200px';
                        dropdown.style.overflowY = 'auto';
                        console.log('✅ SIMPLE DROPDOWN CREATED');
                    } else {
                        console.log('❌ Dropdown element not found');
                    }
                });
            
            // Search functionality
            searchInput.addEventListener('input', function() {
                console.log('Search input changed for row:', index, this.value);
                const searchTerm = this.value.toLowerCase();
                const category = document.getElementById('product_category').value;
                const filteredItems = getFilteredInventoryItems(category);
                const searchResults = filteredItems.filter(item => 
                    item.name.toLowerCase().includes(searchTerm)
                );
                console.log('Search results:', searchResults);
                createDropdownOptions(searchResults, dropdownId);
                showDropdown(dropdownId);
            });
            
            // Handle item selection
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Dropdown clicked for row:', index, e.target);
                    if (e.target.classList.contains('dropdown-item')) {
                        const itemId = e.target.getAttribute('data-id');
                        const itemName = e.target.getAttribute('data-name');
                        
                        console.log('Selected item for row:', index, itemId, itemName);
                        
                        searchInput.value = itemName;
                        componentIdInput.value = itemId;
                        componentNameInput.value = itemName;
                        productIdDisplay.textContent = itemId;
                        productIdContainer.style.display = 'block';
                        
                        hideDropdown(dropdownId);
                    }
                });
            }
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!row.contains(e.target)) {
                    hideDropdown(dropdownId);
                }
            });
        }
        
            // Add composition row
            document.getElementById('add-composition').addEventListener('click', function() {
                const container = document.getElementById('composition-container');
                const newRow = document.createElement('div');
                newRow.className = 'composition-row row mb-2';
                newRow.innerHTML = `
                    <div class="col-md-3">
                        <select class="form-select composition-select" name="compositions[${compositionIndex}][component_id]" id="composition-select-${compositionIndex}" onchange="updateCompositionName(${compositionIndex})">
                            <option value="">Select Material...</option>
                        </select>
                        <input type="hidden" class="composition-component-name" name="compositions[${compositionIndex}][component_name]">
                    </div>
                    <div class="col-md-2">
                        <div class="composition-product-id" style="display: none;">
                            <small class="text-muted">ID: <span class="product-id-display">-</span></small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control composition-quantity" name="compositions[${compositionIndex}][quantity]" placeholder="Qty" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select composition-unit" name="compositions[${compositionIndex}][unit]" required>
                            <option value="">Unit</option>
                            <option value="Pieces">Pieces</option>
                            <option value="Meters">Meters</option>
                            <option value="Grams">Grams</option>
                            <option value="Kilograms">Kilograms</option>
                            <option value="Liters">Liters</option>
                            <option value="Units">Units</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-composition">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                
                // Populate the new select with current category items
                updateCompositionDropdowns();
                
                compositionIndex++;
                
                // Show remove buttons for all rows
                document.querySelectorAll('.remove-composition').forEach(btn => {
                    btn.style.display = 'inline-block';
                });
            });
        
        // Setup search for existing row
        document.querySelectorAll('.composition-row').forEach((row, index) => {
            setupCompositionSearch(row, index);
        });
        
        // Remove composition row
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-composition')) {
                e.target.closest('.composition-row').remove();
                
                // Hide remove button if only one row left
                const rows = document.querySelectorAll('.composition-row');
                if (rows.length <= 1) {
                    document.querySelectorAll('.remove-composition').forEach(btn => {
                        btn.style.display = 'none';
                    });
                }
            }
        });
        
        // Update composition dropdowns when category changes
        document.getElementById('product_category').addEventListener('change', function() {
            updateCompositionDropdowns();
        });
        
            // Test dropdown button
            document.getElementById('test-dropdown').addEventListener('click', function() {
                console.log('=== TEST DROPDOWN CLICKED ===');
                console.log('All inventory items:', allInventoryItems);
                
                // First, let's test if we can show a simple dropdown
                const dropdown = document.getElementById('dropdown-0');
                if (dropdown) {
                    console.log('Dropdown element found:', dropdown);
                    dropdown.innerHTML = '<div class="p-2 border-bottom" style="cursor: pointer; background: #f0f0f0;">Test Item 1</div><div class="p-2 border-bottom" style="cursor: pointer; background: #f0f0f0;">Test Item 2</div><div class="p-2" style="cursor: pointer; background: #f0f0f0;">Test Item 3</div>';
                    dropdown.style.display = 'block';
                    dropdown.style.border = '2px solid red';
                    dropdown.style.backgroundColor = 'yellow';
                    console.log('✅ Test dropdown should be visible now');
                } else {
                    console.log('❌ Dropdown element not found');
                }
                
                // Now try with real data
                const category = document.getElementById('product_category').value;
                console.log('Selected category:', category);
                const filteredItems = getFilteredInventoryItems(category);
                console.log('Filtered items for test:', filteredItems);
                
                // Show Fresh Flowers specifically
                const freshFlowers = allInventoryItems.filter(item => item.category === 'Fresh Flowers');
                console.log('Fresh Flowers for test:', freshFlowers);
                
                if (freshFlowers.length > 0) {
                    dropdown.innerHTML = '';
                    freshFlowers.slice(0, 5).forEach(flower => {
                        const div = document.createElement('div');
                        div.className = 'p-2 border-bottom';
                        div.style.cursor = 'pointer';
                        div.style.backgroundColor = '#e8f5e8';
                        div.innerHTML = `${flower.name} (Stock: ${flower.stock})`;
                        dropdown.appendChild(div);
                    });
                    dropdown.style.display = 'block';
                    dropdown.style.border = '2px solid green';
                    dropdown.style.backgroundColor = 'white';
                    console.log('✅ Fresh Flowers dropdown should be visible now');
                }
            });
        
        // Debug info button
        document.getElementById('debug-info').addEventListener('click', function() {
            console.log('=== DEBUG INFO ===');
            console.log('1. Inventory items loaded:', allInventoryItems ? allInventoryItems.length : 'undefined');
            console.log('2. Dropdown element exists:', document.getElementById('dropdown-0') ? 'YES' : 'NO');
            console.log('3. Search input exists:', document.getElementById('search-0') ? 'YES' : 'NO');
            console.log('4. Category value:', document.getElementById('product_category').value);
            console.log('5. All composition rows:', document.querySelectorAll('.composition-row').length);
            console.log('6. Window loaded:', document.readyState);
            
            // Test basic dropdown functionality
            const dropdown = document.getElementById('dropdown-0');
            if (dropdown) {
                dropdown.style.border = '2px solid red';
                dropdown.style.backgroundColor = 'yellow';
                console.log('✅ Dropdown element is accessible and styled');
            }
        });
    });

        // Simple test function
        function testDropdown() {
            console.log('=== SIMPLE TEST ===');
            const dropdown = document.getElementById('dropdown-0');
            if (dropdown) {
                dropdown.innerHTML = '<div style="padding: 10px; background: red; color: white;">TEST DROPDOWN WORKS!</div>';
                dropdown.style.display = 'block';
                dropdown.style.position = 'absolute';
                dropdown.style.zIndex = '9999';
                dropdown.style.border = '3px solid red';
                console.log('✅ Test dropdown created');
            } else {
                console.log('❌ Dropdown not found');
            }
        }
        
        // Simple dropdown functions
        function showSimpleDropdown(dropdownId) {
            console.log('=== SHOWING DROPDOWN ===', dropdownId);
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                // Get flowers from real inventory data based on category
                const category = document.getElementById('product_category').value;
                console.log('Selected category:', category);
                console.log('All inventory items:', allInventoryItems);
                
                // Filter inventory items by selected category
                let flowers = [];
                if (allInventoryItems && allInventoryItems.length > 0) {
                    flowers = allInventoryItems.filter(item => item.category === category);
                    console.log('Filtered flowers for category', category, ':', flowers);
                } else {
                    console.log('No inventory items loaded, using fallback data');
                    // Fallback data if inventory not loaded
                    if (category === 'Fresh Flowers') {
                        flowers = [
                            {name: 'Red roses', stock: 50, id: 1},
                            {name: 'White roses', stock: 30, id: 2},
                            {name: 'Pink roses', stock: 25, id: 3},
                            {name: 'Sunflower', stock: 15, id: 4},
                            {name: 'Carnation', stock: 20, id: 5},
                            {name: 'Tulips', stock: 18, id: 6}
                        ];
                    } else if (category === 'Dried Flowers') {
                        flowers = [
                            {name: 'Fossilized Roses', stock: 10, id: 7},
                            {name: 'Preserve Roses', stock: 12, id: 8},
                            {name: 'Gypsophila (Dried)', stock: 8, id: 9},
                            {name: 'Eucalyptus (Dried)', stock: 15, id: 10}
                        ];
                    } else {
                        flowers = [
                            {name: 'Sample Material 1', stock: 5, id: 11},
                            {name: 'Sample Material 2', stock: 7, id: 12}
                        ];
                    }
                }
                
                dropdown.innerHTML = '';
                flowers.forEach(flower => {
                    const div = document.createElement('div');
                    div.style.padding = '8px';
                    div.style.cursor = 'pointer';
                    div.style.borderBottom = '1px solid #eee';
                    div.style.backgroundColor = '#f8f9fa';
                    div.innerHTML = `${flower.name} (Stock: ${flower.stock})`;
                    div.setAttribute('data-id', flower.id);
                    div.setAttribute('data-name', flower.name);
                    
                    // Add click handler
                    div.onclick = function() {
                        const input = document.getElementById('search-0');
                        const componentIdInput = document.querySelector('.composition-component-id');
                        const componentNameInput = document.querySelector('.composition-component-name');
                        
                        input.value = flower.name;
                        componentIdInput.value = flower.id;
                        componentNameInput.value = flower.name;
                        
                        hideSimpleDropdown(dropdownId);
                        console.log('Selected:', flower.name);
                    };
                    
                    // Add hover effect
                    div.onmouseover = function() {
                        this.style.backgroundColor = '#e3f2fd';
                    };
                    div.onmouseout = function() {
                        this.style.backgroundColor = '#f8f9fa';
                    };
                    
                    dropdown.appendChild(div);
                });
                
                dropdown.style.display = 'block';
                console.log('✅ Dropdown created with', flowers.length, 'items');
            } else {
                console.log('❌ Dropdown element not found');
            }
        }
        
        function hideSimpleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.style.display = 'none';
                console.log('Dropdown hidden');
            }
        }
        
        // Simple function to update composition name when select changes
        function updateCompositionName(index) {
            const select = document.getElementById('composition-select-' + index);
            const nameInput = select.parentElement.querySelector('.composition-component-name');
            
            if (select.value) {
                const selectedOption = select.options[select.selectedIndex];
                nameInput.value = selectedOption.getAttribute('data-name');
                console.log('Selected material:', selectedOption.getAttribute('data-name'));
            } else {
                nameInput.value = '';
            }
        }
        
        // Update composition dropdowns when category changes
        function updateCompositionDropdowns() {
            const category = document.getElementById('product_category').value;
            console.log('Category changed to:', category);
            
            // Hardcoded data for testing
            let categoryItems = [];
            if (category === 'Fresh Flowers') {
                categoryItems = [
                    {id: 1, name: 'Red roses', stock: 50},
                    {id: 2, name: 'White roses', stock: 30},
                    {id: 3, name: 'Pink roses', stock: 25},
                    {id: 4, name: 'Sunflower', stock: 15},
                    {id: 5, name: 'Carnation', stock: 20},
                    {id: 6, name: 'Tulips', stock: 18},
                    {id: 7, name: 'Aster', stock: 12},
                    {id: 8, name: 'Gypsophila', stock: 8},
                    {id: 9, name: 'Eucalyptus', stock: 22},
                    {id: 10, name: 'Lily', stock: 14}
                ];
            } else if (category === 'Dried Flowers') {
                categoryItems = [
                    {id: 11, name: 'Fossilized Roses', stock: 10},
                    {id: 12, name: 'Preserve Roses', stock: 12},
                    {id: 13, name: 'Gypsophila (Dried)', stock: 8},
                    {id: 14, name: 'Eucalyptus (Dried)', stock: 15},
                    {id: 15, name: 'Bunny tails', stock: 6},
                    {id: 16, name: 'Trigo grass', stock: 9}
                ];
            } else if (category === 'Artificial Flowers') {
                categoryItems = [
                    {id: 17, name: 'Tulip flower (Artificial)', stock: 20},
                    {id: 18, name: 'Rose flower (Artificial)', stock: 25},
                    {id: 19, name: 'Satin Ribbon flower', stock: 15}
                ];
            } else if (category === 'Floral Supplies') {
                categoryItems = [
                    {id: 20, name: 'Floral foam', stock: 30},
                    {id: 21, name: 'Glitter Ribbon (gold)', stock: 12},
                    {id: 22, name: 'Glitter Ribbon (silver)', stock: 10},
                    {id: 23, name: '2 cm satin ribbon (red)', stock: 25},
                    {id: 24, name: '2 cm satin ribbon (blue)', stock: 20}
                ];
            }
            
            console.log('Items for category', category, ':', categoryItems);
            
            // Update all composition selects
            document.querySelectorAll('.composition-select').forEach((select, index) => {
                // Clear existing options except first
                select.innerHTML = '<option value="">Select Material...</option>';
                
                // Add new options
                categoryItems.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.setAttribute('data-name', item.name);
                    option.setAttribute('data-stock', item.stock);
                    option.textContent = item.name + ' (Stock: ' + item.stock + ')';
                    select.appendChild(option);
                });
            });
        }
        
        // Make functions global
        window.testDropdown = testDropdown;
        window.showSimpleDropdown = showSimpleDropdown;
        window.hideSimpleDropdown = hideSimpleDropdown;
        window.updateCompositionName = updateCompositionName;
        window.updateCompositionDropdowns = updateCompositionDropdowns;
        
        // Ensure our code runs after everything is loaded
        window.addEventListener('load', function() {
            console.log('=== PAGE FULLY LOADED - INITIALIZING DROPDOWN ===');
            
            // Re-initialize composition search for existing rows
            document.querySelectorAll('.composition-row').forEach((row, index) => {
                console.log('Re-initializing row:', index);
                setupCompositionSearch(row, index);
            });
            
            // Test if inventory items are loaded
            console.log('Inventory items check:', allInventoryItems);
            if (allInventoryItems && allInventoryItems.length > 0) {
                console.log('✅ Inventory items loaded successfully');
            } else {
                console.log('❌ No inventory items loaded');
            }
        });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/product_catalog/index.blade.php ENDPATH**/ ?>