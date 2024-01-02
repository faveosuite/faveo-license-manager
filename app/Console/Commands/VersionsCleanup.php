<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AflSettings;
use App\Models\AfuVersions;

class VersionsCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:versions-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Version Cleanup';

    /**
     * Execute the console command.
     */
    public function handle()
    {  
        $aflSettings = AflSettings::first();
        if ($aflSettings->DATABASE_CLEANUP_VERSIONS !== null){
            $daysThreshold = now()->subDays($aflSettings->DATABASE_CLEANUP_VERSIONS);
            AfuVersions::where('version_date', '<', $daysThreshold )
            ->delete();      
         }
    }
}
