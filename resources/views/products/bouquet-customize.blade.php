@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-1" style="min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-10 col-xl-10"><div class="row justify-content-center">
            <div class="py-4" style="background: #CDE7C9; min-height: 100vh; border-radius: 1rem;">
                <div class="row h-100">
        <!-- Left Panel - Customization -->
        <div class="col-lg-6" style="background: #CDE7C9; padding: 0.5rem 2rem; overflow-y: auto; max-height: 100vh;">
            <div class="h-100 d-flex flex-column">
                <!-- Header -->
                <div class="text-center mb-2">
                    <h3 class="fw-bold text-dark mb-2">Customize your Desired Bouquet</h3>
                    <br>
                    <div class="d-flex justify-content-center mb-3">
                        <button class="btn btn-success btn-sm">
                            <i class="bi bi-info-circle me-1"></i>Notice to Customers
                        </button>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <!-- Customization Form -->
                <form id="bouquetCustomizationForm" class="flex-grow-1 d-flex flex-column">
                    @csrf
                    
                    <!-- Bouquet Wrapper Selection -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Choose Wrappers</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noWrapper" name="no_wrapper">
                                <label class="form-check-label fw-medium" for="noWrapper">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3" id="wrapperGrid">
                            @foreach(($items['Wrappers'] ?? []) as $wrap)
                            <div class="col-3">
                                <div class="wrapper-option" data-wrapper="{{ $wrap->name }}" data-price="{{ $wrap->price ?? 0 }}" data-image="{{ $wrap->image ? asset('storage/'.$wrap->image) : '' }}">
                                    <div class="wrapper-card position-relative">
                                        <div class="rounded-3" style="height: 120px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($wrap->image)
                                                <img src="{{ asset('storage/'.$wrap->image) }}" alt="{{ $wrap->name }}" style="max-height:120px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold" style="font-size: 8px; line-height: 1.1;">{{ $wrap->name }}</span>
                                            @endif
                                        </div>
                                        <div class="wrapper-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="wrapper-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $wrap->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($wrap->price ?? 0,2) }}</div>
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
                            <h5 class="fw-bold text-dark mb-0">Choose Fresh Flowers</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noFocalFlower1" name="no_focal_flower_1">
                                <label class="form-check-label fw-medium" for="noFocalFlower1">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3" id="focalFlowerGrid1">
                            @foreach(($items['Fresh Flowers'] ?? []) as $focal)
                            <div class="col-3">
                                <div class="focal1-option" data-flower="{{ $focal->name }}" data-price="{{ $focal->price ?? 0 }}" data-image="{{ $focal->image ? asset('storage/'.$focal->image) : '' }}">
                                    <div class="flower-card position-relative">
                                        <div class="rounded-3" style="height: 120px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($focal->image)
                                                <img src="{{ asset('storage/'.$focal->image) }}" alt="{{ $focal->name }}" style="max-height:120px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold" style="font-size: 8px; line-height: 1.1;">{{ $focal->name }}</span>
                                            @endif
                                        </div>
                                        <div class="flower-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        </div>
                                        <div class="flower-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $focal->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($focal->price ?? 0,2) }}</div>
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
                            @foreach(($items['Greenery'] ?? []) as $greenery)
                            <div class="col-3">
                                <div class="greenery-option" data-greenery="{{ $greenery->name }}" data-price="{{ $greenery->price ?? 0 }}" data-image="{{ $greenery->image ? asset('storage/'.$greenery->image) : '' }}">
                                    <div class="greenery-card position-relative">
                                        <div class="rounded-3" style="height: 120px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($greenery->image)
                                                <img src="{{ asset('storage/'.$greenery->image) }}" alt="{{ $greenery->name }}" style="max-height:120px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold" style="font-size: 8px; line-height: 1.1;">{{ $greenery->name }}</span>
                                            @endif
                                        </div>
                                        <div class="greenery-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="greenery-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $greenery->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($greenery->price ?? 0,2) }}</div>
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
                            <h5 class="fw-bold text-dark mb-0">Choose Artificial Flowers</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="noFillers" name="no_fillers">
                                <label class="form-check-label fw-medium" for="noFillers">
                                    Do not include
                                </label>
                            </div>
                        </div>
                        <div class="row g-3">
                            @if(isset($items['Artificial Flowers']) && count($items['Artificial Flowers']) > 0)
                                @foreach($items['Artificial Flowers'] as $filler)
                            <div class="col-3">
                                <div class="filler-option" data-filler="{{ $filler->name }}" data-price="{{ $filler->price ?? 0 }}" data-image="{{ $filler->image ? asset('storage/'.$filler->image) : '' }}">
                                    <div class="filler-card position-relative">
                                        <div class="rounded-3" style="height: 120px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($filler->image)
                                                <img src="{{ asset('storage/'.$filler->image) }}" alt="{{ $filler->name }}" style="max-height:120px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold" style="font-size: 8px; line-height: 1.1;">{{ $filler->name }}</span>
                                            @endif
                                        </div>
                                        <div class="filler-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="filler-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $filler->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($filler->price ?? 0,2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                @endforeach
                            @else
                                <div class="col-12 text-center text-muted">
                                    <p>No artificial flowers available at the moment.</p>
                                </div>
                            @endif
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
                            <h5 class="fw-bold text-dark mb-0">Choose Ribbons</h5>
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
                                <div class="ribbon-option" data-ribbon="{{ $ribbon->name }}" data-price="{{ $ribbon->price ?? 0 }}" data-image="{{ $ribbon->image ? asset('storage/'.$ribbon->image) : '' }}">
                                    <div class="ribbon-card position-relative">
                                        <div class="rounded-3" style="height: 120px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($ribbon->image)
                                                <img src="{{ asset('storage/'.$ribbon->image) }}" alt="{{ $ribbon->name }}" style="max-height:120px; max-width:100%; object-fit:cover;">
                                            @else
                                                <span class="text-muted fw-bold" style="font-size: 8px; line-height: 1.1;">{{ $ribbon->name }}</span>
                                            @endif
                                        </div>
                                        <div class="ribbon-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="ribbon-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $ribbon->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($ribbon->price ?? 0,2) }}</div>
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
                <!-- Regular Bouquet Preview -->
                <div class="img-fluid" style="width: 100%; height: 320px; background: #F1F6F1; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
                    <!-- Base Bouquet Shape -->
                    <div class="bouquet-base" style="position: absolute; width: 80%; height: 60%; background: linear-gradient(45deg, #ff6b6b, #4ecdc4); border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%; opacity: 0.3; transition: all 0.5s ease;"></div>
                    
                    <!-- Wrapper Layer -->
                    <div id="wrapperLayer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgWrapper" alt="wrapper" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:none; z-index:10; pointer-events:none;"/>
                    
                    <!-- Flower Layer (single after removal of 2 & 3) -->
                    <div id="focal1Layer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgFlower" alt="flower" style="position:absolute; width:20%; height:auto; object-fit:contain; bottom:45%; left:50%; transform:translateX(-50%); display:none; z-index:60; pointer-events:none;"/>
                    
                    <!-- Greenery Layer -->
                    <div id="greeneryLayer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgGreenery" alt="greenery" style="position:absolute; width:82%; height:auto; object-fit:contain; bottom:16%; left:50%; transform:translateX(-50%); display:none; z-index:20; opacity:.95; pointer-events:none;"/>
                    
                    <!-- Filler Layer -->
                    <div id="fillerLayer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgFiller" alt="filler" style="position:absolute; width:70%; height:auto; object-fit:contain; bottom:18%; left:50%; transform:translateX(-50%); display:none; z-index:25; pointer-events:none;"/>
                    
                    <!-- Ribbon Layer -->
                    <div id="ribbonLayer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgRibbon" alt="ribbon" style="position:absolute; width:52%; height:auto; object-fit:contain; bottom:0%; left:50%; transform:translateX(-50%); display:none; z-index:80; pointer-events:none;"/>
                    
                    <!-- Default Message -->
                    <div id="defaultMessage" class="text-center text-muted" style="position: relative; z-index: 10;">
                        <i class="bi bi-flower1 fs-1 mb-2"></i>
                        <div class="fw-bold">Select materials to create your bouquet</div>
                        <small>Each component adds beauty to your floral arrangement!</small>
                    </div>
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
                        <span id="totalPrice">₱0.00</span>
                    </div>
                        
                        <!-- Collapsible Details -->
                        <div id="priceDetails" style="display: none; overflow: hidden; transition: all 0.3s ease;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span>Wrappers:</span>
                                <select class="form-select form-select-sm" style="width: auto; min-width: 80px;" id="wrapperColorSelect">
                                    <option value="">Color</option>
                                    <option value="white">White</option>
                                    <option value="pink">Pink</option>
                                    <option value="green">Green</option>
                                    <option value="brown">Brown</option>
                                    <option value="gold">Gold</option>
                                </select>
                            </div>
                            <span id="wrapperPrice">₱0.00</span>
                        </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span>Fresh Flowers:</span>
                                    <select class="form-select form-select-sm" style="width: auto; min-width: 80px;" id="freshFlowerColorSelect">
                                        <option value="">Color</option>
                                        <option value="red">Red</option>
                                        <option value="pink">Pink</option>
                                        <option value="white">White</option>
                                        <option value="yellow">Yellow</option>
                                        <option value="purple">Purple</option>
                                    </select>
                                    <input type="number" class="form-control form-control-sm text-center" id="freshFlowerQty" value="1" min="1" max="10" style="width: 50px; padding: 4px; font-size: 12px;">
                                </div>
                                <span id="focalFlower1Price">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Greenery:</span>
                                <span id="greeneryPrice">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span>Artificial Flowers:</span>
                                    <select class="form-select form-select-sm" style="width: auto; min-width: 80px;" id="artificialFlowerColorSelect">
                                        <option value="">Color</option>
                                        <option value="white">White</option>
                                        <option value="pink">Pink</option>
                                        <option value="yellow">Yellow</option>
                                        <option value="purple">Purple</option>
                                        <option value="blue">Blue</option>
                                    </select>
                                    <input type="number" class="form-control form-control-sm text-center" id="artificialFlowerQty" value="1" min="1" max="10" style="width: 50px; padding: 4px; font-size: 12px;">
                                </div>
                                <span id="fillerPrice">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span>Ribbons:</span>
                                    <select class="form-select form-select-sm" style="width: auto; min-width: 80px;" id="ribbonColorSelect">
                                        <option value="">Color</option>
                                        <option value="red">Red</option>
                                        <option value="pink">Pink</option>
                                        <option value="white">White</option>
                                        <option value="gold">Gold</option>
                                        <option value="silver">Silver</option>
                                        <option value="green">Green</option>
                                    </select>
                                </div>
                                <span id="ribbonPrice">₱0.00</span>
                            </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Assembly Fee:</span>
                        <span id="assemblyFeePrice">₱0.00</span>
                    </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span id="expandedTotalPrice">₱0.00</span>
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
<input type="hidden" id="selectedMoneyAmount" name="money_amount" value="">
<input type="hidden" id="bouquetType" name="bouquet_type" value="regular">

@endsection

@push('styles')
<style>
.wrapper-option, .focal1-option, .greenery-option, .filler-option, .ribbon-option, .money-option, .occasion-option {
    cursor: pointer;
    transition: all 0.6s ease;
}

.wrapper-option:hover, .focal1-option:hover, .greenery-option:hover, .filler-option:hover, .ribbon-option:hover, .money-option:hover, .occasion-option:hover {
    transform: translateY(-2px);
}

.wrapper-card, .flower-card, .greenery-card, .filler-card, .ribbon-card, .money-card, .occasion-card {
    border: 2px solid transparent;
    border-radius: 0.75rem;
    transition: all 0.9s ease;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    height: 180px;
    display: flex;
    flex-direction: column;
}

.wrapper-option.selected .wrapper-card,
.focal1-option.selected .flower-card,
.focal1-option.selected .flower-card,
.greenery-option.selected .greenery-card,
.filler-option.selected .filler-card,
.ribbon-option.selected .ribbon-card,
.money-option.selected .money-card,
.occasion-option.selected .occasion-card {
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

/* Money Bouquet Animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

    .money-bouquet-shimmer {
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
    }

    /* Cooking-style animations */
    @keyframes ingredientAdd {
        0% { 
            transform: scale(0) rotate(180deg); 
            opacity: 0; 
        }
        50% { 
            transform: scale(1.2) rotate(90deg); 
            opacity: 0.8; 
        }
        100% { 
            transform: scale(1) rotate(0deg); 
            opacity: 1; 
        }
    }

    @keyframes cookingBubble {
        0% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(-10px) scale(1.1); }
        100% { transform: translateY(0px) scale(1); }
    }

    @keyframes sizzle {
        0% { transform: translateX(0px); }
        25% { transform: translateX(-2px); }
        75% { transform: translateX(2px); }
        100% { transform: translateX(0px); }
    }

    .ingredient-layer {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .ingredient-added {
        animation: ingredientAdd 0.8s ease-out;
    }

    .cooking-effect {
        animation: cookingBubble 0.6s ease-in-out;
    }

    .sizzle-effect {
        animation: sizzle 0.3s ease-in-out 3;
    }

    .recipe-ingredient {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        border: 2px solid #f39c12;
        border-radius: 20px;
        padding: 8px 16px;
        margin: 4px;
        display: inline-block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #8b4513;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .recipe-ingredient:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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
    let selectedMoneyAmount = null;
    let selectedOccasion = null;
    let quantity = 1;
    let bouquetType = 'regular'; // 'regular' or 'money'
    
    const assemblyFee = 150;
    
    // Bouquet type toggle handlers
    // Bouquet type toggle handlers - commented out since buttons were removed
    /*
    document.getElementById('regularBouquetBtn').addEventListener('click', function() {
        bouquetType = 'regular';
        this.classList.remove('btn-outline-success');
        this.classList.add('btn-success');
        document.getElementById('moneyBouquetBtn').classList.remove('btn-success');
        document.getElementById('moneyBouquetBtn').classList.add('btn-outline-success');
        
        // Show/hide sections
        document.getElementById('moneyBouquetSection').style.display = 'none';
        document.querySelectorAll('.mb-4:not(#moneyBouquetSection)').forEach(section => {
            if (!section.id.includes('moneyBouquet')) {
                section.style.display = 'block';
            }
        });
        
        // Switch preview
        document.getElementById('regularBouquetPreview').style.display = 'flex';
        document.getElementById('moneyBouquetPreview').style.display = 'none';
        
        // Reset money selection
        selectedMoneyAmount = null;
        document.getElementById('selectedMoneyAmount').value = '';
        document.getElementById('bouquetType').value = 'regular';
        
        updatePrice();
    });
    */
    
    /*
    document.getElementById('moneyBouquetBtn').addEventListener('click', function() {
        bouquetType = 'money';
        this.classList.remove('btn-outline-success');
        this.classList.add('btn-success');
        document.getElementById('regularBouquetBtn').classList.remove('btn-success');
        document.getElementById('regularBouquetBtn').classList.add('btn-outline-success');
        
        // Show/hide sections
        document.getElementById('moneyBouquetSection').style.display = 'block';
        document.querySelectorAll('.mb-4:not(#moneyBouquetSection)').forEach(section => {
            if (!section.id.includes('moneyBouquet')) {
                section.style.display = 'none';
            }
        });
        
        // Switch preview
        document.getElementById('regularBouquetPreview').style.display = 'none';
        document.getElementById('moneyBouquetPreview').style.display = 'flex';
        
        // Reset other selections
        selectedWrapper = null;
        selectedFocalFlower1 = null;
        selectedFocalFlower2 = null;
        selectedFocalFlower3 = null;
        selectedGreenery = null;
        selectedFiller = null;
        selectedRibbon = null;
        
        document.getElementById('bouquetType').value = 'money';
        
        updatePrice();
    });
    */
    
    // Money amount selection handlers - commented out since money bouquet was removed
    /*
    document.querySelectorAll('.money-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.money-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.money-check').style.display = 'none';
            });
            
            // Add selection to clicked option
            this.classList.add('selected');
            this.querySelector('.money-check').style.display = 'block';
            
            selectedMoneyAmount = {
                amount: this.dataset.amount,
                price: parseFloat(this.dataset.price)
            };
            
            document.getElementById('selectedMoneyAmount').value = selectedMoneyAmount.amount;
            updateMoneyPreview();
            updatePrice();
        });
    });
    */
    
    // Occasion selection handlers - commented out since occasion section was removed
    /*
    document.querySelectorAll('.occasion-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.occasion-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('.occasion-check').style.display = 'none';
            });
            
            // Add selection to clicked option
            this.classList.add('selected');
            this.querySelector('.occasion-check').style.display = 'block';
            
            selectedOccasion = {
                slug: this.dataset.occasion,
                theme: this.dataset.theme,
                flowers: JSON.parse(this.dataset.flowers),
                wrappers: JSON.parse(this.dataset.wrappers),
                ribbons: JSON.parse(this.dataset.ribbons),
                basePrice: parseFloat(this.dataset.basePrice)
            };
            
            // Auto-select recommended components based on occasion
            autoSelectOccasionComponents();
            updatePreview();
            updatePrice();
        });
    });
    */
    
    // Auto-select recommended components based on occasion - commented out since occasion section was removed
    /*
    function autoSelectOccasionComponents() {
        if (!selectedOccasion) return;
        
        // Clear previous selections
        clearAllSelections();
        
        // Auto-select recommended wrapper
        if (selectedOccasion.wrappers && selectedOccasion.wrappers.length > 0) {
            const wrapperOption = document.querySelector(`[data-wrapper="${selectedOccasion.wrappers[0]}"]`);
            if (wrapperOption) {
                wrapperOption.click();
            }
        }
        
        // Auto-select recommended flowers
        if (selectedOccasion.flowers && selectedOccasion.flowers.length > 0) {
            selectedOccasion.flowers.slice(0, 3).forEach((flower, index) => {
                const flowerOption = document.querySelector(`[data-flower="${flower}"]`);
                if (flowerOption) {
                    flowerOption.click();
                }
            });
        }
        
        // Auto-select recommended ribbon
        if (selectedOccasion.ribbons && selectedOccasion.ribbons.length > 0) {
            const ribbonOption = document.querySelector(`[data-ribbon="${selectedOccasion.ribbons[0]}"]`);
            if (ribbonOption) {
                ribbonOption.click();
            }
        }
    }
    */
    
    // Clear all component selections
    function clearAllSelections() {
        document.querySelectorAll('.wrapper-option, .focal1-option, .focal2-option, .focal3-option, .greenery-option, .filler-option, .ribbon-option').forEach(option => {
            option.classList.remove('selected');
            const checkElement = option.querySelector('.wrapper-check, .flower-check, .greenery-check, .filler-check, .ribbon-check');
            if (checkElement) {
                checkElement.style.display = 'none';
            }
        });
        
        selectedWrapper = null;
        selectedFocalFlower1 = null;
        selectedFocalFlower2 = null;
        selectedFocalFlower3 = null;
        selectedGreenery = null;
        selectedFiller = null;
        selectedRibbon = null;
    }
    
    // Wrapper selection handlers
    document.querySelectorAll('.wrapper-option').forEach(option => {
        option.addEventListener('click', function() {
            // Check if this option is already selected
            if (this.classList.contains('selected')) {
                // If already selected, unselect it
                this.classList.remove('selected');
                this.querySelector('.wrapper-check').style.display = 'none';
                selectedWrapper = null;
                document.getElementById('selectedWrapper').value = '';
            } else {
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
            }
            
            updatePreview();
            updatePrice();
        });
    });
    
    // Focal Flower 1 selection handlers
    document.querySelectorAll('.focal1-option').forEach(option => {
        option.addEventListener('click', function() {
            // Check if this option is already selected
            if (this.classList.contains('selected')) {
                // If already selected, unselect it
                this.classList.remove('selected');
                this.querySelector('.flower-check').style.display = 'none';
                selectedFocalFlower1 = null;
                document.getElementById('selectedFocalFlower1').value = '';
            } else {
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
            }
            
            updatePreview();
            updatePrice();
        });
    });
    
    // Focal Flower 2 selection handlers (removed)
    /* document.querySelectorAll('.focal2-option').forEach(option => {
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
    }); */
    
    // Focal Flower 3 selection handlers (removed)
    /* document.querySelectorAll('.focal3-option').forEach(option => {
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
    }); */
    
    // Greenery selection handlers
    document.querySelectorAll('.greenery-option').forEach(option => {
        option.addEventListener('click', function() {
            // Check if this option is already selected
            if (this.classList.contains('selected')) {
                // If already selected, unselect it
                this.classList.remove('selected');
                this.querySelector('.greenery-check').style.display = 'none';
                selectedGreenery = null;
                document.getElementById('selectedGreenery').value = '';
            } else {
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
            }
            
            updatePreview();
            updatePrice();
        });
    });
    
    // Filler selection handlers
    document.querySelectorAll('.filler-option').forEach(option => {
        option.addEventListener('click', function() {
            // Check if this option is already selected
            if (this.classList.contains('selected')) {
                // If already selected, unselect it
                this.classList.remove('selected');
                this.querySelector('.filler-check').style.display = 'none';
                selectedFiller = null;
                document.getElementById('selectedFiller').value = '';
            } else {
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
            }
            
            updatePreview();
            updatePrice();
        });
    });
    
    // Ribbon selection handlers
    document.querySelectorAll('.ribbon-option').forEach(option => {
        option.addEventListener('click', function() {
            // Check if this option is already selected
            if (this.classList.contains('selected')) {
                // If already selected, unselect it
                this.classList.remove('selected');
                this.querySelector('.ribbon-check').style.display = 'none';
                selectedRibbon = null;
                document.getElementById('selectedRibbon').value = '';
            } else {
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
            }
            
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
    
    // Fresh Flower quantity controls
    document.getElementById('freshFlowerQty').addEventListener('change', function(e) {
        e.stopPropagation();
        let qty = Math.max(1, Math.min(10, parseInt(this.value) || 1));
        this.value = qty;
        updatePrice();
    });
    
    document.getElementById('freshFlowerQty').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Artificial Flower quantity controls
    document.getElementById('artificialFlowerQty').addEventListener('change', function(e) {
        e.stopPropagation();
        let qty = Math.max(1, Math.min(10, parseInt(this.value) || 1));
        this.value = qty;
        updatePrice();
    });
    
    document.getElementById('artificialFlowerQty').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Color selection handlers
    document.getElementById('wrapperColorSelect').addEventListener('change', function(e) {
        e.stopPropagation();
        console.log('Wrapper color selected:', this.value);
        // Add color-specific logic here if needed
    });
    
    document.getElementById('wrapperColorSelect').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    document.getElementById('freshFlowerColorSelect').addEventListener('change', function(e) {
        e.stopPropagation();
        console.log('Fresh flower color selected:', this.value);
        // Add color-specific logic here if needed
    });
    
    document.getElementById('freshFlowerColorSelect').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    document.getElementById('artificialFlowerColorSelect').addEventListener('change', function(e) {
        e.stopPropagation();
        console.log('Artificial flower color selected:', this.value);
        // Add color-specific logic here if needed
    });
    
    document.getElementById('artificialFlowerColorSelect').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    document.getElementById('ribbonColorSelect').addEventListener('change', function(e) {
        e.stopPropagation();
        console.log('Ribbon color selected:', this.value);
        // Add color-specific logic here if needed
    });
    
    document.getElementById('ribbonColorSelect').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Prevent clicks inside price details from closing the summary
    document.getElementById('priceDetails').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Update money preview
    function updateMoneyPreview() {
        if (selectedMoneyAmount) {
            const moneyAmountElement = document.querySelector('.money-amount');
            if (moneyAmountElement) {
                moneyAmountElement.textContent = `₱${selectedMoneyAmount.amount}`;
            }
        }
    }
    
    // Update preview (cooking-style layered approach)
    function updatePreview() {
        if (bouquetType === 'money') {
            updateMoneyPreview();
            return;
        }
        
        // Hide default message when materials are selected
        const defaultMessage = document.getElementById('defaultMessage');
        const hasMaterials = selectedWrapper || selectedFocalFlower1 || selectedGreenery || selectedFiller || selectedRibbon;
        
        if (hasMaterials) {
            defaultMessage.style.display = 'none';
        } else {
            defaultMessage.style.display = 'block';
        }
        
        // Update wrapper layer
        const wrapperLayer = document.getElementById('wrapperLayer');
        if (selectedWrapper) {
            wrapperLayer.style.opacity = '1';
            wrapperLayer.style.background = getWrapperStyle(selectedWrapper.name);
            wrapperLayer.classList.add('ingredient-added');
            const imgW = document.getElementById('imgWrapper');
            if (imgW) {
                const sel = document.querySelector('.wrapper-option.selected');
                imgW.src = sel && sel.dataset.image ? sel.dataset.image : '';
                imgW.style.display = imgW.src ? 'block' : 'none';
            }
        } else {
            wrapperLayer.style.opacity = '0';
            wrapperLayer.classList.remove('ingredient-added');
            const imgW = document.getElementById('imgWrapper');
            if (imgW) imgW.style.display = 'none';
        }
        
        // Update flower layer (single)
        updateFlowerLayer('focal1Layer', selectedFocalFlower1);
        
        // Update greenery layer
        const greeneryLayer = document.getElementById('greeneryLayer');
        if (selectedGreenery) {
            greeneryLayer.style.opacity = '0.8';
            greeneryLayer.style.background = getGreeneryStyle(selectedGreenery.name);
            greeneryLayer.classList.add('ingredient-added');
            const imgG = document.getElementById('imgGreenery');
            if (imgG) {
                const sel = document.querySelector('.greenery-option.selected');
                imgG.src = sel && sel.dataset.image ? sel.dataset.image : '';
                imgG.style.display = imgG.src ? 'block' : 'none';
            }
        } else {
            greeneryLayer.style.opacity = '0';
            greeneryLayer.classList.remove('ingredient-added');
            const imgG = document.getElementById('imgGreenery');
            if (imgG) imgG.style.display = 'none';
        }
        
        // Update filler layer
        const fillerLayer = document.getElementById('fillerLayer');
        if (selectedFiller) {
            fillerLayer.style.opacity = '0.6';
            fillerLayer.style.background = getFillerStyle(selectedFiller.name);
            fillerLayer.classList.add('ingredient-added');
            const imgF = document.getElementById('imgFiller');
            if (imgF) {
                const sel = document.querySelector('.filler-option.selected');
                imgF.src = sel && sel.dataset.image ? sel.dataset.image : '';
                imgF.style.display = imgF.src ? 'block' : 'none';
            }
        } else {
            fillerLayer.style.opacity = '0';
            fillerLayer.classList.remove('ingredient-added');
            const imgF = document.getElementById('imgFiller');
            if (imgF) imgF.style.display = 'none';
        }
        
        // Update ribbon layer
        const ribbonLayer = document.getElementById('ribbonLayer');
        if (selectedRibbon) {
            ribbonLayer.style.opacity = '0.9';
            ribbonLayer.style.background = getRibbonStyle(selectedRibbon.name);
            ribbonLayer.classList.add('ingredient-added');
            const imgR = document.getElementById('imgRibbon');
            if (imgR) {
                const sel = document.querySelector('.ribbon-option.selected');
                imgR.src = sel && sel.dataset.image ? sel.dataset.image : '';
                imgR.style.display = imgR.src ? 'block' : 'none';
            }
        } else {
            ribbonLayer.style.opacity = '0';
            ribbonLayer.classList.remove('ingredient-added');
            const imgR = document.getElementById('imgRibbon');
            if (imgR) imgR.style.display = 'none';
        }
        
        // Update recipe summary
        updateRecipeSummary();
    }
    
    // Helper functions for ingredient styles
    function updateFlowerLayer(layerId, flower) {
        const layer = document.getElementById(layerId);
        if (flower) {
            layer.style.opacity = '0.7';
            layer.style.background = getFlowerStyle(flower.name);
            layer.classList.add('ingredient-added');
            const imgFl = document.getElementById('imgFlower');
            if (imgFl) {
                const sel = document.querySelector('.focal1-option.selected');
                imgFl.src = sel && sel.dataset.image ? sel.dataset.image : '';
                imgFl.style.display = imgFl.src ? 'block' : 'none';
            }
        } else {
            layer.style.opacity = '0';
            layer.classList.remove('ingredient-added');
            const imgFl = document.getElementById('imgFlower');
            if (imgFl) imgFl.style.display = 'none';
        }
    }
    
    function getWrapperStyle(wrapperName) {
        const styles = {
            'White Wrapping Paper': 'linear-gradient(45deg, #f8f9fa, #e9ecef)',
            'Brown Kraft Paper': 'linear-gradient(45deg, #8b4513, #a0522d)',
            'Pink Tissue Paper': 'linear-gradient(45deg, #ffc1cc, #ffb3ba)',
            'Green Cellophane': 'linear-gradient(45deg, #90ee90, #98fb98)',
            'Gold Foil Paper': 'linear-gradient(45deg, #ffd700, #ffed4e)'
        };
        return styles[wrapperName] || 'linear-gradient(45deg, #f8f9fa, #e9ecef)';
    }
    
    function getFlowerStyle(flowerName) {
        const styles = {
            'Red Roses': 'radial-gradient(circle, #ff6b6b, #ee5a52)',
            'Pink Peonies': 'radial-gradient(circle, #ff9ff3, #f368e0)',
            'White Lilies': 'radial-gradient(circle, #f8f9fa, #e9ecef)',
            'Yellow Sunflowers': 'radial-gradient(circle, #ffd700, #ffed4e)',
            'Purple Lavender': 'radial-gradient(circle, #dda0dd, #da70d6)',
            'Orange Marigolds': 'radial-gradient(circle, #ffa500, #ff8c00)'
        };
        return styles[flowerName] || 'radial-gradient(circle, #ff6b6b, #ee5a52)';
    }
    
    function getGreeneryStyle(greeneryName) {
        const styles = {
            'Eucalyptus Leaves': 'linear-gradient(45deg, #228b22, #32cd32)',
            'Fern Fronds': 'linear-gradient(45deg, #006400, #228b22)',
            'Baby\'s Breath': 'linear-gradient(45deg, #f0f8ff, #e6f3ff)',
            'Ruscus Leaves': 'linear-gradient(45deg, #2e8b57, #3cb371)',
            'Asparagus Fern': 'linear-gradient(45deg, #9acd32, #adff2f)'
        };
        return styles[greeneryName] || 'linear-gradient(45deg, #228b22, #32cd32)';
    }
    
    function getFillerStyle(fillerName) {
        const styles = {
            'White Baby\'s Breath': 'radial-gradient(circle, #f0f8ff, #e6f3ff)',
            'Pink Statice': 'radial-gradient(circle, #ffc1cc, #ffb3ba)',
            'Purple Limonium': 'radial-gradient(circle, #dda0dd, #da70d6)',
            'White Waxflower': 'radial-gradient(circle, #f8f9fa, #e9ecef)',
            'Yellow Solidago': 'radial-gradient(circle, #ffd700, #ffed4e)'
        };
        return styles[fillerName] || 'radial-gradient(circle, #f0f8ff, #e6f3ff)';
    }
    
    function getRibbonStyle(ribbonName) {
        const styles = {
            'Red Satin Ribbon': 'linear-gradient(90deg, #dc143c, #b22222)',
            'White Organza Ribbon': 'linear-gradient(90deg, #f8f9fa, #e9ecef)',
            'Gold Curling Ribbon': 'linear-gradient(90deg, #ffd700, #ffed4e)',
            'Pink Grosgrain Ribbon': 'linear-gradient(90deg, #ffc1cc, #ffb3ba)',
            'Green Velvet Ribbon': 'linear-gradient(90deg, #228b22, #32cd32)'
        };
        return styles[ribbonName] || 'linear-gradient(90deg, #dc143c, #b22222)';
    }
    
        // Update price
        function updatePrice() {
            let total = 0;
            
            // Add occasion base price if selected
            if (selectedOccasion) {
                total += selectedOccasion.basePrice;
            }
        
        // Wrapper price
        const wrapperPrice = selectedWrapper ? selectedWrapper.price : 0;
        document.getElementById('wrapperPrice').textContent = `₱${(wrapperPrice * quantity).toFixed(2)}`;
        total += wrapperPrice * quantity;
        
        // Focal flower 1 price (with individual quantity)
        const focal1Price = selectedFocalFlower1 ? selectedFocalFlower1.price : 0;
        const freshFlowerQty = parseInt(document.getElementById('freshFlowerQty').value) || 1;
        document.getElementById('focalFlower1Price').textContent = `₱${(focal1Price * freshFlowerQty * quantity).toFixed(2)}`;
        total += focal1Price * freshFlowerQty * quantity;
        
        // Focal flower 2 price (removed from UI)
        const focal2Price = selectedFocalFlower2 ? selectedFocalFlower2.price : 0;
        total += focal2Price * quantity;
        
        // Focal flower 3 price (removed from UI)
        const focal3Price = 0;
        
        // Greenery price
        const greeneryPrice = selectedGreenery ? selectedGreenery.price : 0;
        document.getElementById('greeneryPrice').textContent = `₱${(greeneryPrice * quantity).toFixed(2)}`;
        total += greeneryPrice * quantity;
        
        // Filler price (with individual quantity)
        const fillerPrice = selectedFiller ? selectedFiller.price : 0;
        const artificialFlowerQty = parseInt(document.getElementById('artificialFlowerQty').value) || 1;
        document.getElementById('fillerPrice').textContent = `₱${(fillerPrice * artificialFlowerQty * quantity).toFixed(2)}`;
        total += fillerPrice * artificialFlowerQty * quantity;
        
        // Ribbon price
        const ribbonPrice = selectedRibbon ? selectedRibbon.price : 0;
        document.getElementById('ribbonPrice').textContent = `₱${(ribbonPrice * quantity).toFixed(2)}`;
        total += ribbonPrice * quantity;
        
        // Money amount price (if money bouquet)
        if (bouquetType === 'money') {
            const moneyPrice = selectedMoneyAmount ? selectedMoneyAmount.price : 0;
            total += moneyPrice * quantity;
        }
        
        // Add assembly fee only if there are materials selected
        const hasMaterials = selectedWrapper || selectedFocalFlower1 || selectedGreenery || selectedFiller || selectedRibbon || selectedMoneyAmount;
        if (hasMaterials) {
            const assemblyFeeTotal = assemblyFee * quantity;
            document.getElementById('assemblyFeePrice').textContent = `₱${assemblyFeeTotal.toFixed(2)}`;
            total += assemblyFeeTotal;
        } else {
            document.getElementById('assemblyFeePrice').textContent = `₱0.00`;
        }
        
        document.getElementById('totalPrice').textContent = `₱${total.toFixed(2)}`;
        document.getElementById('expandedTotalPrice').textContent = `₱${total.toFixed(2)}`;
    }
    
    // Form submission
    document.getElementById('bouquetCustomizationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (bouquetType === 'money') {
            if (!selectedMoneyAmount) {
                alert('Please select a money amount for your money bouquet.');
                return;
            }
        } else {
            if (!selectedWrapper && !selectedFocalFlower1 && !selectedGreenery && !selectedFiller && !selectedRibbon) {
                alert('Please select at least one material for your bouquet.');
                return;
            }
        }
        
        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('bouquet_type', bouquetType);
        formData.append('quantity', quantity);
        
        if (bouquetType === 'money') {
            formData.append('money_amount', selectedMoneyAmount.amount);
        } else {
            if (selectedWrapper) formData.append('wrapper', selectedWrapper.name);
            if (selectedFocalFlower1) formData.append('focal_flower_1', selectedFocalFlower1.name);
            
            if (selectedGreenery) formData.append('greenery', selectedGreenery.name);
            if (selectedFiller) formData.append('filler', selectedFiller.name);
            if (selectedRibbon) formData.append('ribbon', selectedRibbon.name);
        }
        
        // Submit to backend
        fetch('{{ route("customer.products.bouquet-customize.store") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Reset form
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the form.');
        });
    });
    
    // Add to cart functionality
    document.getElementById('addToCartBtn').addEventListener('click', function() {
        if (bouquetType === 'money') {
            if (!selectedMoneyAmount) {
                alert('Please select a money amount for your money bouquet.');
                return;
            }
        } else {
            if (!selectedWrapper && !selectedFocalFlower1 && !selectedGreenery && !selectedFiller && !selectedRibbon) {
                alert('Please select at least one material for your bouquet.');
                return;
            }
        }
        
        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('bouquet_type', bouquetType);
        formData.append('quantity', quantity);
        
        if (bouquetType === 'money') {
            formData.append('money_amount', selectedMoneyAmount.amount);
        } else {
            if (selectedWrapper) formData.append('wrapper', selectedWrapper.name);
            if (selectedFocalFlower1) formData.append('focal_flower_1', selectedFocalFlower1.name);
            
            if (selectedGreenery) formData.append('greenery', selectedGreenery.name);
            if (selectedFiller) formData.append('filler', selectedFiller.name);
            if (selectedRibbon) formData.append('ribbon', selectedRibbon.name);
        }
        
        // Submit to backend
        fetch('{{ route("customer.products.bouquet-customize.add-to-cart") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Added!',
                    text: data.message,
                    confirmButtonColor: '#4CAF50'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while adding to cart.' });
        });
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

    // Add cooking animation effect
    function addCookingAnimation(element) {
        element.classList.add('cooking-effect');
        setTimeout(() => {
            element.classList.remove('cooking-effect');
        }, 600);
    }

    // Update material counters (like flower arrangement)
    function updateIngredientCounters() {
        const counters = {
            wrapper: selectedWrapper ? 1 : 0,
            flowers: (selectedFocalFlower1 ? 1 : 0),
            greenery: selectedGreenery ? 1 : 0,
            filler: selectedFiller ? 1 : 0,
            ribbon: selectedRibbon ? 1 : 0
        };
        
        // Update visual counters if they exist
        Object.keys(counters).forEach(material => {
            const counter = document.getElementById(`${material}Counter`);
            if (counter) {
                counter.textContent = counters[material];
                if (counters[material] > 0) {
                    counter.parentElement.classList.add('ingredient-added');
                }
            }
        });
    }

    // Update materials summary (like flower arrangement)
    function updateRecipeSummary() {
        const recipeSummary = document.getElementById('recipeSummary');
        if (!recipeSummary) return;
        
        const materials = [];
        
        if (selectedWrapper) materials.push(`📦 ${selectedWrapper.name}`);
        if (selectedFocalFlower1) materials.push(`🌸 ${selectedFocalFlower1.name}`);
        
        if (selectedGreenery) materials.push(`🌿 ${selectedGreenery.name}`);
        if (selectedFiller) materials.push(`✨ ${selectedFiller.name}`);
        if (selectedRibbon) materials.push(`🎀 ${selectedRibbon.name}`);
        
        if (materials.length === 0) {
            recipeSummary.innerHTML = '<em>Select materials to see your arrangement components...</em>';
        } else {
            recipeSummary.innerHTML = materials.map(material => 
                `<span class="recipe-ingredient">${material}</span>`
            ).join(' ');
        }
    }
    </script>
    @endpush


