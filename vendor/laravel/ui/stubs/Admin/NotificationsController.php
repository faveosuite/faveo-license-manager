<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AflNotifications;
use App\Http\Requests\NotificationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;



/**
 * Consist of functionalities for the custom Notification page in Auto Faveo licenser 
 * Class NotificationsController
 * @package App\Http\Controllers\Admin
 */
class NotificationsController extends Controller
{

  /**
   * To ADD or UPDATE custom notifications field of license manager
   * @param NotificationRequest $request
   * @param $notification_id
   * @return success response if the record is added or updated
   */
  public function notifications(NotificationRequest $request, $notification_id){

      $notific = AflNotifications::find($notification_id);
      if(empty($notific)){
          $not = new AflNotifications(array(
            'notification_product_not_found' => $request->get('notification_product_not_found'),
            'notification_product_inactive' => $request->get('notification_product_inactive'),
            'notification_license_ok' => $request->get('notification_license_ok'),
            'notification_license_not_found' =>  $request->get('notification_license_not_found'),
            'notification_invalid_ip' =>  $request->get('notification_invalid_ip'),
            'notification_invalid_domain' =>  $request->get('notification_invalid_domain'),
            'notification_domain_required' =>  $request->get('notification_domain_required'),
            'notification_domain_in_use' => $request->get('notification_domain_in_use'),
            'notification_license_suspended' => $request->get('notification_license_suspended'),
            'notification_license_expired' => $request->get('notification_license_expired'),
            'notification_updates_expired' => $request->get('notification_updates_expired'),
            'notification_support_expired'=>  $request->get('notification_support_expired'),
            'notification_license_cancelled' => $request->get('notification_license_cancelled'),
            'notification_license_limit' => $request->get( 'notification_license_limit'),
            'notification_installation_not_found'=> $request->get('notification_installation_not_found'),
            'notification_invalid_signature' => $request->get( 'notification_invalid_signature'),
            'notification_host_banned' => $request->get('notification_host_banned'),
            'notification_unknown_error' => $request->get( 'notification_unknown_error')
            ));
            $not->save();
            return successResponse(Lang::get('lang.notifications'),$not,201);

      }
      else{
              $notific->notification_product_not_found = $request->get('notification_product_not_found');
              $notific->notification_product_inactive = $request->get('notification_product_inactive');
              $notific->notification_license_ok = $request->get('notification_license_ok');
              $notific->notification_license_not_found =  $request->get('notification_license_not_found');
              $notific->notification_invalid_ip =  $request->get('notification_invalid_ip');
              $notific->notification_invalid_domain =  $request->get('notification_invalid_domain');
              $notific->notification_domain_required =  $request->get('notification_domain_required');
              $notific->notification_domain_in_use = $request->get('notification_domain_in_use');
              $notific->notification_license_suspended = $request->get('notification_license_suspended');
              $notific->notification_license_expired = $request->get('notification_license_expired');
              $notific->notification_updates_expired = $request->get('notification_updates_expired');
              $notific->notification_support_expired=  $request->get('notification_support_expired');
              $notific->notification_license_cancelled = $request->get('notification_license_cancelled');
              $notific->notification_license_limit = $request->get( 'notification_license_limit');
              $notific->notification_installation_not_found= $request->get('notification_installation_not_found');
              $notific->notification_invalid_signature = $request->get( 'notification_invalid_signature');
              $notific->notification_host_banned = $request->get('notification_host_banned');
              $notific->notification_unknown_error = $request->get( 'notification_unknown_error');
              $notific->save();
              return successResponse(Lang::get('lang.notifications'),$notific,200);
      }

  }
}
