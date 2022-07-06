<?php

namespace App\Http\Requests\Settings;
use App\Models\AflSettings;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\RequestJsonValidation;


class GeneralSettingsRequest extends FormRequest
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
            'TIMEZONE' => 'required|string',
            'RECORDE_ON_ADMIN_PAGE' => 'required|numeric|min:10|max:500', 
            'RECORDE_ON_INDEX_PAGE' => 'required|numeric|min:1|max:10', 
            'RECORDE_ON_SEARCH_PAGE'=> 'required|numeric|min:10|max:500', 
            'SMART_REPORTS'=> 'required|boolean', 
            'SMART_TABLES'=> 'required|boolean', 
            'RECORDE_ARCHIVE_DAYS'=> 'required|numeric|min:0|max:730',
            'ROOT_URL' => 'string',
           
        
        ];
    }


}
