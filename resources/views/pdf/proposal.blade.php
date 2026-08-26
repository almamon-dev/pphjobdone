<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AI Proposal - {{ $proposal->title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; padding: 25px; line-height: 1.5; font-size: 14px; }
        .header { border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { font-size: 26px; color: #1e3a8a; margin: 0 0 5px 0; }
        .header p { color: #64748b; margin: 0; font-size: 13px; }
        .meta-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; margin-bottom: 25px; }
        .meta-grid { display: table; width: 100%; }
        .meta-cell { display: table-cell; width: 50%; vertical-align: top; }
        .section-title { font-size: 18px; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; margin-top: 25px; margin-bottom: 12px; }
        .greeting { font-style: italic; color: #334155; margin-bottom: 15px; }
        ul.scope-list { padding-left: 20px; }
        ul.scope-list li { margin-bottom: 6px; }
        .highlight-box { background: #eff6ff; border-left: 4px solid #2563eb; padding: 12px 15px; border-radius: 4px; margin: 20px 0; }
        .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PPHJobDone Project Proposal</h1>
        <p>Generated for {{ $proposal->lead_name ?? $proposal->company_name ?? 'Valued Client' }} &bull; Date: {{ $proposal->created_at->format('F d, Y') }}</p>
    </div>

    <div class="meta-box">
        <div class="meta-grid">
            <div class="meta-cell">
                <strong>Client:</strong> {{ $proposal->lead_name }}<br>
                <strong>Company:</strong> {{ $proposal->company_name ?? 'N/A' }}<br>
                <strong>Website:</strong> {{ $proposal->website_url ?? 'N/A' }}
            </div>
            <div class="meta-cell">
                <strong>Target Service:</strong> {{ $proposal->service?->title ?? $proposal->title }}<br>
                <strong>Estimated Budget:</strong> {{ $proposal->budget ?? 'Custom Quote' }}<br>
                <strong>Proposal ID:</strong> PRO-{{ str_pad($proposal->id, 5, '0', STR_PAD_LEFT) }}
            </div>
        </div>
    </div>

    @php
        $data = $proposal->proposal_data ?? [];
    @endphp

    @if(!empty($data['personalized_greeting']))
        <div class="greeting">
            "{{ $data['personalized_greeting'] }}"
        </div>
    @endif

    <div class="section-title">1. Executive Summary</div>
    <p>{{ $data['executive_summary'] ?? 'Custom tailored strategy proposal aimed at maximizing search performance and lead conversions for your business.' }}</p>

    <div class="section-title">2. Recommended Scope of Work</div>
    @if(!empty($data['recommended_scope']))
        <ul class="scope-list">
            @foreach($data['recommended_scope'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @else
        <p>Custom tailored roadmap including Technical Optimization, Content Strategy, and Performance Monitoring.</p>
    @endif

    <div class="highlight-box">
        <strong>Timeline Estimate:</strong> {{ $data['timeline_estimate'] ?? '4-6 weeks' }}<br>
        <strong>Investment / Pricing:</strong> {{ $data['pricing_recommendation'] ?? ($proposal->budget ?? 'Custom Monthly Subscription') }}
    </div>

    <div class="section-title">3. Why Choose PPHJobDone</div>
    <p>{{ $data['why_choose_us'] ?? 'We deliver data-driven digital solutions powered by AI analytics, proven campaign frameworks, and dedicated account management.' }}</p>

    <div class="footer">
        <p>&copy; {{ date('Y') }} PPHJobDone Digital Agency &bull; Confidential Business Proposal</p>
    </div>
</body>
</html>
