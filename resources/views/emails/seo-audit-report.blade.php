<!DOCTYPE html>
<html>
<head>
    <title>Your AI SEO Audit Report is Ready</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <h2>Hello!</h2>
    
    <p>Your AI-powered SEO Audit for <strong>{{ $audit->url }}</strong> is complete.</p>
    
    <p>We've analyzed your website for technical issues, content quality, and performance optimizations. The full breakdown, including actionable recommendations, is attached to this email as a PDF.</p>

    <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #0ea5e9; margin: 20px 0;">
        <h3 style="margin-top: 0;">Quick Summary</h3>
        <ul style="margin-bottom: 0;">
            <li><strong>Overall Score:</strong> {{ $audit->response_data['overall_score'] ?? 'N/A' }}/100</li>
            <li><strong>Technical SEO:</strong> {{ $audit->response_data['technical_seo_score'] ?? 'N/A' }}/100</li>
            <li><strong>Performance:</strong> {{ $audit->response_data['performance_score'] ?? 'N/A' }}/100</li>
        </ul>
    </div>

    <p>Please review the attached PDF for a detailed section-by-section analysis.</p>

    <p>Best regards,<br>
    The Gajura Team</p>

</body>
</html>
