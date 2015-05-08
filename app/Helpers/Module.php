<?php namespace App\Helpers;

use View;

/**
 * For Common Actions regarding languages within the store
 */
class Module {

    /**
     * Get the language value
     * @param  string $key string of array as path
     * @return mixed
     */
    public static function lang($key)
    {
        $locale = \App::getLocale();

        $langRoute = explode('.', $key);

        $langContents = include(app_path().'/modules/'.ucfirst($langRoute[0]).'/Lang/'.$locale.'/'.$langRoute[1].'.php');

        $strippedKey = str_replace($langRoute[1].'.', '', str_replace($langRoute[0].'.', '', $key));

        $lastKey = $langRoute[count($langRoute) - 1];

        return Module::assignArrayByPath($langContents, $strippedKey);
    }

    /**
     * Assign a value to the path
     * @param  array &$arr  Original Array of values
     * @param  string $path  Array as path string
     * @param  string $value Desired key
     * @return mixed
     */
    public static function assignArrayByPath(&$arr, $path)
    {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        return $arr;
    }

    /**
     * Get Module Config
     * @param  string $key Config key
     * @return mixed
     */
    public static function config($key, $fileName = null)
    {
        $splitKey = explode('.', $key);

        if (is_null($fileName)) {
            $file = 'config';
        } else {
            $file = $fileName;
        }

        $moduleConfig = include(app_path().'/modules/'.ucfirst($splitKey[0]).'/Config/'.$file.'.php');
        return $moduleConfig[$splitKey[1]];
    }

    public static function getMenus()
    {
        $modules = app_path()."/Modules/";

        $files = glob($modules . "*");

        $menu = '';

        foreach ($files as $file) {
            if (is_dir($file)) {
                $module = lcfirst(str_replace(app_path().'/Modules/', '', $file));
                if (file_exists($file.'/Views/menu.blade.php')) {
                    $menu .= View::make($module.'::menu');
                }
            }
        }

        return $menu;
    }

    public static function getConfigs($fileName = null)
    {
        if (is_null($fileName)) {
            $fileName = 'config';
        }

        $moduleConfigs = array();
        $modules = app_path()."/Modules/";

        $files = glob($modules . "*");

        foreach ($files as $file) {
            if (is_dir($file)) {
                if (file_exists($file.'/Config/'.$fileName.'.php')) {
                    $moduleName = str_replace(app_path().'/Modules/', '', $file);
                    $moduleConfigs[$moduleName] = include($file.'/Config/'.$fileName.'.php');
                }
            }
        }

        return $moduleConfigs;
    }

    public static function getPermissionConfigs()
    {
        $modulePermissionConfigs = array();
        $modules = app_path()."/Modules/";

        $files = glob($modules . "*");

        foreach ($files as $file) {
            if (is_dir($file)) {
                if (file_exists($file.'/Config/permissions.matrix.php')) {
                    $moduleName = str_replace(app_path().'/Modules/', '', $file);
                    $modulePermissionConfigs[$moduleName] = include($file.'/Config/permissions.matrix.php');
                }
            }
        }

        return $modulePermissionConfigs;
    }

}