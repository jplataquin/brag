<!DOCTYPE html>
<html>
<head>
    <title>New Update on your Manual Payment</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #0a0a1a; color: #ffffff; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #111122; border: 1px solid #00f0ff; padding: 30px; border-radius: 8px;">
        <h2 style="color: #00f0ff; text-align: center;">BRAG - NEW UPDATE</h2>
        <p>Hi {{ $payment->user->username }},</p>
        <p>An administrator has posted a new comment regarding your manual payment request <strong>{{ $payment->reference }}</strong>.</p>
        
        <div style="background-color: rgba(255,255,255,0.05); border-left: 4px solid #00f0ff; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; font-style: italic;">"{{ $comment->comment }}"</p>
        </div>

        <p>You can view the full discussion and reply by clicking the button below:</p>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('payments.show', $payment->id) }}" style="background-color: #00f0ff; color: #000000; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; text-transform: uppercase;">View Discussion</a>
        </div>
    </div>
</body>
</html>
