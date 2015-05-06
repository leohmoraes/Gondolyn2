<?php namespace App\Modules;

use App;

class ModulesServiceProvider extends  \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $modules = config("modules.modules");

        while (list(,$module) = each($modules)) {

            // Load the Helpers via the Alias loader
            if (is_dir(__DIR__.'/'.$module.'/Helpers')) {

                $helpers = glob(__DIR__.'/'.$module.'/Helpers/*');

                foreach ($helpers as $helper) {
                    $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                    $className = $this->getFileClass($helper);

                    $loader->alias($className, 'App\Modules\\'.$module.'\Helpers\\'.$className);
                }
            }

            // Load the Routes
            if (file_exists(__DIR__.'/'.$module.'/routes.php')) {
                require __DIR__.'/'.$module.'/routes.php';
            }

            // Load the Filters
            if (file_exists(__DIR__.'/'.$module.'/filters.php')) {
                include __DIR__.'/'.$module.'/filters.php';
            }

            // Load the Views
            if (is_dir(__DIR__.'/'.$module.'/Views')) {
                \View::addLocation(app('path').'/Modules/'.$module.'/Views');
            }
        }
    }

    public function register() {}

    /**
     * Generate a name from the file path
     * @param  string $file File path
     * @return string
     */
    private function getFileClass($file)
    {
        $sections = explode('/', $file);
        $fileName = $sections[count($sections) - 1];

        $class = str_replace('.php', '', $fileName);

        return $class;
    }

}
