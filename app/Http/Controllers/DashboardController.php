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
        return view('dashboard.main', $data);
    }
}
