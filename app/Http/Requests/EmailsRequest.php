<?php

namespace App\Http\Requests;
use App\Models\AflEmails;
use App\Http\Controllers\Admin\EmailsController;
use Illuminate\Foundation\Http\FormRequest;

class EmailsRequest extends FormRequest
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
            'email_expiring_license_subject'=> 'required|string', 
            'email_expiring_license_text'=> 'required|string', 
            'email_expiring_updates_subject'=>'required|string', 
            'email_expiring_updates_text'=>'required|string', 
            'email_expiring_support_subject'=>'required|string', 
            'email_expiring_support_text'=>'required|string'
        ];
    }
}
