<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #INV-BKG-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #111827;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
            background-color: #ffffff;
        }

        /* Top Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.3px;
            text-transform: uppercase;
        }
        .company-tag {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }
        .invoice-head {
            text-align: right;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .invoice-no {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
            font-weight: 600;
        }

        /* Two-Column Info Grid */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .info-td {
            vertical-align: top;
            width: 50%;
        }
        .section-label {
            font-size: 9px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .client-name {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }
        .client-email {
            font-size: 10px;
            color: #6b7280;
            margin-top: 1px;
        }
        .meta-item {
            font-size: 10px;
            color: #374151;
            margin-bottom: 2px;
            text-align: right;
        }
        .meta-item strong {
            color: #111827;
        }
        .badge-paid {
            display: inline-block;
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f9fafb;
            color: #374151;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            vertical-align: top;
        }
        .item-name {
            font-size: 11px;
            font-weight: 700;
            color: #111827;
        }
        .item-desc {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
            line-height: 1.3;
        }

        /* Summary Section */
        .summary-wrapper {
            width: 100%;
            margin-bottom: 25px;
        }
        .summary-table {
            width: 220px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 4px 8px;
            font-size: 10px;
        }
        .sum-label {
            color: #6b7280;
            text-align: left;
        }
        .sum-val {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }
        .total-row td {
            padding-top: 8px;
            border-top: 1px solid #111827;
            font-size: 12px;
            font-weight: 800;
            color: #111827;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td>
                <div class="company-name">PPHJobDone</div>
                <div class="company-tag">Digital Growth & Agency Services</div>
            </td>
            <td class="invoice-head">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-no">#INV-BKG-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
            </td>
        </tr>
    </table>

    <!-- Billing & Date Info -->
    <table class="info-table">
        <tr>
            <td class="info-td">
                <div class="section-label">Billed To</div>
                <div class="client-name">{{ $booking->user->name ?? 'Valued Client' }}</div>
                <div class="client-email">{{ $booking->user->email ?? 'N/A' }}</div>
            </td>
            <td class="info-td" style="text-align: right;">
                <div class="section-label">Invoice Details</div>
                <div class="meta-item">Date: <strong>{{ $booking->created_at ? $booking->created_at->format('M d, Y') : date('M d, Y') }}</strong></div>
                <div class="meta-item">Payment Method: <strong>Card Payment</strong></div>
                <span class="badge-paid">{{ strtoupper($booking->payment_status ?? 'PAID') }}</span>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 60%;">Description</th>
                <th style="width: 20%; text-align: center;">Billing Type</th>
                <th style="width: 20%; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="item-name">
                        {{ $booking->service?->title ?? ($booking->campaignTier?->campaign?->title ?? ($booking->pricingPlan?->name ?? ($booking->plan_name ?? 'Growth Service Package'))) }}
                    </div>
                    <div class="item-desc">
                        {{ $booking->service?->subtitle ?? ($booking->pricingPlan?->description ?? 'Full SEO optimization, technical auditing, and strategic keyword ranking.') }}
                    </div>
                </td>
                <td style="text-align: center; font-size: 10px; color: #6b7280;">
                    {{ $booking->is_campaign ? 'Campaign Order' : ($booking->pricingPlan?->billing_period ?? 'One-Time') }}
                </td>
                <td style="text-align: right; font-weight: 700; color: #111827;">
                    ${{ number_format((float)($booking->price ?? 0), 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Summary -->
    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td class="sum-label">Subtotal:</td>
                <td class="sum-val">${{ number_format((float)($booking->price ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td class="sum-label">Tax:</td>
                <td class="sum-val">$0.00</td>
            </tr>
            <tr class="total-row">
                <td class="sum-label" style="color: #111827; font-weight: 800;">Total Paid:</td>
                <td class="sum-val" style="font-size: 13px; font-weight: 800;">${{ number_format((float)($booking->price ?? 0), 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        Thank you for choosing PPHJobDone • For billing support, contact support@thesyndicates.team
    </div>

</body>
</html>
