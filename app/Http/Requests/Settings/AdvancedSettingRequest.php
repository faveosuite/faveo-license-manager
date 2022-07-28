<?php

namespace App\Http\Requests\Settings;

use App\Traits\RequestJsonValidation;
use Illuminate\Foundation\Http\FormRequest;

class AdvancedSettingRequest extends FormRequest
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
            'API_STATUS' => 'required|boolean',
            'ENVATO_API_TOKEN' => 'string',
        ];
    }
}
