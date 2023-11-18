<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\AflCallbacks;
use App\Models\AflClients;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Consist of functionalities for the client page in Auto Faveo licenser
 * Class ClientsController
 */
class ClientsController extends Controller
{
    public function __construct(Request $request)
    {
        $this->ip_address = request()->server('REMOTE_ADDR');
    }

    /**
     * Stores newly added clients into the database
     *
     * @param  ClientRequest  $request
     * @param $api_key_secret
     * @param $client_fname
     * @param $client_lname
     * @param $client_email
     * @param $client_status
     * @return  response that a new client is added with array of details
     */
    
    public function clientAdd(ClientRequest $request)
    {
        $added_records = 0;
        $api_key_secret = $request->get('api_key_secret');
        $client_fname = $request->get('client_fname');
        $client_lname = $request->get('client_lname');
        $client_email = $request->get('client_email');
        $client_status = $request->get('client_status');
        $client_role = ($request->get('client_role') == 0) ? 'admin' : 'client';
        $password =Str::random(8);
        $client_password = $client_role == 'admin' ? Hash::make($password) : null;
    
        $api_key = new ApiKeysController();
        $api_action_success =  $api_key->apiKeyCheck($api_key_secret, $this->ip_address);

        if (! empty($client_fname) && ! empty($client_lname) && filter_var($client_email, FILTER_VALIDATE_EMAIL)
              && aflValidateIntegerValue($client_status, 0, 2) && $api_action_success == 1) {
            $client_active_date = date('Y-m-d');
            if ($client_status != 1) {
                $client_cancel_date = '0000-00-00';
            } else {
                if (empty($client_cancel_date) || ! aflVerifyDateTime($client_cancel_date, 'Y-m-d')) { //set cancel date to now only if client is inactive and no previous cancel date set
                    $client_cancel_date = date('Y-m-d');
                }
            }
            try {
                $dataToInsert = [
                    'client_fname' => $client_fname,
                    'client_lname' => $client_lname,
                    'client_email' => $client_email,
                    'client_cancel_date' => $client_cancel_date,
                    'client_status' => $client_status,
                    'client_role' => $client_role,
                ];
                if($client_status == '1'){
                    $dataToInsert['client_active_date'] = $client_active_date;
                }
                try {
                
                    if ($client_role == 'admin') {
                        $dataToInsert['client_password'] = $client_password;
                        $add = AflClients::insertOrIgnore($dataToInsert);
                        $added_records += 1;
                        $client_name = $client_fname . ' ' . $client_lname;
                        $data = [
                            'client_name' => $client_name,
                            'client_email' => $client_email,
                            'password' => $password,
                            'appUrl' => config('app.url'),
                        ];
                        try {
                            Mail::send('emails.welcomeEmail', $data, function ($message) use ($client_email) {
                                $message->to($client_email)->subject('Agora License Manager Login Credentials');
                            });
                        } catch (\Exception $e) {
                        }
                    
                       
                    } else {
                        $added_records += 1;
                        $add = AflClients::insertOrIgnore($dataToInsert);
                    }
                }  catch (\Exception $e) {
                    return errorResponse(Lang::get('lang.Client_Add_Failed'), 500);
                } 
                
            } catch (\Exception $e) {
                $added_records += 0;
            }
            
            if (! aflValidateIntegerValue($added_records)) {
                $api_error_detected = 1;

                return errorResponse(Lang::get('lang.invalid'), 400);
            }
            return successResponse(Lang::get('lang.Client_Add'), $add, 201);
        }

        return errorResponse(Lang::get('lang.invalid'), 400);
    }

    /**
     * shows newly added clients from the database
     *
     *
     * @return response that a client is deleted
     */
    public function show($client_id)
    {
        $clients = AflClients::where('client_id', '!=',$client_id)->select(DB::raw('CONCAT(client_fname, " ", client_lname) As full_name'), 'client_id', 'client_email', 'client_role', 'client_status', 'client_cancel_date', 'client_active_date')
        ->get();

        return successResponse(Lang::get('lang.Client_Show'), $clients, 200);
    }

    /**
     * Deletes the clients from the database based on the id
     *
     * @param $client_id
     * @return response that a client is deleted and all the cascades
     */
    public function deleteClient(Request $request)
    {
        $client_id = $request->get('client_id');
        $removed_records = 0;
        $api_key_secret = $request->get('api_key_secret');

        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);

        if (! aflValidateIntegerValue($client_id) && $api_action_success != 1) {
            return errorResponse(Lang::get('lang.Not_found_client'), 404);
        }

        DB::beginTransaction(); //mysqli_begin_transaction($GLOBALS["mysqli"]);
        $transaction_errors_array = [];
        try {
            AFlCallbacks::where('client_id', $client_id)->delete(); //deleting the callbacks for this client
            AFlInstallations::where('client_id', $client_id)->delete(); //deleting installations of this client
            AFlLicenses::where('client_id', $client_id)->delete(); // deleting the licenses created by this client
            $removed_records += AflClients::where('client_id', $client_id)->delete(); //deleting the client
            DB::commit();

            return successResponse(Lang::get('lang.Client_Destroy'), $removed_records, 200);
        } catch (Exception $e) {
            $transaction_errors_array[] = $e->getMessage();
            DB::rollBack();
            $removed_records = 0;

            return errorResponse(Lang::get('lang.invalid'), 400);
        }

        return $removed_records;
    }

    public function edit($client_id)
    {
        $client = AflClients::where('client_id', $client_id)->firstOrFail();

        if (! empty($client)) {
            $client->client_role = ($client->client_role == 'admin') ? 0 : 1;

            return successResponse('', ['client' => $client], 200);
        }

        return errorResponse(Lang::get('lang.invalid'), 400);
    }

    /**
     * Updates the clients from the database based on the id
     *
     * @param  Request  $request
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
        $updated_records = 0;
        $api_key_secret = $request->get('api_key_secret');
        $client_id = $request->get('client_id');
        $client_fname = $request->get('client_fname');
        $client_lname = $request->get('client_lname');
        $client_email = $request->get('client_email');
        $client_status = $request->get('client_status');
        $client_role = ($request->get('client_role') == 0) ? 'admin' : 'client';
        $password =Str::random(8);
        $client_password = $client_role == 'admin' ? Hash::make($password) : null;
    
        if (empty($client_id) || ! aflValidateIntegerValue($client_id) ||
    empty($rows_array = AflClients::where('client_id', $client_id)->get())) { //invalid record
            return errorResponse(Lang::get('lang.not_found_client'), 404);
        }
        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);
        if (! empty($client_fname) && ! empty($client_lname) && filter_var($client_email, FILTER_VALIDATE_EMAIL) && aflValidateIntegerValue($client_status, 0, 2) && $api_action_success == 1) {
            if ($client_status == 1) {
                $client_cancel_date = '0000-00-00';
            } else {
                $client_cancel_date = $rows_array[0]['client_cancel_date']; //use old client_cancel_date if client was deactivated previously and its status wasn't changed now
                if (empty($client_cancel_date) || ! aflVerifyDateTime($client_cancel_date, 'Y-m-d')) { //set cancel date to now only if no previous cancel date set
                    $client_cancel_date = date('Y-m-d');
                }
            }
            $role= AflClients::where('client_id', $client_id)->value('client_role');
            $active_date= AflClients::where('client_id', $client_id)->value('client_active_date');

                            $dataToUpdate= [
                                             'client_fname' => $client_fname,
                                             'client_lname' => $client_lname,
                                             'client_email' => $client_email,
                                             'client_cancel_date' => $client_cancel_date,
                                             'client_status' => $client_status,
                                             'client_role' => $client_role ,
                                         ];   
                                         if ($client_status == 1 && $active_date == NULL && $active_date == "") {
                                            $dataToUpdate['client_active_date'] = date('Y-m-d');
                                        }
                                         if ($client_role == 'admin'&& $role == "client") {
                                            try{
                                            $dataToUpdate['client_password'] = $client_password;
                                            $updated_records = AflClients::where('client_id', $client_id)
                                            ->update($dataToUpdate );
                                            $client_name = $client_fname . ' ' . $client_lname;
                                            if($client_role =='admin' && $role == "client"){
                                               $data = [
                                                   'client_name' => $client_name,
                                                   'client_email' => $client_email,
                                                   'password' => $password,
                                                   'appUrl' => config('app.url'),
                               
                                               ];
                                               try{
                                               Mail::send('emails.adminRoleMail', $data, function ($message) use ($client_email) {
                                                $message->to($client_email)->subject('Admin Privileges Granted');
                                            });
                                        }
                                        catch(\Exception $e){}  
                                           }}
                                         catch (\Exception $e) {
                                            return errorResponse(Lang::get('lang.Client_Add_Failed'), 500);
                                        } 
                                        }
                                        else{
                                            $updated_records = AflClients::where('client_id', $client_id)
                                            ->update($dataToUpdate );
                                        }
                                    
                                       
                                         if($client_role == "client" || $client_status == 0){
                                              (new AuthController())->logout(new Request,$client_id);
                                         $logout = DB::table('oauth_access_tokens')
                                         ->where('user_id', $client_id)->delete();                                        
                                         }
                                      

            if (! aflValidateIntegerValue($updated_records)) {
                $error_detected = 1;

                return errorResponse(Lang::get('lang.nothing_updated'), 400);
            } else {
                return successResponse(Lang::get('lang.Client_Update'), $updated_records, 200);
            }
        }

        return errorResponse(Lang::get('lang.invalid_client'), 400);
    }

    
 
}
