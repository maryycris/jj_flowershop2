@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Delivery Details #{{ $delivery->id }}
        </div>
        <div class="card-body">
            <h5 class="card-title">Order ID: {{ $delivery->order->id ?? 'N/A' }}</h5>
            <p class="card-text"><strong>Customer:</strong> {{ $delivery->order->user->name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Product:</strong> {{ $delivery->order->product->name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Driver:</strong> {{ $delivery->driver->name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Delivery Date:</strong> {{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') : 'N/A' }}</p>
            <p class="card-text"><strong>Status:</strong> <span class="badge {{ $delivery->status === 'pending' ? 'bg-warning' : ($delivery->status === 'delivered' ? 'bg-success' : 'bg-info') }}">{{ ucfirst($delivery->status) }}</span></p>
            <p class="card-text"><strong>Assigned At:</strong> {{ $delivery->created_at->format('M d, Y H:i A') }}</p>
            <p class="card-text"><strong>Last Updated:</strong> {{ $delivery->updated_at->format('M d, Y H:i A') }}</p>

            <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">Back to Deliveries</a>
            <a href="{{ route('deliveries.edit', $delivery->id) }}" class="btn btn-warning">Edit Delivery</a>
        </div>
    </div>
</div>
@endsection 