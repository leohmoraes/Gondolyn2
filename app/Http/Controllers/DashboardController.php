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
        $data['page_title'] = Lang::get('titles.dashboard');
        return view('dashboard.main', $data);
    }
}
