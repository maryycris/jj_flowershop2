<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; }
        .container { background: #fff; padding: 24px; border-radius: 8px; max-width: 400px; margin: 40px auto; box-shadow: 0 2px 8px #eee; }
        .code { font-size: 2rem; color: #198754; letter-spacing: 4px; font-weight: bold; }
        .footer { margin-top: 24px; font-size: 0.9rem; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ $user->first_name ?? $user->name }},</h2>
        <p>Thank you for registering at <strong>JJ Flowershop</strong>!</p>
        <p>Your verification code is:</p>
        <div class="code">{{ $user->verification_code }}</div>
        <p style="margin-top:20px;">Please enter this code in the website to verify your account.<br>For your security, this code will expire in 10 minutes.</p>
        <div class="footer">
            If you did not request this, you can ignore this email.<br>
            &copy; {{ date('Y') }} JJ Flowershop
        </div>
    </div>
</body>
</html> 