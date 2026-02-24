<?php

namespace App\Service\Owner;

use App\Entity\Owner;
use App\Exception\RequiredFieldException;
use App\Repository\OwnerRepository;

class OwnerService
{
    public function __construct(private OwnerRepository $ownerRepository) {}

    public function createOrGetExistingOwner(string $email): Owner
    {
        if (!$email) {
            throw new RequiredFieldException('Email');
        }

        $existingOwner = $this->ownerRepository->findOneByEmail($email);
        if ($existingOwner) {
            return $existingOwner;
        }

        $owner = new Owner();
        $owner->setEmail($email);
        $this->ownerRepository->save($owner);

        return $owner;
    }
}
