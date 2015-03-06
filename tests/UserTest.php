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
        $response = $this->call('POST', 'forgot/passowrd/request', array(
            'email' => 'what@who.com',
            '_token' => Session::token(),
        ));

        $this->assertRedirectedTo('errors/general');
    }

}
