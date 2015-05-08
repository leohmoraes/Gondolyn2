<?php namespace App\Helpers;

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

}