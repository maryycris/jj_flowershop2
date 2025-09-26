@extends('layouts.admin_app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Inventory Reports</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>PRODUCT ID</th>
                            <th>PRODUCT NAME</th>
                            <th>CATEGORY</th>
                            <th>SELLING PRICE</th>
                            <th>EXPIRATION DATE</th>
                            <th>REORDER LEVEL</th>
                            <th>SPOILAGE/DAMAGED</th>
                            <th>STOCK QUANTITY</th>
                            <th>RESTOCK PRODUCT</th>
                            <th>SELL OR USE PRODUCT</th>
                            <th>LAST RESTOCK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $product->code ?? $product->id ?? 'N/A' }}</td>
                            <td>{{ $product->name ?? 'N/A' }}</td>
                            <td>{{ $product->category ?? 'N/A' }}</td>
                            <td>{{ $product->price ?? 0 }}</td>
                            <td>{{ $product->expiration_date ?? 'N/A' }}</td>
                            <td>{{ $product->reorder_min ?? 0 }}</td>
                            <td>{{ $product->qty_damaged ?? 0 }}</td>
                            <td>{{ $product->stock ?? 0 }}</td>
                            <td>{{ $product->restock_qty ?? 0 }}</td>
                            <td>{{ $product->units_sold ?? 0 }}</td>
                            <td>{{ $product->last_restock ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">No products found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 