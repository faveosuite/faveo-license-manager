<?php

namespace App\Http\Controllers\Admin;

use App\Models\AflLicenses;
use App\Models\AflApiKeys;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Settings;
use App\Traits\Version;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Http\Requests\LicenseRequest;


/**
 * Consist of functionalities for the License page in Auto Faveo licenser 
 * Class LicenseController
 * @package App\Http\Controllers\Admin
 */
class LicenseController extends Controller
{

/**
 * To Add license Details to the license manager via request or entering them, it can be added with client id or anonymously
 * @param LicenseRequest $request 
 * @param $api_key_secret 
 * @param $product_id 
 * @param $license_require_domain 
 * @param $license_status 
 * @param $client_id  
 * @param $license_code 
 * @param $license_order_number 
 * @param $license_ip 
 * @param $license_domain 
 * @param $license_limit 
 * @param $license_expire_date 
 * @param $license_updates_date 
 * @param $license_support_date 
 * @param $license_comments
 * @return the details that has been added with a response 
 */
public function licenseAdd(LicenseRequest $request){



    $api_key_secret = $request->get('api_key_secret'); 
    $product_id = $request->get('product_id');
    $license_require_domain = $request->get('license_require_domain');
    $license_status = $request->get('license_status');
    $client_id = $request->get('client_id'); 
    $license_code = $request->get('license_code');  
    $license_order_number = $request->get('license_order_number');
    $license_ip = $request->get('license_ip');
    $license_domain = $request->get('license_domain'); 
    $license_limit = $request->get('license_limit'); 
    $license_expire_date = $request->get('license_expire_date'); 
    $license_updates_date = $request->get('license_updates_date'); 
    $license_support_date = $request->get('license_support_date'); 
    $license_comments = $request->get('license_comments');

         
        $api_action_success=0;
        $api_error_detected=0; 
        $added_records = 0; 

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
        $api_ips= $api_ip->pluck('api_key_ip');

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

    
    if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($license_require_domain, 0, 1) && aflValidateIntegerValue($license_status, 0, 2))
                {
                
                if (empty($client_id) || !aflValidateIntegerValue($client_id)) //in case no client_id was submitted, its value must be stored as NULL in database
                    {
                    $client_id=null;
                    }
               
                if (empty($license_code)) //in case no license_code was submitted, its value must be stored as NULL in database
                    {
                    $license_code=null;
                    }
                    

                if (!aflValidateIntegerValue($client_id) && empty($license_code))
                    {
                    $api_error_detected=1;
                    return errorResponse(Lang::get('lang.error_client_or_license_code'),400);
                    
                    }

                if (aflValidateIntegerValue($client_id) && !empty($license_code))
                    { 
                    $api_error_detected=1;
                    return errorResponse(Lang::get('lang.invalid_licnese'),400);
                    }

                if (!empty($license_ip))
                    {
                    $license_ips_array=explode(",", $license_ip);
                    foreach ($license_ips_array as $ip_to_validate)
                        {
                        if (!filter_var($ip_to_validate, FILTER_VALIDATE_IP))
                            {
                            $api_error_detected=1;
                            return errorResponse(Lang::get('lang.invalid_license_ip'),400);
                            break;
                            }
                        }
                    }

                if (!empty($license_domain))
                    {
                    $license_domain_array=explode(",", $license_domain);
                    foreach ($license_domain_array as $license_domain_array_key=>$license_domain_array_value)
                        {
                        if (!aflValidateRawDomain(aflGetRawDomain($license_domain_array_value)) || !ctype_alnum(substr($license_domain_array_value, -1))) //invalid TLD, scheme included, or last symbol is not alphanumeric (most likely ends with / or another non-alphanumeric character)
                            {
                            $api_error_detected=1;
                            return errorResponse(Lang::get('lang.invalid_domain'),400);
                            break;
                            }
                        }
                    }

                if (!empty($license_limit) && !aflValidateIntegerValue($license_limit))
                    {
                      $api_error_detected=1;
                      return errorResponse(Lang::get('lang.invalid_license_limit'),400);
                    }

                if (!empty($license_expire_date) && !aflVerifyDateTime($license_expire_date, "Y-m-d"))
                    {
                        $api_error_detected=1;
                        return errorResponse(Lang::get('lang.invalid_license_expiry'),400);
                    }

                if (!empty($license_updates_date) && !aflVerifyDateTime($license_updates_date, "Y-m-d"))
                    {
                        $api_error_detected=1;
                        return errorResponse(Lang::get('lang.invalid_license_update_date'),400);
                    }

                if (!empty($license_support_date) && !aflVerifyDateTime($license_support_date, "Y-m-d"))
                    {
                     $api_error_detected=1;
                     return errorResponse(Lang::get('lang.invalid_license_support_date'),400);
                    }

                if ($api_error_detected!=1)
                    {
                    
                    $license_date=date("Y-m-d");

                    if (empty($license_envato) || !aflValidateIntegerValue($license_envato))
                        {
                        $license_envato=0;
                        }

                    if ($license_status==1)
                        {
                        $license_cancel_date="0000-00-00";
                    
                        }
                    else
                        {
                        if (empty($license_cancel_date) || !aflVerifyDateTime($license_cancel_date, "Y-m-d")) //set cancel date to now only if license is inactive and no previous cancel date set
                            {
                            $license_cancel_date=date("Y-m-d");
                            
                            }
                        }
                        $license_expire_email_date = $license_expire_date;
                        $license_updates_email_date = $license_updates_date;
                        $license_support_email_date = $license_support_date;
                        //dd($client_id,$license_code,$product_id,$license_order_number,$license_ip,$license_domain,$license_require_domain,$license_limit,$license_date);

                            try{
                                   DB::table('afl_licenses')
                                      ->insertOrIgnore(array(
                                       'client_id' => $client_id,
                                       'license_code' =>$license_code,
                                       'product_id' => $product_id,
                                       'license_order_number' =>$license_order_number,
                                       'license_ip' => $license_ip,
                                       'license_domain'=> $license_domain,
                                       'license_require_domain' => $license_require_domain,
                                       'license_limit' => $license_limit,
                                       'license_date' =>  $license_date,
                                       'license_cancel_date' => $license_cancel_date,
                                       'license_expire_date' => $license_expire_date,
                                       'license_updates_date' =>$license_updates_date,
                                       'license_support_date' => $license_support_date,
                                       'license_expire_email_date' => $license_expire_email_date,
                                       'license_updates_email_date' => $license_updates_email_date,
                                       'license_support_email_date' => $license_support_email_date,
                                       'license_comments' => $license_comments,
                                       'license_envato'=> $license_envato,
                                       'license_status' =>$license_status,
                                   ));
                                   
                                   $added_records+=1; 
                                   //doMysqlQuery("INSERT IGNORE INTO apl_licenses (client_id, license_code, product_id, license_order_number, license_ip, license_domain, license_require_domain, license_limit, license_date, license_cancel_date, license_expire_date, license_updates_date, license_support_date, license_comments, license_envato, license_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($client_id, $license_code, $product_id, $license_order_number, $license_ip, $license_domain, $license_require_domain, $license_limit, $license_date, $license_cancel_date, $license_expire_date, $license_updates_date, $license_support_date, $license_comments, $license_envato, $license_status), array("i", "s", "i", "s", "s", "s", "i", "i", "s", "s", "s", "s", "s", "s", "i", "i"));
                                   }
                                   catch(\Exception $e){
                                       $added_records +=0;
                                   }
                                 
                    if (!aflValidateIntegerValue($added_records))
                        {
                         $api_error_detected=1;
                         return errorResponse(Lang::get('lang.invalid_record_data'),400);
                        }
                    else
                        {
                        $action_success=1;

                        if (aflValidateIntegerValue($license_id=DB::getPDO()->lastInsertId()))
                            {
                         
                            foreach($rows_array=AflLicenses::leftJoin('afl_products', 'afl_licenses.product_id','=', 'afl_products.product_id')
                                              ->leftJoin('afl_clients', 'afl_licenses.client_id','=', 'afl_clients.client_id')
                                              ->where('afl_licenses.license_id',$license_id)
                                              ->get()->toArray() as $row)  
                                              //fetchRow("SELECT * FROM apl_licenses LEFT JOIN apl_products ON apl_licenses.product_id=apl_products.product_id LEFT JOIN apl_clients ON apl_licenses.client_id=apl_clients.client_id WHERE apl_licenses.license_id=?", array($license_id), array("i")) as $row) //fetch product and client details to use in reports
                                {
                                extract($row);
                            
                            
                                }
                            $client_formatted=formatClient($license_code, $row['client_email']);
                            
                            return successResponse(Lang::get('lang.success'),$client_formatted,201);
                            }
                        }
                    }
                }
                else
                {
                    return errorResponse(Lang::get('lang.invalid'),400);
                }
       }
}



/**
 * To Update license Details to the license manager via request or entering them, it can be added with client id or anonymously
 * @param LicenseRequest $request 
 * @param $api_key_secret 
 * @param $product_id 
 * @param $license_id
 * @param $license_require_domain 
 * @param $license_status 
 * @param $client_id  
 * @param $license_code 
 * @param $license_order_number 
 * @param $license_ip 
 * @param $license_domain 
 * @param $license_limit 
 * @param $license_expire_date 
 * @param $license_updates_date 
 * @param $license_support_date 
 * @param $license_comments
 * @return the number of records that has been Updated with a response 
 */
public function licenseUpdate(LicenseRequest $request)
{

    $api_key_secret = $request->get('api_key_secret'); 
    $license_id = $request->get('license_id');
    $product_id = $request->get('product_id');
    $license_require_domain = $request->get('license_require_domain');
    $license_status = $request->get('license_status');
    $client_id = $request->get('client_id'); 
    $license_code = $request->get('license_code');  
    $license_order_number = $request->get('license_order_number');
    $license_ip = $request->get('license_ip');
    $license_domain = $request->get('license_domain'); 
    $license_limit = $request->get('license_limit'); 
    $license_expire_date = $request->get('license_expire_date'); 
    $license_updates_date = $request->get('license_updates_date'); 
    $license_support_date = $request->get('license_support_date'); 
    $license_comments = $request->get('license_comments');
    
if (empty($license_id) || !aflValidateIntegerValue($license_id) || empty($rows_array=AflLicenses::where('license_id',$license_id)->get()))/*fetchRow("SELECT * FROM apl_licenses WHERE license_id=?", array($license_id), array("i"))*///invalid record
    {
    return errorResponse(Lang::get('lang.license_id'));
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
        $api_ips= $api_ip->pluck('api_key_ip');

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

         

         if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($license_require_domain, 0, 1) && aflValidateIntegerValue($license_status, 0, 2))
                {
                if (empty($client_id) || !aflValidateIntegerValue($client_id)) //in case no client_id was submitted, its value must be stored as NULL in database
                    {
                    $client_id=null;
                    }

                if (empty($license_code)) //in case no license_code was submitted, its value must be stored as NULL in database
                    {
                    $license_code=null;
                    }

                if (!aflValidateIntegerValue($client_id) && empty($license_code))
                    {
                    $api_error_detected=1;
                    return errorResponse(Lang::get('lang.errorclient_or_license_code'),400);
                    }

                if (aflValidateIntegerValue($client_id) && !empty($license_code))
                    {
                     $api_error_detected=1;
                     return errorResponse(Lang::get('lang.invalid_licnese'),400);
                    }

                if (!empty($license_ip))
                    {
                    $license_ips_array=explode(",", $license_ip);
                    foreach ($license_ips_array as $ip_to_validate)
                        {
                        if (!filter_var($ip_to_validate, FILTER_VALIDATE_IP))
                            {
                            $api_error_detected=1;
                            return errorResponse(Lang::get('lang.invalid_licnese_ip'),400);
                            break;
                            }
                        }
                    }

                if (!empty($license_domain))
                    {
                    $license_domain_array=explode(",", $license_domain);
                    foreach ($license_domain_array as $license_domain_array_key=>$license_domain_array_value)
                        {
                        if (!aflValidateRawDomain(aflGetRawDomain($license_domain_array_value)) || !ctype_alnum(substr($license_domain_array_value, -1))) //invalid TLD, scheme included, or last symbol is not alphanumeric (most likely ends with / or another non-alphanumeric character)
                            {
                                $api_error_detected=1;
                                return errorResponse(Lang::get('lang.invalid_licnese_domain'),400);
                            break;
                            }
                        }
                    }

                 if (!empty($license_limit) && !aflValidateIntegerValue($license_limit))
                    {
                      $api_error_detected=1;
                      return errorResponse(Lang::get('lang.invalid_license_limit'),400);
                    }

                if (!empty($license_expire_date) && !aflVerifyDateTime($license_expire_date, "Y-m-d"))
                    {
                        $api_error_detected=1;
                        return errorResponse(Lang::get('lang.invalid_license_expiry'),400);
                    }

                if (!empty($license_updates_date) && !aflVerifyDateTime($license_updates_date, "Y-m-d"))
                    {
                        $api_error_detected=1;
                        return errorResponse(Lang::get('lang.invalid_license_update_date'),400);
                    }

                if (!empty($license_support_date) && !aflVerifyDateTime($license_support_date, "Y-m-d"))
                    {
                     $api_error_detected=1;
                     return errorResponse(Lang::get('lang.invalid_license_support_date'),400);
                    }

                if ($api_error_detected!=1)
                    {
                    if (!empty($license_expire_date) && aflVerifyDateTime($license_expire_date, "Y-m-d") && $license_expire_date!=$rows_array[0]['license_expire_date']) //license_expire_date changed, reset license_expire_email_date, so client can receive new notification
                        {
                        $license_expire_email_date="0000-00-00";
                        }
                    else
                        {
                        $license_expire_email_date=$rows_array[0]['license_expire_email_date']; //use old license_expire_email_date
                        }

                    if (!empty($license_updates_date) && aflVerifyDateTime($license_updates_date, "Y-m-d") && $license_updates_date!=$rows_array[0]['license_updates_date']) //license_updates_date changed, reset license_updates_email_date, so client can receive new notification
                        {
                        $license_updates_email_date="0000-00-00";
                        }
                    else
                        {
                        $license_updates_email_date=$rows_array[0]['license_updates_email_date']; //use old license_updates_email_date
                        }

                    if (!empty($license_support_date) && alfVerifyDateTime($license_support_date, "Y-m-d") && $license_support_date!=$rows_array[0]['license_support_date']) //license_support_date changed, reset license_support_email_date, so client can receive new notification
                        {
                        $license_support_email_date="0000-00-00";
                        }
                    else
                        {
                        $license_support_email_date=$rows_array[0]['license_support_email_date']; //use old license_support_email_date
                        }

                    if (empty($license_envato) || !aflValidateIntegerValue($license_envato))
                        {
                        $license_envato=0;
                        }

                    if ($license_status==1)
                        {
                        $license_cancel_date="0000-00-00";
                        //dd($license_cancel_date);
                        }
                    else
                        {
                        $license_cancel_date=$rows_array[0]['license_cancel_date']; //use old license_cancel_date if license was deactivated previously and its status wasn't changed now
                        if (empty($license_cancel_date) || !aflVerifyDateTime($license_cancel_date, "Y-m-d")) //set cancel date to now only if no previous cancel date set
                            {
                            $license_cancel_date=date("Y-m-d");
                            
                            }
                        }

                    $updated_records+=AflLicenses::where('license_id',$license_id)
                                     ->update([
                                        'license_order_number'=> $license_order_number, 
                                        'license_ip'=> $license_ip, 
                                        'license_domain'=> $license_domain, 
                                        'license_require_domain'=> $license_require_domain, 
                                        'license_limit'=> $license_limit, 
                                        'license_cancel_date'=> $license_cancel_date, 
                                        'license_expire_date'=> $license_expire_date, 
                                        'license_expire_email_date'=>$license_expire_date,
                                        'license_updates_date'=> $license_updates_date, 
                                        'license_updates_email_date'=> $license_updates_email_date,
                                        'license_support_date'=> $license_support_date, 
                                        'license_support_email_date'=> $license_support_email_date, 
                                        'license_comments'=> $license_comments,
                                        'license_envato'=> $license_envato,
                                        'license_status'=> $license_status
                                         ]);  
                                         //doMysqlQuery("UPDATE apl_licenses SET license_order_number=?, license_ip=?, license_domain=?, license_require_domain=?, license_limit=?, license_cancel_date=?, license_expire_date=?, license_expire_email_date=?, license_updates_date=?, license_updates_email_date=?, license_support_date=?, license_support_email_date=?, license_comments=?, license_envato=?, license_status=? WHERE license_id=?", array($license_order_number, $license_ip, $license_domain, $license_require_domain, $license_limit, $license_cancel_date, $license_expire_date, $license_expire_email_date, $license_updates_date, $license_updates_email_date, $license_support_date, $license_support_email_date, $license_comments, $license_envato, $license_status, $license_id), array("s", "s", "s", "i", "i", "s", "s", "s", "s", "s", "s", "s", "s", "i", "i", "i"));
               
                    if (!aflValidateIntegerValue($updated_records))
                        {
                        $api_error_detected=1;
                         return errorResponse(Lang::get('lang.invalid_record_data'),400);
                        }
                    else
                        {
                        $api_action_success=1;

                        foreach ($rows_array= AflLicenses::leftJoin('afl_products','afl_licenses.product_id','=', 'afl_products.product_id')
                                              ->leftJoin('afl_clients', 'afl_licenses.client_id','=', 'afl_clients.client_id')
                                              ->where('afl_licenses.license_id',$license_id)
                                              ->get()->toArray() as $row) //fetch product and client details to use in reports
                            {
                        
                            extract($row);
                            }

                        $client_formatted=formatClient($license_code, $row['client_email']);
                        return successResponse(Lang::get('lang.license_Update'),$client_formatted,200);
                        }
                    }
                }
                else{
                    return errorResponse(Lang::get('lang.invalid'),400);
                }

    }

}

/**
 * To delete the license stored in the license manager
 * @param $license_id
 * @return the removed records with a success response
 */
public function deleteLicense(LicenseRequest $request)
    {
        
    $api_error_detected=0;
    $api_action_success=0;
    $removed_records=0;
    $license_id = $request->get('license_id');
    $api_key_secret=$request->get('api_key_secret');
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
        $api_ips= $api_ip->pluck('api_key_ip');

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
    if (aflValidateIntegerValue($license_id))
        {
        $removed_records+=AflLicenses::where('license_id',$license_id)->delete();
        //doMysqlQuery("DELETE FROM apl_licenses WHERE license_id=?", array($license_id), array("i"));
        }

    return successResponse(LAng::get('lang.delete'),$removed_records,200);
}
    }




/**
 * To Format the client
 * @param $license_code
 * @param $client_email
 * return a formatted array of license code and client email
 */
public function formatClient($license_code, $client_email)
    {
    if (!empty($license_code))
        {
        $client_formatted=$license_code;
        }
    else
        {
        if (filter_var($client_email, FILTER_VALIDATE_EMAIL))
            {
            $client_formatted=$client_email;
            }
        else
            {
            $client_formatted="Unknown Client";
            }
        }

    return $client_formatted;
    }
}