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

    <!-- Category Tabs -->
    <div class="mx-auto mb-3" style="max-width: 900px;">
        <ul class="nav nav-tabs border-0 justify-content-center" id="productTabs" role="tablist" style="background: #fff; border-radius: 12px 12px 0 0;">
            @php
                $categories = ['all' => 'All', 'bouquets' => 'Bouquets', 'packages' => 'Packages', 'gifts' => 'Gifts'];
                $currentCategory = $categories[request('category', 'all')] ?? 'All';
            @endphp
            @foreach($categories as $key => $label)
            <li class="nav-item" role="presentation">
                <a class="nav-link @if(request('category', 'all') === $key) active @endif" href="?category={{ $key }}" style="font-weight: 500; color: #8ACB88; border: none; border-bottom: 3px solid transparent; @if(request('category', 'all') === $key) border-bottom: 3px solid #8ACB88; color: #385E42; background: #fff; @endif">{{ $label }}</a>
            </li>
        @endforeach
    </ul>
    </div>

    <!-- Product Grid Card -->
    <div class="mx-auto" style="max-width: 900px;">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="mb-3 fw-bold fs-5">{{ $currentCategory }}</div>
            <div class="row g-3 product-grid">
                <!-- Add New Product Card -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card add-new-product-card h-100 d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus fa-3x text-muted"></i>
                    </div>
                </div>
                @php
                    $filteredProducts = $products;
                    if(request('category', 'all') !== 'all') {
                        $filteredProducts = $products->where('category', ucfirst(request('category')));
                    }
                @endphp
                @forelse($filteredProducts as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/logo.png' }}" class="card-img-top product-image" alt="{{ $product->name }}">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">{{ $product->name }}</h6>
                            <p class="card-text product-price">₱{{ number_format($product->price, 2) }}</p>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button class="btn btn-sm btn-info edit-product-btn" data-bs-toggle="modal" data-bs-target="#editProductModal" data-product='{{ json_encode($product) }}'>Edit</button>
                                <button class="btn btn-sm btn-warning manage-images-btn" data-bs-toggle="modal" data-bs-target="#manageImagesModal" data-product='{{ json_encode($product) }}'>Images</button>
                                <form action="#" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product and its images?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
                                <option value="Bouquets">Bouquets</option>
                                <option value="Packages">Packages</option>
                                <option value="Gifts">Gifts</option>
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
                                <option value="Bouquets">Bouquets</option>
                                <option value="Packages">Packages</option>
                                <option value="Gifts">Gifts</option>
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
            form.action = '#'; // Set the form action dynamically

            editProductModal.querySelector('#edit_product_name').value = product.name;
            editProductModal.querySelector('#edit_product_price').value = product.price;
            editProductModal.querySelector('#edit_product_category').value = product.category;
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
            form.action = '#'; // Set update form action

            // Set delete image button action
            var deleteImageBtn = document.getElementById('deleteImageBtn');
            deleteImageBtn.onclick = function() {
                if (confirm('Are you sure you want to delete the current product image?')) {
                    // Implement AJAX delete if needed
                    window.location.reload(); // Reload to reflect changes
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