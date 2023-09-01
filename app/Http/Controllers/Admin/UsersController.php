<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrCreateUser;
use App\Models\AflAdmins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class UsersController extends Controller
{
    public function getUsers(Request $request)
    {
        return successResponse(Lang::get('lang.fetch_user'),AflAdmins::select('admin_id', 'admin_fname', 'admin_lname', 'admin_email', 'admin_ip', 'admin_date', 'admin_status', 'created_at', 'updated_at')
            ->cursor($request->limit ?? 10));
    }


    public function editUser($id)
    {
        $user = AflAdmins::where('admin_id',$id)->first();
        return successResponse(Lang::get('lang.fetch_user'), $user, 200);
    }


    public function updateUser(UpdateOrCreateUser $request, $id = null)
    {
        $defaultValues = [
            'admin_type_id' => 1,
            'admin_hash' => Str::random(37),
            'admin_date' => $request->input('admin_date') ?? now(),
        ];
        try{
            $user = $id ? AflAdmins::where('admin_id',$id)->first() : new AflAdmins();
            $user->fill(array_merge($defaultValues, $request->toArray()));
            $user->save();
            return successResponse(Lang::get('lang.user_updated_successfully'), $user, 200);
        }catch(\Exception $exception){
            Log::error('Exception occurred: ' . $exception->getMessage());
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
            return successResponse(Lang::get('lang.user_deleted'), null, 200);
        }catch( \Exception $exception) {
            Log::error('Exception occurred: ' . $exception->getMessage());
            return errorResponse($exception->getMessage());
        }
    }
}







