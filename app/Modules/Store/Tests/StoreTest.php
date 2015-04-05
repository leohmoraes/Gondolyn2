<?php

class StoreTest extends TestCase {

    /**
     * Store main test
     *
     * @return void
     */
    public function testMain()
    {
        $response = $this->call('GET', '/store/');

        $this->assertEquals(200, $response->getStatusCode());
    }

}