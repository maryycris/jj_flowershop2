@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Delivery Management</h3>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>All Deliveries</h4>
        <a href="{{ route('deliveries.create') }}" class="btn btn-success">Assign New Delivery</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($deliveries->isEmpty())
        <div class="alert alert-info" role="alert">
            No deliveries found.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Driver</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->id }}</td>
                            <td>{{ $delivery->order->id ?? 'N/A' }}</td>
                            <td>{{ $delivery->order->user->name ?? 'N/A' }}</td>
                            <td>{{ $delivery->order->product->name ?? 'N/A' }}</td>
                            <td>{{ $delivery->driver->name ?? 'N/A' }}</td>
                            <td>{{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') : 'N/A' }}</td>
                            <td><span class="badge {{ $delivery->status === 'pending' ? 'bg-warning' : ($delivery->status === 'delivered' ? 'bg-success' : 'bg-info') }}">{{ ucfirst($delivery->status) }}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('deliveries.show', $delivery->id) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i> View</a>
                                    <a href="{{ route('deliveries.edit', $delivery->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i> Edit</a>
                                    <form action="{{ route('deliveries.destroy', $delivery->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this delivery?');">
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
            {{ $deliveries->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection 