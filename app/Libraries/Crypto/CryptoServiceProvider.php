<?php namespace Crypto;

use Illuminate\Support\ServiceProvider;

class CryptoServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register 'underlyingclass' instance container to our UnderlyingClass object
        $this->app['Crypto'] = $this->app->share(function($app)
        {
            return new Crypto;
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Crypto', 'Crypto\Facades\Crypto');
        });
    }
}