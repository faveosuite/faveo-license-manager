<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;
use App\Models\AflClients;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

/**
 * Consist of functionalities for the License page in Auto Faveo licenser
 * Class LicenseController
 */
class LicenseController extends Controller
{
    public function __construct()
    {
        $this->ip_address = request()->server('REMOTE_ADDR');
    }

    /**
     * To Add license Details to the license manager via request or entering them, it can be added with client id or anonymously
     *
     * @param  LicenseRequest  $request
     * @param $api_key_secret
     * @param $product_id
     * @param $license_require_domain
     * @param $license_status
     * @param $client_id
     * @param $license_code
     * @param $license_order_number
     * @param $license_ip
     * @param $license_domain
     * @param $license_limit
     * @param $license_expire_date
     * @param $license_updates_date
     * @param $license_support_date
     * @param $license_comments
     * @return the details that has been added with a response
     */
    public function licenseAdd(LicenseRequest $request)
    {
        $api_action_success = 0;
        $api_error_detected = 0;
        $added_records = 0;

        $api_key_secret = $request->get('api_key_secret');
        $product_id = $request->get('product_id');
        $license_code = $request->get('license_code');
        $license_require_domain = $request->get('license_require_domain');
        $license_status = $request->get('license_status');
        $client_id = $request->get('client_id');
        $license_order_number = $request->get('license_order_number');
        $license_ip = $request->get('license_ip');
        $license_domain = $request->get('license_domain');
        $license_limit = $request->get('license_limit');
        $license_expire_date = $request->input('license_expire_date');
        $license_updates_date = $request->get('license_updates_date');
        $license_support_date = $request->get('license_support_date');
        $license_comments = $request->get('license_comments');

        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);

        if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($license_require_domain, 0, 1) && aflValidateIntegerValue($license_status, 0, 2)) {
            if (empty($client_id) || ! aflValidateIntegerValue($client_id)) { //in case no client_id was submitted, its value must be stored as NULL in database
                $client_id = null;
            }

            if (empty($license_code)) { //in case no license_code was submitted, its value must be stored as NULL in database
                $license_code = null;
            }
            $licenseChecks = $this->licenseChecks($client_id, $license_code, $license_ip, $license_domain, $license_limit, $license_expire_date, $license_updates_date, $license_support_date);
            if (! empty($licenseChecks)) {
                return $licenseChecks->content();
            }
            if ($api_error_detected != 1) {
                $license_date = date('Y-m-d');
                if (empty($license_envato) || ! aflValidateIntegerValue($license_envato)) {
                    $license_envato = 0;
                }

                if ($license_status == 1) {
                    $license_cancel_date = '0000-00-00';
                } else {
                    if (empty($license_cancel_date) || ! aflVerifyDateTime($license_cancel_date, 'Y-m-d')) { //set cancel date to now only if license is inactive and no previous cancel date set
                        $license_cancel_date = date('Y-m-d');
                    }
                }
                $license_expire_email_date = $license_expire_date;
                $license_updates_email_date = $license_updates_date;
                $license_support_email_date = $license_support_date;
                try {
                    DB::table('afl_licenses')
                                       ->insertOrIgnore([
                                           'client_id' => $client_id,
                                           'license_code' => $license_code,
                                           'product_id' => $product_id,
                                           'license_order_number' => $license_order_number,
                                           'license_ip' => $license_ip,
                                           'license_domain' => $license_domain,
                                           'license_require_domain' => $license_require_domain,
                                           'license_limit' => $license_limit,
                                           'license_date' => $license_date,
                                           'license_cancel_date' => $license_cancel_date,
                                           'license_expire_date' => $license_expire_date,
                                           'license_updates_date' => $license_updates_date,
                                           'license_support_date' => $license_support_date,
                                           'license_expire_email_date' => $license_expire_email_date,
                                           'license_updates_email_date' => $license_updates_email_date,
                                           'license_support_email_date' => $license_support_email_date,
                                           'license_comments' => $license_comments,
                                           'license_envato' => $license_envato,
                                           'license_status' => $license_status,
                                       ]);
                    $added_records += 1;
                    //doMysqlQuery("INSERT IGNORE INTO apl_licenses (client_id, license_code, product_id, license_order_number, license_ip, license_domain, license_require_domain, license_limit, license_date, license_cancel_date, license_expire_date, license_updates_date, license_support_date, license_comments, license_envato, license_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($client_id, $license_code, $product_id, $license_order_number, $license_ip, $license_domain, $license_require_domain, $license_limit, $license_date, $license_cancel_date, $license_expire_date, $license_updates_date, $license_support_date, $license_comments, $license_envato, $license_status), array("i", "s", "i", "s", "s", "s", "i", "i", "s", "s", "s", "s", "s", "s", "i", "i"));
                } catch (\Exception $e) {
                    $added_records += 0;
                }

                if (! aflValidateIntegerValue($added_records)) {
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.invalid_record_data'), 400);
                } else {
                    $action_success = 1;
                    $license_id = DB::getpdo()->lastInsertId();
                    if (aflValidateIntegerValue($license_id)) {
                        foreach ($rows_array = AflLicenses::leftJoin('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')
                                              ->leftJoin('users', 'afl_licenses.client_id', '=', 'users.client_id')
                                              ->where('afl_licenses.license_id', $license_id)
                                              ->get()->toArray() as $row) {
                            //fetchRow("SELECT * FROM apl_licenses LEFT JOIN apl_products ON apl_licenses.product_id=apl_products.product_id LEFT JOIN apl_clients ON apl_licenses.client_id=apl_clients.client_id WHERE apl_licenses.license_id=?", array($license_id), array("i")) as $row) //fetch product and client details to use in reports
                            extract((array) $row);
                        }
                        $client_formatted = formatClient($license_code, $client_email);

                        $api_response_array = ['api_action_success' => $api_action_success, 'api_error_detected' => $api_error_detected, 'action_success' => 1, 'error_detected' => 0, 'page_message' => $client_formatted]; //make array with response data

                        return successResponse(Lang::get('lang.adddd'), $client_formatted, 201);
                    }
                }
            }
        } else {
            return errorResponse(Lang::get('lang.invalid'), 400);
        }
    }

    /**
     * To Update license Details to the license manager via request or entering them, it can be added with client id or anonymously
     *
     * @param  LicenseRequest  $request
     * @param $api_key_secret
     * @param $product_id
     * @param $license_id
     * @param $license_require_domain
     * @param $license_status
     * @param $client_id
     * @param $license_code
     * @param $license_order_number
     * @param $license_ip
     * @param $license_domain
     * @param $license_limit
     * @param $license_expire_date
     * @param $license_updates_date
     * @param $license_support_date
     * @param $license_comments
     * @return the number of records that has been Updated with a response
     */
    public function licenseUpdate(Request $request)
    {
        $updated_records = 0;

        $api_key_secret = $request->get('api_key_secret');
        $license_id = $request->get('license_id');
        $product_id = $request->get('product_id');
        $license_require_domain = $request->get('license_require_domain');
        $license_status = $request->get('license_status');
        $client_id = $request->get('client_id');
        $license_code = $request->get('license_code');
        $license_order_number = $request->get('license_order_number');
        $license_ip = $request->get('license_ip');
        $license_domain = $request->get('license_domain');
        $license_limit = $request->get('license_limit');
        $license_expire_date = $request->get('license_expire_date');
        $license_updates_date = $request->get('license_updates_date');
        $license_support_date = $request->get('license_support_date');
        $license_comments = $request->get('license_comments');
        if (empty($license_id) || ! aflValidateIntegerValue($license_id) || empty($rows_array = AflLicenses::where('license_id', $license_id)->get())) {//invalid record
            return errorResponse(Lang::get('lang.license_id'), 400);
        }
        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);

        $optional_api_parameters_array = ['license_order_number', 'license_ip', 'license_domain', 'license_limit', 'license_expire_date', 'license_updates_date', 'license_support_date', 'license_comments']; //optional API parameters for this page
        foreach ($optional_api_parameters_array as $optional_api_parameter) { //in case some required parameter was not submitted, set its value empty to prevent "undefined variable" errors
            if (! isset($$optional_api_parameter)) {
                $$optional_api_parameter = '';
            }
        }
        if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($license_require_domain, 0, 1) && aflValidateIntegerValue($license_status, 0, 2) && $api_action_success == 1) {
            if (empty($client_id) || ! aflValidateIntegerValue($client_id)) { //in case no client_id was submitted, its value must be stored as NULL in database
                $client_id = null;
            }
            if (empty($license_code)) { //in case no license_code was submitted, its value must be stored as NULL in database
                $license_code = null;
            }
            $licenseChecks = $this->licenseChecks($client_id, $license_code, $license_ip, $license_domain, $license_limit, $license_expire_date, $license_updates_date, $license_support_date);
            if (! empty($licenseChecks)) {
                return errorResponse($licenseChecks->getOriginalContent()['message'], 400);
            }
            if ($api_action_success == 1) {
                if (! empty($license_expire_date) && aflVerifyDateTime($license_expire_date, 'Y-m-d') && $license_expire_date != $rows_array[0]['license_expire_date']) { //license_expire_date changed, reset license_expire_email_date, so client can receive new notification
                    $license_expire_email_date = '0000-00-00';
                } else {
                    $license_expire_email_date = $rows_array[0]['license_expire_email_date']; //use old license_expire_email_date
                }

                if (! empty($license_updates_date) && aflVerifyDateTime($license_updates_date, 'Y-m-d') && $license_updates_date != $rows_array[0]['license_updates_date']) { //license_updates_date changed, reset license_updates_email_date, so client can receive new notification
                    $license_updates_email_date = '0000-00-00';
                } else {
                    $license_updates_email_date = $rows_array[0]['license_updates_email_date']; //use old license_updates_email_date
                }

                if (! empty($license_support_date) && aflVerifyDateTime($license_support_date, 'Y-m-d') && $license_support_date != $rows_array[0]['license_support_date']) { //license_support_date changed, reset license_support_email_date, so client can receive new notification
                    $license_support_email_date = '0000-00-00';
                } else {
                    $license_support_email_date = $rows_array[0]['license_support_email_date']; //use old license_support_email_date
                }

                if (empty($license_envato) || ! aflValidateIntegerValue($license_envato)) {
                    $license_envato = 0;
                }
                if ($license_status == 1) {
                    $license_cancel_date = '0000-00-00';
                } else {
                    $license_cancel_date = $rows_array[0]['license_cancel_date']; //use old license_cancel_date if license was deactivated previously and its status wasn't changed now
                    if (empty($license_cancel_date) || ! aflVerifyDateTime($license_cancel_date, 'Y-m-d')) { //set cancel date to now only if no previous cancel date set
                        $license_cancel_date = date('Y-m-d');
                    }
                }
                $updated_records += AflLicenses::where('license_id', $license_id)
                                     ->update([
                                         'license_order_number' => $license_order_number,
                                         'license_ip' => $license_ip,
                                         'license_domain' => $license_domain,
                                         'license_require_domain' => $license_require_domain,
                                         'license_limit' => $license_limit,
                                         'license_cancel_date' => $license_cancel_date,
                                         'license_expire_date' => $license_expire_date,
                                         'license_expire_email_date' => $license_expire_date,
                                         'license_updates_date' => $license_updates_date,
                                         'license_updates_email_date' => $license_updates_email_date,
                                         'license_support_date' => $license_support_date,
                                         'license_support_email_date' => $license_support_email_date,
                                         'license_comments' => $license_comments,
                                         'license_envato' => $license_envato,
                                         'license_status' => $license_status,
                                     ]);
                //doMysqlQuery("UPDATE apl_licenses SET license_order_number=?, license_ip=?, license_domain=?, license_require_domain=?, license_limit=?, license_cancel_date=?, license_expire_date=?, license_expire_email_date=?, license_updates_date=?, license_updates_email_date=?, license_support_date=?, license_support_email_date=?, license_comments=?, license_envato=?, license_status=? WHERE license_id=?", array($license_order_number, $license_ip, $license_domain, $license_require_domain, $license_limit, $license_cancel_date, $license_expire_date, $license_expire_email_date, $license_updates_date, $license_updates_email_date, $license_support_date, $license_support_email_date, $license_comments, $license_envato, $license_status, $license_id), array("s", "s", "s", "i", "i", "s", "s", "s", "s", "s", "s", "s", "s", "i", "i", "i"));

                if (! aflValidateIntegerValue($updated_records)) {
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.invalid_record_data'), 400);
                } else {
                    $api_action_success = 1;
                    foreach ($rows_array = AflLicenses::leftJoin('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')
                                              ->leftJoin('users', 'afl_licenses.client_id', '=', 'users.client_id')
                                              ->where('afl_licenses.license_id', $license_id)
                                              ->get()->toArray() as $row) { //fetch product and client details to use in reports
                        extract((array) $row);
                    }

                    $client_formatted = formatClient($license_code, $client_email);

                    return successResponse(Lang::get('lang.license_Update'), $client_formatted, 200);
                }
            }
        } else {
            return errorResponse(Lang::get('lang.invalid'), 400);
        }
    }

    /**
     * To delete the license stored in the license manager
     *
     * @param $license_id
     * @return the removed records with a success response
     */
    public function deleteLicense(Request $request)
    {
        $removed_records = 0;
        $license_id = $request->get('license_id');
        $api_key_secret = $request->get('api_key_secret');
        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);
        if (aflValidateIntegerValue($license_id) && $api_action_success == 1) {
            $removed_records += AflLicenses::where('license_id', $license_id)->delete();
        }

        return successResponse(Lang::get('lang.delete'), $removed_records, 200);
    }

    public function show()
{
$licenses = AflLicenses::leftJoin('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')
->select('license_code', 'license_status', 'license_date', 'afl_products.product_title')
->withCount(['installations', 'callbacks'])
->with(['callbacks' => function ($query) {
$query->select('license_code', DB::raw('MAX(callback_date_time) as latest_callback_date_time'))
->groupBy('license_code');
}])->cursorPaginate(50000)->toArray();

$root_array = [];

foreach ($licenses['data'] as $license) {
$latest_license = AflLicenses::where('license_code', $license['license_code'])->orderByDesc('license_date')
->value('license_date');
$item_array = [
'license_code' => $license['license_code'],
'product_title' => $license['product_title'],
'license_status' => $license['license_status'],
'total_installations' => $license['installations_count'],
'latest_callbacks' => $license['callbacks'][0]['latest_callback_date_time'] ?? null,
'latest_license' => $latest_license,
'total_callbacks' => $license['callbacks_count'],
'license_status_formatted' => returnFormattedStatusArray($license['license_status'], 'Active', 'Inactive', 'Unknown')
];
$root_array[] = $item_array;
}
return successResponse(Lang::get('lang.License_show'), $root_array, 200);
}

    public function edit($license_id)
    {
        $license = AflLicenses::where('license_id', $license_id)->firstOrFail();
        $product_name = AflLicenses::join('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')->where('afl_licenses.license_id', $license_id)
            ->get(['afl_products.product_title', 'afl_licenses.product_id']);

        $client_name = AflClients::select(DB::raw('CONCAT(client_fname, " ", client_lname,"<",client_email,">") AS full_name'), 'users.client_id')
            ->join('afl_licenses', 'afl_licenses.client_id', '=', 'users.client_id')->where('afl_licenses.license_id', $license_id)
            ->get('full_name', 'users.client_id');

        if (! empty($license)) {
            return successResponse('', ['license' => $license, 'product_name' => $product_name, 'client_name' => $client_name], 200);
        }

        return errorResponse(Lang::get('lang.invalid'), 400);
    }

    /**
     * To Format the client
     *
     * @param $license_code
     * @param $client_email
     * return a formatted array of license code and client email
     */
    public function formatClient($license_code, $client_email)
    {
        if (! empty($license_code)) {
            $client_formatted = $license_code;
        } else {
            if (filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
                $client_formatted = $client_email;
            } else {
                $client_formatted = 'Unknown Client';
            }
        }

        return $client_formatted;
    }

    /***
     * Just performs some license checks while adding a license in faveo license manager.
     */
    protected function licenseChecks($client_id, $license_code, $license_ip, $license_domain, $license_limit, $license_expire_date, $license_updates_date, $license_support_date)
    {
        if (! aflValidateIntegerValue($client_id) && empty($license_code)) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.error_client_or_license_code'), 400);
        }

        if (aflValidateIntegerValue($client_id) && ! empty($license_code)) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_licnese'), 400);
        }

        if (! empty($license_ip)) {
            $license_ips_array = explode(',', $license_ip);
            foreach ($license_ips_array as $ip_to_validate) {
                if (! filter_var($ip_to_validate, FILTER_VALIDATE_IP)) {
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.invalid_license_ip'), 400);
                    break;
                }
            }
        }

        if (! empty($license_domain)) {
            $license_domain_array = explode(',', $license_domain);
            foreach ($license_domain_array as $license_domain_array_key => $license_domain_array_value) {
                if (! aflValidateRawDomain(aflGetRawDomain($license_domain_array_value)) || ! ctype_alnum(substr($license_domain_array_value, -1))) { //invalid TLD, scheme included, or last symbol is not alphanumeric (most likely ends with / or another non-alphanumeric character)
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.invalid_domain'), 400);
                }
            }
        }

        if (! empty($license_limit) && ! aflValidateIntegerValue($license_limit)) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_license_limit'), 400);
        }

        if (! empty($license_expire_date) && ! aflVerifyDateTime($license_expire_date, 'Y-m-d')) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_license_expiry'), 400);
        }

        if (! empty($license_updates_date) && ! aflVerifyDateTime($license_updates_date, 'Y-m-d')) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_license_update_date'), 400);
        }

        if (! empty($license_support_date) && ! aflVerifyDateTime($license_support_date, 'Y-m-d')) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_license_support_date'), 400);
        }
    }

    public function reissueLicenseCloud(Request $request){
        AflInstallations::where('license_code',$request->get('license_code'))->delete();
    }

    public function licenseDeactivate(Request $request){
        AflLicenses::where('license_code',$request->get('license_code'))->update(['license_status'=>0]);
    }
}
