<?php namespace Crypto;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class Crypto {

    public static function encrypt($value)
    {
        $config_key = Config::get('app.key');
        $alt_key    = Session::get('user_id') ?: 0;

        $key        = $alt_key.$config_key;
        $iv         = md5(md5($key));

        $encrypted = rawurlencode(Crypto::url_base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $value, MCRYPT_MODE_CBC, $iv)));

        return trim($encrypted);
    }

    public static function decrypt($value)
    {
        $config_key = Config::get('app.key');
        $alt_key    = Session::get('user_id') ?: 0;

        $key        = $alt_key.$config_key;
        $iv         = md5(md5($key));

        $decrypted =  mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), Crypto::url_base64_decode(rawurldecode($value)), MCRYPT_MODE_CBC, $iv);

        return trim($decrypted);
    }

    public static function url_base64_encode($str)
    {
        return strtr(base64_encode($str),
            array(
                '+' => '.',
                '=' => '-',
                '/' => '~'
            )
        );
    }

    public static function url_base64_decode($str)
    {
        return base64_decode(strtr($str,
            array(
                '.' => '+',
                '-' => '=',
                '~' => '/'
            )
        ));
    }

}

//End of File

?>