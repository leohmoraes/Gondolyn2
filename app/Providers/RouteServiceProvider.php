<?php namespace App\Providers;

use Session;
use Config;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {

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

		Route::filter('validator', function()
		{
		    if ( ! Gondolyn::valid_api_request())
		    {
		        return Gondolyn::response("error", "Invalid request token");
		    }
		});

		Route::filter('is_logged_in', function()
		{
		    if (time() - Session::get("last_activity") > Config::get("session.lifetime") * 60) Session::flush();
		    else Session::put("last_activity", time());

		    if ( ! Session::get("logged_in"))
		    {
		        Session::flash("notification", "You're not currently logged in.");
		        return redirect("errors/general");
		    }
		});

		Route::filter('is_member_logged_in', function()
		{
		    if (Session::get("role") != "member")
		    {
		        Session::flash("notification", "You're not currently logged in.");
		        return redirect("errors/general");
		    }
		});

		Route::filter('is_admin_logged_in', function()
		{
		    if (Session::get("role") != "admin")
		    {
		        Session::flash("notification", "You're not currently logged in.");
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
		$router->group(['namespace' => $this->namespace], function($router)
		{
			require app_path('Http/routes.php');
		});
	}

}
