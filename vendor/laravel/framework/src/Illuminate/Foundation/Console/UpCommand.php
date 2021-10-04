<?php

namespace Illuminate\Foundation\Console;

use Exception;
use Illuminate\Console\Command;
<<<<<<< HEAD
<<<<<<< HEAD
use Illuminate\Foundation\Events\MaintenanceModeDisabled;
=======
>>>>>>> 22c0e54 (table changes)
=======
use Illuminate\Foundation\Events\MaintenanceModeDisabled;
>>>>>>> f330c64 (optimization in progress)

class UpCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bring the application out of maintenance mode';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            if (! is_file(storage_path('framework/down'))) {
                $this->comment('Application is already up.');

                return 0;
            }

            unlink(storage_path('framework/down'));

            if (is_file(storage_path('framework/maintenance.php'))) {
                unlink(storage_path('framework/maintenance.php'));
            }

<<<<<<< HEAD
<<<<<<< HEAD
            $this->laravel->get('events')->dispatch(MaintenanceModeDisabled::class);

=======
>>>>>>> 22c0e54 (table changes)
=======
            $this->laravel->get('events')->dispatch(MaintenanceModeDisabled::class);

>>>>>>> f330c64 (optimization in progress)
            $this->info('Application is now live.');
        } catch (Exception $e) {
            $this->error('Failed to disable maintenance mode.');

            $this->error($e->getMessage());

            return 1;
        }
    }
}
