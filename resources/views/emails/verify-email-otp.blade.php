<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify your GroceryGO Account</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #222;">
    <p>Hello {{ $user->first_name }},</p>

    <p>Thank you for signing up with <strong>GroceryGO</strong>. Use the verification code below to confirm your email address:</p>

    <p style="font-size: 28px; font-weight: bold; letter-spacing: 4px; margin: 24px 0;">
        {{ $otpCode }}
    </p>

    <p>This code expires in <strong>10 minutes</strong>
        @if ($expiresAt)
            (by {{ $expiresAt->format('d M Y H:i') }}).
        @else
            .
        @endif
    </p>

    <p>For your security, do not share this code with anyone. GroceryGO will never ask for it by phone or email.</p>

    <p>If you did not create an account, you can ignore this message.</p>

    <p>— GroceryGO</p>
</body>
</html>
