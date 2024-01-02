<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AflSettings;
use App\Models\AflBannedHosts;
use App\Models\AflWhitelistIps;

class WhitelistMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $settings = AflSettings::first();
        $whitelistedAccess = $settings->WHITELISTED_ACCESS;
        if ($whitelistedAccess == 1) {
            $whitelistedIPsString = AflWhitelistIps::pluck('whitelist_host_ip')->toArray();
            $clientIP = $request->ip();
            if (!in_array($clientIP, $whitelistedIPsString)) {
                return errorResponse('Access denied', 403);
            }
        }
        $bannedHosts = $settings->BANNED_HOSTS;

        if ($bannedHosts == 1) {
            $bannedIPs = AflBannedHosts::pluck('banned_host_ip')->toArray();
            $clientIP = $request->ip();
            if (in_array($clientIP, $bannedIPs)) {
                return errorResponse('Access denied', 403);
            }
        }
        return $next($request);
    }
}
