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
        $aflSettings = AflSettings::first();
        if ($aflSettings->DATABASE_CLEANUP_REPORTS_LICENSES !== null) {
            $daysThreshold = now()->subDays($aflSettings->DATABASE_CLEANUP_REPORTS_LICENSES);
            AflReports::where(function ($query) use ($aflSettings, $daysThreshold) {
                $query->where('license_code' ,'!=', 'null')
                    ->where('license_code', '!=', '')
                    ->where('report_date_time', '<', $daysThreshold);
            })->delete();
        }     
    }
}
