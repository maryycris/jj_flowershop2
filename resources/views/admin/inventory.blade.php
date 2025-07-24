@php $hideSidebar = true; @endphp
@extends('layouts.admin_app')

@section('admin_content')
<div class="container py-4">
    <h2 class="mb-4">Inventory Management</h2>
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="?filter=all" class="text-decoration-none">
                <div class="card bg-primary text-white mb-3 @if(request('filter', 'all') == 'all') border-4 border-dark @endif">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $totalProducts }}</h4>
                            <small>Total Products</small>
                        </div>
                        <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="?filter=low_stock" class="text-decoration-none">
                <div class="card bg-warning text-white mb-3 @if(request('filter') == 'low_stock') border-4 border-dark @endif">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $lowStock }}</h4>
                            <small>Low Stock Items</small>
                        </div>
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="?filter=well_stocked" class="text-decoration-none">
                <div class="card bg-success text-white mb-3 @if(request('filter') == 'well_stocked') border-4 border-dark @endif">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $wellStocked }}</h4>
                            <small>Well Stocked</small>
                        </div>
                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="?filter=categories" class="text-decoration-none">
                <div class="card bg-info text-white mb-3 @if(request('filter') == 'categories') border-4 border-dark @endif">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $categories }}</h4>
                            <small>Categories</small>
                        </div>
                        <i class="bi bi-tags" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Product Inventory Overview</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search products..." style="width: 200px;">
                    <select class="form-select form-select-sm" id="categoryFilter" style="width: 150px;">
                        <option value="">All Categories</option>
                        @foreach($products->unique('category')->pluck('category') as $category)
                            @if($category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="inventoryTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                            <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                                <td><span class="badge bg-secondary">{{ $product->id }}</span></td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->code)
                                        <br><small class="text-muted">Code: {{ $product->code }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-outline-primary">{{ $product->category ?? 'Uncategorized' }}</span>
                                </td>
                                <td>
                                    <strong class="text-success">₱{{ number_format($product->price, 2) }}</strong>
                                    @if($product->cost_price)
                                        <br><small class="text-muted">Cost: ₱{{ number_format($product->cost_price, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($product->stock <= 10)
                                        <span class="badge bg-danger">{{ $product->stock ?? 0 }}</span>
                                    @elseif($product->stock <= 30)
                                        <span class="badge bg-warning">{{ $product->stock ?? 0 }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->stock ?? 0 }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->stock <= 10)
                                        <span class="badge bg-danger">Low Stock</span>
                                    @elseif($product->stock <= 30)
                                        <span class="badge bg-warning">Medium</span>
                                    @else
                                        <span class="badge bg-success">Good</span>
                                    @endif
                                </td>
                                <td>{{ $product->created_at ? $product->created_at->format('M d, Y') : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const table = document.getElementById('inventoryTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();

        for (let row of rows) {
            const name = row.cells[1].textContent.toLowerCase();
            const category = row.cells[2].textContent.toLowerCase();
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = !selectedCategory || category.includes(selectedCategory);
            row.style.display = matchesSearch && matchesCategory ? '' : 'none';
        }
    }

    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);
});
</script>

<style>
.badge.bg-outline-primary {
    background-color: #e3f2fd;
    color: #1976d2;
    border: 1px solid #1976d2;
}
</style>
@endsection 