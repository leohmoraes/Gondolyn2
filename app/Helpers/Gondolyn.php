<?php namespace App\Helpers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;

class Gondolyn
{
    /**
     * Generate a response
     * @param  string $type    Response type
     * @param  string $message Response string
     * @return Response
     */
    public static function response($type, $message)
    {
        return Response::json(array("status" => $type, "data" => $message));
    }

    /**
     * Tests if the API key is valid
     * @return bool
     */
    public static function valid_api_key()
    {
        $headers = getallheaders();

        $auth = @$headers['Authorization'];

        $keys = Config::get('gondolyn.authKeys');

        if ( ! in_array($auth, $keys)) {
            return false;
        }

        Session::put('authKey', $auth);

        return true;
    }

    /**
     * Tests if API token is valid
     * @return bool
     */
    public static function valid_api_token()
    {
        $headers = getallheaders();

        $requestToken = @$headers['Token'];

        $userToken = Session::get('token');

        if ($requestToken === $userToken) {
            return true;
        }

        return false;
    }

    /**
     * Checks if Request is API call
     * @param  Request  $request Request object
     * @return boolean
     */
    public static function is_api_call($request)
    {
        if (php_sapi_name() !== "cli" && stristr($request->path(), 'api')) {
            return true;
        }
        return false;
    }

    /**
     * Tests if AJAX call
     * @return boolean
     */
    public static function is_ajax_call()
    {
        if ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }

        return false;
    }

    /**
     * Gets app build version
     * @return string
     */
    public static function version()
    {
        if (php_sapi_name() !== "cli") {
            $changelog = json_decode(file_get_contents("../build.json"));
            return $changelog[count($changelog) - 1]->version;
        } else {
            return 'cli';
        }
    }

    /**
     * Generates a notification for the app
     * @param  string $string Notification string
     * @param  string $type   Notification type
     * @return void
     */
    public static function notification($string, $type = null)
    {
        if (is_null($type)) {
            $type = 'info';
        }

        Session::flash("notification", $string);
        Session::flash("notificationType", 'alert-'.$type);
    }
}
