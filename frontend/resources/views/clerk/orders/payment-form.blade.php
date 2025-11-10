@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Register Payment - Order #{{ $order->id }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Details</h5>
                            <p><strong>Customer:</strong> {{ $order->user->name }}</p>
                            <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                            <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Delivery Information</h5>
                            <p><strong>Recipient:</strong> {{ $order->delivery->recipient_name ?? 'N/A' }}</p>
                            <p><strong>Address:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}</p>
                            <p><strong>Date:</strong> {{ $order->delivery->delivery_date ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form id="paymentForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Mode of Payment <span class="text-danger">*</span></label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="bank">Bank Transfer</option>
                                    </select>
                                    <div class="form-text">You can add more payment methods in the system settings.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               step="0.01" min="0.01" max="{{ $order->total_price }}" 
                                               value="{{ $order->total_price }}" required>
                                    </div>
                                    <div class="form-text">Maximum: ₱{{ number_format($order->total_price, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                           value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="memo" class="form-label">Memo (Optional)</label>
                                    <textarea class="form-control" id="memo" name="memo" rows="2" 
                                              placeholder="Additional notes about this payment..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('clerk.orders.show', $order->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Order
                            </a>
                            <button type="submit" class="btn btn-success" id="validateBtn">
                                <i class="fas fa-check me-2"></i>Validate Payment
                            </button>
                        </div>
                    </form>

                    <!-- Payment History -->
                    @if($order->paymentTracking->count() > 0)
                    <div class="mt-5">
                        <h5>Payment History</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Recorded By</th>
                                        <th>Memo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->paymentTracking as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ strtoupper($payment->payment_method) }}</span>
                                        </td>
                                        <td>₱{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->recordedBy->name ?? 'System' }}</td>
                                        <td>{{ $payment->memo ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Payment Registered Successfully
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Payment has been successfully registered for Order #{{ $order->id }}.</p>
                <p><strong>Amount:</strong> ₱<span id="registeredAmount"></span></p>
                <p><strong>Method:</strong> <span id="registeredMethod"></span></p>
                <p><strong>Date:</strong> <span id="registeredDate"></span></p>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    You will be redirected to the orders page in 3 seconds...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="window.location.href='{{ route('clerk.orders.index') }}'">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const validateBtn = document.getElementById('validateBtn');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable button and show loading
        validateBtn.disabled = true;
        validateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
        const formData = new FormData(form);
        const url = `{{ route('clerk.payment.register', $order->id) }}`;
        
        console.log('Submitting to URL:', url);
        console.log('Form data:', Object.fromEntries(formData));
        
        // Get CSRF token safely
        let csrfTokenValue = '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            csrfTokenValue = csrfToken.getAttribute('content');
        } else {
            // Fallback: get from form
            const csrfInput = form.querySelector('input[name="_token"]');
            if (csrfInput) {
                csrfTokenValue = csrfInput.value;
            }
        }
        
        console.log('CSRF Token:', csrfTokenValue);
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfTokenValue,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(text => {
                    console.log('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response');
                });
            }
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Show success modal
                document.getElementById('registeredAmount').textContent = data.payment.amount;
                document.getElementById('registeredMethod').textContent = data.payment.payment_method.toUpperCase();
                document.getElementById('registeredDate').textContent = new Date(data.payment.payment_date).toLocaleDateString();
                
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Auto redirect after 3 seconds
                setTimeout(() => {
                    window.location.href = '{{ route('clerk.orders.index') }}';
                }, 3000);
            } else {
                // Show error message
                alert('Error: ' + (data.message || 'Failed to register payment'));
                validateBtn.disabled = false;
                validateBtn.innerHTML = '<i class="fas fa-check me-2"></i>Validate Payment';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the payment: ' + error.message);
            validateBtn.disabled = false;
            validateBtn.innerHTML = '<i class="fas fa-check me-2"></i>Validate Payment';
        });
    });
});
</script>
@endpush
