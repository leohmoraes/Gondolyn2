<?php

use App\Services\AccountServices;

class ApiController extends BaseController
{
    public function __construct()
    {
        $this->middleware('cors');
    }

    /*
    |--------------------------------------------------------------------------
    | Login / Logout
    |--------------------------------------------------------------------------
    */

    public function request()
    {
        try {
            if ( ! Utilities::jsonInput("email") || ! Utilities::jsonInput("password")) {
                return Gondolyn::response("error", Lang::get("notification.login.fail"));
            }

            $Login = new Accounts;
            $user = $Login->loginWithEmail(Utilities::jsonInput("email"), Utilities::jsonInput("password"), Utilities::jsonInput("remember"));

            if ( ! $user) {
                return Gondolyn::response("error", Lang::get("notification.login.fail"));
            } elseif ($user) {
                AccountServices::login($user);
                return Gondolyn::response("success", $user->user_api_token);
            } else {
                return Gondolyn::response("success", Lang::get("notification.login.success"));
            }
        } catch (Exception $e) {
            return Gondolyn::response("error", $e->getMessage());
        }
    }

    public function logout()
    {
        if ( ! Session::get("logged_in")) {
            return Gondolyn::response("error", Lang::get("notification.api.not_logged_in"));
        }

        Session::flush();

        return Gondolyn::response("success", Lang::get("notification.api.logout"));
    }

    /*
    |--------------------------------------------------------------------------
    | User Actions
    |--------------------------------------------------------------------------
    */

    public function getUserData()
    {
        if ( ! Session::get("logged_in")) {
            return Gondolyn::response("error", Lang::get("notification.api.not_logged_in"));
        }

        $data = array(
            "logged_in" => Session::get("logged_in"),
            "id" => Session::get("id"),
            "role" => Session::get("role"),
            "token" => Session::get("token"),
            "plan" => Session::get("plan"),
            "subscribed" => Session::get("subscribed"),
            "last_activity" => Session::get("last_activity"),
            "username" => Session::get("username"),
            "email" => Session::get("email")
        );

        return Gondolyn::response("success", $data);
    }

}
