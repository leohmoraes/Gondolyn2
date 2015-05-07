<?php namespace App\Providers;

use App\Helpers\Utilities;
use Illuminate\Support\ServiceProvider;

class HelperProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $helpers = glob(app_path().'/Helpers/*');

        foreach ($helpers as $helper) {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $className = Utilities::getFileClass($helper);

            $loader->alias($className, 'App\Helpers\\'.$className);
        }
    }
}
