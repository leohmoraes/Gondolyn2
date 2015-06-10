<?php namespace App\Services;

use Session;
use Auth;
use Cookie;
use Request;
use Redirect;
use Accounts;
use Config;
use Utilities;
use Lang;
use Crypto;
use Mail;
use Services_Twilio;

class AccountServices
{
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

    public static function login($user)
    {
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

        if ($user->two_factor_enabled && Config::get('gondolyn.two-factor-authentication.enabled')) {
            $code = rand(111111, 999999);
            AccountServices::sendTwoFactorAuthenticationCode($code, $user->two_factor_phone, $username);
            Accounts::setTwoFactorCode($user->id, $code);
        }

        Session::put($sessionData, null);

        return "dashboard";
    }

    public static function authTwoFactors($user)
    {
        Accounts::setTwoFactorCode($user->id, '');
        Session::put('twoFactored', true);

        $duration = Config::get('gondolyn.two-factor-authentication.duration');

        if ($duration === '60days' || $duration === 'lifetime') {
            $minutes = ($duration === 'lifetime') ? 2628000 : 86400;
            Cookie::queue('twoFactored', true, $minutes);
        }

        return true;
    }

    public static function sendTwoFactorAuthenticationCode($code, $phone, $username)
    {
        $AccountSid = Config::get('gondolyn.two-factor-authentication.twilio.account_sid');
        $AuthToken = Config::get('gondolyn.two-factor-authentication.twilio.auth_token');

        if ($AccountSid && $AuthToken) {
            $client = new Services_Twilio($AccountSid, $AuthToken);

            $message = $client->account->messages->create(array(
                "From" => Config::get('gondolyn.two-factor-authentication.twilio.from_number'),
                "To" => $phone,
                "Body" => $code,
            ));

            if ( ! $message->sid) {
                return false;
            }
        }

        return true;
    }

    public static function sendEmailConfirmation($user)
    {
        $email = Crypto::encrypt($user->user_email);

        $data['link'] = url('login/confirm/'.$email);

        Mail::send('emails.confirmation', $data, function ($message) use ($user) {
            $message->to($user->user_email, $user->user_name)->subject('Email Confirmation');
        });
    }

    public static function isLoggedIn()
    {
        if (Session::get('logged_in')) {
            return true;
        }

        return false;
    }

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

    public static function inAppNotification($notification)
    {
        if (Auth::user()->in_app_notifications === 'on') {
            return 'gondolynNotify("'.$notification.'");';
        }

        return '';
    }

    public static function appAuthCode($id)
    {
        $leadingNumber = substr($id, 0, 1);
        return Config::get('gondolyn.authKeys')[$leadingNumber];
    }
}
