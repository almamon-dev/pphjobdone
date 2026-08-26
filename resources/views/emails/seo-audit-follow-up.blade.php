<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($step === 1)
            Checking in on your SEO Audit
        @elseif($step === 2)
            Let's review your SEO recommendations
        @else
            Final Follow-up: Ready to boost your SEO rankings?
        @endif
    </title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 24px; background-color: #f8fafc;">
    
    <div style="background: #ffffff; border-radius: 12px; padding: 32px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        
        <div style="margin-bottom: 24px; text-align: center;">
            <h1 style="color: #0f172a; font-size: 22px; font-weight: 700; margin: 0;">PPHJobDone SEO Insights</h1>
        </div>

        <h2 style="color: #334155; font-size: 18px; font-weight: 600; margin-top: 0;">Hello there,</h2>
        
        @if($step === 1)
            <!-- Day 2 Follow-Up -->
            <p style="font-size: 15px; color: #475569;">A couple of days ago, we generated an AI-powered SEO Audit for <strong style="color: #0f172a;">{{ $audit->url }}</strong>.</p>
            <p style="font-size: 15px; color: #475569;">Did you have a chance to review the PDF report we sent over? If you have any questions about the technical issues or keyword opportunities highlighted in the report, feel free to reply to this email!</p>
            <div style="margin: 28px 0; text-align: center;">
                <a href="{{ config('app.url') }}/dashboard" style="display: inline-block; padding: 12px 28px; background-color: #0284c7; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">View Your Audit Report</a>
            </div>
        @elseif($step === 2)
            <!-- Day 7 Follow-Up -->
            <p style="font-size: 15px; color: #475569;">It's been a week since we generated your SEO Audit for <strong style="color: #0f172a;">{{ $audit->url }}</strong>.</p>
            <p style="font-size: 15px; color: #475569;">Implementing the recommendations from the audit is the best way to improve your search rankings and drive more organic traffic. If you're unsure where to start, our team can help you prioritize and execute these changes.</p>
            <p style="font-size: 15px; color: #475569;">Would you like to schedule a brief call or check out our specialized SEO growth packages?</p>
            <div style="margin: 28px 0; text-align: center;">
                <a href="{{ config('app.url') }}/dashboard" style="display: inline-block; padding: 12px 28px; background-color: #0ea5e9; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">Explore SEO Services</a>
            </div>
        @else
            <!-- Day 14 Final Follow-Up -->
            <p style="font-size: 15px; color: #475569;">It has been two weeks since your SEO Audit was created for <strong style="color: #0f172a;">{{ $audit->url }}</strong>.</p>
            <p style="font-size: 15px; color: #475569;">This is our final check-in regarding your website audit. Resolving technical bottlenecks, metadata gaps, and backlink deficits early is essential to outperform your competitors on search engines.</p>
            <p style="font-size: 15px; color: #475569;">If you'd like our experienced SEO specialists to handle optimizations and boost your organic visibility, we're ready to get to work.</p>
            <div style="margin: 28px 0; text-align: center;">
                <a href="{{ config('app.url') }}/dashboard" style="display: inline-block; padding: 12px 28px; background-color: #0369a1; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">Start Your SEO Growth Campaign</a>
            </div>
        @endif

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 28px 0;">

        <p style="font-size: 14px; color: #64748b; margin: 0;">Best regards,<br>
        <strong style="color: #334155;">The PPHJobDone Team</strong></p>
    </div>

</body>
</html>

