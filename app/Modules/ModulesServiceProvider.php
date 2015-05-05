<?php namespace App\Modules;

class ModulesServiceProvider extends  \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $modules = config("modules.modules");

        while (list(,$module) = each($modules)) {
            if (file_exists(__DIR__.'/'.$module.'/routes.php')) {
                include __DIR__.'/'.$module.'/routes.php';
            }

            if (file_exists(__DIR__.'/'.$module.'/filters.php')) {
                include __DIR__.'/'.$module.'/filters.php';
            }

            if (is_dir(__DIR__.'/'.$module.'/Views')) {
                \View::addLocation(app('path').'/Modules/'.$module.'/Views');
            }
        }
    }

    public function register() {}

}
