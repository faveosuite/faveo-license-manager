<?php

namespace App\Http\Requests\Settings;
use App\Models\AflSettings;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestJsonValidation;


class SecuritySettingRequest extends FormRequest
{
     use RequestJsonValidation;

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
            'MIN_PASSWORD_LENGTH'=> 'required|numeric|min:1|max:127',
            'WHITELISTED_ACCESS' => 'required|boolean',
            'BANNED_HOSTS' => 'required|boolean',
            'BANNED_HOST_MESSAGE' => 'required|string',
            'FAILED_LOGINS_LIMIT' => 'required|numeric|min:0|max:10',
            'FAILED_LICENSINGS_LIMIT' => 'required|numeric|min:0|max:10',
            'FAILED_HOSTS_FORGET' => 'required|numeric|min:0|max:365',
            'WHITELISTED_IP' =>'required|string'

        ];
    }
}
