<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth\Authenticate;

use App\Application\UseCases\Auth\Authenticate\DTO\AuthenticateDTO;
use App\Application\UseCases\Auth\Authenticate\Service\AuthenticateUserService;
use App\Application\UseCases\Auth\Common\Interface\GenerateTokenInterface;
use App\Application\UseCases\Auth\Exception\InvalidCredentialsException;
use App\Domain\Entity\Auth\Token\Token;

class Authenticate
{
    public function __construct(
        private readonly AuthenticateUserService $service,
        private readonly GenerateTokenInterface $generateToken
    ) {}

    public function handle(AuthenticateDTO $dto): Token
    {
        $user = $this->service->authenticate($dto);

        if (empty($user)) {
            throw InvalidCredentialsException::create();
        }

        return $this->generateToken->create($user);
    }
}
