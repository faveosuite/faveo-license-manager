<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallationLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:installation-logs-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
