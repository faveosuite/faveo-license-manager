<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ImageUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\CountryCode;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use App\Models\AflClients;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function getProfileInfo()
    {
        try {
            $userInfo = AflClients::where('client_id',getAuthUserID())
                ->with('timezone')
                ->first();
            if ($userInfo) {
                $userInfo = $userInfo->toArray(); // Convert the object to an array

                // Iterate over the array and replace null with an empty string
                array_walk_recursive($userInfo, function (&$item) {
                    $item = $item === null ? '' : $item;
                });
            }

            return successResponse('', $userInfo);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
    public function updateProfile(ProfileRequest $request)
    {
        try {
            $user = getAuthUser();
            $user->fill($request->except('client_profile_pic','client_mobile_code','client_iso2','client_mobile'));
            if ($request->hasFile('client_profile_pic')) {
                $fileName = ImageUpload::saveImageToStorage($request->file('client_profile_pic'), 'common/images/users');
                $user->client_profile_pic = $fileName;
            }
            if ($request->has('client_mobile') && !empty($request->client_mobile)) {
                $user->client_mobile = $request->client_mobile;
                $user->client_mobile_code = $request->client_mobile_code;
                $user->client_iso2 = $request->client_iso2;
            } else {
                $user->client_mobile = null;
                $user->client_mobile_code = 91;
                $user->client_iso2 = 'IN';
            }
            $user->save();
            return successResponse(Lang::get('lang.profile_updated_successfully'));
        } catch (\Exception $e) {
            return errorResponse( $e->getMessage());
        }
    }
    public function updatePassword(ProfileRequest $request)
    {
        try {
            $user = getAuthUser();
            $currentPassword = $user->client_password;
            $oldPassword = $request->input('old_password');
            $newPassword = $request->input('new_password');
            if (Hash::check($oldPassword, $currentPassword)) {
                $user->client_password = Hash::make($newPassword);
                $user->save();
                return successResponse(Lang::get('passwords.password_updated_successfully'));
            } else {
                return errorResponse(Lang::get('passwords.incorrect_password'));
            }
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
    public function getCountryCode()
    {
        $countryCode = CountryCode::select('id','name','iso2 as iso','phone_code','example')->orderBy('name','asc')->get();
        return successResponse('', $countryCode);
    }
}
