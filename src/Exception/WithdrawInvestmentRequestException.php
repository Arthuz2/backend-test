<?php

namespace App\Exception;

class WithdrawInvestmentRequestException extends \Exception
{
    public function __construct()
    {
        parent::__construct('An error occurred while processing the withdraw investment request.');
    }
}