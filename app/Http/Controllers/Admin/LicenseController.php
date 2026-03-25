<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;
use App\Models\AflCallbacks;
use App\Models\AflClients;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\CommonSetting;
use App\Models\InstallationLogs;
use App\Models\LicenseColumn;
use App\Models\ReportColumn;
use App\Models\AflProducts;
use App\Models\AfuProducts;
use App\Models\AfuVersions;
use App\Models\LicenseOption;
use App\Models\LicensePlugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Log;

/**
 * Consist of functionalities for the License page in Auto Faveo licenser
 * Class LicenseController
 */
class LicenseController extends Controller
{
    public function __construct()
    {
        $this->ip_address = request()->server('REMOTE_ADDR');
    }

    /**
     * To Add license Details to the license manager via request or entering them, it can be added with client id or anonymously
     *
     * @param  LicenseRequest  $request
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
    public function licenseAdd(LicenseRequest $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'product_id' => $request->get('product_id'),
            'license_code' => $request->get('license_code'),
            'license_require_domain' => $request->get('license_require_domain'),
            'license_status' => $request->get('license_status'),
            'client_id' => $request->get('client_id'),
            'license_order_number' => $request->get('license_order_number'),
            'license_ip' => $request->get('license_ip'),
            'license_domain' => $request->get('license_domain'),
            'license_limit' => $request->get('license_limit'),
            'license_expire_date' => $request->input('license_expire_date'),
            'license_updates_date' => $request->get('license_updates_date'),
            'license_support_date' => $request->get('license_support_date'),
            'license_comments' => $request->get('license_comments'),
        ];

        $result = $this->processLicenseAdd($data, $this->ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code']);
        }

        return successResponse(Lang::get('lang.adddd'), $result['data'], 201);
    }

    public function processLicenseAdd(array $data, string $ipAddress = ''): array
    {
        $api_action_success = 0;
        $api_error_detected = 0;
        $added_records = 0;

        $api_key_secret = $data['api_key_secret'] ?? null;
        $product_id = $data['product_id'] ?? null;
        $license_code = $data['license_code'] ?? null;
        $license_require_domain = $data['license_require_domain'] ?? null;
        $license_status = $data['license_status'] ?? null;
        $client_id = $data['client_id'] ?? null;
        $license_order_number = $data['license_order_number'] ?? null;
        $license_ip = $data['license_ip'] ?? null;
        $license_domain = $data['license_domain'] ?? null;
        $license_limit = $data['license_limit'] ?? null;
        $license_expire_date = $data['license_expire_date'] ?? null;
        $license_updates_date = $data['license_updates_date'] ?? null;
        $license_support_date = $data['license_support_date'] ?? null;
        $license_comments = $data['license_comments'] ?? null;

        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_action_success = $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($license_require_domain, 0, 1) && aflValidateIntegerValue($license_status, 0, 2)) {
            if (empty($client_id) || ! aflValidateIntegerValue($client_id)) { //in case no client_id was submitted, its value must be stored as NULL in database
                $client_id = null;
            }

            if (empty($license_code)) { //in case no license_code was submitted, its value must be stored as NULL in database
                $license_code = null;
            }
            $licenseChecks = $this->licenseChecks($client_id, $license_code, $license_ip, $license_domain, $license_limit, $license_expire_date, $license_updates_date, $license_support_date);
            if (! empty($licenseChecks)) {
                $content = json_decode($licenseChecks->content(), true);

                return ['success' => false, 'message' => $content['message'] ?? Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
            }
            if ($api_error_detected != 1) {
                $license_date = date('Y-m-d');
                if (empty($license_envato) || ! aflValidateIntegerValue($license_envato)) {
                    $license_envato = 0;
                }

                if ($license_status == 1) {
                    $license_cancel_date = '0000-00-00';
                } else {
                    if (empty($license_cancel_date) || ! aflVerifyDateTime($license_cancel_date, 'Y-m-d')) { //set cancel date to now only if license is inactive and no previous cancel date set
                        $license_cancel_date = date('Y-m-d');
                    }
                }
                $license_expire_email_date = $license_expire_date;
                $license_updates_email_date = $license_updates_date;
                $license_support_email_date = $license_support_date;
                try {
                    DB::table('afl_licenses')
                        ->insertOrIgnore([
                            'client_id' => $client_id,
                            'license_code' => $license_code,
                            'product_id' => $product_id,
                            'license_order_number' => $license_order_number,
                            'license_ip' => $license_ip,
                            'license_domain' => $license_domain,
                            'license_require_domain' => $license_require_domain,
                            'license_limit' => $license_limit,
                            'license_date' => $license_date,
                            'license_cancel_date' => $license_cancel_date,
                            'license_expire_date' => $license_expire_date,
                            'license_updates_date' => $license_updates_date,
                            'license_support_date' => $license_support_date,
                            'license_expire_email_date' => $license_expire_email_date,
                            'license_updates_email_date' => $license_updates_email_date,
                            'license_support_email_date' => $license_support_email_date,
                            'license_comments' => $license_comments,
                            'license_envato' => $license_envato,
                            'license_status' => $license_status,
                        ]);
                    $added_records += 1;
                } catch (\Exception $e) {
                    $added_records += 0;
                }

                if (! aflValidateIntegerValue($added_records)) {
                    return ['success' => false, 'message' => Lang::get('lang.invalid_record_data'), 'data' => [], 'status_code' => 400];
                } else {
                    $license_id = DB::getpdo()->lastInsertId();
                    if (aflValidateIntegerValue($license_id)) {
                        $client_email = null;
                        foreach ($rows_array = AflLicenses::leftJoin('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')
                            ->leftJoin('users', 'afl_licenses.client_id', '=', 'users.client_id')
                            ->where('afl_licenses.license_id', $license_id)
                            ->get()->toArray() as $row) {
                            extract((array) $row);
                        }
                        $client_formatted = formatClient($license_code, $client_email);

                        return [
                            'success' => true,
                            'data' => $client_formatted,
                            'message' => '',
                            'license_id' => $license_id,
                        ];
                    }
                }
            }
        } else {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
        }

        return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
    }

    /**
     * To Update license Details to the license manager via request or entering them, it can be added with client id or anonymously
     *
     * @param  LicenseRequest  $request
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
    public function licenseUpdate(Request $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'license_id' => $request->get('license_id'),
            'product_id' => $request->get('product_id'),
            'license_require_domain' => $request->get('license_require_domain'),
            'license_status' => $request->get('license_status'),
            'client_id' => $request->get('client_id'),
            'license_code' => $request->get('license_code'),
            'license_order_number' => $request->get('license_order_number'),
            'license_ip' => $request->get('license_ip'),
            'license_domain' => $request->get('license_domain'),
            'license_limit' => $request->get('license_limit'),
            'license_expire_date' => $request->get('license_expire_date'),
            'license_updates_date' => $request->get('license_updates_date'),
            'license_support_date' => $request->get('license_support_date'),
            'license_comments' => $request->get('license_comments'),
        ];

        $result = $this->processLicenseUpdate($data, $this->ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code']);
        }

        return successResponse(Lang::get('lang.license_Update'), $result['data'], 200);
    }

    public function processLicenseUpdate(array $data, string $ipAddress = ''): array
    {
        $updated_records = 0;

        $api_key_secret = $data['api_key_secret'] ?? null;
        $license_id = $data['license_id'] ?? null;
        $product_id = $data['product_id'] ?? null;
        $license_require_domain = $data['license_require_domain'] ?? null;
        $license_status = $data['license_status'] ?? null;
        $client_id = $data['client_id'] ?? null;
        $license_code = $data['license_code'] ?? null;
        $license_order_number = $data['license_order_number'] ?? '';
        $license_ip = $data['license_ip'] ?? '';
        $license_domain = $data['license_domain'] ?? '';
        $license_limit = $data['license_limit'] ?? '';
        $license_expire_date = $data['license_expire_date'] ?? '';
        $license_updates_date = $data['license_updates_date'] ?? '';
        $license_support_date = $data['license_support_date'] ?? '';
        $license_comments = $data['license_comments'] ?? '';

        if (empty($license_id) || ! aflValidateIntegerValue($license_id) || empty($rows_array = AflLicenses::where('license_id', $license_id)->get())) {//invalid record
            return ['success' => false, 'message' => Lang::get('lang.license_id'), 'data' => [], 'status_code' => 400];
        }

        $api_action_success = 1;
        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_action_success = $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($license_require_domain, 0, 1) && aflValidateIntegerValue($license_status, 0, 2) && $api_action_success == 1) {
            if (empty($client_id) || ! aflValidateIntegerValue($client_id)) { //in case no client_id was submitted, its value must be stored as NULL in database
                $client_id = null;
            }
            if (empty($license_code)) { //in case no license_code was submitted, its value must be stored as NULL in database
                $license_code = null;
            }
            $licenseChecks = $this->licenseChecks($client_id, $license_code, $license_ip, $license_domain, $license_limit, $license_expire_date, $license_updates_date, $license_support_date);
            if (! empty($licenseChecks)) {
                $content = json_decode($licenseChecks->content(), true);

                return ['success' => false, 'message' => $content['message'] ?? Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
            }
            if ($api_action_success == 1) {
                if (! empty($license_expire_date) && aflVerifyDateTime($license_expire_date, 'Y-m-d') && $license_expire_date != $rows_array[0]['license_expire_date']) { //license_expire_date changed, reset license_expire_email_date, so client can receive new notification
                    $license_expire_email_date = '0000-00-00';
                } else {
                    $license_expire_email_date = $rows_array[0]['license_expire_email_date']; //use old license_expire_email_date
                }

                if (! empty($license_updates_date) && aflVerifyDateTime($license_updates_date, 'Y-m-d') && $license_updates_date != $rows_array[0]['license_updates_date']) { //license_updates_date changed, reset license_updates_email_date, so client can receive new notification
                    $license_updates_email_date = '0000-00-00';
                } else {
                    $license_updates_email_date = $rows_array[0]['license_updates_email_date']; //use old license_updates_email_date
                }

                if (! empty($license_support_date) && aflVerifyDateTime($license_support_date, 'Y-m-d') && $license_support_date != $rows_array[0]['license_support_date']) { //license_support_date changed, reset license_support_email_date, so client can receive new notification
                    $license_support_email_date = '0000-00-00';
                } else {
                    $license_support_email_date = $rows_array[0]['license_support_email_date']; //use old license_support_email_date
                }

                if (empty($license_envato) || ! aflValidateIntegerValue($license_envato)) {
                    $license_envato = 0;
                }
                if ($license_status == 1) {
                    $license_cancel_date = '0000-00-00';
                } else {
                    $license_cancel_date = $rows_array[0]['license_cancel_date']; //use old license_cancel_date if license was deactivated previously and its status wasn't changed now
                    if (empty($license_cancel_date) || ! aflVerifyDateTime($license_cancel_date, 'Y-m-d')) { //set cancel date to now only if no previous cancel date set
                        $license_cancel_date = date('Y-m-d');
                    }
                }
                $updated_records += AflLicenses::where('license_id', $license_id)
                    ->update([
                        'license_order_number' => $license_order_number,
                        'license_ip' => $license_ip,
                        'license_domain' => $license_domain,
                        'license_require_domain' => $license_require_domain,
                        'license_limit' => $license_limit,
                        'license_cancel_date' => $license_cancel_date,
                        'license_expire_date' => $license_expire_date,
                        'license_expire_email_date' => $license_expire_date,
                        'license_updates_date' => $license_updates_date,
                        'license_updates_email_date' => $license_updates_email_date,
                        'license_support_date' => $license_support_date,
                        'license_support_email_date' => $license_support_email_date,
                        'license_comments' => $license_comments,
                        'license_envato' => $license_envato,
                        'license_status' => $license_status,
                    ]);

                if (! aflValidateIntegerValue($updated_records)) {
                    return ['success' => false, 'message' => Lang::get('lang.invalid_record_data'), 'data' => [], 'status_code' => 400];
                } else {
                    $client_email = null;
                    foreach ($rows_array = AflLicenses::leftJoin('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')
                        ->leftJoin('users', 'afl_licenses.client_id', '=', 'users.client_id')
                        ->where('afl_licenses.license_id', $license_id)
                        ->get()->toArray() as $row) { //fetch product and client details to use in reports
                        extract((array) $row);
                    }

                    $client_formatted = formatClient($license_code, $client_email);

                    return ['success' => true, 'data' => $client_formatted, 'message' => '', 'license_id' => $license_id];
                }
            }
        } else {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
        }

        return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
    }

    /**
     * To delete the license stored in the license manager
     *
     * @param $license_id
     * @return the removed records with a success response
     */
    public function deleteLicense(Request $request)
    {
        $license_id = $request->get('license_id');
        $api_key_secret = $request->get('api_key_secret');
        $api_key = new ApiKeysController();

        if (!aflValidateIntegerValue($license_id) || !$api_key->apiKeyCheck($api_key_secret, $this->ip_address)) {
            return errorResponse(Lang::get('lang.invalid'), 400);
        }

        $license_code = AflLicenses::where('license_id', $license_id)->value('license_code');

        if (!$license_code) {
            return successResponse(Lang::get('lang.delete'), 0, 200);
        }
        // Begin transaction
        DB::transaction(function () use ($license_code, $license_id) {
            AflCallbacks::where('license_code', $license_code)->delete();
            AflInstallations::where('license_code', $license_code)->delete();
            InstallationLogs::where('license_code', $license_code)->delete();
            AflLicenses::where('license_id', $license_id)->delete();
        });
        return successResponse(Lang::get('lang.delete'), 1, 200);
    }

    public function show(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query', '');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'license_id');
        $fields = json_decode($this->getLicenseColumns()->content())->data;
        $searchable = [
            'license_code',
            'license_ip',
            'license_limit',
            'license_expire_date',
            'license_support_date',
            'license_order_number',
            'license_domain',
            'license_date',
            'license_updates_date',
            'license_status'
        ];
        $licenseQuery = AflLicenses::with('product:product_id,product_title','clients:client_id,client_email')
            ->select(
                'license_id',
                'product_id',
                'client_id',
                'license_code',
                'license_ip',
                'license_limit',
                'license_expire_date',
                'license_support_date',
                'license_order_number',
                'license_domain',
                'license_date',
                'license_updates_date',
                'license_status'
            )
            ->withAggregate(['product as product_title'], 'product_title')
            ->withAggregate(['clients as client_email'], 'client_email')
            ->when($searchQuery, function ($query) use ($searchQuery, $request,$fields,$searchable) {
                    $query->when(in_array('client_email', $fields), function ($query) use ($searchQuery) {
                        $query->orWhereHas('clients', function ($query) use ($searchQuery) {
                            $query->where('client_email', 'like', '%' . $searchQuery . '%');
                        });
                    })
                    ->when(in_array('product_title', $fields), function ($query) use ($searchQuery) {
                        $query->orWhereHas('product', function ($query) use ($searchQuery) {
                            $query->where('product_title', 'like', '%' . $searchQuery . '%');
                        });
                    });
                foreach ($fields as $field) {
                        $query->when(in_array($field,$searchable), function ($query) use ($field, $searchQuery) {
                            if($field == 'license_status'){
                                $query->orWhere($field, 'like', '%' . statusFormatter($searchQuery) . '%');
                            }
                            else if($field == 'license_code'){
                                $query->orWhere($field, 'like', '%' . str_replace('-','',$searchQuery) . '%');
                            }
                            else{
                                $query->orWhere($field, 'like', '%' . $searchQuery . '%');
                            }
                        });
                }
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);
        $licenseQuery->getCollection()->transform(function ($license) {
            $license->license_order_url = $license->order_url;
            $license->installation_counts = $license->installation_count;
            $license->latest_call_backs = $license->latest_call_back;
            $license->call_backs_count = $license->call_backs->count();
            return $license;
        });
        return successResponse(Lang::get('lang.License_show'), $licenseQuery, 200);
    }

    public function edit($license_id)
    {
        $license = AflLicenses::where('license_id', $license_id)->firstOrFail();
        $product_name = AflLicenses::join('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')->where('afl_licenses.license_id', $license_id)
            ->get(['afl_products.product_title', 'afl_licenses.product_id']);

        $client_name = AflClients::select(DB::raw('CONCAT(client_fname, " ", client_lname,"<",client_email,">") AS full_name'), 'users.client_id')
            ->join('afl_licenses', 'afl_licenses.client_id', '=', 'users.client_id')->where('afl_licenses.license_id', $license_id)
            ->get('full_name', 'users.client_id');

        if (! empty($license)) {
            return successResponse('', ['license' => $license, 'product_name' => $product_name, 'client_name' => $client_name], 200);
        }

        return errorResponse(Lang::get('lang.invalid'), 400);
    }

    /**
     * To Format the client
     *
     * @param $license_code
     * @param $client_email
     * return a formatted array of license code and client email
     */
    public function formatClient($license_code, $client_email)
    {
        if (! empty($license_code)) {
            $client_formatted = $license_code;
        } else {
            if (filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
                $client_formatted = $client_email;
            } else {
                $client_formatted = 'Unknown Client';
            }
        }

        return $client_formatted;
    }

    /***
     * Just performs some license checks while adding a license in faveo license manager.
     */
    protected function licenseChecks($client_id, $license_code, $license_ip, $license_domain, $license_limit, $license_expire_date, $license_updates_date, $license_support_date)
    {
        if (! aflValidateIntegerValue($client_id) && empty($license_code)) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.error_client_or_license_code'), 400);
        }

        if (aflValidateIntegerValue($client_id) && ! empty($license_code)) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_licnese'), 400);
        }

        if (! empty($license_ip)) {
            $license_ips_array = explode(',', $license_ip);
            foreach ($license_ips_array as $ip_to_validate) {
                if (! filter_var($ip_to_validate, FILTER_VALIDATE_IP)) {
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.invalid_license_ip'), 400);
                    break;
                }
            }
        }

        if (! empty($license_domain)) {
            $license_domain_array = explode(',', $license_domain);
            foreach ($license_domain_array as $license_domain_array_key => $license_domain_array_value) {
                if (! aflValidateRawDomain(aflGetRawDomain($license_domain_array_value)) || ! ctype_alnum(substr($license_domain_array_value, -1))) { //invalid TLD, scheme included, or last symbol is not alphanumeric (most likely ends with / or another non-alphanumeric character)
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.invalid_domain'), 400);
                }
            }
        }

        if (! empty($license_limit) && ! aflValidateIntegerValue($license_limit)) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_license_limit'), 400);
        }

        if (! empty($license_expire_date) && ! aflVerifyDateTime($license_expire_date, 'Y-m-d')) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_license_expiry'), 400);
        }

        if (! empty($license_updates_date) && ! aflVerifyDateTime($license_updates_date, 'Y-m-d')) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_license_update_date'), 400);
        }

        if (! empty($license_support_date) && ! aflVerifyDateTime($license_support_date, 'Y-m-d')) {
            $api_error_detected = 1;

            return errorResponse(Lang::get('lang.invalid_license_support_date'), 400);
        }
    }

    public function reissueLicenseCloud(Request $request){
        AflInstallations::where('license_code',$request->get('license_code'))->delete();
    }

    public function licenseDeactivate(Request $request){
        $this->licenseDeactivating($request->get('license_code'));
    }

    public function licenseDeactivating($license_code){
        AflLicenses::where('license_code',$license_code)->update([ 'license_status'=> 0]);
    }

    public function updateTheLicenseCode(Request $request){
         $this->updatingLicenseCode($request->old_license_code, $request->license_code);
    }

    public function updatingLicenseCode($old_license_code, $new_license_code)
    {
        AflLicenses::where('license_code',$old_license_code)
            ->update(['license_code'=> $new_license_code]);
    }

    public function getLicenseColumns()
    {
        $userId = getAuthUserId();
        $userColumns = LicenseColumn::where('client_id', $userId)->where('type', 'license')->pluck('column_id');
        if ($userColumns->isEmpty()) {
            $defaultColumns = ReportColumn::where('type', 'license')->where('default', true)->pluck('key');
            return successResponse('',$defaultColumns);
        }
        $defaultColumns = ReportColumn::where('type', 'license')->whereIn('id', $userColumns)->pluck('key');
        return successResponse('',$defaultColumns);
    }
    public function saveLicenseColumns(Request $request)
    {
        $userId = getAuthUserId();
        $selectedColumns = $request->selected_columns;
        $reportColumns = ReportColumn::whereIn('key', $selectedColumns)
            ->where('type', 'license')
            ->pluck('id', 'key');
        LicenseColumn::where('client_id', $userId)->where('type', 'license')->delete();

        foreach ($selectedColumns as $columnKey) {
            if (isset($reportColumns[$columnKey])) {
                LicenseColumn::create([
                    'client_id' => $userId,
                    'column_id' => $reportColumns[$columnKey],
                    'type' => 'license',
                ]);
            }
        }
        return successResponse(Lang::get('lang.column_saved'));
    }
    public function syncTheCreationOfLicense(Request $request)
    {
        $data = [
            'license_code' => $request->input('license_code'),
            'product_ids' => $request->input('product_ids'),
            'options' => $request->input('options', '[]'),
        ];

        $result = $this->processSyncLicense($data);

        if (! $result['success']) {
            return response()->json(['error' => $result['message']], 404);
        }

        return response()->json(['message' => 'License synchronization and options insertion complete']);
    }

    public function processSyncLicense(array $data): array
    {
        try {
            $licenseCode = $data['license_code'] ?? null;
            $productIds = $data['product_ids'] ?? [];
            $options = $data['options'] ?? [];

            if (is_string($productIds)) {
                $productIds = explode(',', $productIds);
            }

            if (is_string($options)) {
                $options = json_decode($options, true) ?: [];
            }

            $license = AflLicenses::where('license_code', $licenseCode)->first();

            if (! $license) {
                return ['success' => false, 'data' => [], 'message' => 'License not found'];
            }

            foreach ($productIds as $productId) {
                LicensePlugin::updateOrCreate(
                    ['license_id' => $license->license_id, 'product_id' => $productId],
                    ['license_id' => $license->license_id, 'product_id' => $productId]
                );
            }

            foreach ($options as $option) {
                LicenseOption::updateOrCreate(
                    [
                        'license_id' => $license->license_id,
                        'product_id' => $option['product_id'],
                        'option_group' => $option['option_group'],
                        'option_name' => $option['option_name'],
                        'key' => $option['key'],
                    ],
                    ['value' => $option['value']]
                );
            }

            return ['success' => true, 'data' => [], 'message' => ''];
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return ['success' => false, 'data' => [], 'message' => $e->getMessage()];
        }
    }



    public function licenseInfo(Request $request)
    {
        // Retrieve license information or throw 404 error if not found
        $license = AflLicenses::where('license_code', $request->input('license_code'))->firstOrFail();

        // Retrieve product information related to the license
        $product = AflProducts::find($license->product_id);

        // Retrieve addon information related to the license
        $addons = $license->addonProducts()->with(['latestVersion'])->get()->map(function ($product) {
            return [
                'product_id' => $product->product_id,
                'product_name' => $product->product_title,
                'product_attributes' => $product->product_attributes,
                'product_attributes_license' => $product->pivot->product_attributes_license,
                'latest_version' => optional($product->latestVersion)->version_number,
                'latest_version_file' => optional($product->latestVersion)->version_upgrade_file,
            ];
        });

        // Return success response with formatted data
        return successResponse(
            Lang::get('lang.license_info'),
            [
                'license' => $license,
                'product' => $product,
                'addons' => $addons,
            ],
            200
        );
    }

    public function individualLicenseInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        $licenseCode = $request->input('license_code');

        // Retrieve the license with the given code and load related license options
        $license = AflLicenses::where('license_code', $licenseCode)
            ->with('licenseOptions') // Eager load license options
            ->first();

        // Check if license is found
        if (!$license) {
            return successResponse('', []);
        }

        // Format the license options data to include license_code
        $licenseOptions = $license->licenseOptions->map(function($option) use ($license) {
            return [
                'license_code' => $license->license_code,
                'product_id' => $option->product_id,
                'option_group' => $option->option_group,
                'option_name' => $option->option_name,
                'key' => $option->key,
                'value' => $option->value,
            ];
        })->toArray();

        return successResponse('', $licenseOptions);
    }

    public function giveLicenseTakeOrder(Request $request){
        return successResponse('', AflLicenses::where('license_code', $request->input('license_code'))->value('license_order_number'));
    }

    public function getPluginInfo(Request $request)
    {
        $licenseCodes = json_decode($request->input('license_code'), true);

        $result = $this->getLicensePluginData($licenseCodes);

        return successResponse('', $result->toJson());
    }
    public function getLicensePluginData(array $licenseCodes)
    {
        $licenseCodes = collect($licenseCodes);

        $licenses = AflLicenses::whereIn('license_code', $licenseCodes)
            ->where(function ($q) {
                $q->where('license_expire_date', '>', \Carbon\Carbon::now())
                    ->orWhere('license_expire_date', '0000:00:00');
            })
            ->get()
            ->keyBy('license_code');

        return $licenseCodes->map(function ($licenseCode) use ($licenses) {

            $license = $licenses->get($licenseCode);

            if (!$license) {
                return null;
            }

            $productIds = LicensePlugin::where('license_id', $license->license_id)
                ->pluck('product_id')
                ->toArray();

            $productIds = !empty($productIds) ? $productIds : [$license->product_id];

            return collect($productIds)
                ->unique()
                ->map(function ($productId) use ($licenseCode) {
                    return $this->generateLicenseData($productId, $licenseCode);
                })
                ->filter();

        })->filter()->values();
    }

    private function generateLicenseData($product_id, $license_code)
    {
        $product = AflProducts::find($product_id);
        $cloud = AfuProducts::find($product_id);

        $version = AfuVersions::where('product_id', $product_id)
            ->orderBy('version_id', 'desc')
            ->first();

        $installed = AflInstallations::where('product_id', $product_id)
            ->where('license_code', $license_code)
            ->exists();

        return (!$product || !$cloud || !$version || $installed) ? null :
        [
            'product_id' => $product_id,
            'product_name' => $product->product_title,
            'product_key' => $cloud->product_key,
            'product_description' => $product->product_description,
            'version' => $version->version_number,
            'license_code' => $license_code,
            'path' => $cloud->product_path,
        ];
    }
}
