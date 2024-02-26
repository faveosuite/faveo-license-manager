<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestJsonValidation;



class whitelistIpsRequest extends FormRequest
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
        return [
            'whitelist_host_ip' => [
                'required',
                'string',
                'unique:afl_whitelist_ips,whitelist_host_ip',
                'regex:/\b(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}\b|\b(?:\d{1,3}\.){3}\d{1,3}\b/'
            ],
            ];
    }
}
