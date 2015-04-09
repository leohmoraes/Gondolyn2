<?php

class SampleTest extends TestCase
{
    /**
     * Sample main test
     *
     * @return void
     */
    public function testMain()
    {
        $response = $this->call('GET', '/sample');

        $this->assertEquals(200, $response->getStatusCode());
    }

}
