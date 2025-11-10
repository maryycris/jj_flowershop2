@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ isset($delivery) ? 'Edit Delivery' : 'Assign New Delivery' }}</div>

                <div class="card-body">
                    <form action="{{ isset($delivery) ? route('deliveries.update', $delivery->id) : route('deliveries.store') }}" method="POST">
                        @csrf
                        @if(isset($delivery))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="order_id" class="form-label">Order</label>
                            <select class="form-select" id="order_id" name="order_id" required {{ isset($delivery) ? 'disabled' : '' }}>
                                <option value="">Select Order</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}" {{ (isset($delivery) && $delivery->order_id == $order->id) ? 'selected' : '' }}>Order #{{ $order->id }} (Customer: {{ $order->user->name ?? 'N/A' }}, Product: {{ $order->product->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            @error('order_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="driver_id" class="form-label">Driver</label>
                            <select class="form-select" id="driver_id" name="driver_id" required>
                                <option value="">Select Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ (isset($delivery) && $delivery->driver_id == $driver->id) ? 'selected' : '' }}>{{ $driver->name }} ({{ $driver->email }})</option>
                                @endforeach
                            </select>
                            @error('driver_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="delivery_date" class="form-label">Delivery Date</label>
                            <input type="date" class="form-control" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', isset($delivery) ? \Carbon\Carbon::parse($delivery->delivery_date)->format('Y-m-d') : '') }}" required>
                            @error('delivery_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                @foreach(['pending', 'assigned', 'in-transit', 'delivered', 'cancelled'] as $statusOption)
                                    <option value="{{ $statusOption }}" {{ (isset($delivery) && $delivery->status == $statusOption) ? 'selected' : ((!isset($delivery) && $statusOption == 'pending') ? 'selected' : '') }}>{{ ucfirst($statusOption) }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">{{ isset($delivery) ? 'Update Delivery' : 'Assign Delivery' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 