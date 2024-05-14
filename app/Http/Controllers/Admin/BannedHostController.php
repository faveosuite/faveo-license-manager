<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannedHostRequest;
use App\Models\AflBannedHosts;
use App\Models\AflWhitelistIps;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

/**
 * Consist of functionalities for the Banned Host page in Auto Faveo licenser
 * Class BannedHostController
 */
class BannedHostController extends Controller
{
    public function __construct(Request $request)
    {
        $this->ip_address = request()->server('REMOTE_ADDR');
    }

    /**
     *To Add Banned hosts of License manager
     *
     *@param  BannedHostRequest  $request
     *@param    $api_key_secret
     *@param $banned_host_ip
     *@param $banned_host_comments
     *@return array of details of banned host if added successfully
     */
    public function bannedHostAdd(BannedHostRequest $request)
    {
        $api_key_secret = $request->input('api_key_secret');
        $banned_host_ip = $request->input('banned_host_ip');
        $banned_host_comments = $request->input('banned_host_comments','');
        $banned_host_blocks = $request->input('banned_host_blocks',1);
        $banned_host_last_block_date = $request->input('banned_host_last_block_date', Carbon::now());

        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);
        $banned_host_date = \date('y-m-d');
        if (empty($banned_host_ip) || $api_action_success != 1) {
            return errorResponse(Lang::get('lang.banned_empty'), 400);
        }
        $whitelistIpExists = AflWhitelistIps::where('whitelist_host_ip', $banned_host_ip)->exists();
        if ($whitelistIpExists) {
            return errorResponse(Lang::get('lang.banned_ip_in_whitelist'), 400);
        }
        $banned = new AflBannedHosts([
            'banned_host_ip' => $banned_host_ip,
            'banned_host_comments' => $banned_host_comments,
            'banned_host_date' => $banned_host_date,
            'banned_host_blocks' => $banned_host_blocks,
            'banned_host_last_block_date' => $banned_host_last_block_date,
        ]);
        $banned->save();

        return successResponse(Lang::get('lang.banned_add'), $banned, 201);
    }

    /**
     *To Edit Banned hosts of License manager
     *
     *@param  BannedHostRequest  $request
     *@param    $api_key_secret
     *@param $banned_host_ip
     *@param $banned_host_comments
     * @return array of details of edited banned host if Updated successfully
     */
    public function bannedHostUpdate(Request $request)
    {
        $banned_host_id = $request->get('banned_host_id');
        $api_key_secret = $request->get('api_key_secret');
        $banned_host_ip = $request->get('banned_host_ip');
        $banned_host_comments = $request->get('banned_host_comments');

        if (empty($banned_host_id) || ! aflValidateIntegerValue($banned_host_id) ||
        empty($rows_array = AflBannedHosts::where('banned_host_id', $banned_host_id)->get()->toArray())) { //invalid record
            return errorResponse(Lang::get('lang.banned_host_not_found'), 404);
        }
        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);
        if (empty($banned_host_ip) || $api_action_success != 1) {
            return errorResponse(Lang::get('lang.banned_empty'), 400);
        }
        $whitelistIpExists = AflWhitelistIps::where('whitelist_host_ip', $banned_host_ip)->exists();
        if ($whitelistIpExists) {
            return errorResponse(Lang::get('lang.banned_ip_in_whitelist'), 400);
        }
        $banned = AflBannedHosts::where('banned_host_id', $banned_host_id)->update([
            'banned_host_ip' => $banned_host_ip,
            'banned_host_comments' => $banned_host_comments,
        ]);

        return successResponse(Lang::get('lang.banned_edit'), $banned, 201);
    }

    /**
     *To Delete Banned hosts of License manager
     *
     *@param $banned_host_id
     * @return success response of how many records deleted if deleted successfully
     */
    public function deleteBannedHost(Request $request)
    {
        $api_key_secret = $request->get('api_key_secret');
        $removed_records = 0;
        $banned_host_id = $request->get('banned_host_id');
        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);
        if ($api_action_success != 1 || ! aflValidateIntegerValue($banned_host_id)) {
            return errorResponse(Lang::get('lang.banned_empty'), 400);
        }
        $banned_ip = DB::table('afl_banned_hosts')
                      ->where('banned_host_id', $banned_host_id)
                      ->value('banned_host_ip');

        DB::table('afl_failed_logins')
                        ->where('failed_login_ip', $banned_ip)
                        ->delete();
        $removed_records += AflBannedHosts::where('banned_host_id', $banned_host_id)->delete();

        return successResponse(Lang::get('lang.delete'), $removed_records, 201);
    }

    /**
     * Returns the list of all the banned host present for this application.
     */
    public function show(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order') ? $request->input('sort_order') : 'desc';
        $sortField = $request->input('sort_field') ? $request->input('sort_field') :'banned_host_id';

        $banned = AflBannedHosts::where(function($query) use ($searchQuery) {
        $query->where('banned_host_ip', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('banned_host_comments', 'LIKE', '%' . $searchQuery . '%');
    })
        ->orderBy($sortField, $sortOrder)
        ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.Banned_Show'), $banned, 200);
    }

    public function view($banned_host_id)
    {
        $banned_host_data = AflBannedHosts::where('banned_host_id', $banned_host_id)->firstOrFail();

        if (! empty($banned_host_data)) {
            return successResponse('', ['banned_host_data' => $banned_host_data], 200);
        }

        return errorResponse(Lang::get('lang.invalid'), 400);
    }
}
