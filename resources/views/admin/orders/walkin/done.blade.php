@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="p-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #e6f0e6;">
                        <span class="badge bg-success">Done</span>
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
                        <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                        <a href="{{ route('admin.orders.index', ['type' => 'walkin']) }}" class="btn btn-outline-secondary">Back to Orders</a>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <span class="btn btn-sm btn-light disabled">Ready</span>
                            <span class="btn btn-sm btn-success disabled">Done</span>
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
                                                $quantityToProvide = max(0, min($demand, $stockAvailable));
                                            @endphp
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $demand }}</td>
                                                <td>{{ $quantityToProvide }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
