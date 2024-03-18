<?php

namespace App\Console\Commands;

use App\Models\AflReports;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AflSettings;

class licenseReportsCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:license-reports-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'License Reports Callback';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the value of DATABASE_CLEANUP_REPORTS_LICENSES from afl_settings table
        $aflSettings = AflSettings::value('DATABASE_CLEANUP_REPORTS_LICENSES');

        // If $aflSettings has a value, proceed with the cleanup
        if ($aflSettings !== null) {
            // Calculate the date threshold based on the $aflSettings value
            $thresholdDate = now()->subDays($aflSettings);

            // Perform cleanup query to delete records older than the threshold date
            AflReports::whereNotNull('license_code')
                ->where('license_code', '!=', '')
                ->where('report_date_time', '<', $thresholdDate)
                ->delete();
        }
    }

}
