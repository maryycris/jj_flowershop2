<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h1, h2, h3, h4 { margin: 0 0 8px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f3f3; }
    </style>
    </head>
<body>
    <h2>Invoice {{ $invoice->invoice_number }}</h2>
    <div class="mb-3">Date: {{ $invoice->created_at->format('M d, Y') }}</div>

    <h4>Customer</h4>
    <div class="mb-3">
        <div><strong>{{ $invoice->order->user->name }}</strong></div>
        <div>{{ $invoice->order->user->email }}</div>
        <div>{{ $invoice->order->user->contact_number ?? 'N/A' }}</div>
    </div>

    @if($invoice->order->delivery)
    <h4>Delivery</h4>
    <div class="mb-3">
        <div>{{ $invoice->order->delivery->delivery_address }}</div>
        <div>Recipient: {{ $invoice->order->delivery->recipient_name }}</div>
        <div>Phone: {{ $invoice->order->delivery->recipient_phone ?? 'N/A' }}</div>
        <div>Date: {{ $invoice->order->delivery->delivery_date }}</div>
        <div>Time: {{ $invoice->order->delivery->delivery_time }}</div>
    </div>
    @endif

    <h4>Items</h4>
    <table class="mb-3">
        <thead>
            <tr>
                <th>Product</th>
                <th width="80">Qty</th>
                <th width="120" class="text-right">Unit Price</th>
                <th width="120" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->order->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td class="text-right">₱{{ number_format($product->price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($product->pivot->quantity * $product->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Subtotal</th>
                <th class="text-right">₱{{ number_format($invoice->subtotal, 2) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="text-right">Shipping Fee</th>
                <th class="text-right">₱{{ number_format($invoice->shipping_fee, 2) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="text-right">Total Amount</th>
                <th class="text-right">₱{{ number_format($invoice->total_amount, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    @if($invoice->payments->count() > 0)
    <h4>Payments</h4>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Mode</th>
                <th class="text-right">Amount</th>
                <th>Memo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->payments as $p)
            <tr>
                <td>{{ $p->payment_date->format('M d, Y') }}</td>
                <td>{{ strtoupper($p->mode_of_payment) }}</td>
                <td class="text-right">₱{{ number_format($p->amount, 2) }}</td>
                <td>{{ $p->memo }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="mb-2">Status: <strong>{{ strtoupper($invoice->status) }}</strong></div>
    <div>Payment Type: <strong>{{ strtoupper($invoice->payment_type) }}</strong></div>
</body>
</html>


