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

        if ( ! is_null($module)) {
            $conditions = Module::config(strtolower($module).'.validation.'.$form);
            $fields = $conditions;
        } else {
            $conditions = Config::get("validation");
            $fields = Utilities::assignArrayByPath($conditions, $form);
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
                    $errors[$key] = $validation->messages()->first($key);
                } else {
                    $inputs[$key] = Validation::getInput($key, $jsonInput);
                }
            }
        }

        $result["redirect"] = Redirect::back()->with('validationErrors', $errors)->with('validationInputs', Validation::inputsArray($jsonInput));

        if ( ! empty($errors)) {
            $result["errors"] = $errors;
        } else {
            $result["errors"] = false;
        }

        return $result;
    }

    public static function errors($format = 'array')
    {
        $errorMessage = "";
        $errors = Session::get("validationErrors") ?: false;

        if ( ! $errors) return false;

        if ($format === 'string') {
            foreach ($errors as $error => $message) {
                $errorMessage .= $message."<br>";
            }
        } else {
            $errorMessage = Session::get("validationErrors");
        }

        return $errorMessage;
    }

    public static function inputs()
    {
        $inputs = Session::get("validationInputs") ?: false;

        if ( ! $inputs) return false;

        return $inputs;
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

    private static function inputsArray($jsonInput)
    {
        if ($jsonInput) {
            $inputs = Utilities::raw_json_input('*');
        } else {
            $inputs = Input::all();
        }

        // Don't send the token back
        unset($inputs['_token']);

        return $inputs;
    }

}
//End of File
