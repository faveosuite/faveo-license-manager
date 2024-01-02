<?php

namespace App\Console\Commands;

use App\Models\AflReports;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AflSettings;

class SystemReportsCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:system-reports-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System Report Cleanup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $aflSettings = AflSettings::first();
        if ($aflSettings->DATABASE_CLEANUP_REPORTS_SYSTEM !== null) {
            $daysThreshold = now()->subDays($aflSettings->DATABASE_CLEANUP_REPORTS_SYSTEMMAIN);           
            AflReports::
             where('report_system', 1)
            ->where('report_date_time', '<', now()->subDays($daysThreshold))
            ->delete();
        }
    }
}
