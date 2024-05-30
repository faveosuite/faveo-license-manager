<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $redirectToRoute
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = getAuthUser();
        $cacheKey = "User " . $user->client_id . "Password confirm at";
        if (!Cache::has($cacheKey) || !Cache::get($cacheKey)) {
            return errorResponse('password_confirmation_required',400);
        }

        return $next($request);
    }
}
