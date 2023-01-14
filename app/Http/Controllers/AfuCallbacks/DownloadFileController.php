<?php

namespace App\Http\Controllers\AfuCallbacks;

use App\Http\Controllers\Controller;
use App\Models\AfuProducts;
use App\Models\AfuVersions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DownloadFileController extends Controller
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
     * Download api which is called from faveo
     * sends the response back to faveo in from of headers and attachment of the file if all checks pass.
     */
    public function downloadFile(Request $request)
    {
        $action_success = 0; //will be changed to 1 later only if everything OK
        $error_detected = 0; //will be changed to 1 later if error occurs
        $error_details = ''; //will be filled with errors (if any)

        //set supported download files
        $SUPPORTED_DOWNLOADS_ARRAY = ['version_install_file', 'version_install_query', 'version_upgrade_file', 'version_upgrade_query'];
        //get IP, refer and user agent
        $settingDetails = extractDetailsOfSettings();
        extract((array) $settingDetails);
        $product_id = $request->get('product_id');
        $product_key = $request->get('product_key');
        $version_number = $request->get('version_number');
        $user_local_path = $request->get('user_local_path');
        $script_signature = $request->get('script_signature');
        $file_type = $request->get('file_type');
        foreach ($rows_array = DB::table('directory')->where('id', 1)->get()->toArray() as $row) {
            extract((array) $row);
        }
        $path = storage_path();
        define('SCRIPT_ROOT_DIRECTORY', __DIR__);
        define('ARCHIVES_DIRECTORY', $ARCHIVES_DIRECTORY);
        define('QUERIES_DIRECTORY', $QUERIES_DIRECTORY);
        //check basic data

        if (filter_var($this->ip_address, FILTER_VALIDATE_IP) && aflValidateIntegerValue($product_id) && ! empty($product_key) && ! empty($user_local_path) && ! empty($script_signature)) {
            $notification_case = '';
            $product_array = [];
            $version_array = [];
            $elements_to_unset_array = ['product_key', 'version_install_file', 'version_install_query', 'version_raw_install_query', 'version_upgrade_file', 'version_upgrade_query', 'version_raw_upgrade_query', 'version_install_count', 'version_upgrade_count', 'version_comments']; //elements to be removed from final array (notification_data) because of security or other reasons for this page
            $product_array = AfuProducts::where('product_id', $product_id)->where('product_key', $product_key)->get()->toArray();
            //check if product exists, so it's possible to generate reports with product name even if product is inactive or version doesn't exist
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
                    if (! empty($version_number)) { //get specified version
                        $version_array = AfuVersions::where('product_id', $product_id)->where('version_number', $version_number)->get()->toArray();
                    } else { //version not specified, get latest active one
                        $version_array = AfuVersions::where('product_id', $product_id)->where('version_status', 1)->orderBy('version_id', 'desc')->limit(1)->get()->toArray();
                    }

                    if (empty($version_array)) { //version doesn't exist
                        $error_detected = 1;

                        if (! empty($version_number)) { //version was specified, display message that specified version not found
                            $error_details = setValue($error_details, 'version not found or is inactive');
                            $notification_case = setValue($notification_case, 'notification_version_not_found');
                        } else { //no version was specified, display message that product has no versions
                            $error_details = setValue($error_details, 'product has no versions');
                            $notification_case = setValue($notification_case, 'notification_product_no_versions');
                        }
                    } else { //version exists, do other checks
                        foreach ($version_array as $row) { //fetch version details
                            extract((array) $row);
                        }

                        if (! afuVerifyScriptSignature($ROOT_URL, $script_signature, $product_id, $product_key)) { //invalid signature
                            $error_detected = 1;
                            $error_details = setValue($error_details, 'invalid script signature');
                            $notification_case = setValue($notification_case, 'notification_invalid_signature');
                        }

                        if ($version_status != 1) { //version inactive
                            $error_detected = 1;
                            $error_details = setValue($error_details, 'version inactive');
                            $notification_case = setValue($notification_case, 'notification_version_inactive');
                        }

                        if (aflVerifyDateTime($version_expire_date, 'Y-m-d') && $version_expire_date < date('Y-m-d')) { //version expired
                            $error_detected = 1;
                            $error_details = setValue($error_details, "version expired on $version_expire_date");
                            $notification_case = setValue($notification_case, 'notification_version_expired');
                        }

                        if (empty($file_type) || ! in_array($file_type, $SUPPORTED_DOWNLOADS_ARRAY)) { //invalid download type
                            $error_detected = 1;
                            $error_details = setValue($error_details, "invalid download type $file_type");
                            $notification_case = setValue($notification_case, 'notification_invalid_parameter');
                        }

                        if ($file_type == 'version_install_file') {
                            $file_to_download_path = ARCHIVES_DIRECTORY;
                            $file_to_download_name = $version_install_file;

                            if (! is_file("$file_to_download_path/$file_to_download_name")) { //file doesn't exist
                                $error_detected = 1;
                                $error_details = setValue($error_details, "installation archive $file_to_download_name not found");
                                $notification_case = setValue($notification_case, 'notification_install_archive_not_found');
                            }

                            if (aflValidateIntegerValue($version_install_limit) && $version_install_count >= $version_install_limit) { //installations limit reached
                                $error_detected = 1;
                                $error_details = setValue($error_details, "installations limit $version_install_limit reached");
                                $notification_case = setValue($notification_case, 'notification_install_limit_reached');
                            }

                            $file_to_download_suffix = 'installation-archive';
                            $callback_type = 2; //callback_type set to 2 - "installation"
                            $file_to_download_title_reports = 'installation archive'; //used in reports;
                            $version_install_count++; //increase installations count
                        }

                        if ($file_type == 'version_install_query') {
                            $file_to_download_path = QUERIES_DIRECTORY;
                            $file_to_download_name = $version_install_query;

                            if (! is_file("$file_to_download_path/$file_to_download_name")) { //file doesn't exist
                                $error_detected = 1;
                                $error_details = setValue($error_details, "installation query $file_to_download_name not found");
                                $notification_case = setValue($notification_case, 'notification_install_query_not_found');
                            }

                            $file_to_download_suffix = 'installation-query';
                            $callback_type = 2; //callback_type set to 2 - "installation"
                            $file_to_download_title_reports = 'installation query'; //used in reports;
                        }
                        if ($file_type == 'version_upgrade_file') {
                            $file_to_download_path = ARCHIVES_DIRECTORY;
                            $file_to_download_name = $version_upgrade_file;
                            if (! is_file("$file_to_download_path".DIRECTORY_SEPARATOR."$file_to_download_name")) { //file doesn't exist
                                $error_detected = 1;
                                $error_details = setValue($error_details, "upgrade archive $file_to_download_name not found");
                                $notification_case = setValue($notification_case, 'notification_upgrade_archive_not_found');
                            }

                            if (aflValidateIntegerValue($version_upgrade_limit) && $version_upgrade_count >= $version_upgrade_limit) { //upgrades limit reached
                                $error_detected = 1;
                                $error_details = setValue($error_details, "upgrades limit $version_upgrade_limit reached");
                                $notification_case = setValue($notification_case, 'notification_upgrade_limit_reached');
                            }

                            $file_to_download_suffix = 'upgrade-archive';
                            $callback_type = 3; //callback_type set to 3 - "upgrade"
                            $file_to_download_title_reports = 'upgrade archive'; //used in reports;
                            $version_upgrade_count++; //increase upgrades count
                        }

                        if ($file_type == 'version_upgrade_query') {
                            $file_to_download_path = QUERIES_DIRECTORY;
                            $file_to_download_name = $version_upgrade_query;

                            if (! is_file("$file_to_download_path/$file_to_download_name")) { //file doesn't exist
                                $error_detected = 1;
                                $error_details = setValue($error_details, "upgrade query $file_to_download_name not found");
                                $notification_case = setValue($notification_case, 'notification_upgrade_query_not_found');
                            }

                            $file_to_download_suffix = 'upgrade-query';
                            $callback_type = 3; //callback_type set to 3 - "upgrade"
                            $file_to_download_title_reports = 'upgrade query'; //used in reports;
                        }

                        if ($VERIFIED_UPDATES == 1 && $callback_type == 3 && ! aflValidateIntegerValue($this->verifyInstallation($product_id, $this->ip_address, $user_local_path))) { //only verified updates allowed and this installation is not verified
                            $error_detected = 1;
                            $error_details = setValue($error_details, 'installation not verified');
                            $notification_case = setValue($notification_case, 'notification_installation_not_verified');
                        }

                        if ($error_detected != 1) {
                            $action_success = 1;
                            $notification_case = setValue($notification_case, 'notification_operation_ok');
                            $notification_data = array_merge($product_array[0], $version_array[0]); //format notification_data to be returned to user by merging product_array and version_array
                            $this->unsetArrayElements($notification_data, $elements_to_unset_array); //remove unneeded elements (if any). function modifies array directly, use it separately from other functions/arguments
                            $this->addUpdateInstallation($product_id, $version_id, $this->ip_address, $user_local_path, $callback_type); //add or update installation
                        }
                    }
                }
            }

            $required_callback_parameters_array = ['product_title', 'product_short_description', 'product_full_description', 'product_url_homepage', 'product_url_order', 'version_id', 'version_number', 'version_expire_date', 'version_install_limit', 'version_install_count', 'version_upgrade_limit', 'version_upgrade_count', 'notification_case', 'notification_data']; //required callback parameters for this page
            foreach ($required_callback_parameters_array as $required_callback_parameter) { //in case some required parameter (used in callback and/or notification functions) was not fetched, set its value empty to prevent "undefined variable" errors
                if (! isset($$required_callback_parameter)) {
                    $$required_callback_parameter = '';
                }
            }

            if (empty($callback_type)) {
                $callback_type = 3; //default callback_type for this page (3 - upgrade)
            }

            if ($action_success == 1) { //everything OK
                $report_text = "$product_title $version_number $file_to_download_title_reports at $this->ip_address ($user_local_path) downloaded.";
            } else {
                $report_text = "$product_title $version_number file_to_download_title_reports at $this->ip_address ($user_local_path) could not be downloaded because of this reason: $error_details.";
            }
            createProductCallback($SMART_REPORTS, $product_id, $version_id, $this->ip_address, $user_local_path, $callback_type, $action_success, $version_install_count, $version_upgrade_count);
            returnUpdateServerNotification($ROOT_URL, $notification_case, $product_id, $product_title, $product_key, $product_short_description, $product_full_description, $product_url_homepage, $product_url_order, $version_number, $version_expire_date, $version_install_limit, $version_upgrade_limit, $this->ip_address, $notification_data);

            if ($action_success == 1) { //only output body content after returnServerNotification returned header with server signature. otherwise, "headers already sent" error will be displayed
                $filename_to_download = "$file_to_download_path/$file_to_download_name";
                $filename_formatted = slugifyText("$product_title-$version_number-$file_to_download_suffix").'.'.pathinfo($file_to_download_name, PATHINFO_EXTENSION); //format name of downloaded file like product-title-version-number-$file_to_download_suffix.extension
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=$filename_formatted");
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Content-Length: '.filesize($filename_to_download));
                readfile($filename_to_download);
            }
        } else { //possible cracking attempt, set variables required for reports function to null and generate cracking report
            $product_id = 0;
            $report_text = "Host $this->ip_address sent invalid data to requested_url and was rejected. Host sent this data: ".json_encode($request->all()).'.';
        }

        createProductReport($SMART_REPORTS, $product_id, $report_text, $action_success);
        if ($action_success != 1) { //record failed update attempt and ban host if needed
            recordFailedUpdate($BANNED_HOSTS, $FAILED_UPDATES_LIMIT, $this->ip_address);
        }
    }

    /**
     * check if installation exists and return its ID
     *
     * @param $product_id
     * @param $installation_ip
     * @param $installation_path
     *
     * @returns $installation_id
     */
    private function verifyInstallation($product_id, $installation_ip, $installation_path)
    {
        $installation_id = 0;

        if (aflValidateIntegerValue($product_id) && filter_var($installation_ip, FILTER_VALIDATE_IP) && ! empty($installation_path)) {
            $rows_array = DB::table('afu_installations')
                ->where('product_id', $product_id)
                ->where('installation_ip', $installation_ip)
                ->where('installation_path', $installation_path)
                ->get()->toArray(); //fetchRow("SELECT * FROM aus_installations WHERE product_id=? AND installation_ip=? AND installation_path=?", array($product_id, $installation_ip, $installation_path), array("i", "s", "s"));
            foreach ($rows_array as $row) {
                extract((array) $row);
            }
        }

        return $installation_id;
    }

    /**
     * remove elements with specified keys from standard/multi-dimensional array. function modifies variable directly (doesn't return any data).
     *
     * @param $array
     * @param $keys_to_unset_array
     */
    private function unsetArrayElements($array, $keys_to_unset_array)
    {
        foreach ($array as $key => $value) {
            if (in_array($key, $keys_to_unset_array, true)) { //element needs to be removed from array (use true for strict comparison, otherwise 0th element will be removed from sub-array)
                unset($array[$key]);
            } else {
                if (is_array($value)) { //it's a multi-dimensional array, re-apply function to each sub-array
                    $this->unsetArrayElements($value, $keys_to_unset_array);
                }
            }
        }
    }

    /**
     * add or update installation in database
     *
     * @param $product_id
     * @param $version_id
     * @param $installation_ip
     * @param $installation_path
     * @param $callback_type
     */
    private function addUpdateInstallation($product_id, $version_id, $installation_ip, $installation_path, $callback_type)
    {
        if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($version_id) && filter_var($installation_ip, FILTER_VALIDATE_IP) && ! empty($installation_path)) {
            $installation_id = $this->verifyInstallation($product_id, $installation_ip, $installation_path);

            if (! aflValidateIntegerValue($installation_id)) { //installation doesn't exist, add it now
                $installation_date = date('Y-m-d');
                DB::table('afu_installations')->insertOrIgnore([
                    'product_id' => $product_id,
                    'version_id' => $version_id,
                    'installation_ip' => $installation_ip,
                    'installation_path' => $installation_path,
                    'installation_date' => $installation_date,
                    'installation_status' => 1,
                ]);
            } else { //installation exists, update it
                $rows_array = DB::table('afu_installations')
                               ->where('installation_id', $installation_id)
                               ->update(['version_id' => $version_id]); //doMysqlQuery("UPDATE aus_installations SET version_id=? WHERE installation_id=?", array($version_id, $installation_id), array("i", "i"));
            }
        }
    }
}
