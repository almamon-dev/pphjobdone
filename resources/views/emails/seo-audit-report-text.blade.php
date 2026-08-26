Hello!

Your AI-powered SEO Audit for {{ $audit->url }} is complete.

We've analyzed your website for technical issues, content quality, and performance optimizations. The full breakdown, including actionable recommendations, is attached to this email as a PDF.

QUICK SUMMARY:
- Overall Score: {{ $audit->response_data['overall_score'] ?? 'N/A' }}/100
- Technical SEO: {{ $audit->response_data['technical_seo_score'] ?? 'N/A' }}/100
- Performance: {{ $audit->response_data['performance_score'] ?? 'N/A' }}/100

Please review the attached PDF for a detailed section-by-section analysis.

Best regards,
The Team
