@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-1" style="background: #f4faf4; min-height: 100vh;">

    <!-- Promoted Banner Carousel (only shows if banners are uploaded) -->
    @php
        $banners = \App\Models\PromotedBanner::active()->take(5)->get();
    @endphp
    @if($banners->count() > 0)
    <div class="mx-auto mb-2" style="max-width: 1000px;">
        <div class="bg-white rounded-3 p-2 position-relative shadow-sm">
            <div id="promotedCarousel" class="carousel slide" data-bs-ride="carousel">
                @if($banners->count() > 1)
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="prev" style="left: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-left" style="font-size: 1.5rem;"></i></button>
                <button class="btn btn-link text-success p-0 position-absolute" data-bs-target="#promotedCarousel" data-bs-slide="next" style="right: 8px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="bi bi-chevron-right" style="font-size: 1.5rem;"></i></button>
                @endif
                <div class="carousel-inner">
                    @foreach($banners as $i => $b)
                    <div class="carousel-item @if($i === 0) active @endif text-center">
                        <a href="{{ $b->link_url ?? '#' }}" @if($b->link_url) target="_self" @endif>
                            <img src="{{ $b->image_url }}" alt="{{ $b->title ?? 'Banner' }}" style="height: 180px; object-fit: cover; border-radius: 6px; width:100%;" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                        </a>
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
                    <div class="input-group position-relative">
                        <input id="productSearchInput" type="text" class="form-control" placeholder="Search products..." aria-label="Search" value="{{ request('search', '') }}" autocomplete="off">
                        <button id="productFilterBtn" class="btn btn-outline-success" type="button" title="Filter"><i class="bi bi-funnel"></i></button>
                        <!-- Search Suggestions Dropdown -->
                        <div id="searchSuggestions" class="search-suggestions-dropdown" style="display: none;"></div>
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

        <!-- Products Section -->
        <div class="mb-1 fw-bold fs-5" style="color: #385E42; padding-left: 15px;">{{ $currentCategory }}</div>
        
        <!-- Horizontal Line Separator -->
        <hr class="my-2" style="border: 2px solid #1f3b2a; border-radius: 1px; margin-left: 15px; margin-right: 15px;">
        <div class="row g-2 product-grid" style="padding-left: 15px; padding-right: 15px;">
                @forelse($products as $product)
                @php
                    $isOutOfStock = isset($productAvailability[$product->id]) && !$productAvailability[$product->id]['can_fulfill'];
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    @if($isOutOfStock)
                        <div class="text-decoration-none text-dark" style="opacity: 0.6;">
                            <div class="card product-card h-100" style="border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: transparent; position: relative;">
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-image" alt="{{ $product->name }}" style="height: 240px; object-fit: cover; border-radius: 8px 8px 0 0; filter: grayscale(50%);">
                                <!-- OUT OF STOCK Overlay -->
                                <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                    <span class="badge bg-danger" style="font-size: 0.7rem;">OUT OF STOCK</span>
                                </div>
                                <div class="card-body text-center" style="background: transparent; padding: 20px 15px 15px 15px;">
                                    <h6 class="card-title mb-2" style="font-size: 0.8rem; font-weight: 600; color: #2c3e50; line-height: 1.2;">{{ $product->name }}</h6>
                                    <p class="card-text product-price mb-0" style="color: #27ae60; font-weight: 700; font-size: 0.85rem;">₱{{ number_format($product->price, 2) }}</p>
                                    <small class="text-muted" style="font-size: 0.7rem;">Insufficient materials</small>
                                </div>
                            </div>
                        </div>
                    @else
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
                    @endif
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

    

    @include('customer.products.modal')
</div>
@endsection

@push('styles')
<style>
    body { background: #f4faf4; }
    
    /* Mobile/Compact Responsive Design (≤650px) */
    @media (max-width: 650px) {
        .container-fluid {
            padding-bottom: 80px; /* Space for bottom nav */
        }
        
        /* Make carousel responsive */
        .carousel-inner img {
            height: 70px !important;
        }
        
        /* Make search bar responsive */
        .input-group {
            margin:  15px 0 -15px;
            align-items: stretch; /* keep children same height */
        }
        
        #productSearchInput {
            font-size: 14px;
            padding: 10px 12px;
            height: 42px;
        }
        
        #productFilterBtn {
            padding: 0 14px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Make category tabs responsive */
        .category-tabs {
            margin: 0 10px;
            padding: 0 !important;
        }
        
        .category-tabs .nav-link {
            font-size: 12px !important;
            padding: 8px 4px !important;
            margin: 0 !important;
        }
        
        /* Make products responsive */
        .product-grid {
            margin: 0 10px;
            padding: 0 !important;
        }
        
        .product-grid .col-6 {
            padding: 5px;
        }
        
        .product-card {
            margin-bottom: 10px;
        }
        
        .product-image {
            height: 150px !important;
        }
        
        .card-title {
            font-size: 0.7rem !important;
        }
        
        .product-price {
            font-size: 0.75rem !important;
        }
        
        .card-body {
            padding: 10px 8px !important;
        }
        
        /* Make section title responsive */
        .fs-5 {
            font-size: 1rem !important;
            margin: 10px 0 5px 0;
        }
        
        /* Make hr responsive */
        .my-2 {
            margin: 5px 0 !important;
        }
        
        /* Mobile Bottom Navigation */
        /* bottom nav now global in layout */

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.3s;
            position: relative;
            flex: 1 1 0; /* spread items evenly in a row */
            text-align: center;
        }

        .nav-item.active {
            background: rgba(255,255,255,0.2);
        }

        .nav-item i {
            font-size: 18px;
            margin-bottom: 4px;
        }
        
        .nav-item .bi {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .nav-item span {
            font-size: 11px;
            font-weight: 500;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            bottom: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.15);
            padding: 10px 0;
            min-width: 200px;
            display: none;
            z-index: 1001;
        }

        .profile-dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            transition: background 0.3s;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
        }

        .dropdown-item i,
        .dropdown-item .bi {
            margin-right: 12px;
            width: 16px;
            text-align: center;
            color: #A0C49D;
        }

        .dropdown-item span {
            font-size: 14px;
            font-weight: 500;
        }
    }
    
    /* Desktop Styles */
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
    
    /* Search bar styling (no white container) */
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
    
    /* Search Suggestions Dropdown - Mobile Responsive */
    .search-suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #27ae60;
        border-top: none;
        border-radius: 0 0 8px 8px;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        margin-top: -2px;
    }
    
    .search-suggestion-item {
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .search-suggestion-item:last-child {
        border-bottom: none;
    }
    
    .search-suggestion-item:hover,
    .search-suggestion-item.active {
        background-color: #f0f8f4;
    }
    
    .search-suggestion-item .suggestion-name {
        flex: 1;
        font-size: 0.9rem;
        color: #2c3e50;
        font-weight: 500;
    }
    
    .search-suggestion-item .suggestion-price {
        font-size: 0.85rem;
        color: #27ae60;
        font-weight: 600;
    }
    
    .search-suggestion-item .suggestion-category {
        font-size: 0.75rem;
        color: #7f8c8d;
        background: #ecf0f1;
        padding: 2px 8px;
        border-radius: 12px;
    }
    
    /* Mobile Responsive Styles */
    @media (max-width: 650px) {
        .search-suggestions-dropdown {
            max-height: 180px;
            border-radius: 0 0 6px 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .search-suggestion-item {
            padding: 10px 12px;
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        
        .search-suggestion-item .suggestion-name {
            font-size: 0.85rem;
            width: 100%;
            word-break: break-word;
        }
        
        .search-suggestion-item .suggestion-price {
            font-size: 0.8rem;
        }
        
        .search-suggestion-item .suggestion-category {
            font-size: 0.7rem;
            padding: 2px 6px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dashboard initialization
    console.log('Dashboard loaded');
    
    // Mobile bottom navigation functionality
    const navItems = document.querySelectorAll('.mobile-bottom-nav .nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Remove active class from all items
            navItems.forEach(nav => nav.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');
        });
    });
    
    // Profile dropdown functionality
    const profileDropdown = document.querySelector('.profile-dropdown');
    if (profileDropdown) {
        profileDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.querySelector('.dropdown-menu');
            if (dropdown) {
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target)) {
                const dropdown = profileDropdown.querySelector('.dropdown-menu');
                if (dropdown) {
                    dropdown.style.display = 'none';
                }
            }
        });
    }
    
    // Search functionality with autocomplete suggestions
    const searchInput = document.getElementById('productSearchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    let suggestionTimeout = null;
    let selectedSuggestionIndex = -1;
    let currentSuggestions = [];
    
    if (searchInput) {
        // Show suggestions as user types
        searchInput.addEventListener('input', function(e) {
            const query = this.value.trim();
            
            clearTimeout(suggestionTimeout);
            
            if (query.length < 2) {
                hideSuggestions();
                return;
            }
            
            // Debounce API calls
            suggestionTimeout = setTimeout(() => {
                fetchSuggestions(query);
            }, 300);
        });
        
        // Handle Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                if (selectedSuggestionIndex >= 0 && currentSuggestions[selectedSuggestionIndex]) {
                    selectSuggestion(currentSuggestions[selectedSuggestionIndex]);
                } else {
                    performSearch();
                }
            }
        });
        
        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (searchSuggestions.style.display === 'none') return;
            
            const items = searchSuggestions.querySelectorAll('.search-suggestion-item');
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    selectedSuggestionIndex = Math.min(selectedSuggestionIndex + 1, items.length - 1);
                    updateSelectedSuggestion();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    selectedSuggestionIndex = Math.max(selectedSuggestionIndex - 1, -1);
                    updateSelectedSuggestion();
                    break;
                case 'Escape':
                    hideSuggestions();
                    break;
            }
        });
        
        // Show suggestions on focus if there's text
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                fetchSuggestions(this.value.trim());
            }
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                hideSuggestions();
            }
        });
    }
    
    function fetchSuggestions(query) {
        fetch(`{{ route('customer.search-suggestions') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.suggestions) {
                    displaySuggestions(data.suggestions);
                } else {
                    hideSuggestions();
                }
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                hideSuggestions();
            });
    }
    
    function displaySuggestions(suggestions) {
        if (!suggestions || suggestions.length === 0) {
            hideSuggestions();
            return;
        }
        
        currentSuggestions = suggestions;
        searchSuggestions.innerHTML = '';
        
        suggestions.forEach((suggestion, index) => {
            const item = document.createElement('div');
            item.className = 'search-suggestion-item';
            item.innerHTML = `
                <span class="suggestion-name">${escapeHtml(suggestion.name)}</span>
                <span class="suggestion-price">₱${parseFloat(suggestion.price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                <span class="suggestion-category">${escapeHtml(suggestion.category)}</span>
            `;
            
            item.addEventListener('click', () => {
                selectSuggestion(suggestion);
            });
            
            item.addEventListener('mouseenter', () => {
                selectedSuggestionIndex = index;
                updateSelectedSuggestion();
            });
            
            item.addEventListener('touchstart', () => {
                selectedSuggestionIndex = index;
                updateSelectedSuggestion();
            });
            
            searchSuggestions.appendChild(item);
        });
        
        searchSuggestions.style.display = 'block';
        selectedSuggestionIndex = -1;
        
        // On mobile, ensure dropdown is positioned correctly
        if (window.innerWidth <= 650) {
            const inputGroup = searchInput.closest('.input-group');
            if (inputGroup) {
                const rect = inputGroup.getBoundingClientRect();
                searchSuggestions.style.position = 'fixed';
                searchSuggestions.style.top = (rect.bottom + window.scrollY) + 'px';
                searchSuggestions.style.left = '0';
                searchSuggestions.style.right = '0';
                searchSuggestions.style.width = '100%';
            }
        } else {
            // Reset to relative positioning on desktop
            searchSuggestions.style.position = '';
            searchSuggestions.style.top = '';
            searchSuggestions.style.left = '';
            searchSuggestions.style.right = '';
            searchSuggestions.style.width = '';
        }
    }
    
    function selectSuggestion(suggestion) {
        if (searchInput) {
            searchInput.value = suggestion.name;
            hideSuggestions();
            performSearch();
        }
    }
    
    function updateSelectedSuggestion() {
        const items = searchSuggestions.querySelectorAll('.search-suggestion-item');
        items.forEach((item, index) => {
            if (index === selectedSuggestionIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else {
                item.classList.remove('active');
            }
        });
    }
    
    function hideSuggestions() {
        searchSuggestions.style.display = 'none';
        selectedSuggestionIndex = -1;
        currentSuggestions = [];
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Filter functionality
    const filterBtn = document.getElementById('productFilterBtn');
    const filterPanel = document.getElementById('productFilterPanel');
    const filterApply = document.getElementById('productFilterApply');
    const filterClear = document.getElementById('productFilterClear');
    
    if (filterBtn && filterPanel) {
        filterBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event from bubbling up
            filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
        });
        
        // Close filter panel when clicking outside
        document.addEventListener('click', function(e) {
            if (filterPanel && filterPanel.style.display === 'block') {
                const within = filterPanel.contains(e.target) || filterBtn.contains(e.target);
                if (!within) {
                    filterPanel.style.display = 'none';
                }
            }
        });
    }
    
    if (filterApply) {
        filterApply.addEventListener('click', function() {
            performSearch();
            if (filterPanel) filterPanel.style.display = 'none';
        });
    }
    
    if (filterClear) {
        filterClear.addEventListener('click', function() {
            clearFilters();
        });
    }
    
    function performSearch() {
        const searchTerm = searchInput ? searchInput.value : '';
        const currentUrl = new URL(window.location.href);
        const category = currentUrl.searchParams.get('category') || 'all';
        const minPrice = document.getElementById('productFilterMin')?.value || '';
        const maxPrice = document.getElementById('productFilterMax')?.value || '';
        
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
        const filterMin = document.getElementById('productFilterMin');
        const filterMax = document.getElementById('productFilterMax');
        if (filterMin) filterMin.value = '';
        if (filterMax) filterMax.value = '';
        
        // Redirect to clean URL
        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        url.searchParams.delete('min_price');
        url.searchParams.delete('max_price');
        window.location.href = url.toString();
    }
});
</script>
@endpush
