@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ isset($order) ? 'Edit Order' : 'Create New Order' }}</div>

                <div class="card-body">
                    <form action="{{ isset($order) ? route('admin.orders.update', $order->id) : route('admin.orders.store') }}" method="POST">
                        @csrf
                        @if(isset($order))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Customer</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ (isset($order) && $order->user_id == $customer->id) ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->email }})</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ (isset($order) && $order->product_id == $product->id) ? 'selected' : '' }}>{{ $product->name }} ({{ $product->price }})</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', $order->quantity ?? '') }}" min="1" required>
                            @error('quantity')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                @foreach(['pending', 'processing', 'completed', 'cancelled'] as $statusOption)
                                    <option value="{{ $statusOption }}" {{ (isset($order) && $order->status == $statusOption) ? 'selected' : ((!isset($order) && $statusOption == 'pending') ? 'selected' : '') }}>{{ ucfirst($statusOption) }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">{{ isset($order) ? 'Update Order' : 'Create Order' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 