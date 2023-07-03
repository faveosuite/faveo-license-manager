<?php

namespace App\Http\Controllers\Api;

//use App\Http\Requests\SessionRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\AflAdmins;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

/**
 * Consist of functionalities for Authentication in Auto Faveo licenser
 * Class AuthController
 */
class AuthController extends Controller
{
    /**
     * To Register an user to Auto faveo licenser
     *
     * @param  RegisterRequest  $request
     * @return response you have registered successfuly along with a unique access token
     */
    public $sharedVariable; 
    public function register(RegisterRequest $request)
    {
        $date = Carbon::now();
        $hash = generateRandomString(64);
        $admin = AflAdmins::create([
            'admin_fname' => $request->get('admin_fname'),
            'admin_lname' => $request->get('admin_lname'),
            'admin_email' => $request->get('admin_email'),
            'admin_password' => bcrypt($request->get('admin_password')),
            'admin_ip' => $request->ip(),
            'admin_date' => $date->toDateString(),
            'admin_hash' => $hash,
        ]);

        //$token = $admin->createToken('AFL')->accessToken;

        $response = [
            'user' => $admin,
        ];

        return successResponse(Lang::get('lang.registered'), $response, 201);
    }

    /**
     * To Login an user to Auto faveo licenser
     *
     * @param  Request  $request
     * @return response you have LoggedIn successfuly along with a unique access token
     */
    public function login(Request $request)
    {
        $filled = $request->validate([
            'admin_email' => 'required|string',
            'admin_password' => 'required|string|min:8',

        ]);

        $admin = AflAdmins::where('admin_email', $filled['admin_email'])->first();

        if (! $admin || ! Hash::check($filled['admin_password'], $admin->admin_password)) {
            return errorResponse(Lang::get('auth.failed'), 401);
        }

        $tokenobj = $admin->createToken('AFL');
        $token = $tokenobj->accessToken;
        // $baseUrl = Config::get('app.url'); 
        // $config = config('clockwork');

        // // Set the Authorization header value
        // $config['headers']['Authorization'] = $token;

        // // Update the configuration array
        // Config::set('clockwork', $config);
        // dd($token );
        // Session::put('sharedVariable', $token);
        // Cache::put('sharedVariable', 'Value from function 1', 60); 
        // Cache::put('sharedVariable', $token, null); // Store the value in the cache without an expiration time

        

        $response = [
            'message' => 'logged in',
            'user' => $admin,
            'token' => $token,
        ];

        return successResponse(Lang::get('lang.Login'), $response, 200);
    }

    /**
     * To Send a reset link as email to users who have forgotten the password
     *
     * @param  Request  $request
     * @return response With a check your email and mail to the registered email address
     */
    public function forgot(Request $request)
    {
        $email = $request->input('admin_email');

        if (AflAdmins::where('admin_email', $email)->doesntExist()) {
            return errorResponse(Lang::get('auth.failed'), 400);
        }
        $tokens = Str::random(10);
        try {
            DB::table('password_resets')->insert([

                'email' => $email,
                'token' => $tokens,
            ]);
            $token = [
                'token' => $tokens,
            ];
            // $from = Config::get('constants.Mail.From');
            Mail::send('emails.myTestMail', $token, function ($message) use ($email) {
                $message->from(config('constants.Mail.From'), 'Forgot Password');
                $message->to($email)->subject('Password Reset Link');
            }

            );

            return successResponse(Lang::get('passwords.sent'), $token, 200);
        } catch (Exception $exception) {
            return  errorResponse($exception->getMessage(), 400);
        }
    }

    /**
     * Used to reset the password after validating email,password and token
     *
     * @param  Request  $request
     * @return response password has been changed
     */
    public function reset(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return errorResponse(Lang::get('lang.form'), 401);
        }

        $password = $request->password;
        $tokenData = DB::table('password_resets')->where('token', $request->token)->first();

        if (! $tokenData) {
            return errorResponse(Lang::get('passwords.token'), 401);
        }

        $admin = AflAdmins::where('admin_email', $tokenData->email)->first();

        if (! $admin) {
            return errorResponse(Lang::get('passwords.user'), 401);
        }

        $admin->admin_password = \Hash::make($password);
        $admin->update(); //or $admin->save();

        // Auth::login($admin);
        $details = DB::table('password_resets')->where('email', $admin->admin_email)->delete();

        return successResponse(Lang::get('passwords.reset'), $details, 201);
    }

    /**
     * To Logout of the Auto Faveo Licenser
     *
     * @param  Request  $request
     * @param $user_id
     * @return response You have logged out successfuly after revoking the token
     */
    public function logout(Request $request, $user_id)
    {
        $logout = DB::table('oauth_access_tokens')
                   ->where('user_id', $user_id)
                   ->update([
                       'revoked' => true,
                   ]);
                //    Cache::forget('sharedVariable'); 
                 

        return successResponse(Lang::get('lang.Logout'), $logout, 201);
    }
    public function clockwork()
    {
        // dd('fghj');
        // $value = Session::get('sharedVariable');
        if (Cache::has('sharedVariable')) {
            // Cache still exists
            dd('Cache exists');
        } else {
            // Cache is cleared
            dd('Cache cleared');
        }
}

}
