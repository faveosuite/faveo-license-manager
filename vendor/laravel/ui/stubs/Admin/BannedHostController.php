<?php

namespace App\Http\Controllers\Admin;

use App\Models\AflBannedHosts;
use App\Models\AflApiKeys;
use App\Models\AflFailedLogins;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Settings;
use App\Traits\Version;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Http\Requests\BannedHostRequest;

class BannedHostController extends Controller
{
//use Settings, Version;

public function bannedHostAdd(BannedHostRequest $request,$api_key_secret,$banned_host_ip,$banned_host_comments=null)

     {
        $api_action_success=0;
        $api_error_detected=0;  
        if (null!==(\request()->server('REMOTE_ADDR'))) 
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
            return \errorResponse(Lang::get('lang.invalid_api_key'),404);
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
                    return \response(['message' => 'Api Access from this ip is not allowed']);
                    }
                    else{
                        $api_action_success=1;
                    }
          }
        }

    if(empty($banned_host_comments)){
        $banned_host_comments = $request->get('banned_host_comments');
    }
    $banned_host_date = \date("y-m-d");

    if(!empty($banned_host_ip) && $api_action_success==1){
    $banned = new AflBannedHosts(array(
                       'banned_host_ip' => $banned_host_ip,
                       'banned_host_comments' => $banned_host_comments,
                       'banned_host_date' => $banned_host_date,
                       'banned_host_blocks' => $request->get('banned_host_blocks'),
                       'banned_host_last_block_date' => $request->get('banned_host_last_block_date')
                       ));
    $banned->save();
    return \successResponse(Lang::get('lang.banned_add'),$banned,201);
}  
else{
     return \errorResponse(Lang::get('lang.invalid'),400);
}
    }
}

 public function bannedHostUpdate(BannedHostRequest $request,$api_key_secret, $banned_host_id, $banned_host_ip, $banned_host_comments=null) 
{
        $api_action_success=0;
        $api_error_detected=0;  
        if (null!==(\request()->server('REMOTE_ADDR'))) 
        {
             $ip_address=request()->server('REMOTE_ADDR');
             } 
             else {
                 $ip_address=$request->ip();
                 }

if (empty($banned_host_id) || !aflValidateIntegerValue($banned_host_id) || 
    empty($rows_array=AflBannedHosts::where('banned_host_id',$banned_host_id)->get()->toArray())) //invalid record
    {
    return \errorResponse(Lang::get('lang.invalid'));
    exit();
    }

       if(!empty($api_key_secret))
       {
        $api = AflApiKeys::where('api_key_secret',$api_key_secret)->where('api_key_status',1)->get();
        if(empty($api))
        {
            return \errorResponse(Lang::get('lang.invalid_api_key'),404);
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
                    return \response(['message' => 'Api Access from this ip is not allowed']);
                    }
                    else{
                        $api_action_success=1;
                    }
          }
        }

    if(empty($banned_host_comments)){
        $banned_host_comments = $request->get('banned_host_comments');
    }
    $banned_host_date = \date("y-m-d");

    if(!empty($banned_host_ip) && $api_action_success==1){
    $banned = AflBannedHosts::where('banned_host_id',$banned_host_id)->update([
                       'banned_host_ip' => $banned_host_ip,
                       'banned_host_comments' => $banned_host_comments,
                       ]);
    
    
    return \successResponse(Lang::get('lang.banned_edit'),$banned,201);
    }  
else{
     return \errorResponse(Lang::get('lang.invalid'),400);
}
    }
   
}

//delete banned host
public function deleteBannedHost($banned_host_id)
    {
    $removed_records=0;

    if (aflValidateIntegerValue($banned_host_id))
        {

         $banned_ip = DB::table('afl_banned_hosts')
                      ->where('banned_host_id', $banned_host_id)
                      ->value('banned_host_ip');
        
        DB::table('afl_failed_logins')
           ->where('failed_login_ip',$banned_ip)
           ->delete();
           
        //doMysqlQuery("DELETE FROM apl_failed_logins WHERE failed_login_ip=(SELECT banned_host_ip FROM apl_banned_hosts WHERE banned_host_id=?)", array($banned_host_id), array("i")); //delete failed login attempts
         $removed_records += AflBannedHosts::where('banned_host_id',$banned_host_id)->delete();
       // $removed_records+=doMysqlQuery("DELETE FROM apl_banned_hosts WHERE banned_host_id=?", array($banned_host_id), array("i"));
        }

    return successResponse(Lang::get('lang.delete'),$removed_records,201);
    }
}
