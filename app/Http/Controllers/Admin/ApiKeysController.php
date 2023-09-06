<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\UpdateApiRequest;
use App\Models\AflApiKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

/**
 * Consist of functionalities for the Api keys Generation page in Auto Faveo licenser
 * Class ApiController
 */
class ApiKeysController extends Controller
{
    /**
     * To Add Api keys to the license manager
     *
     * @param  ApiRequest  $request
     * @return success response if added successfuly
     */
    public function apiKeyAdd(ApiRequest $request)
    {
        $error_detected = 0;
        try {
            $api = new AflApiKeys([
                'api_key_secret' => $request->get('api_key_secret'),
                'api_key_ip' => $request->get('api_key_ip'),
                'api_key_clients_add' => $request->get('api_key_clients_add'),
                'api_key_clients_edit' => $request->get('api_key_clients_edit'),
                'api_key_licenses_add' => $request->get('api_key_licenses_add'),
                'api_key_licenses_edit' => $request->get('api_key_licenses_edit'),
                'api_key_products_add' => $request->get('api_key_products_add'),
                'api_key_products_edit' => $request->get('api_key_products_edit'),
                'api_key_installations_edit' => $request->get('api_key_installations_edit'),
                'api_key_search' => $request->get('api_key_search'),
                'api_key_status' => $request->get('api_key_status'),
                'api_key_description' => $request->get('api_key_description'),
            ]);
            if (! empty($request->get('api_key_ip'))) {
                $api_key_ips_array = explode('.', str_replace(' ', '', $request->get('api_key_ip'))); //remove all space symbols (if any) between IPs
                foreach ($api_key_ips_array as $ip_to_validate) {
                    if (! filter_var($ip_to_validate, FILTER_VALIDATE_IP)) {
                        $error_detected = 1;
                        errorResponse(Lang::get('lang.invalid'), 400);
                        break;
                    }
                }
            }
            $api->save();

            return successResponse(Lang::get('lang.add'), $api, 201);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * To Update Api keys to the license manager
     *
     * @param  ApiRequest  $request
     * @param $api_key_id
     * @return success response if Updated successfuly
     */
    public function apiKeyUpdate(UpdateApiRequest $request, $api_key_id)
    {
        //dd($request);
       //dd(! empty($request->get('api_key_ip')));

        if (! empty($request->get('api_key_ip'))) {
            $api_key_ips_array = explode('.', str_replace(' ', '', $request->get('api_key_ip'))); //remove all space symbols (if any) between IPs
            foreach ($api_key_ips_array as $ip_to_validate) {
                if (! filter_var($ip_to_validate, FILTER_VALIDATE_IP)) {
                    $error_detected = 1;
                    errorResponse(Lang::get('lang.invalid'), 400);
                    break;
                }
            }
        }


        $updateapi = DB::table('afl_api_keys')
                   ->where('api_key_id', $api_key_id)
                   ->update([
                       'api_key_secret' => $request->get('api_key_secret'),
                       'api_key_ip' => $request->get('api_key_ip'),
                       'api_key_clients_add' => $request->get('api_key_clients_add'),
                       'api_key_clients_edit' => $request->get('api_key_clients_edit'),
                       'api_key_licenses_add' => $request->get('api_key_licenses_add'),
                       'api_key_licenses_edit' => $request->get('api_key_licenses_edit'),
                       'api_key_products_add' => $request->get('api_key_products_add'),
                       'api_key_products_edit' => $request->get('api_key_products_edit'),
                       'api_key_installations_edit' => $request->get('api_key_installations_edit'),
                       'api_key_search' => $request->get('api_key_search'),
                       'api_key_status' => $request->get('api_key_status'),
                       'api_key_description' => $request->get('api_key_description'),
            ]);
        if (! aflValidateIntegerValue($updateapi)) {
            return errorResponse(Lang::get('lang.invalid'), 400);
        } else {
            return successResponse(Lang::get('lang.apiUpdate'), $updateapi, 200);
        }
    }

    /**
     * To Delete Api keys to the license manager
     *
     * @param $api_key_id
     * @return success response if Delete successfully
     */
    public function apiKeyDelete($api_key_id)
    {
        if (aflValidateIntegerValue($api_key_id)) {
            $removed_records = AflApiKeys::where('api_key_id', $api_key_id)->delete(); //doMysqlQuery("DELETE FROM apl_api_keys WHERE api_key_id=?", array($api_key_id), array("i"));
        }

        return successResponse(Lang::get('lang.Delete'), $removed_records, 200);
    }

    public function show()
    {
        $apis = AflApiKeys::latest()->get();

        return successResponse(Lang::get('lang.Api_show'), $apis, 200);
    }

    /**
     * This method is used to check if the api key sent in every request is valid
     *
     * @param $api_key_secret
     * @param $ip_address
     * This return 1 or 0 depending on the success only then rest of the functionalities can be accessed
     */
    public function apiKeyCheck($api_key_secret, $ip_address)
    {
        if (! empty($api_key_secret)) {
            $api = AflApiKeys::where('api_key_secret', $api_key_secret)->where('api_key_status', 1)->get()->toArray();
            if (empty($api)) {
                return 0;
            } else {
                $api_ip = new AflApiKeys();
                $api_ips = $api_ip->value('api_key_ip');
                if (! empty($api_ips)) {
                    if (! $api_ips->contains($ip_address)) {
                        return 0;
                    } else {
                        return 1;
                    }
                } else {
                    return 1;
                }
            }
        }
    }

    public function view($api_key_id)
    {
        $api_key = AflApiKeys::where('api_key_id', $api_key_id)->firstOrFail();

        if (! empty($api_key)) {
            return successResponse('', ['api_key' => $api_key], 200);
        }

        return errorResponse(Lang::get('lang.invalid'), 400);
    }
}
