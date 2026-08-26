<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $purpose }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 520px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);">
                    
                    <!-- Top Brand Header (IELST Emerald Theme) -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; text-align: center; background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 100%); border-bottom: 1px solid #f1f5f9;">
                            <div style="display: inline-block; width: 48px; height: 48px; background-color: #d1fae5; border: 1px solid #a7f3d0; border-radius: 12px; line-height: 48px; text-align: center; font-size: 22px; color: #047857;">
                                ✉️
                            </div>
                            <h1 style="margin: 14px 0 2px 0; font-size: 22px; font-weight: 900; color: #0f172a; tracking: -0.025em;">
                                {{ config('app.name', 'PPHJobDone') }}
                            </h1>
                            <p style="margin: 0; font-size: 12px; font-weight: 700; color: #047857; text-transform: uppercase; letter-spacing: 0.05em;">
                                Security Verification
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 28px 32px 32px 32px;">
                            <h2 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 800; color: #0f172a; text-align: center;">
                                {{ $purpose }}
                            </h2>
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #64748b; text-align: center;">
                                Hello <strong style="color: #0f172a;">{{ $user->name }}</strong>, please use the 4-digit verification code below to confirm your request:
                            </p>

                            <!-- OTP Card Box (IELST Card Style) -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px;">
                                <div style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 40px; font-weight: 900; color: #047857; letter-spacing: 10px; margin-bottom: 8px;">
                                    {{ $otp }}
                                </div>
                                <span style="display: inline-block; font-size: 11px; font-weight: 700; color: #047857; background-color: #d1fae5; border: 1px solid #a7f3d0; padding: 4px 12px; border-radius: 20px;">
                                    ⏱️ Code expires in {{ config('auth.otp_expiry', 60) }} minutes
                                </span>
                            </div>

                            <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #94a3b8; text-align: center;">
                                If you did not initiate this request, no further action is required. Please keep your account details secure.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
