<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as LaravelsVerifyCsrfToken;

class VerifyCsrfToken extends LaravelsVerifyCsrfToken {

    private $openRoutes = [
        'api/login'
    ];

    public function handle($request, Closure $next)
    {
        if (env('APP_ENV') === 'testing')
        {
            return $next($request);
        }

        foreach($this->openRoutes as $route)
        {
            if ($request->is($route))
            {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }

};