<?php namespace App\Modules\Store\Models;

class Store extends \Eloquent {

    protected $table = 'Store';

    public static function getAStore($id)
    {
        return Store::findOrFail($id);
    }

}