<?php

namespace App\Exception;

class EndDateBeforeStartDateException extends \Exception
{
    public function __construct()
    {
        parent::__construct("End date cannot be before start date.");
    }
}
