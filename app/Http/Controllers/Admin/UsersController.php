<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users;

class UsersController extends Controller
{
    public function saveUsers(Request $request)
    { 
            $user = new Users();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->designation = $request->designation;
            $user->role = $request->role;
            $user->purpose_to_signin = $request->purpose_to_signin;
            $user->work_phone = $request->work_phone;
            $user->email = $request->email;
            $user->organization = $request->organization;
            $user->organization = $request->organization;
            $user->deleted_at = 0;
            $user->save();
        
        return response()->json(['user' => $user]);
    }
    protected function getUsers()
    {
        $users = Users::where('deleted_at',0)->get();
        return response()->json(['users' => $users]);
    }
    protected function deleteUsers(Request $request)
    {
        $user_id = $request->user_id;
        $user = Users::where('id',$user_id)->first();
        $user->deleted_at = 1;
        $user->save();

        return response()->json(['users' => $user]);
    }
    protected function editUsers($id)
    {
        $user = Users::where('id',$id)->first();
        return response()->json(['users' => $user]);
    }
    protected function UpdateUsers(Request $request, $id)
    {
   $user = Users::find($id);
   $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->designation = $request->designation;
            $user->role = $request->role;
            $user->purpose_to_signin = $request->purpose_to_signin;
            $user->work_phone = $request->work_phone;
            $user->email = $request->email;
            $user->organization = $request->organization;
            $user->organization = $request->organization;
   $user->save();

        return response()->json(['users' => $user]);
    }
}
