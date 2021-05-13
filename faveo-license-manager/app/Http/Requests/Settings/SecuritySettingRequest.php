<?php

namespace App\Http\Requests\Settings;
use App\Models\AflSettings;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Foundation\Http\FormRequest;

class SecuritySettingRequest extends FormRequest
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
            'MIN_PASSWORD_LENGTH'=> 'numeric|min:1|max:127',
            'WHITELISTED_ACCESS' => 'boolean',
            'BANNED_HOSTS' => 'boolean',
            'BANNED_HOST_MESSAGE' => 'string',
            'FAILED_LOGINS_LIMIT' => 'numeric|min:0|max:10',
            'FAILED_LICENSINGS_LIMIT' => 'numeric|min:0|max:10',
            'FAILED_HOSTS_FORGET' => 'numeric|min:0|max:365',
            'WHITELISTED_IP' =>'string'

        ];
    }
}
