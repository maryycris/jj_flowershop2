@extends('layouts.admin_app')

@section('title', 'Inventory Log Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-history me-2"></i>Inventory Log Details</h2>
        <a href="{{ route('admin.inventory-logs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Logs
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Log Details Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Log Entry #{{ $inventoryLog->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Date & Time:</strong><br>
                            <span class="text-muted">{{ $inventoryLog->created_at->format('F d, Y \a\t h:i A') }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Action:</strong><br>
                            @switch($inventoryLog->action)
                                @case('edit')
                                    <span class="badge bg-warning fs-6">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </span>
                                    @break
                                @case('delete')
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </span>
                                    @break
                                @case('create')
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-plus me-1"></i>Create
                                    </span>
                                    @break
                                @case('restore')
                                    <span class="badge bg-info fs-6">
                                        <i class="fas fa-undo me-1"></i>Restore
                                    </span>
                                    @break
                                @default
                                    <span class="badge bg-secondary fs-6">{{ ucfirst($inventoryLog->action) }}</span>
                            @endswitch
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>User:</strong><br>
                            <div class="d-flex align-items-center mt-1">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    {{ substr($inventoryLog->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $inventoryLog->user->name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ $inventoryLog->user->role ?? 'Unknown' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <strong>Product:</strong><br>
                            <div class="mt-1">
                                <div class="fw-bold">{{ $inventoryLog->product->name ?? 'Unknown Product' }}</div>
                                <small class="text-muted">{{ $inventoryLog->product->product_code ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Description:</strong><br>
                        <span class="text-muted">{{ $inventoryLog->description }}</span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>IP Address:</strong><br>
                            <span class="text-muted">{{ $inventoryLog->ip_address ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>User Agent:</strong><br>
                            <small class="text-muted">{{ $inventoryLog->user_agent ?? 'N/A' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Changes Comparison -->
            @if($inventoryLog->action === 'edit' && $inventoryLog->old_values && $inventoryLog->new_values)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Changes Made</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Field</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventoryLog->new_values as $key => $newValue)
                                        @php
                                            $oldValue = $inventoryLog->old_values[$key] ?? null;
                                            $hasChanged = $oldValue != $newValue;
                                        @endphp
                                        @if($hasChanged)
                                            <tr>
                                                <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                                <td>
                                                    <span class="text-danger">{{ $oldValue ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-success">{{ $newValue ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning">Changed</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.inventory-logs.product', $inventoryLog->product) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>View All Logs for This Product
                        </a>
                        <a href="{{ route('admin.products.show', $inventoryLog->product) }}" 
                           class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>View Product Details
                        </a>
                        <a href="{{ route('admin.inventory-logs.export', ['product_id' => $inventoryLog->product_id]) }}" 
                           class="btn btn-outline-success">
                            <i class="fas fa-download me-2"></i>Export Product Logs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            @if($inventoryLog->product)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Product Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Name:</strong><br>
                            <span class="text-muted">{{ $inventoryLog->product->name }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Code:</strong><br>
                            <span class="text-muted">{{ $inventoryLog->product->product_code ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Category:</strong><br>
                            <span class="text-muted">{{ $inventoryLog->product->category ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Current Stock:</strong><br>
                            <span class="text-muted">{{ $inventoryLog->product->stock ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
}
</style>
@endsection
