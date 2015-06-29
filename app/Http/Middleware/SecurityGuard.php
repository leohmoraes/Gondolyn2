<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class SecurityGuard
{
    /**
    * Handle an incoming request.
    *
    * @param \Illuminate\Http\Request $request
    * @param \Closure $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        $content = $next($request);

        // get the config for the CORS
        return $content
            ->header('Access-Control-Allow-Origin', Config::get('gondolyn.cors.access-control-allow-origin'))
            ->header('Access-Control-Allow-Methods', Config::get('gondolyn.cors.access-control-allow-methods'))
            ->header('Access-Control-Allow-Headers', Config::get('gondolyn.cors.access-control-allow-headers'))
            ->header('X-XSS-Protection', Config::get('gondolyn.security.xss-protection'))
            ->header('X-Frame-Options', Config::get('gondolyn.security.x-frame-option'))
            ->header('Content-Security-Policy', Config::get('gondolyn.security.content-security-policy'))
            ->header('X-Content-Security-Policy', Config::get('gondolyn.security.content-security-policy'))
            ->header('X-Content-Type-Options', Config::get('gondolyn.security.x-content-type-options'));
    }
}