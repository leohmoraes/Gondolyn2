<?php namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class Utilities
{
    public static function addSalt($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * Assign a value to the path
     * @param  array &$arr  Original Array of values
     * @param  string $path  Array as path string
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
     * Get raw post
     * @return mixed
     */
    public static function getRawPost()
    {
        $request = Request::instance();
        $content = $request->getContent();

        return $content;
    }

    /**
     * Get header
     * @param  string $key Key
     * @return string
     */
    public static function getHeader($key)
    {
        $headers = getallheaders();

        return @$headers[$key];
    }

    /**
     * Get JSON input
     * @param  string $key JSON key
     * @return mixed
     */
    public static function jsonInput($key)
    {
        $post = json_decode(Utilities::getRawPost());

        if (isset($post->$key)) {
            return $post->$key;
        } else if ($key === '*') {
            return $post;
        }

        return false;
    }

    /**
     * Generate a name from the file path
     * @param  string $file File path
     * @return string
     */
    public static function getFileClass($file)
    {
        $sections = explode('/', $file);
        $fileName = $sections[count($sections) - 1];

        $class = str_replace('.php', '', $fileName);

        return $class;
    }

}
//End of File
