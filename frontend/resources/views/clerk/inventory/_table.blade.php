<table class="modern-table">
    <thead>
        <tr>
            <th>PRODUCT CODE</th>
            <th>PRODUCT NAME</th>
            <th>CATEGORY</th>
            <th>COST PRICE</th>
            <th>SELLING PRICE</th>
            <th>REORDERING RULES<br>MIN/MAX</th>
            <th>QTY DAMAGED</th>
            <th>QTY SOLD</th>
            <th>QTY CONSUMED</th>
            <th>QTY ON HAND</th>
            <th>QTY TO PURCHASE</th>
            <th>LATEST RESTOCK</th>
            <th>ACTIONS</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            @php
                $min = $product->reorder_min ?? 0;
                $max = $product->reorder_max ?? 0;
                $stock = $product->stock ?? 0;
                // Calculate quantity needed to purchase: Max - On Hand
                // Shows how many units needed to reach maximum reorder level
                // If stock is already at or above max, shows 0
                $qtyToPurchase = ($stock < $max) ? max(0, $max - $stock) : 0;
            @endphp
            <tr>
                <td>{{ $product->code ?? $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category }}</td>
                <td>₱{{ number_format($product->cost_price, 2) }}</td>
                <td>₱{{ number_format($product->price, 2) }}</td>
                <td>{{ $min }}/{{ $max }}</td>
                <td>{{ $product->qty_damaged ?? '-' }}</td>
                <td>{{ $product->qty_sold ?? '-' }}</td>
                <td>{{ $product->qty_consumed ?? '-' }}</td>
                <td>{{ $stock }}</td>
                <td>{{ $qtyToPurchase }}</td>
                <td>{{ $product->created_at ? $product->created_at->format('m/d/Y') : '-' }}</td>
                <td>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">Edit</button>
                    <button class="btn btn-sm btn-danger">Delete</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="13" class="text-center">No products found for this category.</td>
            </tr>
        @endforelse
    </tbody>
</table> 