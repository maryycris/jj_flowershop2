@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid" style="background:#f4faf4;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card shadow-sm" style="border:0;">
                <div class="card-body p-0" style="background:#fff;">
                    <div class="p-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #e6f0e6;">
                        <div class="ms-2 small text-muted">{{ sprintf('%05d', $order->id) }}</div>
                        <div class="ms-2 small text-muted">{{ $inventoryMovement ? $inventoryMovement->movement_number : 'OUT / 0001' }}</div>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-light d-flex align-items-center" style="border:1px solid #e6e6e6;">
                                <i class="bi bi-list me-1"></i>
                                Moves
                            </button>
                        </div>
                    </div>

                    <div class="px-3 py-2 d-flex align-items-center gap-2" style="border-bottom:1px solid #e6f0e6;">
                        <a href="{{ route('clerk.orders.walkin.validate_confirmation', $order) }}" class="btn btn-success">Validate</a>
                        <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                        <a href="{{ route('clerk.orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <span class="btn btn-sm btn-success disabled">Ready</span>
                            <span class="btn btn-sm btn-light disabled">Done</span>
                        </div>
                    </div>

                    <div class="px-3 pt-3 pb-4">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <div class="p-3 fw-semibold">INVENTORY / {{ $inventoryMovement ? $inventoryMovement->movement_number : 'OUT / 0001' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 fw-semibold">Delivery Address</div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 small"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 small">
                                    @if($order->delivery)
                                        <div class="mb-1">{{ $order->delivery->delivery_address }}</div>
                                    @endif
                                    <div class="mt-2"><span class="fw-semibold">Schedule Date:</span> <span class="text-muted">{{ optional($order->created_at)->format('m/d/Y') }}</span></div>
                                    <div class="mt-1"><span class="fw-semibold">Order Number:</span> <span class="text-muted">{{ sprintf('%05d', $order->id) }}</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#e6f5e6;border:1px solid #d9ecd9;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Operations</div>
                            <div class="table-responsive" style="border:1px solid #d9ecd9;">
                                <table class="table mb-0">
                                    <thead style="background:#e6f5e6;">
                                        <tr>
                                            <th>Product</th>
                                            <th>Demand</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->products as $product)
                                            @php
                                                $demand = (int) ($product->pivot->quantity ?? 0);
                                                $stockAvailable = (int) ($product->stock ?? 0);
                                                
                                                // Check if we can fulfill based on composition analysis
                                                $canFulfillFromComposition = false;
                                                $compositionMessage = '';
                                                
                                                if (isset($productCompositions[$product->id]) && $productCompositions[$product->id]) {
                                                    $composition = $productCompositions[$product->id];
                                                    $canFulfillFromComposition = $composition['can_fulfill'];
                                                    $compositionMessage = $canFulfillFromComposition ? 
                                                        'Can be made from materials' : 
                                                        'Insufficient materials';
                                                }
                                                
                                                // Use composition analysis if available, otherwise fall back to stock
                                                if (isset($productCompositions[$product->id]) && $productCompositions[$product->id]) {
                                                    $quantityToProvide = $canFulfillFromComposition ? $demand : 0;
                                                    $isInsufficientStock = !$canFulfillFromComposition;
                                                    $stockMessage = $compositionMessage;
                                                } else {
                                                    $quantityToProvide = max(0, min($demand, $stockAvailable));
                                                    $isInsufficientStock = $stockAvailable < $demand;
                                                    $stockMessage = $isInsufficientStock ? 
                                                        "(Insufficient stock: {$stockAvailable} available)" : 
                                                        "({$stockAvailable} available)";
                                                }
                                            @endphp
                                            <tr class="{{ $isInsufficientStock ? 'table-warning' : '' }}">
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $demand }}</td>
                                                <td>
                                                    <span class="{{ $isInsufficientStock ? 'text-warning' : '' }}">
                                                        {{ $quantityToProvide }}
                                                        @if($isInsufficientStock)
                                                            <small class="text-muted">({{ $stockMessage }})</small>
                                                        @else
                                                            <small class="text-success">({{ $stockMessage }})</small>
                                                        @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Product Composition Breakdown -->
                        @if(isset($productCompositions) && !empty($productCompositions))
                            <div class="mt-4">
                                <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#f8f9fa;border:1px solid #dee2e6;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Product Composition Breakdown</div>
                                <div class="table-responsive" style="border:1px solid #dee2e6;">
                                    @foreach($order->products as $product)
                                        @if(isset($productCompositions[$product->id]) && $productCompositions[$product->id])
                                            @php
                                                $composition = $productCompositions[$product->id];
                                                $quantity = $product->pivot->quantity;
                                            @endphp
                                            <div class="p-3 border-bottom">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">{{ $product->name }} (Qty: {{ $quantity }})</h6>
                                                    <span class="badge bg-{{ $composition['can_fulfill'] ? 'success' : 'danger' }}">
                                                        {{ $composition['can_fulfill'] ? 'Can Fulfill' : 'Cannot Fulfill' }}
                                                    </span>
                                                </div>
                                                
                                                @if($composition['total_components'] > 0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Material</th>
                                                                            <th>Required</th>
                                                                            <th>Available</th>
                                                                            <th>Status</th>
                                                                            <th>Shortage</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($composition['components'] as $component)
                                                                            <tr class="{{ $component['sufficient'] ? '' : 'table-warning' }}">
                                                                                <td>
                                                                                    <strong>{{ $component['composition']->component_name }}</strong>
                                                                                    @if($component['component'])
                                                                                        <br><small class="text-muted">ID: {{ $component['component']->id }}</small>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    {{ $component['required_quantity'] }} {{ $component['composition']->unit }}
                                                                                    <br><small class="text-muted">{{ $component['composition']->quantity }} per unit</small>
                                                                                </td>
                                                                                <td>
                                                                                    {{ $component['available_stock'] }} {{ $component['composition']->unit }}
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge bg-{{ $component['status_class'] }}">
                                                                                        {{ $component['status'] }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    @if($component['shortage'] > 0)
                                                                                        <span class="text-danger">
                                                                                            {{ $component['shortage'] }} {{ $component['composition']->unit }} short
                                                                                        </span>
                                                                                    @else
                                                                                        <span class="text-success">✓</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            Summary: {{ $composition['sufficient_components'] }}/{{ $composition['total_components'] }} materials sufficient
                                                        </small>
                                                    </div>
                                                @else
                                                    <div class="text-muted">
                                                        <i class="fas fa-info-circle"></i> No composition data available for this product.
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3 fw-semibold" id="confirmModalLabel">Are you sure you want to proceed ?</div>
                <div class="d-flex justify-content-center gap-3">
                    <form method="POST" action="{{ route('clerk.orders.walkin.validate.confirm', $order) }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-success">Confirm</button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


