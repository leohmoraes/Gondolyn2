<?php

class LandingTest extends TestCase {

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testLanding()
    {
        $response = $this->call('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
    }

}
