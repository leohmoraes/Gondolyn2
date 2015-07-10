<?php

class LandingTest extends TestCase
{

    /**
     * Landing page
     *
     * @return void
     */
    public function testLanding()
    {
        $response = $this->call('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
    }

}
