<?php

use App\Http\Controllers\Installer\InstallerController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'installer'], function () {
    Route::post('db-setup', [InstallerController::class, 'configuration'])->name('db-setup');

    Route::post('config', [InstallerController::class, 'configurationcheck',
    ])->name('config');

    Route::get('config-check', [InstallerController::class, 'database',
    ])->name('config-check');

    Route::post('create/env', [InstallerController::class, 'createEnv',
    ])->name('create.env');

    Route::post('preinstall/check', [InstallerController::class, 'checkPreInstall',
    ])->name('preinstall.check');

    Route::post('migrate', [InstallerController::class, 'migrate',
    ])->name('migrate');
});
