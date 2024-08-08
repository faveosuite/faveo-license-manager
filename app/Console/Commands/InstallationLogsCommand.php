<?php

namespace App\Console\Commands;

use App\Models\InstallationLogs;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InstallationLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installation:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logs every minute for installation status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ExpireDate = Carbon::now()->subDays(5)->toDateString();
        InstallationLogs::where('installation_last_active_date', '<', $ExpireDate)
            ->update(['installation_status' => 0]);
    }
}
