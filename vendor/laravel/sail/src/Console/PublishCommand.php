<?php

namespace Laravel\Sail\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the Laravel Sail Docker files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $this->call('vendor:publish', ['--tag' => 'sail-docker']);

        file_put_contents(
            $this->laravel->basePath('docker-compose.yml'),
            str_replace(
                [
                    './vendor/laravel/sail/runtimes/8.1',
=======
        $this->call('vendor:publish', ['--tag' => 'sail']);
=======
        $this->call('vendor:publish', ['--tag' => 'sail-docker']);
>>>>>>> f330c64 (optimization in progress)

        file_put_contents(
            $this->laravel->basePath('docker-compose.yml'),
            str_replace(
                [
<<<<<<< HEAD
>>>>>>> 22c0e54 (table changes)
=======
                    './vendor/laravel/sail/runtimes/8.1',
>>>>>>> f330c64 (optimization in progress)
                    './vendor/laravel/sail/runtimes/8.0',
                    './vendor/laravel/sail/runtimes/7.4',
                ],
                [
<<<<<<< HEAD
<<<<<<< HEAD
                    './docker/8.1',
                    './docker/8.0',
                    './docker/7.4',
=======
                    './docker/8.0',
                    './docker/7.4', 
>>>>>>> 22c0e54 (table changes)
=======
                    './docker/8.1',
                    './docker/8.0',
                    './docker/7.4',
>>>>>>> f330c64 (optimization in progress)
                ],
                file_get_contents($this->laravel->basePath('docker-compose.yml'))
            )
        );
    }
}
