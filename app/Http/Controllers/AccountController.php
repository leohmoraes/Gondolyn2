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
        if (AccountServices::tooManyFailedLogins()) {
            Gondolyn::notification(Lang::get('notification.login.exceeded-login-attempts'), 'danger');
            return redirect('errors/general');
        }

        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.login');

        Gondolyn::notification(Validation::errors('string'), 'danger');

        return view('account.login', $data);
    }

    public function loginConfirmEmail()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.login');

        Gondolyn::notification(Validation::errors('string'), 'danger');

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

        $gravatarHash = md5(strtolower(trim($user->user_email)));
        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);

        $data['profileImage']       = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';
        $data['user']               = $user;
        $data['inAppNotifications'] = ($user->in_app_notifications === 'on') ? 'checked' : '';
        $data['options']            = Config::get("permissions.matrix.roles");
        $data['billingColumns']    = Config::get('forms.billing');

        return view('account.settings', $data);
    }

    public function password()
    {
        $data = Config::get("gondolyn.appInfo");
        $data['page_title'] = Lang::get('titles.change-password');

        $user = Accounts::getAccount(Session::get("id"));

        $gravatarHash = md5(strtolower(trim($user->user_email)));
        $profileImage = ($user->profile == "") ? null : Utilities::fileAsPublicAsset($user->profile);

        $data['profileImage'] = $profileImage ?: 'http://www.gravatar.com/avatar/'.$gravatarHash.'?s=300';
        $data['user'] = $user;
        $data['roles'] = Config::get("permissions.matrix.roles");

        return view('account.password', $data);
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
        // Validation
        $validation = Validation::check('conditions.update_account');

        // Validation errors
        if ($validation['errors']) {
            Gondolyn::notification(Lang::get("notification.profile.update_failed"), 'danger');
            return $validation['redirect'];
        } else if ( ! $validation['errors']) {
            try {
                $status = Accounts::updateAccount(Session::get("id"));
                if ($status) {
                    Gondolyn::notification(Lang::get("notification.profile.update_success"), 'success');
                }
            } catch (Exception $e) {
                Gondolyn::notification($e->getMessage(), 'danger');
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
                Gondolyn::notification(Lang::get("notification.profile.password_success"), 'success');
            } else {
                Gondolyn::notification(Lang::get("notification.profile.password_failed"), 'danger');
            }
        } catch (Exception $e) {
            Gondolyn::notification(Lang::get("notification.general.error"), 'danger');
            return redirect('errors/general');
        }

        return redirect('account/settings');
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
                Gondolyn::notification(Lang::get("notification.login.fail"), 'danger');
                AccountServices::addFailedAttempt();
                return redirect('errors/general');
            } else {
                if (Config::get('autoConfirmEmail') && $user->user_active !== 'active') {
                    AccountServices::sendEmailConfirmation($user);
                    return redirect('login/confirm-email');
                } else {
                    $redirect = AccountServices::login($user);
                    return redirect($redirect);
                }
            }

        } catch (Exception $e) {
            Gondolyn::notification($e->getMessage());
            return redirect('errors/general');
        }
    }

    public function confirmEmail($email)
    {
        $user = Accounts::getAccountByEmail(Crypto::decrypt($email));
        $result = Accounts::modifyAccountStatus($user->id, 'active');

        if ($user && $result) {
            Gondolyn::notification(Lang::get("notification.login.confirm"), 'success');
        } else {
            Gondolyn::notification(Lang::get("notification.login.not-confirmed"), 'danger');
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

                if ( ! $user) {
                    Gondolyn::notification(Lang::get("notification.login.fail"), 'danger');
                    return redirect('errors/general');
                } else {
                    $redirect = AccountServices::login($user);
                    return redirect($redirect);
                }
            } catch (Exception $e) {
                Gondolyn::notification($e->getMessage(), 'danger');
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
                Gondolyn::notification($e->getMessage(), 'danger');
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

        $user->deleteAccount($id);

        Session::flush();

        Gondolyn::notification(Lang::get("notification.login.deleted"), 'success');

        return redirect("/");
    }

    public function generateNewPassword()
    {
        $data = array();

        try {
            $user = Accounts::getAccountByEmail(Input::get("email"));
            $newPassword = Accounts::generateNewPassword($user->id);
        } catch (Exception $e) {
            Gondolyn::notification(Lang::get("notification.general.cannot_find_user"), 'danger');
            return redirect('errors/general');
        }

        if ($newPassword) {
            $data['newPassword'] = $newPassword;

            Mail::send('emails.new-password', $data, function($message) {
                $user = Accounts::getAccountByEmail(Input::get("email"));
                $message->to($user->user_email, $user->user_name)->subject('New Password!');
            });

            Gondolyn::notification(Lang::get("notification.general.new_password"), 'success');
        } else {
            Gondolyn::notification(Lang::get("notification.general.failed_new_password"), 'danger');
        }

        return redirect('');
    }
}
