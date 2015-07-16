<?php namespace App\Modules\Sample\Services;

use Session;
use App\Modules\Sample\Models\Samples;

class SampleService implements SampleInterface
{
    public $output;

    public function getSamples($sortby)
    {
        return Samples::getSamples($sortby);
    }

    public function serviceInformation($config)
    {
        $data                   = $config;
        $data['notification']   = Session::get("notification");
        $data['welcome']        = 'Welcome to the Sample module '.Session::get("username").', the sample module demonstrates the use of services which would perform the buisness logic of the application therefore allowing controllers to be reserved for framework actions and moving data from models, to services, to views.';

        return $data;
    }

    public static function sorter($sort = null)
    {
        $sortDirection = 'asc';
        $sortBy = ['id', $sortDirection];

        if ( ! is_null($sort)) {
            $sortBy  = explode('|', $sort);
            if ($sortBy[1] === 'asc') $sortDirection = 'desc';
            if ($sortBy[1] === 'desc') $sortDirection = 'asc';
        }

        return [
            'dir' => $sortDirection,
            'sortby' => $sortBy,
        ];
    }

}
