<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\AflApiKeys;
use App\Http\Requests\ApiRequest;




/**
 * Consist of functionalities for the Api keys Generation page in Auto Faveo licenser 
 * Class ApiController
 * @package App\Http\Controllers\Admin
 */
class ApiKeysController extends Controller
{

      /**
       * To Add Api keys to the license manager 
       * @param ApiRequest $request
       * @return success response if added successfuly 
       */
      public function apiKeyAdd(ApiRequest $request)
       {
           $error_detected =0;
           try{
           $api = new AflApiKeys(array (
           'api_key_secret' => $request->get('api_key_secret'),
           'api_key_ip' => $request->get('api_key_ip'),
           'api_key_clients_add' => $request->get('api_key_clients_add'),
           'api_key_clients_edit' => $request->get('api_key_clients_edit'),
           'api_key_licenses_add' => $request->get('api_key_licenses_add'),
           'api_key_licenses_edit' => $request->get('api_key_licenses_edit'),
           'api_key_products_add'=> $request->get('api_key_products_add'),
           'api_key_products_edit'=> $request->get('api_key_products_edit'),
           'api_key_installations_edit' => $request->get('api_key_installations_edit'),
           'api_key_search' => $request->get('api_key_search'),
           'api_key_status' => $request->get('api_key_status')
           ));
            if (!empty($request->get('api_key_ip')))
            {
            $api_key_ips_array=explode(".", str_replace(" ", "", $request->get('api_key_ip')));//remove all space symbols (if any) between IPs
            foreach ($api_key_ips_array as $ip_to_validate)
                { 
                if (!filter_var($ip_to_validate, FILTER_VALIDATE_IP))
                    {
                    $error_detected=1;
                    errorResponse(Lang::get('lang.invalid'),400);
                    break;
                    }
                }
            } 
           $api->save();
           return successResponse(Lang::get('lang.'),$api,201);
           }

           catch(Exception $e){
               return $e->getMessage();
           }
}


   
      /**
       * To Update Api keys to the license manager 
       * @param ApiRequest $request
       * @param $api_key_id
       * @return success response if Updated successfuly 
       */
   public function apiKeyUpdate(Request $request, $api_key_id)
  {   


       if (!empty($request->get('api_key_ip')))
            {
            $api_key_ips_array=explode(".", str_replace(" ", "", $request->get('api_key_ip')));//remove all space symbols (if any) between IPs
            foreach ($api_key_ips_array as $ip_to_validate)
                { 
                if (!filter_var($ip_to_validate, FILTER_VALIDATE_IP))
                    {
                    $error_detected=1;
                    errorResponse(Lang::get('lang.invalid'),400);
                    break;
                    }
                }
            }
      $updateapi = DB::table('afl_api_keys')
                   ->where('api_key_id',$api_key_id)
                   ->update([
                       'api_key_secret' =>$request->get('api_key_secret'),
                       'api_key_ip' => $request->get('api_key_ip'),
                       'api_key_clients_add' => $request->get('api_key_clients_add'),
                       'api_key_clients_edit' => $request->get('api_key_clients_edit'),
                       'api_key_licenses_add' => $request->get('api_key_licenses_add'),
                       'api_key_licenses_edit' => $request->get('api_key_licenses_edit'),
                       'api_key_products_add'=> $request->get('api_key_products_add'),
                       'api_key_products_edit'=> $request->get('api_key_products_edit'),
                       'api_key_installations_edit' => $request->get('api_key_installations_edit'),
                       'api_key_search' => $request->get('api_key_search'),
                       'api_key_status' => $request->get('api_key_status')
                   ]);

        if(!\aflValidateIntegerValue($updateapi))
        {
            return errorResponse(Lang::get('lang.invalid'),400);
        } 
        else
        {
            return successResponse(Lang::get('lang.'),$updateapi,200);
        }  
  }

   
      /**
       * To Delete Api keys to the license manager 
       * @param $api_key_id
       * @return success response if Delete successfuly 
       */
  public function apiKeyDelete($api_key_id)
  {
       if (aflValidateIntegerValue($api_key_id))
        {
        $removed_records=AflApiKeys::where('api_key_id',$api_key_id)->delete();//doMysqlQuery("DELETE FROM apl_api_keys WHERE api_key_id=?", array($api_key_id), array("i"));
        }

        return successResponse(Lang::get('lang.'),$removed_records,200);
    }
  

}
