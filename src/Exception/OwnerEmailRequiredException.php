<?php

namespace App\Exception;

class OwnerEmailRequiredException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Owner email is required to list investments');
    }
}
