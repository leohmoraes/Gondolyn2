<?php namespace App\Services;

use Session, Auth, Cookie, Request, Redirect, Accounts;

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

        Session::put($sessionData, null);

        return "dashboard";
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
}
