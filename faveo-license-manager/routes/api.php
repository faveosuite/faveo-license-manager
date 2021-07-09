<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\AFL\ConnectionController;
use App\Http\Controllers\AflCoreFunctions\AflInstallLicenseController;
use App\Http\Controllers\AflCoreFunctions\AflVerifyLicenseController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\InstallationController;
use App\Http\Controllers\Admin\BannedHostController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\EmailsController;
use App\Http\Controllers\Admin\ApiKeysController;
use App\Http\Controllers\Admin\ApiController;
use App\Http\Controllers\Admin\ConfigGenerateController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\EditProfilesController;
use App\Http\Controllers\AflCallbacks\LicenseInstallController;
use App\Http\Controllers\AflCallbacks\LicenseSchemeController;
use App\Http\Controllers\AflCallbacks\LicenseVerifyController;

use App\Http\Controllers\TestController;

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


Route::post('/ConnectionTest',[ConnectionController::class,'connection']);
//Route::post('/InstallLicenseManager',[AflInstallLicenseController::class,'aflInstallLicense']);
//Route::post('/VerifyLicense/{FORCE_VERIFICATION}', [AflVerifyLicenseController::class,'aflVerifyLicense']);
Route::post('/licenseinstall',[LicenseInstallController::class,'licenseInstall']);
Route::post('/licenseverify',[LicenseVerifyController::class,'licenseVerify']);
Route::post('/licensescheme',[LicenseSchemeController::class,'licenseScheme']);
Route::post('/license/{product_name}/{product_sku}',[TestController::class,'addNewProduct']);


Route::post('API',[ApiController::class,'api']);

Route::group(array('prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'manager'), function ()
{

Route::post('/logout/{user_id}',[AuthController::class,'logout']);

//PRODUCTS
Route::post('products/add',[ProductsController::class,'productAdd']);
Route::get('viewproducts',[ProductsController::class,'show']);
Route::Delete('products/delete',[ProductsController::class,'deleteProduct']);
Route::post('products/edit',[ProductsController::class,'productUpdate']);
   
//CLIENTS
Route::post('clients/add',[ClientsController::class,'clientAdd']);
Route::get('viewClients',[ClientsController::class,'show']);
Route::Delete('clients/delete',[ClientsController::class,'deleteClient']);
Route::post('clients/edit',[ClientsController::class,'clientUpdate']);


//LICENSES
Route::post('license/add',[LicenseController::class,'licenseAdd']);
Route::get('viewLicenses',[LicenseController::class,'show']);
Route::Delete('license/delete',[LicenseController::class,'deleteLicense']);
Route::post('license/edit',[LicenseController::class,'licenseUpdate']);


//INSTALLATIONS
Route::Delete('installations/delete',[InstallationController::class,'deleteInstallation']);
Route::post('installations/edit',[InstallationController::class,'installationUpdate']);


//BANNED HOSTS
Route::post('bannedHosts/add',[BannedHostController::class,'bannedHostAdd']);
Route::Delete('bannedHosts/delete',[BannedHostController::class,'deleteBannedHost']);
Route::post('bannedHosts/update',[BannedHostController::class,'bannedHostUpdate']);


//SETTINGS
Route::post('generalsettings/{SETTING_ID}',[SettingsController::class,'generalSettingsCreate']);
Route::post('advancedsettings/{SETTING_ID}',[SettingsController::class,'advancedSettings']);
Route::post('securitysettings/{SETTING_ID}',[SettingsController::class,'securitySettings']);
Route::post('emailsettings/{SETTING_ID}',   [SettingsController::class,'emailSettings']);
Route::post('cleanupsettings/{SETTING_ID}',[SettingsController::class,'cleanUpSettings']);


//NOTIFICATIONS
Route::post('notifications/{notification_id}',[NotificationsController::class,'notifications']);
Route::post('emails/{email_id}',[EmailsController::class,'emails']);


//EDIT PROFILE
Route::post('editprofile/{admin_id}',[EditProfilesController::class,'editProfile']);


//CONFIGURATION GENERATOR
Route::post('config',[ConfigGenerateController::class,'configGenerate']);

//SEARCH
Route::get('search',[SearchController::class,'search']);


//API KEYS
Route::post('addnewapi',[ApiKeysController::class,'apiKeyAdd']);
Route::post('editnewapi/{api_key_id}',[ApiKeysController::class,'apiKeyUpdate']);
Route::Delete('deleteapi/{api_key_id}',[ApiKeysController::class,'apiKeyDelete']);

});

/*Route::middleware('auth:api')->group(function (){


});*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
