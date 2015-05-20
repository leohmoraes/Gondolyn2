<?php namespace App\Services;

use Session, Auth, Cookie, Request;

class AccountServices
{
    public static function logout()
    {
        // Kill the session
        Session::flush();

        // Kill the auth
        Auth::logout();

        // Drop the remember details
        Cookie::forget('email');
        Cookie::forget('password');
    }

    public static function login($user)
    {
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

        return $user->user_role."/home";
    }

    public static function loggedIn()
    {
        if (Session::get('logged_in')) {
            return true;
        }

        return false;
    }

    public static function isAccountRemembered()
    {
        if (Auth::viaRemember() && ! Session::get("logged_in")) {
            $email      = Request::cookie("email");
            $password   = Request::cookie("password");

            $Users      = new Accounts;
            $Users->loginWithEmail($email, $password, false);
        }
    }
}
