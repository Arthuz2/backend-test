<?php

namespace App\Exception;

class RequiredFieldException extends \Exception
{
    public function __construct(string $fieldName)
    {
        parent::__construct("Required field '{$fieldName}' is missing");
    }
}
