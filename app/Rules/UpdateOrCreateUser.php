<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrCreateUser extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = request('id');

        return [
            'admin_fname' => 'required|string|max:125',
            'admin_lname' => 'required|string|max:125',
            'admin_email' => $id ? 'required|email|unique:afl_admins,admin_email':'required|email|unique:afl_admins,admin_email,' . $id . ',admin_id',
            'admin_status' => 'required',
        ];
    }
}