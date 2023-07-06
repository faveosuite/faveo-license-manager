<?php

namespace App\Http\Requests;

use App\Traits\RequestJsonValidation;
use Illuminate\Foundation\Http\FormRequest;

class BannedHostRequest extends FormRequest
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
            'banned_host_ip' => 'required|string|unique:afl_banned_hosts,banned_host_ip',
            'banned_host_date' => 'date',
            'banned_host_blocks' => 'numeric',
            'banned_host_last_block_date' => 'date',
        ];
    }
}
