<?php

class ErrorsTest extends TestCase {

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testError()
    {
        $response = $this->call('GET', '/errors');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testErrorGeneral()
    {
        $response = $this->call('GET', '/errors/general');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testErrorCritical()
    {
        $response = $this->call('GET', '/errors/critical');

        $this->assertEquals(302, $response->getStatusCode());
    }

}
