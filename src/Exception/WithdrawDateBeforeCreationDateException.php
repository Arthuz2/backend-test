<?php

namespace App\Exception;

class WithdrawDateBeforeCreationDateException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Withdraw date cannot be before creation date.');
    }
}
