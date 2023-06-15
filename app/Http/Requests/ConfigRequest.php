<?php

namespace App\Http\Requests;

use App\Traits\RequestJsonValidation;
use Illuminate\Foundation\Http\FormRequest;

class ConfigRequest extends FormRequest
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
            'product_id' => 'required|numeric',
            'License_Storage_type' => 'required|string',
            'Delete_Cancelled_License' => 'required|string',
            'Delete_Cracked_License' => 'required|string',
            'God_Mode' => 'required|string',
            'License_Verification_Period' => 'required|string',
            'Database_License_File_Location' => 'required|string',
            'MySQL_Table_Name' => 'required|string',
        ];
    }
}
