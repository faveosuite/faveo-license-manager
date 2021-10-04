<?php

use App\Http\Controllers\AFL\ConnectionController;
use App\Http\Controllers\AflCallbacks\LicenseInstallController;
use App\Http\Controllers\AflCallbacks\LicenseSchemeController;
use App\Http\Controllers\AflCallbacks\LicenseVerifyController;
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD

=======
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
>>>>>>> 22c0e54 (table changes)
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


<<<<<<< HEAD
=======

>>>>>>> 22c0e54 (table changes)
Route::post('/apl_callbacks/connection_test.php',[ConnectionController::class,'connection']);
Route::post('/apl_callbacks/license_install.php',[LicenseInstallController::class,'licenseInstall']);
Route::post('/apl_callbacks/license_scheme.php',[LicenseSchemeController::class,'licenseScheme']);
Route::post('/apl_callbacks/license_verify.php',[LicenseVerifyController::class,'licenseVerify']);
<<<<<<< HEAD
=======



/*Route::get('/home', function (Illuminate\Http\Request $request) {
    $http = new \GuzzleHttp\Client;

    $response = $http->post('http://127.0.0.1:8000/oauth/token', [
        'form_params' => [
            'client_id' => '3',
            'client_secret' => 'al2kUNnATgwPYuxZTRYGQrOAQjeEjr5TUDOrTwgI',
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'http://127.0.0.1:8000/home',
            'code' => $request->code,
        ],
    ]);
    return json_decode((string) $response->getBody(), true);
});*/


>>>>>>> 22c0e54 (table changes)
