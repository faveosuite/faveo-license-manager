<?php

namespace App\Http\Controllers\AflCallbacks;

use App\Http\Controllers\Controller;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use App\Models\AflSettings;
use App\Models\InstallationLogs;
use App\Models\LicensePlugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LicenseVerifyControllerV2 extends Controller
{
    protected $ip_address;

    protected $refer;
    protected const string CLOUD_IP = '138.197.237.160';

    public function __construct()
    {
        $this->ip_address = request()->boolean('is_cloud') ? self::CLOUD_IP : request()->ip();
        $this->refer = request()->server('HTTP_REFERER') ? request()->server('HTTP_REFERER') : request()->get('refer');
    }

    public function licenseVerify(Request $request)
    {
        $settings = AflSettings::first();

        if (!$settings) {
            return response()->json(['error' => __('lang.notification_unknown_error')], 500);
        }

        $input = $request->all();
        $client_email = $input['client_email'] ?? null;
        $license_code = $input['license_code'] ?? null;
        $root_url = $input['root_url'] ?? null;
        $installation_hash = $input['installation_hash'] ?? null;
        $license_signature = $input['license_signature'] ?? null;
        $client_id = $input['client_id'] ?? null;
        $isPlugin = $request['is_cloud'] ?? false;
        $pluginProductID = $request['product_id'] ?? null;

        // Sanitize client_id
        if (!aflValidateIntegerValue($client_id)) {
            $client_id = null;
        }

        $license = $this->findLicense($license_code, $client_email, $isPlugin, $pluginProductID);

        if (!$license) {
            return $this->handleLicenseNotFound(null, $root_url, $client_email, $license_code, $settings);
        }

        $product_id = $license->product_id;

        // Basic Validation
        if (!$this->validateRequest($product_id, $root_url, $client_email, $license_code, $installation_hash, $license_signature)) {
            return $this->handleInvalidRequest($request, $settings);
        }

        $installation_domain = getRootUrl("$root_url/", 1, 1, 0, 1);
        $product = AflProducts::where('product_id', $product_id)->first();

        if (!$product) {
            return $this->handleProductNotFound($product_id, $root_url, $client_email, $license_code, $settings);
        }

        if ($product->product_status != 1) {
            return $this->handleProductInactive($product, $root_url, $client_email, $license_code, $settings);
        }

        // License Validations
        $validationResult = $this->validateLicense($license, $license_signature, $product_id, $root_url, $client_email, $license_code, $installation_domain, $client_id);

        if ($validationResult['error_detected']) {
            return $this->handleLicenseError($validationResult, $product, $license, $root_url, $client_email, $license_code, $settings);
        }

        if ($license->license_status != 1) {
            $report_text = "$product->product_title license at $installation_domain ($this->ip_address) could not be verified because of this reason: License status is not active.";
            createLicenseReport($settings->SMART_REPORTS ?? 0, $product->product_id, $client_id, $license_code, $report_text, 0);
            return $this->sendResponse('', $root_url, $client_email, $license_code, $product, $license);
        }

        // Process Verification
        return $this->processVerification($license, $product, $root_url, $client_email, $license_code, $installation_domain, $installation_hash, $client_id, $settings);
    }

    private function validateRequest($product_id, $root_url, $client_email, $license_code, $installation_hash, $license_signature)
    {
        return filter_var($this->ip_address, FILTER_VALIDATE_IP) &&
            aflValidateIntegerValue($product_id) &&
            filter_var($root_url, FILTER_VALIDATE_URL) &&
            $root_url == $this->refer &&
            $installation_hash == hash('sha256', $root_url . $client_email . $license_code) &&
            !empty($license_signature) &&
            isValidLicenseRequest($license_code, $client_email);
    }

    private function findLicense($license_code, $client_email, $isPlugin = false, $pluginProductID = null)
    {
        $license = AflLicenses::when(
            $license_code,
            fn ($q) => $q->where('license_code', $license_code),
            fn ($q) => $q->join('users', 'afl_licenses.client_id', '=', 'users.client_id')
                ->where('users.client_email', $client_email)
                ->where('users.client_status', 1)
                ->select('afl_licenses.*')
        )->first();

        if ($license && $isPlugin && $pluginProductID) {
            $allowed = LicensePlugin::where([
                'license_id' => $license->license_id,
                'product_id' => $pluginProductID
            ])->exists();

            if ($allowed) {
                // attach plugin product id dynamically
                $license->product_id = $pluginProductID;
            }
        }

        return $license;
    }

    private function validateLicense($license, $license_signature, $product_id, $root_url, $client_email, $license_code, $installation_domain, $client_id)
    {
        $error_details = '';
        $notification_case = '';
        $error_detected = 0;

        if (!verifyScriptSignature($license_signature, $product_id, $root_url, $client_email, $license_code)) {
            $error_detected = 1;
            $error_details = 'invalid license signature';
            $notification_case = 'notification_invalid_signature';
        } elseif ($license->license_status === 0) {
            $error_detected = 1;
            $error_details = "license cancelled on $license->license_cancel_date";
            $notification_case = 'notification_license_cancelled';
        } elseif ($license->license_status == 2) {
            $error_detected = 1;
            $error_details = 'license suspended';
            $notification_case = 'notification_license_suspended';
        } elseif (aflVerifyDateTime($license->license_expire_date, 'Y-m-d') && $license->license_expire_date < date('Y-m-d')) {
            $error_detected = 1;
            $error_details = "license expired on $license->license_expire_date";
            $notification_case = 'notification_license_expired';
        } elseif (!empty($license->license_ip)) {
            $license_ips_array = explode(',', str_replace(' ', '', $license->license_ip));
            if (!in_array($this->ip_address, $license_ips_array)) {
                $error_detected = 1;
                $error_details = "IP address $this->ip_address is not licensed";
                $notification_case = 'notification_invalid_ip';
            }
        } elseif (!empty($license->license_domain)) {
            $license_domain_detected = 0;
            $license_domain_array = explode(',', str_replace(' ', '', $license->license_domain));
            foreach ($license_domain_array as $domain) {
                if (stristr(getRootUrl("$root_url/", 1, 1, 0, 1), $domain)) {
                    $license_domain_detected = 1;
                    break;
                }
            }
            if ($license_domain_detected != 1) {
                $error_detected = 1;
                $error_details = "domain $root_url is not licensed";
                $notification_case = 'notification_invalid_domain';
            }
        } elseif ($license->license_require_domain == 1) {
            if (!filter_var($root_url, FILTER_VALIDATE_URL) || !filter_var(gethostbyname(aflGetRawDomain($root_url)), FILTER_VALIDATE_IP) || filter_var(aflGetRawDomain($root_url), FILTER_VALIDATE_IP)) {
                $error_detected = 1;
                $error_details = 'a real domain is required';
                $notification_case = 'notification_domain_required';
            }
        }

        if ($error_detected == 0) {
            // Check installation ownership
            $existingInstallation = AflInstallations::where('product_id', $product_id)
                ->where('installation_ip', $this->ip_address)
                ->where('installation_domain', $installation_domain)
                ->first();

            if ($existingInstallation) {
                if ((!empty($license_code) && $license_code != $existingInstallation->license_code) || (aflValidateIntegerValue($client_id) && $client_id != $existingInstallation->client_id)) {
                    $error_detected = 1;
                    $error_details = "Installation on $installation_domain ($this->ip_address) belongs to another user";
                    $notification_case = 'notification_domain_in_use';
                }
            }

            // Verify only checks total count (no "reached" check — that is install-only)
            if ($license->license_limit != 0 && $error_detected == 0) {
                $all_installations_count = DB::table('afl_installations')->where('product_id', $product_id)
                    ->where(function ($query) use ($client_id, $license_code) {
                        $query->where('client_id', $client_id)
                            ->whereNotNull('client_id')
                            ->orWhere('license_code', $license_code);
                    })->count();

                if ($all_installations_count > $license->license_limit) {
                    $error_detected = 1;
                    $error_details = "maximum installations limit ($license->license_limit) exceeded";
                    $notification_case = 'notification_license_limit';
                }
            }
        }

        return [
            'error_detected' => $error_detected,
            'error_details' => $error_details,
            'notification_case' => $notification_case
        ];
    }

    private function processVerification($license, $product, $root_url, $client_email, $license_code, $installation_domain, $installation_hash, $client_id, $settings)
    {
        $action_success = 0;
        $notification_case = 'notification_installation_not_found';

        $existingInstallation = AflInstallations::where('product_id', $product->product_id)
            ->where(function ($query) use ($client_id, $license_code) {
                $query->where('client_id', $client_id)
                    ->orWhere('license_code', $license_code);
            })
            ->where(function ($query) {
                $query->where('installation_ip', $this->ip_address)
                    ->orWhere('installation_disable_ip_verification', 1);
            })
            ->where('installation_domain', $installation_domain)
            ->where('installation_hash', $installation_hash)
            ->where('installation_status', 1)
            ->first();

        if ($existingInstallation) {
            $action_success = 1;
            $notification_case = 'notification_license_ok';
            $report_text = "$product->product_title license at $installation_domain ($this->ip_address) verified.";
            // Always log successful verifications regardless of SMART_REPORTS setting
            $smart_reports = 1;
        } else {
            $report_text = "$product->product_title license at $installation_domain ($this->ip_address) could not be verified because of this reason: installation does not exist or is inactive.";
            $smart_reports = $settings->SMART_REPORTS ?? 0;
        }

        $this->createLicenseCallback($smart_reports, $product->product_id, $client_id, $license_code, $this->ip_address, $installation_domain, $action_success);
        $this->createInstallationLogs($installation_domain, $this->ip_address, $license_code);

        return $this->sendResponse($notification_case, $root_url, $client_email, $license_code, $product, $license);
    }

    private function handleInvalidRequest(Request $request, $settings)
    {
        $report_text = "Host $this->ip_address sent invalid data to requested_url and was rejected. Host sent this data: " . json_encode($request->all()) . '.';
        createLicenseReport($settings->SMART_REPORTS ?? 0, 0, null, null, $report_text, 0);
        recordFailedLicensing($settings->BANNED_HOSTS ?? 0, $settings->FAILED_LICENSINGS_LIMIT ?? 0, $this->ip_address);
        return errorResponse($report_text, 400);
    }

    private function handleProductNotFound($product_id, $root_url, $client_email, $license_code, $settings)
    {
        $report_text = "Unknown Product (ID: $product_id) license verification attempt at $root_url ($this->ip_address).";
        createLicenseReport($settings->SMART_REPORTS ?? 0, 0, null, $license_code, $report_text, 0);
        return $this->sendResponse('notification_product_not_found', $root_url, $client_email, $license_code, null, null);
    }

    private function handleProductInactive($product, $root_url, $client_email, $license_code, $settings)
    {
        $report_text = "$product->product_title license verification at $root_url ($this->ip_address) failed: product inactive.";
        createLicenseReport($settings->SMART_REPORTS ?? 0, $product->product_id, null, $license_code, $report_text, 0);
        return $this->sendResponse('notification_product_inactive', $root_url, $client_email, $license_code, $product, null);
    }

    private function handleLicenseNotFound($product, $root_url, $client_email, $license_code, $settings)
    {
        $product_title = $product ? $product->product_title : 'Unknown Product';
        $product_id = $product ? $product->product_id : 0;

        $report_text = "$product_title license verification at $root_url ($this->ip_address) failed: license not found.";
        createLicenseReport($settings->SMART_REPORTS ?? 0, $product_id, null, $license_code, $report_text, 0);
        return $this->sendResponse('notification_license_not_found', $root_url, $client_email, $license_code, $product, null);
    }

    private function handleLicenseError($validationResult, $product, $license, $root_url, $client_email, $license_code, $settings)
    {
        $report_text = "$product->product_title license verification at $root_url ($this->ip_address) failed: " . $validationResult['error_details'];
        createLicenseReport($settings->SMART_REPORTS ?? 0, $product->product_id, $license->client_id, $license_code, $report_text, 0);
        return $this->sendResponse($validationResult['notification_case'], $root_url, $client_email, $license_code, $product, $license);
    }

    private function sendResponse($notification_case, $root_url, $client_email, $license_code, $product, $license)
    {
        $client_fname = '';
        $client_lname = '';
        if ($license && $license->client_id) {
            $client = DB::table('users')->where('client_id', $license->client_id)->first();
            if ($client) {
                $client_fname = $client->client_fname;
                $client_lname = $client->client_lname;
            }
        }

        $product_id = $product ? $product->product_id : 0;
        $product_title = $product ? $product->product_title : '';
        $product_description = $product ? $product->product_description : '';
        $product_url_homepage = $product ? $product->product_url_homepage : '';
        $product_url_download = $product ? $product->product_url_download : '';
        $product_version = $product ? $product->product_version : '';

        $license_expire_date = $license ? $license->license_expire_date : '';
        $license_cancel_date = $license ? $license->license_cancel_date : '';
        $license_updates_date = $license ? $license->license_updates_date : '';
        $license_support_date = $license ? $license->license_support_date : '';
        $license_limit = $license ? $license->license_limit : '';

        return returnServerNotification(
            $notification_case,
            $root_url,
            $this->ip_address,
            $client_email,
            $client_fname,
            $client_lname,
            $license_code,
            $product_id,
            $product_title,
            $product_description,
            $product_url_homepage,
            $product_url_download,
            $product_version,
            $license_expire_date,
            $license_cancel_date,
            $license_updates_date,
            $license_support_date,
            $license_limit,
            '' // notification_data
        );
    }

    private function createLicenseCallback($smart_reports, $product_id, $client_id, $license_code, $callback_ip, $callback_domain, $callback_status)
    {
        $date_today = date('Y-m-d');
        $callback_date_time = date('Y-m-d H:i:s');

        $rows_array = [];
        if ($smart_reports == 1) {
            $rows_array = AflCallbacks::where('product_id', $product_id)
                ->where(function ($query) use ($client_id, $license_code) {
                    $query->where('client_id', $client_id)
                        ->whereNotNull('client_id')
                        ->orWhere('license_code', $license_code);
                })->where('callback_ip', $callback_ip)
                ->where('callback_domain', $callback_domain)
                ->whereRaw('callback_date_time BETWEEN ? AND ?', ["$date_today 00:00:00", "$date_today 23:59:59"])
                ->where('callback_status', $callback_status)
                ->get()->toArray();
        }

        if (empty($rows_array)) {
            DB::table('afl_callbacks')->insertOrIgnore([
                'product_id' => $product_id,
                'client_id' => $client_id,
                'license_code' => $license_code,
                'callback_ip' => $callback_ip,
                'callback_domain' => $callback_domain,
                'callback_date_time' => $callback_date_time,
                'callback_status' => $callback_status,
            ]);
        }
    }

    private function createInstallationLogs($installation_domain, $ipaddress, $license_code)
    {
        InstallationLogs::updateOrInsert(
            [
                'installation_domain' => $installation_domain,
                'license_code' => $license_code,
            ],
            [
                'installation_domain' => $installation_domain,
                'license_code' => $license_code,
                'installation_ip' => $ipaddress,
                'installation_status' => 1,
                'installation_last_active_date' => date('Y-m-d H:i:s'),
            ]
        );
    }
}
