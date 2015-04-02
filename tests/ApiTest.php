<?php

class ApiTest extends TestCase {

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
     * Redirect on missing info
     *
     * @return void
     */
    public function testApiRedirect()
    {
        $_SERVER['HTTP']['Token'] = Session::get('Token');

        $response = $this->call('GET', '/api/');

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Fail to get the user Info
     *
     * @return void
     */
    public function testNotLoggedIn()
    {
        $this->session['logged_in'] = false;
        $this->session['token'] = 'foo';

        $this->session($this->session);

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
        $this->session['logged_in'] = true;
        $this->session['token'] = 'foo';

        $this->session($this->session);

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
    public function testLogin()
    {
        $creds = [
            'email' => 'foo@bar.com',
            'password' => 'testing',
            'remember' => 'on'
        ];

        $headers = [
            'CONTENT_TYPE' => 'application/json'
        ];

        $response = $this->call('PUT', '/api/login', [], [], [], $headers, json_encode($creds));

        $this->token = json_decode($response->getContent())->data;

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
        $this->session($this->session);

        $response = $this->call('GET', '/api/user');

        $this->assertEquals(200, $response->getStatusCode());

        $decoded = json_decode($response->getContent());

        $this->assertEquals('success', $decoded->status);

        $this->assertEquals(true, $decoded->data->logged_in);
    }

}
