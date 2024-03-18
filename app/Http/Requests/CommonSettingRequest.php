<?php

namespace App\Http\Requests;

use App\Rules\CaptchaValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class CommonSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'google_site_key' => 'required',
            'google_secret_key' => 'required',
            'g-recaptcha-response' => ['required',new CaptchaValidation(Lang::get('lang.invalid_secret_key')),],
            'agora_invoicing_url' => 'required|url',
            'timezone' => 'required|integer',
            'time_format' => 'required|integer',
            'date_format' => 'required|integer',
        ];
    }
}
