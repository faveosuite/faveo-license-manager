<?php

namespace App\Console;
use App\Console\Commands\SetupTestEnv;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

      protected $commands = [
        SetupTestEnv::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            DB::table('oauth_access_tokens')
                ->orWhere('revoked', 1)
                ->orWhere('expires_at', '<', date('Y-m-d'))->delete();
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


}
