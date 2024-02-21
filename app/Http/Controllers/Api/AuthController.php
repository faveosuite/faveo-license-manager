<?php

namespace App\Http\Controllers\Api;

//use App\Http\Requests\SessionRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\AflAdmins;
use App\Models\AflClients;
use App\Models\AflSettings;
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
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\PhpMailController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\AflSettings;
use App\Http\Requests\UserValidationRequest;



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
    //public function register(RegisterRequest $request)
    //{
       // $date = Carbon::now();
        //$hash = generateRandomString(64);
       // $admin = AflAdmins::create([
            //'admin_fname' => $request->get('admin_fname'),
            //'admin_lname' => $request->get('admin_lname'),
            //'admin_email' => $request->get('admin_email'),
            //'admin_password' => bcrypt($request->get('admin_password')),
            //'admin_ip' => $request->ip(),
            //'admin_date' => $date->toDateString(),
            //'admin_hash' => $hash,
       // ]);

        //previous comment//$token = $admin->createToken('AFL')->accessToken;

       // $response = [
           // 'user' => $admin,
       // ];

       // return successResponse(Lang::get('lang.registered'), $response, 201);
   // }

    /**
     * To Login an user to Auto faveo licenser
     *
     * @param  Request  $request
     * @return response you have LoggedIn successfuly along with a unique access token
     */
    public function login(Request $request)
    {
        $filled = $request->validate([
            'client_email' => 'required|string',
            'client_password' => 'required|string',
        ]);
        $ipAddress = $request->ip();
        $failed_limit = AflSettings::value('FAILED_LOGINS_LIMIT');
        $failed_check = AflSettings::value('FAILED_LOGINS');
        $admin = $this->findAdminUser($filled['client_email']);

        if (! $admin || ! Hash::check($filled['client_password'], $admin->client_password)) {
           return $this->handleFailedLoginAttempt($ipAddress, $failed_limit, $failed_check);
              }
      $token = $admin->createToken('AFL')->accessToken;
        $response = [
            'message' => 'logged in',
            'user' => $admin,
            'token' => $token,
        ];
        return successResponse(Lang::get('lang.Login'), $response, 200);

}
private function findAdminUser($email)
{
    return AflClients::where(function($query) use ($email) {
        $query->whereRaw('BINARY client_email = ?', [$email])
            ->orWhereRaw('BINARY client_username = ?', [$email]);
    })
    ->where('client_role', 'admin')
    ->where('client_status', 1)
    ->first();
}
private function handleFailedLoginAttempt($ipAddress, $failed_limit, $failed_check)
{
    $failedAttempts = Cache::increment('login_attempts:' . $ipAddress);

    Log::info('IP ' . $ipAddress . ' has ' . $failedAttempts . ' failed login attempts.');
    if ($failedAttempts >= $failed_limit && $failed_check ==1 ) {
        Cache::put($ipAddress . $ipAddress, true, now()->addMinutes(30));
        return errorResponse(Lang::get('auth.throttle'), 403);
    }
    return errorResponse(Lang::get('auth.failed'), 401);
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
  $admin = AflClients::where('client_email', $email)
    ->where('client_role', 'admin')
    ->first();
    $ipAddress = $request->ip();
    $failed_limit = AflSettings::value('FAILED_FORGET_LIMIT');
    if (!$admin) {
    $failedAttempts = Cache::increment('forgot_attempts:' . $ipAddress);
    $failed_check = AflSettings::value('FAILED_HOSTS_FORGET');
    Log::info('IP ' . $ipAddress . ' has ' . $failedAttempts . ' failed forgot password attempts.');

    if ($failedAttempts >= $failed_limit && $failed_check ==1 ) {
        Cache::put($ipAddress .  $ipAddress, true, now()->addDays(1)); 
        return errorResponse(Lang::get('lang.too_many_attempts'), 403);
    }
    return errorResponse(Lang::get('lang.recieve_forgot').$email. Lang::get('lang.junk'), 400);

}

$token = Str::random(10);

try {
    DB::table('password_resets')->insert([

        'email' => $email,
        'token' => $token,
    ]);
    $token = [
        'token' => $token,
    ];
    $title = Lang::get('passwords.password_reset');
    $template ='emails.myTestMail';
    postEmailSendConfig($email,$title,$template,$token);
    return successResponse(Lang::get('passwords.sent'), $token, 200);
}  catch (\Exception $e) {
    return  errorResponse($e->getMessage(), 400);
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

        $admin = AflClients::where('client_email', $request->email)
        ->where('client_role','admin')
        ->first();
        if (! $admin) {
            return errorResponse(Lang::get('passwords.user'), 401);
        }
        $admin->client_password = \Hash::make($password);
        $admin->update();

        $details = DB::table('password_resets')->where('email', $admin->client_email)->delete();

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
                       'expires_at' => Carbon::now(),
                   ]);
        return successResponse(Lang::get('lang.Logout'), $logout, 201);
    }
}
