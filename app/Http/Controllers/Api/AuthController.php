<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\AflAdmins;
use App\Models\AflClients;
use App\Models\AflSettings;
use App\Rules\CaptchaValidation;
use App\Models\User;
use App\Models\UserBackupCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Google2FA;
use Crypt;
use function Laravel\Prompts\table;


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
            'g-recaptcha-response' => empty(env('RECAPTCHA_SITE_KEY'))?
                new CaptchaValidation:['required',new CaptchaValidation],
        ]);
        $ipAddress = $request->ip();
        $failed_limit = AflSettings::value('FAILED_LOGINS_LIMIT');
        $failed_check = AflSettings::value('FAILED_LOGINS');
        $admin = $this->findAdminUser($filled['client_email']);
        if ($this->isThrottled('login:'.$ipAddress)) {
            return errorResponse(Lang::get('auth.throttle_login'), 400);
        }
        if (! $admin || ! Hash::check($filled['client_password'], $admin->client_password)) {
           return $this->handleFailedLoginAttempt($ipAddress, $failed_limit, $failed_check);
        }
        Cache::forget('login_attempts:' .$ipAddress);
        // Check if 2FA is enabled for the user
        if ($admin->is_2fa_enabled) {
            return $this->handle2FactorLogin($admin);
        }
        return $this->loginWithResponse($admin);
    }
private function findAdminUser($email)
{
    return AflClients::where(function($query) use ($email) {
        $query->whereRaw('BINARY client_email = ?', [$email])
            ->orWhereRaw('BINARY client_username = ?', [$email]);
    })
    ->where('client_role', 'admin')
    ->where('client_status', 1)->with('timezone')
    ->first();
}
    private function handleFailedLoginAttempt($ipAddress, $failedLimit, $failedCheck)
    {
        $failedAttempts = Cache::increment('login_attempts:' . $ipAddress);

        Log::info('IP ' . $ipAddress . ' has ' . $failedAttempts . ' failed login attempts.');
        if ($failedAttempts > $failedLimit && $failedCheck == 1) {
            Cache::put('login:'.$ipAddress, true, now()->addMinutes(15));
            Cache::forget('login_attempts:' .$ipAddress);
            return errorResponse(Lang::get('auth.throttle'), 400);
        }
        return errorResponse(Lang::get('auth.failed'), 400);
    }

    /**
     * To Send a reset link as email to users who have forgotten the password
     *
     * @param  Request  $request
     * @return response With a check your email and mail to the registered email address
     */

    public function forgot(Request $request)
    {
        try {
            $request->validate(
                [
                    'admin_email' => 'required|email',
                    'g-recaptcha-response' => empty(env('RECAPTCHA_SITE_KEY'))? new CaptchaValidation:['required',new CaptchaValidation],
                ]);
            $email = $request->input('admin_email');
            $user = AflClients::where('client_email', $email)->where('client_role','admin')->first();
            $ipAddress = $request->ip();
            $failed_limit = AflSettings::value('FAILED_FORGET_LIMIT');
            $failed_check = AflSettings::value('FAILED_HOSTS_FORGET');
            if($user){
                if(!$this->maxAttemptsExecuted($ipAddress,$failed_limit,$failed_check)){
                    return errorResponse(Lang::get('lang.too_many_attempts'), 400);
                }
                $token = str_random(60);
                $dataToStore =  [
                    'token' => $token,
                    'created_at' => date('Y-m-d H:i:s'),
                    'expires_at' => now()->addHour(),
                ];
                DB::table('password_resets')->updateOrInsert(['email' => $email], $dataToStore);
                $dataForEmail = [
                    'name' => $user->client_fname . ' ' . $user->client_lname,
                    'username' => $user->client_username,
                    'token' => $token,
                ];
                $title = Lang::get('passwords.password_reset');
                $template ='emails.myTestMail';
                postEmailSendConfig($email,$title,$template,$dataForEmail);
            }
            return successResponse(Lang::get('passwords.sent'), 200);
        }
        catch (\Exception $e){
             return errorResponse($e->getMessage(), 400);
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
        $request->validate([
            'password' => 'required|confirmed|regex:/^(?=\S*[a-z])(?=\S*[A-Z])(?=\S*\d)(?=\S*[^\w\s])\S{8,}/',
            'token' => 'required',
            'g-recaptcha-response' => empty(env('RECAPTCHA_SITE_KEY'))? new CaptchaValidation:['required',new CaptchaValidation],
        ]);

        $token = $request->input('token');
        $password = $request->password;
        $tokenData = DB::table('password_resets')->where([
            ['token', '=', $token], ['expires_at', '>', now()]
        ])->first();


        if (! $tokenData) {
            return errorResponse(Lang::get('passwords.token'), 401);
        }

        $admin = AflClients::where('client_email', $tokenData->email)
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
    public function getRecaptchaStatus(Request $request)
    {
        $data = [
            'recaptcha_status' => env('RECAPTCHA_SITE_KEY') ? 1 : 0,
            'site_key' => env('RECAPTCHA_SITE_KEY') ?? '',
        ];
        return response()->json($data);
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'totp' => 'required|string',
            'g-recaptcha-response' => empty(env('RECAPTCHA_SITE_KEY'))? new CaptchaValidation:['required',new CaptchaValidation],
        ]);
        $ppAuth = $request->input('PPAuth');
        $key = array_keys($ppAuth)[0];
        if (! Cache::has($key)) {
            return errorResponse('Login time expired login again',400);
        }
        $user = AflClients::with('timezone')->find(Crypt::decrypt(cache($key)));
        $secret = $user->google2fa_secret;
        if (! Google2FA::verifyKey($secret, $request->input('totp'))) {
            return errorResponse(Lang::get('lang.invalid_passcode'),400);
        }
        return $this->loginWithResponse($user);
    }
    public function verifyRecoveryCode(Request $request)
    {
        try {
            $request->validate([
                'recovery_code' => 'required|string',
                'g-recaptcha-response' => empty(env('RECAPTCHA_SITE_KEY'))? new CaptchaValidation:['required',new CaptchaValidation],
            ]);
            $ppAuth = $request->input('PPAuth');
            $key = array_keys($ppAuth)[0];
            if (! Cache::has($key)) {
                return errorResponse('Login time expired login again',400);
            }
            $rec_code = $request->input('recovery_code');
            $user = AflClients::with('timezone')->find(Crypt::decrypt(cache($key)));
            $codes = UserBackupCode::where('client_id', $user->client_id)->pluck('backup_codes')->toArray();

            if (in_array($rec_code, $codes)) {
                UserBackupCode::where('client_id', $user->client_id)->where('backup_codes', $rec_code)->delete();
                return $this->loginWithResponse($user);
            } else {
                return errorResponse(Lang::get('lang.invalid_recovery_code'),400);
            }
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
    private function loginWithResponse($user)
    {
        $token = $user->createToken('AFL')->accessToken;
        $response = [
            'message' => 'logged in',
            'user' => $user,
            'token' => $token,
        ];
        return successResponse(Lang::get('lang.Login'), $response, 200);
    }
    private function isThrottled($status)
    {
        //check if the given value is in the catch or not
        return Cache::has($status) || Cache::get($status);
    }
    public function handle2FactorLogin($user)
    {
        $key = Str::random(64);
        $value = Crypt::encrypt($key);
        //setting random key and encrypted User id in Cache for 5 minutes(just in case users kept their phone in another room)
        $userId = $user->client_id;
        cache([$key => Crypt::encrypt($userId)], 300);

        return successResponse('', ['redirect_url' => 'verify-2fa', 'PPAuth' => [$key => $value]]);
    }
    private function maxAttemptsExecuted($ipAddress, $failed_limit, $failed_check): bool
    {
        if($this->isThrottled('forgot:' .  $ipAddress)){
            return false;
        }
        $failedAttempts = Cache::increment('forgot_attempts:' . $ipAddress);
        Log::info('IP ' . $ipAddress . ' has ' . $failedAttempts . ' failed forgot password attempts.');

        if ($failedAttempts > $failed_limit && $failed_check ==1 ) {
            Cache::put('forgot:' .  $ipAddress, true, now()->addMinutes(15));
            Cache::forget('forgot_attempts:' .$ipAddress);
            return false;
        }
        return true;

    }
}
