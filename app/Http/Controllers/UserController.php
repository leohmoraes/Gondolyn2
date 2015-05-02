<?php

class UserController extends BaseController
{
    protected $layout = 'layouts.master';

    public function __construct()
    {
        // Constructor Args
    }

    /*
    |--------------------------------------------------------------------------
    | Home
    |--------------------------------------------------------------------------
    */

    public function home()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $user = Session::get("username");

        $data['message'] = AppPrototype::welcomeMessage($user);

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('user.home', $data),
        ];

        return view($this->layout, $layoutData);
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Recovery
    |--------------------------------------------------------------------------
    */

    public function forgotPassword()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('user.forgot-password', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function generateNewPassword()
    {
        try {
            $user = Users::getMyProfileByEmail(Input::get("email"));
            $newPassword = Users::generateNewPassword($user->id);
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.cannot_find_user"));
            return redirect('errors/general');
        }

        if ($newPassword) {
            $data['newPassword'] = $newPassword;

            Mail::send('emails.newpassword', $data, function ($message) {
                $user = Users::getMyProfileByEmail(Input::get("email"));
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
    | Profile Updates
    |--------------------------------------------------------------------------
    */

    public function settings()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $users = new Users;
        $user = $users->getMyProfile(Session::get("id"));

        $gravatarHash = md5( strtolower( trim( $user->user_email ) ) );

        $data['user']           = $user;
        $data['gravatar']       = $gravatarHash;
        $data['options']        = Config::get("permissions.matrix.roles");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "selectRole"        => View::make('user.selectRole', $data),
            "content"           => View::make('user.settings', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function password()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $data['user'] = Users::getMyProfile(Session::get("id"));
        $data['roles'] = Config::get("permissions.matrix.roles");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('user.password', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function subscription()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $data['user'] = Users::getMyProfile(Session::get("id"));
        $data['packages'] = Config::get("gondolyn.packages");

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
            "selectPlan"        => View::make('user.selectPlan', $data),
        ];

        if ($data['user']->subscribed() && ! $data['user']->cancelled()) {
            $layoutData["content"] = View::make('user.subscription_change', $data);
        } else {
            $layoutData["content"] = View::make('user.subscription_set', $data);
        }

        return view($this->layout, $layoutData);
    }

    public function subscriptionInvoices()
    {
        $data = Config::get("gondolyn.basic-app-info");

        $user = Users::getMyProfile(Session::get("id"));

        $data['invoices'] = '';

        $invoices = $user->invoices();

        foreach ($invoices as $invoice) {
            $data['invoices'] .= View::make('user.invoice', array('invoice' => $invoice));
        }

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "nav_bar"           => View::make('navbar', $data),
        ];

        $layoutData["content"] = View::make('user.subscription_invoices', $data);

        return view($this->layout, $layoutData);
    }

    public function downloadInvoice($id)
    {
        $user = Users::getMyProfile(Session::get("id"));

        $invoice = Crypto::decrypt($id);

        return $user->downloadInvoice($invoice, [
            'vendor'  => Config::get("gondolyn.company"),
            'product' => Config::get("gondolyn.product"),
        ]);
    }

    public function update()
    {
        try {
            $status = Users::updateProfile(Session::get("id"));

            if ($status) Session::flash("notification", Lang::get("notification.profile.update_success"));
            else Session::flash("notification", Lang::get("notification.profile.update_failed"));
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.error"));
            return redirect('errors/general');
        }

        return redirect('user/settings');
    }

    public function updatePassword()
    {
        try {
            $users = new Users;
            $status = $users->updateMyPassword(Session::get("id"));

            if ($status) Session::flash("notification", Lang::get("notification.profile.password_success"));
            else Session::flash("notification", Lang::get("notification.profile.password_failed"));
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.error"));
            return redirect('errors/general');
        }

        return redirect('user/settings');
    }

    public function setSubscription()
    {
        try {
            $users = new Users;
            $status = $users->setMySubscription(Session::get("id"), Input::get("plan"));

            if ($status) Session::flash("notification", Lang::get("notification.subscription.success"));
            else Session::flash("notification", Lang::get("notification.subscription.failed"));
        } catch (Exception $e) {
            Session::flash("notification", $e->getMessage());
            return redirect('errors/general');
        }

        return redirect('user/settings');
    }

    public function updateSubscription()
    {
        try {
            $users = new Users;
            $status = $users->updateMySubscription(Session::get("id"), Input::get("plan"));

            if ($status) Session::flash("notification", Lang::get("notification.subscription.success"));
            else Session::flash("notification", Lang::get("notification.subscription.failed"));
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.error"));
            return redirect('errors/general');
        }

        return redirect('user/settings');
    }

    public function cancelSubscription()
    {
        try {
            $users = new Users;
            $status = $users->cancelSubscription(Session::get("id"));

            if ($status) Session::flash("notification", Lang::get("notification.subscription.cancel_success"));
            else Session::flash("notification", Lang::get("notification.subscription.cancel_failed"));
        } catch (Exception $e) {
            Session::flash("notification", Lang::get("notification.general.error"));
            return redirect('errors/general');
        }

        return redirect('user/settings');
    }

    /*
    |--------------------------------------------------------------------------
    | Login / Logout
    |--------------------------------------------------------------------------
    */

    public function login()
    {
        $data = Config::get("gondolyn.basic-app-info");

        Session::flash('notification', Validation::errors() ?: false);

        $layoutData = [
            "metadata"          => View::make('metadata', $data),
            "general"           => View::make('common', $data),
            "gondolyn_login"    => View::make('user.login-panel', $data),
            "nav_bar"           => View::make('navbar', $data),
            "content"           => View::make('user.login', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function withEmail()
    {
        // Validation
        $validation = Validation::check('login/email');

        // Validation errors
        if (isset($validation['errors'])) return $validation['redirect'];

        try {
            $Users = new Users;
            $user = $Users->login_with_email(Input::get('email'), Input::get('password'), Input::get('remember_me'));

            if ( ! $user) {
                Session::flash("notification", Lang::get("notification.login.fail"));
                return redirect('errors/general');
            } else {
                return $this->process($user);
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

                $Login = new Users;
                $user = $Login->login_with_other_account($result, "facebook");

                return $this->process($user);
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

                $Login = new Users;
                $user = $Login->login_with_other_account($result, "twitter");

                if ( ! is_object($user)) {
                    Session::put("twitterID", $result->id);
                    Session::put("twitterScreenName", $result->nickname);

                    return redirect("login/twitter/verify/");
                }

                return $this->process($user);
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
            "content"           => View::make('user.twitter-verify', $data),
        ];

        return view($this->layout, $layoutData);
    }

    public function loginTwitterVerified()
    {
        $Login = new Users;
        $user = $Login->verify_user_email(Input::get('email'), "twitter");

        return $this->process($user);
    }

    public function logout()
    {
        // Kill the session
        Session::flush();

        // Kill the auth
        Auth::logout();

        // Drop the remember details
        Cookie::forget('email');
        Cookie::forget('password');

        return redirect("/");
    }

    public function deleteUserAccount()
    {
        $id = Session::get("id");

        $user = new Users;

        $user->deleteMyAccount($id);

        Session::flush();

        Session::flash("notification", Lang::get("notification.login.deleted"));

        return redirect("/");
    }

    /*
    |--------------------------------------------------------------------------
    | Private Methods
    |--------------------------------------------------------------------------
    */

    private function process($user)
    {
        $username = ($user->user_name == "") ? $user->user_email : $user->user_name;

        $sessionData = array(
            "logged_in" => TRUE,
            "role" => $user->user_role,
            "username" => $username,
            "email" => $user->user_email,
            "subscribed" => $user->subscribed(),
            "plan" => $user->stripe_plan,
            "last_activity" => time(),
            "id" => $user->id
        );

        Session::put($sessionData, null);

        return redirect($user->user_role."/home");
    }

}
