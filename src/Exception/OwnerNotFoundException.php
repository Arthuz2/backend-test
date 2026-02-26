<?php

namespace App\Exception;

class OwnerNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Owner not found.");
    }
}