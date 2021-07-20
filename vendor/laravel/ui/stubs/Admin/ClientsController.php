<?php

namespace App\Http\Controllers\Admin;
use App\Models\AflClients;
use App\Models\AflApiKeys;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ClientRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use App\Traits\Settings;
use App\Traits\Version;




/**
 * Consist of functionalities for the client page in Auto Faveo licenser 
 * Class ClientsController
 * @package App\Http\Controllers\Admin
 */
class ClientsController extends Controller
{
    
    /**
     * Stores newly added clients into the database
     * @param ClientRequest $request
     * @param $api_key_secret
     * @param $client_fname
     * @param $client_lname 
     * @param $client_email
     * @param $client_status
     * @return  response that a new client is added with array of details 
    */

  public function clientAdd(ClientRequest $request)
      {
        
        $api_action_success=0;
        $api_error_detected=0;  
        $added_records=0;
        $api_key_secret = $request->get('api_key_secret');
        $client_fname = $request->get('client_fname');
        $client_lname = $request->get('client_lname');
        $client_email = $request->get('client_email');
        $client_status = $request->get('client_status');

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

         if (!empty($client_fname) && !empty($client_lname) && filter_var($client_email, FILTER_VALIDATE_EMAIL) && aflValidateIntegerValue($client_status, 0, 2) &&$api_action_success==1)
                {
                if ($api_error_detected!=1)
                    {
                    $client_active_date=date("Y-m-d");

                    if ($client_status==1)
                        {
                        $client_cancel_date="0000-00-00";
                        }
                    else
                        {
                        if (empty($client_cancel_date) || !aflVerifyDateTime($client_cancel_date, "Y-m-d")) //set cancel date to now only if client is inactive and no previous cancel date set
                            {
                            $client_cancel_date=date("Y-m-d");
                            }
                        }
                    //doMysqlQuery("INSERT IGNORE INTO apl_clients (client_fname, client_lname, client_email, client_active_date, client_cancel_date, client_status) VALUES (?, ?, ?, ?, ?, ?)", array($client_fname, $client_lname, $client_email, $client_active_date, $client_cancel_date, $client_status), array("s", "s", "s", "s", "s", "i"));
                    try{
                        $add=DB::table('afl_clients')->insertOrIgnore([
                            'client_fname'=> $client_fname, 
                            'client_lname' => $client_lname, 
                            'client_email' => $client_email, 
                            'client_active_date' => $client_active_date, 
                            'client_cancel_date' => $client_cancel_date, 
                            'client_status' => $client_status
                        ]);
                        
                        $added_records += 1;
                    }
                    catch(Exception $e){
                        $added_records += 0;
                    }
                    if (!aflValidateIntegerValue($added_records))
                        {
                        $api_error_detected=1;
                        return errorResponse(Lang::get('lang.invalid'),400);
                        }
                    else
                        {
                        return successResponse(Lang::get('lang.Client_Add'),$add,201);
                        }
                    }
                }
                else{

                    return errorResponse(Lang::get('lang.invalid'),400);
                }
   
    }

    }

  

    /**
     * shows newly added clients from the database
     * 
     *
     * @return response that a client is deleted
    */
    public function show(){

        $clients  = AflClients::all();
        return successResponse(Lang::get('lang.Client_Show'),$clients,200);
    }


     /**
     * Deletes the clients from the database based on the id
     * @param $client_id
     *
     * @return response that a client is deleted and all the cascades
    */
    public function deleteClient(Request $request)
    {
    $api_action_success=0;
    $api_error_detected=0;
    $client_id = $request->get('client_id');
    $removed_records=0;  
    $api_key_secret= $request->get('api_key_secret');
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

    if (aflValidateIntegerValue($client_id))
        {
        DB::beginTransaction();//mysqli_begin_transaction($GLOBALS["mysqli"]);
        $transaction_errors_array=array();
        try{
        AFlCallbacks::where('client_id',$client_id)->delete();
        //doMysqlQuery("DELETE FROM apl_callbacks WHERE product_id=?", array($product_id), array("i")); //delete callbacks
        AFlInstallations::where('client_id',$client_id)->delete();
        //doMysqlQuery("DELETE FROM apl_installations WHERE product_id=?", array($product_id), array("i")); //delete installations
        AFlLicenses::where('client_id',$client_id)->delete();
        //doMysqlQuery("DELETE FROM apl_licenses WHERE product_id=?", array($product_id), array("i")); //delete licenses
        $removed_records+= AflClients::where('client_id', $client_id)->delete();
        //$removed_records+=doMysqlQuery("DELETE FROM apl_clients WHERE client_id=?", array($client_id), array("i"));
        DB::commit();
        return successResponse(Lang::get('lang.delete'),$removed_records,200);
        }
        catch(Exception $e){
             $transaction_errors_array[]=$e->getMessage();
             DB::rollBack();
             $removed_records=0;
             return errorResponse(Lang::get('lang.invalid'),400);
        }
        }

    return $removed_records;
}
    }

        /* public function edit($client_id)
       {

        $client = afl_clients::where('client_id',$client_id)->firstOrFail();
        return view('',compact('client'));

       }*/

    /**
     * Updates the clients from the database based on the id
     * 
     * @param Request $request
     * @param $client_id
     * @param $api_key_secret
     * @param $client_fname
     * @param $client_lname 
     * @param $client_email
     * @param $client_status
     * @return response that a client details is edited 
    */
public function clientUpdate(Request $request)
{            

  $api_key_secret = $request->get('api_key_secret');
  $client_id = $request->get('client_id');
  $client_fname = $request->get('client_fname');
  $client_lname  = $request->get('client_lname');
  $client_email = $request->get('client_email');
  $client_status = $request->get('client_status');

if (empty($client_id) || !aflValidateIntegerValue($client_id) || empty($rows_array=AflClients::where('client_id',$client_id)->get())) //invalid record
    {
    return errorResponse(Lang::get('lang.notvalid'),400);
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

         if (!empty($client_fname) && !empty($client_lname) && filter_var($client_email, FILTER_VALIDATE_EMAIL) && aflValidateIntegerValue($client_status, 0, 2) &&$api_action_success==1)
                {
                 if ($api_error_detected!=1)
                    {
                    if ($client_status==1)
                        {
                        $client_cancel_date="0000-00-00";
                        }
                    else
                        {
                        $client_cancel_date=$rows_array[0]['client_cancel_date']; //use old client_cancel_date if client was deactivated previously and its status wasn't changed now
                        if (empty($client_cancel_date) || !aflVerifyDateTime($client_cancel_date, "Y-m-d")) //set cancel date to now only if no previous cancel date set
                            {
        
                            $client_cancel_date=date("Y-m-d");
                            }
                        }
                  
                    $updated_records+=DB::table('afl_clients')->where('client_id',$client_id)
                                         ->update([
                                         'client_fname' => $client_fname,
                                         'client_lname' => $client_lname,
                                         'client_email' => $client_email,
                                         'client_cancel_date' =>$client_cancel_date,
                                         'client_status' => $client_status
                                     ]);
                    
                     //doMysqlQuery("UPDATE apl_clients SET client_fname=?, client_lname=?, client_email=?, client_cancel_date=?, client_status=? WHERE client_id=?", array($client_fname, $client_lname, $client_email, $client_cancel_date, $client_status, $client_id), array("s", "s", "s", "s", "i", "i"));
                    if (!aflValidateIntegerValue($updated_records))
                        {
                        $error_detected=1;
                        return errorResponse(Lang::get('lang.invalid'),400);
                        }
                    else
                        {
                        return successResponse(Lang::get('lang.Client_Edit'),$updated_records,200);
                        }
                    }
                }
                else{

                    return errorResponse(Lang::get('lang.error'),400);
                }
    }

    }
}
