<?php

namespace App\Http\Controllers\AfuCallbacks;

use App\Http\Controllers\Controller;
use App\Models\AflProducts;
use App\Models\AfuVersions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetVersionsController extends Controller
{
    public function getVersion(Request $request)
    {

        $action_success = 0; //will be changed to 1 later only if everything OK
        $error_detected = 0; //will be changed to 1 later if error occurs
        $error_details = ""; //will be filled with errors (if any)
        $added_records = 0;
        $updated_records = 0;
        $removed_records = 0;
        $notification_data="";

        //get IP, refer and user agent
        //get script settings
        $sets_array=DB::table('afl_settings')->select('afl_settings.*')->get()->toArray();
        foreach ($sets_array as $set)
        {
            extract((array)$set);
        }

        if (null!==(request()->server('REMOTE_ADDR')))
        {
            $ip_address=request()->server('REMOTE_ADDR');
        }
        else {
            $ip_address=$request->get('ip_address');
        }

        if (null!==(request()->server('HTTP_REFERER')))
        {
            $refer=request()->server('HTTP_REFERER');
        }
        else
        {
            $refer=$request->get('refer');
        }

        $product_id = $request->get('product_id');
        $product_key = $request->get('product_key');
        $version_number = $request->get('version_number');
        $user_local_path = $request->get('user_local_path');
        $script_signature = $request->get('script_signature');

        //check basic data
        if (filter_var($ip_address, FILTER_VALIDATE_IP) && aflValidateIntegerValue($product_id) && !empty($product_key) && !empty($user_local_path) && !empty($script_signature)) {
            $notification_case = "";
            $product_array = array();
            $version_array = array();
            $elements_to_unset_array = array("product_key", "version_install_file", "version_install_query", "version_raw_install_query", "version_upgrade_file", "version_upgrade_query", "version_raw_upgrade_query", "version_install_count", "version_upgrade_count", "version_comments"); //elements to be removed from final array (notification_data) because of security or other reasons for this page

            $product_array = AflProducts::where('product_id',$product_id)->where('product_key',$product_key)->get()->toArray();

            //fetchRow("SELECT * FROM aus_products WHERE product_id=? AND product_key=?", array($product_id, $product_key), array("i", "s")); //check if product exists, so it's possible to generate reports with product name even if product is inactive or version doesn't exist
            if (empty($product_array)) //product doesn't exist
            {
                $error_detected = 1;
                $error_details = setValue($error_details, "product not found");
                $notification_case = setValue($notification_case, "notification_product_not_found");

                $product_title = "Unknown Product (ID: $product_id)"; //make $product_title to use in reports for non-existing product
                $product_id = 0; //set $product_id to 0 for non-existing product, so this report will be displayed in Unknown Reports section
            } else //product exists, do other checks
            {
                foreach ($product_array as $row) //fetch product details
                {
                    extract((array)$row);
                }

                if ($product_status != 1) //product inactive
                {
                    $error_detected = 1;
                    $error_details = setValue($error_details, "product inactive");
                    $notification_case = setValue($notification_case, "notification_product_inactive");
                } else //product active, do other checks
                {
                    if (!empty($version_number)) //get specified version
                    {
                        $version_array = AfuVersions::where('product_id',$product_id)->where('version_number',$version_number)->get()->toArray();//fetchRow("SELECT * FROM aus_versions WHERE product_id=? AND version_number=?", array($product_id, $version_number), array("i", "s"));
                    } else //version not specified, get latest active one
                    {
                        $version_array = AfuVersions::where('product_id',$product_id)->where('version_status',1)->orderBy('version_id','desc')->limit(1)->get()->toArray(); //fetchRow("SELECT * FROM aus_versions WHERE product_id=? AND version_status=? ORDER BY version_id DESC LIMIT 1", array($product_id, 1), array("i", "i"));
                    }

                    if (empty($version_array)) //version doesn't exist
                    {
                        $error_detected = 1;

                        if (!empty($version_number)) //version was specified, display message that specified version not found
                        {
                            $error_details = setValue($error_details, "version not found or is inactive");
                            $notification_case = setValue($notification_case, "notification_version_not_found");
                        } else //no version was specified, display message that product has no versions
                        {
                            $error_details = setValue($error_details, "product has no versions");
                            $notification_case = setValue($notification_case, "notification_product_no_versions");
                        }
                    } else //version exists, do other checks
                    {
                        foreach ($version_array as $row) //fetch version details
                        {
                            extract((array)$row);
                        }

                        if (!afuVerifyScriptSignature($ROOT_URL, $script_signature, $product_id, $product_key)) //invalid signature
                        {
                            $error_detected = 1;
                            $error_details = setValue($error_details, "invalid script signature");
                            $notification_case = setValue($notification_case, "notification_invalid_signature");
                        }

                        if ($version_status != 1) //version inactive
                        {
                            $error_detected = 1;
                            $error_details = setValue($error_details, "version inactive");
                            $notification_case = setValue($notification_case, "notification_version_inactive");
                        }

                        if (aflVerifyDateTime($version_expire_date, "Y-m-d") && $version_expire_date < date("Y-m-d")) //version expired
                        {
                            $error_detected = 1;
                            $error_details = setValue($error_details, "version expired on $version_expire_date");
                            $notification_case = setValue($notification_case, "notification_version_expired");
                        }

                        if ($error_detected != 1) {
                            $action_success = 1;
                            $notification_case = setValue($notification_case, "notification_operation_ok");

                            $notification_data = array_merge($product_array[0], $version_array[0]); //format notification_data to be returned to user by merging product_array and version_array
                            $this->unsetArrayElements($notification_data, $elements_to_unset_array); //remove unneeded elements (if any). function modifies array directly, use it separately from other functions/arguments
                        }
                    }
                }
            }

            $required_callback_parameters_array = array("product_title", "product_short_description", "product_full_description", "product_url_homepage", "product_url_order", "version_id", "version_number", "version_expire_date", "version_install_limit", "version_install_count", "version_upgrade_limit", "version_upgrade_count", "notification_case", "notification_data"); //required callback parameters for this page
            foreach ($required_callback_parameters_array as $required_callback_parameter) //in case some required parameter (used in callback and/or notification functions) was not fetched, set its value empty to prevent "undefined variable" errors
            {
                if (!isset($$required_callback_parameter)) {
                    $$required_callback_parameter = "";
                }
            }

            if (empty($callback_type)) {
                $callback_type = 1; //default callback_type for this page (1 - "version check")
            }

            if ($action_success == 1) //everything OK
            {
                $report_text = "$product_title $version_number version information at $ip_address ($user_local_path) parsed.";
            } else {
                $report_text = "$product_title $version_number version information at $ip_address ($user_local_path) could not be parsed because of this reason: $error_details.";
            }

            createProductCallback($SMART_REPORTS, $product_id, $version_id, $ip_address, $user_local_path, $callback_type, $action_success, $version_install_count, $version_upgrade_count);
            returnUpdateServerNotification($ROOT_URL, $notification_case, $product_id, $product_title, $product_key, $product_short_description, $product_full_description, $product_url_homepage, $product_url_order, $version_number, $version_expire_date, $version_install_limit, $version_upgrade_limit, $ip_address, $notification_data);
        } else //possible cracking attempt, set variables required for reports function to null and generate cracking report
        {
            $product_id = 0;
            $report_text = "Host $ip_address sent invalid data to requested_url and was rejected. Host sent this data: " . json_encode($request->all()) . ".";
        }

        createProductReport($SMART_REPORTS, $product_id, $report_text, $action_success);
        if ($action_success != 1) //record failed update attempt and ban host if needed
        {
            recordFailedUpdate($BANNED_HOSTS, $FAILED_UPDATES_LIMIT, $ip_address);
        }

    }
    //remove elements with specified keys from standard/multi-dimensional array. function modifies variable directly (doesn't return any data).
    public function unsetArrayElements($array, $keys_to_unset_array)
    {
        foreach ($array as $key=>$value)
        {
            if (in_array($key, $keys_to_unset_array, true)) //element needs to be removed from array (use true for strict comparison, otherwise 0th element will be removed from sub-array)
            {
                unset($array[$key]);
            }
            else
            {
                if (is_array($value)) //it's a multi-dimensional array, re-apply function to each sub-array
                {
                    $this->unsetArrayElements($value, $keys_to_unset_array);
                }
            }
        }
    }
}
