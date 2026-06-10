<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campaign Progress Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #673ab7; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #673ab7; margin: 0; font-size: 28px; text-transform: uppercase; }
        .header p { color: #666; margin: 5px 0 0 0; font-size: 14px; }
        
        .info-section { margin-bottom: 30px; display: table; w-full; }
        .info-box { display: table-cell; width: 50%; padding: 15px; background: #f9f9f9; border-radius: 8px; border: 1px solid #eee; }
        .info-box h3 { margin-top: 0; font-size: 14px; color: #555; text-transform: uppercase; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .info-box p { margin: 5px 0; font-size: 13px; }
        .info-box strong { color: #222; }

        .progress-section { margin-bottom: 30px; }
        .progress-section h2 { font-size: 18px; color: #673ab7; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 13px; }
        th { background-color: #673ab7; color: #fff; font-weight: normal; text-transform: uppercase; font-size: 12px; }
        
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-completed { background-color: #dcfce7; color: #166534; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-ongoing { background-color: #f3e8ff; color: #6b21a8; }
        
        .footer { text-align: center; margin-top: 50px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        
        .overall-progress { background: #f3e8ff; border: 1px solid #e9d5ff; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 30px; }
        .overall-progress h3 { margin: 0; color: #6b21a8; font-size: 16px; }
        .overall-progress .percent { font-size: 32px; font-weight: bold; color: #673ab7; margin: 10px 0 0 0; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Campaign Progress Report</h1>
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <div style="width: 100%; margin-bottom: 30px;">
        <table style="width: 100%; border: none; margin: 0;">
            <tr>
                <td style="width: 48%; padding: 0; border: none; vertical-align: top;">
                    <div class="info-box" style="width: 100%; box-sizing: border-box;">
                        <h3>Client Details</h3>
                        <p><strong>Name:</strong> {{ $booking->name ?? $booking->user?->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $booking->email ?? $booking->user?->email ?? 'N/A' }}</p>
                        <p><strong>Booking ID:</strong> BKG-{{ $booking->id }}</p>
                    </div>
                </td>
                <td style="width: 4%; padding: 0; border: none;"></td>
                <td style="width: 48%; padding: 0; border: none; vertical-align: top;">
                    <div class="info-box" style="width: 100%; box-sizing: border-box;">
                        <h3>Service Overview</h3>
                        <p><strong>Plan:</strong> {{ $booking->plan_name }}</p>
                        <p><strong>Service:</strong> {{ $booking->service?->title ?? 'Custom Service' }}</p>
                        <p><strong>Status:</strong> <span style="text-transform: capitalize;">{{ $booking->status }}</span></p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @php
        $totalTasks = $booking->tasks->count();
        $completedTasks = $booking->tasks->where('status', 'completed')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    @endphp

    <div class="overall-progress">
        <h3>Overall Campaign Completion</h3>
        <p class="percent">{{ $progress }}%</p>
    </div>

    <div class="progress-section">
        <h2>Task Execution Breakdown</h2>
        @if($totalTasks > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Task Description</th>
                        <th style="width: 25%;">Progress</th>
                        <th style="width: 25%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking->tasks as $task)
                        <tr>
                            <td>
                                <strong>{{ $task->title }}</strong>
                                @if($task->description)
                                    <br><span style="font-size: 11px; color: #666;">{{ $task->description }}</span>
                                @endif
                            </td>
                            <td>{{ $task->progress }}%</td>
                            <td>
                                @if($task->status == 'completed')
                                    <span class="status-badge status-completed">Completed</span>
                                @elseif($task->status == 'ongoing')
                                    <span class="status-badge status-ongoing">Ongoing</span>
                                @else
                                    <span class="status-badge status-pending">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #666; font-style: italic;">No specific tasks have been logged for this campaign yet.</p>
        @endif
    </div>

    <div class="footer">
        This is an automatically generated document by the system.<br>
        If you have any questions, please contact our support team.
    </div>

</body>
</html>
