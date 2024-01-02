<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AflSettings;
use App\Models\AflCallbacks;

class CleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crack-callback-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup Callbacks';

    /**
     * Execute the console command.
     */
    public function handle()
    {   
        $aflSettings = AflSettings::first();
        if ($aflSettings->DATABASE_CLEANUP_CALLBACKS !== null){
        $daysThreshold = now()->subDays($aflSettings->DATABASE_CLEANUP_CALLBACKS);
            AflCallbacks::where('callback_date_time', '<', $daysThreshold)
                ->delete();
        } 
    }
}

