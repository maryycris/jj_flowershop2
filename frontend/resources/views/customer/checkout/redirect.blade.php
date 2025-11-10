<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Payment...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .redirect-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #28a745;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            color: #28a745;
            margin-bottom: 1rem;
        }
        p {
            color: #666;
            margin-bottom: 1rem;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="redirect-container">
        <div class="spinner"></div>
        <h2>Redirecting to Payment Gateway...</h2>
        <p>Please wait while we redirect you to complete your payment.</p>
        <p>If you are not redirected automatically, <a href="{{ $checkout_url }}" class="btn">Click Here</a></p>
    </div>

    <script>
        console.log('Redirecting to PayMongo:', '{{ $checkout_url }}');
        
        // Immediate redirect
        window.location.href = '{{ $checkout_url }}';
        
        // Fallback redirect after 3 seconds
        setTimeout(function() {
            if (window.location.href === '{{ url()->current() }}') {
                console.log('Fallback redirect triggered');
                window.location.href = '{{ $checkout_url }}';
            }
        }, 3000);
    </script>
</body>
</html>
