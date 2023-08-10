<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrCreateUser;
use App\Models\AflAdmins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function getUsers()
    {
        $users = AflAdmins::all();
        return successResponse(Lang::get('lang.get_record'), $users, 200);
    }

    public function editUser($id)
    {
        $user = AflAdmins::where('admin_id',$id)->first();
        return successResponse(Lang::get('lang.get_record'), $user, 200);
    }

    public function updateUser(UpdateOrCreateUser $request, $id = null)
    {
        try{
            $user = $id ? AflAdmins::where('admin_id',$id)->first() : new AflAdmins();

            $user->admin_fname = $request->input('admin_fname');
            $user->admin_lname = $request->input('admin_lname');
            $user->admin_email = $request->input('admin_email');
            $user->admin_date = $request->input('admin_date') ?? now();
            $user->admin_status = $request->input('admin_status');
            $user->admin_hash = Str::random(37);
            $user->admin_type_id = 1;
            $user->save();
            return successResponse(Lang::get('lang.updated_successfully'), $user, 200);
        }catch(\Exception $exception){
            return errorResponse($exception->getMessage(), 412);
        }
    }

    public function deleteUser($id)
    {
        try{
            if(!$user = AflAdmins::findOrFail($id)) {
                return errorResponse(Lang::get('lang.user_not_found'), null, 200);
            }
            $user->delete();
            return successResponse(Lang::get('lang.deleted_record'), null, 200);
        }catch( \Exception $exception) {
            return errorResponse($exception->getMessage());
        }
    }

}
