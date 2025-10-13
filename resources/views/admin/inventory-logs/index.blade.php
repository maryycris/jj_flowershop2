@extends('layouts.admin_app')

@section('title', 'Inventory Logs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-history me-2"></i>Inventory Logs</h2>
        <div>
            <a href="{{ route('admin.inventory-logs.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.inventory-logs.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search logs...">
                </div>
                <div class="col-md-2">
                    <label for="action" class="form-label">Action</label>
                    <select class="form-select" id="action" name="action">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="user_id" class="form-label">User</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="product_id" class="form-label">Product</label>
                    <select class="form-select" id="product_id" name="product_id">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.inventory-logs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Inventory Changes Log</h5>
        </div>
        <div class="card-body">
            @if($logs->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Product</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Changes</th>
                                <th>IP Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $log->created_at->format('M d, Y') }}<br>
                                            {{ $log->created_at->format('h:i A') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($log->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $log->user->name ?? 'Unknown' }}</div>
                                                <small class="text-muted">{{ $log->user->role ?? 'Unknown' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $log->product->name ?? 'Unknown Product' }}</div>
                                            <small class="text-muted">{{ $log->product->product_code ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($log->action)
                                            @case('edit')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </span>
                                                @break
                                            @case('delete')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </span>
                                                @break
                                            @case('create')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-plus me-1"></i>Create
                                                </span>
                                                @break
                                            @case('restore')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-undo me-1"></i>Restore
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <small>{{ $log->description }}</small>
                                    </td>
                                    <td>
                                        @if($log->changes_summary)
                                            <small class="text-muted">{{ $log->changes_summary }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $log->ip_address ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.inventory-logs.show', $log) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No inventory logs found</h5>
                    <p class="text-muted">No changes have been made to the inventory yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
@endsection
