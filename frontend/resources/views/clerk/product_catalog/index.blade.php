@extends('layouts.clerk_app')
@push('styles')
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
</style>
@endpush
@section('content')
<div class="container-fluid py-2" style="background: #f6faf6; min-height: 100vh;">
    <!-- Promoted Banner Carousel (only shows if banners are uploaded) -->
    @php $banners = \App\Models\PromotedBanner::active()->take(5)->get(); @endphp
    @if($banners->count() > 0)
    <div class="mx-auto mb-2" style="max-width: 1000px;">
        <div class="bg-white rounded-4 shadow-sm p-2 position-relative">
            <div id="promotedCarousel" class="carousel slide" data-bs-ride="carousel">
                @if($banners->count() > 1)
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="prev" style="left: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-left" style="font-size: 2rem;"></i></button>
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="next" style="right: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-right" style="font-size: 2rem;"></i></button>
                @endif
                <div class="carousel-inner">
                    @foreach($banners as $i => $b)
                    <div class="carousel-item @if($i === 0) active @endif text-center">
                        <img src="{{ asset('storage/' . $b->image) }}" alt="{{ $b->title ?? 'Banner' }}" style="height: 180px; object-fit: cover; border-radius: 6px; width:100%;">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

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

    <!-- Product Tabs -->
    <div class="mx-auto mb-3" style="max-width: 1000px; background: transparent;">
        <ul class="nav nav-tabs justify-content-center" style="background: transparent;">
            <li class="nav-item">
                <a class="nav-link @if(!request('category')) active @endif" href="{{ url('/clerk/product_catalog') }}" style="background: transparent;">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(request('category')==='Bouquets') active @endif" href="{{ url('/clerk/product_catalog') }}?category=Bouquets" style="background: transparent;">Bouquets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(request('category')==='Packages') active @endif" href="{{ url('/clerk/product_catalog') }}?category=Packages" style="background: transparent;">Packages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(request('category')==='Gifts') active @endif" href="{{ url('/clerk/product_catalog') }}?category=Gifts" style="background: transparent;">Gifts</a>
            </li>
        </ul>
    </div>

    <!-- Product Grid Card -->
    <div class="mx-auto" style="max-width: 1000px;">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="mb-3 fw-bold fs-5 d-flex justify-content-between align-items-center">
                <span>
                    @if(request('category'))
                        {{ request('category') }} Products
                    @else
                        All Products
                    @endif
                </span>
                <span class="text-muted small">
                    Showing {{ $products->count() }} product(s)
                </span>
            </div>
            <div class="row g-3 product-grid">
                <!-- Add New Product Card -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card add-new-product-card h-100 d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus fa-3x text-muted"></i>
                    </div>
                </div>
                @forelse($products as $product)
                @php
                    $isOutOfStock = isset($productAvailability[$product->id]) && !$productAvailability[$product->id]['can_fulfill'];
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100" data-product-id="{{ $product->id }}" style="{{ $isOutOfStock ? 'opacity: 0.6;' : '' }}">
                        <div style="position: relative;">
                            <img src="{{ $product->image_url ?? '/images/logo.png' }}" class="card-img-top product-image" alt="{{ $product->name }}" style="{{ $isOutOfStock ? 'filter: grayscale(50%);' : '' }}" onerror="this.onerror=null; this.src='/images/logo.png';">
                            @if($isOutOfStock)
                                <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                    <span class="badge bg-danger" style="font-size: 0.7rem;">OUT OF STOCK</span>
                                </div>
                            @endif
                        </div>
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">{{ $product->name }}</h6>
                            <p class="card-text product-price">₱{{ number_format($product->price, 2) }}</p>
                            @if($isOutOfStock)
                                <small class="text-muted" style="font-size: 0.7rem;">Insufficient materials</small>
                            @endif
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button class="btn btn-sm action-btn edit-btn edit-product-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editProductModal" data-product='{{ json_encode($product) }}'><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-sm action-btn delete-btn" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteProductModal" data-product='{{ json_encode($product) }}'><i class="bi bi-trash3"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-center">No products found.</p>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clerk.product_catalog.store') }}" method="POST" enctype="multipart/form-data">
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
        background: #7bb47b;
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
                <form id="editProductForm" method="POST" enctype="multipart/form-data">
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
                            <div id="edit-composition-container" style="display: none;">
                                <label class="form-label mt-3">Product Composition (Materials Needed)</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_product_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_product_description" name="description" rows="3" placeholder="Product description..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_reason" class="form-label">Reason for Change <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_reason" name="reason" rows="2" placeholder="Please explain why you want to make these changes..." required></textarea>
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

    <!-- Product Info Modal -->
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

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteProductModalLabel">Delete Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
                <form id="deleteProductForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This action will submit a deletion request for admin approval. The product will not be deleted immediately.
                        </div>
                        <div class="mb-3">
                            <label for="delete_product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="delete_product_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="delete_reason" class="form-label">Reason for Deletion <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="delete_reason" name="reason" rows="3" placeholder="Please explain why you want to delete this product..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Submit Deletion Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Search Functionality -->
<script>
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
        // Remove live typing search - only search on Enter key press
        // clearTimeout(searchInput.__t);
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
});
</script>
@endsection 

@push('styles')
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

    /* Ensure buttons are evenly spaced and fill the column */
    .d-flex.justify-content-center.gap-2 {
        width: 100%;
        max-width: 120px;
        margin: 0 auto;
        gap: 8px !important;
    }

    /* Make sure both buttons have exactly the same width */
    .edit-btn, .delete-btn {
        width: 50px !important;
        flex: 1 1 50px;
    }
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
@endpush

@push('scripts')
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
            form.action = '{{ route("clerk.product_catalog.update", ":id") }}'.replace(':id', product.id);

            // Refresh CSRF token when modal opens
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                var csrfInput = form.querySelector('input[name="_token"]');
                if (csrfInput) {
                    csrfInput.value = csrfMeta.getAttribute('content');
                }
            }

            editProductModal.querySelector('#edit_product_name').value = product.name;
            editProductModal.querySelector('#edit_product_price').value = product.price;
            editProductModal.querySelector('#edit_product_category').value = product.category;
            editProductModal.querySelector('#edit_product_description').value = product.description || '';

            // show current image if any
            var currentImg = document.getElementById('edit_current_image');
            if (product.image) {
                currentImg.src = '{{ asset('storage') }}' + '/' + product.image;
                currentImg.style.display = 'block';
            } else {
                currentImg.src = '';
                currentImg.style.display = 'none';
            }

            // Load current compositions
            loadCurrentCompositions(product.id);
        });

        // Handle edit product form submission with CSRF token refresh
        var editProductForm = document.getElementById('editProductForm');
        if (editProductForm) {
            editProductForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get fresh CSRF token from meta tag
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
                
                // Update CSRF token in form
                var csrfInput = editProductForm.querySelector('input[name="_token"]');
                if (csrfInput && csrfToken) {
                    csrfInput.value = csrfToken;
                }
                
                // Create FormData
                var formData = new FormData(editProductForm);
                
                // Ensure CSRF token is in FormData
                if (csrfToken) {
                    formData.set('_token', csrfToken);
                }
                
                // Add _method for PUT
                formData.set('_method', 'PUT');
                
                // Submit via fetch with proper headers
                fetch(editProductForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (response.status === 419) {
                        // CSRF token expired, refresh page to get new token
                        window.location.reload();
                        return;
                    }
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json().catch(() => response.text());
                })
                .then(data => {
                    if (typeof data === 'object' && data.success) {
                        // Close modal
                        var modal = bootstrap.Modal.getInstance(editProductModal);
                        if (modal) modal.hide();
                        
                        // Show success message
                        alert('Product change request submitted for admin approval!');
                        
                        // Reload page to show updated data
                        window.location.reload();
                    } else if (typeof data === 'object' && data.message) {
                        alert(data.message);
                    } else {
                        // Success - reload page
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the form. Please try again.');
                });
            });
        }

        // Delete Product Modal population
        var deleteProductModal = document.getElementById('deleteProductModal');
        deleteProductModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var product = JSON.parse(button.getAttribute('data-product'));

            var form = deleteProductModal.querySelector('#deleteProductForm');
            form.action = '{{ route("clerk.product_catalog.destroy", ":id") }}'.replace(':id', product.id);

            // Refresh CSRF token when modal opens
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                var csrfInput = form.querySelector('input[name="_token"]');
                if (csrfInput) {
                    csrfInput.value = csrfMeta.getAttribute('content');
                }
            }

            deleteProductModal.querySelector('#delete_product_name').value = product.name;
            deleteProductModal.querySelector('#delete_reason').value = '';
        });

        // Handle delete product form submission with CSRF token refresh
        var deleteProductForm = document.getElementById('deleteProductForm');
        if (deleteProductForm) {
            deleteProductForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get fresh CSRF token from meta tag
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
                
                // Update CSRF token in form
                var csrfInput = deleteProductForm.querySelector('input[name="_token"]');
                if (csrfInput && csrfToken) {
                    csrfInput.value = csrfToken;
                }
                
                // Create FormData
                var formData = new FormData(deleteProductForm);
                
                // Ensure CSRF token is in FormData
                if (csrfToken) {
                    formData.set('_token', csrfToken);
                }
                
                // Add _method for DELETE
                formData.set('_method', 'DELETE');
                
                // Submit via fetch with proper headers
                fetch(deleteProductForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (response.status === 419) {
                        // CSRF token expired, refresh page to get new token
                        window.location.reload();
                        return;
                    }
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json().catch(() => response.text());
                })
                .then(data => {
                    if (typeof data === 'object' && data.success) {
                        // Close modal
                        var modal = bootstrap.Modal.getInstance(deleteProductModal);
                        if (modal) modal.hide();
                        
                        // Show success message
                        alert('Product deletion request submitted for admin approval!');
                        
                        // Reload page to show updated data
                        window.location.reload();
                    } else if (typeof data === 'object' && data.message) {
                        alert(data.message);
                    } else {
                        // Success - reload page
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the form. Please try again.');
                });
            });
        }

    });

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


    // Product Composition Management for Clerk
    let compositionIndex = 1;
    
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

    // Load current compositions for edit modal
    async function loadCurrentCompositions(productId) {
        try {
            const response = await fetch(`/clerk/api/products/${productId}/compositions`);
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
        console.log('Initializing searchable dropdown for index:', compositionIndex);
        initializeSearchableDropdown(compositionIndex);
        
        compositionIndex++;
            });
        }
        
        // Edit composition functionality
        const editAddButton = document.getElementById('edit-add-composition');
        console.log('Edit add button found:', !!editAddButton);
        if (editAddButton) {
            editAddButton.addEventListener('click', async function() {
                console.log('Edit add composition button clicked');
                const container = document.getElementById('edit-composition-container');
                
                // Show the composition container if it's hidden
                if (container && container.style.display === 'none') {
                    container.style.display = 'block';
                }
                
                // Use the existing addEditCompositionRow function to maintain consistency
                await addEditCompositionRow(null, compositionIndex);
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

    // Card click to open product info (ignore clicks on buttons/links)
    document.addEventListener('click', function(e) {
        const card = e.target.closest('.product-card');
        if (!card) return;
        // If clicking inside actionable controls, do nothing
        if (e.target.closest('button, a, form, input, select, textarea, label')) return;
        const productId = card.getAttribute('data-product-id');
        if (productId) {
            showProductInfo(productId);
        }
    });

    // Show product info function
    async function showProductInfo(productId) {
        try {
            const response = await fetch(`/clerk/api/products/${productId}/details`);
            const result = await response.json();
            
            if (result.success) {
                const product = result.product;
                const modal = new bootstrap.Modal(document.getElementById('productInfoModal'));
                
                // Populate modal content
                document.getElementById('productInfoContent').innerHTML = `
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
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error loading product info:', error);
            alert('Error loading product details');
        }
    }
</script>
@endpush
