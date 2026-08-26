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
        $this->info('Starting SEO Audit Automated Sequence (Day 2, Day 7, Day 14)...');

        // Step 1: Day 2 Follow up (after 2 days)
        $step1Audits = SeoAudit::where('follow_up_step', 0)
            ->whereNotNull('email')
            ->where('created_at', '<=', Carbon::now()->subDays(2))
            ->get();

        foreach ($step1Audits as $audit) {
            if ($this->isConverted($audit)) {
                $audit->follow_up_step = 3;
                $audit->last_follow_up_at = now();
                $audit->save();
                $this->info("Skipped follow-up for converted client {$audit->email}");
                continue;
            }

            try {
                Mail::to($audit->email)->send(new SeoAuditFollowUpMail($audit, 1));
                $audit->follow_up_step = 1;
                $audit->last_follow_up_at = now();
                $audit->save();
                $this->info("Day 2 Follow-up (Step 1) sent to {$audit->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send SEO Audit follow-up (step 1) to {$audit->email}: " . $e->getMessage());
            }
        }

        // Step 2: Day 7 Follow up (after 7 days)
        $step2Audits = SeoAudit::where('follow_up_step', 1)
            ->whereNotNull('email')
            ->where('created_at', '<=', Carbon::now()->subDays(7))
            ->get();

        foreach ($step2Audits as $audit) {
            if ($this->isConverted($audit)) {
                $audit->follow_up_step = 3;
                $audit->last_follow_up_at = now();
                $audit->save();
                $this->info("Skipped follow-up for converted client {$audit->email}");
                continue;
            }

            try {
                Mail::to($audit->email)->send(new SeoAuditFollowUpMail($audit, 2));
                $audit->follow_up_step = 2;
                $audit->last_follow_up_at = now();
                $audit->save();
                $this->info("Day 7 Follow-up (Step 2) sent to {$audit->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send SEO Audit follow-up (step 2) to {$audit->email}: " . $e->getMessage());
            }
        }

        // Step 3: Day 14 Final Follow up (after 14 days)
        $step3Audits = SeoAudit::where('follow_up_step', 2)
            ->whereNotNull('email')
            ->where('created_at', '<=', Carbon::now()->subDays(14))
            ->get();

        foreach ($step3Audits as $audit) {
            if ($this->isConverted($audit)) {
                $audit->follow_up_step = 3;
                $audit->last_follow_up_at = now();
                $audit->save();
                $this->info("Skipped follow-up for converted client {$audit->email}");
                continue;
            }

            try {
                Mail::to($audit->email)->send(new SeoAuditFollowUpMail($audit, 3));
                $audit->follow_up_step = 3;
                $audit->last_follow_up_at = now();
                $audit->save();
                $this->info("Day 14 Final Follow-up (Step 3) sent to {$audit->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send SEO Audit follow-up (step 3) to {$audit->email}: " . $e->getMessage());
            }
        }

        $this->info('SEO Audit Automated Follow-up sequence processing completed.');
        return 0;
    }

    /**
     * Check if the lead/user has already converted into an active customer
     */
    protected function isConverted(SeoAudit $audit): bool
    {
        $user = null;
        if ($audit->user_id) {
            $user = \App\Models\User::find($audit->user_id);
        } elseif ($audit->email) {
            $user = \App\Models\User::where('email', $audit->email)->first();
        }

        if (!$user) {
            return false;
        }

        return (bool) ($user->is_subscribed || \App\Models\Booking::where('user_id', $user->id)->exists());
    }
}
