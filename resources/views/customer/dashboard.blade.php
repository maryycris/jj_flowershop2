@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-2" style="background: #f4faf4; min-height: 100vh;">

    <!-- Promoted Products Carousel -->
    <div class="mx-auto mb-3" style="max-width: 1000px;">
        <div class="bg-white rounded-3 p-2 position-relative shadow-sm">
            <div id="promotedCarousel" class="carousel slide" data-bs-ride="carousel">
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="prev" style="left: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-left" style="font-size: 1.5rem;"></i></button>
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="next" style="right: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-right" style="font-size: 1.5rem;"></i></button>
                <div class="carousel-inner">
                    @php
                        $banners = \App\Models\PromotedBanner::active()->take(5)->get();
                    @endphp
                    @if($banners->count())
                        @foreach($banners as $i => $b)
                        <div class="carousel-item @if($i === 0) active @endif text-center">
                            <a href="{{ $b->link_url ?? '#' }}" @if($b->link_url) target="_self" @endif>
                                <img src="{{ asset('storage/' . $b->image) }}" alt="{{ $b->title ?? 'Banner' }}" style="height: 180px; object-fit: cover; border-radius: 6px; width:100%;">
                            </a>
                        </div>
                        @endforeach
                    @else
                        @foreach($promotedProducts as $i => $product)
                        <div class="carousel-item @if($i === 0) active @endif text-center">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="height: 180px; object-fit: cover; border-radius: 6px; width:100%;">
                            <div class="mt-1 fw-bold" style="font-size: 1rem;">{{ $product->name }}</div>
                            <div class="text-success" style="font-size: 0.95rem;">₱{{ number_format($product->price, 2) }}</div>
                        </div>
                        @endforeach
                    @endif
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

        <!-- Products Section -->
        <div class="mb-1 fw-bold fs-5" style="color: #385E42; padding-left: 15px;">{{ $currentCategory }}</div>
        
        <!-- Horizontal Line Separator -->
        <hr class="my-2" style="border: 2px solid #1f3b2a; border-radius: 1px; margin-left: 15px; margin-right: 15px;">
        <div class="row g-2 product-grid" style="padding-left: 15px; padding-right: 15px;">
                @forelse($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="#" class="text-decoration-none text-dark" onclick='console.log("Clicking product:", {{ $product->id }}); openProductModal({
                        id: {{ $product->id }},
                        name: {!! json_encode($product->name) !!},
                        price: "{{ $product->price }}",
                        image: "{{ asset('storage/' . $product->image) }}",
                        description: {!! json_encode($product->description ?? '') !!}
                    }); return false;'>
                        <div class="card product-card h-100" style="border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s; background: transparent;">
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-image" alt="{{ $product->name }}" style="height: 240px; object-fit: cover; border-radius: 8px 8px 0 0;">
                            <div class="card-body text-center" style="background: transparent; padding: 20px 15px 15px 15px;">
                                <h6 class="card-title mb-2" style="font-size: 0.8rem; font-weight: 600; color: #2c3e50; line-height: 1.2;">{{ $product->name }}</h6>
                                <p class="card-text product-price mb-0" style="color: #27ae60; font-weight: 700; font-size: 0.85rem;">₱{{ number_format($product->price, 2) }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-center">No products found.</p>
                </div>
                @endforelse
        </div>
    </div>
    @include('customer.products.modal')
</div>
@endsection

@push('styles')
<style>
    body { background: #f4faf4; }
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
    }
    .category-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        height: 3px;
        background: #27ae60;
        border-radius: 2px;
    }
    .category-tabs .nav-link:hover {
        color: #27ae60 !important;
        background: #f8f9fa !important;
    }
    .category-tabs {
        border-bottom: none !important;
        padding: 0 1rem;
    }
    .product-card {
        border: none;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        background: transparent;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .product-image {
        height: 240px;
        width: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
        background-color: transparent;
        padding: 0;
        margin: 0;
        border-radius: 8px 8px 0 0;
        transition: transform 0.3s ease;
    }
    .product-card:hover .product-image {
        transform: scale(1.05);
    }
    .product-price {
        color: #27ae60;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .card-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.8rem;
        line-height: 1.2;
    }
    .card-body {
        padding: 20px 15px 15px 15px !important;
    }
    .product-grid {
        background: transparent;
        border-radius: 0 0 8px 8px;
        padding: 0;
        box-shadow: none;
        min-height: 300px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dashboard initialization
    console.log('Dashboard loaded');
});
</script>
@endpush
