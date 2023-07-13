<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\AdvancedSettingRequest;
use App\Http\Requests\Settings\CleanUpSettingRequest;
use App\Http\Requests\Settings\EmailSettingRequest;
use App\Http\Requests\Settings\GeneralSettingsRequest;
use App\Http\Requests\Settings\SecuritySettingRequest;
use App\Models\AflSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Dotenv\Dotenv;
use Illuminate\Support\Facades\Cache;
use Carbon;
use App\Models\ExpireSupportDisplay;
use App\Models\ExpireUpdatesDisplay;
use App\Models\AflLicenses;




/**
 * Consist of functionalities for the Settings page in Auto Faveo licenser
 * Class  SettingsController
 */
class SettingsController extends Controller
{
    /**
     * To Add or Update the general settings of license manager
     *
     * @param  GeneralSettingsRequest  $request
     * @param $SETTING_ID
     * @return $gen with a success response leaving the other fields to be null if not filled
     */
    public function generalSettingsCreate(GeneralSettingsRequest $request, $SETTING_ID)
    {
        $genset = AflSettings::find($SETTING_ID);
        if (empty($genset)) {
            $gen = new AflSettings([
                'SMART_REPORTS' => $request->get('SMART_REPORTS'),
                'SMART_TABLES' => $request->get('SMART_TABLES'),
                'RECORDE_ON_ADMIN_PAGE' => $request->get('RECORDS_ON_ADMIN_PAGE'),
                'RECORDE_ON_INDEX_PAGE' => $request->get('RECORDS_ON_INDEX_PAGE'),
                'RECORDE_ON_SEARCH_PAGE' => $request->get('RECORDS_ON_SEARCH_PAGE'),
                'RECORDE_ARCHIVE_DAYS' => $request->get('RECORDS_ARCHIVE_DAYS'),
                'TIMEZONE' => $request->get('TIMEZONE'),

            ]);
            $gen->save();

            return successResponse(Lang::get('lang.settings_created'), $gen, 201);
        } else {
            $genset->SMART_REPORTS = $request->get('SMART_REPORTS');
            $genset->SMART_TABLES = $request->get('SMART_TABLES');
            $genset->RECORDS_ON_ADMIN_PAGE = $request->get('RECORDE_ON_ADMIN_PAGE');
            $genset->RECORDS_ON_INDEX_PAGE = $request->get('RECORDE_ON_INDEX_PAGE');
            $genset->RECORDS_ON_SEARCH_PAGE = $request->get('RECORDE_ON_SEARCH_PAGE');
            $genset->RECORDS_ARCHIVE_DAYS = $request->get('RECORDE_ARCHIVE_DAYS');
            $genset->TIMEZONE = $request->get('TIMEZONE');
            $genset->save();

            return successResponse(Lang::get('lang.settings_updated'), $genset, 200);
        }
    }

    /**
     * To Add or Update the advanced settings of license manager
     *
     * @param  Request  $request
     * @param $SETTING_ID
     * @return $advset with a success response leaving the other fields to be null if not filled
     */
    public function advancedSettings(AdvancedSettingRequest $request, $SETTING_ID = null)
    {
        $advset = AflSettings::find($SETTING_ID);
        if (empty($advset)) {
            $adv = new AflSettings([
                'API_STATUS' => $request->get('API_STATUS'),
                'VERIFIED_UPDATES' => $request->get('VERIFIED_UPDATES'),
                'ENVATO_API_TOKEN' => $request->get('ENVATO_API_TOKEN'),
            ]);
            $adv->save();

            return successResponse(Lang::get('lang.settings_created'), $adv, 201);
        } else {
            $advset->API_STATUS = $request->get('API_STATUS');
            $advset->VERIFIED_UPDATES = $request->get('VERIFIED_UPDATES');
            $advset->ENVATO_API_TOKEN = $request->get('ENVATO_API_TOKEN');
            $advset->save();

            return successResponse(Lang::get('lang.settings_updated'), $advset, 200);
        }
    }

    /**
     * To Add or Update the Security settings of license manager
     *
     * @param  SecuritySettingsRequest  $request
     * @param $SETTING_ID
     * @return $secset with a success response leaving the other fields to be null if not filled
     */
    public function securitySettings(SecuritySettingRequest $request, $SETTING_ID)
    {
        $secset = AflSettings::find($SETTING_ID);
        if (empty($secset)) {
            $sec = new AflSettings([
                'MIN_PASSWORD_LENGTH' => $request->get('MIN_PASSWORD_LENGTH'),
                'WHITELISTED_ACCESS' => $request->get('WHITELISTED_ACCESS'),
                'BANNED_HOSTS' => $request->get('BANNED_HOSTS'),
                'BANNED_HOST_MESSAGE' => $request->get('BANNED_HOST_MESSAGE'),
                'FAILED_LOGINS_LIMIT' => $request->get('FAILED_LOGINS_LIMIT'),
                'FAILED_LICENSINGS_LIMIT' => $request->get('FAILED_LICENSINGS_LIMIT'),
                'FAILED_HOSTS_FORGET' => $request->get('FAILED_HOSTS_FORGET'),
                'WHITELISTED_IP' => $request->get('WHITELISTED_IP'),
            ]);
            $sec->save();

            return successResponse(Lang::get('lang.settings_created'), $sec, 201);
        } else {
            $secset->MIN_PASSWORD_LENGTH = $request->get('MIN_PASSWORD_LENGTH');
            $secset->WHITELISTED_ACCESS = $request->get('WHITELISTED_ACCESS');
            $secset->BANNED_HOSTS = $request->get('BANNED_HOSTS');
            $secset->BANNED_HOST_MESSAGE = $request->get('BANNED_HOST_MESSAGE');
            $secset->FAILED_LOGINS_LIMIT = $request->get('FAILED_LOGINS_LIMIT');
            $secset->FAILED_LICENSINGS_LIMIT = $request->get('FAILED_LICENSINGS_LIMIT');
            $secset->FAILED_HOSTS_FORGET = $request->get('FAILED_HOSTS_FORGET');
            $secset->WHITELISTED_IP = $request->get('WHITELISTED_IP');
            $secset->save();

            return successResponse(lang::get('lang.settings_updated'), $secset, 200);
        }
    }

    /**
     * To Add or Update the email settings of license manager
     *
     * @param  EmailSettingsRequest  $request
     * @param $SETTING_ID
     * @return $emaset with a success response leaving the other fields to be null if not filled
     */
    public function emailSettings(EmailSettingRequest $request, $SETTING_ID)
    {
        $emaset = AflSettings::find($SETTING_ID);
        if (empty($emaset)) {
            $ema = new AflSettings([
                'EMAIL_FROM_NAME' => $request->get('EMAIL_FROM_NAME'),
                'EMAIL_FROM_ADDRESS' => $request->get('EMAIL_FROM_ADDRESS'),
                'EMAIL_CC_ADMIN' => $request->get('EMAIL_CC_SENDER'),
                'EMAIL_EXPIRING_LICENSE_DAYS' => $request->get('EMAIL_EXPIRING_LICENSE_DAYS'),
                'EMAIL_EXPIRING_UPDATES_DAYS' => $request->get('EMAIL_EXPIRING_UPDATES_DAYS'),
                'EMAIL_EXPIRING_SUPPORT_DAYS' => $request->get('EMAIL_EXPIRING_SUPPORT_DAYS'),
            ]);
            $ema->save();

            return successResponse(Lang::get('lang.settings_created'), $ema, 201);
        } else {
            $emaset->EMAIL_FROM_NAME = $request->get('EMAIL_FROM_NAME');
            $emaset->EMAIL_FROM_ADDRESS = $request->get('EMAIL_FROM_ADDRESS');
            $emaset->EMAIL_CC_ADMIN = $request->get('EMAIL_CC_SENDER');
            $emaset->EMAIL_EXPIRING_LICENSE_DAYS = $request->get('EMAIL_EXPIRING_LICENSE_DAYS');
            $emaset->EMAIL_EXPIRING_UPDATES_DAYS = $request->get('EMAIL_EXPIRING_UPDATES_DAYS');
            $emaset->EMAIL_EXPIRING_SUPPORT_DAYS = $request->get('EMAIL_EXPIRING_SUPPORT_DAYS');

            $emaset->save();

            return successResponse(lang::get('lang.settings_updated'), $emaset, 200);
        }
    }

    /**
     * To Add or Update the cleanup settings of license manager
     *
     * @param  CleanUpSettingsRequest  $request
     * @param $SETTING_ID
     * @return $clean with a success response leaving the other fields to be null if not filled
     */
    public function cleanUpSettings(CleanUpSettingRequest $request, $SETTING_ID)
    {
        $cleanup = AflSettings::find($SETTING_ID);
        if (empty($cleanup)) {
            $clean = new AflSettings([
                'DATABASE_CLEANUP_ENABLED' => $request->get('DATABASE_CLEANUP_ENABLED'),
                'DATABASE_CLEANUP_CALLBACKS' => $request->get('DATABASE_CLEANUP_CALLBACKS'),
                'DATABASE_CLEANUP_REPORTS_MAIN' => $request->get('DATABASE_CLEANUP_REPORTS_MAIN'),
                'DATABASE_CLEANUP_REPORTS_SYSTEM' => $request->get('DATABASE_CLEANUP_REPORTS_SYSTEM'),
                'DATABASE_CLEANUP_REPORTS_LICENSES' => $request->get('DATABASE_CLEANUP_REPORTS_LICENSES'),
                'DATABASE_CLEANUP_VERSIONS' => $request->get('DATABASE_CLEANUP_VERSIONS'),
                'DATABASE_CLEANUP_FILES' => $request->get('DATABASE_CLEANUP_FILES'),
            ]);
            $clean->save();

            return successResponse(Lang::get('lang.settings_created'), $clean, 201);
        } else {
            $cleanup->DATABASE_CLEANUP_ENABLED = $request->get('DATABASE_CLEANUP_ENABLED');
            $cleanup->DATABASE_CLEANUP_CALLBACKS = $request->get('DATABASE_CLEANUP_CALLBACKS');
            $cleanup->DATABASE_CLEANUP_REPORTS_MAIN = $request->get('DATABASE_CLEANUP_REPORTS_MAIN');
            $cleanup->DATABASE_CLEANUP_REPORTS_SYSTEM = $request->get('DATABASE_CLEANUP_REPORTS_SYSTEM');
            $cleanup->DATABASE_CLEANUP_REPORTS_LICENSES = $request->get('DATABASE_CLEANUP_REPORTS_LICENSES');
            $cleanup->DATABASE_CLEANUP_VERSIONS = $request->get('DATABASE_CLEANUP_VERSIONS');
            $cleanup->DATABASE_CLEANUP_FILES = $request->get('DATABASE_CLEANUP_FILES');

            $cleanup->save();

            return successResponse(lang::get('lang.settings_updated'), $cleanup, 200);
        }
    }

    public function show()
    {
        $settings = AflSettings::all();

        return successResponse(Lang::get('lang.Setting_Show'), $settings, 200);
    }

    protected function dropDownForSecuritySettings()
    {
        $sets_array = DB::table('afl_settings')->get()->toArray();
        foreach ($sets_array as $set) {
            extract((array) $set);
        }
        $failed_logins_limit_array = returnNumbersDropdownArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 'Attempts', 'Disabled', $FAILED_LOGINS_LIMIT);
        $failed_licensings_limit_array = returnNumbersDropdownArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 'Attempts', 'Disabled', $FAILED_LICENSINGS_LIMIT);
        $failed_hosts_forget_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days', 'Disabled', $FAILED_HOSTS_FORGET);

        return response()->json([
            'failed logins limit' => $failed_logins_limit_array,
            'failed licensings limit' => $failed_licensings_limit_array,
            'failed hosts forget' => $failed_hosts_forget_array,
        ]);
    }

    protected function dropDownForGeneralSettings()
    {
        $sets_array = DB::table('afl_settings')->get()->toArray();
        foreach ($sets_array as $set) {
            extract((array) $set);
        }

        $timezones_array = returnTimezonesDropdownArray($TIMEZONE);
        $records_on_admin_page_array = returnNumbersDropdownArray([10, 25, 50, 100, 200, 500], 'Records', 'Disabled', $RECORDS_ON_ADMIN_PAGE);
        $records_on_index_page_array = returnNumbersDropdownArray([1, 3, 5, 10], 'Records', 'Disabled', $RECORDS_ON_INDEX_PAGE);
        $records_on_search_page_array = returnNumbersDropdownArray([10, 25, 50, 100, 200, 500], 'Records', 'Disabled', $RECORDS_ON_SEARCH_PAGE);
        $records_archive_days_array = returnNumbersDropdownArray([0, 7, 14, 30, 60, 90, 180, 365, 730], 'Days', 'Disabled', $RECORDS_ARCHIVE_DAYS);

        return response()->json([
            'Timezon' => $timezones_array,
            'records on admin page' => $records_on_admin_page_array,
            'records on index page' => $records_on_index_page_array,
            'records on search page' => $records_on_search_page_array,
            'records on archieve days' => $records_archive_days_array,
        ]);
    }

    protected function dropDownForEmailSettings()
    {
        $sets_array = DB::table('afl_settings')->get()->toArray();
        foreach ($sets_array as $set) {
            extract((array) $set);
        }

        $email_expiring_license_days_array = returnNumbersDropdownArray([0, 1, 7, 14, 30], 'Days', 'Disabled', $EMAIL_EXPIRING_LICENSE_DAYS);
        $email_expiring_updates_days_array = returnNumbersDropdownArray([0, 1, 7, 14, 30], 'Days', 'Disabled', $EMAIL_EXPIRING_UPDATES_DAYS);
        $email_expiring_support_days_array = returnNumbersDropdownArray([0, 1, 7, 14, 30], 'Days', 'Disabled', $EMAIL_EXPIRING_SUPPORT_DAYS);

        return response()->json([
            'email expiring license days' => $email_expiring_license_days_array,
            'email expiring updates days' => $email_expiring_updates_days_array,
            'email expiring support days' => $email_expiring_support_days_array,
        ]);
    }

    protected function dropDownForCleanUpSettings()
    {
        $sets_array = DB::table('afl_settings')->get()->toArray();
        foreach ($sets_array as $set) {
            extract((array) $set);
        }

        $database_cleanup_callbacks_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days', 'Disabled', $DATABASE_CLEANUP_CALLBACKS);
        $database_cleanup_reports_main_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days', 'Disabled', $DATABASE_CLEANUP_REPORTS_MAIN);
        $database_cleanup_reports_system_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days', 'Disabled', $DATABASE_CLEANUP_REPORTS_SYSTEM);
        $database_cleanup_licenses_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days ago', 'Disabled', $DATABASE_CLEANUP_REPORTS_LICENSES);

        return response()->json([
            'database cleanup callbacks' => $database_cleanup_callbacks_array,
            'database cleanup reports main' => $database_cleanup_reports_main_array,
            'database cleanup reports system' => $database_cleanup_reports_system_array,
            'database cleanup licenses' => $database_cleanup_licenses_array,
        ]);
    }
    public function saveUpdateExpireRange(Request $request)
    {
        $update_startDate = Carbon\Carbon::today();
        $update_endDate = Carbon\Carbon::today()->addDays($request->count);
        $expire_update = AflLicenses::where('license_expire_date', '>=', $update_startDate,)
            ->where('license_expire_date', '<=', $update_endDate)->get();
        DB::table('expire_updates_display')->truncate();
        foreach ($expire_update as $e) {
            $expire_update = new ExpireUpdatesDisplay();
            $expire_update->license_id = $e->license_id;
            $expire_update->save();
        }
        return response()->json(['expire_update' => $expire_update]);
    }
    protected function getUpdatesExpirings()
    {
        $expiring_update = ExpireUpdatesDisplay::join('afl_licenses as a', 'a.license_id', '=', 'expire_updates_display.license_id')->get();
        return response()->json(['expiring_update' => $expiring_update]);
    }

    protected function saveSupportExpireRange(Request $request)
    {
        $support_startDate = Carbon\Carbon::today();
        $support_endDate = Carbon\Carbon::today()->addDays($request->count);
        $expire_support = AflLicenses::where('license_support_date', '>=', $support_startDate,)
            ->where('license_support_date', '<=', $support_endDate)
            ->get();
        foreach ($expire_support as $e) {
            $expire_support = new ExpireSupportDisplay();
            $expire_support->license_id = $e->license_id;
            $expire_support->save();
        }
        return response()->json(['expire_support' => $expire_support,]);
    }
    protected function getSupportExpirings()
    {
        $expiring_support = ExpireSupportDisplay::join('afl_licenses as a', 'a.license_id', '=', 'expire_support_display.license_id')->get();
        return response()->json(['expiring_support' => $expiring_support]);
    }
    protected function debuggerSettings(Request $request)
    {
        $activateUser= Cache::get('activateUserId');
        $activateUserId = Cache::get('abcd'.$activateUser);
        $debug = (bool) ($request->debug ?? false);
        Config::set('app.debug', $debug);
        $user_id =  DB::table('afl_admins')->where('admin_id',$activateUserId)->value('admin_id');
        $authorizationHeader = $request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $authorizationHeader);
         $debug ? Cache::forever($user_id, $token) : Cache::forget($user_id);
        $envFilePath = base_path('.env');
        if (File::exists($envFilePath)) {
            $envFileContents = File::get($envFilePath);
            $envFileContents = preg_replace('/^APP_DEBUG=.*$/m', 'APP_DEBUG=' . ($debug ? 'true' : 'false'), $envFileContents);
            File::put($envFilePath, $envFileContents);
            Artisan::call('config:clear');
            $dotenv = Dotenv::createImmutable(base_path());
            $dotenv->load();
        }
        return response()->json([
            'status' => true,
            'debug' => config('app.debug'),
        ]);
    }
    protected function SaveTokenForDebugger(Request $request)
    {
        $activateUserId = Cache::get('activateUserId');
        $acticateUserId= $activateUserId;
        $admin = DB::table('afl_admins')->where('admin_id', $acticateUserId)->first('admin_id');
        $debug = Config::get('app.debug');
        $authorizationHeader = $request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $authorizationHeader);
        $user_id =  DB::table('afl_admins')->where('admin_id',$admin->admin_id)->value('admin_id');
        if($debug === true && $request->getdebug == "true" && $authorizationHeader ){
            Cache::forever($user_id, $token);
        }
    }
    protected function getDebugerValue()
    {
        $debegget = Config::get('app.debug');
        return response()->json([
            'status' => true,
            'debugget' => $debegget,
        ]);
    }
    
}
