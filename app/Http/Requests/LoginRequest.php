<?php

namespace App\Http\Requests;

use App\Models\CommonSetting;
use App\Rules\CaptchaValidation;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'client_email' => 'required|string',
            'client_password' => 'required|string',
        ];

        // Check if reCAPTCHA is enabled in the configuration
        if (CommonSetting::where('key','google-secret-key')->value('value')) {
            // Add the validation rule for reCAPTCHA response
            $rules['g-recaptcha-response'] = ['required', new CaptchaValidation];
        }

        return $rules;
    }
}
