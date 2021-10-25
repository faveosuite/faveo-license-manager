<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AflEmails;
use App\Http\Requests\EmailsRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;




/**
 * Consist of functionalities for the Custom Emails page in Auto Faveo licenser
 * Class EmailsController
 * @package App\Http\Controllers\Admin
 */
class EmailsController extends Controller
{
    /**
     * To Add or Update custom Email fields in license manager
     * @param EmailsRequest $request
     * @param $email_id
     * @return success response wheather the fields has been added or updated
     */
    public function emails(EmailsRequest $request, $email_id){

         $user = AflEmails::find($email_id);
         if(empty($user)){
         $email= new AflEmails(array(
            'email_expiring_license_subject'=> $request->get('email_expiring_license_subject'),
            'email_expiring_license_text'=> $request->get('email_expiring_license_text'),
            'email_expiring_updates_subject'=>$request->get('email_expiring_updates_subject'),
            'email_expiring_updates_text'=>$request->get('email_expiring_updates_text'),
            'email_expiring_support_subject'=>$request->get( 'email_expiring_support_subject'),
            'email_expiring_support_text'=> $request->get('email_expiring_support_text')
         )
         );

         return successResponse(Lang::get('lang.emails'),$email,201);
    }
    else{
              $user->email_expiring_license_subject =  $request->get('email_expiring_license_subject');
              $user->email_expiring_license_text = $request->get('email_expiring_license_text');
              $user->email_expiring_updates_subject=$request->get('email_expiring_updates_subject');
              $user->email_expiring_updates_text=$request->get('email_expiring_updates_text');
              $user->email_expiring_support_subject=$request->get( 'email_expiring_support_subject');
              $user->email_expiring_support_text =  $request->get('email_expiring_support_text');
              $user->save();
              return successResponse(Lang::get('lang.emails'),$user,200);

    }
    }
    public function show(){
        $emails = AflEmails::all();
        return successResponse(Lang::get('lang.Emails_Show'),$emails,200);
    }
}
