<?php namespace Tools;

use Illuminate\Support\Facades\Request;

class Tools {

    public function add_salt($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public function get_raw_post()
    {
        $request = Request::instance();
        $content = $request->getContent();

        return $content;
    }

    public function get_header($key)
    {
        $headers = getallheaders();

        return @$headers[$key];
    }

    public function raw_json_input($key)
    {
        $post = json_decode($this->get_raw_post());

        if (isset($post->$key)) return $post->$key;

        return false;
    }

}
//End of File
?>