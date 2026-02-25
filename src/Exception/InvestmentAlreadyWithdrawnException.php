<?php

namespace App\Exception;

class InvestmentAlreadyWithdrawnException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Investment has already been withdrawn');
    }
}
