<?php

namespace App\Http\Requests;

use App\Models\AflAdmins;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
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
            
            'admin_fname'=> 'required|string',
            'admin_lname'=> 'required|string',
            'admin_email' => 'required|string|unique:afl_admins,admin_email',
            'admin_password'=> 'required|string|min:8|confirmed',
            'admin_ip'=> 'string',
            'admin_date' => 'string'

       
        ];
    }
}
