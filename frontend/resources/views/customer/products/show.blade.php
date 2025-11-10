@extends('layouts.customer_app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-link text-decoration-none text-dark"><i class="fas fa-arrow-left me-2"></i></a>
        <h4 class="mb-0">Product Details</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="d-grid mb-3">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-primary">Continue Shopping</a>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 text-center">
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded product-detail-image" alt="{{ $product->name }}">
                    @if($product->image2 || $product->image3)
                    <div class="d-flex justify-content-center mt-3 gap-2">
                        @if($product->image2)
                        <img src="{{ asset('storage/' . $product->image2) }}" class="img-thumbnail sub-image" alt="{{ $product->name }} - Image 2">
                        @endif
                        @if($product->image3)
                        <img src="{{ asset('storage/' . $product->image3) }}" class="img-thumbnail sub-image" alt="{{ $product->name }} - Image 3">
                        @endif
                    </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h2 class="product-detail-name">{{ $product->name }}</h2>
                    <div class="mb-3">
                        <h5>Description</h5>
                        <p>{{ $product->description ?? 'No description available.' }}</p>
                    </div>

                    <!-- Product Composition Section -->
                    @if($product->compositions && $product->compositions->count() > 0)
                    <div class="mb-3">
                        <h5>Flower Composition</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Component</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->compositions as $composition)
                                    <tr>
                                        <td>{{ $composition->component_name }}</td>
                                        <td>{{ $composition->quantity }}</td>
                                        <td>{{ ucfirst($composition->unit) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <h5>Price</h5>
                        <span class="h3 text-success">₱{{ number_format($product->price, 2) }}</span>
                    </div>

                    <div class="mb-3">
                        <label for="deliveryLocation" class="form-label">Deliver to</label>
                        <div class="input-group">
                            <input type="text" class="form-control delivery-input" id="deliveryLocation" placeholder="Enter delivery address">
                            <button class="btn btn-outline-secondary" type="button" id="changeDeliveryBtn">Change</button>
                        </div>
                    </div>

                    <div class="mb-3 d-flex align-items-center">
                        <label for="quantity" class="form-label me-3 mb-0">Quantity:</label>
                        <div class="input-group quantity-control">
                            <button class="btn btn-outline-secondary quantity-btn" type="button" data-action="decrement">-</button>
                            <input type="text" class="form-control text-center quantity-input" id="quantity" value="1" readonly>
                            <button class="btn btn-outline-secondary quantity-btn" type="button" data-action="increment">+</button>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button class="btn btn-success add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                let quantity = parseInt(quantityInput.value);
                if (this.dataset.action === 'decrement' && quantity > 1) {
                    quantity--;
                } else if (this.dataset.action === 'increment') {
                    quantity++;
                }
                quantityInput.value = quantity;
            });
        });

        // Dummy functionality for Change Delivery Button
        document.getElementById('changeDeliveryBtn').addEventListener('click', function() {
            alert('Change Delivery functionality would go here!');
        });

        // Test alert function
        console.log('Testing alert function...');
        if (typeof showAlert === 'function') {
            console.log('showAlert function is available');
            // Test the alert
            setTimeout(() => {
                showAlert('Test alert - this should appear!', 'success');
            }, 1000);
        } else {
            console.error('showAlert function is NOT available');
        }

        // Add to Cart Button functionality
        document.querySelector('.add-to-cart-btn').addEventListener('click', function() {
            const productId = {{ $product->id }};
            const quantity = parseInt(quantityInput.value);

            fetch('{{ route('customer.cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                console.log('Response redirected:', response.redirected);
                
                if (response.redirected) {
                    console.log('Redirecting to:', response.url);
                    window.location.href = response.url; // Redirect to the new URL
                } else if (!response.ok) {
                    // If response is not OK (e.g., 4xx or 5xx status), parse as JSON for error message
                    return response.json().then(errorData => {
                        console.log('Error data:', errorData);
                        showAlert('Failed to add product to cart: ' + (errorData.message || 'Unknown error'), 'error');
                        throw new Error('Server error'); // Propagate error for catch block
                    });
                } else {
                    // If successful but not redirected, parse JSON response
                    return response.json().then(data => {
                        console.log('Success data:', data);
                        showAlert(data.message || 'Product added to cart!', 'success');
                    });
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showAlert('An error occurred while adding product to cart.', 'error');
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .product-detail-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .sub-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.2s ease;
    }
    .sub-image:hover {
        border-color: var(--primary-green);
    }
    .product-detail-name {
        color: var(--primary-green);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .product-detail-price {
        font-size: 1.5rem;
        color: var(--accent-green);
        font-weight: 600;
        margin-bottom: 1.5rem;
    }
    .delivery-input {
        border-radius: 25px;
        padding: 10px 20px;
        border: 1px solid var(--border-light);
    }
    .quantity-control .btn {
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-color: var(--primary-green);
        color: var(--primary-green);
    }
    .quantity-control .btn:hover {
        background-color: var(--primary-green);
        color: white;
    }
    .quantity-input {
        width: 60px;
        text-align: center;
        border-left: none;
        border-right: none;
        border-color: var(--border-light);
    }
    .add-to-cart-btn {
        background-color: var(--primary-green) !important;
        border-color: var(--primary-green) !important;
        padding: 12px 20px;
        font-size: 1.1rem;
        border-radius: 25px;
    }
    .add-to-cart-btn:hover {
        background-color: #2a4a34 !important;
        border-color: #2a4a34 !important;
    }
</style>
@endpush 