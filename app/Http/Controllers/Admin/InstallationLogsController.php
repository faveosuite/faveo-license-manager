<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallationLogs;
use Illuminate\Http\Request;

class InstallationLogsController extends Controller
{
    protected $ip_address;
    protected $refer;

    public function __construct()
    {
        $this->ip_address = request()->server('REMOTE_ADDR');

        if (null !== (request()->server('HTTP_REFERER'))) {
            $this->refer = request()->server('HTTP_REFERER');
        } else {
            $this->refer = request()->get('refer');
        }
    }

    public function getInstallationLogs(Request $request)
    {
        $action_success = 0; // will be changed to 1 later only if everything OK
        $error_detected = 0; // will be changed to 1 later if error occurs
        $api_error_detected = 0;
        $api_error_details = '';
        $api_key = new ApiKeysController();
        $api_key_secret = $request->get('api_key_secret');
        $licenseCode = $request->get('license_code');

        // Check API key
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);
        if ($api_action_success) {
            $message = InstallationLogs::where('license_code',$licenseCode)
                ->orderBy('installation_last_active_date','desc')
                ->get()->toArray();
            $action_success = 1;
        } else {
            $api_error_detected = 1;
            $message = "The action could not be completed because of this reason: $api_error_details";
        }

        $api_response_array = [
            'api_action_success' => $api_action_success,
            'api_error_detected' => $api_error_detected,
            'action_success' => $action_success,
            'error_detected' => $error_detected,
            'page_message' => $message,
        ];

        return json_encode($api_response_array);
    }

    public function updateInstallationLogs(Request $request)
    {
        $action_success = 0; // will be changed to 1 later only if everything OK
        $error_detected = 0; // will be changed to 1 later if error occurs
        $api_error_detected = 0;
        $api_error_details = '';
        $api_key_secret = $request->get('api_key_secret');
        $root_url = $request->get('root_url');
        $versionNumber = $request->get('version_number');
        $installation_ip = $request->get('installation_ip');
        $api_key = new ApiKeysController();
        $message = '';

        // Check API key and other conditions
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);
        if (filter_var($this->ip_address, FILTER_VALIDATE_IP) && $api_action_success && $root_url) {
            $installation_domain = getRootUrl($root_url, 1, 1, 0, 1);

            // Check if the installation exists in license manager, then update or create in logs
            if ($installation_domain) {
                InstallationLogs::updateOrCreate(
                    ['installation_domain' => $installation_domain],
                    [
                        'version_number' => $versionNumber,
                        'installation_ip' => $installation_ip,
                        'installation_status' => 1,
                        'installation_last_active_date' => date('Y-m-d H:i:s'),
                    ]
                );
                $action_success = 1;
                $message = "Installation Logs updated successfully";
            } else {
                $error_detected = 1;
                $message = "Installation does not exist";
            }
        } else {
            $api_error_detected = 1;
            $message = "The action could not be completed because of this reason: $api_error_details";
        }

        $api_response_array = [
            'api_action_success' => $api_action_success,
            'api_error_detected' => $api_error_detected,
            'action_success' => $action_success,
            'error_detected' => $error_detected,
            'page_message' => $message,
        ];

        return json_encode($api_response_array);
    }
}
