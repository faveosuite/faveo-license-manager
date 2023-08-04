<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AflAdmins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function getUsers(){

        $users = AflAdmins::all();
        return successResponse(Lang::get('lang.get_record'), $users, 200);

    }

    public function addUsers(Request $request)
    {
        $request->validate([
            'admin_fname' => 'required|string|max:125',
            'admin_lname' => 'required|string|max:125',
            'admin_email' => 'required|email|unique:afl_admins,admin_email',
            'admin_status' => 'required',
        ]);

        $user = new AflAdmins();
        $user->admin_fname = $request->input('admin_fname');
        $user->admin_lname = $request->input('admin_lname');
        $user->admin_email = $request->input('admin_email');
        $user->admin_password = Str::random(rand(8, 12));
        $user->admin_date = $request->input('admin_date') ?? now();
        $user->admin_status = $request->input('admin_status');
        $user->save();

        return successResponse(Lang::get('lang.added_record'), $user, 200);
    }



    public function editUsers($id){
        $user = AflAdmins::where('admin_id',$id)->first();
        return successResponse(Lang::get('lang.get_record'), $user, 200);
    }

    public function updateUsers(Request $request, $id = null)
    {
        try{
//            $request->validate([
//                'admin_fname' => 'required|string|max:125',
//                'admin_lname' => 'required|string|max:125',
//                'admin_email' => 'required|email|unique:afl_admins,admin_email',
//                'admin_status' => 'required',
//            ]);

            $user = $id ? AflAdmins::where('admin_id',$id)->first() : new AflAdmins();

            $user->admin_fname = $request->input('admin_fname');
            $user->admin_lname = $request->input('admin_lname');
            $user->admin_email = $request->input('admin_email');
            $user->admin_date = $request->input('admin_date') ?? now();
            $user->admin_status = $request->input('admin_status');
            $user->admin_hash = Str::random(37);
            $user->admin_type_id = 1;
            $user->save();

            return successResponse('Saved Successfully');
        }catch(\Exception $exception){
            return errorResponse($exception->getMessage(), 412);
        }

        return successResponse(Lang::get('lang.updated_successfully'), $user, 200);
    }

    public function deleteUsers($id){
        $user = AflAdmins::findOrFail($id);
        $user->delete();
        return successResponse(Lang::get('lang.deleted_record'), null, 200);
    }

}

