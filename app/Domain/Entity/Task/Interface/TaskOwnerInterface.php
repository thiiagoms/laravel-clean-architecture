<?php

namespace App\Domain\Entity\Task\Interface;

use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;

interface TaskOwnerInterface
{
    public function getName(): Name;

    public function getEmail(): Email;
}
