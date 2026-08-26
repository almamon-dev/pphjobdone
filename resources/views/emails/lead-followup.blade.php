<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Follow Up - PPHJobDone</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #1e3a8a; margin: 0; font-size: 24px; }
        .content { line-height: 1.6; font-size: 15px; }
        .btn { display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PPHJobDone Digital Agency</h1>
        </div>
        <div class="content">
            <p>Hi {{ $lead->name ?? 'there' }},</p>
            <p>I hope this email finds you well! We noticed you recently expressed interest in our {{ $lead->service_interest ?? 'digital marketing' }} solutions for {{ $lead->company_name ?? 'your business' }}.</p>
            
            @if(!empty($customMessage))
                <p>{{ $customMessage }}</p>
            @else
                <p>Our team has reviewed your project requirements ({{ $lead->qualification_summary ?? 'SEO & Growth' }}) and we have tailored actionable strategies to accelerate your search rankings and organic lead generation.</p>
            @endif

            <p>Would you have 10 minutes this week for a brief strategy sync or proposal review?</p>
            
            <p style="text-align: center;">
                <a href="{{ url('/services') }}" class="btn">View Recommended Solutions & Proposals</a>
            </p>

            <p>Best regards,<br><strong>PPHJobDone Growth Team</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} PPHJobDone. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
