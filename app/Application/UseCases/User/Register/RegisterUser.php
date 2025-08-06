<?php

namespace App\Application\UseCases\User\Register;

use App\Application\UseCases\User\Register\DTO\RegisterUserDTO;
use App\Application\UseCases\User\Register\Service\RegisterUserService;
use App\Domain\Entity\User\Factory\UserFactory;
use App\Domain\Entity\User\User;

readonly class RegisterUser
{
    public function __construct(private RegisterUserService $service) {}

    public function handle(RegisterUserDTO $dto): User
    {
        $user = UserFactory::create(name: $dto->getName(), email: $dto->getEmail(), password: $dto->getPassword());

        return $this->service->create($user);
    }
}
