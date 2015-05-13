<?php namespace App\Helpers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;

class Gondolyn
{
    public static function response($type, $message)
    {
        return Response::json(array("status" => $type, "data" => $message));
    }

    public static function valid_api_key()
    {
        $headers = getallheaders();

        $auth = @$headers['Authorization'];

        $keys = Config::get('gondolyn.authKeys');

        if ( ! in_array($auth, $keys)) return false;

        Session::put('authKey', $auth);

        return true;
    }

    public static function valid_api_token()
    {
        $headers = getallheaders();

        $requestToken = @$headers['Token'];

        $userToken = Session::get('token');

        if ($requestToken === $userToken) return true;

        return false;
    }

    public static function is_api_call()
    {
        if (php_sapi_name() !== "cli" && stristr(Request::path(), 'api')) {
            return true;
        }
        return false;
    }

    public static function is_ajax_call()
    {
        if ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }

        return false;
    }

    public static function version()
    {
        if (php_sapi_name() !== "cli") {
            $changelog = json_decode(file_get_contents("../build.json"));
            return $changelog[count($changelog) - 1]->version;
        } else {
            return 'cli';
        }
    }

}
