<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset password</title>
</head>
<body style="font-family:system-ui,sans-serif;line-height:1.5;color:#1a1a1a;">
    <p>Hi {{ $recipientName }},</p>
    <p>We received a request to reset your GroceryGO trader account password.</p>
    <p><a href="{{ $resetUrl }}" style="display:inline-block;padding:10px 18px;background:#16a34a;color:#fff;text-decoration:none;border-radius:6px;">Reset password</a></p>
    <p style="font-size:14px;color:#555;">Or copy this link into your browser:<br>{{ $resetUrl }}</p>
    @if ($expiresAt)
        <p style="font-size:14px;color:#555;">This link expires at {{ $expiresAt->format('g:i A') }}.</p>
    @endif
    <p style="font-size:14px;color:#555;">If you did not request this, you can ignore this email.</p>
</body>
</html>
