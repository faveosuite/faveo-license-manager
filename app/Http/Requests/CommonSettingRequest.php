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
            // Google reCAPTCHA fields
            'google_site_key' => 'sometimes|required',
            'google_secret_key' => [
                'sometimes',
                'required',
                new CaptchaValidation(Lang::get('lang.invalid_secret_key')),
            ],
            'g-recaptcha-response' => 'sometimes|required',

            // reCAPTCHA and general settings
            'recaptcha_status' => 'sometimes|required',

            // URL and time/date format fields
            'agora_invoicing_url' => 'sometimes|required|url',
            'timezone' => 'sometimes|required|integer',
            'time_format' => 'sometimes|required|integer',
            'date_format' => 'sometimes|required|integer',

            // File uploads
            'icon' => 'sometimes|required|image|mimes:png,jpg,jpeg,gif|max:2048',
            'admin_logo' => 'sometimes|required|image|mimes:png,jpg,jpeg,gif|max:2048',
            'client_logo' => 'sometimes|required|image|mimes:png,jpg,jpeg,gif|max:2048',

            // Default status for logos and icon
            'icon_default' => 'sometimes|required',
            'admin_logo_default' => 'sometimes|required',
            'client_logo_default' => 'sometimes|required',
        ];
    }
}
