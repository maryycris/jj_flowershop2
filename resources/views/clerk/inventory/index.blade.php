@extends('layouts.clerk_app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Inventory (Clerk)</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add New Product</button>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('clerk.inventory.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="code" class="form-label">Product Code</label>
            <input type="text" class="form-control" id="code" name="code" required>
          </div>
          <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
              <option value="">Select Category</option>
              <option value="Fresh Flowers">Fresh Flowers</option>
              <option value="Gifts">Gifts</option>
              <option value="Artificial Flowers">Artificial Flowers</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">Selling Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
          </div>
          <div class="mb-3">
            <label for="cost_price" class="form-label">Cost Price</label>
            <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price">
          </div>
          <div class="mb-3">
            <label for="reorder_min" class="form-label">Reordering Min</label>
            <input type="number" class="form-control" id="reorder_min" name="reorder_min">
          </div>
          <div class="mb-3">
            <label for="reorder_max" class="form-label">Reordering Max</label>
            <input type="number" class="form-control" id="reorder_max" name="reorder_max">
          </div>
          <div class="mb-3">
            <label for="stock" class="form-label">Qty On Hand</label>
            <input type="number" class="form-control" id="stock" name="stock">
          </div>
          <div class="mb-3">
            <label for="qty_consumed" class="form-label">Qty Consumed</label>
            <input type="number" class="form-control" id="qty_consumed" name="qty_consumed">
          </div>
          <div class="mb-3">
            <label for="qty_damaged" class="form-label">Qty Damaged</label>
            <input type="number" class="form-control" id="qty_damaged" name="qty_damaged">
          </div>
          <div class="mb-3">
            <label for="qty_sold" class="form-label">Qty Sold</label>
            <input type="number" class="form-control" id="qty_sold" name="qty_sold">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Submit Button -->
<div class="d-flex justify-content-end mt-3">
    <button class="btn btn-success" id="submitInventoryBtn">Submit</button>
</div>

<!-- Inventory Submitted Modal -->
<div class="modal fade" id="inventorySubmittedModal" tabindex="-1" aria-labelledby="inventorySubmittedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
          <circle cx="12" cy="12" r="12" fill="#e6f4ea"/>
          <path d="M7 13l3 3 7-7" stroke="#4caf50" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h5 class="mb-3">YOUR REPORT HAS BEEN SUBMITTED</h5>
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

@if($products->count())
    <!-- Bootstrap Nav Tabs -->
    <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">
        @foreach(['Fresh Flowers', 'Gifts', 'Artificial Flowers'] as $category)
            <li class="nav-item" role="presentation">
                <button class="nav-link @if($loop->first) active @endif" id="tab-{{ Str::slug($category) }}" data-bs-toggle="tab" data-bs-target="#{{ Str::slug($category) }}" type="button" role="tab" aria-controls="{{ Str::slug($category) }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ $category }}
                </button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content" id="inventoryTabsContent">
        @foreach(['Fresh Flowers', 'Gifts', 'Artificial Flowers'] as $category)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="{{ Str::slug($category) }}" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th colspan="2">Reordering Rules<br><small>(Min / Max)</small></th>
                                <th>Qty On Hand</th>
                                <th>Qty Consumed</th>
                                <th>Qty Damaged</th>
                                <th>Qty Sold</th>
                                <th>Qty to Purchase<br><small>(Max - On Hand)</small></th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                @if($product->category === $category)
                                @php
                                    $min = $product->reorder_min ?? 0;
                                    $max = $product->reorder_max ?? 0;
                                    $stock = $product->stock ?? 0;
                                    $qtyToPurchase = ($stock < $max) ? ($max - $stock) : 0;
                                @endphp
                                    <tr>
                                    <td>{{ $product->code ?? $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ $product->cost_price ?? '-' }}</td>
                                    <td>{{ $min }}</td>
                                    <td>{{ $max }}</td>
                                    <td>{{ $stock }}</td>
                                    <td>{{ $product->qty_consumed ?? '-' }}</td>
                                    <td>{{ $product->qty_damaged ?? '-' }}</td>
                                    <td>{{ $product->qty_sold ?? '-' }}</td>
                                    <td>{{ $qtyToPurchase }}</td>
                                    <td>{{ $product->created_at ? $product->created_at->format('Y-m-d') : '-' }}</td>
                                    <td>
                                            <!-- Edit Button -->
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">Edit</button>
                                            <!-- Delete Form -->
                                            <form action="{{ route('clerk.products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                            </form>
                                            <!-- Edit Modal -->
                                <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-labelledby="editProductModalLabel{{ $product->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                                    <h5 class="modal-title" id="editProductModalLabel{{ $product->id }}">Edit Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <form action="{{ route('clerk.inventory.update', $product->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                          <div class="mb-3">
                                                        <label for="name{{ $product->id }}" class="form-label">Product Name</label>
                                                        <input type="text" class="form-control" id="name{{ $product->id }}" name="name" value="{{ $product->name }}" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="category{{ $product->id }}" class="form-label">Category</label>
                                                        <select class="form-select" id="category{{ $product->id }}" name="category" required>
                                                          <option value="Fresh Flowers" @if($product->category == 'Fresh Flowers') selected @endif>Fresh Flowers</option>
                                                          <option value="Gifts" @if($product->category == 'Gifts') selected @endif>Gifts</option>
                                                          <option value="Artificial Flowers" @if($product->category == 'Artificial Flowers') selected @endif>Artificial Flowers</option>
                                                        </select>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="price{{ $product->id }}" class="form-label">Selling Price</label>
                                                        <input type="number" step="0.01" class="form-control" id="price{{ $product->id }}" name="price" value="{{ $product->price }}" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="cost_price{{ $product->id }}" class="form-label">Cost Price</label>
                                                        <input type="number" step="0.01" class="form-control" id="cost_price{{ $product->id }}" name="cost_price" value="{{ $product->cost_price }}">
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="reorder_min{{ $product->id }}" class="form-label">Reordering Min</label>
                                                        <input type="number" class="form-control" id="reorder_min{{ $product->id }}" name="reorder_min" value="{{ $product->reorder_min }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="reorder_max{{ $product->id }}" class="form-label">Reordering Max</label>
                                                        <input type="number" class="form-control" id="reorder_max{{ $product->id }}" name="reorder_max" value="{{ $product->reorder_max }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="stock{{ $product->id }}" class="form-label">Qty On Hand</label>
                                                        <input type="number" class="form-control" id="stock{{ $product->id }}" name="stock" value="{{ $product->stock }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="qty_consumed{{ $product->id }}" class="form-label">Qty Consumed</label>
                                                        <input type="number" class="form-control" id="qty_consumed{{ $product->id }}" name="qty_consumed" value="{{ $product->qty_consumed }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="qty_damaged{{ $product->id }}" class="form-label">Qty Damaged</label>
                                                        <input type="number" class="form-control" id="qty_damaged{{ $product->id }}" name="qty_damaged" value="{{ $product->qty_damaged }}">
                                          </div>
                                          <div class="mb-3">
                                                        <label for="qty_sold{{ $product->id }}" class="form-label">Qty Sold</label>
                                                        <input type="number" class="form-control" id="qty_sold{{ $product->id }}" name="qty_sold" value="{{ $product->qty_sold }}">
                                          </div>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                                      <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p>No products found.</p>
@endif
@endsection