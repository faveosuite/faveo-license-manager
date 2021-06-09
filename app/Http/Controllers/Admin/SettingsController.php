<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AflSettings;
use App\Http\Requests\Settings\GeneralSettingsRequest;
use App\Http\Requests\Settings\AdvancedSettingRequest;
use App\Http\Requests\Settings\SecuritySettingRequest;
use App\Http\Requests\Settings\EmailSettingRequest;
use App\Http\Requests\Settings\CleanUpSettingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class SettingsController extends Controller
{

    
    public function generalSettingsCreate(GeneralSettingsRequest $request,$SETTING_ID){

     $genset = AflSettings::find($SETTING_ID);
    if(empty($genset)){
        $gen =new AflSettings([
            'SMART_REPORTS'=>$request->get('SMART_REPORTS'),
            'SMART_TABLES'=>$request->get('SMART_TABLES'),
            'RECORDE_ON_ADMIN_PAGE'=>$request->get('RECORDE_ON_ADMIN_PAGE'),
            'RECORDE_ON_INDEX_PAGE'=>$request->get('RECORDE_ON_INDEX_PAGE'),
            'RECORDE_ON_SEARCH_PAGE'=> $request->get('RECORDE_ON_SEARCH_PAGE'),
            'RECORDE_ARCHIVE_DAYS'=> $request->get('RECORDE_ARCHIVE_DAYS'),
            'TIMEZONE'=> $request->get('TIMEZONE'),

        ]);
        $gen->save();
        return \successResponse(Lang::get('lang.settings_created'),$gen,201);
    }
    else{
            
            $genset->SMART_REPORTS = $request->get('SMART_REPORTS');
            $genset->SMART_TABLES = $request->get('SMART_TABLES');
            $genset->RECORDE_ON_ADMIN_PAGE=$request->get('RECORDE_ON_ADMIN_PAGE');
            $genset->RECORDE_ON_INDEX_PAGE=$request->get('RECORDE_ON_INDEX_PAGE');
            $genset->RECORDE_ON_SEARCH_PAGE=  $request->get('RECORDE_ON_SEARCH_PAGE');
            $genset->RECORDE_ARCHIVE_DAYS = $request->get('RECORDE_ARCHIVE_DAYS');
            $genset->TIMEZONE=  $request->get('TIMEZONE');
            $genset->save();
            return successResponse(Lang::get('lang.settings_updated'),$genset,200);
        
    }
   
    }

    public function advancedSettings(AdvancedSettingRequest $request,$SETTING_ID){
          
          $advset = AflSettings::find($SETTING_ID);
          if(empty($advset)){
              $adv = new AflSettings([
                  'API_STATUS' => $request->get('API_STATUS'),
                  'ENVATO_API_TOKEN' => $request->get('ENVATO_API_TOKEN')
              ]);
              $adv->save();
              return \successResponse(Lang::get('lang.settings_created'),$adv,201);        
              }
              else{
                  $advset->API_STATUS = $request->get('API_STATUS');
                  $advset->ENVATO_API_TOKEN = $request->get('ENVATO_API_TOKEN');
                  $advset->save();
                  return \successResponse(Lang::get('lang.settings_updated'),$advset,200);
              }
          
    }

    public function securitySettings(SecuritySettingRequest $request,$SETTING_ID){

        $secset = AflSettings::find($SETTING_ID);
        if(empty($secset)){
            $sec = new AflSettings([
            'MIN_PASSWORD_LENGTH'=> $request->get('MIN_PASSWORD_LENGTH'),
            'WHITELISTED_ACCESS' => $request->get('WHITELISTED_ACCESS'),
            'BANNED_HOSTS' => $request->get('BANNED_HOSTS'),
            'BANNED_HOST_MESSAGE' => $request->get('BANNED_HOST_MESSAGE'),
            'FAILED_LOGINS_LIMIT' => $request->get('FAILED_LOGINS_LIMIT'),
            'FAILED_LICENSINGS_LIMIT' => $request->get('FAILED_LICENSINGS_LIMIT'),
            'FAILED_HOSTS_FORGET' => $request->get('FAILED_HOSTS_FORGET'),
            'WHITELISTED_IP' => $request->get('WHITELISTED_IP')
            ]);
            $sec->save();
            return \successResponse(Lang::get('lang.settings_created'),$sec,201);
        }
        else{
            $secset->MIN_PASSWORD_LENGTH= $request->get('MIN_PASSWORD_LENGTH');
             $secset->WHITELISTED_ACCESS = $request->get('WHITELISTED_ACCESS');
             $secset->BANNED_HOSTS = $request->get('BANNED_HOSTS');
             $secset->BANNED_HOST_MESSAGE = $request->get('BANNED_HOST_MESSAGE');
             $secset->FAILED_LOGINS_LIMIT = $request->get('FAILED_LOGINS_LIMIT');
             $secset->FAILED_LICENSINGS_LIMIT = $request->get('FAILED_LICENSINGS_LIMIT');
             $secset->FAILED_HOSTS_FORGET = $request->get('FAILED_HOSTS_FORGET');
             $secset->WHITELISTED_IP = $request->get('WHITELISTED_IP');
             $secset->save();
             return \successResponse(lang::get('lang.settings_updated'),$secset,200);
        }

    }

    public function emailSettings(EmailSettingRequest $request,$SETTING_ID){

          $emaset = AflSettings::find($SETTING_ID);
        if(empty($emaset)){
            $ema = new AflSettings([
            'EMAIL_FROM_NAME'=> $request->get('EMAIL_FROM_NAME'),
            'EMAIL_FROM_ADDRESS' => $request->get('EMAIL_FROM_ADDRESS'),
            'EMAIL_CC_SENDER' => $request->get('EMAIL_CC_SENDER'),
            'EMAIL_EXPIRING_LICENSE_DAYS' => $request->get('EMAIL_EXPIRING_LICENSE_DAYS'),
            'EMAIL_EXPIRING_UPDATES_DAYS' => $request->get('EMAIL_EXPIRING_UPDATES_DAYS'),
            'EMAIL_EXPIRING_SUPPORT_DAYS' => $request->get('EMAIL_EXPIRING_SUPPORT_DAYS') 
            ]);
            $ema->save();
            return \successResponse(Lang::get('lang.settings_created'),$ema,201);
        }
        else{
             $emaset->EMAIL_FROM_NAME= $request->get('EMAIL_FROM_NAME');
             $emaset->EMAIL_FROM_ADDRESS = $request->get('EMAIL_FROM_ADDRESS');
             $emaset->EMAIL_CC_SENDER = $request->get('EMAIL_CC_SENDER');
             $emaset->EMAIL_EXPIRING_LICENSE_DAYS = $request->get('EMAIL_EXPIRING_LICENSE_DAYS');
             $emaset->EMAIL_EXPIRING_UPDATES_DAYS = $request->get('EMAIL_EXPIRING_UPDATES_DAYS');
             $emaset->EMAIL_EXPIRING_SUPPORT_DAYS = $request->get('EMAIL_EXPIRING_SUPPORT_DAYS');
          
             $emaset->save();
             return \successResponse(lang::get('lang.settings_updated'),$emaset,200);
        }
    }

       public function cleanUpSettings(CleanUpSettingRequest $request, $SETTING_ID){

        $cleanup = AflSettings::find($SETTING_ID);
        if(empty($cleanup)){
            $clean = new AflSettings([
            'DATABASE_CLEANUP_ENABLED'=> $request->get('DATABASE_CLEANUP_ENABLED'),
            'DATABASE_CLEANUP_CALLBACKS' => $request->get('DATABASE_CLEANUP_CALLBACKS'),
            'DATABASE_CLEANUP_REPORTS_MAIN' => $request->get('DATABASE_CLEANUP_REPORTS_MAIN'),
            'DATABASE_CLEANUP_REPORTS_SYSTEM' => $request->get('DATABASE_CLEANUP_REPORTS_SYSTEM'),
            'DATABASE_CLEANUP_REPORTS_LICENSES' => $request->get('DATABASE_CLEANUP_REPORTS_LICENSES')
            ]);
            $clean->save();
            return \successResponse(Lang::get('lang.settings_created'),$clean,201);
        }
        else{
             $cleanup->DATABASE_CLEANUP_ENABLED= $request->get('DATABASE_CLEANUP_ENABLED');
             $cleanup->DATABASE_CLEANUP_CALLBACKS = $request->get('DATABASE_CLEANUP_CALLBACKS');
             $cleanup->DATABASE_CLEANUP_REPORTS_MAIN = $request->get('DATABASE_CLEANUP_REPORTS_MAIN');
             $cleanup->DATABASE_CLEANUP_REPORTS_SYSTEM = $request->get('DATABASE_CLEANUP_REPORTS_SYSTEM');
             $cleanup->DATABASE_CLEANUP_REPORTS_LICENSES = $request->get('DATABASE_CLEANUP_REPORTS_LICENSES');
          
             $cleanup->save();
             return \successResponse(lang::get('lang.settings_updated'),$cleanup,200);
        }
    }

}
