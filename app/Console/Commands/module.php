<?php namespace App\Console\Commands;

use Schema;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class module extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'gondolyn:module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build modules for Gondolyn';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $module = ucfirst($this->argument('name'));

        mkdir(app_path().'/Modules/'.$module, 0777);
        mkdir(app_path().'/Modules/'.$module.'/Assets', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Assets/js', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Assets/css', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Config', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Controllers', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Commands', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Helpers', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Lang', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Lang/en', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Models', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Providers', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Requests', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Services', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Services/Interfaces', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Tests', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Views', 0777);

        /*
        |--------------------------------------------------------------------------
        | Menu
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Views/menu.blade.php', '<?php

    /*
    |--------------------------------------------------------------------------
    | Module Menu
    |--------------------------------------------------------------------------
    */

?>

<li><a href="<?= URL::to(\''.lcfirst($module).'\'); ?>"><span class="fa fa-gear"></span> '.$module.' Module</a></li>');


        /*
        |--------------------------------------------------------------------------
        | Module Langs
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Lang/en/notifications.php', '<?php

/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
*/

return array(

    "alert" => [
        "module" => "You are in a module",
    ],

);');

        /*
        |--------------------------------------------------------------------------
        | Config & Validation & Permissions
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Config/config.php', '<?php

/*
|--------------------------------------------------------------------------
| Module Config
|--------------------------------------------------------------------------
*/

return [

    // CSRF Ignored Routes
    "csrfIgnoredRoutes" => [
        "api/put-request",
    ],

];');

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Config/validation.php', '<?php

/*
|--------------------------------------------------------------------------
| Validation Config
|--------------------------------------------------------------------------
*/

return [

    "form" => [
        "action" => [
            "entity" => array("required", "string"),
        ]
    ],

];');

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Config/permissions.matrix.php', '<?php

/*
|--------------------------------------------------------------------------
| Permission Matix
|--------------------------------------------------------------------------
|
| Here we can add to the roles and expand the groups of roles that the
| permission matrix uses. Groups arrays can only be one level deep. We then define in routes:
|
| "permission" => "role OR groups.groupName"
|
*/

return [

    "roles" => [
        "'.$module.'_user"
    ],

    "groups" => [
        "all" => [
            "'.$module.'_user"
        ],
    ],

];');


        /*
        |--------------------------------------------------------------------------
        | Routes & Filters
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/filters.php', '<?php

    // Filters to be run in routes
    Route::filter("'.lcfirst($module).'Filter", function () {
        Log::info("Filtering '.$module.' Module");
    });

');
        $this->makeModuleFile(app_path().'/Modules/'.$module.'/routes.php', '<?php

    /*
    |--------------------------------------------------------------------------
    | Module Routes
    |--------------------------------------------------------------------------
    */

    Route::group(array(\'module\' => \''.$module.'\', \'namespace\' => \'App\Modules\\'.$module.'\Controllers\'), function () {
        Route::get(\''.lcfirst($module).'\',  array(\'before\' => \''.lcfirst($module).'Filter\', \'uses\' => \''.$module.'Controller@main\'));

        // API actions
        Route::group(array(\'prefix\' => \'api\', \'before\' => array(\'valid_api_key\', \'valid_api_token\')), function () {
            Route::get(\''.lcfirst($module).'\',  array(\'uses\' => \''.$module.'Controller@api\'));
        });

    });');

        /*
        |--------------------------------------------------------------------------
        | Controllers
        |--------------------------------------------------------------------------
        */


$this->makeModuleFile(app_path().'/Modules/'.$module.'/Controllers/'.$module.'Controller.php', '<?php namespace App\Modules\\'.$module.'\Controllers;

use Auth, Input, Redirect, View, Config, Session, Log, App, Gondolyn;

use App\Modules\\'.$module.'\Services\\'.$module.'Service;
use App\Modules\\'.$module.'\Models\\'.$module.';

/**
 * '.$module.' Module Controller
 */
class '.$module.'Controller extends \BaseController {

    protected $layout = \'layouts.master\';

    public function __construct()
    {
        Log::info("Loading '.$module.' Module");
        App::register(\'App\Modules\\'.$module.'\Providers\\'.$module.'ServiceProvider\');
    }

    public function main()
    {
        $service = new '.$module.'Service;

        $sysData = [
            \'config\'        => Config::get("gondolyn.basic-app-info"),
            \'notification\'  => Session::get("notification"),
            \'welcome\'       => \'Welcome to the '.$module.' module \'.Session::get("username").\' the sample module demonstrates the use of prototypes which would perform the buisness logic of the application therefore allowing controllers to be reserved for framework actions and moving data from models, to prototypes, to views.\'
        ];

        $modelData      = '.$module.'::getA'.$module.'(1);
        $serviceData    = $service->dataModifier($modelData);
        $data           = $service->processData($serviceData, $sysData);

        return view(\''.lcfirst($module).'::'.lcfirst($module).'\', $data);
    }

    public function api()
    {
        return Gondolyn::response("success", "You have accessed a module");
    }

}');

        /*
        |--------------------------------------------------------------------------
        | Providers
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Providers/'.$module.'ServiceProvider.php', '<?php namespace App\Modules\\'.$module.'\Providers;

class '.$module.'ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Alias the services in the boot
     *
     * @return void
     */
    public function boot()
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias("'.$module.'Service", "App\Modules\\'.$module.'\Services\\'.$module.'Service");
    }

    /**
     * Register the services.
     *
     * @return void
     */
    public function register()
    {
    }
}');

        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Services/'.$module.'Service.php', '<?php namespace App\Modules\\'.$module.'\Services;

use App\Modules\\'.$module.'\Services\Interfaces\\'.$module.'ServiceInterface;

class '.$module.'Service implements '.$module.'ServiceInterface {

    public function dataModifier($modelData)
    {
        $data = [
            \'superman\' => $modelData->id,
            \'birthday\' => $modelData->created_at
        ];

        return $data;
    }

    public function processData($prototypeData, $sysData)
    {
        $data                     = $sysData[\'config\'];
        $data[\'prototype\']      = $prototypeData;
        $data[\'notification\']   = $sysData[\'notification\'];
        $data[\'welcome\']        = $sysData[\'welcome\'];

        return $data;
    }

}');

        /*
        |--------------------------------------------------------------------------
        | Interfaces
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Services/Interfaces/'.$module.'ServiceInterface.php', '<?php namespace App\Modules\\'.$module.'\Services\Interfaces;

interface '.$module.'ServiceInterface {

    public function dataModifier($modelData);

    public function processData($prototypeData, $sysData);

}');

        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Views/'.lcfirst($module).'.blade.php', '@extends(\'layouts.standard\')

@section(\'content\')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h1>'.$module.' Module #1</h1>
            <p>This is an example of a module.</p>
            <br>
            <p>{{ $welcome }}</p>
        </div>
    </div>
@stop');

        /*
        |--------------------------------------------------------------------------
        | Models
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Models/'.$module.'.php', '<?php namespace App\Modules\\'.$module.'\Models;

class '.$module.' extends \Eloquent {

    protected $table = \''.$module.'\';

    public static function getA'.$module.'($id)
    {
        //return '.$module.'::findOrFail($id);

        $result = new \stdClass;
        $result->id = 1;
        $result->created_at = "today";

        return $result;
    }

}');

        /*
        |--------------------------------------------------------------------------
        | Tests
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Tests/'.$module.'Test.php', '<?php

class '.$module.'Test extends TestCase {

    /**
     * '.$module.' main test
     *
     * @return void
     */
    public function testMain()
    {
        $response = $this->call(\'GET\', \'/'.lcfirst($module).'/\');

        $this->assertEquals(200, $response->getStatusCode());
    }

}');

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Tests/'.$module.'ApiTest.php', '<?php

class '.$module.'ApiTest extends TestCase {

    public function __construct()
    {
        $this->session = [
            "logged_in"        => true,
            "id"               => "1",
            "role"             => "admin",
            "token"            => "fooToken",
            "plan"             => null,
            "subscribed"       => false,
            "last_activity"    => 1427863774,
            "username"         => "foo@bar.com",
            "email"            => "foo@bar.com"
        ];
    }

    /**
     * '.$module.' api test fail
     *
     * @return void
     */
    public function testAPIFail()
    {
        $response = $this->call(\'GET\', \'/api/'.lcfirst($module).'\');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(\'error\', json_decode($response->getContent())->status);
    }

    /**
     * '.$module.' api test fail
     *
     * @return void
     */
    public function testAPISuccess()
    {
        $this->session($this->session);

        $response = $this->call(\'GET\', \'/api/'.lcfirst($module).'\');

        $this->assertEquals(200, $response->getStatusCode());

        $decoded = json_decode($response->getContent());

        $this->assertEquals(\'success\', $decoded->status);
    }

}');

        $this->info('Your '.$module.' module has been generated.');

        if ($this->option('table')) {
            Schema::create(lcfirst($module), function($table) {
                $table->increments('id');
                $table->string('updated_at');
                $table->string('created_at');
            });

            $this->info('Your '.$module.' module table has been generated.');
        } else {
            $this->info('Please build a migration for your module: php artisan make:migration added_'.lcfirst($module).'_module');
        }


    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'A module name'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['table', null, InputOption::VALUE_OPTIONAL, 'Add the database table.', null],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Internal Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Make Module File
     * @param  string $file    File
     * @param  string $content Content of file
     * @return boolean
     */
    public function makeModuleFile($file, $content)
    {
        $moduleFile = fopen($file, "w");

        $file = fwrite($moduleFile, $content);

        fclose($moduleFile);

        return (bool) $file;
    }

}
