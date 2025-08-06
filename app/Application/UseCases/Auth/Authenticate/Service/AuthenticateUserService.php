<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth\Authenticate\Service;

use App\Application\UseCases\Auth\Authenticate\DTO\AuthenticateDTO;
use App\Application\UseCases\User\Common\Service\FindOrFailUserByEmailService;
use App\Application\UseCases\User\Exception\UserNotFoundException;
use App\Domain\Entity\User\User;

class AuthenticateUserService
{
    public function __construct(
        private readonly FindOrFailUserByEmailService $findOrFailUserByEmail,
        private readonly CanAuthenticateUserService $canAuthenticateUserService
    ) {}

    public function authenticate(AuthenticateDTO $dto): ?User
    {
        try {

            $user = $this->findOrFailUserByEmail->findOrFail($dto->getEmail());

            $userCanAuthenticate = $this->canAuthenticateUserService->canAuthenticate(user: $user, dto: $dto);

            return $userCanAuthenticate ? $user : null;

        } catch (UserNotFoundException $e) {
            return null;
        }
    }
}
