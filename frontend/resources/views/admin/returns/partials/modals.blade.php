<!-- Return Action Modal -->
<div class="modal fade" id="returnActionModal" tabindex="-1" aria-labelledby="returnActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnActionModalLabel">Return Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="returnActionForm">
                    @csrf
                    <input type="hidden" id="orderId" name="order_id">
                    
                    <div class="mb-3">
                        <label for="return_status" class="form-label">Action</label>
                        <select class="form-select" id="return_status" name="return_status" required>
                            <option value="">Select action...</option>
                            <option value="approved">Approve Return</option>
                            <option value="rejected">Reject Return</option>
                            <option value="resolved">Mark as Resolved</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                  placeholder="Add notes about this return action..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitReturnAction()">Submit Action</button>
            </div>
        </div>
    </div>
</div>

<!-- Refund Processing Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="refundForm">
                    @csrf
                    <input type="hidden" id="refundOrderId" name="order_id">
                    
                    <div class="mb-3">
                        <label for="refund_amount" class="form-label">Refund Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="refund_amount" name="refund_amount" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="refund_method" class="form-label">Refund Method</label>
                        <select class="form-select" id="refund_method" name="refund_method" required>
                            <option value="">Select method...</option>
                            <option value="original_payment">Original Payment Method</option>
                            <option value="store_credit">Store Credit</option>
                            <option value="cash">Cash Refund</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="refund_reason" class="form-label">Refund Reason</label>
                        <input type="text" class="form-control" id="refund_reason" name="refund_reason" 
                               placeholder="Reason for refund amount..." required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processRefund()">Process Refund</button>
            </div>
        </div>
    </div>
</div>
