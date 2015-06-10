<?php namespace App\Http\Middleware;

use Closure;
use Route;
use Session;
use Cookie;
use Gondolyn;
use Lang;
use Auth;
use AccountServices;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

class TwoFactorAuthentication implements Middleware
{
    protected $app;
    protected $redirector;
    protected $request;

    public function __construct(Application $app, Redirector $redirector, Request $request)
    {
        $this->app = $app;
        $this->redirector = $redirector;
        $this->request = $request;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
     public function handle($request, Closure $next)
     {
        $config = Config::get('gondolyn.two-factor-authentication');
        $duration = $config['duration'];
        $twoFactorNotVerified = false;

        if ($config['enabled']) {
            $user = Auth::user();

            if ($user) {
                if ($duration === 'session') {
                    $twoFactorNotVerified = is_null(Session::get('twoFactored'));
                } else if ($duration === 'lifetime' || $duration === '60days') {
                    $twoFactorNotVerified = is_null(Cookie::get('twoFactored'));
                }

                if ($twoFactorNotVerified
                    && $user->two_factor_enabled === 'on'
                    && ! Gondolyn::is_api_call()
                    && $request->url() !== url('account/two-factor')
                    && ! stristr($request->url(), 'account/two-factor/authenticate')
                    && ! stristr($request->url(), 'account/update')
                    && ! stristr($request->url(), 'debugbar')
                    && ! stristr($request->url(), 'login')
                    && ! stristr($request->url(), 'logout')
                    ) {

                    return redirect('account/two-factor');
                }
            }
        }

        $content = $next($request);
        return $content;
    }

}