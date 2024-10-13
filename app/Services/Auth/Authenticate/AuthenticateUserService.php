<?php

declare(strict_types=1);

namespace App\Services\Auth\Authenticate;

use App\Contracts\Services\Auth\Authenticate\AuthenticateUserServiceContract;
use App\Contracts\Services\Auth\Authenticate\AuthenticatorUserServiceContract;
use App\Contracts\Services\Auth\Token\TokenGeneratorServiceContract;
use App\Contracts\Services\User\Find\FindUserByEmailServiceContract;
use App\DTO\Auth\Authenticate\AuthenticateUserDTO;
use App\DTO\Auth\Token\TokenDTO;

class AuthenticateUserService implements AuthenticateUserServiceContract
{
    public function __construct(
        private readonly FindUserByEmailServiceContract $findUserByEmailService,
        private readonly AuthenticatorUserServiceContract $authenticatorUserService,
        private readonly TokenGeneratorServiceContract $tokenGeneratorService,
    ) {}

    public function handle(AuthenticateUserDTO $authenticateUserDTO): TokenDTO
    {
        $user = $this->findUserByEmailService->handle($authenticateUserDTO->email);

        $this->authenticatorUserService->handle($user, $authenticateUserDTO->password);

        $token = $this->tokenGeneratorService->generateToken($authenticateUserDTO);

        return $this->tokenGeneratorService->responseWithToken($token);
    }
}
