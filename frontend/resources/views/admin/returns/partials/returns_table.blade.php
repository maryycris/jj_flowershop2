@if($orders->count() > 0)
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-success">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Driver</th>
                <th>Return Reason</th>
                <th>Return Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>
                    <a href="{{ route('admin.returns.show', $order->id) }}" class="text-decoration-none">
                        <strong>#{{ $order->id }}</strong>
                    </a>
                </td>
                <td>
                    <div>
                        <strong>{{ $order->user->name }}</strong><br>
                        <small class="text-muted">{{ $order->user->email }}</small>
                    </div>
                </td>
                <td>
                    @if($order->returnedByDriver)
                        <strong>{{ $order->returnedByDriver->name }}</strong>
                    @else
                        <span class="text-muted">Unknown</span>
                    @endif
                </td>
                <td>
                    <span class="badge" style="background-color: #ffc107; color: #000; font-weight: 600;">{{ $order->return_reason }}</span>
                    @if($order->return_notes)
                        <br><small class="text-muted">{{ Str::limit($order->return_notes, 30) }}</small>
                    @endif
                </td>
                <td>
                    <small>{{ $order->returned_at ? $order->returned_at->format('M d, Y H:i') : 'N/A' }}</small>
                </td>
                <td>
                    <strong>₱{{ number_format($order->total_price, 2) }}</strong>
                </td>
                <td>
                    @switch($order->return_status)
                        @case('pending')
                            <span class="badge bg-info">Pending Review</span>
                            @break
                        @case('approved')
                            <span class="badge bg-success">Approved</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-danger">Rejected</span>
                            @break
                        @case('resolved')
                            <span class="badge bg-secondary">Resolved</span>
                            @break
                        @default
                            <span class="badge bg-secondary">{{ ucfirst($order->return_status) }}</span>
                    @endswitch
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.returns.show', $order->id) }}" 
                           class="btn btn-outline-primary btn-sm" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        @if($order->return_status === 'pending')
                            <button class="btn btn-outline-success btn-sm" 
                                    onclick="showReturnAction({{ $order->id }}, '{{ $order->return_status }}')" 
                                    title="Take Action">
                                <i class="fas fa-cog"></i>
                            </button>
                        @endif
                        
                        @if($order->return_status === 'approved' && !$order->refund_processed_at)
                            <button class="btn btn-outline-warning btn-sm" 
                                    onclick="showRefundModal({{ $order->id }}, {{ $order->total_price }})" 
                                    title="Process Refund">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                        @endif
                        
                        @if($order->refund_processed_at)
                            <span class="badge bg-success" title="Refund Processed">
                                <i class="fas fa-check"></i> Refunded
                            </span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if(method_exists($orders, 'hasPages') && $orders->hasPages())
<div class="d-flex justify-content-center mt-3">
    {{ $orders->links() }}
</div>
@endif

@else
<div class="text-center py-5">
    <i class="fas fa-inbox display-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No returned orders found</h5>
    <p class="text-muted">
        @if($filter === 'pending')
            No orders are pending review at the moment.
        @elseif($filter === 'approved')
            No approved returns found.
        @else
            No returned orders found.
        @endif
    </p>
</div>
@endif
