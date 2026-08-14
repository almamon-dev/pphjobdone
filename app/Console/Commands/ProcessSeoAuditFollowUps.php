<?php

namespace App\Console\Commands;

use App\Mail\SeoAuditFollowUpMail;
use App\Models\SeoAudit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessSeoAuditFollowUps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo-audit:follow-up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automated follow-ups for SEO Audits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting SEO Audit Follow-ups...');

        // Step 1: Follow up after 2 days
        $step1Audits = SeoAudit::where('follow_up_step', 0)
            ->whereNotNull('email')
            ->where('created_at', '<=', Carbon::now()->subDays(2))
            ->get();

        foreach ($step1Audits as $audit) {
            try {
                Mail::to($audit->email)->send(new SeoAuditFollowUpMail($audit, 1));
                $audit->follow_up_step = 1;
                $audit->last_follow_up_at = now();
                $audit->save();
                $this->info("Step 1 sent to {$audit->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send SEO Audit follow-up (step 1) to {$audit->email}: " . $e->getMessage());
            }
        }

        // Step 2: Follow up after 7 days
        $step2Audits = SeoAudit::where('follow_up_step', 1)
            ->whereNotNull('email')
            ->where('created_at', '<=', Carbon::now()->subDays(7))
            ->get();

        foreach ($step2Audits as $audit) {
            try {
                Mail::to($audit->email)->send(new SeoAuditFollowUpMail($audit, 2));
                $audit->follow_up_step = 2;
                $audit->last_follow_up_at = now();
                $audit->save();
                $this->info("Step 2 sent to {$audit->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send SEO Audit follow-up (step 2) to {$audit->email}: " . $e->getMessage());
            }
        }

        $this->info('SEO Audit Follow-ups completed.');
    }
}
