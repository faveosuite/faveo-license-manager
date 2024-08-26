<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\InstallationLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        if (null !== (request()->server('REMOTE_ADDR'))) {
            $this->ip_address = request()->server('REMOTE_ADDR');
        } else {
            $this->ip_address = request()->ip();
        }
    }
    public function deleteOrder(Request $request)
    {
        $api_key_secret = $request->get('api_key_secret');
        $license_code = $request->get('license_code');

        $api_key = new ApiKeysController();
        $api_action_success = $api_key->apiKeyCheck($api_key_secret, $this->ip_address);

        if ($api_action_success == 1 && !empty($license_code)) {
            try {
                // Use a transaction to ensure all or none of the deletions happen
                $removed_records = DB::transaction(function () use ($license_code) {
                    $deletedLicenses = AflLicenses::where('license_code', $license_code)->delete();
                    $deletedInstallations = AflInstallations::where('license_code', $license_code)->delete();
                    $deletedLogs = InstallationLogs::where('license_code', $license_code)->delete();
                    $deletedCallbacks = AflCallbacks::where('license_code', $license_code)->delete();

                    // Return the total number of deleted records
                    return $deletedLicenses + $deletedInstallations + $deletedLogs + $deletedCallbacks;
                });
                return successResponse(\Lang::get('lang.delete'), $removed_records, 200);
            } catch (\Exception $e) {
                return errorResponse(\Lang::get('lang.invalid'), 400);
            }
        }

        return errorResponse(\Lang::get('lang.invalid'), 400);
    }
}
