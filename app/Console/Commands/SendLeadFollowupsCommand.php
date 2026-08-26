<?php

namespace App\Console\Commands;

use App\Mail\LeadFollowupMail;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLeadFollowupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:send-followups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically send follow-up emails to new or contacted leads and SEO audit submitters';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for leads eligible for automated CRM follow-up...');

        // Fetch leads that are "new" or "contacted" and created at least 1 day ago
        $leads = Lead::whereIn('status', ['new', 'contacted'])
            ->where('created_at', '<=', now()->subDays(1))
            ->limit(20)
            ->get();

        $count = 0;

        foreach ($leads as $lead) {
            if (!$lead->email) {
                continue;
            }

            try {
                Mail::to($lead->email)->send(new LeadFollowupMail($lead));
                
                // Update lead status to 'contacted'
                $lead->update([
                    'status' => 'contacted',
                    'qualification_summary' => ($lead->qualification_summary ?? '') . ' | Automated follow-up sent on ' . now()->toFormattedDateString(),
                ]);

                $count++;
                $this->info("Follow-up email dispatched to: {$lead->email}");
            } catch (\Exception $e) {
                Log::error("CRM Follow-up Error for {$lead->email}: " . $e->getMessage());
                $this->error("Failed to send to {$lead->email}: {$e->getMessage()}");
            }
        }

        $this->info("Automated CRM follow-up execution completed. Sent {$count} follow-ups.");
        return 0;
    }
}
