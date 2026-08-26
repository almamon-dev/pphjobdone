Hello {{ $user->name }},

Your One-Time Password (OTP) code for {{ $purpose }} is: {{ $otp }}

This code is valid for {{ config('auth.otp_expiry', 60) }} minutes.

If you did not request this OTP code, please ignore this email.

Best regards,
{{ config('app.name') }} Team
