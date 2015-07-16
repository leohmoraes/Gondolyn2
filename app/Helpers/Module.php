<?php namespace App\Helpers;

use View;

/**
 * For Common Actions regarding languages within the store
 */
class Module
{
    /**
     * Get the language value
     * @param  string $key string of array as path
     * @return mixed
     */
    public static function lang($key)
    {
        $locale = \App::getLocale();

        $langRoute = explode('.', $key);

        $langContents = include(app_path().'/Modules/'.ucfirst($langRoute[0]).'/Lang/'.$locale.'/'.$langRoute[1].'.php');

        $strippedKey = preg_replace('/'.$langRoute[1].'./', '', preg_replace('/'.$langRoute[0].'./', '', $key, 1), 1);

        return Utilities::assignArrayByPath($langContents, $strippedKey);
    }

    /**
     * Get Module Config
     * @param  string $key Config key
     * @return mixed
     */
    public static function config($key)
    {
        $splitKey = explode('.', $key);

        $moduleConfig = include(app_path().'/Modules/'.ucfirst($splitKey[0]).'/Config/'.$splitKey[1].'.php');

        $strippedKey = preg_replace('/'.$splitKey[1].'./', '', preg_replace('/'.$splitKey[0].'./', '', $key, 1), 1);

        return Utilities::assignArrayByPath($moduleConfig, $strippedKey);
    }

    /**
     * Collect the menus from the modules
     * @return string
     */
    public static function getMenus()
    {
        $modules = app_path()."/Modules/";

        $files = glob($modules."*");

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

    /**
     * Collect the configs from the specified module
     * @param  string $fileName Specific config file name
     * @return mixed
     */
    public static function getConfigs($fileName = null)
    {
        if (is_null($fileName)) {
            $fileName = 'config';
        }

        $moduleConfigs = array();
        $modules = app_path()."/Modules/";

        $files = glob($modules."*");

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

    /**
     * Get the permission matrix config.
     * @return array
     */
    public static function getPermissionConfigs()
    {
        $modulePermissionConfigs = array();
        $modules = app_path()."/Modules/";

        $files = glob($modules."*");

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

    /**
     * Get a module's asset
     * @param  string $module      Module name
     * @param  string $path        Path to module asset
     * @param  string $contentType Asset type
     * @return string
     */
    public static function asset($module, $path, $contentType = 'null', $fullURL = true)
    {
        if ( ! $fullURL) {
            return '/../app/Modules/'.ucfirst($module).'/Assets/'.$path;
        }

        return url('module-asset/'.lcfirst($module).'/'.Crypto::encrypt($path).'/'.Crypto::encrypt($contentType));
    }
}