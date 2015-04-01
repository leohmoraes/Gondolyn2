<?php

use Session;

class ApiLoggedInTest extends TestCase {

    /**
     * Fail to get the user Info
     *
     * @return void
     */
    public function testGetUserData()
    {
        $session = [
            "logged_in"        => true,
            "id"               => "1",
            "role"             => "admin",
            "token"            => "3cf14c0c4e8ed3aa5c23ec020602a666",
            "plan"             => null,
            "subscribed"       => false,
            "last_activity"    => 1427863774,
            "username"         => "mattlantz@gmail.com",
            "email"            => "mattlantz@gmail.com"
        ];

        $this->session($session);

        $_SERVER['HTTP']['Token'] = '3cf14c0c4e8ed3aa5c23ec020602a666';

        $server = [
            'Token' => '3cf14c0c4e8ed3aa5c23ec020602a666',
            'Authorization' => Config::get('gondolyn.authKeys')[0]
        ];

        // $parameters  = ['user'];
        // $cookies     = [];
        // $files       = [];

        // var_dump($_SERVER['HTTP']['Token']); exit;

        // $response = $this->call('GET', '/api/user', $parameters, $cookies, $files, $server, "");

        // var_dump($response->getContent()); exit;

        // $this->assertEquals(200, $response->getContent());
    }

    /*
    |--------------------------------------------------------------------------
    | Private
    |--------------------------------------------------------------------------
    */

    private function getToken()
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json'
        ];

        $creds = [
            'email' => 'foo@bar.com',
            'password' => 'testing',
            'remember' => 'on'
        ];

        $response = $this->call('PUT', '/api/login', [], [], [], $headers, json_encode($creds));

        return json_decode($response->getContent())->data;
    }

}
