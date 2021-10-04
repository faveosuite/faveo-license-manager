<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Admin\ApiKeysController;
use App\Http\Controllers\Admin\InstallationController;
use App\Http\Controllers\Controller;
use App\Models\AfuInstallations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class UpdateInstallationsController extends Controller
{
    public function __construct(Request $request)
    {
        $this->ip_address=request()->server('REMOTE_ADDR');
    }
    public function updateInstallationEdit(Request $request){

        $error_details="";
        $updated_records=0;
        $removed_records=0;
        $error_detected=0;
        $action_success=0;
        $api_error_detected=0;
        $api_key_secret = $request->get('api_key_secret');
        $installation_id = $request->get('installation_id');
        $installation_ip = $request->get('installation_ip');
        $installation_status = $request->get('installation_status');
        $delete_record = $request->get('delete_record');

        if (empty($installation_id) || !aflValidateIntegerValue($installation_id) || empty($rows_array=AfuInstallations::where('installation_id',$installation_id)->get()->toArray())) //invalid record
        {
           return errorResponse(Lang::get('lang.invalid'),404);
        }
        $api_key = new ApiKeysController();
        $api_action_success=$api_key->apiKeyCheck($api_key_secret,$this->ip_address);
            if ($api_action_success==1) //API check OK, continue with actual request
            {
                $optional_api_parameters_array=array(); //optional API parameters for this page
                foreach ($optional_api_parameters_array as $optional_api_parameter) //in case some required parameter was not submitted, set its value empty to prevent "undefined variable" errors
                {
                    if (!isset($$optional_api_parameter)) {
                        $$optional_api_parameter="";
                    }
                }
                    if (!empty($delete_record) && $delete_record==1) {
                        $removed_records+=$this->deleteInstallation($installation_id);
                        if ($removed_records>0) {
                            $action_success=1;
                            $page_message="Deleted $removed_records installation(s).";
                            createReport(strip_tags($page_message), 1, 1, $action_success);
                            return $page_message; //THIS LINE IS CUSTOM IN API. ADMINISTRATION DASHBOARD CODE CONTAINS redirectInvalidRecord($script_name);

                        }
                        else {
                            $error_detected=1;
                            $error_details.="Invalid record or database error.";
                        }
                    }

                    if (filter_var($installation_ip, FILTER_VALIDATE_IP) && aflValidateIntegerValue($installation_status, 0, 2)) {
                        if ($error_detected!=1) {
                            $updated_records+=AfuInstallations::where('installation_id',$installation_id)
                                ->update([
                                    'installation_ip' => $installation_ip,
                                    'installation_status'=> $installation_status
                                ]);
                            if (!aflValidateIntegerValue($updated_records)) {
                                $error_detected=1;
                                $error_details.="Invalid record details, duplicated data, or database error.";
                            }
                            else {
                                $action_success=1;
                                foreach ($rows_array= AfuInstallations::leftJoin('afl_products','afu_installations.product_id','=','afl_products.product_id')
                                                                       ->where('afu_installations.installation_id',$installation_id)->get()->toArray() as $row) {
                                    extract($row);
                                }
                            }
                        }
                    }
                    else {
                        $error_detected=1;
                        $error_details.="Invalid IP address or status.";
                    }

                    if ($action_success==1) //everything OK
                    {
                        $page_message="$product_title installation on $installation_ip updated.";
                    }
                    else //display error message
                    {
                        $page_message="Installation could not be updated because of this reason: $error_details";
                    }

                    createReport(strip_tags($page_message), 1, 1, $action_success);

            }
            else //display error message
            {
                $api_error_detected = 1;
                $page_message="The action could not be completed because of this reason is your api key secret was invalid";
            }

            $api_response_array=array("api_action_success"=>$api_action_success, "api_error_detected"=>$api_error_detected, "action_success"=>$action_success, "error_detected"=>$error_detected, "page_message"=>$page_message); //make array with response data
            return json_encode($api_response_array);

    }

    private function deleteInstallation($installation_id){
            $removed_records=0;
            if (aflValidateIntegerValue($installation_id))
            {
                $removed_records+=AfuInstallations::where('installation_id',$installation_id)->delete();
            }
            return $removed_records;
    }
    public function show(){
        $Install = $this->updateInstallArray();
        return successResponse(Lang::get('lang.Install_show'),$Install,200);
    }
    private function updateInstallArray(){
        $rows_array=AfuInstallations::leftJoin('afl_products','afu_installations.product_id','=','afl_products.product_id')
                                     ->leftJoin('afu_versions','afu_installations.version_id','=','afu_versions.version_id')
                                     ->orderBy('installation_date','DESC')
                                     ->orderBy('installation_id','DESC')->get()->toArray();
        foreach ($rows_array as $row)
         {
          foreach ($row as $key=>$value)
           {
             $item_array[$key]=$value;
           }

           $item_array['installation_status_formatted']=returnFormattedStatusArray($item_array['installation_status'], "Active", "Inactive", "Unknown");
           $root_array[]=$item_array;
         }
      return $root_array;
    }
}
