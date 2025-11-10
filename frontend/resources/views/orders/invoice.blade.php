<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Invoice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.total td {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <h2>JJ Flowershop</h2>
    <p>
        <strong>Order #: </strong> {{ $order->id }}<br>
        <strong>Date: </strong> {{ $order->created_at->format('F d, Y') }}<br>
        <strong>Status: </strong> {{ ucfirst($order->status) }}<br>
    </p>
    <div class="section-title">Customer Details</div>
    <p>
        <strong>Name:</strong> {{ $order->user->name ?? 'Walk-in' }}<br>
        <strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}<br>
        <strong>Contact:</strong> {{ $order->user->contact_number ?? 'N/A' }}<br>
    </p>
    @if($order->delivery)
    <div class="section-title">Delivery Information</div>
    <p>
        <strong>Recipient:</strong> {{ $order->delivery->recipient_name ?? $order->user->name ?? 'N/A' }}<br>
        <strong>Address:</strong> {{ $order->delivery->delivery_address ?? 'N/A' }}<br>
        <strong>Date:</strong> {{ $order->delivery->delivery_date ? date('F d, Y', strtotime($order->delivery->delivery_date)) : 'N/A' }}<br>
        <strong>Time:</strong> {{ $order->delivery->delivery_time ?? 'N/A' }}<br>
    </p>
    @endif
    <div class="section-title">Products</div>
    <table cellpadding="0" cellspacing="0">
        <tr class="heading">
            <td>Product</td>
            <td>Qty</td>
            <td>Unit Price</td>
            <td>Total</td>
        </tr>
        @php $subtotal = 0; @endphp
        @foreach($order->products as $product)
            @php $lineTotal = $product->pivot->quantity * $product->price; $subtotal += $lineTotal; @endphp
            <tr class="item">
                <td>{{ $product->name }}</td>
                <td>{{ $product->pivot->quantity }}</td>
                <td>₱{{ number_format($product->price, 2) }}</td>
                <td>₱{{ number_format($lineTotal, 2) }}</td>
            </tr>
        @endforeach
        @php
            $shippingFee = $order->delivery->shipping_fee ?? 0;
            
            // If shipping_fee is 0 or null, calculate it from the difference between total_price and subtotal
            if ($shippingFee == 0 && $order->total_price > $subtotal) {
                $shippingFee = $order->total_price - $subtotal;
            }
            
            $grandTotal = $subtotal + $shippingFee;
        @endphp
        <tr class="total">
            <td></td>
            <td></td>
            <td><strong>Subtotal:</strong></td>
            <td>₱{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr class="total">
            <td></td>
            <td></td>
            <td><strong>Shipping:</strong></td>
            <td>₱{{ number_format($shippingFee, 2) }}</td>
        </tr>
        <tr class="total">
            <td></td>
            <td></td>
            <td><strong>Grand Total:</strong></td>
            <td>₱{{ number_format($grandTotal, 2) }}</td>
        </tr>
    </table>
    <br>
    <div class="section-title">Payment</div>
    <p>
        <strong>Method:</strong> {{ strtoupper($order->payment_method ?? 'N/A') }}<br>
        <strong>Status:</strong> {{ ucfirst($order->payment_status ?? 'unpaid') }}<br>
    </p>
    <p>Thank you for your order!</p>
</div>
</body>
</html> 