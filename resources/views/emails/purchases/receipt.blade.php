<!DOCTYPE html>
<html>
<head>
    <title>Shard Purchase Receipt</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #0a0a1a; color: #ffffff; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #111122; border: 1px solid #00f0ff; padding: 30px; border-radius: 8px;">
        <h2 style="color: #00f0ff; text-align: center; font-family: sans-serif; letter-spacing: 2px;">BRAG - RECEIPT</h2>
        <p>Hi {{ $payment->user->username }},</p>
        <p>Thank you for purchasing Shards. Your payment was successful and the shards have been added to your wallet.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; border: 1px solid rgba(255,255,255,0.1);">
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Reference ID</th>
                <td style="padding: 10px; text-align: right;">{{ $payment->reference }}</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Item</th>
                <td style="padding: 10px; text-align: right;">{{ $payment->shards_amount }} Shards</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Amount Paid</th>
                <td style="padding: 10px; text-align: right; color: #39ff14; font-weight: bold;">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
            </tr>
            <tr>
                <th style="padding: 10px; text-align: left; color: #8888aa;">Date</th>
                <td style="padding: 10px; text-align: right;">{{ $payment->updated_at->format('M d, Y h:i A') }}</td>
            </tr>
        </table>
        
        <p style="margin-top: 30px; font-size: 0.9em; color: #8888aa; text-align: center;">
            If you have any questions about this receipt, please contact support.
        </p>
    </div>
</body>
</html>
