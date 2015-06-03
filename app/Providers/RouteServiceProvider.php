<?php namespace App\Providers;

use Session;
use Config;
use Lang;
use Auth;
use Accounts;
use Request;
use App\Services\AccountServices;
use Gondolyn;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = null;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);

        Route::filter('is_ajax_call', function() {
            if ( ! Gondolyn::is_ajax_call()) {
                return Gondolyn::response("error", Lang::get("notification.api.ajax_only"));
            }
        });

        Route::filter('is_api_call', function() {
            if ( ! Gondolyn::is_api_call()) {
                return Gondolyn::response("error", Lang::get("notification.api.not_api_call"));
            }
        });

        Route::filter('valid_api_key', function() {
            if ( ! Gondolyn::valid_api_key()) {
                return Gondolyn::response("error", Lang::get("notification.api.bad_key"));
            }
        });

        Route::filter('valid_api_token', function() {
            if ( ! Gondolyn::valid_api_token()) {
                return Gondolyn::response("error", Lang::get("notification.api.bad_token"));
            }
        });

        Route::filter('is_logged_in', function() {
            if (AccountServices::isAccountRemembered()) {
                $email      = Request::cookie("email");
                $password   = Request::cookie("password");

                $Users      = new Accounts;
                $user       = $Users->loginWithEmail($email, $password, false);

                AccountServices::login($user);
            }

            if (time() - Session::get("last_activity") > Config::get("session.lifetime") * 60) {
                Session::flush();
                Auth::logout();
            } else {
                Session::put("last_activity", time());
            }

            if ( ! Session::get("logged_in")) {
                Session::flash("notification", Lang::get("notification.login.expired-session"));

                if (Gondolyn::is_api_call()) {
                    throw new ApiException(Lang::get("notification.login.expired-session"), 1);
                }

                return redirect("errors/general");
            }
        });
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function($router) {
            require app_path('Http/routes.php');
        });
    }

}
