<?php

namespace App\Exception;

class InvestmentMustBePositiveException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Investment must be a positive value.");
    }
}
