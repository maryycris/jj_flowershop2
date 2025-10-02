@php $hideSidebar = true; @endphp
@extends('layouts.admin_app')

@section('admin_content')
<div class="container-fluid py-4" style="background: #f6faf6; min-height: 100vh;">
    <!-- Promoted Banners Carousel (editable in Admin > Promoted Banners) -->
    <div class="mx-auto mb-4" style="max-width: 1000px;">
        <div id="promotedCard" class="bg-white rounded-4 shadow-sm p-2 position-relative promoted-clickable">
            <div id="promotedCarousel" class="carousel slide" data-bs-ride="carousel">
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="prev" style="left: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-left" style="font-size: 2rem;"></i></button>
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="next" style="right: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-right" style="font-size: 2rem;"></i></button>
                <div class="carousel-inner">
                    @php
                        $banners = \App\Models\PromotedBanner::orderBy('sort_order')->get();
                    @endphp
                    @forelse($banners as $i => $b)
                    <div class="carousel-item @if($i === 0) active @endif text-center">
                        <img src="{{ asset('storage/' . $b->image) }}" alt="{{ $b->title ?? 'Banner' }}" style="height: 180px; object-fit: cover; border-radius: 6px; width:100%;">
                    </div>
                    @empty
                    <div class="carousel-item active text-center">
                        <div style="height: 180px;"></div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="mx-auto" style="max-width: 1000px;">
        <ul class="nav nav-tabs border-0 justify-content-center category-tabs mb-2" id="productTabs" role="tablist" style="background: transparent; border-radius: 8px 8px 0 0; box-shadow: none;">
            @php
                $categories = ['all' => 'All', 'bouquets' => 'Bouquets', 'packages' => 'Packages', 'gifts' => 'Gifts'];
                $currentCategory = $categories[request('category', 'all')] ?? 'All';
            @endphp
            @foreach($categories as $key => $label)
            <li class="nav-item" role="presentation">
                <a class="nav-link category-tab-link @if(request('category', 'all') === $key) active @endif" href="?category={{ $key }}">{{ $label }}</a>
            </li>
            @endforeach
        </ul>

        <!-- Products to Approve Section -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <div class="mb-3 fw-bold fs-5">
                Products to approve
                <span class="badge bg-warning text-dark ms-2" id="pendingCount">0</span>
            </div>
            <div class="row g-3" id="pendingProductsGrid">
                <div class="col-12 text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid Card -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="mb-3 fw-bold fs-5">
                {{ $currentCategory }}
            </div>
            <div class="row g-3 product-grid" id="approvedProductsGrid">
                <div class="col-12 text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Banner Modal (from Products page) -->
<div class="modal fade" id="addBannerFromProductsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Banner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/admin/promoted-banners') }}" method="POST" enctype="multipart/form-data" id="addBannerForm">
                @csrf
                <div class="modal-body">
                    <div class="text-center text-muted mb-2">Maximum of 3 images only</div>
                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-outline-success" id="triggerBannerUpload">
                            <i class="bi bi-upload me-2"></i>Upload image
                        </button>
                        <input type="file" id="bannerImagesInput" name="images[]" accept="image/*" multiple style="display:none;">
                    </div>
                    <div id="bannerPreviews" class="d-flex flex-column gap-2"></div>
                    <div class="mt-3">
                        <textarea class="form-control" name="link_url" placeholder="Optional link URL (applied to all)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Banners Modal -->
<div class="modal fade" id="manageBannersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Manage Banners</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBannerFromProductsModal" data-bs-dismiss="modal">
                        <i class="bi bi-plus me-2"></i>Add New Banner
                    </button>
                </div>
                <div id="existingBanners" class="d-flex flex-column gap-2">
                    @php $banners = \App\Models\PromotedBanner::orderBy('sort_order')->get(); @endphp
                    @forelse($banners as $banner)
                    <div class="position-relative border rounded p-2" data-banner-id="{{ $banner->id }}">
                        <img src="{{ asset('storage/' . $banner->image) }}" class="img-fluid rounded" style="height: 80px; object-fit: cover; width: 100%;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-existing-banner" data-banner-id="{{ $banner->id }}">
                            <i class="bi bi-x"></i>
                        </button>
                        @if($banner->title)
                        <div class="mt-1 small text-muted">{{ $banner->title }}</div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">No banners yet. Add your first banner!</div>
                    @endforelse
                </div>
            </div>
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
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                        <label class="form-label">Product Composition (Materials Needed)</label>
                        <div id="composition-container">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success" id="add-composition">
                            <i class="fas fa-plus"></i> Add Category
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
                @csrf
                @method('PUT')
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
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
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
                @csrf
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

<!-- Product Review Modal -->
<div class="modal fade" id="reviewProductModal" tabindex="-1" aria-labelledby="reviewProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="reviewProductModalLabel">Review Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reviewProductContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="disapproveProductBtn">Disapprove</button>
                <button type="button" class="btn btn-success" id="approveProductBtn">Approve</button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
    body { background: #f6faf6; }
    .promoted-clickable { cursor: pointer; }
    .promoted-clickable:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
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
    
    /* Category Tabs Styling */
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
        background: transparent !important;
        background-color: transparent !important;
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
        background: transparent !important;
        background-color: transparent !important;
    }
    
    /* More specific override for any Bootstrap or global styles */
    .category-tabs .nav-link:hover,
    .category-tabs .nav-link:focus,
    .category-tabs .nav-link:active {
        background: transparent !important;
        background-color: transparent !important;
        box-shadow: none !important;
    }
    
    /* Even more specific targeting */
    ul.nav.category-tabs li.nav-item a.nav-link:hover {
        background: transparent !important;
        background-color: transparent !important;
    }
    
    /* Override any Bootstrap nav-tabs styles */
    .nav-tabs.category-tabs .nav-link:hover {
        background: transparent !important;
        background-color: transparent !important;
    }
    
    /* Override active tab background */
    .nav-tabs.category-tabs .nav-link.active,
    ul.nav.category-tabs li.nav-item a.nav-link.active {
        background: transparent !important;
        background-color: transparent !important;
    }
    .category-tabs {
        border-bottom: none !important;
        padding: 0 1rem;
    }
    
    /* Custom Searchable Dropdown Styling */
    .searchable-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    
    .searchable-dropdown input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        font-size: 1rem;
    }
    
    .searchable-dropdown input:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .searchable-dropdown .dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    
    .searchable-dropdown .dropdown-options.show {
        display: block;
    }
    
    .searchable-dropdown .dropdown-option {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .searchable-dropdown .dropdown-option:hover {
        background-color: #f8f9fa;
    }
    
    .searchable-dropdown .dropdown-option:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Open Manage Banners modal when clicking the promoted card (except arrow buttons)
        const promotedCard = document.getElementById('promotedCard');
        if (promotedCard) {
            promotedCard.addEventListener('click', function(e) {
                const isArrow = e.target.closest('[data-bs-slide]');
                if (isArrow) return; // let carousel arrows work
                const modal = new bootstrap.Modal(document.getElementById('manageBannersModal'));
                modal.show();
            });
        }
        // Add Banner modal logic (multiple images, previews, limit 3)
        const triggerUpload = document.getElementById('triggerBannerUpload');
        const fileInput = document.getElementById('bannerImagesInput');
        const previews = document.getElementById('bannerPreviews');
        if (triggerUpload && fileInput && previews) {
            triggerUpload.addEventListener('click', () => fileInput.click());
            const refreshPreviews = () => {
                previews.innerHTML = '';
                const files = Array.from(fileInput.files || []);
                files.slice(0,3).forEach((file, idx) => {
                    const url = URL.createObjectURL(file);
                    const wrapper = document.createElement('div');
                    wrapper.className = 'position-relative';
                    wrapper.innerHTML = `
                        <img src="${url}" class="img-fluid rounded border" style="width:100%; height: 64px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-banner-img" data-index="${idx}">×</button>
                    `;
                    previews.appendChild(wrapper);
                });
            };
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 3) {
                    // Keep only first 3
                    const dt = new DataTransfer();
                    Array.from(fileInput.files).slice(0,3).forEach(f => dt.items.add(f));
                    fileInput.files = dt.files;
                }
                refreshPreviews();
            });
            previews.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-banner-img')) {
                    const removeIdx = parseInt(e.target.getAttribute('data-index'));
                    const dt = new DataTransfer();
                    Array.from(fileInput.files).forEach((f, i) => { if (i !== removeIdx) dt.items.add(f); });
                    fileInput.files = dt.files;
                    refreshPreviews();
                }
            });
        }
        
        // Handle existing banner deletion
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-existing-banner')) {
                const bannerId = e.target.closest('.remove-existing-banner').getAttribute('data-banner-id');
                if (confirm('Are you sure you want to delete this banner?')) {
                    fetch(`/admin/promoted-banners/${bannerId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove banner from UI
                            e.target.closest('[data-banner-id]').remove();
                            // Refresh the page to update carousel
                            window.location.reload();
                        } else {
                            alert('Error deleting banner');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting banner');
                    });
                }
            }
        });
        
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
        
        document.getElementById('add-composition').addEventListener('click', async function() {
            console.log('Add composition button clicked');
            const container = document.getElementById('composition-container');
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
                currentImagePreview.src = '{{ asset('storage/') }}' + '/' + product.image;
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
    
    // Load categories from API
    async function loadCategories() {
        try {
            // For catalog products, only use Bouquets, Packages, Gifts
            const categories = ['Bouquets', 'Packages', 'Gifts'];
            
            // Update filter dropdown
            const filterCategorySelect = document.getElementById('category');
            if (filterCategorySelect) {
                // Clear existing options except "All Categories"
                filterCategorySelect.innerHTML = '<option value="">All Categories</option>';
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    if (new URLSearchParams(window.location.search).get('category') === category) {
                        option.selected = true;
                    }
                    filterCategorySelect.appendChild(option);
                });
            }
            
            // Update edit product modal category dropdown
            const editCategorySelect = document.getElementById('edit_product_category');
            if (editCategorySelect) {
                editCategorySelect.innerHTML = '';
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    editCategorySelect.appendChild(option);
                });
            }
            
            return categories;
        } catch (error) {
            console.error('Error loading categories:', error);
            return [];
        }
    }
    
    // Load inventory categories for material selection
    async function loadInventoryCategories() {
        try {
            console.log('Fetching categories from /admin/api/categories');
            const response = await fetch('/admin/api/categories');
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
            const url = category ? `/admin/api/inventory/${encodeURIComponent(category)}` : '/admin/api/inventory';
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

    // Initialize page on load
    document.addEventListener('DOMContentLoaded', async function() {
        console.log('Admin products page loaded, initializing...');
        
        // Load categories on page load
        await loadCategories();
        
        // Load pending and approved products
        console.log('Loading products...');
        await loadPendingProducts();
        await loadApprovedProducts();
        
        // Remove composition row functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-composition')) {
                e.target.closest('.composition-row').remove();
            }
        });
        
        // Product approval event listeners
        setupApprovalEventListeners();
        
        console.log('Admin products page initialization complete');
    });

    // Load pending products
    async function loadPendingProducts() {
        try {
            console.log('Loading pending products...');
            console.log('Fetching from URL:', '/admin/api/products/pending');
            const response = await fetch('/admin/api/products/pending', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const products = await response.json();
            console.log('Pending products loaded:', products);
            
            const grid = document.getElementById('pendingProductsGrid');
            const count = document.getElementById('pendingCount');
            
            count.textContent = products.length;
            
            if (products.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center py-4"><p class="text-muted">No products pending approval</p></div>';
                return;
            }
            
            grid.innerHTML = products.map(product => `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <img src="/storage/${product.image}" class="card-img-top product-image" alt="${product.name}">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">${product.name}</h6>
                            <p class="card-text product-price">₱${parseFloat(product.price).toFixed(2)}</p>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button class="btn btn-sm btn-success approve-product-btn" data-product-id="${product.id}">Approve</button>
                                <button class="btn btn-sm btn-warning review-product-btn" data-product-id="${product.id}">Review</button>
                                <button class="btn btn-sm btn-danger disapprove-product-btn" data-product-id="${product.id}">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
        } catch (error) {
            console.error('Error loading pending products:', error);
            document.getElementById('pendingProductsGrid').innerHTML = '<div class="col-12 text-center py-4"><p class="text-danger">Error loading products: ' + error.message + '</p></div>';
        }
    }

    // Load approved products
    async function loadApprovedProducts() {
        try {
            const category = new URLSearchParams(window.location.search).get('category') || 'all';
            console.log('Loading approved products for category:', category);
            const url = `/admin/api/products/approved?category=${category}`;
            console.log('Fetching from URL:', url);
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            console.log('Approved products response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const products = await response.json();
            console.log('Approved products loaded:', products);
            
            const grid = document.getElementById('approvedProductsGrid');
            
            // Add Product card HTML
            const addProductCard = `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card add-new-product-card h-100 d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus fa-3x text-muted"></i>
                    </div>
                </div>
            `;
            
            grid.innerHTML = addProductCard + products.map(product => `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <img src="/storage/${product.image}" class="card-img-top product-image" alt="${product.name}">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">${product.name}</h6>
                            <p class="card-text product-price">₱${parseFloat(product.price).toFixed(2)}</p>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button class="btn btn-sm btn-info edit-product-btn" data-bs-toggle="modal" data-bs-target="#editProductModal" data-product='${JSON.stringify(product)}'>Edit</button>
                                <button class="btn btn-sm btn-warning manage-images-btn" data-bs-toggle="modal" data-bs-target="#manageImagesModal" data-product='${JSON.stringify(product)}'>Images</button>
                                <form action="/admin/products/${product.id}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product and its images?');">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
        } catch (error) {
            console.error('Error loading approved products:', error);
            document.getElementById('approvedProductsGrid').innerHTML = '<div class="col-12 text-center py-4"><p class="text-danger">Error loading products: ' + error.message + '</p></div>';
        }
    }

    // Setup approval event listeners
    function setupApprovalEventListeners() {
        // Approve product
        document.addEventListener('click', async function(e) {
            if (e.target.classList.contains('approve-product-btn')) {
                const productId = e.target.getAttribute('data-product-id');
                await approveProduct(productId);
            }
        });

        // Disapprove product
        document.addEventListener('click', async function(e) {
            if (e.target.classList.contains('disapprove-product-btn')) {
                const productId = e.target.getAttribute('data-product-id');
                if (confirm('Are you sure you want to disapprove and delete this product?')) {
                    await disapproveProduct(productId);
                }
            }
        });

        // Review product
        document.addEventListener('click', async function(e) {
            if (e.target.classList.contains('review-product-btn')) {
                const productId = e.target.getAttribute('data-product-id');
                await reviewProduct(productId);
            }
        });

        // Approve from modal
        document.getElementById('approveProductBtn').addEventListener('click', async function() {
            const productId = this.getAttribute('data-product-id');
            await approveProduct(productId);
        });

        // Disapprove from modal
        document.getElementById('disapproveProductBtn').addEventListener('click', async function() {
            const productId = this.getAttribute('data-product-id');
            if (confirm('Are you sure you want to disapprove and delete this product?')) {
                await disapproveProduct(productId);
            }
        });
    }

    // Approve product
    async function approveProduct(productId) {
        try {
            const response = await fetch(`/admin/api/products/${productId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                alert('Product approved successfully!');
                await loadPendingProducts();
                await loadApprovedProducts();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error approving product:', error);
            alert('Error approving product');
        }
    }

    // Disapprove product
    async function disapproveProduct(productId) {
        try {
            const response = await fetch(`/admin/api/products/${productId}/disapprove`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                alert('Product disapproved and deleted successfully!');
                await loadPendingProducts();
                await loadApprovedProducts();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error disapproving product:', error);
            alert('Error disapproving product');
        }
    }

    // Review product
    async function reviewProduct(productId) {
        try {
            const response = await fetch(`/admin/api/products/${productId}/details`);
            const result = await response.json();
            
            if (result.success) {
                const product = result.product;
                const modal = new bootstrap.Modal(document.getElementById('reviewProductModal'));
                
                // Set product ID for approve/disapprove buttons
                document.getElementById('approveProductBtn').setAttribute('data-product-id', productId);
                document.getElementById('disapproveProductBtn').setAttribute('data-product-id', productId);
                
                // Populate modal content
                document.getElementById('reviewProductContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/storage/${product.image}" class="img-fluid rounded" alt="${product.name}">
                        </div>
                        <div class="col-md-8">
                            <h5>${product.name}</h5>
                            <p class="text-muted">${product.category}</p>
                            <p class="h4 text-success">₱${parseFloat(product.price).toFixed(2)}</p>
                            <p class="mt-3">${product.description || 'No description provided'}</p>
                            
                            <h6 class="mt-4">Product Composition:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${product.compositions.map(comp => `
                                            <tr>
                                                <td>${comp.component_name}</td>
                                                <td>${comp.quantity}</td>
                                                <td>${comp.unit}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                
                modal.show();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error reviewing product:', error);
            alert('Error loading product details');
        }
    }
</script>
@endpush
