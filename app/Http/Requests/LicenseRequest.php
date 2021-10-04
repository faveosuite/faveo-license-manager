<?php

namespace App\Http\Requests;

use App\Models\AflLicenses;
use App\Http\Controllers\Admin\LicenseController;

use Illuminate\Foundation\Http\FormRequest;

class LicenseRequest extends FormRequest
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

                    'license_code' => 'string',
                    'product_id' => 'numeric',
                    'license_order_number' => 'numeric',
                    'license_require_domain' => 'boolean',
                    'license_limit' => 'numeric',
                    'license_date' => 'date',
                    'license_cancel_date' => 'date',
                    'license_expire_email_date' => 'date',
                    'license_updates_date' => 'date',
                    'license_updates_email_date' =>'date',
                    'license_support_email_date' => 'date',
                    'license_support_date' => 'date',
                    'license_status' =>'boolean'
        ];
    }
}
