<?php namespace App\Exceptions;

use Exception;

class LoginException extends Exception
{
    // nothing special
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
