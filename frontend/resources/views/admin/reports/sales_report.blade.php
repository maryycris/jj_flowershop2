@extends('layouts.admin_app')

@section('content')
<div class="container-fluid py-3">
    <h3 class="mb-3">Sales Report</h3>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label mb-0">From</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-auto">
            <label class="form-label mb-0">To</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order Code</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Created</th>
                    <th>Proof</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_code ?? ('ORD-'.$order->id) }}</a></td>
                    <td>{{ optional($order->user)->full_name ?? optional($order->user)->name ?? 'N/A' }}</td>
                    <td>{{ $order->status ?? 'N/A' }}</td>
                    <td>₱{{ number_format($order->total_price ?? $order->total_amount ?? 0, 2) }}</td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td>
                        @if($order->latestDeliveryReceipt)
                            <a href="#" data-bs-toggle="modal" data-bs-target="#proofModal{{ $order->id }}">
                                <img src="{{ Storage::url($order->latestDeliveryReceipt->image_path) }}" alt="Proof" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">
                            </a>
                        @else
                            <span class="badge bg-secondary">None</span>
                        @endif
                    </td>
                </tr>

                @if($order->latestDeliveryReceipt)
                <div class="modal fade" id="proofModal{{ $order->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Proof of Receipt - Order {{ $order->order_code ?? $order->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <img src="{{ Storage::url($order->latestDeliveryReceipt->image_path) }}" alt="Proof" class="img-fluid rounded border">
                                    </div>
                                    <div class="col-md-4">
                                        <dl class="row">
                                            <dt class="col-5">Receiver</dt>
                                            <dd class="col-7">{{ $order->latestDeliveryReceipt->receiver_name ?? 'N/A' }}</dd>

                                            <dt class="col-5">Received At</dt>
                                            <dd class="col-7">{{ optional($order->latestDeliveryReceipt->received_at)->format('M d, Y H:i') ?? 'N/A' }}</dd>

                                            <dt class="col-5">Notes</dt>
                                            <dd class="col-7">{{ $order->latestDeliveryReceipt->notes ?? 'None' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a class="btn btn-outline-secondary" href="{{ Storage::url($order->latestDeliveryReceipt->image_path) }}" download>Download</a>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $orders->links() }}
    </div>
</div>
@endsection
