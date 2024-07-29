<?php

use App\Http\Controllers\Admin\ApiKeysController;
use App\Http\Controllers\Admin\BannedHostController;
use App\Http\Controllers\Admin\Google2FAController;
use App\Http\Controllers\Admin\InstallationLogsController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VersionsController;
use App\Http\Controllers\Admin\Views\ClientsViewController;
use App\Http\Controllers\Admin\Views\InstallationViewController;
use App\Http\Controllers\Admin\Views\LicenseViewController;
use App\Http\Controllers\Admin\Views\ProductsViewController;
use App\Http\Controllers\Admin\Views\VersionsViewController;
use App\Http\Controllers\WhitelistIpsController;
use App\Http\Controllers\Admin\CallBackController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ConfigGenerateController;
use App\Http\Controllers\Admin\EmailsController;
use App\Http\Controllers\Admin\EmailSettingsController;
use App\Http\Controllers\Admin\InstallationController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\LogViewController;
use App\Http\Controllers\Admin\LogWriteController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\AFL\ConnectionController;
use App\Http\Controllers\AflCallbacks\LicenseInstallController;
use App\Http\Controllers\AflCallbacks\LicenseSchemeController;
use App\Http\Controllers\AflCallbacks\LicenseVerifyController;
use App\Http\Controllers\AfuCallbacks\DownloadFileController;
use App\Http\Controllers\AfuCallbacks\FetchQueryController;
use App\Http\Controllers\AfuCallbacks\GetAllVersionsController;
use App\Http\Controllers\AfuCallbacks\GetVersionsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CommonSettingController;
use App\Http\Controllers\EditProfilesController;
use App\Http\Controllers\Update\AfuProductsController;
use App\Http\Controllers\Update\AfuVersionsController;
use App\Http\Controllers\Update\DirectoryController;
use App\Http\Controllers\Update\UpdateInstallationsController;
use App\Http\Controllers\Update\UpdateNotificationsController;
use App\Http\Middleware\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
//CLOUD ROUTES
/*
 * These routes are secure any way because it's called internally from the cloud app
 * */

Route::middleware('whitelist')->group(function () {

Route::post('/LicenseReissue', [LicenseController::class, 'reissueLicenseCloud']);

//Only reserved for cloud routes//
//-------------------------------------------------------------------------------------------//



//AUTHENTICATION
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot', [AuthController::class, 'forgot']);
Route::post('/reset', [AuthController::class, 'reset']);
Route::post('/save-debug-value', [SettingsController::class, 'debuggerSettings'])->name('save-debug-value');
Route::post('/saveTokenForDebugger', [SettingsController::class, 'SaveTokenForDebugger']);
Route::get('recaptchaStatus',[AuthController::class,'getRecaptchaStatus']);
//2fa AUTHENTICATION
Route::post('verify-recovery-code', [AuthController::class, 'verifyRecoveryCode']);
Route::post('verify2fa',[AuthController::class, 'verify2fa']);

/*************************************** CALLBACK FROM FAVEO TO LICENSE AND UPDATE *******************************************/

//LICENSE MANAGER CALLBACKS
Route::post('/ConnectionTest', [ConnectionController::class, 'connection']);
Route::post('/licenseInstall', [LicenseInstallController::class, 'licenseInstall']);
Route::post('/licenseScheme', [LicenseSchemeController::class, 'licenseScheme']);
Route::post('/licenseVerify', [LicenseVerifyController::class, 'licenseVerify']);
Route::get('/licenseInfo', [LicenseController::class, 'licenseInfo']);
Route::get('/IndividuallicenseInfo', [LicenseController::class, 'individualLicenseInfo']);
Route::get('/getOrder', [LicenseController::class, 'giveLicenseTakeOrder']);
Route::get('/pluginLicense', [LicenseController::class, 'getPluginInfo']);

//UPDATE MANAGER CALLBACKS
Route::post('/getVersions', [GetVersionsController::class, 'getVersion']);
Route::post('/getAllVersions', [GetAllVersionsController::class, 'getAllVersions']);
Route::post('/fetchQuery', [FetchQueryController::class, 'fetchQuery']);
Route::post('/downloadFile', [DownloadFileController::class, 'downloadFile']);
Route::post('/pdf', [DirectoryController::class, 'pdfForm']);

/********************************************************* CALLBACK *******************************************************************/

//API CALLS FOR UI OF LICENSE AND UPDATE MANAGER AND BILLING

Route::prefix('admin')->namespace('Admin')->middleware('manager')->group(function () {

    Route::post('/logout/{user_id}', [AuthController::class, 'logout']);

    //Google 2fa
    Route::middleware( 'password.confirm')->group(function () {
        Route::post('2fa/enable', [Google2FAController::class, 'enableTwoFactor']);
        Route::post('2fa-recovery-code', [Google2FAController::class, 'generateRecoveryCode']);
        Route::post('2fa/setupValidate', [Google2FAController::class, 'postSetupValidateToken']);
        Route::post('2fa/disable', [Google2FAController::class, 'disableTwoFactor']);
        Route::get('show/verify-password', [Google2FAController::class, 'showVerifyPasswordPopup']);
        Route::get('2fa/downloadRecoveryCode', [Google2FAController::class, 'downloadRecoveryCodes']);
    });
    Route::post('verify/password', [Google2FAController::class, 'verifyPassword']);

    //Profile
    Route::get('profile/info', [UserController::class, 'getProfileInfo']);
    Route::patch('profile', [UserController::class, 'updateProfile']);
    Route::patch('password', [UserController::class, 'updatePassword']);
    Route::get('countryCode',[UserController::class, 'getCountryCode']);

    /******************************************* LICENSE MANAGER ******************************************************/
    //Dashboiard
    Route::get('dashboarddropdown', [DashboardController::class, 'dashboard']);
    //PRODUCTS

    Route::post('products/add', [ProductsController::class, 'productAdd']);
    Route::get('viewproducts', [ProductsController::class, 'show']);
    Route::post('products/delete', [ProductsController::class, 'deleteProduct']);
    Route::post('products/edit', [ProductsController::class, 'productUpdate']);
    Route::get('product/{product_id}', [ProductsController::class, 'edit']);
    Route::get('productView/{product_id}', [ProductsViewController::class, 'getProductDetails']);
    Route::get('productInstallations/{product_id}',[ProductsViewController::class,'getProductInstallations']);
    Route::get('productLicenses/{product_id}',[ProductsViewController::class,'getProductLicenses']);
    Route::get('productVersions/{product_id}',[ProductsViewController::class,'getProductVersions']);
    Route::post('addProduct', [ProductsController::class, 'addAflAndAfuProduct']);
    Route::post('updateProduct',[ProductsController::class,'updateAflAndAfuProduct']);
    Route::post('allProductDelete', [ProductsController::class, 'deleteAflAndAfuProduct']);
    Route::post('restoreProduct', [ProductsController::class, 'restoreSuspendedProduct']);
    Route::get('getProductIdbyKey', [ProductsController::class, 'getProductIdbyKey']);

    //ORDERS
    Route::post('deleteOrder',[OrderController::class,'deleteOrder']);

    //VERSIONS
    Route::get('viewVersions',[VersionsController::class,'show']);
    Route::get('versionView/{version_id}',[VersionsViewController::class,'getVersionInfo']);
    Route::get('versionCallbacks/{version_id}', [VersionsViewController::class, 'getVersionCallbacks']);

    //CLIENTS

    Route::post('clients/add', [ClientsController::class, 'clientAdd']);
    Route::get('viewClients/{client_id}', [ClientsController::class, 'show']);
    Route::post('clients/delete', [ClientsController::class, 'deleteClient']);
    Route::post('clients/edit', [ClientsController::class, 'clientUpdate']);
    Route::get('client/{client_id}', [ClientsController::class, 'edit']);
    Route::get('clientView/{client_id}', [ClientsViewController::class, 'getClientInfo']);
    Route::get('clientInstallations/{client_id}',[ClientsViewController::class,'getClientInstallations']);
    Route::get('clientLicenses/{client_id}',[ClientsViewController::class,'getClientLicenses']);

    //LICENSES
    Route::post('license/add', [LicenseController::class, 'licenseAdd']);
    Route::get('viewLicenses', [LicenseController::class, 'show']);
    Route::post('license/delete', [LicenseController::class, 'deleteLicense']);
    Route::post('license/edit', [LicenseController::class, 'licenseUpdate']);
    Route::get('license/{license_id}', [LicenseController::class, 'edit']);
    Route::post('license/deactivate', [LicenseController::class, 'licenseDeactivate']);
    Route::post('license/updateLicenseCode', [LicenseController::class, 'updateTheLicenseCode']);
    Route::get('licenseView/{license_id}', [LicenseViewController::class, 'getLicenseDetails']);
    Route::get('licenseInstallation/{license_id}', [LicenseViewController::class, 'getLicenseInstallations']);
    Route::get('licenseCallbacks/{license_id}',[LicenseViewController::class,'getLicenseCallBacks']);
    Route::get('getLicenseColumn',[LicenseController::class,'getLicenseColumns']);
    Route::post('saveLicenseColumn',[LicenseController::class,'saveLicenseColumns']);
    Route::get('installationLogs/{id}',[LicenseViewController::class,'getLicenseInstallationLogs']);
    Route::post('license/syncAddonLicense', [LicenseController::class, 'syncTheCreationOfLicense']);


    //INSTALLATIONS
    Route::post('installations/delete', [InstallationController::class, 'deleteInstallations']);
    Route::post('installations/edit', [InstallationController::class, 'installationUpdate']);
    Route::get('viewInstallations', [InstallationController::class, 'show']);
    Route::post('addInstallation', [InstallationController::class, 'installationAdd']);
    Route::get('installation/{installation_id}', [InstallationController::class, 'edit']);
    Route::post('installation/reissue', [InstallationController::class, 'removeUnwantedInstallations']);
    Route::post('installation/updateLicenseCode', [InstallationController::class, 'updateTheLicenseCode']);
    Route::get('installationView/{installation_id}', [InstallationViewController::class, 'getInstallation']);
    Route::get('installationCallbacks/{installation_id}',[InstallationViewController::class,'getInstallationCallBacks']);

    //BANNED HOSTS
    Route::post('bannedHosts/add', [BannedHostController::class, 'bannedHostAdd']);
    Route::post('bannedHosts/delete', [BannedHostController::class, 'deleteBannedHost']);
    Route::post('bannedHosts/edit', [BannedHostController::class, 'bannedHostUpdate']);
    Route::get('viewBannedHost', [BannedHostController::class, 'show']);
    Route::get('viewBannedHost/{banned_host_id}', [BannedHostController::class, 'view']);

    Route::post('whitelist/updateOrCreate', [WhitelistIpsController::class, 'whitelistAdd']);
    Route::get('whitelist-edit/{id}', [WhitelistIpsController::class, 'edit']);
    Route::post('delete-whitelist-ip', [WhitelistIpsController::class, 'deleteWhitelistIp']);
    Route::get('view-Whitelist', [WhitelistIpsController::class, 'view']);

    //SETTINGS
    Route::post('generalsettings/{SETTING_ID}', [SettingsController::class, 'generalSettingsCreate']);
    Route::post('advancedsettings/{SETTING_ID}', [SettingsController::class, 'advancedSettings']);
    Route::post('securitysettings/{SETTING_ID}', [SettingsController::class, 'securitySettings']);
    Route::post('emailsettings/{SETTING_ID}', [SettingsController::class, 'emailSettings']);
    Route::post('cleanupsettings', [SettingsController::class, 'cleanUpSettings']);
    Route::get('viewSettings', [SettingsController::class, 'show']);
    Route::get('securityDropdown', [SettingsController::class, 'dropDownForSecuritySettings']);
    Route::get('generalDropdown', [SettingsController::class, 'dropDownForGeneralSettings']);
    Route::get('emailDropdown', [SettingsController::class, 'dropDownForEmailSettings']);
    Route::post('emailSettings', [EmailSettingsController::class, 'postSettingsEmail']);

    Route::post('common-setting', [CommonSettingController::class, 'createOrUpdateCommonSetting']);
    Route::get('common-setting/get', [CommonSettingController::class, 'getCommonSetting']);
    Route::post('common-setting/reset', [CommonSettingController::class, 'clearCommonSetting']);
    Route::get('timezones', [CommonSettingController::class, 'getDropDownForTimezone']);
    Route::get('date-formats', [CommonSettingController::class, 'getDropDownForDateFormat']);
    Route::get('time-formats', [CommonSettingController::class, 'getDropDownForTimeFormat']);


    Route::get('cleanupSettings', [SettingsController::class, 'dropDownForCleanUpSettings']);
    Route::get('cleanSettings', [SettingsController::class, 'dropForCleanUpSettings']);
    Route::post('saveintervalSettings', [SettingsController::class, 'saveintervalSettings']);
    Route::post('saveLicenseExpireRange', [SettingsController::class, 'saveUpdateExpireRange']);
    Route::get('getUpdatesExpirings', [SettingsController::class, 'getUpdatesExpirings']);
    Route::post('saveSupportExpireRange', [SettingsController::class, 'saveSupportExpireRange']);
    Route::get('getSupportExpirings', [SettingsController::class, 'getSupportExpirings']);
    Route::get('getDebugger', [SettingsController::class, 'getDebugger']);
    Route::get('cronTimeCommands', [SettingsController::class, 'cronTimeCommands']);
    Route::get('intervalTime', [SettingsController::class, 'intervalTime']);
    Route::post('verify-php-path', [SettingsController::class, 'checkPHPExecutablePath'])->name('verify-cron');

    //NOTIFICATIONS
    Route::post('notifications/{notification_id}', [NotificationsController::class, 'notifications']);
    Route::post('emails', [EmailsController::class, 'emails']);
    Route::get('viewNotifications', [NotificationsController::class, 'show']);
    Route::get('viewEmails', [EmailsController::class, 'show']);

    //EDIT PROFILE
    Route::post('editprofile/{admin_id}', [EditProfilesController::class, 'editProfile']);

    //CONFIGURATION GENERATOR
    Route::post('config', [ConfigGenerateController::class, 'configGenerate']);

    //EXCEPTION LOGS
    Route::get('logs/exception',[LogViewController::class,'getExceptionLogs']);

    //SEARCH
    Route::post('search', [SearchController::class, 'search']);

    //API KEYS
    Route::post('addnewapi', [ApiKeysController::class, 'apiKeyAdd']);
    Route::post('editnewapi/{api_key_id}', [ApiKeysController::class, 'apiKeyUpdate']);
    Route::post('deleteapi/{api_key_id}', [ApiKeysController::class, 'apiKeyDelete']);
    Route::get('viewApiKeys/{api_key_id}', [ApiKeysController::class, 'view']);

    //REPORTS FOR LICENSE AND UPDATE
    Route::post('reports/delete', [ReportsController::class, 'reports']);
    Route::get('reportSystem', [ReportsController::class, 'reportArraySystem']);
    Route::get('reportLicense', [ReportsController::class, 'reportArrayLicense']);
    Route::get('reportCracking', [ReportsController::class, 'reportArrayCracking']);
    Route::get('reportUpdate', [ReportsController::class, 'reportArrayUpdate']);

    /**************************************************** UPDATE MANAGER ************************************************************/

    //PRODUCTS
    Route::post('products/UpdateAdd', [AfuProductsController::class, 'productUpdateAdd']);
    Route::post('products/UpdateDelete', [AfuProductsController::class, 'deleteUpdateProduct']);
    Route::post('products/UpdateEdit', [AfuProductsController::class, 'productUpdateUpdate']);
    Route::get('afuProducts',[AfuProductsController::class,'getProducts']);

    //VERSIONS
    Route::post('versions/add', [AfuVersionsController::class, 'versionAdd']);
    Route::post('versions/edit', [AfuVersionsController::class, 'versionUpdate']);
    Route::post('versions/delete', [AfuVersionsController::class, 'deleteVersion']);

    //TO SET PATH FOR ARCHIVES_DIRECTORY AND QUERSIES_DIRECTORY

    Route::post('/setPath', [DirectoryController::class, 'setDirectory']);
    Route::get('/getDirectoryPath', [DirectoryController::class, 'getDirectory']);

    //NOTIFICATION HEADER RESPONSES FOR CALLBACKS APIs UPDATE MANAGER
    Route::post('/updateNotifications/{notification_id}', [UpdateNotificationsController::class, 'updateNotificationFields']);
    Route::get('showUpdateNotifications', [UpdateNotificationsController::class, 'show']);

    //CALLBACKS FOR UPDATE MANAGER
    Route::get('showLicenseCallbacks', [CallBackController::class, 'licneseCallbacks']);
    Route::get('showUpdateCallbacks', [CallBackController::class, 'updateCallbacks']);
    Route::post('callbackdelete', [CallBackController::class, 'callbacksDelete']);

    //UPDATE INSTALLATION AFTER UPDATING THE VERSION
    Route::post('updatedInstallation/edit', [UpdateInstallationsController::class, 'updateInstallationEdit']);
    Route::get('showUpdateInstall', [UpdateInstallationsController::class, 'show']);

    Route::post('updateInstallationLogs',[InstallationLogsController::class,'updateInstallationLogs']);
    Route::post('getInstallationLogs',[InstallationLogsController::class,'getInstallationLogs']);

    Route::get('profile/info', [UserController::class, 'getProfileInfo']);
    Route::patch('profile', [UserController::class, 'updateProfile']);
    Route::patch('password', [UserController::class, 'updatePassword']);
    Route::get('countryCode',[UserController::class, 'getCountryCode']);
});

Route::get('admin/viewApiKeys',[ApiKeysController::class, 'show'])->middleware('manager');
});

