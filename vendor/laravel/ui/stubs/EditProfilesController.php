<?php

namespace App\Http\Controllers;
use App\Models\AflAdmins;
use App\Http\Requests\EditProfilesRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;

class EditProfilesController extends Controller
{
    public function editProfile(EditProfilesRequest $request,$admin_id){
        
        $user = AflAdmins::where('admin_id',$admin_id) 
                         ->update([
                             'admin_fname' => $request->get('admin_fname'),
                             'admin_lname' => $request->get('admin_lname'),
                             'admin_email' => $request->get('admin_email'),
                             'admin_password' => bcrypt($request->get('password')),
                             'admin_ip' =>$request->get('admin_ip'),
                             'admin_data_authenticity' => $request->get('admin_data_authenticity')
                         ]);

       if(!\aflValidateIntegerValue($user)){
           return \errorResponse(Lang::get('lang.'),404);
       }
       else{
           $admin_hash = \generateRandomString(64);
           $hash = AflAdmins::where('admin_id', $admin_id)->update(['admin_hash'=> $admin_hash]);
           return \successResponse(Lang::get('lang.edit'),$user,200);
       }

    }
}
