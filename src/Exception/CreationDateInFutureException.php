<?php

namespace App\Exception;

class CreationDateInFutureException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Creation date cannot be in the future.");
    }
}
