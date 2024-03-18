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
        $clientIP = $request->ip();

        if ($settings->WHITELISTED_ACCESS == 1 || $settings->BANNED_HOSTS == 1) {
            $whitelistedIPs = AflWhitelistIps::pluck('whitelist_host_ip')->toArray();
            $bannedIPs = AflBannedHosts::pluck('banned_host_ip')->toArray();

            if ($settings->WHITELISTED_ACCESS == 1 && !empty($whitelistedIPs) && !in_array($clientIP, $whitelistedIPs)) {
                return errorResponse('Access denied', 400);
            }

            if ($settings->BANNED_HOSTS == 1 && !empty($bannedIPs) && in_array($clientIP, $bannedIPs)) {
                return errorResponse('Access denied', 400);
            }
        }

        return $next($request);

    }
}
