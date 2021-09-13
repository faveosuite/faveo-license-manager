<?php

namespace App\Http\Controllers\Admin;
use App\Models\AflInstallations;
use App\Models\AflApiKeys;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Settings;
use App\Traits\Version;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Http\Requests\InstallationRequest;




/**
 * Consist of functionalities for the Installation page in Auto Faveo licenser
 * Class InstallationController
 * @package App\Http\Controllers\Admin
 */
class InstallationController extends Controller
{

/**
 * To Update intallation details in license manager
 * @param InstallationRequest $request
 * @param $api_key_secret
 * @param $installation_id
 * @param $installation_ip
 * @param $installation_status
 * @param $installation_disable_ip
 * @return success response if the record was found and updated
 */
public function installationUpdate(InstallationRequest $request)
{


    $api_key_secret = $request->get('api_key_secret');
    $installation_id = $request->get('installation_id');
    $installation_ip = $request->get('installation_ip');
    $installation_status = $request->get('installation_status');
    $installation_disable_ip = $request->get('installation_disable_ip');
    $delete_record = $request->get('delete_record');

if (empty($installation_id) || !aflValidateIntegerValue($installation_id) || empty($rows_array=AflInstallations::where('installation_id',$installation_id)->get())) //invalid record
    {
    return errorResponse(Lang::get('lang.invalid'),400);
    exit();
    }

$action_success=0; //will be changed to 1 later only if everything OK
$error_detected=0; //will be changed to 1 later if error occurs
$error_details=""; //will be filled with errors (if any)
$added_records=0;
$updated_records=0;
$removed_records=0;


$api_action_success=0;
$api_error_detected=0;
$api_error_details="";
$logged_admin_id=0; 

        if (null!==(request()->server('REMOTE_ADDR')))
        {
             $ip_address=request()->server('REMOTE_ADDR');
             }
             else {
                 $ip_address=$request->ip();
                 }

       if(!empty($api_key_secret))
       {
        $api = AflApiKeys::where('api_key_secret',$api_key_secret)->where('api_key_status',1)->get();
        if(empty($api))
        {
            return errorResponse(Lang::get('lang.invalid_api_key'),404);
        }
        else
        {
        $api_ip = new AflApiKeys();
        $api_ips= $api_ip->value('api_key_ip');

          if(!empty($api_ips))
          {
                if (!$api_ips->contains($ip_address))
                   {
                    $api_error_detected=1;
                    return errorResponse(Lang::get('lang.Api_Acess_not_allowed'),400);
                    }
                    else{
                        $api_action_success=1;

                    }
          }
          else{
              $api_action_success=1;

          }
        }
    if ($api_action_success==1) //API check OK, continue with actual request
        {
        $optional_api_parameters_array=array("installation_disable_ip_verification"); //optional API parameters for this page
        foreach ($optional_api_parameters_array as $optional_api_parameter) //in case some required parameter was not submitted, set its value empty to prevent "undefined variable" errors
            {
            if (!isset($$optional_api_parameter))
                {
                $$optional_api_parameter="";
                }
            }

       //code between {} tags is identical in files with the same name in /apl_admin and /apl_api directories, EXCEPT redirectInvalidRecord($script_name); line
            
            if (!empty($delete_record) && $delete_record==1)
                {
                $removed_records+=$this->deleteInstallation($installation_id);
                if ($removed_records>0)
                    {
                    $action_success=1;

                    $page_message="Deleted $removed_records installation(s).";
                    createReport(strip_tags($page_message), $logged_admin_id, 1, $action_success);
                    echo $page_message; //THIS LINE IS CUSTOM IN API. ADMINISTRATION DASHBOARD CODE CONTAINS redirectInvalidRecord($script_name);
                    exit();
                    }
                else
                    {
                    $error_detected=1;
                    $error_details.="Invalid record or database error.<br>";
                    }
                }

            if (filter_var($installation_ip, FILTER_VALIDATE_IP) && validateIntegerValue($installation_status, 0, 2))
                {
                if ($error_detected!=1)
                    {
                    $updated_records+=AflInstallations::where('installation_id',$installation_id)
                                     ->update([
                                        'installation_ip' => $installation_ip,
                                        'installation_disable_ip_verification' => $installation_disable_ip,
                                        'installation_status'=> $installation_status
                                         ]);
                    if (!validateIntegerValue($updated_records))
                        {
                        $error_detected=1;
                        $error_details.="Invalid record details, duplicated data, or database error.<br>";
                        }
                    else
                        {
                        $action_success=1;
                         
                        $rows_array = AflInstallations::leftJoin('afl_products','afl_installations.installation_id','=', 'afl_products.product_id')
                                              ->where('afl_installations.installation_id',$installation_id)
                                              ->get()->toArray();
                        foreach ($rows_array as $row) //fetch product details to use in reports
                            {
                            extract($row);
                            }
                        }
                    }
                }
            else
                {
                $error_detected=1;
                $error_details.="Invalid IP address or status.<br>";
                }

            if ($action_success==1) //everything OK
                {
                $page_message="$product_title installation on $installation_domain ($installation_ip) updated.";
                $page_message_class="alert alert-success";
                }
            else //display error message
                {
                $page_message="Installation could not be updated because of this reason: <br><br>$error_details";
                $page_message_class="alert alert-danger";
                }

            createReport(strip_tags($page_message), $logged_admin_id, 1, $action_success);
            
        }
    else //display error message
        {
        $page_message="The action could not be completed because of this reason:<br><br>$api_error_details";
        }

    $api_response_array=array("api_action_success"=>$api_action_success, "api_error_detected"=>$api_error_detected, "action_success"=>$action_success, "error_detected"=>$error_detected, "page_message"=>$page_message); //make array with response data
    echo json_encode($api_response_array);
    }
}



/**
 * To Delete intallation details in license manager
 * @param $installation_id
 * @return success response if the record was found and deleted
 */
public function deleteInstallation($installation_id)
    {
      $removed_records=0;

    if (validateIntegerValue($installation_id))
        {
          
          $removed_records+=AflInstallations::where('installation_id',$installation_id)->delete();

        }

    return $removed_records;

   
}
}
