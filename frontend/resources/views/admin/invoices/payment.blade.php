@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="mx-auto" style="max-width: 800px;">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="mb-0">Register Payment</h2>
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Invoice
                    </a>
                </div>
                
                <!-- Invoice Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Invoice Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                                <p><strong>Customer:</strong> 
                                    @php
                                        $customerName = 'Walk-in Customer';
                                        if ($invoice->order && $invoice->order->notes) {
                                            if (preg_match('/Customer:\s*(.+?)(?:\n|$)/', $invoice->order->notes, $matches)) {
                                                $customerName = trim($matches[1]);
                                            }
                                        }
                                    @endphp
                                    {{ $customerName }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                                <p><strong>Total Amount:</strong> <span class="h5 text-success">₱{{ number_format($invoice->total_amount, 2) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Form -->
                <form method="POST" action="{{ route('invoices.payment.process', $invoice) }}" id="paymentForm">
                    @csrf
                    
                    <!-- Payment Method Selection -->
                    <div class="mb-4">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card payment-method-card" data-method="cash">
                                    <div class="card-body text-center">
                                        <i class="bi bi-cash-stack text-success" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Cash</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card payment-method-card" data-method="bank">
                                    <div class="card-body text-center">
                                        <i class="bi bi-credit-card text-primary" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Bank Transfer</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card payment-method-card" data-method="ewallet">
                                    <div class="card-body text-center">
                                        <i class="bi bi-phone text-warning" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">E-Wallet</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="payment_method" name="payment_method" required>
                        @error('payment_method')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bank Transfer Card Type Selection (Hidden by default) -->
                    <div id="cardTypeSelection" class="mb-2" style="display: none;">
                        <label class="form-label">Card Type <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-type-card" data-type="credit">
                                    <div class="card-body text-center">
                                        <i class="bi bi-credit-card text-success" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Credit Card</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-type-card" data-type="debit">
                                    <div class="card-body text-center">
                                        <i class="bi bi-credit-card text-primary" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Debit Card</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="card_type" name="card_type">
                    </div>

                    <!-- Bank Provider Selection (Hidden by default) -->
                    <div id="bankProviderSelection" class="mb-4" style="display: none;">
                        <label class="form-label">Bank Provider <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bank-provider-card" data-bank="bpi">
                                    <div class="card-body text-center">
                                        <i class="bi bi-bank text-primary" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">BPI</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bank-provider-card" data-bank="bdo">
                                    <div class="card-body text-center">
                                        <i class="bi bi-bank text-success" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">BDO</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bank-provider-card" data-bank="metrobank">
                                    <div class="card-body text-center">
                                        <i class="bi bi-bank text-warning" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Metrobank</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="card bank-provider-card" data-bank="security_bank">
                                    <div class="card-body text-center">
                                        <i class="bi bi-bank text-info" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Security Bank</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bank-provider-card" data-bank="seabank">
                                    <div class="card-body text-center">
                                        <i class="bi bi-bank2 text-info" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">SeaBank</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bank-provider-card" data-bank="rcbc">
                                    <div class="card-body text-center">
                                        <i class="bi bi-bank text-primary" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">RCBC</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="card bank-provider-card" data-bank="other_banks">
                                    <div class="card-body text-center">
                                        <i class="bi bi-building text-secondary" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Other Banks</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="bank_provider" name="bank_provider">
                    </div>

                    <!-- E-Wallet Selection (Hidden by default) -->
                    <div id="ewalletSelection" class="mb-4" style="display: none;">
                        <label class="form-label">E-Wallet Provider <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card ewallet-card" data-ewallet="gcash">
                                    <div class="card-body text-center">
                                        <i class="bi bi-phone text-success" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">GCash</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card ewallet-card" data-ewallet="paymaya">
                                    <div class="card-body text-center">
                                        <i class="bi bi-phone text-primary" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">PayMaya</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="ewallet_provider" name="ewallet_provider">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_mode" class="form-label">Mode of Payment <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="payment_mode" name="payment_mode" readonly>
                                @error('payment_mode')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       value="{{ $invoice->total_amount }}" step="0.01" min="0.01" required>
                                @error('amount')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                       value="{{ date('Y-m-d') }}" required>
                                @error('payment_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="memo" class="form-label">Memo (Optional)</label>
                                <textarea class="form-control" id="memo" name="memo" rows="3" 
                                          placeholder="Additional notes about this payment..."></textarea>
                                @error('memo')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success" id="validateBtn">
                            <i class="bi bi-check-circle me-1"></i>Validate Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentConfirmModal" tabindex="-1" aria-labelledby="paymentConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentConfirmModalLabel">Confirm Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to process this payment?</p>
                <div class="alert alert-info">
                    <strong>Payment Details:</strong><br>
                    <span id="confirmPaymentMode"></span><br>
                    <span id="confirmAmount"></span><br>
                    <span id="confirmDate"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmPaymentBtn">Process Payment</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
.payment-method-card, .card-type-card, .ewallet-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
}

.payment-method-card:hover, .card-type-card:hover, .ewallet-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,123,255,0.2);
}

.payment-method-card.selected, .card-type-card.selected, .ewallet-card.selected {
    border-color: #007bff;
    background-color: #f8f9fa;
    box-shadow: 0 4px 8px rgba(0,123,255,0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const validateBtn = document.getElementById('validateBtn');
    const confirmModal = new bootstrap.Modal(document.getElementById('paymentConfirmModal'));
    
    // Payment method selection
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.addEventListener('click', function() {
            // Remove previous selections
            document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('selected'));
            
            // Select current card
            this.classList.add('selected');
            
            const method = this.dataset.method;
            document.getElementById('payment_method').value = method;
            
            // Show/hide sub-selections
            const cardTypeSelection = document.getElementById('cardTypeSelection');
            const bankProviderSelection = document.getElementById('bankProviderSelection');
            const ewalletSelection = document.getElementById('ewalletSelection');
            
            // Hide all sub-selections first
            cardTypeSelection.style.display = 'none';
            bankProviderSelection.style.display = 'none';
            ewalletSelection.style.display = 'none';
            
            // Clear sub-selections
            document.querySelectorAll('.card-type-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.ewallet-card').forEach(c => c.classList.remove('selected'));
            document.getElementById('card_type').value = '';
            document.getElementById('ewallet_provider').value = '';
            document.getElementById('bank_provider').value = '';
            
            if (method === 'bank') {
                cardTypeSelection.style.display = 'block';
                bankProviderSelection.style.display = 'block';
                // Clear until provider is chosen
                updatePaymentMode('');
            } else if (method === 'ewallet') {
                ewalletSelection.style.display = 'block';
                // Clear until provider is chosen
                updatePaymentMode('');
            } else if (method === 'cash') {
                updatePaymentMode('Cash');
            }
        });
    });
    
    // Card type selection
    document.querySelectorAll('.card-type-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.card-type-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            
            const cardType = this.dataset.type;
            document.getElementById('card_type').value = cardType;
            
            updatePaymentMode('bank transfer');
        });
    });
    
    // E-wallet selection
    document.querySelectorAll('.ewallet-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.ewallet-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            
            const ewallet = this.dataset.ewallet;
            document.getElementById('ewallet_provider').value = ewallet;
            
            // Reflect exact provider (gcash/paymaya) with proper capitalization
            let displayName = ewallet;
            if (ewallet === 'gcash') displayName = 'GCash';
            else if (ewallet === 'paymaya') displayName = 'PayMaya';
            
            updatePaymentMode(displayName);
        });
    });

    // Bank provider selection
    document.querySelectorAll('.bank-provider-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.bank-provider-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');

            const bank = this.dataset.bank;
            document.getElementById('bank_provider').value = bank;

            // Display proper capitalized name in Mode of Payment
            let displayName = bank;
            if (bank === 'rcbc') displayName = 'RCBC';
            else if (bank === 'bpi') displayName = 'BPI';
            else if (bank === 'bdo') displayName = 'BDO';
            else if (bank === 'metrobank') displayName = 'Metrobank';
            else if (bank === 'security_bank') displayName = 'Security Bank';
            else if (bank === 'seabank') displayName = 'SeaBank';
            else if (bank === 'other_banks') displayName = 'Other Banks';
            
            updatePaymentMode(displayName);
        });
    });
    
    function updatePaymentMode(mode) {
        document.getElementById('payment_mode').value = mode;
        
        // Auto-fill amount if empty
        const amountField = document.getElementById('amount');
        if (amountField.value === '') {
            amountField.value = '{{ $invoice->total_amount }}';
        }
    }
    
    // Validate button click handler
    validateBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Show confirmation modal
        const paymentMethod = document.getElementById('payment_method').value;
        const paymentMode = document.getElementById('payment_mode').value;
        const amount = document.getElementById('amount').value;
        const paymentDate = document.getElementById('payment_date').value;
        
        let displayMode = paymentMode;
        if (paymentMethod === 'bank') {
            const cardType = document.getElementById('card_type').value;
            displayMode = `Bank Transfer (${cardType} card)`;
        } else if (paymentMethod === 'ewallet') {
            const ewallet = document.getElementById('ewallet_provider').value;
            displayMode = `E-Wallet (${ewallet})`;
        }
        
        document.getElementById('confirmPaymentMode').textContent = `Mode: ${displayMode}`;
        document.getElementById('confirmAmount').textContent = `Amount: ₱${parseFloat(amount).toFixed(2)}`;
        document.getElementById('confirmDate').textContent = `Date: ${new Date(paymentDate).toLocaleDateString()}`;
        
        confirmModal.show();
    });
    
    // Confirm payment button
    document.getElementById('confirmPaymentBtn').addEventListener('click', function() {
        // Show loading
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        this.disabled = true;
        
        // Submit form
        form.submit();
    });
});
</script>
@endsection
