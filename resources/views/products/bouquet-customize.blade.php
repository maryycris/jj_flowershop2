@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-4" style="min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-10 col-xl-10"><div class="row justify-content-center">
            <div class="py-4" style="background: #CDE7C9; min-height: 100vh; border-radius: 1rem;">
                <div class="row h-100">
        <!-- Left Panel - Customization -->
        <div class="col-lg-6" style="background: #CDE7C9; padding: 2rem; overflow-y: auto; max-height: 100vh;">
            <div class="h-100 d-flex flex-column">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-dark mb-2">Customize your Desired Bouquet</h3>
                    <button class="btn btn-outline-success btn-sm">
                        <i class="bi bi-info-circle me-1"></i>Notice to Customers
                    </button>
                </div>
                
                <hr class="my-4">
                
                <!-- Customization Form -->
                <form id="bouquetCustomizationForm" class="flex-grow-1 d-flex flex-column">
                    @csrf
                    
                    <!-- Bouquet Wrapper Selection -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Choose Bouquet Wrapper</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noWrapper" name="no_wrapper">
                                <label class="form-check-label fw-medium" for="noWrapper">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3" id="wrapperGrid">
                            @foreach(($items['Wrapper'] ?? []) as $wrap)
                            <div class="col-3">
                                <div class="wrapper-option" data-wrapper="{{ $wrap->name }}" data-price="{{ $wrap->price ?? 0 }}">
                                    <div class="wrapper-card position-relative">
                                        <div class="rounded-3" style="height: 80px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($wrap->image)
                                                <img src="{{ asset('storage/'.$wrap->image) }}" alt="{{ $wrap->name }}" style="max-height:80px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold small">{{ $wrap->name }}</span>
                                            @endif
                                        </div>
                                        <div class="wrapper-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="wrapper-info mt-2 text-center">
                                            <small class="fw-medium text-dark">{{ $wrap->name }}</small>
                                            <div class="text-success fw-bold small">₱{{ number_format($wrap->price ?? 0,2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Pagination dots -->
                        <div class="d-flex justify-content-center mt-3">
                            <div class="d-flex gap-1">
                                <div class="dot active"></div>
                                <div class="dot"></div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">

                    <!-- Focal Flower 1 -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Choose Focal Flower 1</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noFocalFlower1" name="no_focal_flower_1">
                                <label class="form-check-label fw-medium" for="noFocalFlower1">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3" id="focalFlowerGrid1">
                            @foreach(($items['Focal'] ?? []) as $focal)
                            <div class="col-3">
                                <div class="focal1-option" data-flower="{{ $focal->name }}" data-price="{{ $focal->price ?? 0 }}">
                                    <div class="flower-card position-relative">
                                        <div class="rounded-3" style="height: 80px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($focal->image)
                                                <img src="{{ asset('storage/'.$focal->image) }}" alt="{{ $focal->name }}" style="max-height:80px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold small">{{ $focal->name }}</span>
                                            @endif
                                        </div>
                                        <div class="flower-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        </div>
                                        <div class="flower-info mt-2 text-center">
                                            <small class="fw-medium text-dark">{{ $focal->name }}</small>
                                            <div class="text-success fw-bold">₱{{ number_format($focal->price ?? 0,2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Pagination dots -->
                        <div class="d-flex justify-content-center mt-3">
                            <div class="d-flex gap-1">
                                <div class="dot active"></div>
                                <div class="dot"></div>
                            </div>
                        </div>
                    </div>
                <hr class="my-4">

                    <!-- Focal Flower 2 -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Choose Focal Flower 2</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noFocalFlower2" name="no_focal_flower_2">
                                <label class="form-check-label fw-medium" for="noFocalFlower2">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3" id="focalFlowerGrid2">
                            @foreach(($items['Focal'] ?? []) as $focal)
                            <div class="col-3">
                                <div class="focal2-option" data-flower="{{ $focal->name }}" data-price="{{ $focal->price ?? 0 }}">
                                    <div class="flower-card position-relative">
                                        <div class="rounded-3" style="height: 80px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($focal->image)
                                                <img src="{{ asset('storage/'.$focal->image) }}" alt="{{ $focal->name }}" style="max-height:80px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold small">{{ $focal->name }}</span>
                                            @endif
                                        </div>
                                        <div class="flower-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        </div>
                                        <div class="flower-info mt-2 text-center">
                                            <small class="fw-medium text-dark">{{ $focal->name }}</small>
                                            <div class="text-success fw-bold">₱{{ number_format($focal->price ?? 0,2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Pagination dots -->
                        <div class="d-flex justify-content-center mt-3">
                            <div class="d-flex gap-1">
                                <div class="dot active"></div>
                                <div class="dot"></div>
                            </div>
                        </div>
                    </div>
                <hr class="my-4">

                    <!-- Focal Flower 3 -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Choose Focal Flower 3</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noFocalFlower3" name="no_focal_flower_3">
                                <label class="form-check-label fw-medium" for="noFocalFlower3">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3" id="focalFlowerGrid3">
                            @foreach(($items['Focal'] ?? []) as $focal)
                            <div class="col-3">
                                <div class="focal3-option" data-flower="{{ $focal->name }}" data-price="{{ $focal->price ?? 0 }}">
                                    <div class="flower-card position-relative">
                                        <div class="rounded-3" style="height: 80px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($focal->image)
                                                <img src="{{ asset('storage/'.$focal->image) }}" alt="{{ $focal->name }}" style="max-height:80px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold small">{{ $focal->name }}</span>
                                            @endif
                                        </div>
                                        <div class="flower-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        </div>
                                        <div class="flower-info mt-2 text-center">
                                            <small class="fw-medium text-dark">{{ $focal->name }}</small>
                                            <div class="text-success fw-bold">₱{{ number_format($focal->price ?? 0,2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Pagination dots -->
                        <div class="d-flex justify-content-center mt-3">
                            <div class="d-flex gap-1">
                                <div class="dot active"></div>
                                <div class="dot"></div>
                            </div>
                        </div>
                    </div>
                <hr class="my-4">
                    
                    <!-- Greenery Selection -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Choose Greenery</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noGreenery" name="no_greenery">
                                <label class="form-check-label fw-medium" for="noGreenery">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3">
                            @foreach(($items['Greeneries'] ?? []) as $greenery)
                            <div class="col-3">
                                <div class="greenery-option" data-greenery="{{ $greenery->name }}" data-price="{{ $greenery->price ?? 0 }}">
                                    <div class="greenery-card position-relative">
                                        <div class="rounded-3" style="height: 80px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($greenery->image)
                                                <img src="{{ asset('storage/'.$greenery->image) }}" alt="{{ $greenery->name }}" style="max-height:80px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold small">{{ $greenery->name }}</span>
                                            @endif
                                        </div>
                                        <div class="greenery-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="greenery-info mt-2 text-center">
                                            <small class="fw-medium text-dark">{{ $greenery->name }}</small>
                                            <div class="text-success fw-bold small">₱{{ number_format($greenery->price ?? 0,2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Pagination dots -->
                        <div class="d-flex justify-content-center mt-3">
                            <div class="d-flex gap-1">
                                <div class="dot active"></div>
                                <div class="dot"></div>
                            </div>
                        </div>
                    </div>
                <hr class="my-4">
                    
                    <!-- Fillers Selection -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Choose Fillers</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noFillers" name="no_fillers">
                                <label class="form-check-label fw-medium" for="noFillers">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3">
                            @foreach(($items['Fillers'] ?? []) as $filler)
                            <div class="col-3">
                                <div class="filler-option" data-filler="{{ $filler->name }}" data-price="{{ $filler->price ?? 0 }}">
                                    <div class="filler-card position-relative">
                                        <div class="rounded-3" style="height: 80px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($filler->image)
                                                <img src="{{ asset('storage/'.$filler->image) }}" alt="{{ $filler->name }}" style="max-height:80px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold small">{{ $filler->name }}</span>
                                            @endif
                                        </div>
                                        <div class="filler-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="filler-info mt-2 text-center">
                                            <small class="fw-medium text-dark">{{ $filler->name }}</small>
                                            <div class="text-success fw-bold small">₱{{ number_format($filler->price ?? 0,2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Pagination dots -->
                        <div class="d-flex justify-content-center mt-3">
                            <div class="d-flex gap-1">
                                <div class="dot active"></div>
                                <div class="dot"></div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">

                    <!-- Ribbon Selection -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Choose Ribbon</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noRibbon" name="no_ribbon">
                                <label class="form-check-label fw-medium" for="noRibbon">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3">
                            @foreach(($items['Ribbons'] ?? []) as $ribbon)
                            <div class="col-3">
                                <div class="ribbon-option" data-ribbon="{{ $ribbon->name }}" data-price="{{ $ribbon->price ?? 0 }}">
                                    <div class="ribbon-card position-relative">
                                        <div class="rounded-3" style="height: 80px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($ribbon->image)
                                                <img src="{{ asset('storage/'.$ribbon->image) }}" alt="{{ $ribbon->name }}" style="max-height:80px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold small">{{ $ribbon->name }}</span>
                                            @endif
                                        </div>
                                        <div class="ribbon-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="ribbon-info mt-2 text-center">
                                            <small class="fw-medium text-dark">{{ $ribbon->name }}</small>
                                            <div class="text-success fw-bold small">₱{{ number_format($ribbon->price ?? 0,2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Pagination dots -->
                        <div class="d-flex justify-content-center mt-3">
                            <div class="d-flex gap-1">
                                <div class="dot active"></div>
                                <div class="dot"></div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">

                    <!-- Quantity Selection -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">Quantity</h6>
                        <div class="input-group" style="max-width: 150px;">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="decreaseQty">-</button>
                            <input type="number" class="form-control form-control-sm text-center" id="quantity" name="quantity" value="1" min="1" max="10">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="increaseQty">+</button>
                        </div>
                    </div>
                                
                    <!-- Action Buttons -->
                    <div class="mt-auto pt-4">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm flex-grow-1" id="addToCartBtn">
                                <i class="bi bi-cart-plus me-1"></i>Add to Cart
                            </button>
                            <button type="submit" class="btn btn-success btn-sm flex-grow-1" id="buyNowBtn">
                                <i class="bi bi-bag-check me-1"></i>Buy Now
                                <i class="bi bi-chevron-down ms-1"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Right Panel - Bouquet Preview -->
        <div class="col-lg-6" style="background: #CDE7C9; padding: 2rem;">
            <div class="h-100 d-flex flex-column align-items-center">
                <!-- Bouquet Preview Container -->
                <div class="bouquet-preview-container position-relative" style="width: 360px; max-width: 100%;">
                    <!-- Bouquet Image (clean, no text) -->
                    <div id="bouquetPreview" class="img-fluid" style="width: 100%; height: 320px; background: #F1F6F1; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);">
                        <img src="/images/bouquet-wrapper-placeholder.png" alt="Bouquet" style="max-width: 90%; max-height: 90%; object-fit: contain;" onerror="this.style.display='none'">
                    </div>
                </div>
                
                <!-- Price Summary -->
                <div class="mt-3 w-100" style="max-width: 360px;">
                    <div class="price-summary bg-white rounded-3 shadow-sm p-3" id="priceSummaryCard" style="cursor: pointer;">
                        <div class="fw-semibold text-dark mb-2">Price Summary</div>
                        <hr class="my-2">
                        
                        <!-- Collapsed Total (shown when collapsed) -->
                        <div class="d-flex justify-content-between fw-bold" id="collapsedTotal">
                            <span>Total:</span>
                            <span id="totalPrice">₱150.00</span>
                        </div>
                        
                        <!-- Collapsible Details -->
                        <div id="priceDetails" style="display: none; overflow: hidden; transition: all 0.3s ease;">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Wrapper:</span>
                                <span id="wrapperPrice">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Focal Flower 1:</span>
                                <span id="focalFlower1Price">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Focal Flower 2:</span>
                                <span id="focalFlower2Price">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Focal Flower 3:</span>
                                <span id="focalFlower3Price">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Greenery:</span>
                                <span id="greeneryPrice">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Fillers:</span>
                                <span id="fillerPrice">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ribbon:</span>
                                <span id="ribbonPrice">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Assembly Fee:</span>
                                <span>₱150.00</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span id="expandedTotalPrice">₱150.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form inputs -->
<input type="hidden" id="selectedWrapper" name="wrapper" value="">
<input type="hidden" id="selectedFocalFlower1" name="focal_flower_1" value="">
<input type="hidden" id="selectedFocalFlower2" name="focal_flower_2" value="">
<input type="hidden" id="selectedFocalFlower3" name="focal_flower_3" value="">
<input type="hidden" id="selectedGreenery" name="greenery" value="">
<input type="hidden" id="selectedFiller" name="filler" value="">
<input type="hidden" id="selectedRibbon" name="ribbon" value="">

@endsection

@push('styles')
<style>
.wrapper-option, .focal1-option, .focal2-option, .focal3-option, .greenery-option, .filler-option, .ribbon-option {
    cursor: pointer;
    transition: all 0.6s ease;
}

.wrapper-option:hover, .focal1-option:hover, .focal2-option:hover, .focal3-option:hover, .greenery-option:hover, .filler-option:hover, .ribbon-option:hover {
    transform: translateY(-2px);
}

.wrapper-card, .flower-card, .greenery-card, .filler-card, .ribbon-card {
    border: 2px solid transparent;
    border-radius: 0.75rem;
    transition: all 0.9s ease;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.wrapper-option.selected .wrapper-card,
.focal1-option.selected .flower-card,
.focal2-option.selected .flower-card,
.focal3-option.selected .flower-card,
.greenery-option.selected .greenery-card,
.filler-option.selected .filler-card,
.ribbon-option.selected .ribbon-card {
    border-color: #28a745;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.3s ease;
}

.dot.active {
    background: #28a745;
    transform: scale(1.2);
}

.bouquet-preview-container {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 1rem;
    padding: 1rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.price-summary {
    border: 1px solid #e9ecef;
}

.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
}

/* Custom Scrollbar Styling */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #000000;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #333333;
}

/* Firefox scrollbar */
* {
    scrollbar-width: thin;
    scrollbar-color: #000000 transparent;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .col-lg-6 {
        padding: 1rem;
    }
    
    .bouquet-preview-container {
        width: 250px !important;
        height: 300px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedWrapper = null;
    let selectedFocalFlower1 = null;
    let selectedFocalFlower2 = null;
    let selectedFocalFlower3 = null;
    let selectedGreenery = null;
    let selectedFiller = null;
    let selectedRibbon = null;
    let quantity = 1;
    
    const assemblyFee = 150;
    
    // Wrapper selection handlers
    document.querySelectorAll('.wrapper-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.wrapper-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.wrapper-check').style.display = 'none';
            });
            
            // Add selection to clicked option
            this.classList.add('selected');
            this.querySelector('.wrapper-check').style.display = 'block';
            
            selectedWrapper = {
                name: this.dataset.wrapper,
                price: parseFloat(this.dataset.price)
            };
            
            document.getElementById('selectedWrapper').value = selectedWrapper.name;
            updatePreview();
            updatePrice();
        });
    });
    
    // Focal Flower 1 selection handlers
    document.querySelectorAll('.focal1-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.focal1-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.flower-check').style.display = 'none';
            });
            
            // Add selection to clicked option
            this.classList.add('selected');
            this.querySelector('.flower-check').style.display = 'block';
            
            selectedFocalFlower1 = {
                name: this.dataset.flower,
                price: parseFloat(this.dataset.price)
            };
            
            document.getElementById('selectedFocalFlower1').value = selectedFocalFlower1.name;
            updatePreview();
            updatePrice();
        });
    });
    
    // Focal Flower 2 selection handlers
    document.querySelectorAll('.focal2-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.focal2-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.flower-check').style.display = 'none';
            });
            
            // Add selection to clicked option
            this.classList.add('selected');
            this.querySelector('.flower-check').style.display = 'block';
            
            selectedFocalFlower2 = {
                name: this.dataset.flower,
                price: parseFloat(this.dataset.price)
            };
            
            document.getElementById('selectedFocalFlower2').value = selectedFocalFlower2.name;
            updatePreview();
            updatePrice();
        });
    });
    
    // Focal Flower 3 selection handlers
    document.querySelectorAll('.focal3-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.focal3-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.flower-check').style.display = 'none';
            });
            
            // Add selection to clicked option
            this.classList.add('selected');
            this.querySelector('.flower-check').style.display = 'block';
            
            selectedFocalFlower3 = {
                name: this.dataset.flower,
                price: parseFloat(this.dataset.price)
            };
            
            document.getElementById('selectedFocalFlower3').value = selectedFocalFlower3.name;
            updatePreview();
            updatePrice();
        });
    });
    
    // Greenery selection handlers
    document.querySelectorAll('.greenery-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.greenery-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.greenery-check').style.display = 'none';
            });
            
            // Add selection to clicked option
            this.classList.add('selected');
            this.querySelector('.greenery-check').style.display = 'block';
            
            selectedGreenery = {
                name: this.dataset.greenery,
                price: parseFloat(this.dataset.price)
            };
            
            document.getElementById('selectedGreenery').value = selectedGreenery.name;
            updatePreview();
            updatePrice();
        });
    });
    
    // Filler selection handlers
    document.querySelectorAll('.filler-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.filler-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.filler-check').style.display = 'none';
            });
            
            // Add selection to clicked option
            this.classList.add('selected');
            this.querySelector('.filler-check').style.display = 'block';
            
            selectedFiller = {
                name: this.dataset.filler,
                price: parseFloat(this.dataset.price)
            };
            
            document.getElementById('selectedFiller').value = selectedFiller.name;
            updatePreview();
            updatePrice();
        });
    });
    
    // Ribbon selection handlers
    document.querySelectorAll('.ribbon-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.ribbon-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.ribbon-check').style.display = 'none';
            });
            
            // Add selection to clicked option
            this.classList.add('selected');
            this.querySelector('.ribbon-check').style.display = 'block';
            
            selectedRibbon = {
                name: this.dataset.ribbon,
                price: parseFloat(this.dataset.price)
            };
            
            document.getElementById('selectedRibbon').value = selectedRibbon.name;
            updatePreview();
            updatePrice();
        });
    });
    
    // Quantity controls
    document.getElementById('decreaseQty').addEventListener('click', function() {
        if (quantity > 1) {
            quantity--;
            document.getElementById('quantity').value = quantity;
            updatePrice();
        }
    });
    
    document.getElementById('increaseQty').addEventListener('click', function() {
        if (quantity < 10) {
            quantity++;
            document.getElementById('quantity').value = quantity;
            updatePrice();
        }
    });
    
    document.getElementById('quantity').addEventListener('change', function() {
        quantity = Math.max(1, Math.min(10, parseInt(this.value) || 1));
        this.value = quantity;
        updatePrice();
    });
    
    // Update preview
    function updatePreview() {
        // Show/hide overlays based on selections
        document.getElementById('focalFlowerOverlay').style.display = selectedFocalFlower ? 'block' : 'none';
        document.getElementById('greeneryOverlay').style.display = selectedGreenery ? 'block' : 'none';
        document.getElementById('fillerOverlay').style.display = selectedFiller ? 'block' : 'none';
        
        // Update bouquet preview based on selections
        const bouquetPreview = document.getElementById('bouquetPreview');
        let previewText = '🌹 Custom Bouquet 🌹';
        let previewStyle = 'linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1)';
        
        if (selectedFocalFlower) {
            switch(selectedFocalFlower.name) {
                case 'red_rose':
                    previewStyle = 'linear-gradient(45deg, #ff6b6b, #ee5a52)';
                    previewText = '🌹 Red Rose Bouquet 🌹';
                    break;
                case 'pink_peony':
                    previewStyle = 'linear-gradient(45deg, #ff9ff3, #f368e0)';
                    previewText = '🌸 Pink Peony Bouquet 🌸';
                    break;
                case 'white_ranunculus':
                    previewStyle = 'linear-gradient(45deg, #f8f9fa, #e9ecef)';
                    previewText = '🌼 White Ranunculus Bouquet 🌼';
                    break;
                case 'light_pink_peony':
                    previewStyle = 'linear-gradient(45deg, #ffc1cc, #ffb3ba)';
                    previewText = '🌺 Light Pink Peony Bouquet 🌺';
                    break;
            }
        }
        
        bouquetPreview.style.background = previewStyle;
        bouquetPreview.textContent = previewText;
    }
    
    // Update price
    function updatePrice() {
        let total = assemblyFee;
        
        // Wrapper price
        const wrapperPrice = selectedWrapper ? selectedWrapper.price : 0;
        document.getElementById('wrapperPrice').textContent = `₱${(wrapperPrice * quantity).toFixed(2)}`;
        total += wrapperPrice * quantity;
        
        // Focal flower 1 price
        const focal1Price = selectedFocalFlower1 ? selectedFocalFlower1.price : 0;
        document.getElementById('focalFlower1Price').textContent = `₱${(focal1Price * quantity).toFixed(2)}`;
        total += focal1Price * quantity;
        
        // Focal flower 2 price
        const focal2Price = selectedFocalFlower2 ? selectedFocalFlower2.price : 0;
        document.getElementById('focalFlower2Price').textContent = `₱${(focal2Price * quantity).toFixed(2)}`;
        total += focal2Price * quantity;
        
        // Focal flower 3 price
        const focal3Price = selectedFocalFlower3 ? selectedFocalFlower3.price : 0;
        document.getElementById('focalFlower3Price').textContent = `₱${(focal3Price * quantity).toFixed(2)}`;
        total += focal3Price * quantity;
        
        // Greenery price
        const greeneryPrice = selectedGreenery ? selectedGreenery.price : 0;
        document.getElementById('greeneryPrice').textContent = `₱${(greeneryPrice * quantity).toFixed(2)}`;
        total += greeneryPrice * quantity;
        
        // Filler price
        const fillerPrice = selectedFiller ? selectedFiller.price : 0;
        document.getElementById('fillerPrice').textContent = `₱${(fillerPrice * quantity).toFixed(2)}`;
        total += fillerPrice * quantity;
        
        // Ribbon price
        const ribbonPrice = selectedRibbon ? selectedRibbon.price : 0;
        document.getElementById('ribbonPrice').textContent = `₱${(ribbonPrice * quantity).toFixed(2)}`;
        total += ribbonPrice * quantity;
        
        document.getElementById('totalPrice').textContent = `₱${total.toFixed(2)}`;
        document.getElementById('expandedTotalPrice').textContent = `₱${total.toFixed(2)}`;
    }
    
    // Form submission
    document.getElementById('bouquetCustomizationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!selectedWrapper && !selectedFocalFlower1 && !selectedFocalFlower2 && !selectedFocalFlower3 && !selectedGreenery && !selectedFiller && !selectedRibbon) {
            alert('Please select at least one component for your bouquet.');
            return;
        }
        
        // Here you would typically send the data to your backend
        console.log('Bouquet customization submitted:', {
            wrapper: selectedWrapper,
            focalFlower1: selectedFocalFlower1,
            focalFlower2: selectedFocalFlower2,
            focalFlower3: selectedFocalFlower3,
            greenery: selectedGreenery,
            filler: selectedFiller,
            ribbon: selectedRibbon,
            quantity: quantity
        });
        
        alert('Bouquet customization submitted successfully!');
    });
    
    // Add to cart functionality
    document.getElementById('addToCartBtn').addEventListener('click', function() {
        if (!selectedWrapper && !selectedFocalFlower1 && !selectedFocalFlower2 && !selectedFocalFlower3 && !selectedGreenery && !selectedFiller && !selectedRibbon) {
            alert('Please select at least one component for your bouquet.');
            return;
        }
        
        // Here you would typically add to cart
        console.log('Added to cart:', {
            wrapper: selectedWrapper,
            focalFlower1: selectedFocalFlower1,
            focalFlower2: selectedFocalFlower2,
            focalFlower3: selectedFocalFlower3,
            greenery: selectedGreenery,
            filler: selectedFiller,
            ribbon: selectedRibbon,
            quantity: quantity
        });
        
        alert('Bouquet added to cart successfully!');
    });
    
    // Price Summary Toggle
    const priceSummaryCard = document.getElementById('priceSummaryCard');
    const priceDetails = document.getElementById('priceDetails');
    const collapsedTotal = document.getElementById('collapsedTotal');
    const expandedTotalPrice = document.getElementById('expandedTotalPrice');
    
    priceSummaryCard.addEventListener('click', function() {
        if (priceDetails.style.display === 'none' || priceDetails.style.display === '') {
            // Expand: show details, hide collapsed total
            priceDetails.style.display = 'block';
            collapsedTotal.style.display = 'none';
        } else {
            // Collapse: hide details, show collapsed total
            priceDetails.style.display = 'none';
            collapsedTotal.style.display = 'flex';
        }
    });

    // Initialize
    updatePrice();
});
</script>
@endpush


