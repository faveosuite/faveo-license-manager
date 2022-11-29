<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
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
            'notification_product_not_found' => 'required|string',
            'notification_product_inactive' => 'required|string',
            'notification_license_ok' => 'required|string',
            'notification_license_not_found' => 'required|string',
            'notification_invalid_ip' => 'required|string',
            'notification_invalid_domain' => 'required|string',
            'notification_domain_required' => 'required|string',
            'notification_domain_in_use' => 'required|string',
            'notification_license_suspended' => 'required|string',
            'notification_license_expired' => 'required|string',
            'notification_updates_expired' => 'required|string',
            'notification_support_expired' => 'required|string',
            'notification_license_cancelled' => 'required|string',
            'notification_license_limit' => 'required|string',
            'notification_installation_not_found' => 'required|string',
            'notification_invalid_signature' => 'required|string',
            'notification_host_banned' => 'required|string',
            'notification_unknown_error' => 'required|string',
        ];
    }
}
