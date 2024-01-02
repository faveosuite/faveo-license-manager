<?php

namespace App\Http\Requests\Settings;

use App\Traits\RequestJsonValidation;
use Illuminate\Foundation\Http\FormRequest;

class CleanUpSettingRequest extends FormRequest
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
            'DATABASE_CLEANUP_CALLBACKS' => 'required|numeric|min:0|max:365',
            'DATABASE_CLEANUP_REPORTS_MAIN' => 'required|numeric|min:0|max:365',
            'DATABASE_CLEANUP_REPORTS_SYSTEM' => 'required|numeric|min:0|max:365',
           'DATABASE_CLEANUP_REPORTS_LICENSES' => 'required|numeric|min:0|max:365',
        ];
    }
}
