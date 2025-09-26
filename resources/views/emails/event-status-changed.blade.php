<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Status Update</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            margin: 10px 0;
        }
        .event-details {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .event-details h3 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 16px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            min-width: 80px;
            margin-right: 10px;
        }
        .detail-value {
            color: #495057;
        }
        .cta-button {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 Event Status Update</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Your event status has been updated</p>
        </div>
        
        <div class="content">
            <h2 style="color: #495057; margin-top: 0;">Hello {{ $event->user->first_name }}!</h2>
            
            <p>Your event <strong>{{ $event->event_type }}</strong> {{ $message }}.</p>
            
            <div class="status-badge" style="background-color: {{ $color }};">
                {{ ucfirst($newStatus) }}
            </div>
            
            <div class="event-details">
                <h3>📅 Event Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value">{{ $event->event_type }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}</span>
                </div>
                @if($event->event_time)
                <div class="detail-row">
                    <span class="detail-label">Time:</span>
                    <span class="detail-value">{{ $event->event_time }}</span>
                </div>
                @endif
                @if($event->venue)
                <div class="detail-row">
                    <span class="detail-label">Venue:</span>
                    <span class="detail-value">{{ $event->venue }}</span>
                </div>
                @endif
                @if($event->recipient)
                <div class="detail-row">
                    <span class="detail-label">Recipient:</span>
                    <span class="detail-value">{{ $event->recipient }}</span>
                </div>
                @endif
            </div>
            
            @if($newStatus === 'confirmed')
            <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; padding: 15px; margin: 20px 0;">
                <h4 style="color: #155724; margin: 0 0 10px 0;">✅ Great News!</h4>
                <p style="color: #155724; margin: 0;">Your event has been confirmed! We're excited to help make your special day memorable.</p>
            </div>
            @elseif($newStatus === 'cancelled')
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 6px; padding: 15px; margin: 20px 0;">
                <h4 style="color: #721c24; margin: 0 0 10px 0;">⚠️ Event Cancelled</h4>
                <p style="color: #721c24; margin: 0;">We're sorry to hear that your event has been cancelled. If you need to reschedule, please contact us.</p>
            </div>
            @elseif($newStatus === 'completed')
            <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 6px; padding: 15px; margin: 20px 0;">
                <h4 style="color: #0c5460; margin: 0 0 10px 0;">🎊 Event Completed</h4>
                <p style="color: #0c5460; margin: 0;">Thank you for choosing JJ Flowershop! We hope your event was everything you dreamed of.</p>
            </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ url('/customer/events/' . $event->id) }}" class="cta-button">
                    View Event Details
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>JJ Flowershop</strong></p>
            <p>Thank you for choosing us for your special events!</p>
            <p style="font-size: 12px; margin-top: 15px;">
                This is an automated notification. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
