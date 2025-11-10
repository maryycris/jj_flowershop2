@extends('layouts.admin_app')

@section('admin_content')
<style>
    /* Font and Icon Hierarchy - matching invoice cleanliness */
    .card-title {
        font-size: 1.1rem !important;
        font-weight: 600;
    }

    .card-header h5 {
        font-size: 0.95rem !important;
        font-weight: 600;
    }

    .card-body p {
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .card-body strong {
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Table styling */
    .table {
        font-size: 0.85rem;
    }

    .table thead th {
        font-size: 0.8rem !important;
        font-weight: 600;
        background-color: #e6f4ea;
        padding: 0.5rem 0.75rem;
    }

    .table tbody td {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }

    /* Section headers */
    h6 {
        font-size: 0.9rem !important;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    /* Button sizing */
    .btn {
        font-size: 0.85rem;
    }

    .btn i {
        font-size: 0.85rem;
    }

    /* Badge sizing */
    .badge {
        font-size: 0.8rem;
    }

    /* Clean success message */
    .success-message {
        background-color: #e8f5e8;
        border-left: 3px solid #7bb47b;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .success-message i {
        color: #5aa65a;
        font-size: 1rem;
    }

    .success-message .message-content {
        flex: 1;
    }

    .success-message .message-content strong {
        color: #2d5a2d;
        font-size: 0.9rem;
        display: block;
        margin-bottom: 0.25rem;
    }

    .success-message .message-content span {
        color: #5a7a5a;
        font-size: 0.85rem;
    }

    /* Warning message */
    .warning-message {
        background-color: #fff3cd;
        border-left: 3px solid #ffc107;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .warning-message i {
        color: #ff9800;
        font-size: 1rem;
    }

    .warning-message .message-content {
        flex: 1;
    }

    .warning-message .message-content strong {
        color: #856404;
        font-size: 0.9rem;
        display: block;
        margin-bottom: 0.25rem;
    }
</style>
<div class="container-fluid" style="margin-top: 0.5rem; padding-top: 0.1rem;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Consolidated Card: Everything in One Box -->
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>
                            Generated Invoice Summary
                        </h5>
                        <div>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-eye me-1"></i>View Full Invoice
                            </a>
                            <button class="btn btn-sm btn-success" onclick="window.print()">
                                <i class="fas fa-print me-1"></i>Print
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Success Message -->
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <div class="message-content">
                            <strong>Order Validated Successfully!</strong>
                            <span>Order #{{ $order->id }} has been validated, invoice generated, and driver assigned for delivery.</span>
                        </div>
                    </div>

                    <!-- Invoice and Client Information -->
                    <div class="row mb-4">
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
                    
                    <!-- Products Table -->
                    <div class="mb-4" style="border-top: 1px solid #e0e0e0; padding-top: 1rem;">
                        <h6>Products to be Delivered ({{ $order->products->count() }} items)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
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

                    <!-- Driver Assignment Section -->
                    @php
                        // Get only Mochi Boy driver (filter by username or name)
                        $mochiBoyUser = \App\Models\User::where('role', 'driver')
                            ->where(function($query) {
                                $query->where('username', 'Mochi')
                                      ->orWhere('name', 'like', '%Mochi%');
                            })
                            ->first();
                        
                        // If Mochi Boy exists, get only his driver record
                        if ($mochiBoyUser) {
                            $drivers = \App\Models\Driver::with('user')
                                ->where('user_id', $mochiBoyUser->id)
                                ->where('is_active', true)
                                ->get()
                                ->filter(function($driver) {
                                    return $driver->user !== null;
                                })
                                ->unique('user_id'); // Remove duplicates by user_id
                        } else {
                            // Fallback: get all drivers but remove duplicates
                            $drivers = \App\Models\Driver::with('user')
                                ->where('is_active', true)
                                ->get()
                                ->filter(function($driver) {
                                    return $driver->user !== null;
                                })
                                ->unique('user_id'); // Remove duplicates by user_id
                        }
                    @endphp
                    @if($order->assigned_driver_id)
                    <div style="border-top: 1px solid #e0e0e0; padding-top: 1rem;">
                        <h6>Driver Assignment</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Assigned Driver:</strong></p>
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
                                <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                                    <i class="fas fa-exchange-alt me-1"></i> Change Driver
                                </button>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Delivery Status:</strong></p>
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
                    @else
                    <div style="border-top: 1px solid #e0e0e0; padding-top: 1rem;">
                        <div class="warning-message mb-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div class="message-content">
                                <strong>Driver Assignment Required</strong>
                                <span>No driver has been assigned yet. Please select an available driver for delivery.</span>
                            </div>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                            <i class="fas fa-plus me-1"></i> Assign Driver
                        </button>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-4 pt-3" style="border-top: 1px solid #e0e0e0;">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                    </div>
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
            <form method="POST" action="{{ route('admin.orders.assignDelivery', $order->id) }}">
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
                                    {{ $driver->user ? $driver->user->name : 'Unknown Driver' }} 
                                    ({{ $driver->getAvailabilityText() }})
                                    @if(!$driver->isAvailable())
                                        - Not Available
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="delivery_date" class="form-label">Delivery Date</label>
                        <input type="date" id="delivery_date" name="delivery_date" class="form-control" 
                               value="{{ now()->addDay()->format('Y-m-d') }}" required>
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
                                        <p><strong>Contact:</strong> <span id="driverContact">{{ $drivers->first() && $drivers->first()->user ? $drivers->first()->user->contact_number : 'N/A' }}</span></p>
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
