<?php

namespace App\Exception;

class InvestmentNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Investment not found.");
    }
}