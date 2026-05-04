<!DOCTYPE html>
<html>
<head>
    <title>Manual Payment Approved</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #0a0a1a; color: #ffffff; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #111122; border: 1px solid #39ff14; padding: 30px; border-radius: 8px;">
        <h2 style="color: #39ff14; text-align: center; font-family: sans-serif; letter-spacing: 2px;">BRAG - PAYMENT APPROVED</h2>
        <p>Hi {{ $payment->user->username }},</p>
        <p>Good news! Your manual payment proof has been reviewed and approved. The diamonds have been credited to your wallet.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; border: 1px solid rgba(255,255,255,0.1);">
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Reference ID</th>
                <td style="padding: 10px; text-align: right;">{{ $payment->reference }}</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Item</th>
                <td style="padding: 10px; text-align: right;">{{ $payment->diamonds_amount }} Diamonds</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Amount Paid</th>
                <td style="padding: 10px; text-align: right; color: #39ff14; font-weight: bold;">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
            </tr>
            <tr>
                <th style="padding: 10px; text-align: left; color: #8888aa;">Approval Date</th>
                <td style="padding: 10px; text-align: right;">{{ $payment->updated_at->format('M d, Y h:i A') }}</td>
            </tr>
        </table>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('wallet.index') }}" style="background-color: #39ff14; color: #000000; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; text-transform: uppercase;">View My Wallet</a>
        </div>
        
        <p style="margin-top: 30px; font-size: 0.9em; color: #8888aa; text-align: center;">
            Thank you for being part of the Brag community!
        </p>
    </div>
</body>
</html>
