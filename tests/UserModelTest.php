<?php

use Users;

class UserModelTest extends TestCase {

    /**
     * Check the home page
     *
     * @return void
     */
    public function testGetProfile()
    {
        $test = Users::getMyProfile(1);
        $this->assertInstanceOf('Users', $test);
    }

    /**
     * Check the home page
     *
     * @return void
     */
    public function testGetProfileByEmail()
    {
        $test = Users::getMyProfileByEmail('foo@bar.com');
        $this->assertInstanceOf('Users', $test);
    }

}
