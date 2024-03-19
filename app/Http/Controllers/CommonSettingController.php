<?php

namespace App\Http\Controllers;

use App\Models\CommonSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CommonSettingController extends Controller
{
    public function __construct(){
      $this->requestKeys = ['google_site_key'];
      $this->envKeys = ['RECAPTCHA_SITE_KEY'];
    }
    public function createOrUpdateCommonSetting(Request $request){

        foreach ($request->all() as $key => $value) {
            CommonSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->anyEnvUpdate($request,$this->envKeys,$this->requestKeys);

        return successResponse(trans('lang.common_setting_svaed'));
    }


    public function getCommonSetting(){
        try {
            $commonSettings = CommonSetting::all();

            $settingsArray = [];
            foreach ($commonSettings as $setting) {
                $settingsArray[$setting->key] = $setting->value;
            }

            return successResponse('',$settingsArray);

        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function clearCommonSetting(){
        try{
            CommonSetting::query()->delete();
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

}
