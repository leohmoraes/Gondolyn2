<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase {

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
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
