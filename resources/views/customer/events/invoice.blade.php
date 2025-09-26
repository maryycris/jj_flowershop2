<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Invoice</title>
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
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background: #d4edda; color: #155724; }
        .status-unpaid { background: #f8d7da; color: #721c24; }
        .status-partial { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
<div class="invoice-box">
    <div class="header">
        <h1>JJ Flowershop</h1>
        <p>Event Arrangement Invoice</p>
    </div>
    
    <p>
        <strong>Event Order #: </strong> {{ $event->order_id ?? 'EVT-' . str_pad($event->id, 6, '0', STR_PAD_LEFT) . '-' . date('Ymd') }}<br>
        <strong>Event Type: </strong> {{ ucfirst($event->event_type) }}<br>
        <strong>Event Date: </strong> {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}<br>
        <strong>Event Time: </strong> {{ $event->event_time ?? 'Not specified' }}<br>
        <strong>Status: </strong> {{ ucfirst($event->status) }}<br>
        <strong>Payment Status: </strong> 
        <span class="status-badge status-{{ $event->payment_status === 'paid' ? 'paid' : ($event->payment_status === 'partial' ? 'partial' : 'unpaid') }}">
            {{ $event->payment_status === 'paid' ? 'Fully Paid' : ($event->payment_status === 'partial' ? 'Partially Paid' : 'Unpaid') }}
        </span><br>
        <strong>Invoice Date: </strong> {{ now()->format('F d, Y') }}<br>
    </p>

    <div class="section-title">Customer Details</div>
    <p>
        <strong>Name:</strong> {{ $event->user->name ?? 'N/A' }}<br>
        <strong>Email:</strong> {{ $event->user->email ?? 'N/A' }}<br>
        <strong>Contact:</strong> {{ $event->user->contact_number ?? 'N/A' }}<br>
    </p>

    <div class="section-title">Event Information</div>
    <p>
        <strong>Venue:</strong> {{ $event->venue }}<br>
        <strong>Recipient:</strong> {{ $event->recipient_name ?? 'N/A' }}<br>
        <strong>Recipient Phone:</strong> {{ $event->recipient_phone ?? 'N/A' }}<br>
        @if($event->guest_count)
        <strong>Guest Count:</strong> {{ $event->guest_count }}<br>
        @endif
        @if($event->color_scheme)
        <strong>Color Scheme:</strong> {{ $event->color_scheme }}<br>
        @endif
    </p>

    @if($event->personalized_message)
    <div class="section-title">Personalized Message</div>
    <p style="background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;">
        {{ $event->personalized_message }}
    </p>
    @endif

    @if($event->special_instructions)
    <div class="section-title">Special Instructions</div>
    <p style="background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #17a2b8;">
        {{ $event->special_instructions }}
    </p>
    @endif

    <div class="section-title">Selected Products</div>
    @php
        $flowerSelections = session()->get('event_' . $event->id . '_flowers', []);
    @endphp
    
    @if(count($flowerSelections) > 0)
    <table cellpadding="0" cellspacing="0">
        <tr class="heading">
            <td>Product</td>
            <td>Qty</td>
            <td>Unit Price</td>
            <td>Total</td>
        </tr>
        @php $subtotal = 0; @endphp
        @foreach($flowerSelections as $selection)
            @php $lineTotal = $selection['quantity'] * $selection['price']; $subtotal += $lineTotal; @endphp
            <tr class="item">
                <td>{{ $selection['name'] }}</td>
                <td>{{ $selection['quantity'] }}</td>
                <td>₱{{ number_format($selection['price'], 2) }}</td>
                <td>₱{{ number_format($lineTotal, 2) }}</td>
            </tr>
        @endforeach
    </table>
    @else
    <p style="text-align: center; color: #6c757d; font-style: italic;">No products selected yet</p>
    @endif

    <div class="section-title">Cost Breakdown</div>
    <table cellpadding="0" cellspacing="0">
        <tr class="total">
            <td></td>
            <td></td>
            <td><strong>Subtotal:</strong></td>
            <td>₱{{ number_format($event->subtotal ?? 0, 2) }}</td>
        </tr>
        <tr class="total">
            <td></td>
            <td></td>
            <td><strong>Delivery Fee:</strong></td>
            <td>₱{{ number_format($event->delivery_fee ?? 0, 2) }}</td>
        </tr>
        <tr class="total">
            <td></td>
            <td></td>
            <td><strong>Service Fee:</strong></td>
            <td>₱{{ number_format($event->service_fee ?? 0, 2) }}</td>
        </tr>
        <tr class="total" style="border-top: 3px solid #28a745;">
            <td></td>
            <td></td>
            <td><strong>Total Amount:</strong></td>
            <td style="color: #28a745; font-size: 18px;">₱{{ number_format($event->total ?? 0, 2) }}</td>
        </tr>
    </table>

    @if($event->payment_method)
    <div class="section-title">Payment Information</div>
    <p>
        <strong>Payment Method:</strong> {{ strtoupper($event->payment_method) }}<br>
        @if($event->payment_status === 'paid')
        <strong>Payment Date:</strong> {{ $event->updated_at->format('F d, Y \a\t g:i A') }}<br>
        @endif
    </p>
    @endif

    <div style="margin-top: 40px; text-align: center; color: #6c757d; font-size: 14px;">
        <p>Thank you for choosing JJ Flowershop for your special event!</p>
        <p>For inquiries, please contact us at: <strong>+63 XXX XXX XXXX</strong></p>
        <p>Email: <strong>info@jjflowershop.com</strong></p>
    </div>
</div>
</body>
</html>
