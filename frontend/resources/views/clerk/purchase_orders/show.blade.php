@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Purchase Order #{{ $purchaseOrder->id }}</h2>
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fa fa-print me-1"></i> Print/Receipt</button>
    </div>
    <!-- Print Header (only visible when printing) -->
    <div class="text-center mb-4 d-none" id="print-header">
        <img src="/path/to/your/logo.png" alt="JJ Flower Shop Logo" style="height:60px;">
        <h3 class="mt-2 mb-0">JJ FLOWER SHOP</h3>
        <div>Purchase Order Receipt</div>
        <hr>
    </div>
    <div class="card mb-4">
        <div class="card-header">Supplier</div>
        <div class="card-body row g-3">
            <div class="col-md-4"><strong>Business Name:</strong> {{ $purchaseOrder->supplier_name }}</div>
            <div class="col-md-4"><strong>Contact #:</strong> {{ $purchaseOrder->contact }}</div>
            <div class="col-md-4"><strong>Address:</strong> {{ $purchaseOrder->address }}</div>
            <div class="col-md-4"><strong>Order Date Received:</strong> {{ $purchaseOrder->order_date_received }}</div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">Order Details</div>
        <div class="card-body">
            <form action="{{ route('clerk.purchase_orders.validate', $purchaseOrder) }}" method="POST">
                @csrf
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Received</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->pivot->quantity }}</td>
                            <td>
                                @if($purchaseOrder->status === 'validated')
                                    {{ $product->pivot->received }}
                                @else
                                    <input type="number" name="received[{{ $product->id }}]" class="form-control" min="0" max="{{ $product->pivot->quantity }}" value="{{ $product->pivot->quantity }}">
                                @endif
                            </td>
                            <td>{{ number_format($product->pivot->unit_price, 2) }}</td>
                            <td>{{ number_format($product->pivot->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3 text-end">
                    <strong>Total Amount: ₱{{ number_format($purchaseOrder->total_amount, 2) }}</strong>
                </div>
                @if($purchaseOrder->status !== 'validated')
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success">Validate</button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    .container-fluid, .container-fluid * { visibility: visible; }
    .btn, nav, .sidebar, .list-group, .navbar, .sidebar-heading { display: none !important; }
    .container-fluid { position: absolute; left: 0; top: 0; width: 100%; }
    #print-header { display: block !important; }
}
#print-header { display: none; }
</style>
@endpush 