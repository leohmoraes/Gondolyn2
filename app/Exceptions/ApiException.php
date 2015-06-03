<?php namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    // nothing special
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
