<?php

namespace App\Rules;

use App\Models\CommonSetting;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class CaptchaValidation implements Rule
{
    protected $message;

    public function __construct($message = null)
    {
        $this->message = $message;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $googleSecretKey = \Request::get('google_secret_key') ?:  CommonSetting::where('key','google_secret_key')->value('value');
        $value = \Request::get('google_secret_key') ? \Request::get('g-recaptcha-response') : $value;
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $googleSecretKey,
            'response' => $value,
        ]);
        return $response->json('success');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message ?? 'Woah, This will be termed as a hacking attempt.';
    }
}
