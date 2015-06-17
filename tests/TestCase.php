<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    protected $baseUrl = 'http://localhost';

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
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $_SERVER['HTTP_HOST'] = 'cli';

        if (php_sapi_name() === 'cli' && ! function_exists('getallheaders'))
        {
            function getallheaders()
            {
                $headers = '';

                $_SERVER['HTTP_Token'] = 'fooToken';
                $_SERVER['HTTP_Authorization'] = Config::get('gondolyn.authKeys')[0];

                foreach ($_SERVER as $name => $value)
                {
                    if (substr($name, 0, 5) == 'HTTP_')
                    {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }

                return $headers;
            }
        }

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

}
