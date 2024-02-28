<?php

namespace App\Console\Commands;

use App\Models\AflReports;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AflSettings;

class CrackReportsCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crack-reports-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crack Reports Cleanup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $aflSettings = AflSettings::value('DATABASE_CLEANUP_REPORTS_MAIN');
        $aflSettings ?  AflReports::where('product_id', 0)->where('report_system', 0)->where('report_date_time', '<', now()->subDays($aflSettings->DATABASE_CLEANUP_REPORTS_MAIN))->delete() : null;
        
    }
}
