@extends('layouts.driver_mobile')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-undo me-2"></i>Return Order
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Order Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Details</h6>
                            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                            <p><strong>Customer:</strong> {{ $order->user->name }}</p>
                            <p><strong>Delivery Address:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                            <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Current Status</h6>
                            <span class="badge bg-info fs-6">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                        </div>
                    </div>

                    <hr>

                    <!-- Return Form -->
                    <form id="returnOrderForm">
                        @csrf
                        <div class="mb-4">
                            <label for="return_reason" class="form-label">
                                <strong>Reason for returning the order:</strong>
                            </label>
                            <select class="form-select" id="return_reason" name="return_reason" required>
                                <option value="">Select a reason...</option>
                                <option value="Customer not available">Customer not available</option>
                                <option value="Wrong address provided">Wrong address provided</option>
                                <option value="Customer refused delivery">Customer refused delivery</option>
                                <option value="Package damaged during delivery">Package damaged during delivery</option>
                                <option value="Customer requested return">Customer requested return</option>
                                <option value="Delivery location inaccessible">Delivery location inaccessible</option>
                                <option value="Customer phone unreachable">Customer phone unreachable</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="return_notes" class="form-label">
                                <strong>Additional Notes (Optional):</strong>
                            </label>
                            <textarea class="form-control" id="return_notes" name="return_notes" rows="3" 
                                      placeholder="Provide additional details about the return..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="history.back()">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-warning" id="sendReturnBtn">
                                <i class="fas fa-paper-plane me-1"></i>Send Return Notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('returnOrderForm');
    const sendBtn = document.getElementById('sendReturnBtn');
    const returnReason = document.getElementById('return_reason');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!returnReason.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Required Field',
                text: 'Please select a reason for returning the order.'
            });
            return;
        }

        // Show loading state
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';
        sendBtn.disabled = true;

        // Prepare form data
        const formData = new FormData(form);
        
        // Send AJAX request
        fetch(`{{ route('driver.orders.return.store', $order->id) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Return Notification Sent!',
                    text: data.message,
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    // Redirect back to orders or dashboard
                    window.location.href = '{{ route("driver.orders.index") }}';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
                
                // Reset button
                sendBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Send Return Notification';
                sendBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while sending the return notification.'
            });
            
            // Reset button
            sendBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Send Return Notification';
            sendBtn.disabled = false;
        });
    });
});
</script>
@endsection
