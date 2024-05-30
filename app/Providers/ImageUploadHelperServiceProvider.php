<?php

namespace App\Providers;

use App\Helper\ImageUploadHelper;
use Illuminate\Support\ServiceProvider;

class ImageUploadHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('ImageUpload-helper', function () {
            return new ImageUploadHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
