<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
         if (!$this->isInstall()) {
            return $next($request);
        } else{
                return redirect('/');
            }
        
    
    }
        
        /**
 * check installed
 * @return boolean
 */
private function isInstall()
{
    $check = false;
    $env   = base_path('.env');
    if (\Config::get('database.install') == 1) {
        $check = true;
    }
    return $check;
}
    
}
