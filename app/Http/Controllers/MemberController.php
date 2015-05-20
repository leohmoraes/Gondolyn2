<?php

class MemberController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $data['user'] = Session::get("username");
        $data['notification'] = Session::get("notification");

        return view('members.home', $data);
    }
}
