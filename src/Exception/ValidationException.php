<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \Exception
{
    public function __construct(
        private ConstraintViolationListInterface $errors
    ) {
        parent::__construct('Validation failed');
    }

    public function getErrors(): array
    {
        $formatted = [];

        foreach ($this->errors as $error) {
            $formatted[$error->getPropertyPath()] = $error->getMessage();
        }

        return $formatted;
    }
}
