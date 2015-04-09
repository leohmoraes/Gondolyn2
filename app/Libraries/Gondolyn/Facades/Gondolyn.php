<?php namespace Gondolyn\Facades;

use Illuminate\Support\Facades\Facade;

class Gondolyn extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Gondolyn'; }

}
