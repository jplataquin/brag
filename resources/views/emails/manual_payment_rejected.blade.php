<!DOCTYPE html>
<html>
<head>
    <title>Manual Payment Rejected</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #0a0a1a; color: #ffffff; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #111122; border: 1px solid #ff0000; padding: 30px; border-radius: 8px;">
        <h2 style="color: #ff0000; text-align: center; font-family: sans-serif; letter-spacing: 2px;">BRAG - PAYMENT REJECTED</h2>
        <p>Hi {{ $payment->user->username }},</p>
        <p>We're sorry, but your manual payment proof for reference <strong>{{ $payment->reference }}</strong> has been rejected by our team.</p>
        
        @if($reason)
            <div style="background-color: rgba(255,0,0,0.1); border-left: 4px solid #ff0000; padding: 15px; margin: 20px 0;">
                <p style="margin: 0; font-weight: bold; color: #ff0000;">Reason for rejection:</p>
                <p style="margin: 5px 0 0 0;">{{ $reason }}</p>
            </div>
        @endif

        <p>If you believe this is a mistake, please try submitting a clearer proof of payment or contact our support team for assistance.</p>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('wallet.index') }}" style="background-color: #ff0000; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; text-transform: uppercase;">Go to Wallet</a>
        </div>
        
        <p style="margin-top: 30px; font-size: 0.9em; color: #8888aa; text-align: center;">
            If you have any questions, please reply to this email.
        </p>
    </div>
</body>
</html>
