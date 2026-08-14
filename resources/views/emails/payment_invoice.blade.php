<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Confirmation & Invoice</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #0f172a;
            padding: 24px 30px;
            text-align: left;
            border-bottom: 2px solid #334155;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 11px;
            color: #94a3b8;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .text {
            font-size: 13px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 20px;
        }
        .box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }
        .row-item {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .row-item:last-child {
            border-bottom: none;
        }
        .label {
            color: #64748b;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            float: left;
        }
        .value {
            color: #0f172a;
            font-weight: 700;
            float: right;
        }
        .clear {
            clear: both;
        }
        .btn {
            display: inline-block;
            text-align: center;
            background-color: #0f172a;
            color: #ffffff !important;
            text-decoration: none;
            padding: 11px 22px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .footer {
            padding: 18px 30px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PPHJobDone</h1>
            <p>Payment Confirmation • Invoice #INV-BKG-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="content">
            <div class="greeting">Hi {{ $booking->user->name ?? 'Valued Customer' }},</div>
            <div class="text">
                Thank you for your purchase! We have successfully received your payment. Your project roadmap and task deliverables are now live on your account dashboard.
            </div>

            <div class="box">
                <div class="row-item">
                    <span class="label">Invoice Number:</span>
                    <span class="value">#INV-BKG-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                    <div class="clear"></div>
                </div>
                <div class="row-item">
                    <span class="label">Purchased Item:</span>
                    <span class="value">{{ $booking->service?->title ?? ($booking->campaignTier?->campaign?->title ?? ($booking->pricingPlan?->name ?? ($booking->plan_name ?? 'Growth Package'))) }}</span>
                    <div class="clear"></div>
                </div>
                <div class="row-item">
                    <span class="label">Total Paid:</span>
                    <span class="value" style="color: #0f172a; font-size: 14px;">${{ number_format((float)($booking->price ?? 0), 2) }} USD</span>
                    <div class="clear"></div>
                </div>
                <div class="row-item">
                    <span class="label">Payment Status:</span>
                    <span class="value" style="color: #047857; font-weight: 800;">PAID (Verified)</span>
                    <div class="clear"></div>
                </div>
            </div>

            <div class="text">
                We have attached your official PDF invoice (<strong>Invoice-INV-BKG-{{ $booking->id }}.pdf</strong>) directly to this email.
            </div>

            <a href="https://pphjobdone.com/dashboard/progress-and-tasks" class="btn">View Project Progress & Tasks</a>
        </div>

        <div class="footer">
            <p style="margin: 0;">If you have any questions, please reply to this email or contact support@thesyndicates.team</p>
        </div>
    </div>
</body>
</html>
