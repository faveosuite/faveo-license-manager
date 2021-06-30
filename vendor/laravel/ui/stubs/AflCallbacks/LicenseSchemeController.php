<?php

namespace App\Http\Controllers\AflCallbacks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use App\Models\AflLicenseSchemes;
use App\Models\AflInstallations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class LicenseSchemeController extends Controller
{  
    

    /**
     * This is used by script on user's machine to check if license is active and get MySQL scheme for local license storage from Auto PHP Licenser database during installation of protected script
     * api format for example:  http://127.0.0.1:8000/api/licensescheme?product_id=1&root_url=https://www.license.com&client_email=sandeshm40450@gamil.com&license_code=vvbdjvsbjbdvb&installation_hash=aa33da99a04490b20b01c40cced3d2981ca6e29e6fd30ccfd63961a83e798856&license_signature=e930f7623d746b7f81ad4b61af3b7ad1390358529799342eaa1736fa56696dba
     * 
     * @return success response with a message
     * */
    public function licenseScheme(Request $request)
    {
     //Global $ROOT_URL;
     $action_success=0; //will be changed to 1 later only if everything OK
     $error_detected=0; //will be changed to 1 later if error occurs
     $error_details=""; //will be filled with errors (if any)
     $added_records=0;
     $updated_records=0;
     $removed_records=0;


     foreach ($sets_array=DB::select('select * from afl_settings') as $set)
     {
     extract((array)$set);
     }
     $SUPPORTED_BROWSERS_ARRAY=array("Mozilla/5.0 (Windows NT 6.3; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0", "phpmillion Custom Post", "phpmillion cURL","Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
       //get IP, refer and user agent
       if (null!==(request()->server('REMOTE_ADDR'))) 
         {
         $ip_address=request()->server('REMOTE_ADDR');
         } 
         else {
           $ip_address=$request->get('ip_address');
           }
       
       if (null!==(request()->server('HTTP_REFERER'))) 
         {
         $refer=request()->server('HTTP_REFERER');
         } 
         else 
          {
           $refer=$request->get('refer');
           }
         
       if (null!==(request()->server('HTTP_USER_AGENT'))) 
        {
         $user_agent=request()->server('HTTP_USER_AGENT');
         } 
         else 
         {
           $user_agent=$request->get('user_agent');
         
         }


// These are the data that needs to be passed to this function inorder to get a response
      //$root_ips_array=gethostbynamel(aflGetRawDomain($ROOT_URL));
      $product_id = $request->get('product_id');
      $root_url = $request->get('root_url');
      $client_email = $request->get('client_email');
      $license_code = $request->get('license_code');
      $license_signature = $request->get('license_signature');
      $installation_hash = $request->get('installation_hash');
      $client_id = $request->get('client_id');
      $client_fname = $request->get('client_fname');
      $client_lname = $request->get('client_lname');


//check basic data
if (filter_var($ip_address, FILTER_VALIDATE_IP) && in_array($user_agent, $SUPPORTED_BROWSERS_ARRAY) && aflValidateIntegerValue($product_id) && filter_var($root_url, FILTER_VALIDATE_URL) && $root_url==$refer && $installation_hash==hash("sha256", $root_url.$client_email.$license_code) && !empty($license_signature) && isValidLicenseRequest($license_code, $client_email)===true)
    {
    $notification_case="";
    $installation_domain=getRootUrl("$root_url/", 1, 1, 0, 1); //make url without scheme, www. and / at the end because this type of url is stored on server (add / at the end before processing because software stores root url without /)
    $client_formatted=formatClient($license_code, $client_email);

    $product_array=AflProducts::where('product_id',$product_id)->get()->toArray();//fetchRow("SELECT * FROM apl_products WHERE product_id=?", array($product_id), array("i")); //check if product exists, so it's possible to generate reports with product name even if product is inactive or license doesn't exist
    if (empty($product_array)) //product doesn't exist
        {
        $error_detected=1;
        $error_details=setValue($error_details, "product not found");
        $notification_case=setValue($notification_case, "notification_product_not_found");

        $product_title="Unknown Product (ID: $product_id)"; //make $product_title to use in reports for non-existing product
        $product_id=0; //set $product_id to 0 for non-existing product, so this report will be displayed in Unknown Reports section
        }
    else //product exists, do other checks
        {
        foreach ($product_array as $row) //fetch product details
            {
            extract($row);
            }

        if ($product_status!=1) //product inactive
            {
            $error_detected=1;
            $error_details=setValue($error_details, "product inactive");
            $notification_case=setValue($notification_case, "notification_product_inactive");
            }
        else //product active, do other checks
            {
            if (!empty($license_code)) //search for code-based license
                {
                $license_array=AflLicenses::where('license_code',$license_code)
                                 ->where('product_id',$product_id)->get()->toArray();//fetchRow("SELECT * FROM apl_licenses WHERE license_code=? AND product_id=?", array($license_code, $product_id), array("s", "i"));
                }
            else //search for email-based license
                {
                $license_array=AflLicenses::join('afl_clients','afl_licenses.client_id','=','apl_clients.client_id')
                                  ->where('afl_clients.client_email',$client_email)
                                  ->where('afl_clients.client_status',1)
                                  ->where('afl_licenses.product_id',$product_id)
                                  ->get()->toArray(); //fetchRow("SELECT * FROM apl_licenses JOIN apl_clients ON apl_licenses.client_id=apl_clients.client_id AND apl_clients.client_email=? AND apl_clients.client_status=? WHERE apl_licenses.product_id=?", array($client_email, 1, $product_id), array("s", "i", "i"));
                }
            
            if (empty($license_array)) //license doesn't exist
                {
                $error_detected=1;
                $error_details=setValue($error_details, "license not found");
                $notification_case=setValue($notification_case, "notification_license_not_found");
                }
            else //license exists, do other checks
                {
                foreach ($license_array as $row) //fetch license details
                    {
                    extract($row);
                    }

                if (!verifyScriptSignature($license_signature, $product_id, $root_url, $client_email, $license_code)) //invalid signature
                    {
                    $error_detected=1;
                    $error_details=setValue($error_details, "invalid license signature");
                    $notification_case=setValue($notification_case, "notification_invalid_signature");
                    }

                if ($license_status===0) //license cancelled
                    {
                    $error_detected=1;
                    $error_details=setValue($error_details, "license cancelled on $license_cancel_date");
                    $notification_case=setValue($notification_case, "notification_license_cancelled");
                    }

                if ($license_status==2) //license suspended
                    {
                    $error_detected=1;
                    $error_details=setValue($error_details, "license suspended");
                    $notification_case=setValue($notification_case, "notification_license_suspended");
                    }

                if (aflVerifyDateTime($license_expire_date, "Y-m-d") && $license_expire_date<date("Y-m-d")) //license expired
                    {
                    $error_detected=1;
                    $error_details=setValue($error_details, "license expired on $license_expire_date");
                    $notification_case=setValue($notification_case, "notification_license_expired");
                    }

                if (!empty($license_ip))
                    {
                    $license_ips_array=explode(",", str_replace(" ", "", $license_ip)); //remove all space symbols (if any) between IPs
                    if (!in_array($ip_address, $license_ips_array)) //invalid IP
                        {
                        $error_detected=1;
                        $error_details=setValue($error_details, "IP address $ip_address is not licensed");
                        $notification_case=setValue($notification_case, "notification_invalid_ip");
                        }
                    }

                if (!empty($license_domain))
                    {
                    $license_domain_detected=0; //will be changed to 1 later only if everything OK
                    $license_domain_array=explode(",", str_replace(" ", "", $license_domain)); //remove all space symbols (if any) between domains
                    foreach ($license_domain_array as $license_domain_array_key=>$license_domain_array_value)
                        {
                        if (stristr(getRootUrl("$root_url/", 1, 1, 0, 1), $license_domain_array_value)) //check if URL (where script is installed) matches one of allowed URLs (add / at the end before processing because software stores root url without /)
                            {
                            $license_domain_detected=1;
                            break;
                            }
                        }

                    if ($license_domain_detected!=1) //invalid domain
                        {
                        $error_detected=1;
                        $error_details=setValue($error_details, "domain $root_url is not licensed");
                        $notification_case=setValue($notification_case, "notification_invalid_domain");
                        }
                    }
                
                if ($license_require_domain==1) //domain required
                    {
                    if (!filter_var($root_url, FILTER_VALIDATE_URL) || !filter_var(gethostbyname(aflGetRawDomain($root_url)), FILTER_VALIDATE_IP) || filter_var(aflGetRawDomain($root_url), FILTER_VALIDATE_IP)) //script uploaded on invalid domain, domain not resolving to any IP, or local (IP-based) address
                        {
                        $error_detected=1;
                        $error_details=setValue($error_details, "a real domain is required");
                        $notification_case=setValue($notification_case, "notification_domain_required");
                        }
                    }
                
                $this_installation_owner_array=AflInstallations::where('product_id',$product_id)
                                                                ->where('installation_ip',$ip_address)
                                                                ->where('installation_domain',$installation_domain)
                                                                ->get()->toArray();
                                                    //fetchRow("SELECT * FROM apl_installations WHERE product_id=? AND installation_ip=? AND installation_domain=?", array($product_id, $ip_address, $installation_domain), array("i", "s", "s"));
                if (!empty($this_installation_owner_array)) //installation exists, check whom it belongs to
                    {
                        
                    if (!empty($license_code) && $license_code!=$this_installation_owner_array[0]['license_code'] || aflValidateIntegerValue($client_id) && $client_id!=$this_installation_owner_array[0]['client_id']) //this domain is used by another user
                        {      
                        $error_detected=1;
                        $error_details=setValue($error_details, "$product_title installation on $installation_domain ($ip_address) belongs to another user");
                        $notification_case=setValue($notification_case, "notification_domain_in_use");
                        }
                    }
                
                if ($license_limit!=0) //check installations limit
                    {
                    /*$other_installations_array=fetchRow("SELECT * FROM apl_installations WHERE product_id=? AND (client_id=? OR license_code=?) AND (installation_ip!=? OR installation_domain!=?)", array($product_id, $client_id, $license_code, $ip_address, $installation_domain), array("i", "i", "s", "s", "s")); //check if new installation will not exceed limit - THIS PART SHOULD ONLY BE USED IN LICENSE_INSTALL.PHP FILE
                    if (count($other_installations_array)>=$license_limit) //client can't make new installation because it would exceed his current limit
                        {
                        $error_detected=1;
                        $error_details=setValue($error_details, "maximum installations limit ($license_limit) reached");
                        $notification_case=setValue($notification_case, "notification_license_limit");
                        }*/

                    $all_installations_array=AflInstallations::where('product_id',$product_id)
                                                              ->orWhere('client_id',$client_id)
                                                              ->orWhere('license_code',$license_code)->get()->toArray();
                                                             //fetchRow("SELECT * FROM apl_installations WHERE product_id=? AND (client_id=? OR license_code=?)", array($product_id, $client_id, $license_code), array("i", "i", "s")); //check how many installations client has
                    if (count($all_installations_array)>$license_limit) //client has more installations than he is allowed to (most likely limit was changed after installations were made)
                        {
                        $error_detected=1;
                        $error_details=setValue($error_details, "maximum installations limit ($license_limit) exceeded");
                        $notification_case=setValue($notification_case, "notification_license_limit");
                        }
                    }

                /*if (validateDateTime($license_updates_date, "Y-m-d") && $license_updates_date<date("Y-m-d")) //updates expired - THIS PART SHOULD ONLY BE USED IN LICENSE_UPDATES.PHP FILE
                    {
                    //$error_detected=1; don't mark this as hard error, but fill error details, set notification_case and create new variable, so system will know about expiration, but won't mark whole report as failure just because of it
                    $error_details=setValue($error_details, "updates expired on $license_updates_date");
                    $notification_case=setValue($notification_case, "notification_updates_expired");
                    $updates_expired=1; //create new variable, so system will know about expiration
                    }*/

                /*if (validateDateTime($license_support_date, "Y-m-d") && $license_support_date<date("Y-m-d")) //support expired - THIS PART SHOULD ONLY BE USED IN LICENSE_SUPPORT.PHP FILE
                    {
                    //$error_detected=1; don't mark this as hard error, but fill error details, set notification_case and create new variable, so system will know about expiration, but won't mark whole report as failure just because of it
                    $error_details=setValue($error_details, "support expired on $license_support_date");
                    $notification_case=setValue($notification_case, "notification_support_expired");
                    $support_expired=1; //create new variable, so system will know about expiration
                    }*/

                if ($error_detected!=1) //everything OK so far, do final checks
                    {
                    if ($license_status==1) //license active, check if specified installation exists and is active
                        {
                        $this_installation_array=AflInstallations::where('product_id',$product_id)
                                                                  ->orWhere('client_id',$client_id)
                                                                  ->orWhere('license_code',$license_code)
                                                                  ->orWhere('installation_ip',$ip_address)
                                                                  ->orWhere('installation_disable_ip_verification',1)
                                                                  ->where('installation_domain',$installation_domain)
                                                                  ->where('installation_hash',$installation_hash)
                                                                  ->where('installation_status',1)->get()->toArray();
                                                                  //fetchRow("SELECT * FROM apl_installations WHERE product_id=? AND (client_id=? OR license_code=?) AND (installation_ip=? OR installation_disable_ip_verification=?) AND installation_domain=? AND installation_hash=? AND installation_status=?", array($product_id, $client_id, $license_code, $ip_address, 1, $installation_domain, $installation_hash, 1), array("i", "i", "s", "s", "i", "s", "s", "i"));
                        if (!empty($this_installation_array)) //installation exists and is active
                            {
                            $mysql_scheme_rows=AflLicenseSchemes::where('scheme_id',1)
                                                  ->where('scheme_status',1)->get()->toArray();
                                                
                                                  //fetchRow("SELECT * FROM apl_license_schemes WHERE scheme_id=? AND scheme_status=?", array(1, 1), array("i", "i")); //fetch MySQL scheme
                            if (!empty($mysql_scheme_rows)) //scheme exists
                                {
                                $action_success=1;
                                $notification_case=setValue($notification_case, "notification_license_ok");
                                $notification_data=$mysql_scheme_rows[0]; //make array with MySQL scheme
                                }
                            else //scheme does not exist
                                {
                                $error_detected=1;
                                $error_details=setValue($error_details, "MySQL scheme not found");
                                $notification_case=setValue($notification_case, "notification_unknown_error");
                                }
                            }
                        else //installation does not exist or is inactive
                            {
                            $error_detected=1;
                            $error_details=setValue($error_details, "installation does not exist or is inactive");
                            $notification_case=setValue($notification_case, "notification_installation_not_found");
                            }
                        }
                    }
                }
            }
        }

    $required_callback_parameters_array=array("client_id", "client_fname", "client_lname", "product_id", "product_title", "product_description", "product_url_homepage", "product_url_download", "product_version", "license_code", "license_expire_date", "license_cancel_date", "license_updates_date", "license_support_date", "license_limit", "notification_case", "notification_data"); //required callback parameters for this page
    foreach ($required_callback_parameters_array as $required_callback_parameter) //in case some required parameter (used in callback and/or notification functions) was not fetched, set its value empty to prevent "undefined variable" errors
        {
        if (!isset($$required_callback_parameter))
            {
            $$required_callback_parameter="";
            }
        }

    if (!aflValidateIntegerValue($client_id)) //empty client_id must be null, not empty string
        {
        $client_id=null;
        }

    if ($action_success==1) //everything OK
        {
        $report_text="MySQL scheme for local license at $installation_domain ($ip_address) parsed.";
        }
    else
        {
        $report_text="MySQL scheme for local license at $installation_domain ($ip_address) could not be parsed because of this reason: $error_details.";
        }

    returnServerNotification($notification_case, $root_url, $ip_address, $client_email, $client_fname, $client_lname, $license_code, $product_id, $product_title, $product_description, $product_url_homepage, $product_url_download, $product_version, $license_expire_date, $license_cancel_date, $license_updates_date, $license_support_date, $license_limit, $notification_data); //always return server notification when valid basic data was received from script
     
    }
else //possible cracking attempt, set variables required for reports function to null and generate cracking report
    {
    $product_id=0;
    $client_id=null;
    $license_code=null;
    $report_text="Host $ip_address sent invalid data to requested_url and was rejected. Host sent this data: ".json_encode($_POST).".";
    return errorResponse(Lang::get('lang.invalid'),404);
    }

;
return successResponse(Lang::get('lang.success_license_scheme'),createLicenseReport($SMART_REPORTS, $product_id, $client_id, $license_code, $report_text, $action_success),200); //always create report, no matter result
if ($action_success!=1) //record failed licensing attempt and ban host if needed
    {
    recordFailedLicensing($BANNED_HOSTS, $FAILED_LICENSINGS_LIMIT, $ip_address);
    return errorResponse(Lang::get('lang.failed_licensing'),400);
    }
    }
}
