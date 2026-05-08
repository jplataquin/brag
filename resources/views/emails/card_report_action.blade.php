<!DOCTYPE html>
<html>
<head>
    <title>Card Report Actioned</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #0a0a1a; color: #ffffff; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #111122; border: 1px solid #00f0ff; padding: 30px; border-radius: 8px;">
        <h2 style="color: #00f0ff; text-align: center; font-family: sans-serif; letter-spacing: 2px;">BRAG - REPORT UPDATE</h2>
        <p>Hi {{ $report->user->username }},</p>
        <p>Your report for a digital card has been reviewed by our moderation team.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; border: 1px solid rgba(255,255,255,0.1);">
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Report ID</th>
                <td style="padding: 10px; text-align: right;">#{{ $report->id }}</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Card</th>
                <td style="padding: 10px; text-align: right;">{{ $report->digitalCard->template->card_title }}</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Reason</th>
                <td style="padding: 10px; text-align: right;">{{ ucfirst($report->reason) }}</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <th style="padding: 10px; text-align: left; color: #8888aa;">Status</th>
                <td style="padding: 10px; text-align: right; color: {{ $report->status === 'resolved' ? '#39ff14' : '#ff00ff' }}; font-weight: bold;">
                    {{ strtoupper($report->status === 'resolved' ? 'confirmed' : $report->status) }}
                </td>
            </tr>
            @if($report->admin_notes)
            <tr>
                <th style="padding: 10px; text-align: left; color: #8888aa;">Moderator Notes</th>
                <td style="padding: 10px; text-align: right;">{{ $report->admin_notes }}</td>
            </tr>
            @endif
        </table>
        
        <p style="margin-top: 30px; font-size: 0.9em; color: #8888aa; text-align: center;">
            Thank you for helping keep Brag a safe and competitive environment!
        </p>
    </div>
</body>
</html>
