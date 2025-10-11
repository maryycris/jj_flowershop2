@extends('layouts.admin_app')

@section('title', 'Walk-in Orders')

@section('content')
<div class="container-fluid">
    <div class="mx-auto" style="max-width: 1200px;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Walk-in Orders</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Walk-in Order
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($walkInOrders->count() > 0)
                        <div class="table-responsive orders-table-container">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Products</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($walkInOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->customer_name ?? 'Walk-in Customer' }}</td>
                                        <td>
                                            @foreach($order->products as $product)
                                                <span class="badge badge-info">{{ $product->name }} ({{ $product->pivot->quantity }})</span>
                                            @endforeach
                                        </td>
                                        <td>₱{{ number_format($order->total_price, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-file-invoice"></i> Invoice
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No walk-in orders found</h5>
                            <p class="text-muted">Create a new walk-in order to get started.</p>
                            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Walk-in Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Orders table scrollbar styling */
    .orders-table-container {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        background: #fff;
    }
    
    .orders-table-container::-webkit-scrollbar {
        width: 8px;
    }
    
    .orders-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .orders-table-container::-webkit-scrollbar-thumb {
        background: #5E8458;
        border-radius: 4px;
    }
    
    .orders-table-container::-webkit-scrollbar-thumb:hover {
        background: #4a6b45;
    }
</style>
@endpush
