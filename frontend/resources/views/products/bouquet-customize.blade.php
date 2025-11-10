@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-2" style="min-height: 90vh;">
    <div class="row justify-content-center">
        <div class="col-10 col-xl-9"><div class="row justify-content-center">
            <div class="py-4 customize-shell" style="background: #CDE7C9; min-height: 20vh; border-radius: 1rem;">
                <div class="row h-100">
        <!-- Left Panel - Customization -->
        <div class="col-lg-6 order-2 order-lg-1" style="background: #CDE7C9; padding: 0.5rem 2rem; overflow-y: auto; max-height: 78vh;">
            <div class="h-100 d-flex flex-column">
                <!-- Header -->
                <div class="text-center mb-2">
                    <h4 class="fw-bold text-dark mb-2">Customize your Desired Bouquet</h4>
                    <br>
                    <div class="d-flex justify-content-center mb-3">
                        <button id="noticeToggleBtn" class="btn btn-success btn-sm">
                            <i class="bi bi-info-circle me-1"></i>Notice to Customers
                        </button>
                    </div>
                </div>

                <!-- Notice to Customers Section -->
                <div id="noticeSection" class="notice-section" style="display: none; background: #E6F5E6; padding: 2rem; border-radius: 1rem; margin-bottom: 1rem;">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold text-dark mb-3" style="font-size: 1.2rem;">
                            <i class="bi bi-flower1 text-danger me-2"></i>Notice to Customers - Bouquet Customization
                            <i class="bi bi-flower1 text-danger ms-2"></i>
                        </h5>
                    </div>

                    <div class="notice-content" style="color: #2d5016; font-size: 0.95rem; line-height: 1.8;">
                        <p class="mb-3">
                            Thank you for choosing our customization service! Please note that we have limited supplies of flowers, ribbons, and other materials. We kindly ask that you select only from the available options in the customization area.
                        </p>
                        <p class="mb-3">
                            If you have a specific bouquet design in mind or a reference photo, we encourage you to chat with our clerk using the chat box. We're here to help bring your vision to life!
                        </p>
                        <p class="mb-4">
                            We will do our best to accommodate your requests. If a particular flower or material from your reference photo is unavailable, we will inform you and suggest suitable alternatives that match your design.
                        </p>

                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-3" style="font-size: 1rem;">
                                <i class="bi bi-flower1 text-success me-2"></i>How to Customize Your Bouquet:
                            </h6>
                            <ol style="padding-left: 1.5rem; line-height: 2;">
                                <li class="mb-2">
                                    <strong>Choose your flowers</strong> - Select from the flower types available in the customization panel.
                                </li>
                                <li class="mb-2">
                                    <strong>Pick your wrapping</strong> - Choose your preferred wrapping style and ribbon color.
                                </li>
                                <li class="mb-2">
                                    <strong>Review your design</strong> - Double-check your selections before proceeding.
                                </li>
                                <li class="mb-2">
                                    <strong>Submit your order</strong> - Click "Buy Now" or "Add to Cart" once you're satisfied.
                                </li>
                            </ol>
                        </div>

                        <p class="mb-0 text-center" style="font-style: italic; color: #5a7c3a;">
                            <i class="bi bi-chat-dots me-2"></i>Need help? Use the chat box to talk to our clerk if you have a design reference or special request.
                        </p>
                    </div>
                </div>

                <hr class="my-3" id="customizationDivider">

                <!-- Customization Form -->
                <form id="bouquetCustomizationForm" class="flex-grow-1 d-flex flex-column" style="display: flex !important;">
                    @csrf

                    <!-- Bouquet Wrapper Selection -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0">Choose Wrappers</h6>
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
                                                <div class="text-center text-muted p-2">
                                                    <i class="bi bi-image" style="font-size: 2rem;"></i>
                                                    <div class="small mt-1" style="font-size: 0.65rem; line-height: 1.1;">{{ $wrap->name }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="wrapper-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="wrapper-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $wrap->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($wrap->inventoryItem ? $wrap->inventoryItem->price : ($wrap->price ?? 0), 2) }}</div>
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
                            <h6 class="fw-bold text-dark mb-0">Choose Fresh Flowers</h6>
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
                                                <div class="text-center text-muted p-2">
                                                    <i class="bi bi-flower1" style="font-size: 2rem;"></i>
                                                    <div class="small mt-1" style="font-size: 0.65rem; line-height: 1.1;">{{ $focal->name }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flower-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        </div>
                                        <div class="flower-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $focal->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($focal->inventoryItem ? $focal->inventoryItem->price : ($focal->price ?? 0), 2) }}</div>
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
                            <h6 class="fw-bold text-dark mb-0">Choose Greenery</h6>
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
                                                <div class="text-center text-muted p-2">
                                                    <i class="bi bi-flower2" style="font-size: 2rem;"></i>
                                                    <div class="small mt-1" style="font-size: 0.65rem; line-height: 1.1;">{{ $greenery->name }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="greenery-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="greenery-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $greenery->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($greenery->inventoryItem ? $greenery->inventoryItem->price : ($greenery->price ?? 0), 2) }}</div>
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
                            <h6 class="fw-bold text-dark mb-0">Choose Artificial Flowers</h6>
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
                                                <div class="text-center text-muted p-2">
                                                    <i class="bi bi-flower3" style="font-size: 2rem;"></i>
                                                    <div class="small mt-1" style="font-size: 0.65rem; line-height: 1.1;">{{ $filler->name }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="filler-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="filler-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $filler->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($filler->inventoryItem ? $filler->inventoryItem->price : ($filler->price ?? 0), 2) }}</div>
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
                            <h6 class="fw-bold text-dark mb-0">Choose Ribbons</h6>
                        </div>
                        <div class="row g-3">
                            @foreach(($items['Ribbon'] ?? []) as $ribbon)
                            <div class="col-3">
                                <div class="ribbon-option" data-ribbon="{{ $ribbon->name }}" data-price="{{ $ribbon->price ?? 0 }}" data-image="{{ $ribbon->image ? asset('storage/'.$ribbon->image) : '' }}">
                                    <div class="ribbon-card position-relative">
                                        <div class="rounded-3" style="height: 120px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            @if($ribbon->image)
                                                <img src="{{ asset('storage/'.$ribbon->image) }}" alt="{{ $ribbon->name }}" style="max-height:120px; max-width:100%; object-fit:cover;">
                                            @else
                                                <div class="text-center text-muted p-2">
                                                    <i class="bi bi-gift" style="font-size: 2rem;"></i>
                                                    <div class="small mt-1" style="font-size: 0.65rem; line-height: 1.1;">{{ $ribbon->name }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ribbon-check position-absolute top-0 end-0 m-1" style="display: none;">
                                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                        </div>
                                        <div class="ribbon-info mt-2 text-center">
                                            <small class="fw-medium text-dark" style="font-size: 10px;">{{ $ribbon->name }}</small>
                                            <div class="text-success fw-bold" style="font-size: 9px;">₱{{ number_format($ribbon->inventoryItem ? $ribbon->inventoryItem->price : ($ribbon->price ?? 0), 2) }}</div>
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
        <div class="col-lg-6 order-1 order-lg-2" style="background: #CDE7C9; padding: 2rem;">
            <div class="h-100 d-flex flex-column align-items-center sticky-mobile" id="previewSection">
            <!-- Bouquet Preview Container -->
            <div class="bouquet-preview-container position-relative" style="width: 360px; max-width: 100%;" id="bouquetPreviewContainer">
                <!-- Regular Bouquet Preview -->
                <div class="img-fluid bouquet-canvas" style="width: 100%; height: 320px; background: #F1F6F1; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
                    <!-- Base Bouquet Shape -->
                    <div class="bouquet-base" style="position: absolute; width: 80%; height: 60%; background: linear-gradient(45deg, #ff6b6b, #4ecdc4); border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%; opacity: 0.3; transition: all 0.5s ease;"></div>

                    <!-- Wrapper Layer -->
                    <div id="wrapperLayer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgWrapper" alt="wrapper" style="position:absolute; inset:0; width:90%; height:90%; left: 5%; object-fit:cover; display:none; z-index:10; pointer-events:none;"/>

                    <!-- Fresh Flower Layer (single after removal of 2 & 3) -->
                    <div id="focal1Layer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgFlower" alt="flower" style="position:absolute; width:20%; height:auto; object-fit:contain; bottom:45%; left:44%; transform:translateX(-50%); display:none; z-index:60; pointer-events:none;"/>

                    <!-- Greenery Layer -->
                    <div id="greeneryLayer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgGreenery" alt="greenery" style="position:absolute; width:42%; height:auto; object-fit:contain; bottom:44%; left:50%; transform:translateX(-50%); display:none; z-index:20; opacity:.95; pointer-events:none;"/>

                    <!-- Artificial Flower Filler Layer -->
                    <div id="fillerLayer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgFiller" alt="filler" style="position:absolute; width:20%; height:auto; object-fit:contain; bottom:45%; left:56%; transform:translateX(-50%); display:none; z-index:25; pointer-events:none;"/>

                    <!-- Ribbon Layer -->
                    <div id="ribbonLayer" class="ingredient-layer" style="position: absolute; width: 100%; height: 100%; border-radius: 0.5rem; opacity: 0; transition: all 0.5s ease;"></div>
                    <img id="imgRibbon" alt="ribbon" style="position:absolute; width:20%; height:auto; object-fit:contain; bottom:29%; left:50%; transform:translateX(-50%); display:none; z-index:80; pointer-events:none;"/>

                    <!-- Default Message -->
                    <div id="defaultMessage" class="text-center text-muted" style="position: relative; z-index: 10;">
                        <i class="bi bi-flower1 fs-1 mb-2"></i>
                        <div class="fw-bold">Select materials to create your bouquet</div>
                        <small>Each component adds beauty to your floral arrangement!</small>
                    </div>
                </div>

                </div>

                <!-- Price Summary -->
                <div class="mt-3 w-100" style="max-width: 360px;" id="priceSummarySection">
                    <div class="price-summary bg-white rounded-3 shadow-sm p-3" id="priceSummaryCard" style="cursor: pointer; transition: all 0.3s ease;" data-bs-toggle="modal" data-bs-target="#priceSummaryModal">
                        <div class="fw-semibold text-dark mb-2">Price Summary</div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span id="totalPrice">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    </div>

<!-- Price Summary Modal -->
<div class="modal fade" id="priceSummaryModal" tabindex="-1" aria-labelledby="priceSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="priceSummaryModalLabel">Price Summary Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span>Wrappers:</span>
                                    <div class="text-muted small" id="modalWrapperName">None selected</div>
                                </div>
                                        <span id="modalWrapperPrice">₱0.00</span>
                            </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span>Fresh Flowers:</span>
                                        <input type="number" class="form-control form-control-sm text-center" id="modalFreshFlowerQty" value="1" min="1" max="10" style="width: 50px; padding: 4px; font-size: 12px;">
                                    </div>
                                    <div class="text-muted small" id="modalFreshFlowerName">None selected</div>
                                </div>
                            <span id="modalFocalFlower1Price">₱0.00</span>
                            </div>
                        <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <span>Greenery:</span>
                                    <div class="text-muted small" id="modalGreeneryName">None selected</div>
                                </div>
                            <span id="modalGreeneryPrice">₱0.00</span>
                            </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span>Artificial Flowers:</span>
                                        <input type="number" class="form-control form-control-sm text-center" id="modalArtificialFlowerQty" value="1" min="1" max="10" style="width: 50px; padding: 4px; font-size: 12px;">
                                    </div>
                                    <div class="text-muted small" id="modalArtificialFlowerName">None selected</div>
                                </div>
                            <span id="modalFillerPrice">₱0.00</span>
                            </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span>Ribbons:</span>
                                    <div class="text-muted small" id="modalRibbonName">None selected</div>
                                </div>
                            <span id="modalRibbonPrice">₱0.00</span>
                                </div>
                        <div class="d-flex justify-content-between mb-3">
                        <span>Assembly Fee:</span>
                            <span id="modalAssemblyFeePrice">₱0.00</span>
                    </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total:</span>
                            <span id="modalTotalPrice">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <!-- Buttons removed for cleaner UI -->
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
/* Notice Section Styles */
.notice-section {
    transition: all 0.3s ease;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#previewSection, #priceSummarySection, #bouquetCustomizationForm {
    transition: opacity 0.3s ease, display 0.3s ease;
}

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
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.price-summary:hover {
    border-color: #8ACB88;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
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

    .bouquet-preview-container { width: 320px !important; }
    .bouquet-preview-container .bouquet-canvas { height: 280px !important; }
}
/* Mobile sticky preview + price summary at top */
@media (max-width: 650px) {
    .sticky-mobile { position: sticky; top: 40px; z-index: 5; }
    .sticky-mobile .price-summary { margin-bottom: .5rem; }
    /* Ensure the preview section has full width on mobile */
    .order-1.order-lg-2 { padding-top: .5rem !important; }
    /* Target the page shell to fit typical 1000px devices */
    .customize-shell { min-height: 330px !important; }
    /* Left column scroll within fixed height so preview stays visible */
    .order-2.order-lg-1 { max-height: 330px !important; overflow-y: auto !important; }
    /* Nudge the whole section closer to the top navbar */
    .container-fluid { padding-top: 0 !important; }
    .customize-shell { margin-top: 0 !important; }
    /* Make the main content column wider on mobile */
    .col-10.col-xl-9 { width: 96% !important; }
    .col-10.col-xl-9 > .row { margin-left: 0 !important; margin-right: 0 !important; }
}

/* Center Price Summary modal on mobile */
@media (max-width: 650px) {
    #priceSummaryModal .modal-dialog { width: 90vw !important; max-width: 90vw !important; margin: 20vh auto !important; }
}
</style>
@endpush

@push('scripts')
<script>
// Define updatePrice function globally BEFORE DOMContentLoaded
window.updatePrice = function() {
    let total = 0;

    // Add occasion base price if selected
    if (window.selectedOccasion) {
        total += window.selectedOccasion.basePrice;
    }

    // Wrapper price
    const wrapperPrice = window.selectedWrapper ? window.selectedWrapper.price : 0;
    total += wrapperPrice * window.quantity;

    // Focal flower 1 price (with individual window.quantity)
    const focal1Price = window.selectedFocalFlower1 ? window.selectedFocalFlower1.price : 0;
    const freshFlowerQty = window.freshFlowerQuantity || 1;
    total += focal1Price * freshFlowerQty * window.quantity;

    // Focal flower 2 price (removed from UI)
    const focal2Price = window.selectedFocalFlower2 ? window.selectedFocalFlower2.price : 0;
    total += focal2Price * window.quantity;

    // Focal flower 3 price (removed from UI)
    const focal3Price = 0;

    // Greenery price
    const greeneryPrice = window.selectedGreenery ? window.selectedGreenery.price : 0;
    total += greeneryPrice * window.quantity;

    // Filler price (with individual window.quantity)
    const fillerPrice = window.selectedFiller ? window.selectedFiller.price : 0;
    const artificialFlowerQty = window.artificialFlowerQuantity || 1;
    total += fillerPrice * artificialFlowerQty * window.quantity;

    // Ribbon price
    const ribbonPrice = window.selectedRibbon ? window.selectedRibbon.price : 0;
    total += ribbonPrice * window.quantity;

    // Money amount price (if money bouquet)
    if (window.bouquetType === 'money') {
        const moneyPrice = window.selectedMoneyAmount ? window.selectedMoneyAmount.price : 0;
        total += moneyPrice * window.quantity;
    }

    // Add assembly fee only if there are materials selected
    const hasMaterials = window.selectedWrapper || window.selectedFocalFlower1 || window.selectedGreenery || window.selectedFiller || window.selectedRibbon || window.selectedMoneyAmount;
    if (hasMaterials) {
        const assemblyFeeTotal = window.assemblyFee * window.quantity;
        total += assemblyFeeTotal;
    }

    // Update the main price summary total
    const totalPriceElement = document.getElementById('totalPrice');
    if (totalPriceElement) {
        totalPriceElement.textContent = `₱${total.toFixed(2)}`;
    }

    // Update modal prices if modal exists
    if (typeof window.updateModalPrices === 'function') {
        window.updateModalPrices();
    }

    // Debug: Log total price and selected items
    console.log('=== PRICE UPDATE DEBUG ===');
    console.log('Total price updated:', total.toFixed(2));
    console.log('Selected wrapper:', window.selectedWrapper);
    console.log('Selected focal flower 1:', window.selectedFocalFlower1);
    console.log('Selected greenery:', window.selectedGreenery);
    console.log('Selected filler:', window.selectedFiller);
    console.log('Selected ribbon:', window.selectedRibbon);
    console.log('Quantity:', window.quantity);
    console.log('Assembly fee:', window.assemblyFee);
    console.log('Has materials:', hasMaterials);
    console.log('========================');
};

// Define updateModalPrices function globally
window.updateModalPrices = function() {
    // Calculate individual prices for modal display
    const wrapperPrice = window.selectedWrapper ? window.selectedWrapper.price * window.quantity : 0;
    const modalWrapperPrice = document.getElementById('modalWrapperPrice');
    if (modalWrapperPrice) {
        modalWrapperPrice.textContent = `₱${wrapperPrice.toFixed(2)}`;
    }

    // Update wrapper name
    const modalWrapperName = document.getElementById('modalWrapperName');
    if (modalWrapperName) {
        modalWrapperName.textContent = window.selectedWrapper ? window.selectedWrapper.name : 'None selected';
    }

    const focal1Price = window.selectedFocalFlower1 ? window.selectedFocalFlower1.price : 0;
    const freshFlowerQty = window.freshFlowerQuantity || 1;
    const focalFlower1Total = focal1Price * freshFlowerQty * window.quantity;
    const modalFocalFlower1Price = document.getElementById('modalFocalFlower1Price');
    if (modalFocalFlower1Price) {
        modalFocalFlower1Price.textContent = `₱${focalFlower1Total.toFixed(2)}`;
    }

    // Update fresh flower name
    const modalFreshFlowerName = document.getElementById('modalFreshFlowerName');
    if (modalFreshFlowerName) {
        modalFreshFlowerName.textContent = window.selectedFocalFlower1 ? window.selectedFocalFlower1.name : 'None selected';
    }

    const greeneryPrice = window.selectedGreenery ? window.selectedGreenery.price * window.quantity : 0;
    const modalGreeneryPrice = document.getElementById('modalGreeneryPrice');
    if (modalGreeneryPrice) {
        modalGreeneryPrice.textContent = `₱${greeneryPrice.toFixed(2)}`;
    }

    // Update greenery name
    const modalGreeneryName = document.getElementById('modalGreeneryName');
    if (modalGreeneryName) {
        modalGreeneryName.textContent = window.selectedGreenery ? window.selectedGreenery.name : 'None selected';
    }

    const fillerPrice = window.selectedFiller ? window.selectedFiller.price : 0;
    const artificialFlowerQty = window.artificialFlowerQuantity || 1;
    const fillerTotal = fillerPrice * artificialFlowerQty * window.quantity;
    const modalFillerPrice = document.getElementById('modalFillerPrice');
    if (modalFillerPrice) {
        modalFillerPrice.textContent = `₱${fillerTotal.toFixed(2)}`;
    }

    // Update artificial flower name
    const modalArtificialFlowerName = document.getElementById('modalArtificialFlowerName');
    if (modalArtificialFlowerName) {
        modalArtificialFlowerName.textContent = window.selectedFiller ? window.selectedFiller.name : 'None selected';
    }

    const ribbonPrice = window.selectedRibbon ? window.selectedRibbon.price * window.quantity : 0;
    const modalRibbonPrice = document.getElementById('modalRibbonPrice');
    if (modalRibbonPrice) {
        modalRibbonPrice.textContent = `₱${ribbonPrice.toFixed(2)}`;
    }

    // Update ribbon name
    const modalRibbonName = document.getElementById('modalRibbonName');
    if (modalRibbonName) {
        modalRibbonName.textContent = window.selectedRibbon ? window.selectedRibbon.name : 'None selected';
    }

    const hasMaterials = window.selectedWrapper || window.selectedFocalFlower1 || window.selectedGreenery || window.selectedFiller || window.selectedRibbon || window.selectedMoneyAmount;
        const assemblyFeeTotal = hasMaterials ? window.assemblyFee * window.quantity : 0;
    const modalAssemblyFeePrice = document.getElementById('modalAssemblyFeePrice');
    if (modalAssemblyFeePrice) {
        modalAssemblyFeePrice.textContent = `₱${assemblyFeeTotal.toFixed(2)}`;
    }

    const totalPrice = document.getElementById('totalPrice');
    const modalTotalPrice = document.getElementById('modalTotalPrice');
    if (totalPrice && modalTotalPrice) {
        modalTotalPrice.textContent = totalPrice.textContent;
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Notice toggle functionality
    const noticeToggleBtn = document.getElementById('noticeToggleBtn');
    const noticeSection = document.getElementById('noticeSection');
    const customizationForm = document.getElementById('bouquetCustomizationForm');
    const customizationDivider = document.getElementById('customizationDivider');
    let isNoticeVisible = false;

    if (noticeToggleBtn) {
        noticeToggleBtn.addEventListener('click', function() {
            isNoticeVisible = !isNoticeVisible;

            if (isNoticeVisible) {
                // Show notice, hide only customization form (keep preview and price summary visible)
                noticeSection.style.display = 'block';
                if (customizationForm) customizationForm.style.display = 'none';
                if (customizationDivider) customizationDivider.style.display = 'none';

                // Change button text
                this.innerHTML = '<i class="bi bi-flower1 me-1"></i>Make your bouquet now';
            } else {
                // Hide notice, show customization form
                noticeSection.style.display = 'none';
                if (customizationForm) customizationForm.style.display = 'flex';
                if (customizationDivider) customizationDivider.style.display = 'block';

                // Change button text back
                this.innerHTML = '<i class="bi bi-info-circle me-1"></i>Notice to Customers';
            }
        });
    }

    // Make all variables global so they can be accessed by the global functions
    window.selectedWrapper = null;
    window.selectedFocalFlower1 = null;
    window.selectedFocalFlower2 = null;
    window.selectedFocalFlower3 = null;
    window.selectedGreenery = null;
    window.selectedFiller = null;
    window.selectedRibbon = null;
    window.selectedMoneyAmount = null;
    window.selectedOccasion = null;
    window.quantity = 1;
    window.bouquetType = 'regular'; // 'regular' or 'money'

    window.assemblyFee = {{ $assemblingFee ?? 150 }};

    // Initialize modal quantities
    window.freshFlowerQuantity = 1;
    window.artificialFlowerQuantity = 1;

    // Bouquet type toggle handlers
    // Bouquet type toggle handlers - commented out since buttons were removed
    /*
    // Regular bouquet button
    const regularBouquetBtn = document.getElementById('regularBouquetBtn');
    if (regularBouquetBtn) {
        regularBouquetBtn.addEventListener('click', function() {
        window.bouquetType = 'regular';
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
        window.selectedMoneyAmount = null;
        document.getElementById('selectedMoneyAmount').value = '';
        document.getElementById('window.bouquetType').value = 'regular';

        updatePrice();
    });
    */

    /*
    document.getElementById('moneyBouquetBtn').addEventListener('click', function() {
        window.bouquetType = 'money';
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
        window.selectedWrapper = null;
        window.selectedFocalFlower1 = null;
        window.selectedFocalFlower2 = null;
        window.selectedFocalFlower3 = null;
        window.selectedGreenery = null;
        window.selectedFiller = null;
        window.selectedRibbon = null;

        document.getElementById('window.bouquetType').value = 'money';

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

            window.selectedMoneyAmount = {
                amount: this.dataset.amount,
                price: parseFloat(this.dataset.price)
            };

            document.getElementById('selectedMoneyAmount').value = window.selectedMoneyAmount.amount;
            updateMoneyPreview();
            window.updatePrice();
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

            window.selectedOccasion = {
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
            window.updatePrice();
        });
    });
    */

    // Auto-select recommended components based on occasion - commented out since occasion section was removed
    /*
    function autoSelectOccasionComponents() {
        if (!window.selectedOccasion) return;

        // Clear previous selections
        clearAllSelections();

        // Auto-select recommended wrapper
        if (window.selectedOccasion.wrappers && window.selectedOccasion.wrappers.length > 0) {
            const wrapperOption = document.querySelector(`[data-wrapper="${window.selectedOccasion.wrappers[0]}"]`);
            if (wrapperOption) {
                wrapperOption.click();
            }
        }

        // Auto-select recommended flowers
        if (window.selectedOccasion.flowers && window.selectedOccasion.flowers.length > 0) {
            window.selectedOccasion.flowers.slice(0, 3).forEach((flower, index) => {
                const flowerOption = document.querySelector(`[data-flower="${flower}"]`);
                if (flowerOption) {
                    flowerOption.click();
                }
            });
        }

        // Auto-select recommended ribbon
        if (window.selectedOccasion.ribbons && window.selectedOccasion.ribbons.length > 0) {
            const ribbonOption = document.querySelector(`[data-ribbon="${window.selectedOccasion.ribbons[0]}"]`);
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

        window.selectedWrapper = null;
        window.selectedFocalFlower1 = null;
        window.selectedFocalFlower2 = null;
        window.selectedFocalFlower3 = null;
        window.selectedGreenery = null;
        window.selectedFiller = null;
        window.selectedRibbon = null;
    }

    // Wrapper selection handlers
    document.querySelectorAll('.wrapper-option').forEach(option => {
        option.addEventListener('click', function() {
            // Check if this option is already selected
            if (this.classList.contains('selected')) {
                // If already selected, unselect it
                this.classList.remove('selected');
                this.querySelector('.wrapper-check').style.display = 'none';
                window.selectedWrapper = null;
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

                window.selectedWrapper = {
                    name: this.dataset.wrapper,
                    price: parseFloat(this.dataset.price)
                };

                document.getElementById('selectedWrapper').value = window.selectedWrapper.name;
            }

            updatePreview();
            window.updatePrice();
        });
    });

    // Focal Flower 1 selection handlers
    document.querySelectorAll('.focal1-option').forEach(option => {
        option.addEventListener('click', function() {
            console.log('Focal flower clicked:', this.dataset.flower, 'Price:', this.dataset.price);

            // Check if this option is already selected
            if (this.classList.contains('selected')) {
                // If already selected, unselect it
                this.classList.remove('selected');
                this.querySelector('.flower-check').style.display = 'none';
                window.selectedFocalFlower1 = null;
                document.getElementById('selectedFocalFlower1').value = '';
                console.log('Focal flower unselected');
            } else {
                // Remove previous selection
                document.querySelectorAll('.focal1-option').forEach(opt => {
                    opt.classList.remove('selected');
                    opt.querySelector('.flower-check').style.display = 'none';
                });

                // Add selection to clicked option
                this.classList.add('selected');
                this.querySelector('.flower-check').style.display = 'block';

                window.selectedFocalFlower1 = {
                    name: this.dataset.flower,
                    price: parseFloat(this.dataset.price)
                };

                document.getElementById('selectedFocalFlower1').value = window.selectedFocalFlower1.name;
                console.log('Focal flower selected:', window.selectedFocalFlower1);
            }

            updatePreview();
            window.updatePrice();
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

            window.selectedFocalFlower2 = {
                name: this.dataset.flower,
                price: parseFloat(this.dataset.price)
            };

            document.getElementById('selectedFocalFlower2').value = window.selectedFocalFlower2.name;
            updatePreview();
            window.updatePrice();
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

            window.selectedFocalFlower3 = {
                name: this.dataset.flower,
                price: parseFloat(this.dataset.price)
            };

            document.getElementById('selectedFocalFlower3').value = window.selectedFocalFlower3.name;
            updatePreview();
            window.updatePrice();
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
                window.selectedGreenery = null;
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

                window.selectedGreenery = {
                    name: this.dataset.greenery,
                    price: parseFloat(this.dataset.price)
                };

                document.getElementById('selectedGreenery').value = window.selectedGreenery.name;
            }

            updatePreview();
            window.updatePrice();
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
                window.selectedFiller = null;
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

                window.selectedFiller = {
                    name: this.dataset.filler,
                    price: parseFloat(this.dataset.price)
                };

                document.getElementById('selectedFiller').value = window.selectedFiller.name;
            }

            updatePreview();
            window.updatePrice();
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
                window.selectedRibbon = null;
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

                window.selectedRibbon = {
                    name: this.dataset.ribbon,
                    price: parseFloat(this.dataset.price)
                };

                document.getElementById('selectedRibbon').value = window.selectedRibbon.name;
            }

            updatePreview();
            window.updatePrice();
        });
    });

    // Quantity controls
    document.getElementById('decreaseQty').addEventListener('click', function() {
        if (window.quantity > 1) {
            window.quantity--;
            document.getElementById('quantity').value = window.quantity;
            window.updatePrice();
        }
    });

    document.getElementById('increaseQty').addEventListener('click', function() {
        if (window.quantity < 10) {
            window.quantity++;
            document.getElementById('quantity').value = window.quantity;
            window.updatePrice();
        }
    });

    document.getElementById('quantity').addEventListener('change', function() {
        window.quantity = Math.max(1, Math.min(10, parseInt(this.value) || 1));
        this.value = window.quantity;
        updatePrice();
    });

    // Fresh Flower window.quantity controls
    // Fresh flower quantity controls
    const freshFlowerQty = document.getElementById('freshFlowerQty');
    if (freshFlowerQty) {
        freshFlowerQty.addEventListener('change', function(e) {
            e.stopPropagation();
            let qty = Math.max(1, Math.min(10, parseInt(this.value) || 1));
            this.value = qty;
            updatePrice();
        });
    }

    const freshFlowerQtyClick = document.getElementById('freshFlowerQty');
    if (freshFlowerQtyClick) {
        freshFlowerQtyClick.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Artificial Flower quantity controls
    const artificialFlowerQty = document.getElementById('artificialFlowerQty');
    if (artificialFlowerQty) {
        artificialFlowerQty.addEventListener('change', function(e) {
            e.stopPropagation();
            let qty = Math.max(1, Math.min(10, parseInt(this.value) || 1));
            this.value = qty;
            updatePrice();
        });

        artificialFlowerQty.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Color selection handlers - wrapped in null checks
    const wrapperColorSelect = document.getElementById('wrapperColorSelect');
    if (wrapperColorSelect) {
        wrapperColorSelect.addEventListener('change', function(e) {
            e.stopPropagation();
            console.log('Wrapper color selected:', this.value);
            // Add color-specific logic here if needed
        });

        wrapperColorSelect.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    const freshFlowerColorSelect = document.getElementById('freshFlowerColorSelect');
    if (freshFlowerColorSelect) {
        freshFlowerColorSelect.addEventListener('change', function(e) {
            e.stopPropagation();
            console.log('Fresh flower color selected:', this.value);
            // Add color-specific logic here if needed
        });

        freshFlowerColorSelect.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    const artificialFlowerColorSelect = document.getElementById('artificialFlowerColorSelect');
    if (artificialFlowerColorSelect) {
        artificialFlowerColorSelect.addEventListener('change', function(e) {
            e.stopPropagation();
            console.log('Artificial flower color selected:', this.value);
            // Add color-specific logic here if needed
        });

        artificialFlowerColorSelect.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    const ribbonColorSelect = document.getElementById('ribbonColorSelect');
    if (ribbonColorSelect) {
        ribbonColorSelect.addEventListener('change', function(e) {
            e.stopPropagation();
            console.log('Ribbon color selected:', this.value);
            // Add color-specific logic here if needed
        });

        ribbonColorSelect.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Prevent clicks inside price details from closing the summary
    const priceDetails = document.getElementById('priceDetails');
    if (priceDetails) {
        priceDetails.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Update money preview
    function updateMoneyPreview() {
        if (window.selectedMoneyAmount) {
            const moneyAmountElement = document.querySelector('.money-amount');
            if (moneyAmountElement) {
                moneyAmountElement.textContent = `₱${window.selectedMoneyAmount.amount}`;
            }
        }
    }

    // Update preview (cooking-style layered approach)
    function updatePreview() {
        if (window.bouquetType === 'money') {
            updateMoneyPreview();
            return;
        }

        // Hide default message when materials are selected
        const defaultMessage = document.getElementById('defaultMessage');
        const hasMaterials = window.selectedWrapper || window.selectedFocalFlower1 || window.selectedGreenery || window.selectedFiller || window.selectedRibbon;

        if (hasMaterials) {
            defaultMessage.style.display = 'none';
        } else {
            defaultMessage.style.display = 'block';
        }

        // Update wrapper layer
        const wrapperLayer = document.getElementById('wrapperLayer');
        if (window.selectedWrapper) {
            wrapperLayer.style.opacity = '1';
            wrapperLayer.style.background = getWrapperStyle(window.selectedWrapper.name);
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
        updateFlowerLayer('focal1Layer', window.selectedFocalFlower1);

        // Update greenery layer
        const greeneryLayer = document.getElementById('greeneryLayer');
        if (window.selectedGreenery) {
            greeneryLayer.style.opacity = '0.8';
            greeneryLayer.style.background = getGreeneryStyle(window.selectedGreenery.name);
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
        if (window.selectedFiller) {
            fillerLayer.style.opacity = '0.6';
            fillerLayer.style.background = getFillerStyle(window.selectedFiller.name);
            fillerLayer.classList.add('ingredient-added');
            const imgF = document.getElementById('imgFiller');
            if (imgF) {
                const sel = document.querySelector('.filler-option.selected');
                imgF.src = sel && sel.dataset.image ? sel.dataset.image : '';
                imgF.style.display = imgF.src ? 'block' : 'none';

                // Smart positioning logic
                updateFlowerPositions();
            }
        } else {
            fillerLayer.style.opacity = '0';
            fillerLayer.classList.remove('ingredient-added');
            const imgF = document.getElementById('imgFiller');
            if (imgF) imgF.style.display = 'none';

            // Update positions when filler is removed
            updateFlowerPositions();
        }

        // Update ribbon layer
        const ribbonLayer = document.getElementById('ribbonLayer');
        if (window.selectedRibbon) {
            ribbonLayer.style.opacity = '0.9';
            ribbonLayer.style.background = getRibbonStyle(window.selectedRibbon.name);
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

                // Smart positioning logic
                updateFlowerPositions();
            }
        } else {
            layer.style.opacity = '0';
            layer.classList.remove('ingredient-added');
            const imgFl = document.getElementById('imgFlower');
            if (imgFl) imgFl.style.display = 'none';

            // Update positions when flower is removed
            updateFlowerPositions();
        }
    }

    // Smart positioning function for flowers
    function updateFlowerPositions() {
        const imgFlower = document.getElementById('imgFlower');
        const imgFiller = document.getElementById('imgFiller');

        const hasFreshFlower = window.selectedFocalFlower1;
        const hasArtificialFlower = window.selectedFiller;

        if (hasFreshFlower && hasArtificialFlower) {
            // Both flowers: position them side by side
            if (imgFlower) {
                imgFlower.style.left = '44%';
            }
            if (imgFiller) {
                imgFiller.style.left = '56%';
            }
        } else if (hasFreshFlower || hasArtificialFlower) {
            // Single flower: center it
            if (imgFlower) {
                imgFlower.style.left = '50%';
            }
            if (imgFiller) {
                imgFiller.style.left = '50%';
            }
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


    // Form submission
    document.getElementById('bouquetCustomizationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (window.bouquetType === 'money') {
            if (!window.selectedMoneyAmount) {
                showCheckoutAlert('Please select a money amount for your money bouquet.', 'error');
                return;
            }
        } else {
            // Require wrapper, ribbon, greenery AND at least one of fresh/artificial flowers
            const hasRequiredBase = !!(window.selectedWrapper && window.selectedRibbon && window.selectedGreenery);
            const hasAnyFlower = !!(window.selectedFocalFlower1 || window.selectedFiller);
            if (!hasRequiredBase || !hasAnyFlower) {
                showCheckoutAlert('Please select a wrapper, ribbon, greenery, and at least one flower (fresh or artificial).', 'error');
                return;
            }
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('bouquet_type', window.bouquetType);
        formData.append('quantity', window.quantity);

        if (window.bouquetType === 'money') {
            formData.append('money_amount', window.selectedMoneyAmount.amount);
        } else {
            if (window.selectedWrapper) formData.append('wrapper', window.selectedWrapper.name);
            if (window.selectedFocalFlower1) formData.append('focal_flower_1', window.selectedFocalFlower1.name);

            if (window.selectedGreenery) formData.append('greenery', window.selectedGreenery.name);
            if (window.selectedFiller) formData.append('filler', window.selectedFiller.name);
            if (window.selectedRibbon) formData.append('ribbon', window.selectedRibbon.name);
            // pass per-component quantities
            formData.append('fresh_flower_qty', window.freshFlowerQuantity || 1);
            formData.append('artificial_flower_qty', window.artificialFlowerQuantity || 1);
        }

        // Submit to backend
        fetch('{{ route("customer.products.bouquet-customize.store") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCheckoutAlert(data.message || 'Saved successfully');
                // Reset form
                location.reload();
            } else {
                showCheckoutAlert('Error: ' + (data.message || 'Save failed'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCheckoutAlert('An error occurred while submitting the form.', 'error');
        });
    });

    // Add to cart functionality
    document.getElementById('addToCartBtn').addEventListener('click', function() {
        if (window.bouquetType === 'money') {
            if (!window.selectedMoneyAmount) {
                showCheckoutAlert('Please select a money amount for your money bouquet.', 'error');
                return;
            }
        } else {
            const hasRequiredBase = !!(window.selectedWrapper && window.selectedRibbon && window.selectedGreenery);
            const hasAnyFlower = !!(window.selectedFocalFlower1 || window.selectedFiller);
            if (!hasRequiredBase || !hasAnyFlower) {
                showCheckoutAlert('Please select a wrapper, ribbon, greenery, and at least one flower (fresh or artificial).', 'error');
                return;
            }
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('bouquet_type', window.bouquetType);
        formData.append('quantity', window.quantity);

        if (window.bouquetType === 'money') {
            formData.append('money_amount', window.selectedMoneyAmount.amount);
        } else {
            if (window.selectedWrapper) formData.append('wrapper', window.selectedWrapper.name);
            if (window.selectedFocalFlower1) formData.append('focal_flower_1', window.selectedFocalFlower1.name);

            if (window.selectedGreenery) formData.append('greenery', window.selectedGreenery.name);
            if (window.selectedFiller) formData.append('filler', window.selectedFiller.name);
            if (window.selectedRibbon) formData.append('ribbon', window.selectedRibbon.name);
            formData.append('fresh_flower_qty', window.freshFlowerQuantity || 1);
            formData.append('artificial_flower_qty', window.artificialFlowerQuantity || 1);
        }

        // Submit to backend
        fetch('{{ route("customer.products.bouquet-customize.add-to-cart") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show toast notification similar to checkout redirect
                showCheckoutAlert(data.message || 'Custom bouquet added to cart successfully!');
                // Redirect to cart page after short delay
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1500);
            } else {
                // Show error notification
                showCheckoutAlert(data.message || 'Failed to add to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCheckoutAlert('An error occurred while adding to cart.', 'error');
        });
    });

    // Buy Now functionality
    document.getElementById('buyNowBtn').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent form submission

        if (window.bouquetType === 'money') {
            if (!window.selectedMoneyAmount) {
                showCheckoutAlert('Please select a money amount for your money bouquet.', 'error');
                return;
            }
        } else {
            const hasRequiredBase = !!(window.selectedWrapper && window.selectedRibbon && window.selectedGreenery);
            const hasAnyFlower = !!(window.selectedFocalFlower1 || window.selectedFiller);
            if (!hasRequiredBase || !hasAnyFlower) {
                showCheckoutAlert('Please select a wrapper, ribbon, greenery, and at least one flower (fresh or artificial).', 'error');
                return;
            }
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('bouquet_type', window.bouquetType);
        formData.append('quantity', window.quantity);

        if (window.bouquetType === 'money') {
            formData.append('money_amount', window.selectedMoneyAmount.amount);
        } else {
            if (window.selectedWrapper) formData.append('wrapper', window.selectedWrapper.name);
            if (window.selectedFocalFlower1) formData.append('focal_flower_1', window.selectedFocalFlower1.name);

            if (window.selectedGreenery) formData.append('greenery', window.selectedGreenery.name);
            if (window.selectedFiller) formData.append('filler', window.selectedFiller.name);
            if (window.selectedRibbon) formData.append('ribbon', window.selectedRibbon.name);
            formData.append('fresh_flower_qty', window.freshFlowerQuantity || 1);
            formData.append('artificial_flower_qty', window.artificialFlowerQuantity || 1);
        }

        // Submit to backend
        fetch('{{ route("customer.products.bouquet-customize.buy-now") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show toast notification similar to cart success
                showCheckoutAlert(data.message || 'Redirecting to checkout...');
                // Redirect to checkout page after short delay
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1500);
            } else {
                // Show error notification
                showCheckoutAlert(data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while processing buy now.' });
        });
    });

    function syncModalValues() {
        // Sync fresh flower quantity
        const modalFreshFlowerQty = document.getElementById('modalFreshFlowerQty');
        if (modalFreshFlowerQty) {
            modalFreshFlowerQty.value = window.freshFlowerQuantity || 1;
        }

        // Sync artificial flower quantity
        const modalArtificialFlowerQty = document.getElementById('modalArtificialFlowerQty');
        if (modalArtificialFlowerQty) {
            modalArtificialFlowerQty.value = window.artificialFlowerQuantity || 1;
        }

        // Update modal prices
        window.updateModalPrices();
    }

        // Initialize
        window.updatePrice();
    });

    // Sync modal when modal is shown
    const priceSummaryModal = document.getElementById('priceSummaryModal');
    if (priceSummaryModal) {
        priceSummaryModal.addEventListener('show.bs.modal', function() {
            if (typeof syncModalValues === 'function') {
                syncModalValues();
            }

            // Add event listeners to modal quantity inputs
            setupModalQuantityListeners();
        });
    }

    function setupModalQuantityListeners() {
        // Remove existing listeners first to prevent duplicates
        const modalFreshFlowerQty = document.getElementById('modalFreshFlowerQty');
        if (modalFreshFlowerQty) {
            // Clone the element to remove all event listeners
            const newElement = modalFreshFlowerQty.cloneNode(true);
            modalFreshFlowerQty.parentNode.replaceChild(newElement, modalFreshFlowerQty);
        }

        const modalArtificialFlowerQty = document.getElementById('modalArtificialFlowerQty');
        if (modalArtificialFlowerQty) {
            // Clone the element to remove all event listeners
            const newElement = modalArtificialFlowerQty.cloneNode(true);
            modalArtificialFlowerQty.parentNode.replaceChild(newElement, modalArtificialFlowerQty);
        }

        // Fresh Flower window.quantity in modal
        const freshFlowerQty = document.getElementById('modalFreshFlowerQty');
        if (freshFlowerQty) {
            freshFlowerQty.addEventListener('change', function() {
                const qty = Math.max(1, Math.min(10, parseInt(this.value) || 1));
                this.value = qty;

                // Store the window.quantity in a global variable for price calculation
                window.freshFlowerQuantity = qty;

                // Recalculate price
                if (typeof window.updatePrice === 'function') {
                    window.updatePrice();
                }
                console.log('Modal fresh flower window.quantity changed to:', qty);
            });

            // Add input event for real-time updates
            freshFlowerQty.addEventListener('input', function() {
                console.log('Modal fresh flower input event triggered, value:', this.value);
                const qty = Math.max(1, Math.min(10, parseInt(this.value) || 1));
                console.log('Processed window.quantity:', qty);

                // Store the window.quantity in a global variable for price calculation
                window.freshFlowerQuantity = qty;
                console.log('Set fresh flower window.quantity to:', qty);

                // Recalculate price
                console.log('Calling updatePrice()...');
                if (typeof window.updatePrice === 'function') {
                    window.updatePrice();
        } else {
                    console.error('updatePrice function not found!');
                }
            });

            // Add click event to prevent event bubbling
            freshFlowerQty.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Artificial Flower window.quantity in modal
        const artificialFlowerQty = document.getElementById('modalArtificialFlowerQty');
        if (artificialFlowerQty) {
            artificialFlowerQty.addEventListener('change', function() {
                const qty = Math.max(1, Math.min(10, parseInt(this.value) || 1));
                this.value = qty;

                // Store the window.quantity in a global variable for price calculation
                window.artificialFlowerQuantity = qty;

                // Recalculate price
                if (typeof window.updatePrice === 'function') {
                    window.updatePrice();
                }
                console.log('Modal artificial flower window.quantity changed to:', qty);
            });

            // Add input event for real-time updates
            artificialFlowerQty.addEventListener('input', function() {
                console.log('Modal artificial flower input event triggered, value:', this.value);
                const qty = Math.max(1, Math.min(10, parseInt(this.value) || 1));
                console.log('Processed artificial flower window.quantity:', qty);

                // Store the window.quantity in a global variable for price calculation
                window.artificialFlowerQuantity = qty;
                console.log('Set artificial flower window.quantity to:', qty);

                // Recalculate price
                console.log('Calling updatePrice() for artificial flowers...');
                if (typeof window.updatePrice === 'function') {
                    window.updatePrice();
                } else {
                    console.error('updatePrice function not found!');
                }
            });

            // Add click event to prevent event bubbling
            artificialFlowerQty.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

    }

    // Add hover effects to price summary card
    const priceSummaryCard = document.getElementById('priceSummaryCard');
    if (priceSummaryCard) {
        priceSummaryCard.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        });

        priceSummaryCard.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 0.5rem 1rem rgba(0,0,0,0.05)';
        });
    }



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
            wrapper: window.selectedWrapper ? 1 : 0,
            flowers: (window.selectedFocalFlower1 ? 1 : 0),
            greenery: window.selectedGreenery ? 1 : 0,
            filler: window.selectedFiller ? 1 : 0,
            ribbon: window.selectedRibbon ? 1 : 0
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

        if (window.selectedWrapper) materials.push(`📦 ${window.selectedWrapper.name}`);
        if (window.selectedFocalFlower1) materials.push(`🌸 ${window.selectedFocalFlower1.name}`);

        if (window.selectedGreenery) materials.push(`🌿 ${window.selectedGreenery.name}`);
        if (window.selectedFiller) materials.push(`✨ ${window.selectedFiller.name}`);
        if (window.selectedRibbon) materials.push(`🎀 ${window.selectedRibbon.name}`);

        if (materials.length === 0) {
            recipeSummary.innerHTML = '<em>Select materials to see your arrangement components...</em>';
        } else {
            recipeSummary.innerHTML = materials.map(material =>
                `<span class="recipe-ingredient">${material}</span>`
            ).join(' ');
        }
    }
    </script>

    <!-- Toast Alert Function for Checkout -->
    <script>
    function showCheckoutAlert(message, type = 'success') {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.checkout-toast-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Determine alert styling
        const isSuccess = type === 'success';
        const bgColor = isSuccess ? '#d4edda' : '#f8d7da';
        const borderColor = isSuccess ? '#c3e6cb' : '#f5c6cb';
        const textColor = isSuccess ? '#155724' : '#721c24';
        const icon = isSuccess ? 'check-circle' : 'exclamation-triangle';

        // Create toast alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'checkout-toast-alert';
        alertDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 320px;
            max-width: 450px;
            background: ${bgColor};
            border: 1px solid ${borderColor};
            border-radius: 8px;
            padding: 14px 18px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInRight 0.3s ease-out;
        `;

        alertDiv.innerHTML = `
            <i class="fas fa-${icon}" style="color: ${textColor}; font-size: 18px; flex-shrink: 0;"></i>
            <span style="color: ${textColor}; font-weight: 500; flex: 1; font-size: 14px;">${message}</span>
        `;

        document.body.appendChild(alertDiv);

        // Auto remove after 3 seconds with fade out
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                alertDiv.style.opacity = '0';
                alertDiv.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 300);
            }
        }, 3000);
    }

    // Add CSS animation if not exists
    if (!document.getElementById('checkout-alert-animation')) {
        const style = document.createElement('style');
        style.id = 'checkout-alert-animation';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
    </script>
    @endpush


