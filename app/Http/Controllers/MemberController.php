<?php

class MemberController extends BaseController {

    protected $layout = 'layouts.master';

    public function __construct()
    {
        // Constructor Args
    }

    public function home()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $data['user'] = Session::get("username");
        $data['notification'] = Session::get("notification");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('members.home', $data),
        ];

        return view($this->layout, $layoutData);
    }
}