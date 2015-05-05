<?php namespace Module;

/**
 * For Common Actions regarding languages within the store
 */
class Module {

    /**
     * Get the language value
     * @param  string $key string of array as path
     * @param  string $module string of module name
     * @return mixed
     */
    public static function lang($key)
    {
        $locale = \App::getLocale();

        $langRoute = explode('.', $key);

        $langContents = include(app_path().'/modules/'.ucfirst($langRoute[0]).'/Lang/'.$locale.'/'.$langRoute[1].'.php');

        $strippedKey = str_replace($langRoute[1].'.', '', str_replace($langRoute[0].'.', '', $key));

        $lastKey = $langRoute[count($langRoute) - 1];

        return Module::assignArrayByPath($langContents, $strippedKey, $lastKey);
    }

    /**
     * Assign a value to the path
     * @param  array &$arr  Original Array of values
     * @param  string $path  Array as path string
     * @param  string $value Desired key
     * @return mixed
     */
    public static function assignArrayByPath(&$arr, $path, $value)
    {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        return $arr;
    }

}