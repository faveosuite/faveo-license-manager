<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\afl_clients;

class ClientRequest extends FormRequest
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
            'client_fname'=> 'required|string',
            'client_lname'=> 'required|string',
            'client_email'=> 'required|string|unique:afl_products,client_email',
            'client_active_date'=>'date',
            'client_cancel_date'=> 'date',
            'client_status'=>'boolean'
        ];
    }
}
