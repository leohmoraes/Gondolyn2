<?php

use Session;

class UserTest extends TestCase {

    /**
     * Check the home page
     *
     * @return void
     */
    public function testHome()
    {
        $response = $this->call('GET', '/login/email');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Check the forgot password page
     *
     * @return void
     */
    public function testForgotPassword()
    {
        $response = $this->call('GET', '/forgot/password');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Check with a bad account
     *
     * @return void
     */
    public function testForgotPasswordAction()
    {
        Session::start();

        $response = $this->call('POST', 'forgot/passowrd/request', array(
            'email' => 'foo@bar.com',
            '_token' => Session::token(),
        ));

        $this->assertRedirectedTo('errors/general');
    }

    /**
     * Check log in
     *
     * @return void
     */
    public function testLoginFail()
    {
        Session::start();

        $response = $this->call('POST', 'login/request', array(
            'email' => 'foo@bar.com',
            'password' => 'testing',
            '_token' => Session::token(),
        ));

        $this->assertRedirectedTo('login/email');
    }

    /**
     * Check log in
     *
     * @return void
     */
    public function testLoginSuccess()
    {
        Session::start();

        $inputs = array(
            'email' => 'what@who.com',
            'password' => 'testing1234',
            '_token' => Session::token(),
        );

        $response = $this->call('POST', 'login/request', $inputs);

        $this->assertRedirectedTo('member/home');
    }

}
