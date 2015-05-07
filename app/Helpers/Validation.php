<?php namespace App\Helpers;

use Validator;
use Redirect;
use Session;
use Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class Validation
{
    public static function check($form, $module = null, $jsonInput = false)
    {
        $result = array();
        $errors = array();
        $inputs = array();

        $langRoute = explode('.', $form);
        $strippedKey = str_replace($langRoute[0].'.', '', $form);
        $lastKey = $langRoute[count($langRoute) - 1];

        if ( ! is_null($module)) {
            $conditions = include(app_path().'/modules/'.ucfirst($module).'/Config/validation.php');
            $fields = Validation::assignArrayByPath($conditions, $form, $lastKey);
        } else {
            $conditions = Config::get("validation.conditions");
            $fields = $conditions[$form];
        }

        foreach ($fields as $key => $value) {
            if (isset($fields[$key])) {
                $validation = Validator::make(
                    array(
                        $key => Validation::getInput($key, $jsonInput)
                    ),
                    array(
                        $key => $fields[$key]
                    )
                );

                if ($validation->fails()) {
                    array_push($errors, $validation->messages()->first($key));
                } else {
                    $inputs[$key] = Validation::getInput($key, $jsonInput);
                }
            }
        }

        $result["redirect"] = redirect($form)->with('validationErrors', $errors);
        if ( ! empty($errors)) {
            $result["errors"] = $errors;
        } else {
            $result["inputs"] = $inputs;
        }

        return $result;
    }

    public static function errors()
    {
        $errorMessage = "";
        $errors = Session::get("validationErrors") ?: false;

        if ( ! $errors) return false;

        foreach ($errors as $error) {
            $errorMessage .= $error."<br>";
        }

        return $errorMessage;
    }

    /**
     * Assign a value to the path
     * @param  array &$arr  Original Array of values
     * @param  string $path  Array as path string
     * @param  string $value Desired key
     * @return mixed
     */
    private static function assignArrayByPath(&$arr, $path, $value)
    {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        return $arr;
    }

    private static function getInput($key, $jsonInput)
    {
        if ($jsonInput) {
            $input = Utilities::raw_json_input($key);
        } else {
            $input = Input::get($key);
        }

        return $input;
    }

}
//End of File
