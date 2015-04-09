<?php namespace App\Modules\Sample\Models;

class Samples extends \Eloquent
{
    protected $table = 'samples';

    public static function getASample($id)
    {
        return Samples::findOrFail($id);
    }

}
