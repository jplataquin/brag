<!DOCTYPE html>
<html>
<head>
    <title>User Response: Manual Payment</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #0a0a1a; color: #ffffff; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #111122; border: 1px solid #ffdd00; padding: 30px; border-radius: 8px;">
        <h2 style="color: #ffdd00; text-align: center;">BRAG - USER RESPONSE</h2>
        <p>A user has replied to the discussion thread for manual payment <strong>{{ $payment->reference }}</strong>.</p>
        
        <p><strong>User:</strong> {{ $comment->user->username }}</p>

        <div style="background-color: rgba(255,255,255,0.05); border-left: 4px solid #ffdd00; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; font-style: italic;">"{{ $comment->comment }}"</p>
        </div>

        <p>Please review the response in the admin panel:</p>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('admin.payments.show', $payment->id) }}" style="background-color: #ffdd00; color: #000000; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; text-transform: uppercase;">View in Admin Panel</a>
        </div>
    </div>
</body>
</html>
