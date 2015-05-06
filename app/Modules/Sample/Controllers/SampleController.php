<?php namespace App\Modules\Sample\Controllers;

use Auth;
use Input;
use Redirect;
use View;
use Config;
use Session;
use Gondolyn;
use App\Modules\Sample\Services\SampleService;
use App\Modules\Sample\Models\Samples;

/**
 * Sample Module Controller
 */
class SampleController extends \BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        \Log::info("Loading Sample Module");
    }

    public function main()
    {
        $service = new SampleService;

        $sysData = [
            'config'        => Config::get("gondolyn.basic-app-info"),
            'notification'  => Session::get("notification"),
            'welcome'       => 'Welcome to the Sample module '.Session::get("username").' the sample module demonstrates the use of services which would perform the buisness logic of the application therefore allowing controllers to be reserved for framework actions and moving data from models, to services, to views.'
        ];

        $modelData      = Samples::getASample(1);
        $serviceData    = $service->dataModifier($modelData);
        $data           = $service->processData($serviceData, $sysData);

        $layoutData = [
            "metadata"    => View::make('metadata', $data),
            "general"     => View::make('common', $data),
            "nav_bar"     => View::make('navbar', $data),
            "content"     => View::make('sample', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function api()
    {
        return Gondolyn::response("success", "You have accessed a module");
    }

}
