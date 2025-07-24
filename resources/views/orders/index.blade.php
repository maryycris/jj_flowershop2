@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Order Management</h3>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>All Orders</h4>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-success">Add New Order</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($orders->isEmpty())
        <div class="alert alert-info" role="alert">
            No orders found.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                            <td>{{ $order->product->name ?? 'N/A' }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td><span class="badge {{ $order->status === 'pending' ? 'bg-warning' : ($order->status === 'completed' ? 'bg-success' : 'bg-info') }}">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ $order->created_at->format('M d, Y H:i A') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i> View</a>
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i> Edit</a>
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection 