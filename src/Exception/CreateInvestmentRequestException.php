<?php

namespace App\Exception;

class CreateInvestmentRequestException extends \Exception
{
    public function __construct()
    {
        parent::__construct("An error occurred while creating the investment.");
    }
}