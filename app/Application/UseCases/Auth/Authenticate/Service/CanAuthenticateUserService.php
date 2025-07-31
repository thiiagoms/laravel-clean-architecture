<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth\Authenticate\Service;

use App\Application\UseCases\Auth\Authenticate\DTO\AuthenticateDTO;
use App\Domain\Entity\User\User;

class CanAuthenticateUserService
{
    public function canAuthenticate(User $user, AuthenticateDTO $dto): bool
    {
        return $this->passwordMatches($user, $dto);
    }

    private function passwordMatches(User $user, AuthenticateDTO $dto): bool
    {
        return $user->getPassword()->match(passwordAsPlainText: $dto->getPassword()->getValue());
    }
}
