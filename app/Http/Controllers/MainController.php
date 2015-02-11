<?php

class MainController extends BaseController {

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

        // If we were to be remembered and we're not logged in
        if (Auth::viaRemember() && ! Session::get("logged_in"))
        {
            $email      = Cookie::get("email");
            $password   = Cookie::get("password");

            $Users      = new Users;
            $user       = $Users->login_with_email($email, $password, false);
        }

        // If we are logged in lets get personal
        if (Session::get("logged_in"))
        {
            $data["back"] = " back ";
            $data["user"] = Session::get("username");
        }

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "gondolyn_login"    => View::make('user.login-panel', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('main.welcome', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function changelog()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $data['changes'] = array_reverse(json_decode(file_get_contents("../build.json")));

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "gondolyn_login"    => View::make('user.login-panel', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('main.changelog', $data),
        ];

        return view($this->layout, $layoutData);
    }
}