<?php

namespace App\Http\Controllers;
use App\Models\AflAdmins;
use App\Http\Requests\EditProfilesRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;


/**
 * Consist of functionalities for the Edit profiles page in Auto Faveo licenser 
 * Class  EditProfilesController
 * @package App\Http\Controllers
 */
class EditProfilesController extends Controller
{

    /**
     * To Edit the details of the logged in admin
     * @param EditProfilesRequest $request
     * @param $admin_id
     * returns the number of records updated also generates a new admin_hash
     */
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

       if(!aflValidateIntegerValue($user)){
           return errorResponse(Lang::get('lang.'),404);
       }
       else{
           $admin_hash = generateRandomString(64);
           $hash = AflAdmins::where('admin_id', $admin_id)->update(['admin_hash'=> $admin_hash]);
           return successResponse(Lang::get('lang.edit'),$user,200);
       }

    }
}
