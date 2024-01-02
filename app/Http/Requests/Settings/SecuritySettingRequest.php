<?php

namespace App\Http\Requests\Settings;

use App\Traits\RequestJsonValidation;
use Illuminate\Foundation\Http\FormRequest;

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
            'WHITELISTED_ACCESS' => 'required|boolean',
            'BANNED_HOSTS' => 'required|boolean',
            'FAILED_LOGINS_LIMIT' => 'required|numeric|min:0|max:10',

        ];
    }
}
