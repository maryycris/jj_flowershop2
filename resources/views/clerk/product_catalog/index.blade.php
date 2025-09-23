@extends('layouts.clerk_app')
@section('content')
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
                    @foreach($promotedProducts as $i => $product)
                    <div class="carousel-item @if($i === 0) active @endif text-center">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="height: 180px; object-fit: cover; border-radius: 12px;">
                        @endif
                        <div class="mt-2 fw-bold">{{ $product->name }}</div>
                        <div class="text-success">₱{{ number_format($product->price, 2) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mx-auto mb-3" style="max-width: 900px;">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('clerk.product_catalog.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">All Categories</option>
                            <option value="Fresh Flowers" {{ request('category') == 'Fresh Flowers' ? 'selected' : '' }}>Fresh Flowers</option>
                            <option value="Dried Flowers" {{ request('category') == 'Dried Flowers' ? 'selected' : '' }}>Dried Flowers</option>
                            <option value="Artificial Flowers" {{ request('category') == 'Artificial Flowers' ? 'selected' : '' }}>Artificial Flowers</option>
                            <option value="Floral Supplies" {{ request('category') == 'Floral Supplies' ? 'selected' : '' }}>Floral Supplies</option>
                            <option value="Packaging Materials" {{ request('category') == 'Packaging Materials' ? 'selected' : '' }}>Packaging Materials</option>
                            <option value="Materials, Tools, and Equipment" {{ request('category') == 'Materials, Tools, and Equipment' ? 'selected' : '' }}>Materials, Tools, and Equipment</option>
                            <option value="Office Supplies" {{ request('category') == 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                            <option value="Other Offers" {{ request('category') == 'Other Offers' ? 'selected' : '' }}>Other Offers</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="price_min" class="form-label">Min Price</label>
                        <input type="number" name="price_min" id="price_min" class="form-control" value="{{ request('price_min') }}" placeholder="0.00" step="0.01">
                    </div>
                    <div class="col-md-3">
                        <label for="price_max" class="form-label">Max Price</label>
                        <input type="number" name="price_max" id="price_max" class="form-control" value="{{ request('price_max') }}" placeholder="9999.99" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label for="sort" class="form-label">Sort By</label>
                        <select name="sort" id="sort" class="form-select">
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price Low-High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price High-Low</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Apply Filters
                        </button>
                        <a href="{{ route('clerk.product_catalog.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                        <span class="ms-3 text-muted">
                            Showing {{ $products->count() }} product(s)
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
                @if(request('category'))
                    {{ request('category') }} Products
                @else
                    All Products
                @endif
            </div>
            <div class="row g-3 product-grid">
                <!-- Add New Product Card -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card add-new-product-card h-100 d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus fa-3x text-muted"></i>
                    </div>
                </div>
                @forelse($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/logo.png' }}" class="card-img-top product-image" alt="{{ $product->name }}">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">{{ $product->name }}</h6>
                            <p class="card-text product-price">₱{{ number_format($product->price, 2) }}</p>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button class="btn btn-light btn-icon edit-product-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editProductModal" data-product='{{ json_encode($product) }}'><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-light btn-icon manage-images-btn" title="Images" data-bs-toggle="modal" data-bs-target="#manageImagesModal" data-product='{{ json_encode($product) }}'><i class="bi bi-images"></i></button>
                                <form action="/clerk/product_catalog/{{ $product->id }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product and its images?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <button type="submit" class="btn btn-light btn-icon text-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                </form>
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
                    @csrf
                    @method('PUT')
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
                    @csrf
                    @method('PUT')
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
            form.action = '/clerk/product_catalog/' + product.id;

            editProductModal.querySelector('#edit_product_name').value = product.name;
            editProductModal.querySelector('#edit_product_price').value = product.price;
            editProductModal.querySelector('#edit_product_category').value = product.category;

            // show current image if any
            var currentImg = document.getElementById('edit_current_image');
            if (product.image) {
                currentImg.src = '{{ asset('storage') }}' + '/' + product.image;
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
                currentImagePreview.src = '{{ asset('storage/') }}' + '/' + product.image;
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
</script>
@endpush 