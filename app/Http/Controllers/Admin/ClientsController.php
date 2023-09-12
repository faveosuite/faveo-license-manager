<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\AflCallbacks;
use App\Models\AflClients;
use App\Models\AflAdmins;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;




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
     * @param ClientRequest $request
     * @param $api_key_secret
     * @param $client_fname
     * @param $client_lname
     * @param $client_email
     * @param $client_status
     * @return response that a new client is added with array of details
     */
    public function clientAdd(ClientRequest $request)
    {
        $client_role = $request->get('client_role') ?? 'client';

        if($client_role == "admin"){
            try{
                $user = new AflAdmins();
                $user-> admin_fname= $request->get('client_fname');
                $user->admin_lname = $request->get('client_lname');
                $user->admin_email = $request->get('client_email');
                $user->admin_status = $request->get('client_status');
                $pssword = Str::random(37);
                $user->admin_password = $pssword;
                $user->admin_hash = $pssword;
                $user->admin_date = $request->input('client_date') ?? now();
                $user->admin_type_id = 1;
                $user->save();
                return successResponse(Lang::get('lang.user_updated_successfully'), $user, 200);
            }catch(\Exception $exception){
                Log::error('Exception occurred: ' . $exception->getMessage());
                return errorResponse($exception->getMessage(), 412);
            }
        }
        else{
            $added_records = 0;
            $api_key_secret = $request->get('api_key_secret');
            $client_fname = $request->get('client_fname');
            $client_lname = $request->get('client_lname');
            $client_email = $request->get('client_email');
            $client_status = $request->get('client_status');


            $api_key = new ApiKeysController();
            $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);


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
                    $add = DB::table('afl_clients')->insertOrIgnore([
                        'client_fname' => $client_fname,
                        'client_lname' => $client_lname,
                        'client_email' => $client_email,
                        'client_active_date' => $client_active_date,
                        'client_cancel_date' => $client_cancel_date,
                        'client_status' => $client_status,
                    ]);
                    $added_records += 1;
                } catch (Exception $e) {
                    $added_records += 0;
                }
                if (! aflValidateIntegerValue($added_records)) {
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.no_client'), 400);
                }
                return successResponse(Lang::get('lang.Client_Add'), $add, 201);
            }
            return errorResponse(Lang::get('lang.invalid'), 400);
        }
    }


    /**
     * shows newly added clients from the database
     *
     *
     * @return response that a client is deleted
     */
    public function show()
    {
        $users = AflClients::select(DB::raw('CONCAT(client_fname, " ", client_lname) As full_name'), 'client_id', 'client_email', 'client_status', 'client_active_date')
        ->get()
        ->map(function ($client) {
            $client['client_role'] = 'client';
            return $client;
        });
    
    $admins = AflAdmins::select(DB::raw('CONCAT(admin_fname, " ", admin_lname) As full_name'), 'admin_id', 'admin_email', 'admin_status', 'admin_date')
        ->get()
        ->map(function ($admin) {
            $admin['client_role'] = 'admin';
            return $admin;
        });
    
    $clients = $users->concat($admins);
    

        return successResponse(Lang::get('lang.Client_Show'), $clients,  200);
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
        $client_role = $request->get('client_role') ?? 'client';

     if($client_role == 'admin') {
        try{
                $user = AflAdmins::findOrFail($client_id);
                if(!$user) {
                    return errorResponse(Lang::get('lang.user_not_found'), null, 200);
                }
                $user->delete();
                return successResponse(Lang::get('lang.user_deleted'), null, 200);

            }
            catch( \Exception $exception) {
                Log::error('Exception occurred: ' . $exception->getMessage());
                return errorResponse($exception->getMessage());
            }
        }
        else{
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
    }


    public function edit($client_id,$client_role = 'client')
    {
        if($client_role == "admin"){
            $admin = AflAdmins::where('admin_id', $client_id)->firstOrFail();

            if ($admin) {
                return successResponse('', ['admin' => $admin], 200);
            }
        }
        else{
        $client = AflClients::where('client_id', $client_id)->firstOrFail();
        if ($client) {
            return successResponse('', ['client' => $client], 200);
        }
    }

        return errorResponse(Lang::get('lang.invalid'), 400);
    }


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
        $client_role = $request->get('client_role') ?? 'client';
        if($client_role =="admin"){
            try{
                $user = new AflAdmins();
                $user-> admin_fname= $request->get('client_fname');
                $user->admin_lname = $request->get('client_lname');
                $user->admin_email = $request->get('client_email');
                $user->admin_status = $request->get('client_status');
                $pssword = Str::random(37);
                $user->admin_password = $pssword;
                $user->admin_hash = $pssword;
                $user->admin_date = $request->input('client_date') ?? now();
                $user->admin_type_id = 1;
                $user->save();
                return successResponse(Lang::get('lang.user_updated_successfully'), $user, 200);
            }catch(\Exception $exception){
                Log::error('Exception occurred: ' . $exception->getMessage());
                return errorResponse($exception->getMessage(), 412);
            }
        }

else{
        $updated_records = 0;
        $api_key_secret = $request->get('api_key_secret');
        $client_id = $request->get('client_id');
        $client_fname = $request->get('client_fname');
        $client_lname = $request->get('client_lname');
        $client_email = $request->get('client_email');
        $client_status = $request->get('client_status');


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
            $updated_records += DB::table('afl_clients')->where('client_id', $client_id)
                ->update([
                    'client_fname' => $client_fname,
                    'client_lname' => $client_lname,
                    'client_email' => $client_email,
                    'client_cancel_date' => $client_cancel_date,
                    'client_status' => $client_status,
                ]);


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
}



