<?php

namespace App\Http\Middleware;

use App\Models\OauthAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

class Manager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
   public function handle(Request $request, Closure $next)
   {
       $activateUserId = Cache::get('activateUserId');
       $randomId = Cache::get('abcd'.$activateUserId);
    //    $randomId = Cache::get('abcd'.$activateUserId);
    // Cache::rememberForever(Crypt::encryptString($admin->admin_id));
       $admin = DB::table('afl_admins')->where('admin_id', $randomId)->select('admin_id')->first();
       $user_id = $admin->admin_id;
       $tokenRecieved = ($request->bearerToken())?? Cache::get($user_id);

       if (! empty($tokenRecieved)) {
           $jwtConfig = Configuration::forUnsecuredSigner();
           $tokenId = $jwtConfig->parser()->parse($tokenRecieved)->claims()->get('jti'); //retrieves the id of the token from license manager
           //$tokenId = (new Parser(new JoseEncoder()))->parse($tokenRecieved)->claims()->all()['jti'];//(new Parser(new JoseEncoder()))->parse($tokenRecieved)->claims()->all()['jti'];
           $tokens = new OauthAccessToken();
           $token = json_decode($tokens->where('id', $tokenId)->first()); //gets that particluar token details

           if ((! empty($token->revoked) && $token->revoked != '1') || $token->expires_at >= date('Y-m-d H:i:s')) {
               return $next($request);
           } else {
               return errorResponse(Lang::get('lang.invalid_token'), 401);
           }
        }
   }
}
