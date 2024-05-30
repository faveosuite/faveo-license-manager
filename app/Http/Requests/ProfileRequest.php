<?php

namespace App\Http\Requests;

use App\Traits\RequestJsonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

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
//            'client_timezone_id' => 'required',
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
            'client_fname.required' => 'First name is required.',
            'client_fname.max' => 'First name may not be greater than 30 characters.',
            'client_lname.required' => 'Last name is required.',
            'client_lname.max' => 'Last name may not be greater than 30 characters.',
            'client_username.required' => 'Username is required.',
            'client_username.max' => 'Username may not be greater than 30 characters.',
            'client_username.unique' => 'Username has already been taken.',
            'client_email.required' => 'Email is required.',
            'client_email.email' => 'Email must be a valid email address.',
            'client_email.unique' => 'Email has already been taken.',
            'client_mobile.numeric' => 'Mobile number must be numeric.',
            'client_mobile.unique' => 'Mobile number has already been taken.',
            'client_mobile_code.max' => 'Mobile code may not be greater than 5 characters.',
            'client_profile_pic.mimes' => 'Profile picture must be a file of type: png, jpeg, jpg.',
            'old_password.required' => 'Old password is required.',
            'old_password.min' => 'Old password must be at least 6 characters.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 6 characters.',
            'confirm_password.required' => 'Confirm password is required.',
            'confirm_password.same' => 'Confirm password must match the new password.',
        ];
    }
}
