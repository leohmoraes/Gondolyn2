<?php namespace App\Providers;

use App, Utilities, View;

class ModulesProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $modules = config("modules.modules");

        while (list(,$module) = each($modules)) {

            // Load the Helpers via the Alias loader
            if (is_dir(app_path().'/Modules/'.$module.'/Helpers')) {

                $helpers = glob(app_path().'/Modules/'.$module.'/Helpers/*');

                foreach ($helpers as $helper) {
                    $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                    $className = Utilities::getFileClass($helper);

                    $loader->alias($className, 'App\Modules\\'.$module.'\Helpers\\'.$className);
                }
            }

            // Load the Routes
            if (file_exists(app_path().'/Modules/'.$module.'/routes.php')) {
                require app_path().'/Modules/'.$module.'/routes.php';
            }

            // Load the Filters
            if (file_exists(app_path().'/Modules/'.$module.'/filters.php')) {
                include app_path().'/Modules/'.$module.'/filters.php';
            }

            // Load the Views
            if (is_dir(app_path().'/Modules/'.$module.'/Views')) {
                View::addNamespace(lcfirst($module), app('path').'/Modules/'.$module.'/Views');
            }
        }
    }

    public function register() {}

}
