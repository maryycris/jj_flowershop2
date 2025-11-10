@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <!-- Header with Navigation and Workflow -->
                    <div class="p-3" style="border-bottom:1px solid #e6f0e6;">
                        <!-- Navigation and Order Info -->
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <a href="{{ route('clerk.orders.walkin.sales_order', $order->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Back" aria-label="Back" style="width:34px;height:34px;padding:0;">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                            <button type="button" class="btn btn-info btn-sm px-3 py-1" style="border-radius: 15px; font-size: 0.75rem;">New</button>
                            <div class="ms-2 small text-muted">
                                <div>Quotations / {{ sprintf('%05d', $order->id) }}</div>
                                <div class="fw-bold text-primary">
                                    @if($order->salesOrder)
                                        <a href="{{ route('clerk.sales-orders.show', $order->id) }}" class="text-decoration-none text-primary" style="cursor: pointer;">
                                            {{ str_replace('-', '', $order->salesOrder->so_number) }}
                                        </a>
                                    @else
                                        SO{{ sprintf('%05d', $order->id) }}
                                    @endif
                                </div>
                        </div>
                    </div>

                        <!-- Action Buttons and Workflow -->
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                    <i class="bi bi-check-circle me-1"></i>Validate
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="bi bi-printer me-1"></i>Print
                                </button>
                                <a href="{{ route('clerk.sales-orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                            </div>
                            <div class="d-flex align-items-center gap-1 ms-3">
                                <div class="badge bg-secondary text-white px-2 py-1">Draft</div>
                                <i class="bi bi-chevron-right text-muted"></i>
                                <div class="badge bg-secondary text-white px-2 py-1">Waiting</div>
                                <i class="bi bi-chevron-right text-muted"></i>
                                <div class="badge {{ request('view') === 'moves' ? 'bg-success' : 'bg-secondary' }} text-white px-2 py-1">Ready</div>
                                <i class="bi bi-chevron-right text-muted"></i>
                                <div class="badge {{ $order->order_status === 'approved' || $order->order_status === 'completed' || $order->order_status === 'on_delivery' ? 'bg-success' : 'bg-secondary' }} text-white px-2 py-1">Done</div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details Section -->
                    <div class="px-3 pt-3 pb-4">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <div class="p-3">
                                    <h4 class="fw-bold mb-2">{{ $inventoryMovement ? $inventoryMovement->movement_number : 'OUT / 0001' }}</h4>
                                    <div class="text-muted">Delivery Address</div>
                            </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3">
                                    <div class="fw-semibold mb-2">Customer Information</div>
                                    <div class="small">
                                        @php
                                            $notes = $order->notes ?? '';
                                            $customerName = $order->user->name ?? 'Walk-in Customer';
                                            if (!empty($notes) && preg_match('/Customer:\s*(.*?)(?:[;,]|$)/', $notes, $m)) {
                                                $customerName = trim($m[1]);
                                            }
                                            $emailFromNotes = null;
                                            if (!empty($notes) && preg_match('/Email:\s*([^;,\s]+@[^;,\s]+)/', $notes, $m)) {
                                                $emailFromNotes = trim($m[1]);
                                            }
                                        @endphp
                                        <div class="mb-1">{{ $customerName }}</div>
                                        @if($emailFromNotes)
                                            <div class="mb-1">{{ $emailFromNotes }}</div>
                                        @endif
                                    @if($order->delivery)
                                        <div class="mb-1">{{ $order->delivery->delivery_address }}</div>
                                    @endif
                                        <div class="mt-2">
                                            <div class="mb-1"><span class="fw-semibold">Schedule Date:</span> <span class="text-muted">{{ optional($order->created_at)->format('m/d/Y') }}</span></div>
                                            <div><span class="fw-semibold">Order Number:</span> <span class="text-muted">{{ sprintf('%05d', $order->id) }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Operations Section -->
                        <div class="mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="px-3 py-2 fw-semibold" style="display:inline-block;background:#e6f5e6;border:1px solid #d9ecd9;border-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;">Operations</div>
                            </div>
                            <div class="table-responsive" style="border:1px solid #d9ecd9;">
                                <table class="table mb-0" id="operationsTable">
                                    <thead style="background:#e6f5e6;">
                                        <tr>
                                            <th style="width:30%">Product</th>
                                            <th style="width:20%">Demand</th>
                                            <th style="width:20%">Quantity</th>
                                            <th style="width:15%">UoM</th>
                                            <th style="width:15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->products as $product)
                                            @php
                                                $demand = (int) ($product->pivot->quantity ?? 0);
                                                $stockAvailable = (int) ($product->stock ?? 0);
                                                $canFulfillFromComposition = false;
                                                $compositionMessage = '';
                                                if (isset($productCompositions[$product->id]) && $productCompositions[$product->id]) {
                                                    $composition = $productCompositions[$product->id];
                                                    $canFulfillFromComposition = $composition['can_fulfill'];
                                                    $compositionMessage = $canFulfillFromComposition ? 'Can be made from materials' : 'Insufficient materials';
                                                }
                                                if (isset($productCompositions[$product->id]) && $productCompositions[$product->id]) {
                                                    $quantityToProvide = $canFulfillFromComposition ? $demand : 0;
                                                    $isInsufficientStock = !$canFulfillFromComposition;
                                                    $stockMessage = $compositionMessage;
                                                } else {
                                                    $quantityToProvide = max(0, min($demand, $stockAvailable));
                                                    $isInsufficientStock = $stockAvailable < $demand;
                                                    $stockMessage = $isInsufficientStock ? "(Insufficient stock: {$stockAvailable} available)" : "({$stockAvailable} available)";
                                                }
                                            @endphp
                                            <tr class="{{ $isInsufficientStock ? 'table-warning' : '' }}">
                                                <td>
                                                    <div class="fw-semibold">{{ $product->name }}</div>
                                                        @if($isInsufficientStock)
                                                        <small class="text-warning">{{ $stockMessage }}</small>
                                                        @else
                                                        <small class="text-success">{{ $stockMessage }}</small>
                                                        @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm demand-input" value="{{ $demand }}" min="0" data-product-id="{{ $product->id }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm quantity-input" value="{{ $quantityToProvide }}" min="0" data-product-id="{{ $product->id }}">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm uom-select" data-product-id="{{ $product->id }}">
                                                        <option value="pcs">PCS</option>
                                                        <option value="dozen">Dozen</option>
                                                        <option value="box">Box</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-success" title="Validate"><i class="bi bi-check-circle"></i></button>
                                                        <button type="button" class="btn btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></button>
                                                        <button type="button" class="btn btn-outline-danger" title="Remove"><i class="bi bi-trash"></i></button>
                                                    </div>
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
                                            @php $composition = $productCompositions[$product->id]; $quantity = $product->pivot->quantity; @endphp
                                            <div class="p-3 border-bottom">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">{{ $product->name }} (Qty: {{ $quantity }})</h6>
                                                    <span class="badge bg-{{ $composition['can_fulfill'] ? 'success' : 'danger' }}">{{ $composition['can_fulfill'] ? 'Can Fulfill' : 'Cannot Fulfill' }}</span>
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
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($composition['components'] as $component)
                                                                            <tr class="{{ $component['sufficient'] ? '' : 'table-warning' }}">
                                                                                <td><strong>{{ $component['composition']->component_name }}</strong></td>
                                                                                <td>{{ $component['required_quantity'] }} {{ $component['composition']->unit }}<br><small class="text-muted">{{ $component['composition']->quantity }} per unit</small></td>
                                                                                <td>{{ $component['available_stock'] }} {{ $component['composition']->unit }}</td>
                                                                                <td><span class="badge bg-{{ $component['status_class'] }}">{{ $component['status'] }}</span></td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2"><small class="text-muted">Summary: {{ $composition['sufficient_components'] }}/{{ $composition['total_components'] }} materials sufficient</small></div>
                                                @else
                                                    <div class="text-muted"><i class="fas fa-info-circle"></i> No composition data available for this product.</div>
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
                    <button type="button" class="btn btn-success" id="confirmValidateBtn">Confirm</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const confirmBtn = document.getElementById('confirmValidateBtn');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function() {
      const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
      if (modal) modal.hide();

      fetch(`{{ route('clerk.orders.walkin.validate.confirm', $order->id) }}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(r => r.json())
      .then(() => {
        // Update badges to reflect Done
        const badges = document.querySelectorAll('.badge');
        badges.forEach(b => {
          if (b.textContent.trim() === 'Done') {
            b.classList.remove('bg-secondary');
            b.classList.add('bg-success');
          }
          if (b.textContent.trim() === 'Ready') {
            b.classList.remove('bg-success');
            b.classList.add('bg-secondary');
          }
        });

        // Hide Validate button
        const validateBtn = document.querySelector('button.btn.btn-success[data-bs-target="#confirmModal"]');
        if (validateBtn) validateBtn.style.display = 'none';

        // Toast then hard refresh to reflect deducted stocks in composition table
        try {
          Swal.fire({ icon: 'success', title: 'Success!', text: 'Order validated successfully!', timer: 900, showConfirmButton: false })
            .then(() => window.location.reload());
        } catch (_) {
          window.location.reload();
        }
      })
      .catch(err => {
        console.error(err);
        try { Swal.fire({ icon: 'error', title: 'Error', text: 'Validation failed' }); } catch (_) {}
      });
    });
  }
});
</script>
@endpush


