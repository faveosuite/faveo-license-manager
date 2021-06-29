<?php

namespace App\Http\Controllers\AFL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AflSettings;
use App\Traits\Settings;
use App\Traits\Version;
use App\Models\AflProducts;
use Illuminate\Support\Facades\Lang;



/**
 * Consist of functionalities for Establishing connection between Helpdesk and Auto Faveo licenser 
 * Class ConnectionController
 * @package App\Http\Controllers\AFL
 */
class ConnectionController extends Controller

{
  /*use Settings, Version;

  public $action_success=0; //will be changed to 1 later only if everything OK
  public $error_detected=0; //will be changed to 1 later if error occurs
  public $error_details=""; //will be filled with errors (if any)
  public $added_records=0;
  public $updated_records=0;
  public $removed_records=0;*/

  
     /**
     * To test if the connection between the Faveo Helpdesk and Auto faveo Licenser has been established
     * 
     * @param Request $request
     * @param $product_id
     * @param $connection_hash
     *
     * @return  response connection is established successfuly
    */
       public function connection(Request $request,$product_id,$connection_hash)
       {
       
        //set supported browsers (internal requests only coming from these browsers will be processed)
        $SUPPORTED_BROWSERS_ARRAY=array("Mozilla/5.0 (Windows NT 6.3; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0", "phpmillion Custom Post", "phpmillion cURL","Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
        //$connection_hash=rawurlencode(hash("sha256", "ConnectionController")); //should be passed from billing or helpdesk
        //dd($connection_hash);
        
        
        //get IP, refer, requested page, script filename, and user agent
       if (null!==(\request()->server('REMOTE_ADDR'))) 
       {
         $ip_address=request()->server('REMOTE_ADDR');
         } 
         else {
           $ip_address=$request->get('ip_address');
           }
       
       if (null!==(\request()->server('HTTP_REFERER'))) 
       {
         $refer=request()->server('HTTP_REFERER');
         } 
         else 
         {
           $refer=$request->get('refer');
           }
         
       if (null!==(\request()->server('HTTP_USER_AGENT'))) 
       {
         $user_agent=request()->server('HTTP_USER_AGENT');
         } 
         else 
         {
           $user_agent=$request->get('user_agent');
           }
       
        
       
        if (filter_var($ip_address, FILTER_VALIDATE_IP) &&
        in_array($user_agent, $SUPPORTED_BROWSERS_ARRAY) &&
        filter_var($refer, FILTER_VALIDATE_URL)
        && aflValidateIntegerValue($product_id) && $connection_hash==hash("sha256", "ConnectionController"))
        {     
           $rows_array =[$ip_address,$user_agent,$refer,$product_id,$connection_hash];
               return \successResponse(Lang::get('lang.Connection_OK'),$rows_array,200);
        }
        else{
          return \errorResponse(Lang::get('lang.invalid_connection'),400);
        }

    }
   
}
