<?php

class DashboardTest extends TestCase
{
    /**
     * Tests the dashboard
     *
     * @return void
     */
    public function testDashboard()
    {
        $user = Accounts::find(1);
        AccountServices::login($user);

        $response = $this->call('GET', '/dashboard');

        $this->assertEquals(200, $response->getStatusCode());
    }

}
