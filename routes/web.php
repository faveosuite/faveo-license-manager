<?php

use App\Http\Controllers\AFL\ConnectionController;
use App\Http\Controllers\AflCallbacks\LicenseInstallController;
use App\Http\Controllers\AflCallbacks\LicenseSchemeController;
use App\Http\Controllers\AflCallbacks\LicenseVerifyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::post('/apl_callbacks/connection_test.php',[ConnectionController::class,'connection']);
Route::post('/apl_callbacks/license_install.php',[LicenseInstallController::class,'licenseInstall']);
Route::post('/apl_callbacks/license_scheme.php',[LicenseSchemeController::class,'licenseScheme']);
Route::post('/apl_callbacks/license_verify.php',[LicenseVerifyController::class,'licenseVerify']);
