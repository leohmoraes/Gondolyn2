<?php namespace Gondolyn;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;

class Gondolyn
{
    public function response($type, $message)
    {
        return Response::json(array("status" => $type, "data" => $message));
    }

    public function valid_api_key()
    {
        $headers = getallheaders();

        $auth = @$headers['Authorization'];

        $keys = Config::get('gondolyn.authKeys');

        if ( ! in_array($auth, $keys)) return false;

        Session::put('authKey', $auth);

        return true;
    }

    public function valid_api_token()
    {
        $headers = getallheaders();

        $requestToken = @$headers['Token'];

        $userToken = Session::get('token');

        if ($requestToken !== $userToken) return false;

        return true;
    }

    public static function is_api_call()
    {
        if (php_sapi_name() !== "cli" && stristr(Request::path(), 'api')) {
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

    public function getModuleMenus()
    {
        $modules = app_path()."/Modules/";

        $files = glob($modules . "*");

        foreach ($files as $file) {
            if (is_dir($file)) {
                if (file_exists($file.'/menu.php')) {
                    @include($file.'/menu.php');
                }
            }
        }
    }

    public function getModuleConfigs()
    {
        $moduleConfigs = array();
        $modules = app_path()."/Modules/";

        $files = glob($modules . "*");

        foreach ($files as $file) {
            if (is_dir($file)) {
                if (file_exists($file.'/Config/config.php')) {
                    $moduleName = str_replace(app_path().'/Modules/', '', $file);
                    $moduleConfigs[$moduleName] = include($file.'/Config/config.php');
                }
            }
        }

        return $moduleConfigs;
    }

}
