<?php

class StoreApiTest extends TestCase {

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
     * Store api test fail
     *
     * @return void
     */
    public function testAPIFail()
    {
        $response = $this->call('GET', '/api/store');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('error', json_decode($response->getContent())->status);
    }

    /**
     * Store api test fail
     *
     * @return void
     */
    public function testAPISuccess()
    {
        $this->session($this->session);

        $response = $this->call('GET', '/api/store');

        $this->assertEquals(200, $response->getStatusCode());

        $decoded = json_decode($response->getContent());

        $this->assertEquals('success', $decoded->status);
    }

}