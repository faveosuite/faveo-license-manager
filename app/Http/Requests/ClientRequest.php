<?php

namespace App\Http\Requests;

use App\Models\AflClients;
use App\Http\Controllers\Admin\ClientsController;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestJsonValidation;


class ClientRequest extends FormRequest
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
            'client_fname'=> 'string',
            'client_lname'=> 'string',
            'client_email'=> 'string|unique:afl_clients,client_email',
            'client_active_date'=>'date',
            'client_cancel_date'=> 'date',
            'client_status'=>'boolean'
        ];
    }
}
