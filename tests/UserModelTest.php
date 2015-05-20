<?php

class UserModelTest extends TestCase {

    /**
     * Check the home page
     *
     * @return void
     */
    public function testGetProfile()
    {
        $test = Accounts::getAccount(1);
        $this->assertInstanceOf('Accounts', $test);
    }

    /**
     * Check the home page
     *
     * @return void
     */
    public function testGetProfileByEmail()
    {
        $test = Accounts::getAccountByEmail('foo@bar.com');
        $this->assertInstanceOf('Accounts', $test);
    }

}
