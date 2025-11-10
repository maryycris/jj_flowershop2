<div class="modal fade" id="uploadProofModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Proof of Receipt - Order {{ $order->order_code ?? $order->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('driver.orders.receipt.store', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Photo (jpg, png, webp, max 4MB)</label>
                        <input type="file" name="image" class="form-control" accept="image/*" capture="environment" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receiver Name</label>
                        <input type="text" name="receiver_name" class="form-control" placeholder="Who received the item?">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Any additional notes (optional)"></textarea>
                    </div>
                    <input type="hidden" name="received_at" value="{{ now()->toDateTimeLocalString() }}">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
