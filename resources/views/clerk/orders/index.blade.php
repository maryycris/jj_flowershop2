@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sales Orders</h1>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs" id="orderTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="online-orders-tab" data-bs-toggle="tab" data-bs-target="#online-orders" type="button" role="tab" aria-controls="online-orders" aria-selected="true">Online Orders</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="walkin-orders-tab" data-bs-toggle="tab" data-bs-target="#walkin-orders" type="button" role="tab" aria-controls="walkin-orders" aria-selected="false">Walk-in Orders</button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="orderTabsContent">
        <!-- Online Orders Pane -->
        <div class="tab-pane fade show active" id="online-orders" role="tabpanel" aria-labelledby="online-orders-tab">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Search...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select">
                                <option selected>Region</option>
                                <!-- Add regions if necessary -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control">
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Order Number</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($onlineOrders as $order)
                                    <tr>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('m/d/Y') }}</td>
                                        <td>₱{{ number_format($order->total_price, 2) }}</td>
                                        <td><span class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span></td>
                                        <td>
                                            @if($order->status === 'pending')
                                                <form action="{{ route('clerk.orders.approve', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                            @elseif($order->status === 'approved')
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignDeliveryModal{{ $order->id }}">Assign for Delivery</button>
                                            @endif
                                            <a href="{{ route('clerk.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">No online orders found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Walk-in Orders Pane -->
        <div class="tab-pane fade" id="walkin-orders" role="tabpanel" aria-labelledby="walkin-orders-tab">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <!-- Filters and New Button -->
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <input type="text" class="form-control d-inline-block" style="width: 250px;" placeholder="Search...">
                            <input type="date" class="form-control d-inline-block" style="width: auto;">
                        </div>
                        <a href="{{ route('clerk.orders.create') }}" class="btn btn-success">New</a>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive">
                         <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Order Number</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($walkInOrders as $order)
                                    <tr>
                                        <td>{{ $order->user->name ?? 'Walk-in Customer' }}</td>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('m/d/Y') }}</td>
                                        <td>₱{{ number_format($order->total_price, 2) }}</td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($order->status) }}</span></td>
                                        <td>
                                            @if($order->status === 'on_delivery')
                                                <form action="{{ route('clerk.orders.markDelivered', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Mark as Delivered</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('clerk.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('clerk.orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-secondary">Invoice</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">No walk-in orders found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign for Delivery Modals for Online Orders -->
@foreach($onlineOrders as $order)
    @if($order->status === 'approved')
        <div class="modal fade" id="assignDeliveryModal{{ $order->id }}" tabindex="-1" aria-labelledby="assignDeliveryModalLabel{{ $order->id }}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="assignDeliveryModalLabel{{ $order->id }}">Assign for Delivery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="{{ route('clerk.orders.assignDelivery', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="driver_id{{ $order->id }}" class="form-label">Select Driver</label>
                    <select class="form-select" id="driver_id{{ $order->id }}" name="driver_id" required>
                      <option value="">Select Driver</option>
                      @foreach(\App\Models\User::where('role', 'driver')->get() as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="delivery_date{{ $order->id }}" class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" id="delivery_date{{ $order->id }}" name="delivery_date" value="{{ now()->format('Y-m-d') }}" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Assign</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    @endif
@endforeach
<!-- Assign for Delivery Modals for Walk-in Orders -->
@foreach($walkInOrders as $order)
    @if($order->status === 'approved')
        <div class="modal fade" id="assignDeliveryModal{{ $order->id }}" tabindex="-1" aria-labelledby="assignDeliveryModalLabel{{ $order->id }}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="assignDeliveryModalLabel{{ $order->id }}">Assign for Delivery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="{{ route('clerk.orders.assignDelivery', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="driver_id{{ $order->id }}" class="form-label">Select Driver</label>
                    <select class="form-select" id="driver_id{{ $order->id }}" name="driver_id" required>
                      <option value="">Select Driver</option>
                      @foreach(\App\Models\User::where('role', 'driver')->get() as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="delivery_date{{ $order->id }}" class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" id="delivery_date{{ $order->id }}" name="delivery_date" value="{{ now()->format('Y-m-d') }}" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Assign</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    @endif
@endforeach
@endsection

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        color: #000;
        background-color: #f8f9fc;
        border-color: #dee2e6 #dee2e6 #f8f9fc;
    }
    .tab-content {
        border-top: none;
        padding-top: 1rem;
    }
</style>
@endpush