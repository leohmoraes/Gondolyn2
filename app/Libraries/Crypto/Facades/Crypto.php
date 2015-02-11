<?php namespace Crypto\Facades;

use Illuminate\Support\Facades\Facade;

class Crypto extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Crypto'; }

}