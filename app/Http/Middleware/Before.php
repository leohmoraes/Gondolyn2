<?php namespace App\Http\Middleware;

use Closure;
use Route;
use Session;
use Lang;
use Gondolyn;
use Module;
use Auth;
use Accounts;
use App\Services\AccountServices;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use App\Exceptions\PermissionException;

class Before
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
        $route = Route::getRoutes()->match($request);
        $action = $route->getAction();

        // Before Filters as Middleware
        if (isset($action['before'])) {
            $filterResult = null;

            $beforeFilter = $action['before'];

            // Ensure we're working with an array
            if ( ! is_array($beforeFilter)) {
                settype($beforeFilter, 'array');
            }

            // If its an array of filters check each
            if (is_array($beforeFilter)) {
                foreach ($beforeFilter as $filter) {
                    if (method_exists($this, $filter)) {

                        $filterResult = $this->$filter($request);
                        // Fail immediately
                        if ( ! is_null($filterResult)) {
                            return $filterResult;
                        }
                    }
                }
            }
        }

        $content = $next($request);
        return $content;
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    public function is_ajax_call()
    {
        if ( ! Gondolyn::is_ajax_call()) {
            return Gondolyn::response("error", Lang::get("notification.api.ajax_only"));
        }
    }

    public function is_api_call($request)
    {
        if ( ! Gondolyn::is_api_call($request)) {
            return Gondolyn::response("error", Lang::get("notification.api.not_api_call"));
        }
    }

    public function is_logged_in($request)
    {
        // If remembered by cookies
        if (AccountServices::isAccountRemembered()) {
            AccountServices::loginWithCookies();
        }

        if (time() - Session::get("last_activity") > Config::get("session.lifetime") * 60) {
            Session::flush();

            Auth::logout();

            Gondolyn::notification(Lang::get("notification.login.expired-session"), 'warning');

            if (Gondolyn::is_api_call($request)) {
                throw new ApiException(Lang::get("notification.login.expired-session"), 1);
            }

            return redirect("errors/general");
        } else {
            Session::put("last_activity", time());
        }
    }

    public function valid_api_key()
    {
        if ( ! Gondolyn::valid_api_key()) {
            return Gondolyn::response("error", Lang::get("notification.api.bad_key"));
        }
    }

    public function valid_api_token()
    {
        if ( ! Gondolyn::valid_api_token()) {
            return Gondolyn::response("error", Lang::get("notification.api.bad_token"));
        }
    }

}
