@extends('layouts.admin_app')
@section('content')
<div class="container-fluid">
    <div class="mx-auto" style="max-width: 1200px;">
    @php $type = request('type', 'online'); @endphp
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="mb-0">{{ $type == 'online' ? 'Online Orders' : 'Walk-in Orders' }}</h2>
                @if($type == 'walkin')
                    <a href="{{ route('admin.orders.create') }}" class="btn btn-success">Add New</a>
                @endif
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <form method="GET" action="" class="d-flex">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search by name or order number..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-success">Search</button>
                    </form>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option selected>Region</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control">
                </div>
            </div>
            <div class="table-responsive orders-table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Order Number</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($type == 'online')
                            @forelse($onlineOrders as $order)
                                @php
                                    $statusClass = 'bg-warning text-dark';
                                    $statusText = ucfirst($order->status ?? 'pending');
                                    $redirectUrl = route('admin.orders.online.invoice', $order); // default like clerk

                                    if ($order->order_status) {
                                        switch($order->order_status) {
                                            case 'approved':
                                                $statusClass = 'bg-success';
                                                $statusText = 'Approved';
                                                $redirectUrl = route('admin.orders.online.done', $order);
                                                break;
                                            case 'on_delivery':
                                                $statusClass = 'bg-info';
                                                $statusText = 'On Delivery';
                                                $redirectUrl = route('admin.orders.online.done', $order);
                                                break;
                                            case 'completed':
                                                $statusClass = 'bg-primary';
                                                $statusText = 'Completed';
                                                $redirectUrl = route('admin.orders.online.done', $order);
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'bg-danger';
                                                $statusText = 'Cancelled';
                                                $redirectUrl = route('admin.orders.online.done', $order);
                                                break;
                                            default:
                                                $statusClass = 'bg-warning text-dark';
                                                $statusText = 'Pending';
                                        }
                                    }
                                @endphp
                                <tr class="cursor-pointer" onclick="window.location='{{ $redirectUrl }}'">
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('m/d/Y') }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                    <td>₱{{ number_format($order->total_price, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No online orders found.</td></tr>
                            @endforelse
                        @else
                            @forelse($walkInOrders as $order)
                                <tr class="cursor-pointer" onclick="window.location='{{ route('admin.orders.walkin.quotation', $order) }}'">
                                    <td>{{ $order->user->name ?? 'Walk-in Customer' }}</td>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('m/d/Y') }}</td>
                                    <td>
                                        @if($order->status === 'quotation')
                                            <span class="badge bg-warning text-dark">Quotation</span>
                                        @elseif($order->status === 'sales_order')
                                            <span class="badge bg-success">SALES ORDER</span>
                                        @elseif($order->status === 'validated')
                                            <span class="badge bg-info">Validated</span>
                                        @elseif($order->status === 'done')
                                            <span class="badge bg-success">Done</span>
                                        @elseif($order->status === 'approved')
                                            <span class="badge bg-primary">Approved</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td>₱{{ number_format($order->total_price, 2) }}</td>
                                    <td>
                                        @if($order->status === 'approved')
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignDeliveryModal{{ $order->id }}">Assign for Delivery</button>
                                        @endif
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-secondary">Invoice</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No walk-in orders found.</td></tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert Success Message -->
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                html: `
                    <div class="text-start">
                        <p>{{ session('success') }}</p>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="dontShowAgain">
                            <label class="form-check-label" for="dontShowAgain">
                                Don't show this message again
                            </label>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                allowOutsideClick: true,
                didOpen: () => {
                    // Auto-dismiss after 5 seconds
                    setTimeout(() => {
                        Swal.close();
                    }, 5000);
                }
            });
        });
    </script>
@endif

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
              <form action="{{ route('admin.orders.assignDelivery', $order->id) }}" method="POST">
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
              <form action="{{ route('admin.orders.assignDelivery', $order->id) }}" method="POST">
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
    </div>
</div>
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
    .cursor-pointer {
        cursor: pointer;
    }
    
    /* Orders table scrollbar styling */
    .orders-table-container {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        background: #fff;
    }
    
    .orders-table-container::-webkit-scrollbar {
        width: 8px;
    }
    
    .orders-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .orders-table-container::-webkit-scrollbar-thumb {
        background: #5E8458;
        border-radius: 4px;
    }
    
    .orders-table-container::-webkit-scrollbar-thumb:hover {
        background: #4a6b45;
    }
</style>
@endpush

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Custom SweetAlert function with checkbox and auto-dismiss
    function showSweetAlertWithCheckbox(title, message, icon = 'success', timer = 5000) {
        return Swal.fire({
            title: title,
            html: `
                <div class="text-start">
                    <p>${message}</p>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="dontShowAgain">
                        <label class="form-check-label" for="dontShowAgain">
                            Don't show this message again
                        </label>
                    </div>
                </div>
            `,
            icon: icon,
            showConfirmButton: false,
            timer: timer,
            timerProgressBar: true,
            allowOutsideClick: true,
            didOpen: () => {
                // Auto-dismiss after specified time
                setTimeout(() => {
                    Swal.close();
                }, timer);
            }
        });
    }

    // Show success message on page load if exists
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showSweetAlertWithCheckbox('Success!', '{{ session('success') }}', 'success', 5000);
        @endif
    });
</script>
@endpush 