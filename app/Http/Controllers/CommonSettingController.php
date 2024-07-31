<?php

namespace App\Http\Controllers;

use App\Facades\ImageUpload;
use App\Http\Requests\CommonSettingRequest;
use App\Models\CommonSetting;
use App\Models\DateFormat;
use App\Models\TimeFormat;
use App\Models\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CommonSettingController extends Controller
{
    public function __construct(){
      $this->requestKeys = ['google_site_key'];
      $this->envKeys = ['RECAPTCHA_SITE_KEY'];
    }
    public function createOrUpdateCommonSetting(CommonSettingRequest $request){
        // Get status from request or default to 1
        $status = $request->input('recaptcha_status', 1);

        // Update or create general settings except for specific keys related to files
        foreach ($request->except(['g-recaptcha-response', 'icon_default', 'admin_logo_default', 'client_logo_default', 'icon', 'client_logo', 'admin_logo']) as $key => $value) {
            CommonSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'status' => $status]
            );
        }

        // Define default values
        $defaults = [
            'icon' => 'themes/default/img/favicon.ico',
            'admin_logo' => 'themes/default/img/logo.png',
            'client_logo' => 'themes/default/img/avatar.png',
        ];

        // Handle icon
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconPath = $request->input('icon_default') ? $defaults['icon'] : ImageUpload::saveImageToStorage($icon, 'common/images/icon');
            CommonSetting::updateOrCreate(['key' => 'icon'], ['value' => $iconPath]);
        } else if ($request->input('icon_default')) {
            CommonSetting::updateOrCreate(['key' => 'icon'], ['value' => $defaults['icon']]);
        }

        // Handle admin logo
        if ($request->hasFile('admin_logo')) {
            $logoAdmin = $request->file('admin_logo');
            $adminLogoPath = $request->input('admin_logo_default') ? $defaults['admin_logo'] : ImageUpload::saveImageToStorage($logoAdmin, 'common/images/admin_logo');
            CommonSetting::updateOrCreate(['key' => 'admin_logo'], ['value' => $adminLogoPath]);
        } else if ($request->input('admin_logo_default')) {
            CommonSetting::updateOrCreate(['key' => 'admin_logo'], ['value' => $defaults['admin_logo']]);
        }

        // Handle client logo
        if ($request->hasFile('client_logo')) {
            $logoClient = $request->file('client_logo');
            $clientLogoPath = $request->input('client_logo_default') ? $defaults['client_logo'] : ImageUpload::saveImageToStorage($logoClient, 'common/images/client_logo');
            CommonSetting::updateOrCreate(['key' => 'client_logo'], ['value' => $clientLogoPath]);
        } else if ($request->input('client_logo_default')) {
            CommonSetting::updateOrCreate(['key' => 'client_logo'], ['value' => $defaults['client_logo']]);
        }

        $this->anyEnvUpdate($request, $this->envKeys, $this->requestKeys);

        return successResponse(trans('lang.common_setting_svaed'));
    }


    public function getCommonSetting(){
        try {
            $commonSettings = CommonSetting::all();

            $settingsArray = [];
            foreach ($commonSettings as $setting) {
                $settingsArray[$setting->key] = $setting->value;
            }
            $settingsArray['timezone']=  Timezone::find($settingsArray['timezone']);
            $settingsArray['date_format']= DateFormat::find($settingsArray['date_format']);
            $settingsArray['time_format'] = TimeFormat::find($settingsArray['time_format']);

            $googleSiteKeyStatus = CommonSetting::where('key', 'google_site_key')->pluck('status')->first();
            $googleSecretKeyStatus = CommonSetting::where('key', 'google_secret_key')->pluck('status')->first();

            $recaptchaStatus = ($googleSiteKeyStatus === '1' && $googleSecretKeyStatus === '1') ? 1 : 0;
            $settingsArray['recaptcha_status'] = $recaptchaStatus;

            return successResponse('',$settingsArray);

        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function clearCommonSetting(){
        try{
            $settings = [
                'agora_invoicing_url' => '',
                'timezone' => Timezone::where('name','UTC')->value('id'),
                'date_format' => DateFormat::where('format','F j, Y')->value('id'),
                'time_format' => TimeFormat::where('format','g:i a')->value('id'),
            ];
            foreach ($settings as $key => $value) {
                CommonSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
            $this->clearSomeEnv();
            return successResponse(trans('lang.reset_successfully'));
        }
        Catch(\Exception $e){
            return errorResponse($e->getMessage(),400);
        }
    }

    private function anyEnvUpdate($request, $envKeys, $requestKeys) {
        try {
            $content = file_get_contents(base_path('.env'));

            foreach ($requestKeys as $index => $key) {
                if ($request->has($key)) {
                    $value = $request->input($key);
                    $envKey = $envKeys[$index];
                    $pattern = "/^$envKey=.*/m";
                    $replacement = "$envKey=$value";
                    $content = preg_replace($pattern, $replacement, $content);
                }
            }

            file_put_contents(base_path('.env'), $content);

            return successResponse(trans('lang.updated_successfully'));
        } catch(\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    private function clearSomeEnv()
    {
        $envFilePath = base_path('.env');

        // Read the contents of the .env file
        $envContent = File::get($envFilePath);

        foreach ($this->envKeys as $envKey){
            // Use double quotes to properly interpolate $envKey variable in the regex
            $envContent = preg_replace("/^($envKey)=(.*)$/m", "$1=", $envContent);
        }

        // Write the updated contents back to the .env file
        File::put($envFilePath, $envContent);
    }

    public function getDropDownForTimezone(Request $request)
    {
        $sortField = 'name';
        $sortOrder = 'asc';
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $timezones = Timezone::whereRaw("concat(location, ' ', name) LIKE ?", ['%'.$search.'%'])
            ->select('id', 'name', 'location')
            ->orderBy($sortField,$sortOrder)
            ->paginate(10, ['*'], 'page', $page);
        $timezones->getCollection()->transform(function ($element) {
            return (object) ['id' => $element->id, 'location' => $element->timezone_name,'name' => $element->name];
        });
        return successResponse('', $timezones);
    }

    public function getDropDownForDateFormat(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $date_formats = DateFormat::where('format', 'like', '%'.$search.'%')
            ->where('is_active', 1)
            ->paginate(10, ['*'], 'page', $page);
        return successResponse('', $date_formats);
    }

    public function getDropDownForTimeFormat(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $time_formats = TimeFormat::where('hours', 'like', '%'.$search.'%')
            ->where('is_active', 1)
            ->paginate(10, ['*'], 'page', $page);
        return successResponse('', $time_formats);
    }


}
