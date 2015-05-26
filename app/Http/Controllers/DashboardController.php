<?php

use App\Services\AccountServices;

class DashboardController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        // Constructor Args
    }

    public function main()
    {
        $data = Config::get("gondolyn.basic-app-info");

        // $data["back"] = "";
        // $data["user"] = "";

        // // If we are logged in lets get personal
        // if (AccountServices::loggedIn()) {
        //     $data["back"] = " back ";
        //     $data["user"] = Session::get("username");
        // }

        return view('dashboard.main', $data);
    }
}
