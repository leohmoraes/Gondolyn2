<?php namespace App\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request, Kernel $kernel)
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('AccountServices', 'App\Services\AccountServices');

        if ($request->isMethod('OPTIONS')) {
            $kernel->pushMiddleware('App\Http\Middleware\Preflight');
        }
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            'App\Services\AppServices',
            'App\Services\AccountServices'
        );
    }

}
