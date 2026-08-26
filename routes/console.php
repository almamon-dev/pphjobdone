<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('seo-audit:follow-up')->daily();
Schedule::command('crm:send-followups')->daily();
