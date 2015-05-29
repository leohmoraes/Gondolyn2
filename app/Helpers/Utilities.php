<?php namespace App\Helpers;

use App;
use Config;
use Exception;
use Lang;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
        if (php_sapi_name() !== 'cli') {
            $headers = getallheaders();
        } else {
            $headers = '';

            foreach ($_SERVER as $name => $value)
            {
                if (substr($name, 0, 5) == 'HTTP_')
                {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }

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

    /**
     * Saves File
     * @param  string $fileName File input name
     * @param  string $location Storage location
     * @return array
     */
    public static function saveFile($fileName, $directory = "", $fileTypes = array(), $location = 'local')
    {
        $file = Request::file($fileName);

        if (is_null($file) || File::size($file) > Config::get('gondolyn.max_file_upload_size')) {
            return false;
        }

        $extension = $file->getClientOriginalExtension();
        $newFileName = md5(rand(1111,9999).time());

        // In case we don't want that file type
        if ( ! empty($fileTypes)) {
            if ( ! in_array($extension, $fileTypes)) {
                throw new Exception(Lang::get('notification.errors.bad-file-type'), 1);
            }
        }

        Storage::disk($location)->put($directory.$newFileName.'.'.$extension,  File::get($file));

        return [
            'original' => $file->getFilename().'.'.$extension,
            'name'  => $directory.$newFileName.'.'.$extension,
        ];
    }

    /**
     * Provide a URL for the file as a public asset
     * @param  string $fileName File name
     * @return string
     */
    public static function fileAsPublicAsset($fileName)
    {
        return url('public-asset/'.Crypto::encrypt($fileName));
    }

    /**
     * Provides a URL for the file as a download
     * @param  string $fileName File name
     * @param  string $realFileName Real file name
     * @return string
     */
    public static function fileAsDownload($fileName, $realFileName)
    {
        return url('public-download/'.Crypto::encrypt($fileName).'/'.Crypto::encrypt($realFileName));
    }

}
//End of File
