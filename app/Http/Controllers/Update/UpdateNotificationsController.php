<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use App\Models\AfuNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Lcobucci\Jose\Parsing\Exception;

class UpdateNotificationsController extends Controller
{
    public function updateNotificationFields(Request $request, $notification_id)
    {
        $afuNotifications = AfuNotifications::where('notification_id', $notification_id)->firstOrFail();
        if (! empty($afuNotifications)) {
            try {
                $afuNotifications->notification_operation_ok = $request->get('notification_operation_ok');
                $afuNotifications->notification_product_not_found = $request->get('notification_product_not_found');
                $afuNotifications->notification_product_inactive = $request->get('notification_product_inactive');
                $afuNotifications->notification_product_no_versions = $request->get('notification_product_no_versions');
                $afuNotifications->notification_version_not_found = $request->get('notification_version_not_found');
                $afuNotifications->notification_version_inactive = $request->get('notification_version_inactive');
                $afuNotifications->notification_version_expired = $request->get('notification_version_expired');
                $afuNotifications->notification_install_limit_reached = $request->get('notification_install_limit_reached');
                $afuNotifications->notification_upgrade_limit_reached = $request->get('notification_upgrade_limit_reached');
                $afuNotifications->notification_install_archive_not_found = $request->get('notification_install_archive_not_found');
                $afuNotifications->notification_install_query_not_found = $request->get('notification_install_query_not_found');
                $afuNotifications->notification_upgrade_archive_not_found = $request->get('notification_upgrade_archive_not_found');
                $afuNotifications->notification_upgrade_query_not_found = $request->get('notification_upgrade_query_not_found');
                $afuNotifications->notification_raw_install_query_not_found = $request->get('notification_raw_install_query_not_found');
                $afuNotifications->notification_raw_upgrade_query_not_found = $request->get('notification_raw_upgrade_query_not_found');
                $afuNotifications->notification_installation_not_verified = $request->get('notification_installation_not_verified');
                $afuNotifications->notification_invalid_parameter = $request->get('notification_invalid_parameter');
                $afuNotifications->notification_invalid_signature = $request->get('notification_invalid_signature');
                $afuNotifications->notification_host_banned = $request->get('notification_host_banned');
                $afuNotifications->notification_unknown_error = $request->get('notification_unknown_error');

                $afuNotifications->save();

                return successResponse(Lang::get('lang.'), $afuNotifications, 200);
            } catch (Exception $exception) {
                return $exception->getMessage();
            }
        }
    }

    public function show()
    {
        $afu = AfuNotifications::all();

        return $afu;
    }
}
