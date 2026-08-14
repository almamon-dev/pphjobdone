<!DOCTYPE html>
<html>
<head>
    <title>{{ $step === 1 ? 'Checking in on your SEO Audit' : 'Let\'s review your SEO recommendations' }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <h2>Hello there,</h2>
    
    @if($step === 1)
        <p>A couple of days ago, we generated an AI-powered SEO Audit for <strong>{{ $audit->url }}</strong>.</p>
        <p>Did you have a chance to review the PDF report we sent over? If you have any questions about the technical issues or keyword opportunities highlighted in the report, feel free to reply to this email!</p>
    @else
        <p>It's been a week since we generated your SEO Audit for <strong>{{ $audit->url }}</strong>.</p>
        <p>Implementing the recommendations from the audit is the best way to improve your search rankings and drive more organic traffic. If you're unsure where to start, our team can help you prioritize and execute these changes.</p>
        <p>Would you like to schedule a brief call to discuss how we can help you achieve better results?</p>
        <p><a href="{{ config('app.url') }}" style="display: inline-block; padding: 10px 20px; background-color: #0ea5e9; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 10px;">Visit Your Dashboard</a></p>
    @endif

    <p style="margin-top: 30px;">Best regards,<br>
    The Gajura Team</p>

</body>
</html>
