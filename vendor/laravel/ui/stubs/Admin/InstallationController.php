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
public function installationUpdate(InstallationRequest $request){


    $api_key_secret = $request->get('api_key_secret');
    $installation_id = $request->get('installation_id');
    $installation_ip = $request->get('installation_ip');
    $installation_status = $request->get('installation_status');
    $installation_disable_ip = $request->get('installation_disable_ip');

if (empty($installation_id) || !aflValidateIntegerValue($installation_id) || empty($rows_array=AflInstallations::where('installation_id',$installation_id)->get())) //invalid record
    {
    return errorResponse(Lang::get('lang.invalid'),400);
    exit();
    }


        $api_action_success=0;
        $api_error_detected=0;
        $updated_records=0;
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

        if (filter_var($installation_ip, FILTER_VALIDATE_IP) && aflValidateIntegerValue($installation_status, 0, 2) && $api_action_success==1)
                {

                if ($api_error_detected!=1)
                  {
                    $updated_records+=AflInstallations::where('installation_id',$installation_id)
                                     ->update([
                                        'installation_ip' => $installation_ip,
                                        'installation_disable_ip_verification' => $installation_disable_ip,
                                        'installation_status'=> $installation_status
                                         ]);

                    //doMysqlQuery("UPDATE apl_installations SET installation_ip=?, installation_disable_ip_verification=?, installation_status=? WHERE installation_id=?", array($installation_ip, $installation_disable_ip_verification, $installation_status, $installation_id), array("s", "i", "i", "i"));

                    if (!aflValidateIntegerValue($updated_records))
                        {
                        $error_detected=1;
                        return errorResponse(Lang::get('lang.invalid'),400);
                        }
                    else
                        {
                        $api_action_success=1;
                        $rows_array = AflInstallations::leftJoin('afl_products','afl_installations.installation_id','=', 'afl_products.product_id')
                                              ->where('afl_installations.installation_id',$installation_id)
                                              ->get()->toArray();
                        foreach ($rows_array as $row) //fetchRow("SELECT * FROM apl_installations LEFT JOIN apl_products ON apl_installations.product_id=apl_products.product_id WHERE apl_installations.installation_id=?", array($installation_id), array("i")) as $row) //fetch product details to use in reports
                        {
                             extract($row);

                        }
                        return successResponse(Lang::get('lang.done'),$row,200);
                        }
                    }
                }
                else{
                    return errorResponse(Lang::get('lang.error'),400);
                }
    }
}



/**
 * To Delete intallation details in license manager
 * @param $installation_id
 * @return success response if the record was found and deleted
 */
public function deleteInstallation(Request $request)
    {

    $api_action_success=0;
    $api_error_detected=0;
    $removed_records=0;
    $installation_id = $request->get('installation_id');
    $api_key_secret= $request->get('api_key_secret');
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
        }
    if (aflValidateIntegerValue($installation_id))
        {
        $removed_records+=AflInstallations::where('installation_id',$installation_id)->delete();
        }

    return successResponse(Lang::get('lang.delete'),$removed_records,200);
    }
}
}
