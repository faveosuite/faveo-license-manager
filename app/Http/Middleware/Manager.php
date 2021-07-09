<?php

namespace App\Http\Middleware;

use App\Models\AflAdmins;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\AuthController;

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
            $toknnn="";
            $token=$request->bearerToken();
            if(empty($token)){      
              $toknnn = "key passed from billing";
            }
            $tok = DB::table('oauth_access_tokens')->where('id',$token)->get('revoked')->toArray();
            if((!empty($tok) && $tok!='1')|| $toknnn =='key passedfrom billing'){
             return $next($request);  
            }  
        else { 
          return response(['message'=> 'Not Authorized']);//redirect('/login');
        }
}
    }


