<?php

class ApiTest extends TestCase
{

    public function __construct()
    {
        $this->sessionInfo = [
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
     * Redirect on missing info
     *
     * @return void
     */
    public function testApiRedirect()
    {
        $_SERVER['HTTP']['Token'] = Session::get('Token');

        $response = $this->call('GET', '/api/');

        $this->assertEquals(200, $response->getStatusCode());

        $decoded = json_decode($response->getContent());

        $this->assertEquals('error', $decoded->status);
    }

    /**
     * Fail to get the user Info
     *
     * @return void
     */
    public function testNotLoggedIn()
    {
        $this->sessionInfo['logged_in'] = false;
        $this->sessionInfo['token'] = 'foo';

        $this->session($this->sessionInfo);

        $response = $this->call('GET', '/api/user');

        $this->assertEquals(200, $response->getStatusCode());

        $decoded = json_decode($response->getContent());

        $this->assertEquals('error', $decoded->status);
    }

    /**
     * Fail to get the user Info
     *
     * @return void
     */
    public function testInvalidToken()
    {
        $this->sessionInfo['logged_in'] = true;
        $this->sessionInfo['token'] = 'foo';

        $this->session($this->sessionInfo);

        $response = $this->call('GET', '/api/user');

        $this->assertEquals(200, $response->getStatusCode());

        $decoded = json_decode($response->getContent());

        $this->assertEquals('error', $decoded->status);
    }

    /**
     * API Login
     *
     * @return void
     */
    public function testLogin()
    {
        $this->startSession();

        $creds = [
            'email' => 'foo@bar.com',
            'password' => 'testing',
            'remember' => 'off',
        ];

        $response = $this->call('PUT', '/api/login', [], [], [], [], json_encode($creds));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', json_decode($response->getContent())->status);
    }

    /**
     * API Logout
     *
     * @return void
     */
    public function testLogout()
    {
        $this->sessionInfo['logged_in'] = true;
        $this->sessionInfo['token'] = 'fooToken';

        $this->session($this->sessionInfo);

        $response = $this->call('GET', '/api/logout');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', json_decode($response->getContent())->status);
    }

    /**
     * Get the user Info
     *
     * @return void
     */
    public function testLoggedIn()
    {
        $this->session($this->sessionInfo);

        $response = $this->call('GET', '/api/user');

        $this->assertEquals(200, $response->getStatusCode());

        $decoded = json_decode($response->getContent());

        $this->assertEquals('success', $decoded->status);

        $this->assertEquals(true, $decoded->data->logged_in);
    }

}
