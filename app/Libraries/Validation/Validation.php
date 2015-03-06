<?php namespace Validation;

use Validator;
use Redirect;
use Session;
use Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

class Validation {

    public static function check($form)
    {
        $result = array();
        $errors = array();
        $conditions = Config::get("validation.conditions");

        $feilds = $conditions[$form];

        foreach ($feilds as $key => $value)
        {
            if (isset($feilds[$key]))
            {
                $validation = Validator::make(
                    array(
                        $key => Input::get($key)
                    ),
                    array(
                        $key => $feilds[$key]
                    )
                );

                if ($validation->fails())
                {
                    array_push($errors, $validation->messages()->first($key));
                }
            }
        }

        $result["redirect"] = redirect($form)->with('validationErrors', $errors);
        if ( ! empty($errors)) $result["errors"] = $errors;

        return $result;
    }

    public static function errors()
    {
        $errorMessage = "";
        $errors = Session::get("validationErrors") ?: false;

        if ( ! $errors) return false;

        foreach ($errors as $error)
        {
            $errorMessage .= $error."<br>";
        }

        return $errorMessage;
    }

}
//End of File
?>
