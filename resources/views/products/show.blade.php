@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Product Details</h4>
                    <div>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-secondary text-white text-center rounded" 
                                     style="height: 200px; line-height: 200px;">
                                    No Image Available
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3>{{ $product->name }}</h3>
                            <p class="text-muted">Category: {{ $product->category }}</p>
                            
                            <div class="mb-3">
                                <h5>Description</h5>
                                <p>{{ $product->description ?: 'No description available.' }}</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Price</h5>
                                    <p class="h4 text-primary">₱{{ number_format($product->price, 2) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Stock</h5>
                                    <p class="h4 {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $product->stock }}
                                    </p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h5>Status</h5>
                                <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="mb-3">
                                <h5>Created</h5>
                                <p>{{ $product->created_at->format('F j, Y g:i A') }}</p>
                            </div>

                            <div class="mb-3">
                                <h5>Last Updated</h5>
                                <p>{{ $product->updated_at->format('F j, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush 