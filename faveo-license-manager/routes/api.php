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
use App\Http\Controllers\AflCoreController\AflVerifyLicenseController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\InstallationController;
use App\Http\Controllers\Admin\BannedHostController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\EmailsController;
use App\Http\Controllers\Admin\ApiController;
use App\Http\Controllers\Admin\ConfigGenerateController;
use App\Http\Controllers\EditProfilesController;
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


Route::post('/ConnectionTest/{product_id}/{connection_hash}',[ConnectionController::class,'connection']);
Route::get('/InstallLicenseManager',[AflInstallLicenseController::class,'aflInstallLicense']);
Route::get('/VerifyLicense', [AflVerifyLicenseController::class,'aflVerifyLicense']);


Route::group(array('prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'manager'), function ()
{
Route::post('/logout/{user_id}',[AuthController::class,'logout']);

Route::post('/Addnewprod/{api_key_secret}/{product_title}/{product_sku}/{product_status}/{product_description?}/{product_url_homepage?}/{product_url_download?}/{product_version?}/{product_envato_id?}',[ProductsController::class,'productAdd']);
Route::get('/Viewproducts',[ProductsController::class,'show']);
Route::Delete('Deleteproducts/{product_id}',[ProductsController::class,'deleteProduct']);
Route::post('/Editproducts/{api_key_secret}/{product_id}/{product_title}/{product_sku}/{product_status}/{product_description?}/{product_url_homepage?}/{product_url_download?}/{product_version?}/{product_envato_id?}',[ProductsController::class,'productUpdate']);


Route::post('/Addnewclient/{api_key_secret}/{client_fname}/{client_lname}/{client_email}/{client_status}/',[ClientsController::class,'clientAdd']);
Route::get('/ViewClients',[ClientsController::class,'show']);
Route::Delete('DeleteClients/{client_id}',[ClientsController::class,'deleteClient']);
Route::post('/EditClients/{api_key_secret}/{client_id}/{client_fname}/{client_lname}/{client_email}/{client_status}/',[ClientsController::class,'clientUpdate']);


Route::post('/Addnewlicense/{api_key_secret}/{product_id}/{license_require_domain}/{license_status}/{client_id?}/{license_code?}/{license_order_number?}/{license_ip?}/{license_domain?}/{license_limit?}/{license_expire_date?}{license_updates_date?}/{license_support_date?}/{license_comments?}',[LicenseController::class,'licenseAdd']);
//Route::post('/Addnewlicense/{api_key_secret}/{product_id}/{client_id}{license_require_domain}/{license_status}/{license_order_number?}/{license_ip?}/{license_domain?}/{license_limit?}/{license_expire_date?}{license_updates_date?}/{license_support_date?}/{license_comments?}',[LicenseController::class,'licenseAdd']);
Route::get('/ViewLicenses',[LicenseController::class,'show']);
Route::Delete('deletelicense/{license_id}',[LicenseController::class,'deleteLicense']);
Route::post('/Editlicense/{api_key_secret}/{license_id}/{product_id}/{license_require_domain}/{license_status}/{client_id?}/{license_code?}{license_order_number?}/{license_ip?}/{license_domain?}/{license_limit?}/{license_expire_date?}{license_updates_date?}/{license_support_date?}/{license_comments?}',[LicenseController::class,'licenseUpdate']);


Route::Delete('deleteInstallations/{installation_id}',[InstallationController::class,'deleteInstallation']);
Route::post('/editInstallation/{api_key_secret}/{installation_id}/{installation_ip}/{installation_status}/{installation_disable_ip?}',[InstallationController::class,'installationUpdate']);


Route::post('/Addnewbannedhost/{api_key_secret}/{banned_host_ip}/{banned_host_comments?}',[BannedHostController::class,'bannedHostAdd']);
Route::Delete('DeleteBannedHosts/{banned_host_id}/delete',[BannedHostController::class,'deleteBannedHost']);
Route::post('/UpdateBannedHosts/{api_key_secret}/{banned_host_id}/{banned_host_ip}/{banned_host_comments?}',[BannedHostController::class,'bannedHostUpdate']);


Route::post('/generalsettings/{SETTING_ID}',[SettingsController::class,'generalSettingsCreate']);
Route::post('advancedsettings/{SETTING_ID}',[SettingsController::class,'advancedSettings']);
Route::post('securitysettings/{SETTING_ID}',[SettingsController::class,'securitySettings']);
Route::post('emailsettings/{SETTING_ID}',   [SettingsController::class,'emailSettings']);
Route::post('cleanupsettings/{SETTING_ID}',[SettingsController::class,'cleanUpSettings']);

Route::post('/notifications/{notification_id}',[NotificationsController::class,'notifications']);

Route::post('/emails/{email_id}',[EmailsController::class,'emails']);

Route::post('/editprofile/{admin_id}',[EditProfilesController::class,'editProfile']);

Route::post('/config',[ConfigGenerateController::class,'configGenerate']);

Route::post('/Addnewapi',[ApiController::class,'apiKeyAdd']);
Route::post('/Editnewapi/{api_key_id}',[ApiController::class,'apiKeyUpdate']);
Route::Delete('/deleteapi/{api_key_id}',[ApiController::class,'apiKeyDelete']);

});

/*Route::middleware('auth:api')->group(function (){


});*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
