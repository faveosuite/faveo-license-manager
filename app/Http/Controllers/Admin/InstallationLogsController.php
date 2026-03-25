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
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'license_code' => $request->get('license_code'),
        ];

        $result = $this->processGetInstallationLogs($data, $this->ip_address);

        if (! $result['success']) {
            return json_encode([
                'api_action_success' => 0, 'api_error_detected' => 1,
                'action_success' => 0, 'error_detected' => 0,
                'page_message' => $result['message'],
            ]);
        }

        return json_encode([
            'api_action_success' => 1, 'api_error_detected' => 0,
            'action_success' => 1, 'error_detected' => 0,
            'page_message' => $result['data'],
        ]);
    }

    public function processGetInstallationLogs(array $data, string $ipAddress = ''): array
    {
        $apiKeySecret = $data['api_key_secret'] ?? null;
        $licenseCode = $data['license_code'] ?? null;

        if ($apiKeySecret) {
            $apiKey = new ApiKeysController();
            if (! $apiKey->apiKeyCheck($apiKeySecret, $ipAddress)) {
                return ['success' => false, 'data' => [], 'message' => 'API key check failed'];
            }
        }

        $logs = InstallationLogs::where('license_code', $licenseCode)
            ->orderBy('installation_last_active_date', 'desc')
            ->get()->toArray();

        return ['success' => true, 'data' => $logs, 'message' => ''];
    }

    public function updateInstallationLogs(Request $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'root_url' => $request->get('root_url'),
            'version_number' => $request->get('version_number'),
            'installation_ip' => $request->get('installation_ip'),
            'license_code' => $request->get('license_code'),
        ];

        $result = $this->processUpdateInstallationLogs($data, $this->ip_address);

        if (! $result['success']) {
            return json_encode([
                'api_action_success' => 0, 'api_error_detected' => 1,
                'action_success' => 0, 'error_detected' => 1,
                'page_message' => $result['message'],
            ]);
        }

        return json_encode([
            'api_action_success' => 1, 'api_error_detected' => 0,
            'action_success' => 1, 'error_detected' => 0,
            'page_message' => $result['data'],
        ]);
    }

    public function processUpdateInstallationLogs(array $data, string $ipAddress = ''): array
    {
        $apiKeySecret = $data['api_key_secret'] ?? null;
        $rootUrl = $data['root_url'] ?? '';
        $versionNumber = $data['version_number'] ?? null;
        $installationIp = $data['installation_ip'] ?? null;
        $licenseCode = $data['license_code'] ?? null;

        if ($apiKeySecret) {
            $apiKey = new ApiKeysController();
            if (! filter_var($ipAddress, FILTER_VALIDATE_IP) || ! $apiKey->apiKeyCheck($apiKeySecret, $ipAddress)) {
                return ['success' => false, 'data' => [], 'message' => 'API key check failed'];
            }
        }

        if (empty($rootUrl)) {
            return ['success' => false, 'data' => [], 'message' => 'Root URL is required'];
        }

        $installationDomain = getRootUrl($rootUrl, 1, 1, 0, 1);

        if (! $installationDomain) {
            return ['success' => false, 'data' => [], 'message' => 'Installation does not exist'];
        }

        InstallationLogs::updateOrCreate(
            [
                'installation_domain' => $installationDomain,
                'license_code' => $licenseCode,
            ],
            [
                'version_number' => $versionNumber,
                'installation_ip' => $installationIp,
                'installation_status' => 1,
                'installation_last_active_date' => date('Y-m-d H:i:s'),
            ]
        );

        return ['success' => true, 'data' => 'Installation Logs updated successfully', 'message' => ''];
    }
}
