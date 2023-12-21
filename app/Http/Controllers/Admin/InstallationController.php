<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstallationRequest;
use App\Models\AflApiKeys;
use App\Models\AflInstallations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

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
        $action_success = 0; //will be changed to 1 later only if everything OK
        $error_detected = 0; //will be changed to 1 later if error occurs
        $error_details = ''; //will be filled with errors (if any)
        $updated_records = 0;
        $removed_records = 0;
        $api_error_detected = 0;
        $api_error_details = '';
        $logged_admin_id = 0;
        $api_key_secret = $request->get('api_key_secret');
        $installation_id = $request->get('installation_id');
        $installation_ip = $request->get('installation_ip');
        $installation_status = $request->get('installation_status');
        $installation_disable_ip = $request->get('installation_disable_ip');
        $delete_record = $request->get('delete_record');

        if (empty($installation_id) || ! aflValidateIntegerValue($installation_id) || empty($rows_array = AflInstallations::where('installation_id', $installation_id)->get())) { //invalid record
            return errorResponse(Lang::get('lang.invalid'), 400);
        }
        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);
        if ($api_action_success == 1) { //API check OK, continue with actual request
            $optional_api_parameters_array = ['installation_disable_ip_verification']; //optional API parameters for this page
            foreach ($optional_api_parameters_array as $optional_api_parameter) { //in case some required parameter was not submitted, set its value empty to prevent "undefined variable" errors
                if (! isset($$optional_api_parameter)) {
                    $$optional_api_parameter = '';
                }
            }
            if (! empty($delete_record) && $delete_record == 1) {
                $removed_records += $this->deleteInstallation($installation_id);
                if ($removed_records > 0) {
                    $action_success = 1;
                    $page_message = "Deleted $removed_records installation(s).";
                    createReport(strip_tags($page_message), $logged_admin_id, 1, $action_success);

                    return $page_message; //THIS LINE IS CUSTOM IN API. ADMINISTRATION DASHBOARD CODE CONTAINS redirectInvalidRecord($script_name);
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

            if ($action_success == 1) { //everything OK
                $page_message = "$product_title installation on $installation_domain ($installation_ip) updated.";
            } else { //display error message
                $page_message = "Installation could not be updated because of this reason: $error_details";
            }

            createReport(strip_tags($page_message), $logged_admin_id, 1, $action_success);
        } else { //display error message
            $page_message = "The action could not be completed because of this reason: $api_error_details";
        }
        $api_response_array = ['api_action_success' => $api_action_success, 'api_error_detected' => $api_error_detected, 'action_success' => $action_success, 'error_detected' => $error_detected, 'page_message' => $page_message]; //make array with response data

        return json_encode($api_response_array);
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
        if (aflValidateIntegerValue($installation_id)) {
            $removed_records += AflInstallations::where('installation_id', $installation_id)->delete();
        }

        return $removed_records;
    }

    /**
     * Returns the list of all the instalaltions using license manager
     */
    public function show()
    {
    $root_array = AflInstallations::leftJoin('afl_products', 'afl_installations.product_id', '=', 'afl_products.product_id')
    ->orderByDesc('installation_date')
    ->orderByDesc('installation_id')
    ->select('installation_id','afl_products.product_id', 'afl_products.product_title', 'license_code', 'installation_status',
    DB::raw('(SELECT COUNT(*) FROM afl_installations AS sub_installations WHERE sub_installations.license_code = afl_installations.license_code) as total_installations'),
    DB::raw('(SELECT MAX(installation_date) FROM afl_installations AS sub_installations WHERE sub_installations.license_code = afl_installations.license_code) as latest_installation_date')
)
    ->cursor()
    ->toArray();

    return successResponse(Lang::get('lang.Install_show'), $root_array, 200);
    }

    //for localized license only
    public function installationAdd(Request $request)
    {
        $license_code = $request->get('license_code');
        $product_id = $request->get('product_id');
        $installation_domain = $request->get('installation_domain');
        $installation_date = $request->get('installation_date');
        $installation_status = $request->get('installation_status');
        $installation_hash = $request->get('installation_status');
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
        return AflInstallations::where('installation_domain',$request->installation_path)->delete();
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
