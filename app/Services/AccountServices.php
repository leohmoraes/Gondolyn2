<?php namespace App\Services;

use Session;
use Auth;
use Cookie;
use Request;
use Accounts;
use Config;
use Crypto;
use Mail;
use Services_Twilio;

class AccountServices
{
    /**
     * Logout the user
     * @return Redirect
     */
    public static function logout()
    {
        // Kill the session
        Session::flush();

        // Kill the auth
        Auth::logout();

        return redirect('/')
            ->withCookie(Cookie::forget('email'))
            ->withCookie(Cookie::forget('password'));
    }

    /**
     * Login a user
     * @param  object|bool $user User object | false
     * @return string
     */
    public static function login($user)
    {
        if ( ! $user) {
            return 'errors/general';
        }

        Auth::login($user);

        $username = ($user->user_name == "") ? $user->user_email : $user->user_name;

        $sessionData = array(
            "logged_in" => TRUE,
            "role" => $user->user_role,
            "username" => $username,
            "email" => $user->user_email,
            "token" => $user->user_api_token,
            "subscribed" => $user->subscribed(),
            "plan" => $user->stripe_plan,
            "last_activity" => time(),
            "id" => $user->id
        );

        if ($user->two_factor_enabled && Config::get('gondolyn.twoFactorAuthentication.enabled')) {
            $code = rand(111111, 999999);
            AccountServices::sendTwoFactorAuthenticationCode($code, $user->two_factor_phone);
            Accounts::setTwoFactorCode($user->id, $code);
        }

        Session::put($sessionData, null);

        return "dashboard";
    }

    /**
     * Two factor auth
     * @param  object $user User object
     * @return bool
     */
    public static function authTwoFactors($user)
    {
        Accounts::setTwoFactorCode($user->id, '');
        Session::put('twoFactored', true);

        $duration = Config::get('gondolyn.twoFactorAuthentication.duration');

        if ($duration === '60days' || $duration === 'lifetime') {
            $minutes = ($duration === 'lifetime') ? 2628000 : 86400;
            Cookie::queue('twoFactored', true, $minutes);
        }

        return true;
    }

    /**
     * Send two factor Auth code
     * @param  string $code  Auth code
     * @param  string $phone Phone number
     * @return bool
     */
    public static function sendTwoFactorAuthenticationCode($code, $phone)
    {
        $AccountSid = Config::get('gondolyn.twoFactorAuthentication.twilio.account_sid');
        $AuthToken = Config::get('gondolyn.twoFactorAuthentication.twilio.auth_token');

        if ($AccountSid && $AuthToken) {
            $client = new Services_Twilio($AccountSid, $AuthToken);

            $message = $client->account->messages->create(array(
                "From" => Config::get('gondolyn.twoFactorAuthentication.twilio.from_number'),
                "To" => $phone,
                "Body" => $code,
            ));

            if ( ! $message->sid) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send email confirmation
     * @param  object $user User object
     * @return void
     */
    public static function sendEmailConfirmation($user)
    {
        $email = Crypto::encrypt($user->user_email);

        $data = [];
        $data['link'] = url('login/confirm/'.$email);

        Mail::send('emails.confirmation', $data, function ($message) use ($user) {
            $message->to($user->user_email, $user->user_name)->subject('Email Confirmation');
        });
    }

    /**
     * Check if User is logged in
     * @return boolean
     */
    public static function isLoggedIn()
    {
        if (Session::get('logged_in')) {
            return true;
        }

        return false;
    }

    /**
     * Check if account is remembered
     * @return boolean
     */
    public static function isAccountRemembered()
    {
        $email      = Request::cookie("email");
        $password   = Request::cookie("password");

        if ( ! AccountServices::isLoggedIn() && $email && $password) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if User wants in app notification
     * @param  string $notification     Notification string
     * @param  string $notificationType Notification type
     * @return string
     */
    public static function inAppNotification($notification, $notificationType)
    {
        if (Auth::user()->in_app_notifications === 'on') {
            return 'gondolynNotify("'.$notification.'", "'.$notificationType.'");';
        }

        return '';
    }

    /**
     * Get user App Auth Code
     * @param  integer $id User ID
     * @return string     Auth code
     */
    public static function appAuthCode($id)
    {
        $leadingNumber = substr($id, 0, 1);
        return Config::get('gondolyn.authKeys')[$leadingNumber];
    }

    /**
     * Adds failed attempts with the login action
     *
     * @return  void
     */
    public static function addFailedAttempt()
    {
        if (Config::get('gondolyn.failedLogins')) {
            $failedAttempts = Cookie::get('failed_attempts');

            if (is_null($failedAttempts)) {
                $failedAttempts = 0;
            } else {
                $failedAttempts++;
            }

            Cookie::queue('failed_attempts', $failedAttempts, 60);
        }
    }

    /**
     * Checks if there have been too many failed
     * login attempts.
     *
     * @return  bool
     */
    public static function tooManyFailedLogins()
    {
        $failedAttempts = Cookie::get('failed_attempts');

        if (Config::get('gondolyn.failedLogins') && ($failedAttempts >= Config::get('gondolyn.failedLoginsLimit'))) {
            return true;
        }

        return false;
    }
}
