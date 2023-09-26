<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditProfilesRequest;
use App\Models\AflClients;
use Illuminate\Support\Facades\Lang;

/**
 * Consist of functionalities for the Edit profiles page in Auto Faveo licenser
 * Class  EditProfilesController
 */
class EditProfilesController extends Controller
{
    /**
     * To Edit the details of the logged in admin
     *
     * @param  EditProfilesRequest  $request
     * @param $admin_id
     * returns the number of records updated also generates a new admin_hash
     */
    public function editProfile(EditProfilesRequest $request, $admin_id)
    {
        $user = AflClients::where('client_id', $admin_id)
                         ->update([
                             'client_fname' => $request->get('admin_fname'),
                             'client_lname' => $request->get('admin_lname'),
                             'client_email' => $request->get('admin_email'),
                             'client_password' => bcrypt($request->get('password')),
                         ]);

        if (! aflValidateIntegerValue($user)) {
            return errorResponse(Lang::get('lang.error'), 404);
        }
        return successResponse(Lang::get('lang.edit'), $user, 200);
    }
}
