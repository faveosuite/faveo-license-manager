<?php

namespace App\Rules;

use App\Models\CommonSetting;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class CaptchaValidation implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => CommonSetting::where('key','google_secret_key')->value('value'),
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
        return 'Woah, This will be termed as an hacking attempt.';
    }
}
