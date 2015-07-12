<?php namespace App\Console\Commands;

use Schema;
use DB;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class dbuild extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'gondolyn:dbuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Database builder for Gondolyn.';

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
        // No database setup
        if ( ! Schema::hasTable('users')) {
            Schema::create('users', function ($table) {
                $table->increments('id');
                $table->string('user_email')->unique();
                $table->string('user_phone')->nullable();
                $table->string('user_passwd');
                $table->string('user_salt');
                $table->string('user_role');
                $table->string('user_alt_email')->nullable();
                $table->string('user_name')->nullable();
                $table->string('user_facebook_id')->nullable();
                $table->string('user_twitter_id')->nullable();
                $table->string('user_active');
                $table->string('profile')->nullable();
                $table->string('user_api_token')->nullable();
                $table->string('remember_token')->nullable();
                $table->string('updated_at');
                $table->string('created_at');
            });

            Schema::create('samples', function ($table) {
                $table->increments('id')->unique();
                $table->string('updated_at');
                $table->string('created_at');
            });

            DB::table('samples')->insert([
                'id' => 1,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);

            DB::table('samples')->insert([
                'id' => 2,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);

            $this->info("Your database has been constructed");
        } else {
            $this->info("Your database already exists");
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
            // ['example', InputArgument::REQUIRED, 'An example argument.'],
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
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

}
