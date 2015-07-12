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
    /**
     * Validation check
     * @param  string  $form      form in question from the config
     * @param  string  $module    module name
     * @param  bool    $jsonInput JSON input
     * @return array
     */
    public static function check($form, $module = null, $jsonInput = false)
    {
        $result = array();
        $errors = array();
        $inputs = array();

        if (is_array($form)) {
            $fields = $form;
        } else {
            if ( ! is_null($module)) {
                $conditions = Module::config(strtolower($module).'.validation.'.$form);
                $fields = $conditions;
            } else {
                $conditions = Config::get("validation");
                $fields = Utilities::assignArrayByPath($conditions, $form);
            }
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
            $result["inputs"] = $inputs;
        }

        return $result;
    }

    /**
     * Validation Errors
     * @param  string $format Type of error request
     * @return mixed
     */
    public static function errors($format = 'array')
    {
        $errorMessage = "";
        $errors = Session::get("validationErrors") ?: false;

        if ( ! $errors) {
            return false;
        }

        if ($format === 'string') {
            foreach ($errors as $error => $message) {
                $errorMessage .= $message."<br>";
            }
        } else {
            $errorMessage = Session::get("validationErrors");
        }

        return $errorMessage;
    }

    /**
     * Validation inputs
     * @return mixed
     */
    public static function inputs()
    {
        $inputs = Session::get("validationInputs") ?: false;

        if ( ! $inputs) {
            return false;
        }

        return $inputs;
    }

    /**
     * Get input
     * @param  string $key       Input name
     * @param  bool $jsonInput JSON or not
     * @return mixed
     */
    private static function getInput($key, $jsonInput)
    {
        if ($jsonInput) {
            $input = Utilities::jsonInput($key);
        } else if (Input::file($key)) {
            $input = Input::file($key);
        } else {
            $input = Input::get($key);
        }

        return $input;
    }

    /**
     * Get the inputs as an array
     * @param  bool $jsonInput JSON or not
     * @return array
     */
    private static function inputsArray($jsonInput)
    {
        if ($jsonInput) {
            $inputs = Utilities::jsonInput('*');
        } else {
            $inputs = Input::all();
        }

        // Don't send the token back
        unset($inputs['_token']);

        return $inputs;
    }

    /**
     * Get the value last attempted in valuation
     * @param  string $key Input key
     * @return string
     */
    public static function value($key)
    {
        $inputs = Session::get("validationInputs") ?: false;

        if ( ! $inputs) {
            return '';
        }

        return $inputs[$key];
    }

}
//End of File
