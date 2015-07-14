<?php

class AccountTest extends TestCase
{
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

        Config::set('mail.pretend', 'true');

        DB::beginTransaction();

        $response = $this->call('POST', 'forgot/password/request', array(
            'email' => 'foo@bar.com',
            '_token' => Session::token()
        ));

        DB::rollback();

        $this->assertRedirectedTo('/');
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

        $this->assertRedirectedTo('http://localhost');
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

        $this->assertRedirectedTo('http://localhost');
    }

    /**
     * User Settings - Standard
     *
     * @return void
     */
    public function testUserSettings()
    {
        $user = Accounts::find(1);
        AccountServices::login($user);

        $response = $this->call('GET', 'account/settings');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('user', $user);
    }

    /**
     * User Settings - Password
     *
     * @return void
     */
    public function testUserSettingsPassword()
    {
        $user = Accounts::find(1);
        AccountServices::login($user);

        $response = $this->call('GET', 'account/settings/password');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('user', $user);
    }

    /**
     * User Settings - Subscription
     *
     * @return void
     */
    public function testUserSettingsSubscription()
    {
        $user = Accounts::find(1);
        AccountServices::login($user);

        $response = $this->call('GET', 'account/settings/subscription');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('user', $user);
    }

    /**
     * User Settings - Delete
     *
     * @return void
     */
    public function testUserAccountDelete()
    {
        $user = Accounts::find(1);
        AccountServices::login($user);

        DB::beginTransaction();

        $response = $this->call('GET', 'account/delete/account');

        DB::rollback();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedTo('http://localhost');
    }

    /**
     * User Settings - Setting Save Fail
     *
     * @return void
     */
    public function testUserSettingsSaveFail()
    {
        $this->startSession();

        $user = Accounts::find(1);
        AccountServices::login($user);

        DB::beginTransaction();

        $response = $this->call('POST', 'account/settings/update', [
            'user_name' => 'mr. awesome',
            'user_alt_email' => 'mrawesome@bar.com',
            '_token' => Session::token(),
        ]);

        DB::rollback();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedTo('http://localhost');
    }

    /**
     * User Settings - Setting Save
     *
     * @return void
     */
    public function testUserSettingsSave()
    {
        $this->startSession();

        $user = Accounts::find(1);
        AccountServices::login($user);

        DB::beginTransaction();

        $response = $this->call('POST', 'account/settings/update', [
            'user_email' => $user->user_email,
            'user_name' => 'mr. awesome',
            'country' => 'CA',
            'state' => 'Ontario',
            'user_alt_email' => 'mrawesome@bar.com',
            '_token' => Session::token(),
        ]);

        DB::rollback();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedTo('account/settings');
    }

}
