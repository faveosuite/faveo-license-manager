<?php

namespace App\Http\Requests\Settings;
use App\Models\AflSettings;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Foundation\Http\FormRequest;

class EmailSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'EMAIL_FROM_NAME'=> 'required|string', 
            'EMAIL_FROM_ADDRESS'=>'required|string|unique:afl_settings,EMAIL_FROM_ADDRESS', 
            'EMAIL_CC_SENDER'=>'boolean', 
            'EMAIL_EXPIRING_LICENSE_DAYS'=>'numeric|min:0|max:30', 
            'EMAIL_EXPIRING_UPDATES_DAYS'=>'numeric|min:0|max:30', 
            'EMAIL_EXPIRING_SUPPORT_DAYS'=>'numeric|min:0|max:30'
        ];
    }
}
