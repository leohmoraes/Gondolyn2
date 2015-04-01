<?php namespace App\Http\Middleware;

use Config;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as LaravelsVerifyCsrfToken;

class VerifyCsrfToken extends LaravelsVerifyCsrfToken {

    public function handle($request, Closure $next)
    {
        if ($this->isReading($request) || $this->excludedRoutes($request) || $this->tokensMatch($request))
        {
            return $this->addCookieToResponse($request, $next($request));
        }

        throw new TokenMismatchException;
    }

    protected function excludedRoutes($request)
    {
        $routes = Config::get('gondolyn.openRoutes');

        foreach($routes as $route)
        {
            if ($request->is($route))
            {
                return true;
            }

            return false;
        }
    }

};