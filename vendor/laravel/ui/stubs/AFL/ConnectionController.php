<?php

namespace App\Http\Controllers\AFL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AflSettings;
use App\Traits\Settings;
use App\Traits\Version;
use Illuminate\Support\Facades\Lang;



/**
 * Consist of functionalities for Establishing connection between Helpdesk and Auto Faveo licenser 
 * Class ConnectionController
 * @package App\Http\Controllers\AFL
 */
class ConnectionController extends Controller

{
  use Settings, Version;

  public $action_success=0; //will be changed to 1 later only if everything OK
  public $error_detected=0; //will be changed to 1 later if error occurs
  public $error_details=""; //will be filled with errors (if any)
  public $added_records=0;
  public $updated_records=0;
  public $removed_records=0;

  
     /**
     * To test if the connection between the Faveo Helpdesk and Auto faveo Licenser has been established
     * 
     * @param Request $request
     *
     * @return  response connection is established successfuly
    */
       public function connection(Request $request)
       {
         
        $setting = $request->post();

        if(!empty($setting)
        && is_array($setting)
        && array_walk_recursive($setting, "sanitizeSubmittedData", array("script_name"=>$script_name, "html_fields"=>$FORM_FIELDS_WITH_TAGS)))
        {
         extract($setting, EXTR_SKIP);
        }

        else{
          return \errorResponse(Lang::get('lang.Exit'),404);
        }

        if (filter_var($ip_address, FILTER_VALIDATE_IP) &&
        in_array($user_agent, $SUPPORTED_BROWSERS_ARRAY) &&
        filter_var($refer, FILTER_VALIDATE_URL)
        && aflValidateIntegerValue($product_id) && $connection_hash==hash("sha256", "connection_test"))
        {     
               return \successResponse(Lang::get('lang.Connection_OK'),200);
        }

    }
   
}
