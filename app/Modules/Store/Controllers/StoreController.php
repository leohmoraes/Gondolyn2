<?php namespace App\Modules\Store\Controllers;

use Auth;
use Input;
use Redirect;
use View;
use Config;
use Session;
use App\Modules\Store\Prototypes\StorePrototype;
use App\Modules\Store\Models\Stores;

/**
 * Store Module Controller
 */
class StoreController extends \BaseController {

    protected $layout = 'layouts.master';

    public function __construct()
    {
        \Log::info("Loading Store Module");
    }

    public function main()
    {
        $prototype = new StorePrototype;

        $sysData = [
            'config'        => Config::get("gondolyn.basic-app-info"),
            'notification'  => Session::get("notification"),
            'welcome'       => 'Welcome to the Store module '.Session::get("username").' the sample module demonstrates the use of prototypes which would perform the buisness logic of the application therefore allowing controllers to be reserved for framework actions and moving data from models, to prototypes, to views.'
        ];

        $modelData      = Store::getAStore(1);
        $prototypeData  = $prototype->dataModifier($modelData)->output;
        $data           = $prototype->processData($prototypeData, $sysData)->toArray();

        $layoutData = [
            "metadata"    => View::make('metadata', $data),
            "general"     => View::make('common', $data),
            "nav_bar"     => View::make('navbar', $data),
            "content"     => View::make('store', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function api()
    {
        return Gondolyn::response("success", "You have accessed a module");
    }

}