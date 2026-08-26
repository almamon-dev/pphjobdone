<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI SEO Audit Report</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 560px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);">
                    
                    <!-- Top Brand Header (IELST Emerald Theme) -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; text-align: center; background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 100%); border-bottom: 1px solid #f1f5f9;">
                            <div style="display: inline-block; width: 48px; height: 48px; background-color: #d1fae5; border: 1px solid #a7f3d0; border-radius: 12px; line-height: 48px; text-align: center; font-size: 22px; color: #047857;">
                                📊
                            </div>
                            <h1 style="margin: 14px 0 2px 0; font-size: 22px; font-weight: 900; color: #0f172a; tracking: -0.025em;">
                                {{ config('app.name', 'PPHJobDone') }}
                            </h1>
                            <p style="margin: 0; font-size: 12px; font-weight: 700; color: #047857; text-transform: uppercase; letter-spacing: 0.05em;">
                                Executive AI SEO Audit
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 28px 32px 32px 32px;">
                            <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 1.6; color: #475569;">
                                Your AI-powered SEO Audit for <strong style="color: #047857;">{{ $audit->url }}</strong> is ready. Below is your performance summary:
                            </p>

                            <!-- Score Summary Card (IELST Style) -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                                <h3 style="margin: 0 0 14px 0; font-size: 13px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Audit Metrics Breakdown
                                </h3>
                                
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 13px; font-weight: 500;">Overall SEO Score</td>
                                        <td align="right" style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; font-weight: 900; color: #047857; font-size: 16px;">
                                            {{ $audit->response_data['overall_score'] ?? 'N/A' }}/100
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 13px; font-weight: 500;">Technical SEO</td>
                                        <td align="right" style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; font-weight: 900; color: #0284c7; font-size: 16px;">
                                            {{ $audit->response_data['technical_seo_score'] ?? 'N/A' }}/100
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; color: #64748b; font-size: 13px; font-weight: 500;">Performance Score</td>
                                        <td align="right" style="padding: 10px 0; font-weight: 900; color: #d97706; font-size: 16px;">
                                            {{ $audit->response_data['performance_score'] ?? 'N/A' }}/100
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- CTA Button (IELST Emerald #047857 Button) -->
                            <div style="text-align: center; margin-bottom: 12px;">
                                <a href="{{ url('/api/seo-audit/download?audit_id=' . $audit->id) }}" style="display: inline-block; padding: 14px 32px; background-color: #047857; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 800; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(4, 120, 87, 0.2);">
                                    📥 Download PDF Report
                                </a>
                            </div>
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
