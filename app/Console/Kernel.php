<?php

namespace App\Console;

use App\Console\Commands\CleanupCommand;
use App\Console\Commands\CrackReportsCleanup;
use App\Console\Commands\InstallationLogsCommand;
use App\Console\Commands\licenseReportsCleanup;
use App\Console\Commands\SystemReportsCleanup;
use App\Console\Commands\VersionsCleanup;
use App\Console\Commands\SetupTestEnv;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use App\Models\AflSettings;
use App\Models\ScheduleCron;
use Exception;
use \Illuminate\Support\Facades\App;
use PhpParser\Node\Stmt\Catch_;

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
        CleanupCommand::class,
        CrackReportsCleanup::class,
        licenseReportsCleanup::class,
        SystemReportsCleanup::class,
        VersionsCleanup::class,
        InstallationLogsCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            DB::table('oauth_access_tokens')
                ->orWhere('revoked', 1)
                ->orWhere('expires_at', '<', date('Y-m-d'))->delete();
        })->daily();
        try{
            if (isInstall() && App::runningInConsole()) {
                $cron = new ScheduleCron();

                $tasks = $cron->getAllActiveCron();


                foreach ($tasks as $data) {
                    foreach ($data as $value => $command) {
                        $executionTime = $cron->getConditionValue($value);
                        $this->getCondition($schedule->command($command), $executionTime);
                    }
                }

            }
        }
        catch(Exception $e){
            return errorResponse($e, 404);
        }

    }

    public function getCondition($schedule, $condition)
    {
        return match ($condition['condition']) {
            'everyFiveMinutes' => $schedule->everyFiveMinutes(),
            'everyTenMinutes' => $schedule->everyTenMinutes(),
            'everyThirtyMinutes' => $schedule->everyThirtyMinutes(),
            'hourly' => $schedule->hourly(),
            'everySixHours' => $schedule->everySixHours(),
            'daily' => $schedule->daily()->at('12:00'),
            'dailyAt' => $this->getConditionWithMultipleOptions($schedule, $condition),
            'weekdays' => $schedule->weekdays()->at('12:00'),
            'weekends' => $schedule->weekends()->at('12:00'),
            'weeklyOn' => $this->getConditionWithMultipleOptions($schedule, $condition),
            'monthly' => $schedule->monthly()->at('12:00'),
            'quarterly' => $schedule->quarterly()->at('12:00'),
            'yearly' => $schedule->yearly()->at('12:00'),
            default => $schedule->everyMinute(),
        };
    }
    private function getConditionWithMultipleOptions($schedule, $condition): void
    {
        match ($condition['condition']) {
            'dailyAt' => $schedule->dailyAt($condition['time']),
            'weeklyOn' => $schedule->weeklyOn($this->returnIntegerValueOfDay($condition['day']), $condition['time']),
            default => null,
        };
    }
    private function returnIntegerValueOfDay($scanDay): int
    {
        return match ($scanDay) {
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            default => 1,
        };
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
