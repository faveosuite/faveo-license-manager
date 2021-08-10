<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Admin\ConfigGenerateController;
class ConfigRequest extends FormRequest
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
            'product_id' =>'numeric',
            'License_Storage_type' => 'string',
            'Delete_Cancelled_License' => 'string',
            'Delete_Cracked_License' => 'string',
            'God_Mode' => 'string'             
        ];
    }
}
