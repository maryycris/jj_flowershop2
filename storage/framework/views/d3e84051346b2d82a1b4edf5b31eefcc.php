<?php $hideSidebar = true; ?>


<?php $__env->startSection('admin_content'); ?>
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
                        <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>" style="height: 180px; object-fit: cover; border-radius: 12px;">
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
                <form method="GET" action="<?php echo e(route('admin.products.index')); ?>" class="row g-3">
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
                        <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline-secondary">
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
                        <img src="<?php echo e(asset('storage/' . $product->image)); ?>" class="card-img-top product-image" alt="<?php echo e($product->name); ?>">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1"><?php echo e($product->name); ?></h6>
                            <p class="card-text product-price">₱<?php echo e(number_format($product->price, 2)); ?></p>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                        <button class="btn btn-sm btn-info edit-product-btn" data-bs-toggle="modal" data-bs-target="#editProductModal" data-product='<?php echo e(json_encode($product)); ?>'>Edit</button>
                                <button class="btn btn-sm btn-warning manage-images-btn" data-bs-toggle="modal" data-bs-target="#manageImagesModal" data-product='<?php echo e(json_encode($product)); ?>'>Images</button>
                        <form action="<?php echo e(route('admin.products.destroy', $product->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product and its images?');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
            <form action="<?php echo e(route('admin.products.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <label for="product_image" class="btn btn-outline-secondary">
                            <i class="fas fa-upload me-2"></i>Upload Image
                            <input type="file" id="product_image" name="image" style="display:none;" accept="image/*" required>
                        </label>
                        <img id="image_preview" src="" alt="Image Preview" class="img-thumbnail mt-2" style="display:none; max-width: 150px; max-height: 150px;">
                    </div>
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product name</label>
                        <input type="text" class="form-control" id="product_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="product_price" name="price" step="0.01" required>
                    </div>
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
                    
                    <!-- Product Composition Section -->
                    <div class="mb-3">
                        <label class="form-label">Product Composition (Materials Needed)</label>
                        <div id="composition-container">
                            <div class="composition-row row mb-2">
                                <div class="col-4">
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
                                <div class="col-3">
                                    <input type="number" class="form-control" name="compositions[0][quantity]" placeholder="Qty" min="1" required>
                                </div>
                                <div class="col-3">
                                    <select class="form-select" name="compositions[0][unit]" required>
                                        <option value="">Unit</option>
                                        <option value="pieces">Pieces</option>
                                        <option value="stems">Stems</option>
                                        <option value="bunches">Bunches</option>
                                        <option value="grams">Grams</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-composition">×</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success" id="add-composition">
                            <i class="fas fa-plus"></i> Add Component
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="product_description" class="form-label">Description</label>
                        <textarea class="form-control" id="product_description" name="description" rows="3" placeholder="Product description..."></textarea>
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
            <form id="editProductForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
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
        object-fit: cover;
    }
    .product-price {
        color: #8ACB88;
        font-weight: 600;
    }
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
            form.action = '/admin/products/' + product.id; // Set the form action dynamically

            editProductModal.querySelector('#edit_product_name').value = product.name;
            editProductModal.querySelector('#edit_product_price').value = product.price;
            editProductModal.querySelector('#edit_product_category').value = product.category;
        });

        // Composition fields management
        let compositionIndex = 1;
        
        document.getElementById('add-composition').addEventListener('click', function() {
            const container = document.getElementById('composition-container');
            const newRow = document.createElement('div');
            newRow.className = 'composition-row row mb-2';
            newRow.innerHTML = `
                <div class="col-4">
                    <input type="text" class="form-control" name="compositions[${compositionIndex}][component_name]" placeholder="Flower/Component" required>
                </div>
                <div class="col-3">
                    <input type="number" class="form-control" name="compositions[${compositionIndex}][quantity]" placeholder="Qty" min="1" required>
                </div>
                <div class="col-3">
                    <select class="form-select" name="compositions[${compositionIndex}][unit]" required>
                        <option value="">Unit</option>
                        <option value="pieces">Pieces</option>
                        <option value="stems">Stems</option>
                        <option value="bunches">Bunches</option>
                        <option value="grams">Grams</option>
                    </select>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-composition">×</button>
                </div>
            `;
            container.appendChild(newRow);
            compositionIndex++;
        });

        // Remove composition row
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-composition')) {
                e.target.closest('.composition-row').remove();
            }
        });

        // Manage Images Modal population and functionality
        var manageImagesModal = document.getElementById('manageImagesModal');
        manageImagesModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var product = JSON.parse(button.getAttribute('data-product'));

            var form = manageImagesModal.querySelector('#manageImagesForm');
            form.action = '/admin/products/' + product.id + '/images/update'; // Set update form action

            // Show current image if exists
            var currentImagePreview = document.getElementById('current_image_preview');
            if (product.image) {
                currentImagePreview.src = '<?php echo e(asset('storage/')); ?>' + '/' + product.image;
                currentImagePreview.style.display = 'block';
            } else {
                currentImagePreview.style.display = 'none';
            }

            // Set delete image button action
            var deleteImageBtn = document.getElementById('deleteImageBtn');
            deleteImageBtn.onclick = function() {
                if (confirm('Are you sure you want to delete the image for this product?')) {
                    fetch('/admin/products/' + product.id + '/images/delete', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    }).then(response => {
                        if (response.ok) {
                            window.location.reload(); // Reload to reflect changes
                        } else {
                            alert('Failed to delete image.');
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred.');
                    });
                }
            };

            // Image preview for new upload
            var productImageInput = document.getElementById('manage_product_image');
            var newImagePreview = document.getElementById('new_image_preview');
            
            if (productImageInput) {
                productImageInput.addEventListener('change', function(event) {
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
    });

    // Product Composition Management for Admin
    let compositionIndex = 1;

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
                    {id: 1, name: 'Red roses', stock: 50, unit: 'stems'},
                    {id: 2, name: 'White roses', stock: 30, unit: 'stems'},
                    {id: 3, name: 'Pink roses', stock: 25, unit: 'stems'},
                    {id: 4, name: 'Sunflower', stock: 15, unit: 'stems'},
                    {id: 5, name: 'Carnation', stock: 20, unit: 'stems'},
                    {id: 6, name: 'Tulips', stock: 18, unit: 'stems'},
                    {id: 7, name: 'Aster', stock: 12, unit: 'stems'},
                    {id: 8, name: 'Gypsophila', stock: 8, unit: 'bunches'},
                    {id: 9, name: 'Eucalyptus', stock: 22, unit: 'stems'},
                    {id: 10, name: 'Lily', stock: 14, unit: 'stems'},
                    {id: 11, name: 'Baby\'s breath', stock: 16, unit: 'bunches'},
                    {id: 12, name: 'Chrysanthemum', stock: 20, unit: 'stems'}
                ];
            } else if (category === 'Dried Flowers') {
                categoryItems = [
                    {id: 13, name: 'Fossilized Roses', stock: 10, unit: 'pieces'},
                    {id: 14, name: 'Preserve Roses', stock: 12, unit: 'pieces'},
                    {id: 15, name: 'Gypsophila (Dried)', stock: 8, unit: 'bunches'},
                    {id: 16, name: 'Eucalyptus (Dried)', stock: 15, unit: 'stems'},
                    {id: 17, name: 'Bunny tails', stock: 6, unit: 'bunches'},
                    {id: 18, name: 'Trigo grass', stock: 9, unit: 'stems'},
                    {id: 19, name: 'Palm Spear Anahaw', stock: 5, unit: 'pieces'}
                ];
            } else if (category === 'Artificial Flowers') {
                categoryItems = [
                    {id: 20, name: 'Tulip flower (Artificial)', stock: 20, unit: 'pieces'},
                    {id: 21, name: 'Rose flower (Artificial)', stock: 25, unit: 'pieces'},
                    {id: 22, name: 'Satin Ribbon flower', stock: 15, unit: 'pieces'}
                ];
            } else if (category === 'Floral Supplies') {
                categoryItems = [
                    {id: 23, name: 'Floral foam', stock: 30, unit: 'pieces'},
                    {id: 24, name: 'Glitter Ribbon (gold)', stock: 12, unit: 'meters'},
                    {id: 25, name: 'Glitter Ribbon (silver)', stock: 10, unit: 'meters'},
                    {id: 26, name: '2 cm satin ribbon (red)', stock: 25, unit: 'meters'},
                    {id: 27, name: '2 cm satin ribbon (blue)', stock: 20, unit: 'meters'},
                    {id: 28, name: '2 cm satin ribbon (white)', stock: 18, unit: 'meters'},
                    {id: 29, name: '2.5 cm satin ribbon (pink)', stock: 15, unit: 'meters'},
                    {id: 30, name: '4cm satin ribbon (green)', stock: 12, unit: 'meters'},
                    {id: 31, name: 'Metal heart shape stick', stock: 8, unit: 'pieces'},
                    {id: 32, name: 'Quick dry floral supply (black)', stock: 6, unit: 'pieces'}
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
    window.updateCompositionName = updateCompositionName;
    window.updateCompositionDropdowns = updateCompositionDropdowns;

    // Add event listener for category changes
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('product_category');
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                updateCompositionDropdowns();
            });
        }

        // Add composition row functionality
        const addCompositionBtn = document.getElementById('add-composition');
        if (addCompositionBtn) {
            addCompositionBtn.addEventListener('click', function() {
                const container = document.getElementById('composition-container');
                const newRow = document.createElement('div');
                newRow.className = 'composition-row row mb-2';
                newRow.innerHTML = `
                    <div class="col-4">
                        <select class="form-select composition-select" name="compositions[${compositionIndex}][component_id]" id="composition-select-${compositionIndex}" onchange="updateCompositionName(${compositionIndex})">
                            <option value="">Select Material...</option>
                        </select>
                        <input type="hidden" class="composition-component-name" name="compositions[${compositionIndex}][component_name]">
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control" name="compositions[${compositionIndex}][quantity]" placeholder="Qty" min="1" required>
                    </div>
                    <div class="col-3">
                        <select class="form-select" name="compositions[${compositionIndex}][unit]" required>
                            <option value="">Unit</option>
                            <option value="pieces">Pieces</option>
                            <option value="stems">Stems</option>
                            <option value="bunches">Bunches</option>
                            <option value="grams">Grams</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-composition">×</button>
                    </div>
                `;
                container.appendChild(newRow);
                
                // Populate the new select with current category items
                updateCompositionDropdowns();
                
                compositionIndex++;
            });
        }

        // Remove composition row functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-composition')) {
                e.target.closest('.composition-row').remove();
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/products/index.blade.php ENDPATH**/ ?>