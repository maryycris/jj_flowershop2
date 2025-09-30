
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

/* Searchable dropdown styles */
.searchable-dropdown {
    position: relative;
}

.dropdown-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    display: none;
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    max-height: 200px;
    overflow-y: auto;
}

.dropdown-options.show {
    display: block !important;
}

.dropdown-option {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.dropdown-option:hover {
    background-color: #f8f9fa;
}

.dropdown-option:last-child {
    border-bottom: none;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="background: #f6faf6; min-height: 100vh;">
    <!-- Promoted Products Carousel -->
    <div class="mx-auto mb-4" style="max-width: 1000px;">
        <div class="bg-white rounded-4 shadow-sm p-2 position-relative">
            <div id="promotedCarousel" class="carousel slide" data-bs-ride="carousel">
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="prev" style="left: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-left" style="font-size: 2rem;"></i></button>
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="next" style="right: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-right" style="font-size: 2rem;"></i></button>
                <div class="carousel-inner">
                    <?php $banners = \App\Models\PromotedBanner::active()->take(5)->get(); ?>
                    <?php $__empty_1 = true; $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="carousel-item <?php if($i === 0): ?> active <?php endif; ?> text-center">
                        <img src="<?php echo e(asset('storage/' . $b->image)); ?>" alt="Banner" style="height: 180px; object-fit: cover; border-radius: 6px; width:100%;">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="carousel-item active text-center">
                        <div style="height: 180px;"></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <!-- Product Tabs -->
    <div class="mx-auto mb-3" style="max-width: 1000px; background: transparent;">
        <ul class="nav nav-tabs justify-content-center" style="background: transparent;">
            <li class="nav-item">
                <a class="nav-link <?php if(!request('category')): ?> active <?php endif; ?>" href="<?php echo e(url('/clerk/product_catalog')); ?>" style="background: transparent;">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if(request('category')==='Bouquets'): ?> active <?php endif; ?>" href="<?php echo e(url('/clerk/product_catalog')); ?>?category=Bouquets" style="background: transparent;">Bouquets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if(request('category')==='Packages'): ?> active <?php endif; ?>" href="<?php echo e(url('/clerk/product_catalog')); ?>?category=Packages" style="background: transparent;">Packages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if(request('category')==='Gifts'): ?> active <?php endif; ?>" href="<?php echo e(url('/clerk/product_catalog')); ?>?category=Gifts" style="background: transparent;">Gifts</a>
            </li>
        </ul>
    </div>

    <!-- Product Grid Card -->
    <div class="mx-auto" style="max-width: 1000px;">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="mb-3 fw-bold fs-5 d-flex justify-content-between align-items-center">
                <span>
                    <?php if(request('category')): ?>
                        <?php echo e(request('category')); ?> Products
                    <?php else: ?>
                        All Products
                    <?php endif; ?>
                </span>
                <span class="text-muted small">
                    Showing <?php echo e($products->count()); ?> product(s)
                </span>
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
                                <option value="">Select Category...</option>
                                <option value="Bouquets">Bouquets</option>
                                <option value="Packages">Packages</option>
                                <option value="Gifts">Gifts</option>
                            </select>
                        </div>
                        
                        <!-- Product Composition Section -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-success" id="add-composition">
                                <i class="fas fa-plus"></i> Add Category
                            </button>
                            <div id="composition-container" style="display: none;">
                                <label class="form-label mt-3">Product Composition (Materials Needed)</label>
                            </div>
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
        background: transparent !important;
    }
    .nav-tabs .nav-link {
        border: none !important;
        color: #8ACB88 !important;
        font-weight: 500;
        background: transparent !important;
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
    });

    // Custom Searchable Dropdown Functions
    function initializeSearchableDropdown(index) {
        const searchInput = document.getElementById(`composition-search-${index}`);
        const optionsContainer = document.getElementById(`composition-options-${index}`);
        const hiddenSelect = document.getElementById(`composition-select-${index}`);
        const hiddenName = document.querySelector(`#composition-select-${index}`).parentElement.querySelector('.composition-component-name');
        
        if (!searchInput || !optionsContainer || !hiddenSelect || !hiddenName) {
            console.log('Missing elements for searchable dropdown:', {searchInput, optionsContainer, hiddenSelect, hiddenName});
            return;
        }
        
        // Show/hide dropdown on focus/blur
        searchInput.addEventListener('focus', () => {
            optionsContainer.classList.add('show');
            refreshSearchableDropdown(index);
        });
        
        searchInput.addEventListener('blur', (e) => {
            // Delay hiding to allow option clicks
            setTimeout(() => {
                optionsContainer.classList.remove('show');
            }, 200);
        });
        
        // Search functionality
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const options = optionsContainer.querySelectorAll('.dropdown-option');
            
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });
        
        // Option selection
        optionsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('dropdown-option')) {
                const value = e.target.getAttribute('data-value');
                const name = e.target.getAttribute('data-name');
                
                searchInput.value = e.target.textContent;
                hiddenSelect.value = value;
                hiddenName.value = name;
                
                optionsContainer.classList.remove('show');
                
                // Trigger change event
                updateCompositionName(index);
            }
        });
    }
    
    // Load inventory categories for material selection
    async function loadInventoryCategories() {
        try {
            console.log('Fetching categories from /clerk/api/categories');
            const response = await fetch('/clerk/api/categories');
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const categories = await response.json();
            console.log('Fetched categories:', categories);
            return categories;
        } catch (error) {
            console.error('Error loading inventory categories:', error);
            return [];
        }
    }
    
    // Load inventory items by category
    async function loadInventoryByCategory(category) {
        try {
            const url = category ? `/clerk/api/inventory/${encodeURIComponent(category)}` : '/clerk/api/inventory';
            console.log('Fetching inventory items from:', url);
            const response = await fetch(url);
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const items = await response.json();
            console.log('Fetched items:', items);
            return items;
        } catch (error) {
            console.error('Error loading inventory items:', error);
            return [];
        }
    }
    
    async function refreshSearchableDropdown(index) {
        const optionsContainer = document.getElementById(`composition-options-${index}`);
        const categorySelect = document.getElementById(`composition-category-${index}`);
        const category = categorySelect.value;
        
        // Clear existing options
        if (optionsContainer) {
            optionsContainer.innerHTML = '';
        }
        
        if (!category) {
            return;
        }
        
        // Load materials for selected category from inventory API
        try {
            const categoryItems = await loadInventoryByCategory(category);
            
            // Add new options
            if (optionsContainer) {
                categoryItems.forEach(item => {
                    const option = document.createElement('div');
                    option.className = 'dropdown-option';
                    option.setAttribute('data-value', item.id);
                    option.setAttribute('data-name', item.name);
                    option.textContent = item.name + ' (Stock: ' + item.stock + ')';
                    optionsContainer.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading inventory items for category:', category, error);
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

    // Function to update materials when category is selected in composition rows
    async function updateCompositionMaterials(index) {
        console.log('updateCompositionMaterials called for index:', index);
        const categorySelect = document.getElementById('composition-category-' + index);
        const materialSelect = document.getElementById('composition-select-' + index);
        const searchInput = document.getElementById('composition-search-' + index);
        const optionsContainer = document.getElementById('composition-options-' + index);
        const category = categorySelect.value;
        
        console.log('Category selected:', category);
        console.log('Elements found:', {
            categorySelect: !!categorySelect,
            materialSelect: !!materialSelect,
            searchInput: !!searchInput,
            optionsContainer: !!optionsContainer
        });
        
        // Clear existing options
        if (materialSelect) {
            materialSelect.innerHTML = '<option value="">Select Material...</option>';
        }
        if (optionsContainer) {
            optionsContainer.innerHTML = '';
        }
        if (searchInput) {
            searchInput.value = '';
        }
        
        if (!category) {
            console.log('No category selected');
            return;
        }
        
        // Load materials for selected category from inventory API
        try {
            console.log('Loading materials for category:', category);
            const categoryItems = await loadInventoryByCategory(category);
            console.log('Loaded items:', categoryItems);
            
            // Add new options to select dropdown
            if (materialSelect) {
                console.log('Adding options to select dropdown...');
                categoryItems.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.setAttribute('data-name', item.name);
                    option.setAttribute('data-stock', item.stock);
                    option.textContent = item.name + ' (Stock: ' + item.stock + ')';
                    materialSelect.appendChild(option);
                });
                console.log('Select dropdown now has', materialSelect.options.length, 'options');
            }
            
            // Add new options to searchable dropdown
            if (optionsContainer) {
                console.log('Adding options to searchable dropdown...');
                categoryItems.forEach(item => {
                    const option = document.createElement('div');
                    option.className = 'dropdown-option';
                    option.setAttribute('data-value', item.id);
                    option.setAttribute('data-name', item.name);
                    option.textContent = item.name + ' (Stock: ' + item.stock + ')';
                    optionsContainer.appendChild(option);
                });
                console.log('Searchable dropdown now has', optionsContainer.children.length, 'options');
            }
            
            console.log('Materials loaded successfully');
        } catch (error) {
            console.error('Error loading materials for category:', category, error);
        }
    }


    // Product Composition Management for Clerk
    let compositionIndex = 1;
    
    // Custom Searchable Dropdown Functions
    function initializeSearchableDropdown(index) {
        const searchInput = document.getElementById(`composition-search-${index}`);
        const optionsContainer = document.getElementById(`composition-options-${index}`);
        const hiddenSelect = document.getElementById(`composition-select-${index}`);
        const hiddenName = document.querySelector(`#composition-select-${index}`).parentElement.querySelector('.composition-component-name');
        
        if (!searchInput || !optionsContainer || !hiddenSelect || !hiddenName) {
            console.log('Missing elements for searchable dropdown:', {searchInput, optionsContainer, hiddenSelect, hiddenName});
            return;
        }
        
        // Show/hide dropdown on focus/blur
        searchInput.addEventListener('focus', () => {
            optionsContainer.classList.add('show');
            refreshSearchableDropdown(index);
        });
        
        searchInput.addEventListener('blur', (e) => {
            // Delay hiding to allow option clicks
            setTimeout(() => {
                optionsContainer.classList.remove('show');
            }, 200);
        });
        
        // Search functionality
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const options = optionsContainer.querySelectorAll('.dropdown-option');
            
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });
        
        // Option selection
        optionsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('dropdown-option')) {
                const value = e.target.getAttribute('data-value');
                const name = e.target.getAttribute('data-name');
                
                searchInput.value = e.target.textContent;
                hiddenSelect.value = value;
                hiddenName.value = name;
                
                optionsContainer.classList.remove('show');
                
                // Trigger change event
                updateCompositionName(index);
            }
        });
    }
    
    // Load inventory categories for material selection
    async function loadInventoryCategories() {
        try {
            console.log('Fetching categories from /clerk/api/categories');
            const response = await fetch('/clerk/api/categories');
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const categories = await response.json();
            console.log('Fetched categories:', categories);
            return categories;
        } catch (error) {
            console.error('Error loading inventory categories:', error);
            return [];
        }
    }
    
    // Load inventory items by category
    async function loadInventoryByCategory(category) {
        try {
            const url = category ? `/clerk/api/inventory/${encodeURIComponent(category)}` : '/clerk/api/inventory';
            console.log('Fetching inventory items from:', url);
            const response = await fetch(url);
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const items = await response.json();
            console.log('Fetched items:', items);
            return items;
        } catch (error) {
            console.error('Error loading inventory items:', error);
            return [];
        }
    }
    
    async function refreshSearchableDropdown(index) {
        const optionsContainer = document.getElementById(`composition-options-${index}`);
        const categorySelect = document.getElementById(`composition-category-${index}`);
        const category = categorySelect.value;
        
        // Clear existing options
        if (optionsContainer) {
            optionsContainer.innerHTML = '';
        }
        
        if (!category) {
            return;
        }
        
        // Load materials for selected category from inventory API
        try {
            const categoryItems = await loadInventoryByCategory(category);
            
            // Add new options
            if (optionsContainer) {
                categoryItems.forEach(item => {
                    const option = document.createElement('div');
                    option.className = 'dropdown-option';
                    option.setAttribute('data-value', item.id);
                    option.setAttribute('data-name', item.name);
                    option.textContent = item.name + ' (Stock: ' + item.stock + ')';
                    optionsContainer.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading inventory items for category:', category, error);
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

    // Function to update materials when category is selected in composition rows
    async function updateCompositionMaterials(index) {
        console.log('updateCompositionMaterials called for index:', index);
        const categorySelect = document.getElementById('composition-category-' + index);
        const materialSelect = document.getElementById('composition-select-' + index);
        const searchInput = document.getElementById('composition-search-' + index);
        const optionsContainer = document.getElementById('composition-options-' + index);
        const category = categorySelect.value;
        
        console.log('Category selected:', category);
        console.log('Elements found:', {
            categorySelect: !!categorySelect,
            materialSelect: !!materialSelect,
            searchInput: !!searchInput,
            optionsContainer: !!optionsContainer
        });
        
        // Clear existing options
        if (materialSelect) {
            materialSelect.innerHTML = '<option value="">Select Material...</option>';
        }
        if (optionsContainer) {
            optionsContainer.innerHTML = '';
        }
        if (searchInput) {
            searchInput.value = '';
        }
        
        if (!category) {
            console.log('No category selected');
            return;
        }
        
        // Load materials for selected category from inventory API
        try {
            console.log('Loading materials for category:', category);
            const categoryItems = await loadInventoryByCategory(category);
            console.log('Loaded items:', categoryItems);
            
            // Add new options to select dropdown
            if (materialSelect) {
                console.log('Adding options to select dropdown...');
                categoryItems.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.setAttribute('data-name', item.name);
                    option.setAttribute('data-stock', item.stock);
                    option.textContent = item.name + ' (Stock: ' + item.stock + ')';
                    materialSelect.appendChild(option);
                });
                console.log('Select dropdown now has', materialSelect.options.length, 'options');
            }
            
            // Add new options to searchable dropdown
            if (optionsContainer) {
                console.log('Adding options to searchable dropdown...');
                categoryItems.forEach(item => {
                    const option = document.createElement('div');
                    option.className = 'dropdown-option';
                    option.setAttribute('data-value', item.id);
                    option.setAttribute('data-name', item.name);
                    option.textContent = item.name + ' (Stock: ' + item.stock + ')';
                    optionsContainer.appendChild(option);
                });
                console.log('Searchable dropdown now has', optionsContainer.children.length, 'options');
            }
            
            console.log('Materials loaded successfully');
        } catch (error) {
            console.error('Error loading materials for category:', category, error);
        }
    }

    // Make functions global
    window.updateCompositionName = updateCompositionName;
    window.updateCompositionMaterials = updateCompositionMaterials;
    window.loadInventoryCategories = loadInventoryCategories;
    window.loadInventoryByCategory = loadInventoryByCategory;

    // Add composition functionality
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, looking for add-composition button');
        const addButton = document.getElementById('add-composition');
        console.log('Add button found:', !!addButton);
        if (addButton) {
            addButton.addEventListener('click', async function() {
        console.log('Add composition button clicked');
        const container = document.getElementById('composition-container');
        
        // Show the composition container if it's hidden
        if (container && container.style.display === 'none') {
            container.style.display = 'block';
        }
        
        const newRow = document.createElement('div');
        newRow.className = 'composition-row row mb-2';
        
        // Load categories dynamically from inventory for material selection
        console.log('Loading inventory categories...');
        let categoryOptions = '<option value="">Select Category...</option>';
        try {
            const categories = await loadInventoryCategories();
            console.log('Loaded categories:', categories);
            categories.forEach(category => {
                categoryOptions += `<option value="${category}">${category}</option>`;
            });
            console.log('Category options created:', categoryOptions);
        } catch (error) {
            console.error('Error loading categories:', error);
        }
        
        newRow.innerHTML = `
            <div class="col-4">
                <select class="form-select composition-category mb-2" name="compositions[${compositionIndex}][category]" id="composition-category-${compositionIndex}" onchange="updateCompositionMaterials(${compositionIndex})">
                    ${categoryOptions}
                </select>
                <div class="searchable-dropdown">
                    <input type="text" class="form-control composition-search" id="composition-search-${compositionIndex}" placeholder="Search materials..." autocomplete="off">
                    <select class="form-select composition-select" name="compositions[${compositionIndex}][component_id]" id="composition-select-${compositionIndex}" style="display: none;">
                        <option value="">Select Material...</option>
                    </select>
                    <input type="hidden" class="composition-component-name" name="compositions[${compositionIndex}][component_name]">
                    <div class="dropdown-options" id="composition-options-${compositionIndex}">
                        <!-- Options will be populated dynamically -->
                    </div>
                </div>
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
        
        // Initialize searchable dropdown functionality
        console.log('Initializing searchable dropdown for index:', compositionIndex);
        initializeSearchableDropdown(compositionIndex);
        
        compositionIndex++;
            });
        }
    });

    // Remove composition row functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-composition')) {
            e.target.closest('.composition-row').remove();
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/product_catalog/index.blade.php ENDPATH**/ ?>