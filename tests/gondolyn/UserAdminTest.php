<?php

class UserAdminTest extends TestCase
{

    public function loginAdmin()
    {
        $admin = Accounts::find(2);

        if ( ! $admin) {

            Eloquent::unguard();

            $adminInfo = [
                "user_role" => "admin",
                "user_salt" => Utilities::addSalt(10),
                "user_name" => "foo@bar.com",
                "user_email" => "foo@bar.com",
                "user_passwd" => "test",
                "user_active" => "active",
                "stripe_active" => false,
            ];

            $user = Accounts::create($adminInfo);
        } else {
            $user = Accounts::find(2);
        }

        AccountServices::login($user);
    }

    /**
     * Admin Home
     *
     * @return void
     */
    public function testHome()
    {
        $this->loginAdmin();

        $response = $this->call('GET', '/admin/home');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Admin User listing
     *
     * @return void
     */
    public function testAdminUsers()
    {
        $this->loginAdmin();

        $response = $this->call('GET', '/admin/users');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test User Editor
     *
     * @return void
     */
    public function testAdminUserEditor()
    {
        $this->loginAdmin();

        $user = Accounts::find(1);

        $response = $this->call('GET', '/admin/users/editor/'.Crypto::encrypt($user->id));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('user', $user);
        $this->assertResponseOk();
    }

    /**
     * User creator
     *
     * @return void
     */
    public function testAdminUserCreate()
    {
        $this->loginAdmin();

        $response = $this->call('GET', '/admin/users/create');

        $this->assertResponseOk();
    }

    /**
     * User Deactivation
     *
     * @return void
     */
    public function testAdminUserDeactivation()
    {
        $this->loginAdmin();

        $user = Accounts::find(1);

        Session::set("userInEditor", $user);

        DB::beginTransaction();
        $response = $this->call('GET', '/admin/users/deactivate');
        DB::rollback();

        $this->assertEquals(302, $response->getStatusCode());

        $this->assertRedirectedTo('/admin/users/editor/'.Crypto::encrypt($user->id));
    }

    /**
     * User Activation
     *
     * @return void
     */
    public function testAdminUserActivation()
    {
        $this->loginAdmin();

        $user = Accounts::find(1);

        Session::set("userInEditor", $user);

        DB::beginTransaction();
        $response = $this->call('GET', '/admin/users/activate');
        DB::rollback();

        $this->assertEquals(302, $response->getStatusCode());

        $this->assertRedirectedTo('/admin/users/editor/'.Crypto::encrypt($user->id));
    }

    /**
     * User Delete
     *
     * @return void
     */
    public function testAdminUserDelete()
    {
        $this->loginAdmin();

        $user = Accounts::find(1);

        Session::set("userInEditor", $user);

        DB::beginTransaction();
        $response = $this->call('GET', '/admin/users/delete/'.Crypto::encrypt($user->id));
        DB::rollback();

        $this->assertEquals(302, $response->getStatusCode());

        $this->assertRedirectedTo('/admin/users');
    }

    /**
     * User create action
     *
     * @return void
     */
    public function testAdminUserCreateAction()
    {
        $this->startSession();

        $this->loginAdmin();

        Config::set('mail.pretend', 'true');

        DB::beginTransaction();

        $response = $this->call('POST', '/admin/users/generate', array(
            'user_email' => 'foo@bar2.com',
            'user_name' => 'Jiminy Cricket',
            'role' => 'member',
            '_token' => Session::token()
        ));

        DB::rollback();

        $this->assertEquals(302, $response->getStatusCode());
    }

}
