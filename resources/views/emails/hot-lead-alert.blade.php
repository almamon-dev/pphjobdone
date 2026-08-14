<!DOCTYPE html>
<html>
<head>
    <title>🔥 HOT LEAD ALERT</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <h2 style="color: #e11d48;">🔥 Hot Lead Captured!</h2>
    
    <p>Great news! The AI Chatbot has just qualified a new high-intent lead for <strong>PPHJobDone</strong>.</p>
    
    <div style="background-color: #fff1f2; padding: 18px; border-left: 5px solid #e11d48; margin: 20px 0; border-radius: 4px;">
        <h3 style="margin-top: 0; color: #9f1239;">Lead Profile</h3>
        <ul style="margin-bottom: 0; padding-left: 20px;">
            <li><strong>Name:</strong> {{ $lead->name ?? 'Not provided yet' }}</li>
            <li><strong>Email:</strong> {{ $lead->email ?? 'Not provided yet' }}</li>
            <li><strong>Phone:</strong> {{ $lead->phone ?? 'N/A' }}</li>
            <li><strong>Company/Website:</strong> {{ $lead->company_name ?? 'N/A' }}</li>
            <li><strong>Requested Service:</strong> {{ $lead->service_interest ?? 'N/A' }}</li>
            <li><strong>Budget:</strong> {{ $lead->budget ?? 'N/A' }}</li>
            <li><strong>AI Qualification Note:</strong> {{ $lead->qualification_summary ?? 'High purchase intent detected.' }}</li>
        </ul>
    </div>

    <p><a href="{{ config('app.url') }}/admin/leads" style="display: inline-block; padding: 12px 24px; background-color: #e11d48; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold;">View Full Lead & Chat Transcript in Admin</a></p>

    <p style="margin-top: 30px; font-size: 12px; color: #888;">
        Sent automatically by PPHJobDone AI Lead Management System.
    </p>

</body>
</html>
