<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\AflCallbacks;
use App\Models\AflClients;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflSettings;
use Doctrine\DBAL\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\PhpMailController;

/**
 * Consist of functionalities for the client page in Auto Faveo licenser
 * Class ClientsController
 */
class ClientsController extends Controller
{
    public function __construct(?Request $request = null)
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
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'client_fname' => $request->get('client_fname'),
            'client_lname' => $request->get('client_lname'),
            'client_email' => $request->get('client_email'),
            'client_username' => $request->get('client_username'),
            'client_status' => $request->get('client_status'),
            'client_role' => $request->get('client_role'),
        ];

        $result = $this->processClientAdd($data, $this->ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code'] ?? 400);
        }

        return successResponse(Lang::get('lang.Client_Add'), $result['data'], 201);
    }

    /**
     * shows newly added clients from the database
     *
     *
     * @return response that a client is deleted
     */
    public function show(Request $request, $client_id)
    {
        // Set default pagination values
        $perPage = $request->input('perPage', 10); // Default per page is 10
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','client_id');

        // Query to retrieve clients excluding the specified client ID
        $clients = AflClients::where('client_id', '!=', $client_id)
            ->when($searchQuery, function ($query) use ($searchQuery) {
                return $query->where(function ($query) use ($searchQuery) {
                    $query->where(DB::raw('CONCAT(client_fname, " ", client_lname)'), 'LIKE', '%'.$searchQuery.'%')
                        ->orWhere('client_role', 'LIKE', '%'.$searchQuery.'%')
                        ->orWhere('client_status', 'LIKE',  '%' . statusFormatter($searchQuery) . '%')
                        ->orWhere('client_email', 'LIKE', '%'.$searchQuery.'%');
                });
            })
            ->select(DB::raw('CONCAT(client_fname, " ", client_lname) AS full_name'), 'client_id', 'client_email', 'client_role', 'client_status', 'client_cancel_date', 'client_active_date','is_2fa_enabled')
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        // Return success response with paginated client data
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

        if (!aflValidateIntegerValue($client_id) && $api_action_success != 1) {
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

        if (!empty($client)) {
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
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'client_id' => $request->get('client_id'),
            'client_fname' => $request->get('client_fname'),
            'client_lname' => $request->get('client_lname'),
            'client_email' => $request->get('client_email'),
            'client_username' => $request->get('client_username'),
            'client_status' => $request->get('client_status'),
            'client_role' => $request->get('client_role'),
        ];

        $result = $this->processClientUpdate($data, $this->ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code'] ?? 400);
        }

        return successResponse(Lang::get('lang.Client_Update'), $result['data'], 200);
    }

    public function processClientAdd(array $data, string $ipAddress = ''): array
    {
        $api_key_secret = $data['api_key_secret'] ?? null;
        $client_fname = $data['client_fname'] ?? '';
        $client_lname = $data['client_lname'] ?? '';
        $client_email = $data['client_email'] ?? '';
        $client_username = $data['client_username'] ?? '';
        $client_status = $data['client_status'] ?? 1;
        $client_role = ($data['client_role'] ?? 1) == 0 ? 'admin' : 'client';
        $password = Str::random(8);
        $client_password = $client_role == 'admin' ? Hash::make($password) : null;

        $api_action_success = 1;
        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_action_success = $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if (empty($client_fname) || empty($client_lname) || ! filter_var($client_email, FILTER_VALIDATE_EMAIL) || ! aflValidateIntegerValue($client_status, 0, 2) || $api_action_success != 1) {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
        }

        $client_active_date = date('Y-m-d');
        if ($client_status != 1) {
            $client_cancel_date = '0000-00-00';
        } else {
            $client_cancel_date = date('Y-m-d');
        }

        try {
            $dataToInsert = [
                'client_fname' => $client_fname,
                'client_lname' => $client_lname,
                'client_email' => $client_email,
                'client_cancel_date' => $client_cancel_date,
                'client_status' => $client_status,
                'client_role' => $client_role,
                'client_username' => $client_username,
            ];

            if ($client_status == '1') {
                $dataToInsert['client_active_date'] = $client_active_date;
            }

            if ($client_role == 'admin' && $client_status == '1') {
                $dataToInsert['client_password'] = $client_password;
                $add = AflClients::insertOrIgnore($dataToInsert);

                $client_name = $client_fname . ' ' . $client_lname;
                $emailData = [
                    'client_name' => $client_name,
                    'client_email' => $client_email,
                    'password' => $password,
                    'appUrl' => config('app.url'),
                ];
                $title = Lang::get('lang.login_credentials_agora');
                $template = 'emails.welcomeEmail';
                postEmailSendConfig($client_email, $title, $template, $emailData);
            } else {
                $add = AflClients::insertOrIgnore($dataToInsert);
            }

            $clientId = DB::getPdo()->lastInsertId();

            return ['success' => true, 'data' => ['client_id' => $clientId], 'message' => ''];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => Lang::get('lang.Client_Add_Failed'), 'data' => [], 'status_code' => 500];
        }
    }

    public function processClientUpdate(array $data, string $ipAddress = ''): array
    {
        $api_key_secret = $data['api_key_secret'] ?? null;
        $client_id = $data['client_id'] ?? null;
        $client_fname = $data['client_fname'] ?? '';
        $client_lname = $data['client_lname'] ?? '';
        $client_email = $data['client_email'] ?? '';
        $client_username = $data['client_username'] ?? '';
        $client_status = $data['client_status'] ?? 1;
        $client_role = ($data['client_role'] ?? 1) == 0 ? 'admin' : 'client';
        $password = Str::random(8);
        $client_password = $client_role == 'admin' ? Hash::make($password) : null;

        if (empty($client_id) || ! aflValidateIntegerValue($client_id) || empty(AflClients::where('client_id', $client_id)->get())) {
            return ['success' => false, 'message' => Lang::get('lang.not_found_client'), 'data' => [], 'status_code' => 404];
        }

        $api_action_success = 1;
        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_action_success = $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if (empty($client_fname) || empty($client_lname) || ! filter_var($client_email, FILTER_VALIDATE_EMAIL) || ! aflValidateIntegerValue($client_status, 0, 2) || $api_action_success != 1) {
            return ['success' => false, 'message' => Lang::get('lang.invalid_client'), 'data' => [], 'status_code' => 400];
        }

        $rows_array = AflClients::where('client_id', $client_id)->get();

        if ($client_status == 1) {
            $client_cancel_date = '0000-00-00';
        } else {
            $client_cancel_date = $rows_array[0]['client_cancel_date'];
            if (empty($client_cancel_date) || ! aflVerifyDateTime($client_cancel_date, 'Y-m-d')) {
                $client_cancel_date = date('Y-m-d');
            }
        }

        $role = $rows_array[0]['client_role'];
        $status = $rows_array[0]['client_status'];
        $active_date = $rows_array[0]['client_active_date'];

        $dataToUpdate = [
            'client_fname' => $client_fname,
            'client_lname' => $client_lname,
            'client_email' => $client_email,
            'client_username' => $client_username,
            'client_cancel_date' => $client_cancel_date,
            'client_status' => $client_status,
            'client_role' => $client_role,
        ];

        if ($client_status == 1 && $active_date == '0000-00-00') {
            $dataToUpdate['client_active_date'] = date('Y-m-d');
        }

        $changingroleCondition = ($client_role == 'admin' && $role == 'client' && $client_status == '1');
        $changingstatusCondition = ($client_role == 'admin' && $role == 'admin' && $status == '0' && $client_status == '1' && $active_date == '0000-00-00');

        try {
            if ($changingroleCondition || $changingstatusCondition) {
                $dataToUpdate['client_password'] = $client_password;
                $updated_records = AflClients::where('client_id', $client_id)->update($dataToUpdate);

                $client_name = $client_fname . ' ' . $client_lname;
                $emailData = [
                    'client_name' => $client_name,
                    'client_email' => $client_email,
                    'password' => $password,
                    'appUrl' => config('app.url'),
                ];
                $title = Lang::get('lang.admin_privileges');
                $template = 'emails.adminRoleMail';
                postEmailSendConfig($client_email, $title, $template, $emailData);
            } else {
                $updated_records = AflClients::where('client_id', $client_id)->update($dataToUpdate);
            }

            if ($client_role == 'client' || $client_status == 0) {
                (new AuthController())->logout(new Request, $client_id);
                DB::table('oauth_access_tokens')->where('user_id', $client_id)->delete();
            }

            return ['success' => true, 'data' => $updated_records, 'message' => ''];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => Lang::get('lang.Client_Add_Failed'), 'data' => [], 'status_code' => 500];
        }
    }
}
