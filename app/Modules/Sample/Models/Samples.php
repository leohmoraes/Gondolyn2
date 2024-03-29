<?php namespace App\Modules\Sample\Models;

use Input;

class Samples extends \Eloquent
{
    protected $table = 'samples';

    public static $rules = [
        'created_at' => 'required',
    ];

    public static function getSamples($sortby)
    {
        return Samples::orderby($sortby[0], $sortby[1])->get();
    }

    public static function getSample($id)
    {
        return Samples::find($id);
    }

    public static function editSample($id)
    {
        $sample = Samples::getSample($id);

        $sample->created_at = Input::get('created_at');
        $sample->updated_at = Input::get('updated_at');

        return $sample->save();
    }

}
