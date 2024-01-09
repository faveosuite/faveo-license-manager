<?php

namespace App\Http\Requests;
use App\Traits\RequestJsonValidation;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class EmailSettingRequest extends FormRequest
{
    use RequestJsonValidation;
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
       // dd($this->driver);
        if ($this->EMAIL_DRIVER == 'smtp') {
            return [
                'EMAIL_DRIVER' => 'required',
                'EMAIL_FROM_ADDRESS' => 'required',
                'EMAIL_FROM_NAME' => 'required',
                'EMAIL_PASSWORD' => 'required',
                'EMAIL_PORT' => 'required',
                'EMAIL_ENCRYPTION' => 'required',
                'EMAIL_HOST' => 'required',
            ];
        } elseif ($this->driver == 'mailgun') {
            return [
                'EMAIL_DRIVER' => 'required',
                'EMAIL_FROM_ADDRESS' => 'required',
                'EMAIL_FROM_NAME' => 'required',
                'EMAIL_PASSWORD' => 'required',
                'secret' => 'required',
                'domain' => 'required',
            ];
        } elseif ($this->driver == 'mandrill') {
            return [
                'EMAIL_DRIVER' => 'required',
                'EMAIL_FROM_ADDRESS' => 'required',
                'EMAIL_PASSWORD' => 'required',
                'secret' => 'required',
            ];
        } elseif ($this->driver == 'ses') {
            return [
                'EMAIL_DRIVER' => 'required',
                'EMAIL_FROM_ADDRESS' => 'required',
                'EMAIL_PASSWORD' => 'required',
                'secret' => 'required',
                'key' => 'required',
                'region' => 'required',
            ];
        } elseif ($this->driver == 'sparkpost') {
            return [
                'EMAIL_DRIVER' => 'required',
                'EMAIL_FROM_ADDRESS' => 'required',
                'EMAIL_PASSWORD' => 'required',
                'secret' => 'required',
            ];
        } else {
            return [
                'EMAIL_DRIVER' => 'required',
                'EMAIL_FROM_NAME' => 'required',
                'EMAIL_FROM_ADDRESS' => [
                    'required',
                    'email',
                    function ($attribute, $value, $fail) {
                        $emailDomain = explode('@', $value)[1];
                        $url = Request::url();
                        $domain = parse_url($url);
                        if (strcasecmp($domain['host'], $emailDomain) !== 0) {
                            return $fail('The email domain does not match the URL domain.');
                        }
                    },
                ],
            ];
        }
    }
}
