<?php

namespace App\Http\Controllers\Admin;

namespace App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Settings;
use App\Traits\Version;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;


class SearchController extends Controller
{
/*use Settings, Version;
public $action_success=0; //will be changed to 1 later only if everything OK
public $error_detected=0; //will be changed to 1 later if error occurs
public $error_details=""; //will be filled with errors (if any).
public $added_records=0;
public $updated_records=0;
public $removed_records=0;


public $api_action_success=0;
public $api_error_detected=0;
public $api_error_details="";
public $logged_admin_id=0; //used for compatibility with createReport function in the same file in /apl_admin directory. since admin is not logged in when API is called, $logged_admin_id must be 0*/

public function search(Request $request,$api_key_secret,$search_type, $search_keyword){
/*if (!empty($_POST) && is_array($_POST) && array_walk_recursive($_POST, "sanitizeSubmittedData", array("script_name"=>$script_name, "html_fields"=>$FORM_FIELDS_WITH_TAGS))) //sanitize super variable with all POST values
    {
    extract($_POST, EXTR_SKIP); //extract sanitized data (don't overwrite existing variables)
    }
else //block invalid requests to callback file immediately
    {
    exit();
    }*/
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    $SUPPORTED_API_SEARCHES_ARRAY=array("banned_host", "callback", "client", "installation", "license", "product", "report");

//set default values for essential variables (mostly submitted to dropdown functions) when no values are set or values need to be reset
if (!isset($date_from) || !empty($date_from) && !aflVerifyDateTime($date_from, "Y-m-d")) //set default start date depending on system settings if start date is not set or invalid
    {
    $date_from=setDefaultDateFrom($RECORDS_ARCHIVE_DAYS);
    }

if (!isset($date_to) || !empty($date_to) && !aflVerifyDateTime($date_to, "Y-m-d")) //set default end date empty (all records will be included) if end date is not set or invalid
    {
    $date_to="";
    }

        $api_action_success=0;
        $api_error_detected=0;  
        if (null!==(\request()->server('REMOTE_ADDR'))) 
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
            return \errorResponse(Lang::get('lang.invalid_api_key'),404);
        }
        else
        {
        $api_ip = new AflApiKeys();
        $api_ips= $api_ip->pluck('api_key_ip');

          if(!empty($api_ips))
          {
                if (!$api_ips->contains($ip_address))
                   {   
                    $api_error_detected=1;
                    return \response(['message' => 'Api Access from this ip is not allowed']);
                    }
                    else{
                        $api_action_success=1;
                    }
          }
        }

        
    if ($api_action_success==1) //API check OK, continue with actual request
        {
        if (!in_array($search_type, $SUPPORTED_API_SEARCHES_ARRAY))
            {
            $api_error_detected=1;
            return \errorResponse(Lang::get('invalid_search_type'),400);
            }

        if (mb_strlen(trim($search_keyword), "UTF-8")<3)
            {
            $api_error_detected=1;
             return \errorResponse(Lang::get('invalid_search_term_min_3_characters'),400);
            }

        if ($api_error_detected!=1)
            {
            if ($search_type=="banned_host")
                {
                $elements_to_unset_array=array(); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=returnBannedHostsArray($date_from, $date_to, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="callback")
                {
                $elements_to_unset_array=array("product_title", "product_description", "product_sku", "product_url_homepage", "product_url_download", "product_date", "product_version", "product_envato_id", "product_status", "client_fname", "client_lname", "client_email",  "client_active_date", "client_cancel_date", "client_status", "client_formatted", "callback_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=returnCallbacksArray(0, $date_from, $date_to, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="client")
                {
                $elements_to_unset_array=array("client_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=returnClientsArray($search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="installation")
                {
                $elements_to_unset_array=array("product_title", "product_description", "product_sku", "product_url_homepage", "product_url_download", "product_date", "product_version", "product_envato_id", "product_status", "client_fname", "client_lname", "client_email",  "client_active_date", "client_cancel_date", "client_status", "client_formatted", "installation_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=returnInstallationsArray(0, $date_from, $date_to, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="license")
                {
                $elements_to_unset_array=array("product_title", "product_description", "product_sku", "product_url_homepage", "product_url_download", "product_date", "product_version", "product_envato_id", "product_status", "client_fname", "client_lname", "client_email",  "client_active_date", "client_cancel_date", "client_status", "client_formatted", "license_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=returnLicensesArray(0, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="product")
                {
                $elements_to_unset_array=array("product_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=returnProductsArray($search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="report")
                {
                $elements_to_unset_array=array("account_id", "client_fname", "client_lname", "client_email", "client_active_date", "client_cancel_date", "client_status", "client_formatted", "report_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=returnLicenseReportsArray(0, $date_from, $date_to, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if (empty($rows_array))
                {
                 return \errorResponse(Lang::get('lang.No_results_found'),400);
                }
            else
                {
                $api_action_success=1;
                }
            }

        if ($api_action_success==1) //everything OK
            {
            unsetArrayElements($rows_array, $elements_to_unset_array); //remove unneeded elements (if any). function modifies array directly, use it separately from other functions/arguments
            $page_message=$rows_array;
            return \successResponse(Lang::get('lang.search_complete'),$page_message,200);
            }
        else //display error message
            {
            
              return \errorResponse(Lang::get('lang.search_error'),400);

            }
        }
        else
        {
            return \errorResponse(Lang::get('lang.invalid'),400);
        }
            
        
   
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
                unsetArrayElements($value, $keys_to_unset_array);
                }
            }
        }
    }




//return banned hosts
public function returnBannedHostsArray($date_from="", $date_to="", $search_keyword="", $results_limit=0)
    {
    $root_array=array();

    if (!aflVerifyDateTime($date_from, "Y-m-d"))
        {
        $date_from="0000-00-00";
        }

    if (!aflVerifyDateTime($date_to, "Y-m-d"))
        {
        $date_to="9999-12-31";
        }

    $date_to.=" 23:59:59"; //include all records of last specified day

    if (!empty($search_keyword) && aflValidateIntegerValue($results_limit))
        {
        $search_keyword="%$search_keyword%"; //add wildcards

        $rows_array=DB::table('afl_banned_hosts')
                    ->where('banned_host_date','>=',$date_from)
                    ->where('banned_host_date','<=',$date_to)
                    ->where('banned_host_ip','like', $search_keyword)
                    ->orWhere('banned_host_comments','like',$search_keyword)
                    ->orderBy('banned_host_date','desc')
                    ->orderBy('banned_host_id','desc')
                    ->limit($results_limit)->get();
                     //fetchRow("SELECT * FROM apl_banned_hosts WHERE banned_host_date>=? AND banned_host_date<=? AND (banned_host_ip LIKE ? OR banned_host_comments LIKE ?) ORDER BY banned_host_date DESC, banned_host_id DESC LIMIT ?", array($date_from, $date_to, $search_keyword, $search_keyword, $results_limit), array("s", "s", "s", "s", "i"));
        }
    else
        {
        $rows_array=DB::table('afl_banned_hosts')
                     ->where('banned_host_date','>=',$date_from)
                     ->where('banned_host_date','<=',$date_to)
                     ->orderBy('banned_host_date', 'desc')
                     ->orderBy('banned_host_id','desc')
                     ->get();//fetchRow("SELECT * FROM apl_banned_hosts WHERE banned_host_date>=? AND banned_host_date<=? ORDER BY banned_host_date DESC, banned_host_id DESC", array($date_from, $date_to), array("s", "s"));
        }
    foreach ($rows_array as $row)
        {
        foreach ($row as $key=>$value)
            {
            $item_array[$key]=$value;
            }

        if (!aflVerifyDateTime($item_array['banned_host_last_block_date'], "Y-m-d"))
            {
            $item_array['banned_host_last_block_date']="";
            }

        $root_array[]=$item_array;
        }

    return $root_array;
    }


    
//return callbacks
public function returnCallbacksArray($product_id, $date_from="", $date_to="", $search_keyword="", $results_limit=0)
    {
    $root_array=array();

    if (!aflVerifyDateTime($date_from, "Y-m-d"))
        {
        $date_from="0000-00-00";
        }

    if (!aflVerifyDateTime($date_to, "Y-m-d"))
        {
        $date_to="9999-12-31";
        }

    $date_to.=" 23:59:59"; //include all records of last specified day

    if (!empty($search_keyword) && aflValidateIntegerValue($results_limit))
        {
        $search_keyword="%$search_keyword%"; //add wildcards

        $rows_array=DB::table('afl_callbacks')
                     ->leftJoin('afl_products','afl_callbacks.product_id','=','afl_products.product_id')
                     ->leftJoin('afl_clients','afl_callbacks.client_id','=','afl_clients.client_id')
                     ->where('afl_callbacks.callback_date_time','>=', $date_from)
                     ->where('afl_callbacks.callback_date_time','<=', $date_to)
                     ->where('afl_clients.client_email', 'like' ,$search_keyword)
                     ->orWhere('afl_callbacks.license_code','like', $search_keyword)
                     ->orWhere('afl_callbacks.callback_domain','like', $search_keyword)
                     ->orWhere('afl_callbacks.callback_ip','like', $search_keyword)
                     ->orderBy('afl_callbacks.callback_date_time','desc')
                     ->orderBy('afl_callbacks.callback_id','desc')
                     ->limit($results_limit)->get();
        /*fetchRow("SELECT * FROM apl_callbacks
        LEFT JOIN apl_products
        ON apl_callbacks.product_id=apl_products.product_id
        LEFT JOIN apl_clients
        ON apl_callbacks.client_id=apl_clients.client_id
        WHERE apl_callbacks.callback_date_time>=? AND apl_callbacks.callback_date_time<=? AND (apl_clients.client_email LIKE ? OR apl_callbacks.license_code LIKE ? OR apl_callbacks.callback_domain LIKE ? OR apl_callbacks.callback_ip LIKE ?)
        ORDER BY apl_callbacks.callback_date_time DESC, apl_callbacks.callback_id DESC LIMIT ?", array($date_from, $date_to, $search_keyword, $search_keyword, $search_keyword, $search_keyword, $results_limit), array("s", "s", "s", "s", "s", "s", "i"));  */
        }
    else
        {
        $rows_array= DB::table('afl_callbacks')
                   ->leftJoin('afl_products','afl_callbacks.product_id','=','afl_products.product_id')
                   ->leftJoin('afl_clients','afl_callbacks.client_id','=','afl_clients.client_id')
                   ->where('afl_callbacks.product_id','=' , $product_id)
                   ->where('afl_callbacks.callback_date_time','>=', $date_from)
                   ->where('afl_callbacks.callback_date_time','<=', $date_to)
                   ->orderBy('afl_callbacks.callback_date_time','desc')
                   ->orderBy('afl_callbacks.callback_id','desc')->get();
        /*fetchRow("SELECT * FROM apl_callbacks
        LEFT JOIN apl_products
        ON apl_callbacks.product_id=apl_products.product_id
        LEFT JOIN apl_clients
        ON apl_callbacks.client_id=apl_clients.client_id
        WHERE apl_callbacks.product_id=? AND apl_callbacks.callback_date_time>=? AND apl_callbacks.callback_date_time<=?
        ORDER BY apl_callbacks.callback_date_time DESC, apl_callbacks.callback_id DESC", array($product_id, $date_from, $date_to), array("i", "s", "s"));*/
        }
    foreach ($rows_array as $row)
        {
        foreach ($row as $key=>$value)
            {
            $item_array[$key]=$value;
            }

        $item_array['client_formatted']=formatClient($item_array['license_code'], $item_array['client_email']);
        $item_array['callback_date_time']=removeSeconds($item_array['callback_date_time']);
        $item_array['callback_status_formatted']=returnFormattedStatusArray($item_array['callback_status'], "Success", "Error", "Unknown");

        $root_array[]=$item_array;
        }

    return $root_array;
    }

//return clients
public function returnClientsArray($search_keyword="", $results_limit=0)
    {
    $root_array=array();

    if (!empty($search_keyword) && aflValidateIntegerValue($results_limit))
        {
        $search_keyword="%$search_keyword%"; //add wildcards

        $rows_array = DB::table('afl_clients')
                      ->join('afl_licenses','afl_clients.client_id','=','afl_licenses.client_id')
                      ->join('afl_installations','afl_clients.client_id','=','afl_installations.client_id')
                      ->select('afl_clients.*', DB::raw("afl_licenses.count(*)' AS total_licenses"), DB::raw("afl_installations.count(*) AS total_installations"))
                      ->where('client_fname','like',$search_keyword)
                      ->where('client_lname','like',$search_keyword)
                      ->where('client_email','like',$search_keyword)
                      ->limit($results_limit)->get();
        /*fetchRow("SELECT *,
        (SELECT COUNT(*) FROM apl_licenses WHERE apl_clients.client_id=apl_licenses.client_id) AS total_licenses,
        (SELECT COUNT(*) FROM apl_installations WHERE apl_clients.client_id=apl_installations.client_id) AS total_installations
        FROM apl_clients
        WHERE client_fname LIKE ? OR client_lname LIKE ? OR client_email LIKE ?
        ORDER BY client_fname, client_lname LIMIT ?", array($search_keyword, $search_keyword, $search_keyword, $results_limit), array("s", "s", "s", "i"));*/
        }
    else
        {
        $rows_array=DB::table('afl_clients')
                      ->join('afl_licenses','afl_clients.client_id','=','afl_licenses.client_id')
                      ->join('afl_installations','afl_clients.client_id','=','afl_installations.client_id')
                      ->select('afl_clients.*', DB::raw("afl_licenses.count(*)' AS total_licenses"), DB::raw("afl_installations.count(*) AS total_installations"))
                      ->orderBy('client_fname')
                      ->orderBy('client_lname')
                      ->get();
        /*fetchRow("SELECT *,
        (SELECT COUNT(*) FROM apl_licenses WHERE apl_clients.client_id=apl_licenses.client_id) AS total_licenses,
        (SELECT COUNT(*) FROM apl_installations WHERE apl_clients.client_id=apl_installations.client_id) AS total_installations
        FROM apl_clients
        ORDER BY client_fname, client_lname");*/
        }
    foreach ($rows_array as $row)
        {
        foreach ($row as $key=>$value)
            {
            $item_array[$key]=$value;
            }

        $item_array['client_status_formatted']=returnFormattedStatusArray($item_array['client_status']);

        $root_array[]=$item_array;
        }

    return $root_array;
    }
//return installations
public function returnInstallationsArray($product_id, $date_from="", $date_to="", $search_keyword="", $results_limit=0)
    {
    $root_array=array();

    if (!aflVerifyDateTime($date_from, "Y-m-d"))
        {
        $date_from="0000-00-00";
        }

    if (!aflVerifyDateTime($date_to, "Y-m-d"))
        {
        $date_to="9999-12-31";
        }

    $date_to.=" 23:59:59"; //include all records of last specified day

    if (!empty($search_keyword) && aflValidateIntegerValue($results_limit))
        {
        $search_keyword="%$search_keyword%"; //add wildcards

        $rows_array=DB::table('afl_installations')
                    ->leftJoin('afl_products','apl_installations.product_id','=','apl_products.product_id')
                    ->leftJoin('afl_clients','apl_installations.client_id','=','apl_clients.client_id')
                    ->where('afl_installations.installation_date','>=',$date_from)
                    ->where('afl_installations.installation_date','<=', $date_to)
                    ->orWhere('afl_clients.client_email','like', $search_keyword)
                    ->orWhere('afl_installations.license_code','like', $search_keyword)
                    ->orWhere('afl_installations.installation_domain','like', $search_keyword)
                    ->orWhere('afl_installations.installation_ip','like', $search_keyword)
                    ->orderBy('installation_date','desc')
                    ->orderBy('installation_id','desc')
                    ->limit($results_limit)->get();
        /*fetchRow("SELECT * FROM apl_installations
        LEFT JOIN apl_products
        ON apl_installations.product_id=apl_products.product_id
        LEFT JOIN apl_clients
        ON apl_installations.client_id=apl_clients.client_id
        WHERE apl_installations.installation_date>=? AND apl_installations.installation_date<=? AND (apl_clients.client_email LIKE ? OR apl_installations.license_code LIKE ? OR apl_installations.installation_domain LIKE ? OR apl_installations.installation_ip LIKE ?)
        ORDER BY installation_date DESC, installation_id DESC LIMIT ?", array($date_from, $date_to, $search_keyword, $search_keyword, $search_keyword, $search_keyword, $results_limit), array("s", "s", "s", "s", "s", "s", "i"));*/
        }
    else
        {
        $rows_array= DB::table('afl_installations')
                     ->leftJoin('afl_products','afl_installations.product_id','=','afl_products.product_id')
                     ->leftJoin('afl_clients','afl_installations.client_id','=','afl_clients.client_id')
                     ->where('afl_installations.product_id','=' ,$product_id)
                     ->where('afl_installations.installation_date','>=',$date_from)
                     ->where('afl_installations.installations_date','<=' , $date_to)
                     ->orderBy('installation_date','desc')
                     ->orderBy('insatalltion_id','desc')->get();
        /*fetchRow("SELECT * FROM apl_installations
        LEFT JOIN apl_products
        ON apl_installations.product_id=apl_products.product_id
        LEFT JOIN apl_clients ON apl_installations.client_id=apl_clients.client_id
        WHERE apl_installations.product_id=? AND apl_installations.installation_date>=? AND apl_installations.installation_date<=?
        ORDER BY installation_date DESC, installation_id DESC", array($product_id, $date_from, $date_to), array("i", "s", "s"));*/
        }
    foreach ($rows_array as $row)
        {
        foreach ($row as $key=>$value)
            {
            $item_array[$key]=$value;
            }

        $item_array['client_formatted']=formatClient($item_array['license_code'], $item_array['client_email']);
        $item_array['installation_status_formatted']=returnFormattedStatusArray($item_array['installation_status'], "Active", "Inactive", "Unknown");

        $root_array[]=$item_array;
        }

    return $root_array;
    }


    //return licenses
public function returnLicensesArray($product_id, $search_keyword="", $results_limit=0)
    {
    $root_array=array();

    if (!empty($search_keyword) && aflValidateIntegerValue($results_limit))
        {
        $search_keyword="%$search_keyword%"; //add wildcards

        $rows_array=DB::table('afl_licenses')
                    ->select('afl_license.*', 
                     DB::raw("SELECT COUNT(*) FROM afl_installations WHERE afl_licenses.product_id=afl_installations.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_installations.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_installations.license_code) AS total_installations"),
                     DB::raw("SELECT callback_date_time FROM afl_callbacks WHERE afl_licenses.product_id=afl_callbacks.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_callbacks.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_callbacks.license_code) ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time"))
                    ->join('afl_products','afl_licenses.product_id','=','afl_products.product_id')
                    ->leftJoin('afl_clients','afl_licenses.client_id','=','afl_clients.client_id')
                    ->where('afl_licenses.license_code'.'like' ,$search_keyword)
                    ->orWhere('afl_clients.client_email','like',$search_keyword)
                    ->orWhere('afl_licenses.license_comments','like',$search_keyword)
                    ->orderBy(' license_date','desc')
                    ->orderBy(' license_id','desc')
                    ->limit($results_limit)->get();

        /*fetchRow("SELECT *,
        (SELECT COUNT(*) FROM apl_installations WHERE apl_licenses.product_id=apl_installations.product_id AND (apl_licenses.client_id IS NOT NULL AND apl_licenses.client_id=apl_installations.client_id OR apl_licenses.client_id IS NULL AND apl_licenses.license_code IS NOT NULL AND apl_licenses.license_code=apl_installations.license_code)) AS total_installations,
        (SELECT callback_date_time FROM apl_callbacks WHERE apl_licenses.product_id=apl_callbacks.product_id AND (apl_licenses.client_id IS NOT NULL AND apl_licenses.client_id=apl_callbacks.client_id OR apl_licenses.client_id IS NULL AND apl_licenses.license_code IS NOT NULL AND apl_licenses.license_code=apl_callbacks.license_code) ORDER BY apl_callbacks.callback_date_time DESC, apl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time
        FROM apl_licenses
        JOIN apl_products
        ON apl_licenses.product_id=apl_products.product_id
        LEFT JOIN apl_clients
        ON apl_licenses.client_id=apl_clients.client_id
        WHERE apl_licenses.license_code LIKE ? OR apl_clients.client_email LIKE ? OR apl_licenses.license_comments LIKE ?
        ORDER BY license_date DESC, license_id DESC LIMIT ?", array($search_keyword, $search_keyword, $search_keyword, $results_limit), array("s", "s", "s", "i"));*/
        }
    else
        {
        $rows_array= DB::table('afl_licenses')
                     ->select('afl_licenses.*',
                              DB::raw("SELECT COUNT(*) FROM apl_installations WHERE apl_licenses.product_id=apl_installations.product_id AND (apl_licenses.client_id IS NOT NULL AND apl_licenses.client_id=apl_installations.client_id OR apl_licenses.client_id IS NULL AND apl_licenses.license_code IS NOT NULL AND apl_licenses.license_code=apl_installations.license_code) AS total_installations"),
                              DB::raw("SELECT callback_date_time FROM apl_callbacks WHERE apl_licenses.product_id=apl_callbacks.product_id AND (apl_licenses.client_id IS NOT NULL AND apl_licenses.client_id=apl_callbacks.client_id OR apl_licenses.client_id IS NULL AND apl_licenses.license_code IS NOT NULL AND apl_licenses.license_code=apl_callbacks.license_code) ORDER BY apl_callbacks.callback_date_time DESC, apl_callbacks.callback_id DESC LIMIT 1 AS latest_callback_date_time")
                        )->join('afl_products','apl_licenses.product_id','=','apl_products.product_id')
                        ->leftJoin('afl_clients','afl_licenses.client_id','=','afl_clients.client_id')
                        ->where('afl_licenses.product_id',$product_id)
                        ->orderBy('license_date','desc')
                        ->orderBy('license_id','desc')
                        ->get();

        /*fetchRow("SELECT *,
        (SELECT COUNT(*) FROM apl_installations WHERE apl_licenses.product_id=apl_installations.product_id AND (apl_licenses.client_id IS NOT NULL AND apl_licenses.client_id=apl_installations.client_id OR apl_licenses.client_id IS NULL AND apl_licenses.license_code IS NOT NULL AND apl_licenses.license_code=apl_installations.license_code)) AS total_installations,
        (SELECT callback_date_time FROM apl_callbacks WHERE apl_licenses.product_id=apl_callbacks.product_id AND (apl_licenses.client_id IS NOT NULL AND apl_licenses.client_id=apl_callbacks.client_id OR apl_licenses.client_id IS NULL AND apl_licenses.license_code IS NOT NULL AND apl_licenses.license_code=apl_callbacks.license_code) ORDER BY apl_callbacks.callback_date_time DESC, apl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time
        FROM apl_licenses
        JOIN apl_products
        ON apl_licenses.product_id=apl_products.product_id
        LEFT JOIN apl_clients ON apl_licenses.client_id=apl_clients.client_id
        WHERE apl_licenses.product_id=?
        ORDER BY license_date DESC, license_id DESC", array($product_id), array("i"));*/
        }
    foreach ($rows_array as $row)
        {
        foreach ($row as $key=>$value)
            {
            $item_array[$key]=$value;
            }

        if (!aflValidateIntegerValue($item_array['license_limit']))
            {
            $item_array['license_limit']="";
            }

        if (aflVerifyDateTime($item_array['license_expire_date'], "Y-m-d") && $item_array['license_expire_date']<=date("Y-m-d")) //expired status will be formatted
            {
            $item_array['license_status']=2;
            }

        if (!aflVerifyDateTime($item_array['license_expire_date'], "Y-m-d"))
            {
            $item_array['license_expire_date']="";
            }

        if (!aflVerifyDateTime($item_array['license_updates_date'], "Y-m-d"))
            {
            $item_array['license_updates_date']="";
            }

        if (!aflVerifyDateTime($item_array['license_support_date'], "Y-m-d"))
            {
            $item_array['license_support_date']="";
            }

        $item_array['client_formatted']=formatClient($item_array['license_code'], $item_array['client_email']);
        $item_array['latest_callback_date_time']=removeSeconds($item_array['latest_callback_date_time']);
        $item_array['license_status_formatted']=returnFormattedStatusArray($item_array['license_status'], "Active", "Inactive", "Expired");

        $root_array[]=$item_array;
        }

    return $root_array;
    }


    //return products
public function returnProductsArray($search_keyword="", $results_limit=0)
    {
    $root_array=array();

    if (!empty($search_keyword) && aflValidateIntegerValue($results_limit))
        {
        $search_keyword="%$search_keyword%"; //add wildcards

        $rows_array=DB::table('afl_products')
                    ->join('afl_licenses' ,'afl_products.product_id','=','afl_licenses.product_id')
                    ->join('afl_installations','afl_products.product_id','=','afl_installations.product_id')
                    ->join('afl_callbacks','afl_products.product_id','=','afl_callbacks.product_id')
                    ->join('afl_reports','afl_products.product_id','=','afl_reports.product_id')
                    ->select('afl_products.*',
                             DB::raw('afl_licenses.count(*) AS  total_licenses'),
                             DB::raw('afl_installations.count(*) AS total_installations'),
                             DB::raw('afl_callbacks.count(*) AS total_callbacks'),
                             DB::raw('afl_reports.count(*) AS total_reports'),
                             DB::raw("SELECT license_date FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id ORDER BY afl_licenses.license_date DESC, afl_licenses.license_id DESC LIMIT 1 AS latest_license_date"),
                             DB::raw("SELECT installation_date FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id ORDER BY afl_installations.installation_date DESC, afl_installations.installation_id DESC LIMIT 1 AS latest_installation_date"),
                             DB::raw("SELECT callback_date_time FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1 AS latest_callback_date_time"),
                             DB::raw("SELECT report_date_time FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id ORDER BY afl_reports.report_date_time DESC, afl_reports.report_id DESC LIMIT 1 AS latest_report_date_time")
                            )->where('product_title','like',$search_keyword)
                            ->where('product_sku','like',$search_keyword)
                            ->orderBy('product_title')
                            ->limit($results_limit)->get();
        /*fetchRow("SELECT *,
        (SELECT COUNT(*) FROM apl_licenses WHERE apl_products.product_id=apl_licenses.product_id) AS total_licenses,
        (SELECT COUNT(*) FROM apl_installations WHERE apl_products.product_id=apl_installations.product_id) AS total_installations,
        (SELECT COUNT(*) FROM apl_callbacks WHERE apl_products.product_id=apl_callbacks.product_id) AS total_callbacks,
        (SELECT COUNT(*) FROM apl_reports WHERE apl_products.product_id=apl_reports.product_id) AS total_reports,
        (SELECT license_date FROM apl_licenses WHERE apl_products.product_id=apl_licenses.product_id ORDER BY apl_licenses.license_date DESC, apl_licenses.license_id DESC LIMIT 1) AS latest_license_date,
        (SELECT installation_date FROM apl_installations WHERE apl_products.product_id=apl_installations.product_id ORDER BY apl_installations.installation_date DESC, apl_installations.installation_id DESC LIMIT 1) AS latest_installation_date,
        (SELECT callback_date_time FROM apl_callbacks WHERE apl_products.product_id=apl_callbacks.product_id ORDER BY apl_callbacks.callback_date_time DESC, apl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time,
        (SELECT report_date_time FROM apl_reports WHERE apl_products.product_id=apl_reports.product_id ORDER BY apl_reports.report_date_time DESC, apl_reports.report_id DESC LIMIT 1) AS latest_report_date_time
        FROM apl_products
        WHERE product_title LIKE ? OR product_sku LIKE ?
        ORDER BY product_title LIMIT ?", array($search_keyword, $search_keyword, $results_limit), array("s", "s", "i"));*/
        }
    else
        {
        $rows_array = DB::table('afl_products')
                      ->join('afl_licenses', 'afl_products.product_id','=','afl_licenses.product_id')
                      ->join('afl_installations', 'afl_products.product_id','=','afl_installations.product_id')
                      ->join('afl_callbacks','afl_products.product_id','=','afl_callbacks.product_id')
                      ->join('afl_reports','apl_products.product_id','=','apl_reports.product_id')
                      ->select('afl_products.*',
                             DB::raw('afl_licenses.count(*) AS  total_licenses'),
                             DB::raw('afl_installations.count(*) AS total_installations'),
                             DB::raw('afl_callbacks.count(*) AS total_callbacks'),
                             DB::raw('afl_reports.count(*) AS total_reports'),
                             DB::raw("SELECT license_date FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id ORDER BY afl_licenses.license_date DESC, apf_licenses.license_id DESC LIMIT 1 AS latest_license_date"),
                             DB::raw("SELECT installation_date FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id ORDER BY afl_installations.installation_date DESC, afl_installations.installation_id DESC LIMIT 1 AS latest_installation_date"),
                             DB::raw("SELECT callback_date_time FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1 AS latest_callback_date_time"),
                             DB::raw("SELECT report_date_time FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id ORDER BY afl_reports.report_date_time DESC, afl_reports.report_id DESC LIMIT 1 AS latest_report_date_time")
                            )->orderBy('product_title')->get();
        /*fetchRow("SELECT *,
        (SELECT COUNT(*) FROM apl_licenses WHERE apl_products.product_id=apl_licenses.product_id) AS total_licenses,
        (SELECT COUNT(*) FROM apl_installations WHERE apl_products.product_id=apl_installations.product_id) AS total_installations,
        (SELECT COUNT(*) FROM apl_callbacks WHERE apl_products.product_id=apl_callbacks.product_id) AS total_callbacks,
        (SELECT COUNT(*) FROM apl_reports WHERE apl_products.product_id=apl_reports.product_id) AS total_reports,
        (SELECT license_date FROM apl_licenses WHERE apl_products.product_id=apl_licenses.product_id ORDER BY apl_licenses.license_date DESC, apl_licenses.license_id DESC LIMIT 1) AS latest_license_date,
        (SELECT installation_date FROM apl_installations WHERE apl_products.product_id=apl_installations.product_id ORDER BY apl_installations.installation_date DESC, apl_installations.installation_id DESC LIMIT 1) AS latest_installation_date,
        (SELECT callback_date_time FROM apl_callbacks WHERE apl_products.product_id=apl_callbacks.product_id ORDER BY apl_callbacks.callback_date_time DESC, apl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time,
        (SELECT report_date_time FROM apl_reports WHERE apl_products.product_id=apl_reports.product_id ORDER BY apl_reports.report_date_time DESC, apl_reports.report_id DESC LIMIT 1) AS latest_report_date_time
        FROM apl_products
        ORDER BY product_title");*/
        }
    foreach ($rows_array as $row)
        {
        foreach ($row as $key=>$value)
            {
            $item_array[$key]=$value;
            }

        $item_array['latest_callback_date_time']=removeSeconds($item_array['latest_callback_date_time']);
        $item_array['latest_report_date_time']=removeSeconds($item_array['latest_report_date_time']);
        $item_array['product_status_formatted']=returnFormattedStatusArray($item_array['product_status']);

        $root_array[]=$item_array;
        }

    return $root_array;
    }

    
//return license reports
public function returnLicenseReportsArray($product_id, $date_from="", $date_to="", $search_keyword="", $results_limit=0)
    {
    $root_array=array();

    if (!aflVerifyDateTime($date_from, "Y-m-d"))
        {
        $date_from="0000-00-00";
        }

    if (!aflVerifyDateTime($date_to, "Y-m-d"))
        {
        $date_to="9999-12-31";
        }

    $date_to.=" 23:59:59"; //include all records of last specified day

    if (!empty($search_keyword) && aflValidateIntegerValue($results_limit))
        {
        $search_keyword="%$search_keyword%"; //add wildcards

        $rows_array=DB::table('afl_reports')
                     ->leftJoin('afl_clients','afl_reports.account_id','=','afl_clients.client_id')
                     ->orWhere('afl_reports.report_text','like' ,$search_keyword)
                     ->orWhere('afl_reports.license_code','like',$search_keyword)
                     ->orWhere('afl_clients.client_email','like' , $search_keyword)
                     ->where('afl_reports.report_system','=' ,0)
                     ->where('afl_reports.report_date_time','>=', $date_from)
                     ->where('afl_reports.report_date_time','<=', $date_to)
                     ->orderBy('report_date_time','desc')
                     ->orderBy('report_id','desc')
                     ->limit($results_limit)->get();
        /*fetchRow("SELECT * FROM apl_reports
        LEFT JOIN apl_clients
        ON apl_reports.account_id=apl_clients.client_id
        WHERE (apl_reports.report_text LIKE ? OR apl_reports.license_code LIKE ? OR apl_clients.client_email LIKE ?) AND apl_reports.report_system=? AND apl_reports.report_date_time>=? AND apl_reports.report_date_time<=?
        ORDER BY report_date_time DESC, report_id DESC LIMIT ?", array($search_keyword, $search_keyword, $search_keyword, 0, $date_from, $date_to, $results_limit), array("s", "s", "s", "i", "s", "s", "i"));*/
        }
    else
        {
        $rows_array= DB::table('afl_reports')
                   ->leftJoin('afl_clients',' afl_reports.account_id','=','afl_clients.client_id')
                   ->where('afl_reports.product_id','=', $product_id )
                   ->where('afl_reports.report_system','=' ,0)
                   ->where('afl_reports.report_date_time','>=' ,$date_from)
                   ->where('afl_reports.report_date_time','<=', $date_to )
                   ->orderBy('report_date_time', 'desc')
                   ->orderBy('report_id','desc')->get();
        /*fetchRow("SELECT * FROM apl_reports
        LEFT JOIN apl_clients ON apl_reports.account_id=apl_clients.client_id
        WHERE apl_reports.product_id=? AND apl_reports.report_system=? AND apl_reports.report_date_time>=? AND apl_reports.report_date_time<=?
        ORDER BY report_date_time DESC, report_id DESC", array($product_id, 0, $date_from, $date_to), array("i", "i", "s", "s"));*/
        }
    foreach ($rows_array as $row)
        {
        foreach ($row as $key=>$value)
            {
            $item_array[$key]=$value;
            }

        $item_array['client_formatted']=formatClient($item_array['license_code'], $item_array['client_email']);
        $item_array['report_date_time']=removeSeconds($item_array['report_date_time']);
        $item_array['report_status_formatted']=returnFormattedReportStatusArray($item_array['report_status']);

        $root_array[]=$item_array;
        }

    return $root_array;
    }

}


