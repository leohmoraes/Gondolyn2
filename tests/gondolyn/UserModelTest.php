<?php

class UserModelTest extends TestCase {

    /**
     * Get Profile
     *
     * @return void
     */
    public function testGetProfile()
    {
        $test = Accounts::getAccount(1);
        $this->assertInstanceOf('Accounts', $test);
    }

    /**
     * Get Profile By Email
     *
     * @return void
     */
    public function testGetProfileByEmail()
    {
        $test = Accounts::getAccountByEmail('foo@bar.com');
        $this->assertInstanceOf('Accounts', $test);
    }

    /**
     * Get Tax
     *
     * @return void
     */
    public function testGetTax()
    {
        $accounts = new Accounts;
        $test = $accounts->getTaxPercent();

        $this->assertEquals(0.0, $test);
    }

    /**
     * Get Billable Name
     *
     * @return void
     */
    public function testGetBillableName()
    {
        $this->session($this->sessionInfo);

        $accounts = new Accounts;
        $test = $accounts->getBillableName();
        $this->assertEquals('foo@bar.com', $test);
    }

    /**
     * Get All Accounts
     *
     * @return void
     */
    public function testGetAllAccounts()
    {
        $test = Accounts::getAllAccounts();
        $this->assertInstanceOf('Illuminate\Pagination\LengthAwarePaginator', $test);
    }

    /**
     * Generate New Password
     *
     * @return void
     */
    public function testGenerateNewPassword()
    {
        $accounts = new Accounts;

        DB::beginTransaction();
        $test = $accounts->generateNewPassword(1);
        DB::rollback();

        $this->assertTrue(is_string($test));
    }

    /**
     * Update User Password
     *
     * @return void
     */
    public function testUpdateMyPassword()
    {
        Input::replace([
            'old_password' => 'testing',
            'new_password' => 'testing',
        ]);

        $test = Accounts::updateMyPassword(1);

        $this->assertTrue($test);
    }

    /**
     * Update User Account
     *
     * @return void
     */
    public function testUpdateAccount()
    {
        Input::replace([
            'email' => 'foo@bar.com',
            'alt_email' => 'foobar@bar.com',
        ]);

        $test = Accounts::updateAccount(1);

        $this->assertTrue($test);
    }

}
