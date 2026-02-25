<?php

namespace App\Exception;

class WithdrawDateInFutureException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Withdraw date cannot be in the future.');
    }
}