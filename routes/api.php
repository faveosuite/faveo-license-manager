<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\AFL\ConnectionController;
use App\Http\Controllers\AflCoreController\AflInstallLicenseController;
use App\Http\Middleware\Manager;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/forgot',[AuthController::class,'forgot']);
Route::post('/reset',[AuthController::class,'reset']);



Route::get('/ConnectionTest',[ConnectionController::class,'connection']);
Route::get('/InstallLicenseManager',[AflInstallLicenseController::class,'aflInstallLicense']);




Route::group(array('prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'manager'), function ()
{

Route::post('/logout/{user_id}',[AuthController::class,'logout']);

Route::post('/Addnewprod',[ProductsController::class,'store']);
Route::get('/Viewproducts',[ProductsController::class,'show']);
Route::post('Viewnewproducts/{product_title}/delete',[ProductsController::class,'destroy']);
Route::post('/Viewnewproducts/{product_id}/edit',[ProductsController::class,'update']);

Route::post('/Addnewclient',[ClientsController::class,'store']);
Route::get('/ViewClients',[ClientsController::class,'show']);
Route::post('ViewClients/{client_id}/delete',[ClientssController::class,'destroy']);
Route::post('/ViewClients/{client_id/edit',[ClientsController::class,'update']);


});

/*Route::middleware('auth:api')->group(function (){



});*/




Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
