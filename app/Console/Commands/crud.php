<?php namespace App\Console\Commands;

use Schema;
use Config;
use Artisan;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Foundation\Application;
use Mitul\Generator\Commands\BaseCommand;
use Mitul\Generator\CommandData;
use Mitul\Generator\Generators\API\RepoAPIControllerGenerator;
use Mitul\Generator\Generators\Common\MigrationGenerator;
use Mitul\Generator\Generators\Common\ModelGenerator;
use Mitul\Generator\Generators\Common\RepositoryGenerator;
use Mitul\Generator\Generators\Common\RequestGenerator;
use Mitul\Generator\Generators\Common\RoutesGenerator;
use Mitul\Generator\Generators\Scaffold\RepoViewControllerGenerator;
use Mitul\Generator\Generators\Scaffold\ViewGenerator;

class crud extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'gondolyn:crud';

    public $commandData;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a CRUD Scaffolding and/or API';

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
    public function handle()
    {
        if (strtolower($this->argument('platform')) !== 'core') {
            $platform = ucfirst($this->argument('platform'));
            Config::set('generator', [
                'path_migration'           => base_path('database/migrations/'),
                'path_model'               => app_path('Modules/'.$platform.'/Models/'),
                'path_repository'          => app_path('Modules/'.$platform.'/Services/Repositories/'),
                'path_controller'          => app_path('Modules/'.$platform.'/Controllers/'),
                'path_api_controller'      => app_path('Modules/'.$platform.'/Controllers/API/'),
                'path_views'               => base_path('Modules/'.$platform.'/Views'),
                'path_request'             => app_path('Modules/'.$platform.'/Requests/'),
                'path_routes'              => app_path('Modules/'.$platform.'/routes.php'),
                'namespace_model'          => 'App\Modules\''.$platform.'\Models',
                'namespace_repository'     => 'App\Modules\''.$platform.'\Services\Repositories',
                'namespace_controller'     => 'App\Modules\''.$platform.'\Controllers',
                'namespace_api_controller' => 'App\Modules\''.$platform.'\Controllers\API',
                'namespace_request'        => 'App\Modules\''.$platform.'\Requests',
                'model_extend'             => false,
                'model_extend_namespace'   => 'Illuminate\Database\Eloquent\Model',
                'model_extend_class'       => 'Model',
                'api_prefix'               => 'api',
            ]);
        }

        $this->commandData = new CommandData($this, $this->argument('scope'));
        $this->commandData->modelName = $this->argument('model');
        $this->commandData->useSoftDelete = $this->option('softDelete');
        $this->commandData->useSearch = $this->option('search');
        $this->commandData->fieldsFile = $this->option('fieldsFile');
        $this->commandData->initVariables();

        if ($this->commandData->fieldsFile)
        {
            $fileHelper = new FileHelper();
            try {
                if (file_exists($this->commandData->fieldsFile)) {
                    $filePath = $this->commandData->fieldsFile;
                }
                else {
                    $filePath = base_path($this->commandData->fieldsFile);
                }

                if ( ! file_exists($filePath)) {
                    $this->commandData->commandObj->error("Fields file not found");
                    exit;
                }

                $fileContents = $fileHelper->getFileContents($filePath);
                $fields = json_decode($fileContents, true);

                $this->commandData->inputFields = GeneratorUtils::validateFieldsFile($fields);
            } catch(Exception $e) {
                $this->commandData->commandObj->error($e->getMessage());
                exit;
            }
        } else {
            $this->commandData->inputFields = $this->commandData->getInputFields();
        }

        $migrationGenerator = new MigrationGenerator($this->commandData);
        $migrationGenerator->generate();

        $modelGenerator = new ModelGenerator($this->commandData);
        $modelGenerator->generate();

        $requestGenerator = new RequestGenerator($this->commandData);
        $requestGenerator->generate();

        $repositoryGenerator = new RepositoryGenerator($this->commandData);
        $repositoryGenerator->generate();

        if (stristr($this->argument('platform'), 'api')) {
            $repoControllerGenerator = new RepoAPIControllerGenerator($this->commandData);
            $repoControllerGenerator->generate();
        }

        $viewsGenerator = new ViewGenerator($this->commandData);
        $viewsGenerator->generate();

        $repoControllerGenerator = new RepoViewControllerGenerator($this->commandData);
        $repoControllerGenerator->generate();

        $routeGenerator = new RoutesGenerator($this->commandData);
        $routeGenerator->generate();

        if($this->confirm("\nDo you want to migrate database? [y|N]", false)) {
            $this->call('migrate');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return array_merge(parent::getArguments(), [
            ['platform', InputArgument::REQUIRED, 'Either a module name or core for directly inside Gondolyn'],
            ['scope', InputArgument::REQUIRED, 'CRUD scope - api, scaffold, scaffold_api'],
        ]);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);

    }
}
