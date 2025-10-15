@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inventory Reports</h3>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $summary['total_products'] }}</h4>
                                            <p class="mb-0">Total Products</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-boxes fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $summary['low_stock_products'] }}</h4>
                                            <p class="mb-0">Low Stock</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $summary['out_of_stock'] }}</h4>
                                            <p class="mb-0">Out of Stock</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $summary['total_movements_today'] }}</h4>
                                            <p class="mb-0">Movements Today</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exchange-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Filters</h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('admin.admin.inventory.reports') }}">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="product_id" class="form-label">Product</label>
                                                <select name="product_id" id="product_id" class="form-select">
                                                    <option value="">All Products</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="movement_type" class="form-label">Movement Type</label>
                                                <select name="movement_type" id="movement_type" class="form-select">
                                                    <option value="">All Types</option>
                                                    <option value="IN" {{ request('movement_type') == 'IN' ? 'selected' : '' }}>IN</option>
                                                    <option value="OUT" {{ request('movement_type') == 'OUT' ? 'selected' : '' }}>OUT</option>
                                                    <option value="TRANSFER" {{ request('movement_type') == 'TRANSFER' ? 'selected' : '' }}>TRANSFER</option>
                                                    <option value="ADJUSTMENT" {{ request('movement_type') == 'ADJUSTMENT' ? 'selected' : '' }}>ADJUSTMENT</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="date_from" class="form-label">Date From</label>
                                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="date_to" class="form-label">Date To</label>
                                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                                <a href="{{ route('admin.admin.inventory.reports') }}" class="btn btn-secondary">Clear</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Movement History Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Movement History</h5>
                        </div>
                        <div class="card-body">
                            @if($movements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Movement #</th>
                                                <th>Product</th>
                                                <th>Type</th>
                                                <th>Quantity</th>
                                                <th>Unit Cost</th>
                                                <th>User</th>
                                                <th>Order #</th>
                                                <th>Date</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($movements as $movement)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-{{ $movement->movement_type == 'OUT' ? 'danger' : ($movement->movement_type == 'IN' ? 'success' : 'info') }}">
                                                            {{ $movement->movement_number }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $movement->product->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $movement->movement_type == 'OUT' ? 'danger' : ($movement->movement_type == 'IN' ? 'success' : 'info') }}">
                                                            {{ $movement->movement_type }}
                                                        </span>
                                                    </td>
                                                    <td class="{{ $movement->movement_type == 'OUT' ? 'text-danger' : 'text-success' }}">
                                                        {{ $movement->movement_type == 'OUT' ? '-' : '+' }}{{ $movement->quantity }}
                                                    </td>
                                                    <td>₱{{ number_format($movement->unit_cost ?? 0, 2) }}</td>
                                                    <td>{{ $movement->user->name ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($movement->order)
                                                            <a href="{{ route('admin.orders.show', $movement->order->id) }}" class="text-decoration-none">
                                                                #{{ str_pad($movement->order->id, 5, '0', STR_PAD_LEFT) }}
                                                            </a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                                    <td>{{ $movement->notes ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination -->
                                <div class="d-flex justify-content-center">
                                    {{ $movements->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No movements found</h5>
                                    <p class="text-muted">Try adjusting your filters to see more results.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
