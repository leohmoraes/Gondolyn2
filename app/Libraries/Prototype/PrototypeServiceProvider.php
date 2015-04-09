<?php namespace Prototype;

use Illuminate\Support\ServiceProvider;

class PrototypeServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register 'underlyingclass' instance container to our UnderlyingClass object
        $this->app['Prototype'] = $this->app->share(function ($app) {
            return new Prototype;
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Prototype', 'Prototype\Prototype');
        });
    }
}
