<?php

use App\Services\AccountServices;

class AccountController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        $this->middleware('security.guard');
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.login');

        Session::flash('notification', Validation::errors('string') ?: false);

        return view('account.login', $data);
    }

    public function loginConfirmEmail()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.login');

        Session::flash('notification', Validation::errors('string') ?: false);

        return view('account.login-confirm-email', $data);
    }

    public function loginTwitterVerified()
    {
        $Login = new Accounts;
        $user = $Login->verifyAccountEmail(Input::get('email'), "twitter");

        $redirect = AccountServices::login($user);
        return redirect($redirect);
    }

    /*
    |--------------------------------------------------------------------------
    | Account
    |--------------------------------------------------------------------------
    */

    public function settings()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.settings');

        $users = new Accounts;
        $user = $users->getAccount(Session::get("id"));

        $gravatarHash = md5( strtolower( trim( $user->user_email ) ) );
        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);
        $data['profileImage']       = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';

        $data['user']               = $user;
        $data['inAppNotifications'] = ($user->in_app_notifications === 'on') ? 'checked' : '';
        $data['options']            = Config::get("permissions.matrix.roles");
        $data['shippingColumns']    = Config::get('forms.shipping');

        return view('account.settings', $data);
    }

    public function password()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.change-password');

        $user = Accounts::getAccount(Session::get("id"));

        $gravatarHash = md5( strtolower( trim( $user->user_email ) ) );
        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);
        $data['profileImage']       = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';

        $data['user'] = $user;
        $data['roles'] = Config::get("permissions.matrix.roles");

        return view('account.password', $data);
    }

    public function subscription()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.subscription');

        $user = Accounts::getAccount(Session::get("id"));

        $gravatarHash = md5( strtolower( trim( $user->user_email ) ) );
        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);
        $data['profileImage']       = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';

        $data['user'] = $user;
        $data['packages'] = Config::get("gondolyn.packages");

        if ($data['user']->subscribed()) {
            $view = view('account.subscription-change', $data);
        } else {
            $view = view('account.subscription-set', $data);
        }
        return $view;
    }

    public function subscriptionInvoices()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.subscription-invoices');

        $user = Accounts::getAccount(Session::get("id"));
        $gravatarHash = md5( strtolower( trim( $user->user_email ) ) );
        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);
        $data['profileImage']       = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';

        $invoices = $user->invoices();

        $data['invoices'] = View::make('account.invoice', array('invoices' => $invoices));

        return view('account.subscription-invoices', $data);
    }

    public function twoFactor()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.two-factor');

        $user = Accounts::getAccount(Session::get("id"));

        Log::info($user->two_factor_code);

        return view('account.two-factor', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | Account Recovery
    |--------------------------------------------------------------------------
    */

    public function forgotPassword()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.forgot-password');
        return view('account.forgot-password', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    public function update()
    {
        $validation = Validation::check(Accounts::$rules);
        Session::flash("notification", Lang::get("notification.profile.update_failed"));

        if ( ! $validation['errors']) {
            try {
                $status = Accounts::updateAccount(Session::get("id"));
                if ($status) {
                    Session::flash("notification", Lang::get("notification.profile.update_success"));
                }
            } catch (Exception $e) {
                Session::flash("notification", $e->getMessage());
            }

            return redirect('account/settings');
        }

        return $validation['redirect'];
    }

    public function updatePassword()
    {
        try {
            $users = new Accounts;
            $status = $users->updateMyPassword(Session::get("id"));

            if ($status) {
                Session::flash("notification", Lang::get("notification.profile.password_success"));
            } else {
                Session::flash("notification", Lang::get("notification.profile.password_failed"));
            }
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.error"));
            return redirect('errors/general');
        }

        return redirect('account/settings');
    }

    public function setSubscription()
    {
        try {
            $users = new Accounts;
            $status = $users->setAccountSubscription(Session::get("id"), Input::get("plan"));

            if ($status) {
                Session::flash("notification", Lang::get("notification.subscription.success"));
            } else {
                Session::flash("notification", Lang::get("notification.subscription.failed"));
            }
        } catch (Exception $e) {
            Session::flash("notification", $e->getMessage());
            return redirect('errors/general');
        }

        return redirect('account/settings');
    }

    public function updateSubscription()
    {
        try {
            $users = new Accounts;
            $status = $users->updateAccountSubscription(Session::get("id"), Input::get("plan"));

            if ($status) {
                Session::flash("notification", Lang::get("notification.subscription.success"));
            } else {
                Session::flash("notification", Lang::get("notification.subscription.failed"));
            }
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.error"));
            return redirect('errors/general');
        }

        return redirect('account/settings');
    }

    public function cancelSubscription()
    {
        try {
            $users = new Accounts;
            $status = $users->cancelSubscription(Session::get("id"));

            if ($status) {
                Session::flash("notification", Lang::get("notification.subscription.cancel_success"));
            } else {
                Session::flash("notification", Lang::get("notification.subscription.cancel_failed"));
            }
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.error"));
            return redirect('errors/general');
        }

        return redirect('account/settings');
    }

    public function downloadInvoice($id)
    {
        $user = Accounts::getAccount(Session::get("id"));

        $invoice = Crypto::decrypt($id);
        $plan = Config::get('gondolyn.packages.'.Session::get('plan'));

        return $user->downloadInvoice($invoice, [
            'vendor'    => Config::get("gondolyn.company"),
            'street'    => Config::get("gondolyn.street"),
            'location'  => Config::get("gondolyn.location"),
            'phone'     => Config::get("gondolyn.phone"),
            'url'       => Config::get("gondolyn.url"),
            'product'   => Config::get("gondolyn.product"),
            'description'   => 'Subscription ('.$plan['name'].')',
        ]);
    }

    public function withEmail()
    {
        // Validation
        $validation = Validation::check('conditions.login_email');

        // Validation errors
        if ($validation['errors']) {
            return $validation['redirect'];
        }

        try {
            $Users = new Accounts;
            $user = $Users->loginWithEmail(Input::get('email'), Input::get('password'), Input::get('remember_me'));

            if (Config::get('autoConfirmEmail') && $user->user_active !== 'active') {
                AccountServices::sendEmailConfirmation($user);
                return redirect('login/confirm-email');
            } else {
                if ( ! $user) {
                    Session::flash("notification", Lang::get("notification.login.fail"));
                    return redirect('errors/general');
                } else {
                    $redirect = AccountServices::login($user);
                    return redirect($redirect);
                }
            }

            Session::flash("notification", Lang::get("notification.login.success"));
            return redirect('errors/general');

        } catch (Exception $e) {
            Session::flash("notification", $e->getMessage());
            return redirect('errors/general');
        }
    }

    public function confirmEmail($email)
    {
        $user = Accounts::getAccountByEmail(Crypto::decrypt($email));
        $result = Accounts::modifyAccountStatus($user->id, 'active');

        if ($user && $result) {
            Session::flash("notification", Lang::get("notification.login.confirm"));
        } else {
            Session::flash("notification", Lang::get("notification.login.not-confirmed"));
        }

        return redirect('login/email');
    }

    public function withFacebook()
    {
        $code = Input::get('code');

        if ( ! empty($code)) {
            try {
                $result = Socialize::with('facebook')->user();

                $Login = new Accounts;
                $user = $Login->loginWithSocialMedia($result, "facebook");

                $redirect = AccountServices::login($user);
                return redirect($redirect);
            } catch (Exception $e) {
                Session::flash("notification", $e->getMessage());
                return redirect('errors/general');
            }
        } else {
            return Socialize::with('facebook')->redirect();
        }
    }

    public function withTwitter()
    {
        $oauth_verifier = Input::get('oauth_verifier');

        if ( ! empty($oauth_verifier)) {
            try {
                $result = Socialize::with('twitter')->user();

                $Login = new Accounts;
                $user = $Login->loginWithSocialMedia($result, "twitter");

                if ( ! is_object($user)) {
                    Session::put("twitterID", $result->id);
                    Session::put("twitterScreenName", $result->nickname);

                    return redirect("login/twitter/verify/");
                }

                $redirect = AccountServices::login($user);
                return redirect($redirect);
            } catch (Exception $e) {
                Session::flash("notification", $e->getMessage());
                return redirect('errors/general');
            }
        } else {
            return Socialize::with('twitter')->redirect();
        }
    }

    public function loginTwitterVerify()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.login-twitter');

        return view('account.twitter-verify', $data);
    }

    public function logout()
    {
        return AccountServices::logout();
    }

    public function deleteAccount()
    {
        $id = Session::get("id");

        $user = new Accounts;

        $user->deleteMyAccount($id);

        Session::flush();

        Session::flash("notification", Lang::get("notification.login.deleted"));

        return redirect("/");
    }

    public function generateNewPassword()
    {
        $data = array();

        try {
            $user = Accounts::getAccountByEmail(Input::get("email"));
            $newPassword = Accounts::generateNewPassword($user->id);
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.cannot_find_user"));
            return redirect('errors/general');
        }

        if ($newPassword) {
            $data['newPassword'] = $newPassword;

            Mail::send('emails.newpassword', $data, function ($message) {
                $user = Accounts::getAccountByEmail(Input::get("email"));
                $message->to($user->user_email, $user->user_name)->subject('New Password!');
            });

            Session::flash("notification", Lang::get("notification.general.new_password"));
        } else {
            Session::flash("notification", Lang::get("notification.general.failed_new_password"));
        }

        return redirect('');
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
