<?php namespace App\Exceptions;

use Exception;

class PermissionException extends Exception
{
    // nothing special
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
