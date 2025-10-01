@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Success Alert -->
            <div class="alert alert-success d-flex align-items-center mb-4">
                <i class="fas fa-check-circle me-3"></i>
                <div>
                    <h5 class="mb-1">Order Validated Successfully!</h5>
                    <p class="mb-0">Order #{{ $order->id }} has been validated, invoice generated, and driver assigned for delivery.</p>
                </div>
            </div>

            <!-- Invoice Summary Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Generated Invoice Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Invoice Details</h6>
                            <p><strong>Invoice Number:</strong> {{ $invoiceData['invoice_number'] ?? 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                            <p><strong>Generated Date:</strong> {{ $invoiceData['generated_date'] ?? now()->format('M d, Y') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $order->invoice_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->invoice_status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Client Information</h6>
                            <p><strong>Name:</strong> {{ $order->user->name }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email }}</p>
                            <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Products to be Delivered ({{ $order->products->count() }} items)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->products as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->pivot->quantity }}</td>
                                        <td>₱{{ number_format($product->price, 2) }}</td>
                                        <td>₱{{ number_format($product->pivot->quantity * $product->price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver Assignment Card -->
            @php
                $drivers = \App\Models\Driver::with('user')->where('is_active', true)->get();
            @endphp
            @if($order->assigned_driver_id)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>
                        Driver Assignment
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Assigned Driver</h6>
                            @if($order->assignedDriver)
                                <p><strong>Name:</strong> {{ $order->assignedDriver->name ?? 'N/A' }}</p>
                                <p><strong>Contact:</strong> {{ $order->assignedDriver->contact_number ?? 'N/A' }}</p>
                                @if($order->assignedDriver->driver)
                                    <p><strong>Vehicle:</strong> {{ $order->assignedDriver->driver->vehicle_type ?? 'N/A' }} ({{ $order->assignedDriver->driver->vehicle_plate ?? 'N/A' }})</p>
                                    <p><strong>License:</strong> {{ $order->assignedDriver->driver->license_number ?? 'N/A' }}</p>
                                @endif
                            @else
                                <p class="text-muted">Driver information not available</p>
                            @endif
                            <button class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                                <i class="fas fa-exchange-alt me-1"></i> Change Driver
                            </button>
                        </div>
                        <div class="col-md-6">
                            <h6>Delivery Status</h6>
                            <p><strong>Status:</strong> 
                                @if($order->order_status === 'on_delivery')
                                    <span class="badge bg-info">On Delivery</span>
                                @elseif($order->order_status === 'approved')
                                    <span class="badge bg-warning">Ready for Delivery</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->order_status) }}</span>
                                @endif
                            </p>
                            <p><strong>Assigned:</strong> {{ $order->on_delivery_at ? \Carbon\Carbon::parse($order->on_delivery_at)->format('M d, Y g:i A') : 'Not assigned yet' }}</p>
                            @if($order->assignedDriver && $order->assignedDriver->driver)
                                <p><strong>Driver Status:</strong> 
                                    <span class="badge {{ $order->assignedDriver->driver->getAvailabilityBadgeClass() }}">
                                        {{ $order->assignedDriver->driver->getAvailabilityText() }}
                                    </span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Driver Assignment Required
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">No driver has been assigned yet. Please select an available driver for delivery.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                        <i class="fas fa-plus me-1"></i> Assign Driver
                    </button>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('clerk.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
                <div>
                    <a href="{{ route('clerk.orders.show', $order->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-eye me-2"></i>View Full Invoice
                    </a>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Assign Driver Modal -->
<div class="modal fade" id="assignDriverModal" tabindex="-1" aria-labelledby="assignDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignDriverModalLabel">Assign Driver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('clerk.orders.assignDelivery', $order->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="driver_id" class="form-label">Select Driver</label>
                        <select id="driver_id" name="driver_id" class="form-select" required>
                            <option value="" selected disabled>Choose a driver...</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->user_id }}" 
                                        data-availability="{{ $driver->availability_status }}"
                                        data-vehicle="{{ $driver->vehicle_type }}"
                                        data-license="{{ $driver->license_number }}"
                                        data-deliveries="{{ $driver->current_deliveries_today }}/{{ $driver->max_deliveries_per_day }}"
                                        data-work-hours="{{ $driver->work_start_time->format('H:i') }} - {{ $driver->work_end_time->format('H:i') }}"
                                        {{ !$driver->isAvailable() ? 'disabled' : '' }}>
                                    {{ $driver->user->name }} 
                                    ({{ $driver->getAvailabilityText() }})
                                    @if(!$driver->isAvailable())
                                        - Not Available
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Driver Information Display -->
                    <div id="driverInfo" class="mt-3" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Driver Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Contact:</strong> <span id="driverContact">{{ $drivers->first()->user->contact_number ?? 'N/A' }}</span></p>
                                        <p><strong>Vehicle:</strong> <span id="driverVehicle">N/A</span></p>
                                        <p><strong>License:</strong> <span id="driverLicense">N/A</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Status:</strong> <span id="driverStatus" class="badge">N/A</span></p>
                                        <p><strong>Deliveries Today:</strong> <span id="driverDeliveries">N/A</span></p>
                                        <p><strong>Work Hours:</strong> <span id="driverWorkHours">N/A</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="assignBtn" disabled>Assign Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const driverSelect = document.getElementById('driver_id');
    const driverInfo = document.getElementById('driverInfo');
    const assignBtn = document.getElementById('assignBtn');
    
    driverSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            // Show driver information
            driverInfo.style.display = 'block';
            
            // Update driver details
            document.getElementById('driverContact').textContent = selectedOption.textContent.split(' (')[0];
            document.getElementById('driverVehicle').textContent = selectedOption.dataset.vehicle || 'N/A';
            document.getElementById('driverLicense').textContent = selectedOption.dataset.license || 'N/A';
            document.getElementById('driverDeliveries').textContent = selectedOption.dataset.deliveries || 'N/A';
            document.getElementById('driverWorkHours').textContent = selectedOption.dataset.workHours || 'N/A';
            
            // Update status badge
            const statusSpan = document.getElementById('driverStatus');
            const availability = selectedOption.dataset.availability;
            statusSpan.textContent = availability.charAt(0).toUpperCase() + availability.slice(1).replace('_', ' ');
            statusSpan.className = 'badge ' + getStatusClass(availability);
            
            // Enable/disable assign button based on availability
            if (availability === 'available') {
                assignBtn.disabled = false;
                assignBtn.textContent = 'Assign Driver';
            } else {
                assignBtn.disabled = true;
                assignBtn.textContent = 'Driver Not Available';
            }
        } else {
            driverInfo.style.display = 'none';
            assignBtn.disabled = true;
        }
    });
    
    function getStatusClass(status) {
        switch(status) {
            case 'available': return 'bg-success';
            case 'busy': return 'bg-warning';
            case 'off_duty': return 'bg-secondary';
            case 'on_delivery': return 'bg-info';
            default: return 'bg-secondary';
        }
    }
});
</script>
@endsection


