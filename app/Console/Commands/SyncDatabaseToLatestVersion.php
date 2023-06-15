<?php

namespace App\Console\Commands;

use App\Http\Controllers\SyncLicenseToLatestVersion;
use Illuminate\Console\Command;

class SyncDatabaseToLatestVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo (new SyncLicenseToLatestVersion)->sync();
    }
}
