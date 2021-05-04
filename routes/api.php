<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ClientsController;



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
Route::post('/Addnewprod',[ProductsController::class,'store']);
Route::get('/Viewproducts',[ProductsController::class,'show']);
Route::post('/Addnewclient',[ClientsController::class,'store']);
Route::post('Viewnewproducts/{product_title}/delete',[ProductsController::class,'destroy']);

Route::group(array('prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'manag
er'), function () {
Route::post('/logout',[AuthController::class,'logout']);
});

//Route::middleware('auth:api')->group(function (){



//  });




Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



