<?php

use App\Services\AccountServices;

class TwoFactorController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        $this->middleware('security.guard');
    }

    public function twoFactor()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.two-factor');

        $user = Accounts::getAccount(Session::get("id"));

        Log::info($user->two_factor_code);

        return view('account.two-factor', $data);
    }

    public function twoFactorAuthenticate()
    {
        $user = Auth::user();

        if ($user->two_factor_code === Request::input('code')) {
            AccountServices::authTwoFactors($user);
        } else {
            return redirect('account/two-factor')->with('bad-code', true);
        }

        return redirect('dashboard');
    }
}
