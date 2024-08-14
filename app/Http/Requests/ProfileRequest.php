<?php

namespace App\Http\Requests;

use App\Traits\RequestJsonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class ProfileRequest extends FormRequest
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
        $userId = getAuthUserId();

        $rules = [
            'client_fname' => 'required|max:30',
            'client_lname' => 'required|max:30',
            'client_username' => 'required|max:30|unique:users,client_username,' . $userId. ',client_id',
            'client_email' => 'required|email|unique:users,client_email,' . $userId. ',client_id',
            'client_mobile' => $this->checkMobile($userId),
            'client_mobile_code' => 'max:5',
            'client_timezone_id' => 'required',
            'client_profile_pic' => 'sometimes|nullable',
        ];

        if ($this->hasFile('client_profile_pic') && $this->client_profile_pic != 'null') {
            $rules['client_profile_pic'] .= '|mimes:png,jpeg,jpg';
        }

        if ($this->segment(3) == 'password') {
            return [
                'old_password' => 'required|min:6',
                'new_password' => 'required|min:6',
                'confirm_password' => 'required|same:new_password',
            ];
        }
        return $rules;
    }

    public function checkMobile($userId)
    {
        $rule = 'numeric|nullable';

        if (getAuthUser()->client_mobile != Request::input('client_mobile')) {
            $rule .= '|unique:users,client_mobile,' . $userId. ',client_id';
        }

        return $rule;
    }
    public function messages()
    {
        return [
            'client_fname.required' => Lang::get('lang.client_fname.required'),
            'client_fname.max' => Lang::get('lang.client_fname.max'),
            'client_lname.required' => Lang::get('lang.client_lname.required'),
            'client_lname.max' => Lang::get('lang.client_lname.max'),
            'client_username.required' => Lang::get('lang.client_username.required'),
            'client_username.max' => Lang::get('lang.client_username.max'),
            'client_username.unique' => Lang::get('lang.client_username.unique'),
            'client_email.required' => Lang::get('lang.client_email.required'),
            'client_email.email' => Lang::get('lang.client_email.email'),
            'client_email.unique' => Lang::get('lang.client_email.unique'),
            'client_mobile.numeric' => Lang::get('lang.client_mobile.numeric'),
            'client_mobile.unique' => Lang::get('lang.client_mobile.unique'),
            'client_mobile_code.max' => Lang::get('lang.client_mobile_code.max'),
            'client_profile_pic.mimes' => Lang::get('lang.client_profile_pic.mimes'),
            'old_password.required' => Lang::get('lang.old_password.required'),
            'old_password.min' => Lang::get('lang.old_password.min'),
            'new_password.required' => Lang::get('lang.new_password.required'),
            'new_password.min' => Lang::get('lang.new_password.min'),
            'confirm_password.required' => Lang::get('lang.confirm_password.required'),
            'confirm_password.same' => Lang::get('lang.confirm_password.same'),
            'client_timezone_id.required' => Lang::get('lang.client_timezone_id.required'),
        ];
    }
}
