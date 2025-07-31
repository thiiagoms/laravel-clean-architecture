<?php

namespace App\Application\UseCases\Auth\Common\Interface;

use App\Domain\Entity\Auth\Token\Token;
use App\Domain\Entity\User\User;

interface GenerateTokenInterface
{
    public function create(User $user): Token;
}
