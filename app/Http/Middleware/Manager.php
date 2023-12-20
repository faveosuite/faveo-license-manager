<?php

namespace App\Http\Middleware;

use App\Models\OauthAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;

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
       try{
           $tokenRecieved = $request->bearerToken();
        if (! empty($tokenRecieved)) {
            $jwtConfig =Configuration::forSymmetricSigner(
                    new Signer\Blake2b(),
                    InMemory::base64Encoded(base64_encode(str_random(60)))
                );
            $tokenId = $jwtConfig->parser()->parse($tokenRecieved)->claims()->get('jti'); //retrieves the id of the token from license manager
            //$tokenId = (new Parser(new JoseEncoder()))->parse($tokenRecieved)->claims()->all()['jti'];//(new Parser(new JoseEncoder()))->parse($tokenRecieved)->claims()->all()['jti'];
            $tokens = new OauthAccessToken();
            $token = json_decode($tokens->where('id', $tokenId)->first()); //gets that particluar token details
            if ((! empty($token->revoked) && $token->revoked != '1') || $token->expires_at > date('Y-m-d H:i:s')) {
                return $next($request);
            } else {
                return errorResponse(Lang::get('lang.invalid_token'), 401);
            }
         }

    }
    catch (\Exception $e) {
        return errorResponse(Lang::get('lang.invalid_token'),401);
    }


   }

}
