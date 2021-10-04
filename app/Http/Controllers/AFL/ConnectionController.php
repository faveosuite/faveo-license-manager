<?php

namespace App\Http\Controllers\AFL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AflSettings;
use App\Models\AflProducts;
use Illuminate\Support\Facades\Lang;



/**
 * Consist of functionalities for Establishing connection between Helpdesk and Auto Faveo licenser
 * Class ConnectionController
 * @package App\Http\Controllers\AFL
 */
class ConnectionController extends Controller

{
    public function __construct(){
        $this->ip_address=request()->server('REMOTE_ADDR');
        if (null!==(request()->server('HTTP_REFERER')))
        {
            $this->refer=request()->server('HTTP_REFERER');
        }
        else
        {
            $this->refer=request()->get('refer');
        }
    }
     /**
     * To test if the connection between the Faveo Helpdesk and Auto faveo Licenser has been established
     *
     * @param Request $request
     * @param $product_id
     * @param $connection_hash
     *
     * @return  response connection is established successfuly
    */
    
       public function connection(Request $request)
       {
        //set supported browsers (internal requests only coming from these browsers will be processed)
        //$connection_hash=rawurlencode(hash("sha256", "connection_test")); //should be passed from helpdesk

        //dd($connection_hash);
        $product_id = $request->input('product_id');
        $connection_hash = $request->input('connection_hash');
        if (filter_var($this->ip_address, FILTER_VALIDATE_IP) &&
        filter_var($this->refer, FILTER_VALIDATE_URL)
        && aflValidateIntegerValue($product_id) && $connection_hash==hash("sha256", "connection_test"))
        {
               $rows_array =[$this->ip_address,$this->refer,$product_id,$connection_hash];
               return "<connection_test>OK</connection_test>";
        }
        else{
          return errorResponse(Lang::get('lang.invalid_connection'),400);
        }

    }
}
