<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class whitelistIpsRequest extends FormRequest
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
            'whitelist_host_ip' => 'required|string|unique:afl_whitelist_ips,whitelist_host_ip|ip',
        ];
    }
}
