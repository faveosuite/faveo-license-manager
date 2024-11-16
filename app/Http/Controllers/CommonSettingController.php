<?php

namespace App\Http\Controllers;


use App\Facades\ImageUpload;
use App\Http\Controllers\Update\DirectoryController;
use App\Http\Requests\CommonSettingRequest;
use App\Models\CommonSetting;
use App\Models\DateFormat;
use App\Models\TimeFormat;
use App\Models\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

class CommonSettingController extends Controller
{
    public function __construct(){
      $this->requestKeys = ['google_site_key'];
      $this->envKeys = ['RECAPTCHA_SITE_KEY'];
    }

    public function createOrUpdateCommonSetting(CommonSettingRequest $request){
        $response = (new DirectoryController())->setDirectory($request);
        if($response->getStatusCode() !== 200){
            return $response;
        }
        // Get status from request or default to 1
        $status = $request->input('recaptcha_status', 1);

        // Keys related to file uploads
        $fileKeys = [
            'icon' => 'common/images/icon',
            'admin_logo' => 'common/images/admin_logo',
            'client_logo' => 'common/images/client_logo'
        ];

        // Update or create general settings except for specific keys related to files and reCAPTCHA status
        $excludedKeys = array_merge(array_keys($fileKeys), ['g-recaptcha-response', 'recaptcha_status','icon_default', 'admin_logo_default', 'client_logo_default']);
        foreach ($request->except($excludedKeys) as $key => $value) {
            CommonSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'status' => $status]
            );
        }

        // Handle reCAPTCHA keys if recaptcha_status is not set
        if (!$request->input('recaptcha_status')) {
            foreach (['google_site_key', 'google_secret_key'] as $key) {
                CommonSetting::updateOrCreate(
                    ['key' => $key],
                    ['status' => 0]
                );
            }
        }

        // Handle file uploads
        foreach ($fileKeys as $key => $path) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $filePath = $request->input("{$key}_default") ? '' : ImageUpload::saveImageToStorage($file, $path);
                CommonSetting::updateOrCreate(['key' => $key], ['value' => $filePath]);
            } elseif ($request->input("{$key}_default")) {
                CommonSetting::updateOrCreate(['key' => $key], ['value' => '']);
            }
        }

        // Update environment variables if recaptcha_status is set
        if ($request->input('recaptcha_status')) {
            $this->anyEnvUpdate($request, $this->envKeys, $this->requestKeys);
        } else {
            $this->clearSomeEnv();
        }

        return successResponse(Lang::get('lang.common_setting_svaed'));
    }


    public function getCommonSetting(){
        try {
            $commonSettings = CommonSetting::all();
            // Define default values
            $defaults = [
                'icon' => 'themes/default/img/favicon.ico',
                'admin_logo' => 'themes/default/img/logo.png',
                'client_logo' => 'themes/default/img/default.png',
            ];
            $settingsArray = [];
            foreach ($commonSettings as $setting) {
                $settingsArray[$setting->key] = $setting->value;
            }
            $settingsArray['timezone']=  Timezone::find($settingsArray['timezone']);
            $settingsArray['timezone'] = (object) [
                'id' => $settingsArray['timezone']->id,
                'location' => $settingsArray['timezone']->timezone_name,
                'name' => $settingsArray['timezone']->name,
            ];
            $settingsArray['date_format']= DateFormat::find($settingsArray['date_format']);
            $settingsArray['time_format'] = TimeFormat::find($settingsArray['time_format']);
            $settingsArray['icon'] = empty($settingsArray['icon']) ? asset($defaults['icon']) :  asset('storage/common/images/icon/'.$settingsArray['icon']);
            $settingsArray['admin_logo'] = empty($settingsArray['admin_logo']) ? asset($defaults['admin_logo']) :   asset('storage/common/images/admin_logo/'.$settingsArray['admin_logo']);
            $settingsArray['client_logo'] = empty($settingsArray['client_logo']) ? asset($defaults['client_logo']) :   asset( 'storage/common/images/client_logo/'.$settingsArray['client_logo']);
            $googleSiteKeyStatus = CommonSetting::where('key', 'google_site_key')->pluck('status')->first();
            $googleSecretKeyStatus = CommonSetting::where('key', 'google_secret_key')->pluck('status')->first();
            $recaptchaStatus = ($googleSiteKeyStatus === '1' && $googleSecretKeyStatus === '1') ? 1 : 0;
            $settingsArray['recaptcha_status'] = $recaptchaStatus;

            $storageSettings = (new DirectoryController())->getDirectory();

            $settingsArray['storage'] = json_decode($storageSettings->getContent())->data;

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
        $search = $request->input('search_query', '');
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
        $search = $request->input('search_query', '');
        $date_formats = DateFormat::where('format', 'like', '%'.$search.'%')
            ->where('is_active', 1)
            ->paginate(10, ['*'], 'page', $page);
        return successResponse('', $date_formats);
    }

    public function getDropDownForTimeFormat(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search_query', '');
        $time_formats = TimeFormat::where('hours', 'like', '%'.$search.'%')
            ->where('is_active', 1)
            ->paginate(10, ['*'], 'page', $page);
        return successResponse('', $time_formats);
    }


}
