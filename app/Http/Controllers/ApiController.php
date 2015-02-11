<?php

class ApiController extends BaseController {

    public function __construct()
    {
        if (Session::get('logged_in'))
        {
            if (Tools::get_header("Token") != Session::get('token')) return Gondolyn::response("error", Lang::get("notification.api.bad_token"));
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Login / Logout
    |--------------------------------------------------------------------------
    */

    public function request()
    {
        try
        {
            $Login = new Users;
            $user = $Login->login_with_email(Tools::raw_json_input("email"), Tools::raw_json_input("password"), Tools::raw_json_input("remember"));

            if ( ! $user)
            {
                return Gondolyn::response("success", Lang::get("notification.login.failed"));
            }
            else
            {
                return $this->process($user);
            }

            return Gondolyn::response("success", Lang::get("notification.login.success"));

        }
        catch (Exception $e)
        {
            return Gondolyn::response("error", $e->getMessage());
        }
    }

    public function getUserData()
    {
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

    public function logout()
    {
        Session::flush();
        return Gondolyn::response("success", Lang::get("notification.api.logout"));
    }

    private function process($user)
    {
        $username = ($user->user_name == "") ? $user->user_email : $user->user_name;

        $sessionData = array(
            "logged_in" => TRUE,
            "role" => $user->user_role,
            "username" => $username,
            "subscribed" => $user->subscribed(),
            "token" => $user->user_api_token,
            "plan" => $user->stripe_plan,
            "email" => $user->user_email,
            "last_activity" => time(),
            "id" => $user->id
        );

        Session::put($sessionData, null);

        return Gondolyn::response("success", $user->user_api_token);
    }

}