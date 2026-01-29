<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\AdvancedSettingRequest;
use App\Http\Requests\Settings\CleanUpSettingRequest;
use App\Http\Requests\Settings\EmailSettingRequest;
use App\Http\Requests\Settings\GeneralSettingsRequest;
use App\Http\Requests\Settings\SecuritySettingRequest;
use App\Models\AflSettings;
use App\Models\GoogleRecaptchaSetting;
use Exception;
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
use App\Models\ScheduleCron;
use App\Models\AflLicenses;
use Illuminate\Support\Facades\Redirect;
use Throwable;

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

        AflSettings::updateOrCreate(["SETTING_ID" => $SETTING_ID],
        [
            'WHITELISTED_ACCESS' => $request->input('WHITELISTED_ACCESS'),
            'BANNED_HOSTS' => $request->input('BANNED_HOSTS'),
            'FAILED_HOSTS_FORGET' => $request->input('FAILED_FORGET_LIMIT') > 0 ? 1 : 0,
            'FAILED_FORGET_LIMIT' => $request->input('FAILED_FORGET_LIMIT') ?: null,
            'FAILED_LOGINS' => $request->input('FAILED_LOGINS_LIMIT') > 0 ? 1 : 0,
            'FAILED_LOGINS_LIMIT' => $request->input('FAILED_LOGINS_LIMIT') ?: null,
        ]);

        return successResponse(trans('lang.settings_updated'), 200);
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
    public function saveintervalSettings(Request $request)
    {
        $cleanup = AflSettings::first();
        if (empty($cleanup)) {
            $clean = new AflSettings([
                'DATABASE_CLEANUP_CALLBACKS' => $request->get('DATABASE_CLEANUP_CALLBACKS'),
                'DATABASE_CLEANUP_REPORTS_MAIN' => $request->get('DATABASE_CLEANUP_REPORTS_MAIN'),
                'DATABASE_CLEANUP_REPORTS_SYSTEM' => $request->get('DATABASE_CLEANUP_REPORTS_SYSTEM'),
                'DATABASE_CLEANUP_REPORTS_LICENSES' => $request->get('DATABASE_CLEANUP_REPORTS_LICENSES'),
                'DATABASE_CLEANUP_VERSIONS' => $request->get('DATABASE_CLEANUP_VERSIONS'),
            ]);
            $clean->save();

            return successResponse(Lang::get('lang.settings_created'), $clean, 201);
        } else {
            $cleanup->DATABASE_CLEANUP_CALLBACKS = $request->get('DATABASE_CLEANUP_CALLBACKS');
            $cleanup->DATABASE_CLEANUP_REPORTS_MAIN = $request->get('DATABASE_CLEANUP_REPORTS_MAIN');
            $cleanup->DATABASE_CLEANUP_REPORTS_SYSTEM = $request->get('DATABASE_CLEANUP_REPORTS_SYSTEM');
            $cleanup->DATABASE_CLEANUP_REPORTS_LICENSES = $request->get('DATABASE_CLEANUP_REPORTS_LICENSES');
            $cleanup->DATABASE_CLEANUP_VERSIONS = $request->get('DATABASE_CLEANUP_VERSIONS');

            $cleanup->save();

            return successResponse(lang::get('lang.settings_updated'), $cleanup, 200);
        }
    }
    public function cleanUpSettings(Request $request)
    {
        $cleanupSettings = $request->get('conditions');
        foreach ($cleanupSettings as $settingName => $settingData) {
            $job = ScheduleCron::where('scenario', $settingName)->first();

            if ($job) {
                $job->update([
                    'value' => $settingData['value'],
                    'status' => $settingData['status'],
                ]);
            }
        }
            return successResponse(Lang::get('lang.settings_created'),['Cleanup Settings' => $cleanupSettings],200);
    }

    public function cronTimeCommands()
    {
        $commands = [
            ['id' => 'everyMinute', 'name' => trans('lang.everyMinute')],
            ['id' => 'everyFiveMinutes', 'name' => trans('lang.everyFiveMinutes')],
            ['id' => 'everyTenMinutes',  'name' => trans('lang.everyTenMinutes')],
            ['id' => 'everyThirtyMinutes', 'name' => trans('lang.everyThirtyMinutes')],
            ['id' => 'hourly', 'name' => trans('lang.hourly')],
            ['id' => 'daily', 'name' => trans('lang.daily')],
            ['id' => 'dailyAt', 'name' => trans('lang.dailyAt')],
            ['id' => 'weekly', 'name' => trans('lang.weekly')],
            ['id' => 'monthly', 'name' => trans('lang.monthly')],
            ['id' => 'yearly', 'name' => trans('lang.yearly')],
        ];

        return successResponse('Commands', ['cron_time_commands' => $commands]);
    }

    public function show()
    {
        $settings = AflSettings::all();

        $settings->transform(function ($setting) {
            unset($setting->EMAIL_PASSWORD);
            if($setting->EMAIL_DRIVER === 'mail' ) {
                unset($setting->EMAIL_ENCRYPTION);
                unset($setting->EMAIL_PORT);
                unset($setting->EMAIL_HOST);
            }
            return $setting;
        });

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
        $failed_hosts_forget_array = returnNumbersDropdownArray([00, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 'Attempts', 'Disabled', $FAILED_HOSTS_FORGET);
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

        $phpBinPaths = $this->getPHPBinPath();
        $cronPath = '-q '.base_path('artisan').' schedule:run 2>&1';
        $execEnabled = $this->execEnabled();
        $sets_array = DB::table('afl_settings')->get();
        $conditions = DB::table('schedule_crons')->get()->toArray();
        return successResponse('', [ 'conditions' => $conditions,
            'php_bin_paths' => $phpBinPaths,
            'cron_path' => $cronPath,
            'settings' =>$sets_array,
            'php_extension_status' => $execEnabled,]);
    }
    protected function dropForCleanUpSettings()
    {
        $sets_array = DB::table('afl_settings')->get()->toArray();
        foreach ($sets_array as $set) {
            extract((array) $set);
        }

        $database_cleanup_callbacks_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days', 'Disabled', $DATABASE_CLEANUP_CALLBACKS);
        $database_cleanup_reports_main_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days', 'Disabled', $DATABASE_CLEANUP_REPORTS_MAIN);
        $database_cleanup_reports_system_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days', 'Disabled', $DATABASE_CLEANUP_REPORTS_SYSTEM);
        $database_cleanup_versions_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days', 'Disabled', $DATABASE_CLEANUP_REPORTS_LICENSES);
        $database_cleanup_license_reports_array = returnNumbersDropdownArray([0, 1, 7, 14, 30, 60, 90, 180, 365], 'Days', 'Disabled', $DATABASE_CLEANUP_REPORTS_LICENSES);

        return response()->json([
            'database cleanup callbacks' => $database_cleanup_callbacks_array,
            'database cleanup reports main' => $database_cleanup_reports_main_array,
            'database cleanup reports system' => $database_cleanup_reports_system_array,
            'database cleanup versions' => $database_cleanup_versions_array,
            'database cleanup reports license' => $database_cleanup_license_reports_array,
        ]);
    }

    public function execEnabled()
    {
        try {
            // make a small test
            return function_exists('exec') && ! in_array('exec', array_map('trim', explode(', ', ini_get('disable_functions'))));
        } catch (\Exception $ex) {
            return false;
        }
    }

    protected function getPHPBinPath()
    {
        $paths = [
            '/usr/bin/php',
            '/usr/local/bin/php',
            '/bin/php',
            '/usr/bin/php8',
            '/usr/bin/php8.2',
        ];
        // try to detect system's PHP CLI
        if ($this->execEnabled()) {
            try {
                $paths = array_unique(array_merge($paths, explode(' ', exec('whereis php'))));
            } catch (\Exception $e) {
                // @todo: system logging here
                echo $e->getMessage();
            }
        }
        // validate detected / default PHP CLI
        // Because array_filter() preserves keys, you should consider the resulting array to be an associative array even if the original array had integer keys for there may be holes in your sequence of keys. This means that, for example, json_encode() will convert your result array into an object instead of an array. Call array_values() on the result array to guarantee json_encode() gives you an array.
        $paths = array_values(array_filter($paths, function ($path) {
            try {
                return is_executable($path) && preg_match("/php[0-9\.a-z]{0,3}$/i", $path);
            } catch(\Exception $e) {
                // in case of open_basedir, just throw skip it
                return true;
            }
        }));

        return $paths;
    }
    public function saveUpdateExpireRange(Request $request)
    {
        $update_startDate = Carbon\Carbon::today();
        $update_endDate = Carbon\Carbon::today()->addDays($request->count);
        $expire_update = AflLicenses::where('license_expire_date', '>=', $update_startDate)
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
        $expire_support = AflLicenses::where('license_support_date', '>=', $support_startDate)
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
    public function debuggerSettings(Request $request)
    {
        $debug = (bool) ($request->debug ?? false);
        AflSettings::updateOrInsert(
            ['SETTING_ID' => 1],
            ['debugger' => $debug]
        );
        Config::set('app.debug', $debug);
        $authorizationHeader = $request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $authorizationHeader);
        $debug ? Cache::forever($request->user_id, $token) : Cache::forget($request->user_id);
        $envFilePath = base_path('.env');
        if (File::exists($envFilePath)) {
            $envFileContents = File::get($envFilePath);
            $envFileContents = preg_replace('/^APP_DEBUG=.*$/m', 'APP_DEBUG=' . ($debug ? 'true' : 'false'), $envFileContents);
            $envFileContents = preg_match('/^PULSE_ENABLED=.*$/m', $envFileContents)
                ? preg_replace('/^PULSE_ENABLED=.*$/m', "PULSE_ENABLED=" . ($debug ? 'true' : 'false'), $envFileContents)
                : $envFileContents . "\nPULSE_ENABLED=" . ($debug ? 'true' : 'false');
            $envFileContents = preg_match('/^CLOCKWORK_ENABLE=.*$/m', $envFileContents)
                ? preg_replace('/^CLOCKWORK_ENABLE=.*$/m', 'CLOCKWORK_ENABLE=' . ($debug ? 'true' : 'false'), $envFileContents)
                : $envFileContents . "\nCLOCKWORK_ENABLE=" . ($debug ? 'true' : 'false');
            File::put($envFilePath, $envFileContents);
            Artisan::call('config:clear');
            $dotenv = Dotenv::createImmutable(base_path());
            $dotenv->load();
        }
        $debug = config('app.debug');
        $app_url = env('APP_URL');
        return successResponse(trans('lang.updated'), ['debug' => $debug, 'app_url' => $app_url]);
    }
    protected function clockwork(Request $request)
    {
        $userId = $request->query('user_id');
        if ($userId) {
            return redirect('/__clockwork/app');
        } else {
            return redirect('/login');
        }
    }
    public function getDebugger()
    {
        try {
            $debuggerValue = AflSettings::where('SETTING_ID', 1)->value('debugger');
            return response()->json(['debugger' => $debuggerValue]);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(),500);
        }
    }

    public function checkPHPExecutablePath(Request $request)
    {
        try {
            $path = $request->get('path');
            $cronCommandCopiedMessage = trans('lang.cron-command-copied');
            $cronCommandNotCopiedMessage = trans('lang.cron-command-not-copied');
            $version = '8.2';
            if (! file_exists($path) || ! is_executable($path)) {
                return errorResponse($cronCommandNotCopiedMessage.' '.trans('lang.invalid-php-path'));
            }

            if ($this->execEnabled()) {
                $execScript = $path.' '.public_path('cron-test.php');
                $version = exec($execScript, $output);

                return (version_compare($version, '7.3', '>=') == 1) ? successResponse($cronCommandCopiedMessage.' '.trans('lang.valid-php-path')) : errorResponse(trans('lang.cron-command-copied').' '.trans('lang.invalid-php-version-or-path'));
            }

            return errorResponse(trans('lang.cron-command-copied').' '.trans('lang.please_enable_php_exec_for_cronjob_check'));
        } catch(\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function createOrUpdateGoogleRecaptcha(Request $request){
        try{
            $request->validate(
                [
                    'google_site_key' => 'required',
                    'google_secret_key' => 'required',
                ]
            );
            GoogleRecaptchaSetting::updateOrCreate([
                'google_site_key' => $request->input('google_site_key'),
                'google_secret_key' => $request->input('google_secret_key')
            ],
                [
                    'google_site_key' => $request->input('google_site_key'),
                    'google_secret_key' => $request->input('google_secret_key')
                ]
            );
            return successResponse(trans('lang.complete_google'));
        }
        Catch(\Exception $e){
            return errorResponse($e->getMessage(),400);
        }
    }

    public function getGoogleRecaptcha(){
        try{
            return successResponse('', GoogleRecaptchaSetting::find(1));
        }
        Catch(\Exception $e){
            return errorResponse($e->getMessage(),400);
        }
    }

    public function clearGoogleRecaptcha(Request  $request){
        try{
            !($request->has('clear'))?:GoogleRecaptchaSetting::query()->delete();
            return successResponse(trans('lang.reset_successfully'));
        }
        Catch(\Exception $e){
            return errorResponse($e->getMessage(),400);

        }
    }
}
