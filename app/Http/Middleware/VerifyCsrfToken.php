<?php namespace App\Http\Middleware;

use Config;
use Closure;
use Lang;
use Module;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as LaravelsVerifyCsrfToken;

class VerifyCsrfToken extends LaravelsVerifyCsrfToken
{
    public function handle($request, Closure $next)
    {
        if ($this->isReading($request) || $this->tokensMatch($request)) {
            return $this->addCookieToResponse($request, $next($request));
        } else {
            if ($this->isInExcludedRoutes($request)) {
                return $next($request);
            }
        }

        if (stristr($request->url(), 'api')) {
            throw new \Exception(Lang::get('notification.api.incorrect'), 1);
        }

        throw new \Illuminate\Session\TokenMismatchException;
    }

    private function isInExcludedRoutes($request)
    {
        $allRoutes = array();

        $routes = Config::get('gondolyn.csrfIgnoredRoutes');

        $configs = Module::getConfigs();

        foreach ($configs as $config) {
            $allRoutes = array_merge($routes, $config['csrfIgnoredRoutes']);
        }

        foreach ($allRoutes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }

};
