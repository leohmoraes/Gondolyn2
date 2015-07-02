<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class Cors
{
    /**
    * Handle an incoming request.
    *
    * Is intended for use with APIs for all other components use SecurityGuard.
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
            ->header('Access-Control-Allow-Headers', Config::get('gondolyn.cors.access-control-allow-headers'));
    }
}