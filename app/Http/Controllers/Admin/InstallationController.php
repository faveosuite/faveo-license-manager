<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstallationRequest;
use App\Models\AflApiKeys;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\LicensePlugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use function Laravel\Prompts\select;

/**
 * Consist of functionalities for the Installation page in Auto Faveo licenser
 * Class InstallationController
 */
class InstallationController extends Controller
{
    public function __construct()
    {
        $this->ip_address = request()->server('REMOTE_ADDR');
    }

    /**
     * To Update intallation details in license manager
     *
     * @param  InstallationRequest  $request
     * @param $api_key_secret
     * @param $installation_id
     * @param $installation_ip
     * @param $installation_status
     * @param $installation_disable_ip
     * @return success response if the record was found and updated
     */
    public function installationUpdate(Request $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'installation_id' => $request->get('installation_id'),
            'installation_ip' => $request->get('installation_ip'),
            'installation_status' => $request->get('installation_status'),
            'installation_disable_ip' => $request->get('installation_disable_ip'),
            'delete_record' => $request->get('delete_record'),
        ];

        $result = $this->processInstallationUpdate($data, $this->ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code']);
        }

        return json_encode([
            'api_action_success' => 1,
            'api_error_detected' => 0,
            'action_success' => 1,
            'error_detected' => 0,
            'page_message' => $result['data'],
        ]);
    }

    public function processInstallationUpdate(array $data, string $ipAddress = ''): array
    {
        $action_success = 0;
        $error_detected = 0;
        $error_details = '';
        $updated_records = 0;
        $removed_records = 0;
        $logged_admin_id = 0;

        $api_key_secret = $data['api_key_secret'] ?? null;
        $installation_id = $data['installation_id'] ?? null;
        $installation_ip = $data['installation_ip'] ?? null;
        $installation_status = $data['installation_status'] ?? null;
        $installation_disable_ip = $data['installation_disable_ip'] ?? null;
        $delete_record = $data['delete_record'] ?? null;

        if (empty($installation_id) || ! aflValidateIntegerValue($installation_id) || empty($rows_array = AflInstallations::where('installation_id', $installation_id)->get())) { //invalid record
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
        }

        $api_action_success = 1;
        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_action_success = $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if ($api_action_success != 1) {
            return ['success' => false, 'message' => 'API key check failed', 'data' => [], 'status_code' => 400];
        }

        if (! empty($delete_record) && $delete_record == 1) {
            $removed_records += $this->deleteInstallation($installation_id);
            if ($removed_records > 0) {
                $page_message = "Deleted $removed_records installation(s).";
                createReport(strip_tags($page_message), $logged_admin_id, 1, 1);

                return ['success' => true, 'data' => $page_message, 'message' => ''];
            } else {
                $error_detected = 1;
                $error_details .= 'Invalid record or database error.';
            }
        }

        if (filter_var($installation_ip, FILTER_VALIDATE_IP) && aflValidateIntegerValue($installation_status, 0, 2)) {
            if ($error_detected != 1) {
                $updated_records += AflInstallations::where('installation_id', $installation_id)
                                 ->update([
                                     'installation_ip' => $installation_ip,
                                     'installation_disable_ip_verification' => $installation_disable_ip,
                                     'installation_status' => $installation_status,
                                 ]);
                if (! aflValidateIntegerValue($updated_records)) {
                    $error_detected = 1;
                    $error_details .= 'Invalid record details, duplicated data, or database error.';
                } else {
                    $action_success = 1;
                    $product_title = '';
                    $installation_domain = '';
                    $rows_array = AflInstallations::leftJoin('afl_products', 'afl_installations.product_id', '=', 'afl_products.product_id')
                                          ->where('afl_installations.installation_id', $installation_id)
                                          ->get()->toArray();
                    foreach ($rows_array as $row) { //fetch product details to use in reports
                        extract($row);
                    }
                }
            }
        } else {
            $error_detected = 1;
            $error_details .= 'Invalid IP address or status.';
        }

        if ($action_success == 1) {
            $page_message = "$product_title installation on $installation_domain ($installation_ip) updated.";
        } else {
            $page_message = "Installation could not be updated because of this reason: $error_details";
        }

        createReport(strip_tags($page_message), $logged_admin_id, 1, $action_success);

        if ($action_success != 1) {
            return ['success' => false, 'message' => $page_message, 'data' => [], 'status_code' => 400];
        }

        return ['success' => true, 'data' => $page_message, 'message' => ''];
    }

    /**
     * To Delete intallation details in license manager
     *
     * @param $installation_id
     * @return success response if the record was found and deleted
     */
    private function deleteInstallation($installation_id)
    {
        $removed_records = 0;

        // Fetch license code associated with the given installation ID
        $licenseCode = AflInstallations::where('installation_id', $installation_id)->value('license_code');

        // Fetch license ID associated with the license code
        $licenseId = AflLicenses::where('license_code', $licenseCode)->value('license_id');

        // Fetch plugin IDs associated with the license ID
        $pluginIds = LicensePlugin::where('license_id', $licenseId)->pluck('product_id');

        // Fetch all installation IDs associated with the plugin IDs
        $relatedInstallationIds = AflInstallations::where('license_code', $licenseCode)
            ->whereIn('product_id', $pluginIds)
            ->pluck('installation_id');

        // Merge the provided installation ID with the related ones
        $installationIdsToDelete = collect($relatedInstallationIds)->push($installation_id)->unique();

        // Validate and delete all installations in the merged list
        $removed_records += AflInstallations::whereIn('installation_id', $installationIdsToDelete)->delete();

        return $removed_records;
    }


    /**
     * Returns the list of all the instalaltions using license manager
     */
    public function show(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'installation_id');

        $installations = AflInstallations::with('product:product_id,product_title','clients:client_id,client_email','license:license_id,license_code')
            ->withAggregate(['product as product_title'], 'product_title')
            ->withAggregate(['clients as client_email'], 'client_email')
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->whereHas('clients', function ($query) use ($searchQuery) {
                        $query->where('client_email', 'like', '%' . $searchQuery . '%');
                    })
                        ->orWhereHas('product', function ($query) use ($searchQuery) {
                            $query->where('product_title', 'like', '%' . $searchQuery . '%');
                        })
                        ->orWhere('license_code', 'like', '%' . str_replace("-", "", $searchQuery) . '%')
                        ->orWhere('installation_ip', 'like', '%' . $searchQuery . '%')
                        ->orWhere('installation_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%')
                        ->orWhere('installation_domain', 'like', '%' . $searchQuery . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.Install_show'), $installations);
    }


    //for localized license only
    public function installationAdd(Request $request)
    {
        $license_code = $request->get('license_code');
        $product_id = $request->get('product_id');
        $installation_domain = $request->get('installation_domain');
        $installation_date = $request->get('installation_date');
        $installation_status = $request->get('installation_status');
        $installation_hash = $request->get('installation_hash');
        $api_key_secret = $request->get('api_key_secret');
        $api_action_success = 0;
        $api_error_detected = 0;

        if (null !== (request()->server('REMOTE_ADDR'))) {
            $ip_address = request()->server('REMOTE_ADDR');
        } else {
            $ip_address = $request->ip();
        }

        if (! empty($api_key_secret)) {
            $api = AflApiKeys::where('api_key_secret', $api_key_secret)->where('api_key_status', 1)->get();
            if (empty($api)) {
                return errorResponse(Lang::get('lang.invalid_api_key'), 404);
            } else {
                $api_ip = new AflApiKeys();
                $api_ips = $api_ip->value('api_key_ip');

                if (! empty($api_ips)) {
                    if (! $api_ips->contains($ip_address)) {
                        $api_error_detected = 1;

                        return errorResponse(Lang::get('lang.Api_Acess_not_allowed'), 400);
                    } else {
                        $api_action_success = 1;
                    }
                } else {
                    $api_action_success = 1;
                }
            }
            if ($api_action_success == 1) {
                $api = DB::table('afl_installations')->insertOrIgnore([
                    'license_code' => $license_code,
                    'product_id' => $product_id,
                    'installation_ip' => $ip_address,
                    'installation_domain' => $installation_domain,
                    'installation_date' => $installation_date,
                    'installation_status' => $installation_status,
                    'installation_hash' => $installation_hash,
                ]);

                return successResponse(Lang::get('lang.install_added'), $api, 200);
            }
        }
    }

    public function edit($installation_id)
    {
        $installation = AflInstallations::where('installation_id', $installation_id)->firstOrFail();

        if (! empty($installation)) {
            return successResponse('', ['installation' => $installation], 200);
        }

        return errorResponse(Lang::get('lang.invalid'), 400);
    }


    public function removeUnwantedInstallations(Request $request){
        return $this->removeInstallation($request->installation_path);
    }

    public function removeInstallation(string $installationPath)
    {
        return AflInstallations::where('installation_domain',$installationPath)->delete();
    }

    public function updateTheLicenseCode(Request $request){
        return AflInstallations::where('license_code',$request->old_license_code)
            ->delete();
    }

    public function deleteInstallations(Request $request)
    {
        $removed_records = 0;
        $installation_id = $request->input('installation_id');
        if (aflValidateIntegerValue($installation_id)) {
            $removed_records += AflInstallations::where('installation_id', $installation_id)->delete();
        }

        return successResponse(Lang::get('lang.installation_delete'), $removed_records);
    }
}
