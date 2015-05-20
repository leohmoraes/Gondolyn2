<?php

use App\Services\AccountServices;

class MainController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        // Constructor Args
    }

    public function welcome()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $data["back"] = "";
        $data["user"] = "";

        // If we are logged in lets get personal
        if (AccountServices::loggedIn()) {
            $data["back"] = " back ";
            $data["user"] = Session::get("username");
        }

        return view('main.welcome', $data);
    }

    public function changelog()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $data['changes'] = array_reverse(json_decode(file_get_contents("../build.json")));

        return view('main.changelog', $data);
    }
}
