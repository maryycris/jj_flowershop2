@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">New Purchase Order</h2>
    <form action="{{ route('clerk.purchase_orders.store') }}" method="POST">
        @csrf
        <div class="card mb-4">
            <div class="card-header">Supplier</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Business Name</label>
                    <input type="text" name="supplier_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Contact #</label>
                    <input type="text" name="contact" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Order Date Received</label>
                    <input type="date" name="order_date_received" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Order Details</div>
            <div class="card-body">
                <table class="table table-bordered align-middle" id="productsTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="products[0][product_id]" class="form-select" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="products[0][quantity]" class="form-control qty-input" min="1" required></td>
                            <td><input type="number" name="products[0][unit_price]" class="form-control price-input" min="0" step="0.01" required></td>
                            <td><input type="text" class="form-control subtotal-input" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" id="addRowBtn">Add Product</button>
                <div class="mt-3 text-end">
                    <strong>Total Amount: ₱<span id="totalAmount">0.00</span></strong>
                </div>
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let rowIdx = 1;
const products = @json($products);

function recalcTotals() {
    let total = 0;
    document.querySelectorAll('#productsTable tbody tr').forEach(function(row) {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const subtotal = qty * price;
        row.querySelector('.subtotal-input').value = subtotal.toFixed(2);
        total += subtotal;
    });
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

document.getElementById('addRowBtn').addEventListener('click', function() {
    const tbody = document.querySelector('#productsTable tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="products[${rowIdx}][product_id]" class="form-select" required>
                <option value="">Select Product</option>
                ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
            </select>
        </td>
        <td><input type="number" name="products[${rowIdx}][quantity]" class="form-control qty-input" min="1" required></td>
        <td><input type="number" name="products[${rowIdx}][unit_price]" class="form-control price-input" min="0" step="0.01" required></td>
        <td><input type="text" class="form-control subtotal-input" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
    `;
    tbody.appendChild(tr);
    rowIdx++;
});

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
        recalcTotals();
    }
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
        recalcTotals();
    }
});
</script>
@endpush 