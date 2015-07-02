<?php

use App\Services\AccountServices;

class DashboardController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        $this->middleware('security.guard');
    }

    public function main()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.dashboard');
        return view('dashboard.main', $data);
    }
}
