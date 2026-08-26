<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome & Onboarding - PPHJobDone</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #065f46; margin: 0; font-size: 24px; }
        .step-list { background: #f8fafc; padding: 15px 20px; border-radius: 6px; margin: 20px 0; list-style-type: none; }
        .step-list li { margin-bottom: 10px; font-size: 14px; }
        .step-list li.completed { color: #10b981; font-weight: bold; }
        .btn { display: inline-block; background-color: #059669; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; margin-top: 15px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome Aboard, {{ $user->name }}! 🎉</h1>
        </div>
        <div class="content">
            <p>Thank you for choosing PPHJobDone! We are thrilled to partner with you on scaling your digital presence.</p>
            <p>To ensure a seamless project launch, we have initiated your personal **Onboarding Checklist**:</p>

            <ul class="step-list">
                @if(!empty($workflow->steps))
                    @foreach($workflow->steps as $step)
                        <li class="{{ $step['status'] === 'completed' ? 'completed' : '' }}">
                            {{ $step['status'] === 'completed' ? '✓' : '○' }} {{ $step['title'] }}
                        </li>
                    @endforeach
                @endif
            </ul>

            <p style="text-align: center;">
                <a href="{{ url('/dashboard') }}" class="btn">Go to Client Onboarding Portal</a>
            </p>

            <p>If you have any questions or require custom assistance, simply reply to this email or send us a message in your client portal.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} PPHJobDone. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
