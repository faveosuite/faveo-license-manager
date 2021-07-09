<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AflApiKeys;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class ApiController extends Controller
{

public function api(Request $request){

$action_success=0; //will be changed to 1 later only if everything OK
$error_detected=0; //will be changed to 1 later if error occurs
$error_details=""; //will be filled with errors (if any)
$added_records=0;
$updated_records=0;
$removed_records=0;


$api_action_success=0;
$api_error_detected=0;
$api_error_details="";
$formatted_api_string=""; //used only by this file to forward API requests to other files


    foreach ($sets_array=DB::select('select * from afl_settings') as $set)
     {
     extract((array)$set);
     }
     
$api_key_secret = $request->get('api_key_secret');
$api_function = $request->get('api_function');  
$ip_address = $request->ip();


$SUPPORTED_API_FUNCTIONS_ARRAY=array("banned_hosts_add", "banned_hosts_edit", "clients_add", "clients_edit", "installations_edit", "licenses_add", "licenses_edit", "products_add", "products_edit", "search");

if (!empty($api_key_secret) && !empty($api_function))
    {
    if ($API_STATUS==1 && in_array($api_function, $SUPPORTED_API_FUNCTIONS_ARRAY))
        {
        $api_key_rows_array=AflApiKeys::where('api_key_secret',$api_key_secret)->where('api_key_status',1)->get()->toArray();
        //fetchRow("SELECT * FROM apl_api_keys WHERE api_key_secret=? AND api_key_status=?", array($api_key_secret, 1), array("s", "i"));
        if (empty($api_key_rows_array))
            {
            $api_error_detected=1;
            $api_error_details.="Invalid or inactive API key.<br>";
            }
        else
            {
            foreach ($api_key_rows_array as $api_key_row)
                {
                extract($api_key_row);
                }

            if (!empty($api_key_ip))
                {
                $api_key_ips_array=explode(",", str_replace(" ", "", $api_key_ip)); //remove all space symbols (if any) between IPs
                if (!in_array($ip_address, $api_key_ips_array))
                    {
                    $api_error_detected=1;
                    $api_error_details.="API access from IP $ip_address is not allowed.<br>";
                    }
                }

            $api_permissions_name="api_key_".$api_function; //since each permission in database starts with api_key_ prefix, add this prefix to name of function submitted by user for quick permissions check
            if ($$api_permissions_name!=1)
                {
                $api_error_detected=1;
                $api_error_details.="Invalid API key permissions.<br>";
                }

            if ($api_error_detected!=1 && $$api_permissions_name==1)
                {
                $api_action_success=1;
                }
            }
        }
    else
        {
        $api_error_detected=1;
        $api_error_details.="API not enabled or invalid API function.<br>";
        }

    if ($api_action_success==1) //API check OK, continue with actual request
        {
        $formatted_api_string.=http_build_query($_POST); //format API string using user's submitted variables
        $formatted_api_string.="&api_post_key=".hash("sha256", $ROOT_URL.$DATABASE_VERSION.$api_function)."&submit_ok=Submit"; //add auto-generated key and extra arguments at the end of string

        echo aflCustomPost("$ROOT_URL/apl_api/$api_function.php", $formatted_api_string, "$ROOT_URL/apl_api/api.php"); //send formatted API request to correct file and output received (json-encoded) data right away
        }
    else //display error message
        {
        $page_message="The action could not be completed because of this reason:<br><br>$api_error_details";

        $api_response_array=array("api_action_success"=>$api_action_success, "api_error_detected"=>$api_error_detected, "action_success"=>$action_success, "error_detected"=>$error_detected, "page_message"=>$page_message); //make array with response data
        echo json_encode($api_response_array);
        }
    }

}
}
