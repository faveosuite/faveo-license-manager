<?php

namespace App\Providers;

use App\Http\Controllers\Admin\LogWriteController;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('Log', function () {
            return new LogWriteController;
        });
    }

    public function provides()
    {
        return ['Log'];
    }
}
