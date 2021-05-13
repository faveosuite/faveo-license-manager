<?php

namespace App\Http\Controllers\Api;



use App\Http\Requests\ResetRequest;
use App\Models\afl_admins;
use App\Models\oauth_access_token;

use http\Message;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;




use Lcobucci\JWT\Token\Parser;


class  AuthController extends Controller
{
    
    public function register(Request $request){

        $date = Carbon::now();
        $filled = $request->validate([
            'admin_fname'=> 'required|string',
            'admin_lname'=> 'required|string',
            'admin_email' => 'required|string|unique:afl_admins,admin_email',
            'admin_password'=> 'required|string|min:8|confirmed',
            'admin_ip'=> 'string',
            'admin_date' => 'string'

        ]);
        $admin = afl_admins::create([
            'admin_fname'=>$filled['admin_fname'],
            'admin_lname'=>$filled['admin_lname'],
            'admin_email'=>$filled['admin_email'],
            'admin_password'=>bcrypt($filled['admin_password']),
            'admin_ip'=> $request->ip(),
            'admin_date'=> $date->toDateString()


        ]);

        $token = $admin->createToken('AFL')->accessToken;

        $response = [
            'user'=> $admin,
            'token'=> $token
        ];

        return response($response,201);
    }

    public function login(Request $request)
    {

        $filled = $request->validate([
            'admin_email' => 'required|string',
            'admin_password'=> 'required|string|min:8',

        ]);
          $admin = afl_admins::where('admin_email',$filled['admin_email'])->first();

          if( !$admin || !Hash::check($filled['admin_password'],$admin->admin_password)){
              return response([
                  'message'=>'Login credentials invalid'
              ],401);
          }

        $token = $admin->createToken('AFL')->accessToken;

        $response = [
            'message'=> 'logged in',
            'user'=> $admin,
            'token'=> $token
        ];

        return response($response,201);

    }

   public function forgot(Request $request){

        $email = $request->input('admin_email');

        if(afl_admins::where('admin_email',$email)->doesntExist()){
            return response([
                'message'=> 'User doesn\'t exist'
            ],404);
        }
        $tokens = Str::random(10);
        try {
            DB::table('password_resets')->insert([

                'email' => $email,
                'token' => $tokens
            ]);
            $token = array(
                'token'=>$tokens
            );

            Mail::send('emails.myTestMail', $token, function ($message) use ($email){

              $message->from('admin@example.com', 'Forgot Password');
              $message->to($email)->subject('Password Reset Link');
                              
            }

             );

            return \response(['message'=>'check your email!']);
        }
        catch(\Exception $exception){
            return \response([
                'message' => $exception->getMessage()
            ],400);
        }

    }

    public function reset(Request $request)
    {

       /* $token = $request->input('token');
        if(!$PasswordResets = DB::table('password_resets')->where('token',$token)->first()){

            return \response(['message'=>'invalid token'],400);
        }

    

        if(!$admin = afl_admins::where('admin_email', $PasswordResets->email)->first()){
            return \response(['message'=> 'user doesn\'t exist'],404);
        }
    
        $admin->admin_password  = Hash::make($request->input('admin_password'));
        $admin->save();
        return \response(['message'=> 'Passsword has been changed']);*/
        
    //Validate input
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:afl_admins,admin_email',
        'password' => 'required|confirmed',
        'token' => 'required'
        ]);
          
    //check if payload is valid before moving on
    if ($validator->fails()) {
        return response([
            'message'=> 'Please complete the form'
            ]);
    }

    $password = $request->password;
// Validate the token
    $tokenData = DB::table('password_resets')->where('token', $request->token)->first();

    if (!$tokenData) {
    return \response(['message' => 'Your token data is invalid']);
    }
   
    $admin = afl_admins::where('admin_email', $tokenData->email)->first();

    if (!$admin){
        return response(['message'=>'email is invalid']);
    }
    //Hash and update the new password
    $admin->admin_password = \Hash::make($password);
    $admin->update(); //or $admin->save();
    

    //login the user immediately they change password successfully
   // Auth::login($admin);

    //Delete the token
    DB::table('password_resets')->where('email', $admin->admin_email)->delete();

    return \response(['message'=> 'Password reset complete']);

    //Send Email Reset Success Email
   /* if ($this->sendSuccessEmail($tokenData->email)) {
        return \response(['message'=> 'Success']);
    } else {
        return redirect()->back()->withErrors(['email' => trans('A Network Error occurred. Please try again.')]);
    }*/
    }

    public function logout(Request $request){
         
         // if (Auth::check()) {
            //Auth::user()->AauthAcessToken()->delete();      
         //}  
        DB::table('oauth_access_tokens')
        ->where('user_id', Auth::user())
        ->update([
            'revoked' => true
            
        ]);
         return \response(['message'=> 'You are logged out'],200);

        

    }


}
