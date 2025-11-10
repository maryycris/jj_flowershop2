@php $hideSidebar = true; @endphp
@extends('layouts.admin_app')

@push('styles')
<style>
.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-icon i {
    font-size: 14px;
}

/* Action Buttons Styling */
.action-btn {
    width: 50px;
    height: 40px;
    border: none;
    background: transparent;
    color: #4CAF50;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    padding: 0;
    font-size: 16px;
    flex: 1;
    min-width: 50px;
    max-width: 50px;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.action-btn i {
    transition: color 0.3s ease;
}

/* Edit Button */
.edit-btn:hover {
    background-color: #007bff;
    color: white;
}

.edit-btn:hover i {
    color: white;
}

/* Delete Button */
.delete-btn:hover {
    background-color: #dc3545;
    color: white;
}

.delete-btn:hover i {
    color: white;
}

/* Approve Button */
.approve-btn:hover {
    background-color: #28a745;
    color: white;
}

.approve-btn:hover i {
    color: white;
}

/* Review Button */
.review-btn:hover {
    background-color: #ffc107;
    color: white;
}

.review-btn:hover i {
    color: white;
}

/* Ensure buttons are evenly spaced and fill the column */
.d-flex.justify-content-center.gap-2 {
    width: 100%;
    max-width: 180px;
    margin: 0 auto;
    gap: 8px !important;
}

/* Make sure all buttons have exactly the same width */
.edit-btn, .delete-btn, .approve-btn, .review-btn {
    width: 50px !important;
    flex: 1 1 50px;
}

/* Search bar styling */
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

/* Product Change Cards Styling */
.product-card[data-change-id] {
    transition: all 0.3s ease;
    cursor: pointer;
}

.product-card[data-change-id]:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.product-card[data-change-id] .badge {
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Action badge positioning */
.product-card .position-absolute .badge {
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Delete overlay styling */
.product-card .position-absolute .fa-trash {
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
}

/* Modal styling */
#productChangeDetailsContent .table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

#productChangeDetailsContent .table td {
    vertical-align: middle;
}

/* Badge colors for different actions */
.badge.bg-primary {
    background-color: #007bff !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}
</style>
@endpush

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
                        $banners = \App\Models\PromotedBanner::active()->orderBy('sort_order')->get();
                    @endphp
                    @forelse($banners as $i => $b)
                    <div class="carousel-item @if($i === 0) active @endif text-center">
                        <img src="{{ $b->image_url }}" alt="{{ $b->title ?? 'Banner' }}" style="height: 180px; object-fit: cover; border-radius: 6px; width:100%;" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                    </div>
                    @empty
                    <div class="carousel-item active text-center">
                        <div style="height: 180px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                            <div class="text-center">
                                <i class="bi bi-image" style="font-size: 2rem; opacity: 0.5;"></i>
                                <p class="mt-2 mb-0" style="font-size: 0.9rem;">No active banners. Click to add banners.</p>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

<!-- Product Info Modal (for approved products) -->
<div class="modal fade" id="productInfoModal" tabindex="-1" aria-labelledby="productInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="productInfoModalLabel">Product Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productInfoContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                        <input id="productSearchInput" type="text" class="form-control" placeholder="Search products..." aria-label="Search" value="{{ request('search', '') }}">
                        <button id="productFilterBtn" class="btn btn-outline-success" type="button" title="Filter"><i class="bi bi-funnel"></i></button>
                    </div>
                </div>
            </div>
            <!-- Advanced Filter Panel -->
            <div id="productFilterPanel" class="card p-3 mt-2" style="display:none;">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1">Min Price</label>
                        <input id="productFilterMin" type="number" min="0" class="form-control form-control-sm" placeholder="0" value="{{ request('min_price', '') }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1">Max Price</label>
                        <input id="productFilterMax" type="number" min="0" class="form-control form-control-sm" placeholder="9999" value="{{ request('max_price', '') }}">
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
                <span class="badge bg-success text-dark ms-2" id="pendingCount">0</span>
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
            
            <!-- Pagination -->
            <div id="paginationContainer">
                @if($products->hasPages())
                    <x-pagination 
                        :currentPage="$products->currentPage()" 
                        :totalPages="$products->lastPage()" 
                        :baseUrl="request()->url()" 
                        :queryParams="request()->query()" 
                    />
                @endif
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
                        <img src="{{ $banner->image_url }}" class="img-fluid rounded" style="height: 80px; object-fit: cover; width: 100%;" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
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
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" onsubmit="handleAddProductForm(event)">
                @csrf
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
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

    <style>
    /* Add Product Modal scrollbar styling */
    #addProductModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    #addProductModal .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    #addProductModal .modal-body::-webkit-scrollbar-thumb {
        background: #7bb47b;
        border-radius: 3px;
    }
    #addProductModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #5aa65a;
    }
    </style>

    <!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProductForm" method="POST" enctype="multipart/form-data" onsubmit="handleEditProductForm(event, currentEditProductId)">
                @csrf
                @method('PUT')
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
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
                            <option value="">Select Category...</option>
                            <option value="Bouquets">Bouquets</option>
                            <option value="Packages">Packages</option>
                            <option value="Gifts">Gifts</option>
                        </select>
                    </div>
                    
                    <!-- Product Composition Section -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-success" id="edit-add-composition">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                        <div id="edit-composition-container">
                            <label class="form-label mt-3">Product Composition (Materials Needed)</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_product_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_product_description" name="description" rows="3" placeholder="Product description..."></textarea>
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

    <!-- Product Change Details Modal -->
    <div class="modal fade" id="productChangeDetailsModal" tabindex="-1" aria-labelledby="productChangeDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white" style="padding: 0.75rem 1rem;">
                    <h5 class="modal-title mb-0" id="productChangeDetailsModalLabel" style="font-size: 1rem;">Product Change Request Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="productChangeDetailsContent" style="padding: 1rem;">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 0.75rem 1rem;">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">Close</button>
                    <button type="button" class="btn btn-sm btn-success" id="approveFromModal" style="font-size: 0.85rem;">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" id="rejectFromModal" style="font-size: 0.85rem;">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- Search Functionality -->
<script>
// Store current active category tab
let currentActiveCategory = null;
let currentEditProductId = null;

// Initialize category tab state preservation
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a saved category state
    const savedCategory = sessionStorage.getItem('activeProductCategory');
    
    if (savedCategory) {
        // Activate the saved category tab
        const categoryLink = document.querySelector(`a[href="?category=${savedCategory}"]`);
        if (categoryLink) {
            categoryLink.click();
            currentActiveCategory = savedCategory;
        }
        // Clear the saved category state
        sessionStorage.removeItem('activeProductCategory');
    } else {
        // Store the initially active category
        const currentUrl = new URL(window.location.href);
        currentActiveCategory = currentUrl.searchParams.get('category') || 'all';
    }
    
    // Listen for category tab changes
    const categoryLinks = document.querySelectorAll('.category-tab-link');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function() {
            const url = new URL(this.href);
            currentActiveCategory = url.searchParams.get('category') || 'all';
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearchInput');
    const filterBtn = document.getElementById('productFilterBtn');
    const filterPanel = document.getElementById('productFilterPanel');
    const filterApply = document.getElementById('productFilterApply');
    const filterClear = document.getElementById('productFilterClear');
    const filterMin = document.getElementById('productFilterMin');
    const filterMax = document.getElementById('productFilterMax');

    function performSearch() {
        const searchTerm = searchInput ? searchInput.value : '';
        // preserve current category from URL (default to 'all')
        const currentUrl = new URL(window.location.href);
        const category = (currentUrl.searchParams.get('category') || 'all');
        const minPrice = filterMin && filterMin.value ? filterMin.value : '';
        const maxPrice = filterMax && filterMax.value ? filterMax.value : '';

        // Build URL with search parameters
        const url = new URL(window.location.href);
        url.searchParams.set('search', searchTerm);
        url.searchParams.set('category', category);
        if (minPrice) url.searchParams.set('min_price', minPrice);
        if (maxPrice) url.searchParams.set('max_price', maxPrice);

        // Redirect to the same page with search parameters
        window.location.href = url.toString();
    }

    function clearFilters() {
        if (searchInput) searchInput.value = '';
        if (filterMin) filterMin.value = '';
        if (filterMax) filterMax.value = '';
        
        // Redirect to clean URL
        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        url.searchParams.delete('min_price');
        url.searchParams.delete('max_price');
        window.location.href = url.toString();
    }

    // Event listeners
    if (filterBtn && filterPanel) {
        filterBtn.addEventListener('click', function() {
            filterPanel.style.display = (filterPanel.style.display === 'none' || !filterPanel.style.display) ? 'block' : 'none';
        });
        
        // Close filter panel when clicking outside
        document.addEventListener('click', function(e){
            if (filterPanel.style.display === 'block') {
                const within = filterPanel.contains(e.target) || filterBtn.contains(e.target);
                if (!within) filterPanel.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        // Remove the live typing search - only search on Enter key press
        // searchInput.addEventListener('input', function(){
        //     clearTimeout(searchInput.__t);
        //     searchInput.__t = setTimeout(performSearch, 400);
        // });
    }

    if (filterApply) {
        filterApply.addEventListener('click', function(){
            performSearch();
            if (filterPanel) filterPanel.style.display = 'none';
        });
    }

    if (filterClear) {
        filterClear.addEventListener('click', clearFilters);
    }

    // Product Change Details Modal Event Listeners
    document.getElementById('approveFromModal').addEventListener('click', function() {
        if (currentChangeId) {
            approveProductChange(currentChangeId);
            bootstrap.Modal.getInstance(document.getElementById('productChangeDetailsModal')).hide();
        }
    });

    document.getElementById('rejectFromModal').addEventListener('click', function() {
        if (currentChangeId) {
            rejectProductChange(currentChangeId);
            bootstrap.Modal.getInstance(document.getElementById('productChangeDetailsModal')).hide();
        }
    });

    // Add click event listener for product change cards
    document.addEventListener('click', function(e) {
        const productCard = e.target.closest('.product-card[data-change-id]');
        if (productCard && !e.target.closest('button')) {
            const changeId = productCard.getAttribute('data-change-id');
            if (changeId) {
                viewProductChangeDetails(changeId);
            }
        }
    });
});

// AJAX function to handle add product form submission
async function handleAddProductForm(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
            modal.hide();
            
            // Show success message
            showAlert(result.message, 'success');
            
            // Reload the page to show the new product and preserve category state
            setTimeout(() => {
                if (currentActiveCategory) {
                    sessionStorage.setItem('activeProductCategory', currentActiveCategory);
                }
                location.reload();
            }, 1000);
        } else {
            showAlert(result.message || 'An error occurred', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('An error occurred while adding the product', 'error');
    }
}

// AJAX function to handle edit product form submission
async function handleEditProductForm(event, productId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch(`/admin/products/${productId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
            modal.hide();
            
            // Show success message
            showAlert(result.message, 'success');
            
            // Reload the page to show the updated product and preserve category state
            setTimeout(() => {
                if (currentActiveCategory) {
                    sessionStorage.setItem('activeProductCategory', currentActiveCategory);
                }
                location.reload();
            }, 1000);
        } else {
            showAlert(result.message || 'An error occurred', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('An error occurred while updating the product', 'error');
    }
}

// AJAX function to handle delete product
async function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product and its images?')) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/products/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Show success message
            showAlert(result.message, 'success');
            
            // Reload the page to remove the deleted product and preserve category state
            setTimeout(() => {
                if (currentActiveCategory) {
                    sessionStorage.setItem('activeProductCategory', currentActiveCategory);
                }
                location.reload();
            }, 1000);
        } else {
            showAlert(result.message || 'An error occurred', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('An error occurred while deleting the product', 'error');
    }
}

// Function to show alerts
function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Pending Product Changes Functions
async function approveProductChange(changeId) {
    if (!confirm('Are you sure you want to approve this product change?')) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/api/product-changes/${changeId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                admin_notes: ''
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
            // Reload the page to refresh all content
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Error approving change: ' + error.message, 'error');
    }
}

async function rejectProductChange(changeId) {
    if (!confirm('Are you sure you want to reject this product change?')) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/api/product-changes/${changeId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                admin_notes: ''
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
            // Reload the page to refresh all content
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Error rejecting change: ' + error.message, 'error');
    }
}

let currentChangeId = null;

async function viewProductChangeDetails(changeId) {
    currentChangeId = changeId;
    
    try {
        const response = await fetch(`/admin/api/product-changes/${changeId}/details`);
        const result = await response.json();
        
        if (result.success) {
            const change = result.change;
            const modal = new bootstrap.Modal(document.getElementById('productChangeDetailsModal'));
            const content = document.getElementById('productChangeDetailsContent');
            
            // Build the detailed content
            let html = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            <img src="${change.product.image_url || '/storage/' + (change.product.image || 'images/logo.png')}" class="img-fluid rounded" alt="${change.product.name}" style="max-height: 200px;" onerror="this.onerror=null; this.src='/images/logo.png';">
                        </div>
                        <div class="text-center">
                            <span class="badge ${change.action === 'edit' ? 'bg-primary' : 'bg-danger'} fs-6">
                                ${change.action === 'edit' ? 'EDIT REQUEST' : 'DELETE REQUEST'}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5 class="mb-3">${change.product.name}</h5>
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Current Price:</strong> ₱${parseFloat(change.product.price).toFixed(2)}
                            </div>
                            <div class="col-sm-6">
                                <strong>Category:</strong> ${change.product.category}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Requested by:</strong> ${change.requested_by.name}
                            </div>
                            <div class="col-sm-6">
                                <strong>Date:</strong> ${new Date(change.created_at).toLocaleString()}
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong>Reason:</strong>
                            <p class="text-muted mt-1">${change.reason}</p>
                        </div>
            `;
            
            if (change.action === 'edit' && change.changes) {
                html += `
                    <hr class="my-2">
                    <h6 class="mb-2" style="font-size: 0.9rem; font-weight: 600;">Proposed Changes:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-2" style="font-size: 0.75rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding: 0.25rem 0.5rem;">Field</th>
                                    <th style="padding: 0.25rem 0.5rem;">Current</th>
                                    <th style="padding: 0.25rem 0.5rem;">New</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                Object.entries(change.changes).forEach(([key, value]) => {
                    if (key !== 'compositions' && key !== 'image') {
                        const currentValue = change.product[key] || 'N/A';
                        const fieldName = key.charAt(0).toUpperCase() + key.slice(1).replace('_', ' ');
                        html += `
                            <tr>
                                <td style="padding: 0.25rem 0.5rem;"><strong>${fieldName}</strong></td>
                                <td style="padding: 0.25rem 0.5rem;">${currentValue}</td>
                                <td style="padding: 0.25rem 0.5rem;" class="text-primary"><strong>${value}</strong></td>
                            </tr>
                        `;
                    }
                });
                
                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                
                // Handle image changes
                if (change.changes.image) {
                    html += `
                        <div class="mb-2" style="font-size: 0.85rem;">
                            <strong>New Image:</strong>
                            <div class="mt-1 text-center">
                                <img src="/storage/${change.changes.image}" class="img-fluid rounded" alt="New Image" style="max-height: 100px; max-width: 120px;">
                            </div>
                        </div>
                    `;
                }
                
                // Handle composition changes
                if (change.changes.compositions) {
                    html += `
                        <hr class="my-2">
                        <h6 class="mb-2" style="font-size: 0.9rem; font-weight: 600;">New Compositions:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-2" style="font-size: 0.75rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="padding: 0.25rem 0.5rem;">Component</th>
                                        <th style="padding: 0.25rem 0.5rem;">Category</th>
                                        <th style="padding: 0.25rem 0.5rem;">Qty</th>
                                        <th style="padding: 0.25rem 0.5rem;">Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    change.changes.compositions.forEach(comp => {
                        html += `
                            <tr>
                                <td style="padding: 0.25rem 0.5rem;">${comp.component_name}</td>
                                <td style="padding: 0.25rem 0.5rem;">${comp.category || 'N/A'}</td>
                                <td style="padding: 0.25rem 0.5rem;">${comp.quantity}</td>
                                <td style="padding: 0.25rem 0.5rem;">${comp.unit}</td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }
            }
            
            html += `
                </div>
            `;
            
            content.innerHTML = html;
            modal.show();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Error fetching details: ' + error.message, 'error');
    }
}
</script>
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
        z-index: 9999;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
                            showAlert('Error deleting banner', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Error deleting banner', 'error');
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
        editProductModal.addEventListener('show.bs.modal', async function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var product = JSON.parse(button.getAttribute('data-product'));

            // Set the current edit product ID for AJAX handling
            currentEditProductId = product.id;

            var form = editProductModal.querySelector('#editProductForm');
            form.action = '/admin/products/' + product.id; // Set the form action dynamically

            editProductModal.querySelector('#edit_product_name').value = product.name;
            editProductModal.querySelector('#edit_product_price').value = product.price;
            editProductModal.querySelector('#edit_product_category').value = product.category;
            editProductModal.querySelector('#edit_product_description').value = product.description || '';

            // show current image if any
            var currentImg = document.getElementById('edit_current_image');
            if (product.image) {
                // Use image_url if available (Cloudinary), otherwise construct local path
                currentImg.src = product.image_url || ('{{ asset('storage') }}' + '/' + product.image);
                currentImg.style.display = 'block';
            } else {
                currentImg.src = '';
                currentImg.style.display = 'none';
            }

            // Load current compositions
            await loadCurrentCompositions(product.id);
        });

        // Image replacement functionality for edit modal
        const editProductImageInput = document.getElementById('edit_product_image');
        const editCurrentImage = document.getElementById('edit_current_image');
        
        if (editProductImageInput) {
            editProductImageInput.addEventListener('change', function(event) {
                if (event.target.files && event.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editCurrentImage.src = e.target.result;
                        editCurrentImage.style.display = 'block';
                    };
                    reader.readAsDataURL(event.target.files[0]);
                }
            });
        }

        // Composition fields management (Create modal uses its own index)
        let createCompositionIndex = 1;
        
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
                // Fallback to hardcoded inventory categories if API fails
                const fallbackCategories = [
                    'Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 
                    'Floral Supplies', 'Packaging Materials', 'Wrappers', 'Ribbon', 'Greenery', 'Other Offers'
                ];
                fallbackCategories.forEach(category => {
                    categoryOptions += `<option value="${category}">${category}</option>`;
                });
            }
            
            newRow.innerHTML = `
                <div class="col-4">
                    <select class="form-select composition-category mb-2" name="compositions[${createCompositionIndex}][category]" id="composition-category-${createCompositionIndex}" onchange="updateCompositionMaterials(${createCompositionIndex})">
                        ${categoryOptions}
                    </select>
                    <div class="searchable-dropdown">
                        <input type="text" class="form-control composition-search" id="composition-search-${createCompositionIndex}" placeholder="Search materials..." autocomplete="off">
                        <select class="form-select composition-select" name="compositions[${createCompositionIndex}][component_id]" id="composition-select-${createCompositionIndex}" style="display: none;">
                            <option value="">Select Material...</option>
                        </select>
                        <input type="hidden" class="composition-component-name" name="compositions[${createCompositionIndex}][component_name]">
                        <div class="dropdown-options" id="composition-options-${createCompositionIndex}">
                            <!-- Options will be populated dynamically -->
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <input type="number" class="form-control" name="compositions[${createCompositionIndex}][quantity]" placeholder="Qty" min="1" required>
                </div>
                <div class="col-3">
                    <select class="form-select" name="compositions[${createCompositionIndex}][unit]" required>
                        <option value="">Unit</option>
                        <option value="pieces">Pieces</option>
                        <option value="stems">Stems</option>
                        <option value="bunches">Bunches</option>
                        <option value="grams">Grams</option>
                        <option value="meters">Meters</option>
                        <option value="rolls">Rolls</option>
                        <option value="sheets">Sheets</option>
                        <option value="boxes">Boxes</option>
                    </select>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-composition">×</button>
                </div>
            `;
            container.appendChild(newRow);
            
            // Initialize searchable dropdown functionality
            console.log('Initializing searchable dropdown for create index:', createCompositionIndex);
            initializeSearchableDropdown(createCompositionIndex);
            
            createCompositionIndex++;
        });

        // Edit composition functionality
        const editAddButton = document.getElementById('edit-add-composition');
        let editCompositionIndex = 1000; // separate namespace to avoid clashes with create modal rows
        if (editAddButton) {
            editAddButton.addEventListener('click', async function() {
                console.log('Edit add composition button clicked');
                const container = document.getElementById('edit-composition-container');
                
                // Show the composition container if it's hidden
                if (container && container.style.display === 'none') {
                    container.style.display = 'block';
                }
                
                // Use the existing addEditCompositionRow function to maintain consistency
                await addEditCompositionRow(null, editCompositionIndex);
                editCompositionIndex++;
            });
        }

        // Remove composition row
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-composition')) {
                e.target.closest('.composition-row').remove();
            }
        });

    });

    // Product Composition Management for Admin
    let compositionIndex = 1; // used by Add Product modal only
    
    // Custom Searchable Dropdown Functions
    function initializeSearchableDropdown(index) {
        // Try both regular and edit modal element IDs
        let searchInput = document.getElementById(`composition-search-${index}`);
        let optionsContainer = document.getElementById(`composition-options-${index}`);
        let hiddenSelect = document.getElementById(`composition-select-${index}`);
        let hiddenName = null;
        
        // If not found, try edit modal IDs
        if (!searchInput) {
            searchInput = document.getElementById(`edit-composition-search-${index}`);
        }
        if (!optionsContainer) {
            optionsContainer = document.getElementById(`edit-composition-options-${index}`);
        }
        if (!hiddenSelect) {
            hiddenSelect = document.getElementById(`edit-composition-select-${index}`);
        }
        
        // Get the hidden name input
        if (hiddenSelect) {
            hiddenName = hiddenSelect.parentElement.querySelector('.composition-component-name');
        }
        
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
            // Check if the blur is caused by clicking on an option
            const relatedTarget = e.relatedTarget;
            if (relatedTarget && relatedTarget.classList.contains('dropdown-option')) {
                return; // Don't hide if clicking on an option
            }
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
        
        // Prevent dropdown from closing when clicking inside it
        optionsContainer.addEventListener('mousedown', (e) => {
            e.preventDefault();
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
        let optionsContainer = document.getElementById(`composition-options-${index}`);
        let categorySelect = document.getElementById(`composition-category-${index}`);
        
        // If not found, try edit modal IDs
        if (!optionsContainer) {
            optionsContainer = document.getElementById(`edit-composition-options-${index}`);
        }
        if (!categorySelect) {
            categorySelect = document.getElementById(`edit-composition-category-${index}`);
        }
        
        const category = categorySelect ? categorySelect.value : '';
        
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
        let select = document.getElementById('composition-select-' + index);
        let nameInput = null;
        
        // If not found, try edit modal ID
        if (!select) {
            select = document.getElementById('edit-composition-select-' + index);
        }
        
        if (select) {
            nameInput = select.parentElement.querySelector('.composition-component-name');
        }
        
        if (select && nameInput) {
            if (select.value) {
                const selectedOption = select.options[select.selectedIndex];
                nameInput.value = selectedOption.getAttribute('data-name');
                console.log('Selected material:', selectedOption.getAttribute('data-name'));
            } else {
                nameInput.value = '';
            }
        }
    }


    // Function to update materials when category is selected in composition rows
    async function updateCompositionMaterials(index) {
        console.log('updateCompositionMaterials called for index:', index);
        
        // Try both regular and edit modal element IDs
        let categorySelect = document.getElementById('composition-category-' + index);
        let materialSelect = document.getElementById('composition-select-' + index);
        let searchInput = document.getElementById('composition-search-' + index);
        let optionsContainer = document.getElementById('composition-options-' + index);
        
        // If not found, try edit modal IDs
        if (!categorySelect) {
            categorySelect = document.getElementById('edit-composition-category-' + index);
        }
        if (!materialSelect) {
            materialSelect = document.getElementById('edit-composition-select-' + index);
        }
        if (!searchInput) {
            searchInput = document.getElementById('edit-composition-search-' + index);
        }
        if (!optionsContainer) {
            optionsContainer = document.getElementById('edit-composition-options-' + index);
        }
        
        const category = categorySelect ? categorySelect.value : '';
        
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

    // Highlight a product or ensure pending section visible when coming from notifications
    (function () {
        const url = new URL(window.location.href);
        const highlight = url.searchParams.get('highlight');
        const productIdToHighlight = url.searchParams.get('product_id');
        if (highlight === 'pending') {
            // After pending products load, try to scroll to and outline the specific product
            const tryHighlight = () => {
                const card = document.querySelector(`[data-product-id='${productIdToHighlight}']`)?.closest('.card');
                if (card) {
                    card.classList.add('border', 'border-3', 'border-warning');
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            };
            // Run after initial loadPendingProducts completes
            setTimeout(tryHighlight, 1000);
        }
    })();

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
            
            // Load pending products as cards with ADD REQUEST badge
            const grid = document.getElementById('pendingProductsGrid');
            const count = document.getElementById('pendingCount');
            
            // Also load pending changes
            let changes = [];
            try {
                const changesResponse = await fetch('/admin/api/product-changes/pending', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                if (changesResponse.ok) {
                    changes = await changesResponse.json();
                }
            } catch (error) {
                console.log('No pending changes found or error loading changes:', error);
            }
            
            if (products.length === 0 && changes.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center py-4"><p class="text-muted">No pending products to approve.</p></div>';
                if (count) count.textContent = '0';
                return;
            }
            
            // Create product cards for new product approvals (ADD)
            const productCards = products.map(product => `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100 position-relative" data-product-id="${product.id}" style="border: 2px solid #ffc107;">
                        <!-- Action Badge -->
                        <div class="position-absolute" style="top: 8px; right: 8px; z-index: 10;">
                            <span class="badge bg-warning text-dark" style="font-size: 0.6rem; padding: 0.25rem 0.5rem;">
                                ADD
                            </span>
                        </div>
                        
                        <!-- Product Image -->
                        <img src="${product.image_url || '/storage/' + (product.image || 'images/logo.png')}" class="card-img-top product-image" alt="${product.name}" style="height: 120px; object-fit: cover;" onerror="this.onerror=null; this.src='/images/logo.png';">
                        
                        <!-- Product Info -->
                        <div class="card-body text-center p-2">
                            <h6 class="card-title mb-1" style="font-size: 0.9rem;">${product.name}</h6>
                            <p class="card-text product-price mb-1" style="font-size: 0.8rem;">₱${parseFloat(product.price).toFixed(2)}</p>
                            <p class="text-muted small mb-2" style="font-size: 0.7rem;">
                                <strong>Category:</strong> ${product.category}
                            </p>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-center gap-1 mt-1">
                                <button class="btn btn-success btn-sm approve-product-btn" title="Approve" data-product-id="${product.id}" style="padding: 0.25rem 0.4rem; font-size: 0.7rem;">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <button class="btn btn-info btn-sm review-product-btn" title="Review" data-product-id="${product.id}" style="padding: 0.25rem 0.4rem; font-size: 0.7rem;">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-danger btn-sm disapprove-product-btn" title="Delete" data-product-id="${product.id}" style="padding: 0.25rem 0.4rem; font-size: 0.7rem;">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Create product cards for pending changes (EDIT/DELETE)
            const changeCards = changes.map(change => `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100 position-relative" data-change-id="${change.id}" style="border: 2px solid ${change.action === 'edit' ? '#007bff' : '#dc3545'};">
                        <!-- Action Badge -->
                        <div class="position-absolute" style="top: 8px; right: 8px; z-index: 10;">
                            <span class="badge ${change.action === 'edit' ? 'bg-primary' : 'bg-danger'}" style="font-size: 0.6rem; padding: 0.25rem 0.5rem;">
                                ${change.action === 'edit' ? 'EDIT' : 'DELETE'}
                            </span>
                        </div>
                        
                        <!-- Product Image -->
                        <div style="position: relative;">
                            <img src="${change.product.image_url || '/storage/' + (change.product.image || 'images/logo.png')}" class="card-img-top product-image" alt="${change.product.name}" style="height: 120px; object-fit: cover;" onerror="this.onerror=null; this.src='/images/logo.png';">
                            ${change.action === 'delete' ? '<div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: rgba(220, 53, 69, 0.3); display: flex; align-items: center; justify-content: center;"><i class="fas fa-trash fa-2x text-white"></i></div>' : ''}
                        </div>
                        
                        <!-- Product Info -->
                        <div class="card-body text-center p-2">
                            <h6 class="card-title mb-1" style="font-size: 0.9rem;">${change.product.name}</h6>
                            <p class="card-text product-price mb-1" style="font-size: 0.8rem;">₱${parseFloat(change.product.price).toFixed(2)}</p>
                            <p class="text-muted small mb-1" style="font-size: 0.7rem;">
                                <strong>By:</strong> ${change.requested_by.name}
                            </p>
                            <p class="text-muted small mb-2" style="font-size: 0.7rem;">
                                ${new Date(change.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}
                            </p>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-center gap-1 mt-1">
                                <button class="btn btn-success btn-sm" onclick="approveProductChange(${change.id})" title="Approve" style="padding: 0.25rem 0.4rem; font-size: 0.7rem;">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="rejectProductChange(${change.id})" title="Reject" style="padding: 0.25rem 0.4rem; font-size: 0.7rem;">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <button class="btn btn-info btn-sm" onclick="viewProductChangeDetails(${change.id})" title="View Details" style="padding: 0.25rem 0.4rem; font-size: 0.7rem;">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Combine all cards in the same grid
            grid.innerHTML = changeCards + productCards;
            
            if (count) count.textContent = products.length + changes.length;
            
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
            
            const data = await response.json();
            const products = data.products || data;
            const productAvailability = data.productAvailability || {};
            console.log('Approved products loaded:', products);
            console.log('Product availability loaded:', productAvailability);
            
            const grid = document.getElementById('approvedProductsGrid');
            
            // Add Product card HTML
            const addProductCard = `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card add-new-product-card h-100 d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus fa-3x text-muted"></i>
                    </div>
                </div>
            `;
            
            grid.innerHTML = addProductCard + products.map(product => {
                const isOutOfStock = productAvailability && productAvailability[product.id] && !productAvailability[product.id].can_fulfill;
                return `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100" data-product-id="${product.id}" style="${isOutOfStock ? 'opacity: 0.6;' : ''}">
                        <div style="position: relative;">
                            <img src="${product.image_url || '/storage/' + (product.image || 'images/logo.png')}" class="card-img-top product-image" alt="${product.name}" style="${isOutOfStock ? 'filter: grayscale(50%);' : ''}" onerror="this.onerror=null; this.src='/images/logo.png';">
                            ${isOutOfStock ? '<div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;"><span class="badge bg-danger" style="font-size: 0.7rem;">OUT OF STOCK</span></div>' : ''}
                        </div>
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">${product.name}</h6>
                            <p class="card-text product-price">₱${parseFloat(product.price).toFixed(2)}</p>
                            ${isOutOfStock ? '<small class="text-muted" style="font-size: 0.7rem;">Insufficient materials</small>' : ''}
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button class="btn btn-sm action-btn edit-btn edit-product-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editProductModal" data-product='${JSON.stringify(product)}'><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-sm action-btn delete-btn" title="Delete" onclick="deleteProduct(${product.id})"><i class="bi bi-trash3"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
            
        } catch (error) {
            console.error('Error loading approved products:', error);
            document.getElementById('approvedProductsGrid').innerHTML = '<div class="col-12 text-center py-4"><p class="text-danger">Error loading products: ' + error.message + '</p></div>';
        }
    }

    // Setup approval event listeners
    function setupApprovalEventListeners() {
        // Card click to open modal (review for pending, product info for approved)
        document.addEventListener('click', function(e) {
            const card = e.target.closest('.product-card');
            if (!card) return;
            // If clicking inside actionable controls, do nothing
            if (e.target.closest('button, a, form, input, select, textarea, label')) return;
            const productId = card.getAttribute('data-product-id');
            if (productId) {
                // Determine which grid the click came from
                const inPending = !!card.closest('#pendingProductsGrid');
                if (inPending) {
                    reviewProduct(productId);
                } else {
                    showProductInfo(productId);
                }
            }
        });

        // Approve product
        document.addEventListener('click', async function(e) {
            const approveBtn = e.target.closest('.approve-product-btn');
            if (approveBtn) {
                const productId = approveBtn.getAttribute('data-product-id');
                await approveProduct(productId);
            }
        });

        // Disapprove product
        document.addEventListener('click', async function(e) {
            const disapproveBtn = e.target.closest('.disapprove-product-btn');
            if (disapproveBtn) {
                const productId = disapproveBtn.getAttribute('data-product-id');
                if (confirm('Are you sure you want to disapprove and delete this product?')) {
                    await disapproveProduct(productId);
                }
            }
        });

        // Review product
        document.addEventListener('click', async function(e) {
            const reviewBtn = e.target.closest('.review-product-btn');
            if (reviewBtn) {
                const productId = reviewBtn.getAttribute('data-product-id');
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
                showAlert('Product approved successfully!', 'success');
                // Reload the page to refresh all content
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error approving product:', error);
            showAlert('Error approving product', 'error');
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
                showAlert('Product disapproved and deleted successfully!', 'success');
                // Reload the page to refresh all content
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error disapproving product:', error);
            showAlert('Error disapproving product', 'error');
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
                            <img src="${product.image_url || '/storage/' + (product.image || 'images/logo.png')}" class="img-fluid rounded" alt="${product.name}" onerror="this.onerror=null; this.src='/images/logo.png';">
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
                                            <th>Category</th>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${product.compositions.map(comp => `
                                            <tr>
                                                <td>${comp.category || 'N/A'}</td>
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
                showAlert('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error reviewing product:', error);
            showAlert('Error loading product details', 'error');
        }
    }

    // Show product info (approved products)
    async function showProductInfo(productId) {
        try {
            const response = await fetch(`/admin/api/products/${productId}/details`);
            const result = await response.json();
            
            if (result.success) {
                const product = result.product;
                const modal = new bootstrap.Modal(document.getElementById('productInfoModal'));
                
                // Populate modal content
                document.getElementById('productInfoContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-4">
                            <img src="${product.image_url || '/storage/' + (product.image || 'images/logo.png')}" class="img-fluid rounded" alt="${product.name}" onerror="this.onerror=null; this.src='/images/logo.png';">
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
                                            <th>Category</th>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${product.compositions.map(comp => `
                                            <tr>
                                                <td>${comp.category || 'N/A'}</td>
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
                showAlert('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error loading product info:', error);
            showAlert('Error loading product details', 'error');
        }
    }

    // Load current compositions for edit modal
    async function loadCurrentCompositions(productId) {
        try {
            const response = await fetch(`/admin/api/products/${productId}/compositions`);
            const result = await response.json();
            
            const container = document.getElementById('edit-composition-container');
            container.innerHTML = '';
            
            if (result.success && result.compositions && result.compositions.length > 0) {
                container.style.display = 'block';
                container.innerHTML = '<label class="form-label mt-3">Product Composition (Materials Needed)</label>';
                
                // Reset the global composition index to start after existing compositions
                compositionIndex = result.compositions.length;
                
                let index = 0;
                for (const comp of result.compositions) {
                    await addEditCompositionRow(comp, index);
                    index++;
                }
            } else {
                container.style.display = 'none';
                // Reset composition index if no existing compositions
                compositionIndex = 0;
            }
        } catch (error) {
            console.error('Error loading compositions:', error);
            const container = document.getElementById('edit-composition-container');
            container.style.display = 'none';
            // Reset composition index on error
            compositionIndex = 0;
        }
    }

    // Add composition row for edit modal
    async function addEditCompositionRow(composition = null, index = 0) {
        const container = document.getElementById('edit-composition-container');
        
        const newRow = document.createElement('div');
        newRow.className = 'composition-row row mb-2';
        
        // Load categories dynamically from inventory for material selection
        let categoryOptions = '<option value="">Select Category...</option>';
        try {
            const categories = await loadInventoryCategories();
            categories.forEach(category => {
                categoryOptions += `<option value="${category}">${category}</option>`;
            });
        } catch (error) {
            console.error('Error loading categories for edit:', error);
            // Fallback to hardcoded inventory categories if API fails
            const fallbackCategories = [
                'Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 
                'Floral Supplies', 'Packaging Materials', 'Wrappers', 'Ribbon', 'Greenery', 'Other Offers'
            ];
            fallbackCategories.forEach(category => {
                categoryOptions += `<option value="${category}">${category}</option>`;
            });
        }
        
        newRow.innerHTML = `
            <div class="col-4">
                <select class="form-select composition-category mb-2" name="compositions[${index}][category]" id="edit-composition-category-${index}" onchange="updateCompositionMaterials(${index})">
                    ${categoryOptions}
                </select>
                <div class="searchable-dropdown">
                    <input type="text" class="form-control composition-search" id="edit-composition-search-${index}" placeholder="Search materials..." autocomplete="off">
                    <select class="form-select composition-select" name="compositions[${index}][component_id]" id="edit-composition-select-${index}" style="display: none;">
                        <option value="">Select Material...</option>
                    </select>
                    <input type="hidden" class="composition-component-name" name="compositions[${index}][component_name]">
                    <div class="dropdown-options" id="edit-composition-options-${index}">
                        <!-- Options will be populated dynamically -->
                    </div>
                </div>
            </div>
            <div class="col-3">
                <input type="number" class="form-control" name="compositions[${index}][quantity]" placeholder="Qty" min="1" required value="${composition ? composition.quantity : ''}">
            </div>
            <div class="col-3">
                <select class="form-select" name="compositions[${index}][unit]" required>
                    <option value="">Unit</option>
                    <option value="pieces" ${composition && composition.unit === 'pieces' ? 'selected' : ''}>Pieces</option>
                    <option value="stems" ${composition && composition.unit === 'stems' ? 'selected' : ''}>Stems</option>
                    <option value="bunches" ${composition && composition.unit === 'bunches' ? 'selected' : ''}>Bunches</option>
                    <option value="grams" ${composition && composition.unit === 'grams' ? 'selected' : ''}>Grams</option>
                    <option value="meters" ${composition && composition.unit === 'meters' ? 'selected' : ''}>Meters</option>
                    <option value="rolls" ${composition && composition.unit === 'rolls' ? 'selected' : ''}>Rolls</option>
                    <option value="sheets" ${composition && composition.unit === 'sheets' ? 'selected' : ''}>Sheets</option>
                    <option value="boxes" ${composition && composition.unit === 'boxes' ? 'selected' : ''}>Boxes</option>
                </select>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-sm btn-outline-danger remove-composition">×</button>
            </div>
        `;
        container.appendChild(newRow);
        
        // Initialize searchable dropdown functionality
        initializeSearchableDropdown(index);
        
        // If we have composition data, populate the fields
        if (composition) {
            // Wait a moment for DOM to be ready
            await new Promise(resolve => setTimeout(resolve, 10));
            
            // Set category first - this must be done before loading materials
            const categorySelect = document.getElementById(`edit-composition-category-${index}`);
            if (categorySelect && composition.category) {
                categorySelect.value = composition.category;
                
                // Trigger change event to ensure proper initialization
                categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
                
                // Load materials after category is set
                await updateCompositionMaterials(index);
                
                // Set component name and ID after materials are loaded
                const componentNameInput = document.querySelector(`#edit-composition-select-${index}`).parentElement.querySelector('.composition-component-name');
                if (componentNameInput) {
                    componentNameInput.value = composition.component_name || '';
                }
                const componentSelect = document.getElementById(`edit-composition-select-${index}`);
                if (componentSelect) {
                    componentSelect.value = composition.component_id || '';
                }
                
                // Set the search input value to show the selected material
                const searchInput = document.getElementById(`edit-composition-search-${index}`);
                if (searchInput) {
                    searchInput.value = composition.component_name || '';
                }
            } else if (categorySelect) {
                // If no category, still load materials (for backward compatibility)
                await updateCompositionMaterials(index);
                
                const componentNameInput = document.querySelector(`#edit-composition-select-${index}`).parentElement.querySelector('.composition-component-name');
                if (componentNameInput) {
                    componentNameInput.value = composition.component_name || '';
                }
                const componentSelect = document.getElementById(`edit-composition-select-${index}`);
                if (componentSelect) {
                    componentSelect.value = composition.component_id || '';
                }
                
                const searchInput = document.getElementById(`edit-composition-search-${index}`);
                if (searchInput) {
                    searchInput.value = composition.component_name || '';
                }
            }
        }
    }

</script>
@endpush
