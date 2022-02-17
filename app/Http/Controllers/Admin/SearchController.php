<?php

namespace App\Http\Controllers\Admin;

//namespace App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AflApiKeys;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;


class SearchController extends Controller
{


public function search(Request $request){

    $RECORDS_ARCHIVE_DAYS=11;
    $RECORDS_ON_SEARCH_PAGE=1;
    $api_key_secret = $request->get('api_key_secret');
    $search_type = $request->get('search_type');
    $search_keyword = $request->get('search_keyword');
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
            return \errorResponse(Lang::get('lang.invalid_api_key'),404);
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
        if (!in_array($search_type, $SUPPORTED_API_SEARCHES_ARRAY))
            {
            $api_error_detected=1;
            return errorResponse(Lang::get('invalid_search_type'),400);
            }

        if (mb_strlen(trim($search_keyword), "UTF-8")<3)
            {
            $api_error_detected=1;
             return errorResponse(Lang::get('invalid_search_term_min_3_characters'),400);
            }

        if ($api_error_detected!=1)
            {
            if ($search_type=="banned_host")
                {
                $elements_to_unset_array=array(); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=$this->returnBannedHostsArray($date_from, $date_to, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="callback")
                {
                $elements_to_unset_array=array("product_title", "product_description", "product_sku", "product_url_homepage", "product_url_download", "product_date", "product_version", "product_envato_id", "product_status", "client_fname", "client_lname", "client_email",  "client_active_date", "client_cancel_date", "client_status", "client_formatted", "callback_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=$this->returnCallbacksArray(0, $date_from, $date_to, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="client")
                {
                $elements_to_unset_array=array("client_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=$this->returnClientsArray($search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="installation")
                {
                $elements_to_unset_array=array("product_title", "product_description", "product_sku", "product_url_homepage", "product_url_download", "product_date", "product_version", "product_envato_id", "product_status", "client_fname", "client_lname", "client_email",  "client_active_date", "client_cancel_date", "client_status", "client_formatted", "installation_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=$this->returnInstallationsArray(0, $date_from, $date_to, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="license")
                {
                $elements_to_unset_array=array("product_title", "product_description", "product_sku", "product_url_homepage", "product_url_download", "product_date", "product_version", "product_envato_id", "product_status", "client_fname", "client_lname", "client_email",  "client_active_date", "client_cancel_date", "client_status", "client_formatted", "license_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=$this->returnLicensesArray(0, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="product")
                {
                $elements_to_unset_array=array("product_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=$this->returnProductsArray($search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if ($search_type=="report")
                {
                $elements_to_unset_array=array("account_id", "client_fname", "client_lname", "client_email", "client_active_date", "client_cancel_date", "client_status", "client_formatted", "report_status_formatted"); //elements to be removed from final array because of security or other reasons for this search type
                $rows_array=$this->returnLicenseReportsArray(0, $date_from, $date_to, $search_keyword, $RECORDS_ON_SEARCH_PAGE);
                }

            if (empty($rows_array))
                {
              $page_message = "There was an error searching for the particular detail in license manager";
              $api_response_array=array("api_action_success"=>$api_action_success, "api_error_detected"=>1, "action_success"=>0, "error_detected"=>1, "page_message"=>$page_message); //make array with response data
              return json_encode($api_response_array);
                }
            else
                {
                $api_action_success=1;
                }
            }

        if ($api_action_success==1) //everything OK
            {
            $this->unsetArrayElements($rows_array, $elements_to_unset_array); //remove unneeded elements (if any). function modifies array directly, use it separately from other functions/arguments
            $page_message=$rows_array;
            //return successResponse(Lang::get('lang.search_complete'),$page_message,200);
            $api_response_array=array("api_action_success"=>$api_action_success, "api_error_detected"=>$api_error_detected, "action_success"=>1, "error_detected"=>0, "page_message"=>$page_message); //make array with response data
            return json_encode($api_response_array);
            }
        else //display error message
            {
              $page_message = "There was an error searching for the particular detail regarding id";
              $api_response_array=array("api_action_success"=>$api_action_success, "api_error_detected"=>$api_error_detected, "action_success"=>0, "error_detected"=>1, "page_message"=>$page_message); //make array with response data
              return json_encode($api_response_array);

            }
        }
        else
        {
            $page_message = "Invalid details has been looked for here";
            $api_response_array=array("api_action_success"=>$api_action_success, "api_error_detected"=>$api_error_detected, "action_success"=>1, "error_detected"=>0, "page_message"=>$page_message); //make array with response data
            return json_encode($api_response_array);
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
                $this->unsetArrayElements($value, $keys_to_unset_array);
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
                    ->orWhere('banned_host_ip','like', $search_keyword)
                    ->orWhere('banned_host_comments','like',$search_keyword)
                    ->orderBy('banned_host_date','desc')
                    ->orderBy('banned_host_id','desc')
                    ->limit($results_limit)->get()->toArray();

        }
    else
        {
        $rows_array=DB::table('afl_banned_hosts')
                     ->where('banned_host_date','>=',$date_from)
                     ->where('banned_host_date','<=',$date_to)
                     ->orderBy('banned_host_date', 'desc')
                     ->orderBy('banned_host_id','desc')
                     ->get()->toArray();
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
                     ->orWhere('afl_clients.client_email', 'like' ,$search_keyword)
                     ->orWhere('afl_callbacks.license_code','like', $search_keyword)
                     ->orWhere('afl_callbacks.callback_domain','like', $search_keyword)
                     ->orWhere('afl_callbacks.callback_ip','like', $search_keyword)
                     ->orderBy('afl_callbacks.callback_date_time','desc')
                     ->orderBy('afl_callbacks.callback_id','desc')
                     ->limit($results_limit)->get()->toArray();

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
                   ->orderBy('afl_callbacks.callback_id','desc')->get()->toArray();

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
                         ->select('afl_clients.*',
                                  DB::raw('(SELECT COUNT(*) FROM afl_licenses WHERE afl_clients.client_id=afl_licenses.client_id) AS total_licenses'),
                                  DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE afl_clients.client_id=afl_installations.client_id) AS total_installations'),


                                  )->orWhere('client_fname','like',$search_keyword)
                                   ->orWhere('client_lname','like',$search_keyword)
                                   ->orWhere('client_email','like',$search_keyword)
                                   ->orderBy('client_fname')->orderBy('client_lname')->limit($results_limit)->get()->toArray();


        }
    else
        {
        $rows_array=DB::table('afl_clients')
                         ->select('afl_clients.*',
                                  DB::raw('(SELECT COUNT(*) FROM afl_licenses WHERE afl_clients.client_id=afl_licenses.client_id) AS total_licenses'),
                                  DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE afl_clients.client_id=afl_installations.client_id) AS total_installations'),

                                  )->orWhere('client_fname','like',$search_keyword)
                                   ->orWhere('client_lname','like',$search_keyword)
                                   ->orWhere('client_email','like',$search_keyword)
                                   ->orderBy('client_fname')->orderBy('client_lname')->get()->toArray();
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
                    ->leftJoin('afl_products','afl_installations.product_id','=','afl_products.product_id')
                    ->leftJoin('afl_clients','afl_installations.client_id','=','afl_clients.client_id')
                    ->where('afl_installations.installation_date','>=',$date_from)
                    ->where('afl_installations.installation_date','<=', $date_to)
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('afl_clients.client_email', 'like', $search_keyword)
                            ->orWhere('afl_installations.license_code', 'like', $search_keyword)
                            ->orWhere('afl_installations.installation_domain', 'like', $search_keyword)
                            ->orWhere('afl_installations.installation_ip', 'like', $search_keyword);
                            })
                    ->orderBy('installation_date','desc')
                    ->orderBy('installation_id','desc')
                    ->get()->toArray(); //showing installation path details

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
                     ->orderBy('insatalltion_id','desc')->get()->toArray();

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
                    ->select('afl_licenses.*','afl_clients.client_email',
                     DB::raw("(SELECT COUNT(*) FROM afl_installations WHERE afl_licenses.product_id=afl_installations.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_installations.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_installations.license_code)) AS total_installations"),
                     DB::raw("(SELECT callback_date_time FROM afl_callbacks WHERE afl_licenses.product_id=afl_callbacks.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_callbacks.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_callbacks.license_code) ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time"))
                    ->join('afl_products','afl_licenses.product_id','=','afl_products.product_id')
                    ->leftJoin('afl_clients','afl_licenses.client_id','=','afl_clients.client_id')
                    ->orWhere('afl_licenses.license_code','like' ,$search_keyword)
                    ->orWhere('afl_clients.client_email','like',$search_keyword)
                    ->orWhere('afl_licenses.license_comments','like',$search_keyword)
                    ->orderBy('license_date','desc')
                    ->orderBy('license_id','desc')
                    ->limit($results_limit)->get()->toArray();

        }
    else
        {
        $rows_array= DB::table('afl_licenses')
                     ->select('afl_licenses.*','afl_clients.client_email',
                              DB::raw("(SELECT COUNT(*) FROM afl_installations WHERE afl_licenses.product_id=afl_installations.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_installations.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_installations.license_code)) AS total_installations"),
                              DB::raw("(SELECT callback_date_time FROM afl_callbacks WHERE afl_licenses.product_id=afl_callbacks.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_callbacks.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_callbacks.license_code) ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time")
                        )->join('afl_products','afl_licenses.product_id','=','afl_products.product_id')
                        ->leftJoin('afl_clients','afl_licenses.client_id','=','afl_clients.client_id')
                        ->where('afl_licenses.product_id',$product_id)
                        ->orderBy('license_date','desc')
                        ->orderBy('license_id','desc')
                        ->get()->toArray();

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

        //$item_array['client_email']= null;
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
                       ->select('afl_products.*',
                                DB::raw('(SELECT COUNT(*) FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id) AS total_licenses'),
                                DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id) AS total_installations'),
                                DB::raw('(SELECT COUNT(*) FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id) AS total_callbacks'),
                                DB::raw('(SELECT COUNT(*) FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id) AS total_reports'),
                                DB::raw('(SELECT license_date FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id ORDER BY afl_licenses.license_date DESC, afl_licenses.license_id DESC LIMIT 1) AS latest_license_date'),
                                DB::raw('(SELECT installation_date FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id ORDER BY afl_installations.installation_date DESC, afl_installations.installation_id DESC LIMIT 1) AS latest_installation_date'),
                                DB::raw('(SELECT callback_date_time FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time'),
                                DB::raw('(SELECT report_date_time FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id ORDER BY afl_reports.report_date_time DESC, afl_reports.report_id DESC LIMIT 1) AS latest_report_date_time'),

                                )->orWhere('product_title','like',$search_keyword)->orWhere('product_sku','like',$search_keyword)

                                ->orderBy('product_title')->limit($results_limit)->get()->toArray();
        }
    else
        {
        $rows_array =DB::table('afl_products')
                       ->select('afl_products.*',
                                DB::raw('(SELECT COUNT(*) FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id) AS total_licenses'),
                                DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id) AS total_installations'),
                                DB::raw('(SELECT COUNT(*) FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id) AS total_callbacks'),
                                DB::raw('(SELECT COUNT(*) FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id) AS total_reports'),
                                DB::raw('(SELECT license_date FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id ORDER BY afl_licenses.license_date DESC, afl_licenses.license_id DESC LIMIT 1) AS latest_license_date'),
                                DB::raw('(SELECT installation_date FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id ORDER BY afl_installations.installation_date DESC, afl_installations.installation_id DESC LIMIT 1) AS latest_installation_date'),
                                DB::raw('(SELECT callback_date_time FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time'),
                                DB::raw('(SELECT report_date_time FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id ORDER BY afl_reports.report_date_time DESC, afl_reports.report_id DESC LIMIT 1) AS latest_report_date_time'),

                                )->orWhere('product_title',$search_keyword)->orWhere('product_sku',$search_keyword)

                                ->orderBy('product_title')->get()->toArray();

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
                     ->limit($results_limit)->get()->toArray();

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
                   ->orderBy('report_id','desc')->get()->toArray();

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


