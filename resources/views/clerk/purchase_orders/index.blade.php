@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Purchase Orders</h2>
    <a href="{{ route('clerk.purchase_orders.create') }}" class="btn btn-success mb-3">New Purchase Order</a>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                        <tr>
                            <td>{{ $po->id }}</td>
                            <td>{{ $po->supplier_name }}</td>
                            <td>{{ $po->order_date_received }}</td>
                            <td><span class="badge bg-{{ $po->status === 'validated' ? 'success' : 'secondary' }}">{{ ucfirst($po->status) }}</span></td>
                            <td>₱{{ number_format($po->total_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('clerk.purchase_orders.show', $po) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No purchase orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 