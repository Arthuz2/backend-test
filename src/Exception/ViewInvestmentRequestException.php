<?php

namespace App\Exception;

class ViewInvestmentRequestException extends \Exception
{
    public function __construct()
    {
        return parent::__construct("An error occurred. Please check the provided data.");
    }
}