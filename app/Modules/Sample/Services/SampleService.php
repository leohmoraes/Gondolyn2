<?php namespace App\Modules\Sample\Services;

use Session;
use App\Modules\Sample\Models\Samples;

class SampleService implements SampleInterface
{
    public $output;

    public function getSamples()
    {
        return Samples::getSamples();
    }

    public function serviceInformation($config)
    {
        $data                   = $config;
        $data['notification']   = Session::get("notification");
        $data['welcome']        = 'Welcome to the Sample module '.Session::get("username").' the sample module demonstrates the use of services which would perform the buisness logic of the application therefore allowing controllers to be reserved for framework actions and moving data from models, to services, to views.';

        return $data;
    }

}
