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
        mkdir(app_path().'/Modules/'.$module.'/Controllers', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Libraries', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Config', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Models', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Prototypes', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Views', 0777);
        mkdir(app_path().'/Modules/'.$module.'/Tests', 0777);

        /*
        |--------------------------------------------------------------------------
        | Menu
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/menu.php', '<?php

    /*
    |--------------------------------------------------------------------------
    | Module Menu
    |--------------------------------------------------------------------------
    */

?>

<li><a href="<?= URL::to(\''.lcfirst($module).'\'); ?>"><span class="fa fa-gear"></span> '.$module.' Module</a></li>');

        /*
        |--------------------------------------------------------------------------
        | Config & Validation
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Config/config.php', '<?php

/*
|--------------------------------------------------------------------------
| Module Config
|--------------------------------------------------------------------------
*/

return [

    // General
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

    "item" => [
        "action" => [
            "entity" => array("required", "string"),
        ]
    ],

];');


        /*
        |--------------------------------------------------------------------------
        | Routes & Filters
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/filters.php', '');
        $this->makeModuleFile(app_path().'/Modules/'.$module.'/routes.php', '<?php

    /*
    |--------------------------------------------------------------------------
    | Module Routes
    |--------------------------------------------------------------------------
    */

    Route::group(array(\'module\' => \''.$module.'\', \'namespace\' => \'App\Modules\\'.$module.'\Controllers\'), function () {
        Route::get(\''.lcfirst($module).'\',  array(\'uses\' => \''.$module.'Controller@main\'));

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

use Auth;
use Input;
use Redirect;
use View;
use Config;
use Session;
use App\Modules\\'.$module.'\Prototypes\\'.$module.'Prototype;
use App\Modules\\'.$module.'\Models\\'.$module.';

/**
 * '.$module.' Module Controller
 */
class '.$module.'Controller extends \BaseController {

    protected $layout = \'layouts.master\';

    public function __construct()
    {
        \Log::info("Loading '.$module.' Module");
    }

    public function main()
    {
        $prototype = new '.$module.'Prototype;

        $sysData = [
            \'config\'        => Config::get("gondolyn.basic-app-info"),
            \'notification\'  => Session::get("notification"),
            \'welcome\'       => \'Welcome to the '.$module.' module \'.Session::get("username").\' the sample module demonstrates the use of prototypes which would perform the buisness logic of the application therefore allowing controllers to be reserved for framework actions and moving data from models, to prototypes, to views.\'
        ];

        $modelData      = '.$module.'::getA'.$module.'(1);
        $prototypeData  = $prototype->dataModifier($modelData)->output;
        $data           = $prototype->processData($prototypeData, $sysData)->toArray();

        $layoutData = [
            "metadata"    => View::make(\'metadata\', $data),
            "general"     => View::make(\'common\', $data),
            "nav_bar"     => View::make(\'navbar\', $data),
            "content"     => View::make(\''.lcfirst($module).'\', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function api()
    {
        return Gondolyn::response("success", "You have accessed a module");
    }

}');

        /*
        |--------------------------------------------------------------------------
        | Prototypes
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Prototypes/'.$module.'Prototype.php', '<?php namespace App\Modules\\'.$module.'\Prototypes;

/**
 * The purpose of a prototype is to handle all buisness logic
 * enabling solid TDD.
 */
class '.$module.'Prototype extends \Prototype {

    public $output;

    public function __construct()
    {
        parent::__construct();
    }

    public function dataModifier($modelData)
    {
        $data = [
            \'superman\' => $modelData->id,
            \'birthday\' => $modelData->created_at
        ];

        $this->output = $data;

        return $this;
    }

    public function processData($prototypeData, $sysData)
    {
        $data                     = $sysData[\'config\'];
        $data[\'prototype\']      = $prototypeData;
        $data[\'notification\']   = $sysData[\'notification\'];
        $data[\'welcome\']        = $sysData[\'welcome\'];

        $this->output = $data;

        return $this;
    }

}');
        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        $this->makeModuleFile(app_path().'/Modules/'.$module.'/Views/'.lcfirst($module).'.blade.php', '@section(\'content\')
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
        return '.$module.'::findOrFail($id);
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
            Schema::create(lcfirst($module), function ($table) {
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

    public function makeModuleFile($file, $content)
    {
        $moduleFile = fopen($file, "w");

        $file = fwrite($moduleFile, $content);

        fclose($moduleFile);

        return (bool) $file;
    }

}
