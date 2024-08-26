<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordResetToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $url = $request->path();
        if (strpos($url, 'reset') !== false) {
            $token = $request->segment(2);
            $check = \DB::table('password_resets')
                ->where('token', '=', $token)
                ->where('expires_at', '>', now())
                ->first();
            if (! $check) {
                return redirect('/login?error='.\Lang::get('lang.reset-token-expired-or-not-found'));
            }
        }

        return $next($request);
    }
}
