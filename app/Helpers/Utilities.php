<?php namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class Utilities
{
    public static function add_salt($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public static function get_raw_post()
    {
        $request = Request::instance();
        $content = $request->getContent();

        return $content;
    }

    public static function get_header($key)
    {
        $headers = getallheaders();

        return @$headers[$key];
    }

    public static function raw_json_input($key)
    {
        $post = json_decode(Utilities::get_raw_post());

        if (isset($post->$key)) return $post->$key;

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
