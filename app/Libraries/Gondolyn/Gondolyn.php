<?php namespace Gondolyn;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class Gondolyn {

    public function response($type, $message)
    {
        return Response::json(array("status" => $type, "data" => $message));
    }

    public function valid_api_request()
    {
        $headers = getallheaders();

        $auth = @$headers['Authorization'];

        $keys = Config::get('gondolyn.authKeys');

        if ( ! in_array($auth, $keys)) return false;

        Session::put('authKey', $auth);

        return true;
    }

    public static function version()
    {
        if (php_sapi_name() != "cli")
        {
            $changelog = json_decode(file_get_contents("../build.json"));
            return $changelog[count($changelog) - 1]->version;
        }
    }

    public function getModuleMenus()
    {
        $modules = app_path()."/modules/";

        $files = glob($modules . "*");

        foreach ($files as $file)
        {
            if (is_dir($file))
            {
                $moduleDetails = json_decode(file_get_contents($file.'/module.json'));

                if (file_exists($file.'/menu.php') && $moduleDetails->enabled)
                {
                    @include($file.'/menu.php');
                }
            }
        }
    }

}