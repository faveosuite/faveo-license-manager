<?php

namespace App\Http\Controllers\AflCallbacks;

use App\Http\Controllers\Controller;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;


class LicenseInstallController extends Controller
{
    public function __construct()
    {
        $this->ip_address = request()->server('REMOTE_ADDR');

        if (null !== (request()->server('HTTP_REFERER'))) {
            $this->refer = request()->server('HTTP_REFERER');
        } else {
            $this->refer = request()->get('refer');
        }
    }

    /**
     * This is used by script on user's machine to check if license is active and add installation details to Auto PHP Licenser database during installation of protected script
     * api format for example:  http://127.0.0.1:8000/api/licenseinstall?product_id=1&root_url=https://www.license.com&client_email=sandeshm40450@gamil.com&license_code=vvbdjvsbjbdvb&installation_hash=aa33da99a04490b20b01c40cced3d2981ca6e29e6fd30ccfd63961a83e798856&license_signature=e930f7623d746b7f81ad4b61af3b7ad1390358529799342eaa1736fa56696dba
     *
     * @return success response with a message
     * */
    public function licenseInstall(Request $request)
    {
        //Global $ROOT_URL;
        $action_success = 0; //will be changed to 1 later only if everything OK
        $error_detected = 0; //will be changed to 1 later if error occurs
        $error_details = ''; //will be filled with errors (if any)
        $added_records = 0;
        $updated_records = 0;
        $removed_records = 0;
        $notification_data = '';
        //get IP, refer and user agent

        //get script settings
        $settingDetails = extractDetailsOfSettings();
        extract((array) $settingDetails);
        // These are the data that needs to be passed to this function inorder to get a response
        //$root_ips_array=gethostbynamel(aflGetRawDomain($ROOT_URL));
        $product_id = $request->input('product_id');
        $client_email = $request->input('client_email');
        $license_code = $request->input('license_code');
        $root_url = $request->input('root_url');
        $installation_hash = $request->input('installation_hash');
        $license_signature = $request->input('license_signature');
        $client_id = $request->get('client_id');
        $client_fname = $request->get('client_fname');
        $client_lname = $request->get('client_lname');
        $is_cloud = $request->get('is_cloud');

        //check basic dat
        if (filter_var($this->ip_address, FILTER_VALIDATE_IP) && aflValidateIntegerValue($product_id) && filter_var($root_url, FILTER_VALIDATE_URL) && $root_url == $this->refer && $installation_hash == hash('sha256', $root_url.$client_email.$license_code) && ! empty($license_signature) && isValidLicenseRequest($license_code, $client_email) === true) {
            if ($is_cloud == true) {
                $this->ip_address = '138.197.237.160'; //This is the floating ip for the Load balancer since the ip of pods keep on changing.
            }
            $notification_case = '';
            $installation_domain = getRootUrl("$root_url/", 1, 1, 0, 1); //make url without scheme, www. and / at the end because this type of url is stored on server (add / at the end before processing because software stores root url without /)
            $client_formatted = formatClient($license_code, $client_email);

            $product_array = AflProducts::where('product_id', $product_id)->get()->toArray();
            //check if product exists, so it's possible to generate reports with product name even if product is inactive or license doesn't exist

            if (empty($product_array)) { //product doesn't exist
                $error_detected = 1;
                $error_details = setValue($error_details, 'product not found');
                $notification_case = setValue($notification_case, 'notification_product_not_found');

                $product_title = "Unknown Product (ID: $product_id)"; //make $product_title to use in reports for non-existing product
                $product_id = 0; //set $product_id to 0 for non-existing product, so this report will be displayed in Unknown Reports section
            } else { //product exists, do other checks
                foreach ($product_array as $row) { //fetch product details
                    extract((array) $row);
                }

                if ($product_status != 1) { //product inactive
                    $error_detected = 1;
                    $error_details = setValue($error_details, 'product inactive');
                    $notification_case = setValue($notification_case, 'notification_product_inactive');
                } else { //product active, do other checks
                    if (! empty($license_code)) { //search for code-based license
                        $license_array = AflLicenses::where('license_code', $license_code)
                                                  ->where('product_id', $product_id)->get()->toArray();
                    } else { //search for email-based license
                        $license_array = DB::table('afl_licenses')
                                            ->join('afl_clients', ' afl_licenses.client_id', '=', 'afl_clients.client_id')
                                            ->where('afl_clients.client_email', $client_email)
                                            ->where('afl_clients.client_status', 1)
                                            ->where('afl_licenses.product_id', $product_id)->get()->toArray();
                    }

                    if (empty($license_array)) { //license doesn't exist
                        $error_detected = 1;
                        $error_details = setValue($error_details, 'license not found');
                        $notification_case = setValue($notification_case, 'notification_license_not_found');
                    } else { //license exists, do other checks
                        foreach ($license_array as $row) { //fetch license details
                            extract((array) $row);
                        }
                        if (! verifyScriptSignature($license_signature, $product_id, $root_url, $client_email, $license_code)) { //invalid signature
                            $error_detected = 1;
                            $error_details = setValue($error_details, 'invalid license signature');
                            $notification_case = setValue($notification_case, 'notification_invalid_signature');
                        }

                        if ($license_status === 0) { //license cancelled
                            $error_detected = 1;
                            $error_details = setValue($error_details, "license cancelled on $license_cancel_date");
                            $notification_case = setValue($notification_case, 'notification_license_cancelled');
                        }

                        if ($license_status == 2) { //license suspended
                            $error_detected = 1;
                            $error_details = setValue($error_details, 'license suspended');
                            $notification_case = setValue($notification_case, 'notification_license_suspended');
                        }
                        if (aflVerifyDateTime($license_expire_date, 'Y-m-d') && $license_expire_date < date('Y-m-d')) { //license expired
                            $error_detected = 1;
                            $error_details = setValue($error_details, "license expired on $license_expire_date");
                            $notification_case = setValue($notification_case, 'notification_license_expired');
                        }

                        if (! empty($license_ip)) {
                            $license_ips_array = explode(',', str_replace(' ', '', $license_ip)); //remove all space symbols (if any) between IPs
                            if (! in_array($this->ip_address, $license_ips_array)) { //invalid IP
                                $error_detected = 1;
                                $error_details = setValue($error_details, "IP address $this->ip_address is not licensed");
                                $notification_case = setValue($notification_case, 'notification_invalid_ip');
                            }
                        }

                        if (! empty($license_domain)) {
                            $license_domain_detected = 0; //will be changed to 1 later only if everything OK
                            $license_domain_array = explode(',', str_replace(' ', '', $license_domain));
                            //remove all space symbols (if any) between domains
                            foreach ($license_domain_array as $license_domain_array_key => $license_domain_array_value) {
                                if (stristr(getRootUrl("$root_url/", 1, 1, 0, 1), $license_domain_array_value)) { //check if URL (where script is installed) matches one of allowed URLs (add / at the end before processing because software stores root url without /)
                                    $license_domain_detected = 1;
                                    break;
                                }
                            }

                            if ($license_domain_detected != 1) { //invalid domain
                                $error_detected = 1;
                                $error_details = setValue($error_details, "domain $root_url is not licensed");
                                $notification_case = setValue($notification_case, 'notification_invalid_domain');
                            }
                        }
                        if ($license_require_domain == 1) { //domain required
                            if (! filter_var($root_url, FILTER_VALIDATE_URL) || ! filter_var(gethostbyname(aflGetRawDomain($root_url)), FILTER_VALIDATE_IP) || filter_var(aflGetRawDomain($root_url), FILTER_VALIDATE_IP)) { //script uploaded on invalid domain, domain not resolving to any IP, or local (IP-based) address
                                $error_detected = 1;
                                $error_details = setValue($error_details, 'a real domain is required');
                                $notification_case = setValue($notification_case, 'notification_domain_required');
                            }
                        }

                        $this_installation_owner_array = AflInstallations::where('product_id', $product_id)
                                                                ->where('installation_ip', $this->ip_address)
                                                                ->where('installation_domain', $installation_domain)
                                                                ->get()->toArray();

                        if (! empty($this_installation_owner_array)) { //installation exists, check whom it belongs to
                            if (! empty($license_code) && $license_code != $this_installation_owner_array[0]['license_code'] || aflValidateIntegerValue($client_id) && $client_id != $this_installation_owner_array[0]['client_id']) { //this domain is used by another user
                                $error_detected = 1;
                                $error_details = setValue($error_details, "$product_title installation on $installation_domain ($this->ip_address) belongs to another user");
                                $notification_case = setValue($notification_case, 'notification_domain_in_use');
                            }
                        }
                        if ($license_limit != 0) { //check installations limit
                            $other_installations_array = DB::table('afl_installations')->where('product_id', $product_id)
                                                                ->where(function ($query) use ($license_code) {
                                                                    $query->where('license_code', $license_code);
                                                                })->where(function ($query) use ($installation_domain) {
                                                                    $query->where('installation_ip', '!=', $this->ip_address)
                                                                          ->orWhere('installation_domain', '!=', $installation_domain);
                                                                })->get()->toArray();
                            if (count($other_installations_array) >= $license_limit) { //client can't make new installation because it would exceed his current limit
                                $error_detected = 1;
                                $error_details = setValue($error_details, "maximum installations limit ($license_limit) reached");

                                $notification_case = setValue($notification_case, 'notification_license_limit');
                            }

                            $all_installations_array = DB::table('afl_installations')->where('product_id', $product_id)
                                                               ->where(function ($query) use ($client_id, $license_code) {
                                                                   $query->where('client_id', $client_id)
                                                                         ->whereNotNull('client_id')
                                                                         ->orWhere('license_code', $license_code);
                                                               })->get()->toArray();

                            if (count($all_installations_array) > $license_limit) { //client has more installations than he is allowed to (most likely limit was changed after installations were made)
                                $error_detected = 1;
                                $error_details = setValue($error_details, "maximum installations limit ($license_limit) exceeded");
                                $notification_case = setValue($notification_case, 'notification_license_limit');
                            }
                        }
                        /*if (validateDateTime($license_updates_date, "Y-m-d") && $license_updates_date<date("Y-m-d")) //updates expired - THIS PART SHOULD ONLY BE USED IN LICENSE_UPDATES.PHP FILE
                            {
                            //$error_detected=1; don't mark this as hard error, but fill error details, set notification_case and create new variable, so system will know about expiration, but won't mark whole report as failure just because of it
                            $error_details=setValue($error_details, "updates expired on $license_updates_date");
                            $notification_case=setValue($notification_case, "notification_updates_expired");
                            $updates_expired=1; //create new variable, so system will know about expiration
                            }*/

                        /*if (validateDateTime($license_support_date, "Y-m-d") && $license_support_date<date("Y-m-d")) //support expired - THIS PART SHOULD ONLY BE USED IN LICENSE_SUPPORT.PHP FILE
                            {
                            //$error_detected=1; don't mark this as hard error, but fill error details, set notification_case and create new variable, so system will know about expiration, but won't mark whole report as failure just because of it
                            $error_details=setValue($error_details, "support expired on $license_support_date");
                            $notification_case=setValue($notification_case, "notification_support_expired");
                            $support_expired=1; //create new variable, so system will know about expiration
                            }*/

                        if ($error_detected != 1) { //everything OK so far, do final checks
                            if ($license_status == 1) { //license active, add or update installation details in database
                                $installation_date = date('Y-m-d');
                                $installation_status = 1;

                                $this_installation_array = AflInstallations::where('product_id', $product_id)
                                                                        ->where(function ($query) use ($client_id, $license_code) {
                                                                            $query->where('client_id', $client_id)
                                                                                 ->orWhere('license_code', $license_code);
                                                                        })
                                                                       ->where('installation_ip', $this->ip_address)
                                                                       ->where('installation_domain', $installation_domain)
                                                                       ->where('installation_hash', $installation_hash)
                                                                       ->get()->toArray();
                                //always perform verification with IP, but without status, for new installations

                                if (! empty($this_installation_array)) { //this is existing installation, update its details in database
                                    $action_success = 1;
                                    $notification_case = setValue($notification_case, 'notification_license_ok');

                                    foreach ($this_installation_array as $row) {
                                        extract($row);
                                    }

                                    AflInstallations::where('installation_id', $installation_id)
                                                     ->update([
                                                         'installation_date' => $installation_date,
                                                         'installation_status' => $installation_status,
                                                         'installation_hash' => $installation_hash,
                                                     ]);
                        //don't check if $updated_records is more than 0 because when installation_date variable is the same (script re-installed Nth time same day), $updated_records will be 0
                                } else { //this is new installation, add its details to database
                                    try {
                                        $api = DB::table('afl_installations')->insertOrIgnore([
                                            'client_id' => $client_id,
                                            'license_code' => $license_code,
                                            'product_id' => $product_id,
                                            'installation_ip' => $this->ip_address,
                                            'installation_domain' => $installation_domain,
                                            'installation_date' => $installation_date,
                                            'installation_status' => $installation_status,
                                            'installation_hash' => $installation_hash,
                                        ]);

                                        $action_success = 1;
                                        $notification_case = setValue($notification_case, 'notification_license_ok');
                                    } catch (\Exception $e) {
                                        $error_detected = 1;
                                        $error_details = setValue($error_details, 'unknown error occurred');
                                        $notification_case = setValue($notification_case, 'notification_unknown_error');
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $required_callback_parameters_array = ['client_id', 'client_fname', 'client_lname', 'product_id', 'product_title', 'product_description', 'product_url_homepage', 'product_url_download', 'product_version', 'license_code', 'license_expire_date', 'license_cancel_date', 'license_updates_date', 'license_support_date', 'license_limit', 'notification_case', 'notification_data']; //required callback parameters for this page4
            foreach ($required_callback_parameters_array as $required_callback_parameter) { //in case some required parameter (used in callback and/or notification functions) was not fetched, set its value empty to prevent "undefined variable" errors
                if (! isset($$required_callback_parameter)) {
                    $$required_callback_parameter = '';
                }
            }

            if (! aflValidateIntegerValue($client_id)) { //empty client_id must be null, not empty string
                $client_id = null;
            }

            if ($action_success == 1) { //everything OK
                if (! empty($this_installation_array)) { //generate different report for existing installation
                    $report_text = "Existing $product_title installation at $installation_domain ($this->ip_address) updated.";
                } else {
                    $report_text = "New $product_title installation at $installation_domain ($this->ip_address) performed.";
                }
            } else {
                $report_text = "$product_title installation at $installation_domain ($this->ip_address) could not be performed because of this reason: $error_details.";
            }

            return returnServerNotification($notification_case, $root_url, $this->ip_address, $client_email, $client_fname, $client_lname, $license_code, $product_id, $product_title, $product_description, $product_url_homepage, $product_url_download, $product_version, $license_expire_date, $license_cancel_date, $license_updates_date, $license_support_date, $license_limit, $notification_data);  //always return server notification when valid basic data was received from script
    //return successResponse(Lang::get('lang.success'),$api,201);
        } else { //possible cracking attempt, set variables required for reports function to null and generate cracking report
            $product_id = 0;
            $client_id = null;
            $license_code = null;
            $report_text = "Host $this->ip_address sent invalid data to requested_url and was rejected. Host sent this data: ".json_encode($request->all()).'.';
        }
        createLicenseReport($SMART_REPORTS, $product_id, $client_id, $license_code, $report_text, $action_success); //always create report, no matter result
        if ($action_success != 1) { //record failed licensing attempt and ban host if needed
            recordFailedLicensing($BANNED_HOSTS, $FAILED_LICENSINGS_LIMIT, $this->ip_address);
        }
    }

    public function reissueLicenseCloud(Request $request){
        AflInstallations::where('license_code',$request->get('license_code'))->delete();
    }
}
