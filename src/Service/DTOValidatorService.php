<?php

namespace App\Service;

use App\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOValidatorService
{
    public function __construct(private ValidatorInterface $validator) {}

    public function validate(object $dto): void
    {
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }
}
