<?php namespace App\Console\Commands;

use Schema;
use DB;
use Utilities;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class key extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'gondolyn:key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets the API Auth keys';

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
        $fileContents = file_get_contents(base_path().'/config/gondolyn.php');

        preg_match_all("/'authKeys' => \[((.|\n)*?)\],/", $fileContents, $originalAuthKeys);

        $authKeys = "'authKeys' => [\n";
        foreach (range(0, 10) as $x) {
            $authKeys .= "        '".Utilities::addSalt(28)."',\n";
        }
        $authKeys .= "    ],";

        $gondolynConfig = str_replace($originalAuthKeys[0][0], $authKeys, $fileContents);

        file_put_contents(base_path().'/config/gondolyn.php', $gondolynConfig);

        $this->info("Your authKeys are set");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

}
