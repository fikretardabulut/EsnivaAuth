<?php

namespace App\Domain\Auth\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct(string $message = 'The provided credentials are incorrect.')
    {
        parent::__construct($message);
    }
}