<?php namespace Gondolyn;

use Illuminate\Support\ServiceProvider;

class GondolynServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register 'underlyingclass' instance container to our UnderlyingClass object
        $this->app['Gondolyn'] = $this->app->share(function($app)
        {
            return new Gondolyn;
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Gondolyn', 'Gondolyn\Facades\Gondolyn');
        });
    }
}