<?php

namespace App\Exception;

class InvalidIdException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Invalid ID format. Expected a valid UUID.");
    }
}