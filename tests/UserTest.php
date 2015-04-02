<?php

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
        $this->startSession();

        $response = $this->call('POST', 'forgot/password/request', array(
            'email' => 'foo@bar.com',
            '_token' => Session::token()
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
        $this->startSession();

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
        $this->startSession();

        $inputs = array(
            'email' => 'foo@bar.com',
            'password' => 'testing',
            'remember_me' => 'off',
            '_token' => Session::token(),
        );

        $response = $this->call('POST', 'login/request', $inputs);

        $this->assertRedirectedTo('login/email');
    }

}
