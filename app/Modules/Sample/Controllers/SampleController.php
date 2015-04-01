<?php namespace App\Modules\Sample\Controllers;

use Auth;
use Input;
use Redirect;
use View;
use Config;
use Session;
use Gondolyn;
use App\Modules\Sample\Models\Samples;

/**
 * Sample Module Controller
 */
class SampleController extends \BaseController {

    protected $layout = 'layouts.master';

    public function __construct()
    {
        \Log::info("Loading Sample Module");
    }

    public function main()
    {
        $data                   = Config::get("gondolyn.basic-app-info");
        $data['info']           = Samples::getASample(1);
        $data['notification']   = Session::get("notification");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('sample', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function api()
    {
        return Gondolyn::response("success", "You have accessed a module");
    }

}