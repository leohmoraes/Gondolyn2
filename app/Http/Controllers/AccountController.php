<?php

use App\Services\AccountServices;

class AccountController extends BaseController
{
    protected $layout = 'layouts.master';

    /*
    |--------------------------------------------------------------------------
    | Account Recovery
    |--------------------------------------------------------------------------
    */

    public function forgotPassword()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('account.forgot-password', $data),
        ];

        return view($this->layout, $layoutData);
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

    /*
    |--------------------------------------------------------------------------
    | Account Updates
    |--------------------------------------------------------------------------
    */

    public function settings()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $users = new Accounts;
        $user = $users->getAccount(Session::get("id"));

        $gravatarHash = md5( strtolower( trim( $user->user_email ) ) );

        $data['user']               = $user;
        $data['gravatar']           = $gravatarHash;
        $data['options']            = Config::get("permissions.matrix.roles");
        $data['shippingColumns']    = Config::get('forms.shipping');

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "selectRole"        => View::make('account.selectRole', $data),
            "content"           => View::make('account.settings', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function password()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $data['user'] = Accounts::getAccount(Session::get("id"));
        $data['roles'] = Config::get("permissions.matrix.roles");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('account.password', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function subscription()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $data['user'] = Accounts::getAccount(Session::get("id"));
        $data['packages'] = Config::get("gondolyn.packages");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "selectPlan"        => View::make('account.selectPlan', $data),
        ];

        if ($data['user']->subscribed() && ! $data['user']->cancelled()) {
            $layoutData["content"] = View::make('account.subscription_change', $data);
        } else {
            $layoutData["content"] = View::make('account.subscription_set', $data);
        }

        return view($this->layout, $layoutData);
    }

    public function subscriptionInvoices()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $user = Accounts::getAccount(Session::get("id"));

        $data['invoices'] = '';

        $invoices = $user->invoices();

        foreach ($invoices as $invoice) {
            $data['invoices'] .= View::make('account.invoice', array('invoice' => $invoice));
        }

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
        ];

        $layoutData["content"] = View::make('account.subscription_invoices', $data);

        return view($this->layout, $layoutData);
    }

    public function downloadInvoice($id)
    {
        $user = Accounts::getAccount(Session::get("id"));

        $invoice = Crypto::decrypt($id);

        return $user->downloadInvoice($invoice, [
            'vendor'  => Config::get("gondolyn.company"),
            'product' => Config::get("gondolyn.product"),
        ]);
    }

    public function update()
    {
        try {
            $status = Accounts::updateAccount(Session::get("id"));
            if ($status) {
                Session::flash("notification", Lang::get("notification.profile.update_success"));
            } else {
                Session::flash("notification", Lang::get("notification.profile.update_failed"));
            }
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.error"));
            return redirect('errors/general');
        }

        return redirect('account/settings');
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

    /*
    |--------------------------------------------------------------------------
    | Login / Logout
    |--------------------------------------------------------------------------
    */

    public function login()
    {
        $data = Config::get("gondolyn.basic-app-info");

        Session::flash('notification', Validation::errors('string') ?: false);

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "gondolyn_login"    => View::make('account.login-panel', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('account.login', $data),
        ];

        return view($this->layout, $layoutData);
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

            if ( ! $user) {
                Session::flash("notification", Lang::get("notification.login.fail"));
                return redirect('errors/general');
            } else {
                $redirect = AccountServices::login($user);
                return redirect($redirect);
            }

            Session::flash("notification", Lang::get("notification.login.success"));
            return redirect('errors/general');

        } catch (Exception $e) {
            Session::flash("notification", $e->getMessage());
            return redirect('errors/general');
        }
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
        $data = Config::get("gondolyn.basic-app-info");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('account.twitter-verify', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function loginTwitterVerified()
    {
        $Login = new Accounts;
        $user = $Login->verifyAccountEmail(Input::get('email'), "twitter");

        $redirect = AccountServices::login($user);
        return redirect($redirect);
    }

    public function logout()
    {
        AccountServices::logout();
        return redirect("/");
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
}
