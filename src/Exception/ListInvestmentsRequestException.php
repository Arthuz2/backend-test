<?php

namespace App\Exception;

class ListInvestmentsRequestException extends \Exception
{
    public function __construct()
    {
        parent::__construct('An error occurred while listing investments.');
    }
}
